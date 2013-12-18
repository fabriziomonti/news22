<?
include "street.inc.php";

//*****************************************************************************
class argomento extends street 
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		

	//*************************************************************************
	function __construct()
		{
		parent::__construct();

		if (!$this->utente)
			$this->modulo = $this->creaModuloLogin();
		else
			{
			// il controllo sul privilegio è messo per puro scopo documentativo:
			// in realtà è autoevidente che l'utente registrato può inserire
			// un proprio intervento, senno' che si registra a fare?
			$this->checkPrivilegio(PRIV_INTERVENTI_INSERIMENTO_PROPRI);
			$this->creaModuloInterventi ();
			}

		// alcuni elementi vengono mostrati sempre, anche se si esce con una
		// mostraMessaggio
		$dbconn = $this->dammiConnessioneDB();
		$this->aggiungiElemento($this->dammiArgomento($dbconn), "argomento", "XML");
		$this->aggiungiElemento($this->dammiInterventi($dbconn), "interventi", "XML");
		$this->aggiungiElemento($_POST["email"], "email");

		if ($this->modulo->daAggiornare())
			{
			if (!$this->utente)
				$this->eseguiLogin($this->modulo);
			else
				$this->aggiornaRecordInterventi ();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_INTERVENTI_ELIMINA, PRIV_INTERVENTI_ELIMINA_PROPRI, $this->modulo->record);
			$this->eliminaRecord($this->modulo->record, false);
			$this->creaRSSInterventi($this->modulo->righeDB->connessioneDB, $_GET['id_argomento']);
			$this->ridireziona("?id_argomento=$_GET[id_argomento]#interventi");
			}
		else
			{
			$this->mostra();
			}
		
		}
	
	//*************************************************************************
	function dammiArgomento(waConnessioneDB $dbconn)
		{
		$sql = "SELECT argomenti.*," .
				" categorie_argomenti.nome as nome_categoria," .
				" utenti.nickname," .
				" COUNT(interventi.id_intervento) AS nr_interventi" . 
				" FROM argomenti" .
				" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
				" INNER JOIN utenti ON argomenti.id_utente=utenti.id_utente" .
				" LEFT JOIN interventi ON argomenti.id_argomento=interventi.id_argomento AND NOT interventi.sospeso" .
				" WHERE NOT argomenti.sospeso" .
				" AND argomenti.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (argomenti.data_ora_fine_pubblicazione>=NOW() OR argomenti.data_ora_fine_pubblicazione IS NULL)" .
				" AND argomenti.id_argomento=" . $dbconn->interoSql($_GET['id_argomento']) .
				" GROUP BY argomenti.id_argomento" . 
				" ORDER BY argomenti.data_ora_inizio_pubblicazione DESC";
				
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		if (!$rs->righe)
			$this->mostraMessaggio ("Argomento non trovato", "Argomento non trovato");

		$buffer = $this->rs2XML($rs);
		// all'interno del buffer sostituiamo il tag [youtube:xxxx] con il codice fatto apposta
//		$this->youTubeJoke($buffer);
		
		return $buffer;
		}
						
	//*************************************************************************
	function dammiInterventi(waConnessioneDB $dbconn)
		{
		$sql = "SELECT interventi.*," .
			" utenti.nickname," . 
			" utenti.avatar," . 
			" IF(interventi.id_intervento_genitore, CONCAT(utenti_interventi_genitori.nickname, ' - ', DATE_FORMAT(interventi_genitori.data_ora_creazione, '%d/%m/%Y %H.%i.%s')), '') AS intervento_genitore" .
			" FROM interventi" .
			" INNER JOIN utenti ON interventi.id_utente=utenti.id_utente" .
			" LEFT JOIN interventi AS interventi_genitori ON interventi.id_intervento_genitore=interventi_genitori.id_intervento" . 
			" LEFT JOIN utenti AS utenti_interventi_genitori ON interventi_genitori.id_utente=utenti_interventi_genitori.id_utente" .
			" WHERE NOT interventi.sospeso" .
			" AND interventi.id_argomento=" . $dbconn->interoSql($_GET['id_argomento']) .
			" ORDER BY interventi.data_ora_creazione";
				
		$pagina = intval($_GET['pag_interventi']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);

		$buffer = $this->rs2XML($rs, $pagina);
		
		return $buffer;
		}
						
	//***************************************************************************
	function creaModuloInterventi()
		{
		$this->modulo = new waModulo(null, $this);
		$this->modulo->righeDB = $this->dammiRecordsetIntervento();
		
		$this->modulo->aggiungiAreaTesto("testo", "Testo", false, true);
		$button = new waBottone($this->modulo, 'cmd_invia', 'Accedi');
		
		$this->modulo->leggiValoriIngresso();
		}

	//***************************************************************************
	/**
	* -
	*
	* @return waRigheDB
	*/
	function dammiRecordsetIntervento()
		{
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT interventi.*" .
				" FROM interventi" .
				" WHERE interventi.id_intervento=" . $dbconn->interoSql($_GET['id_intervento']) . 
				" AND NOT interventi.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_intervento'] ? 1 : 0);
		if ($_GET['id_intervento'] && !$righeDB->righe)
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);

		return $righeDB;
		}
		
	//***************************************************************************
	function aggiornaRecordInterventi()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->righe[0];
			
		if (!$riga)
			{
			if (!$this->haPrivilegio(PRIV_INTERVENTI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_INTERVENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			
			// inseriamo di default l'utente loggato; se l'utente ha i privilegi
			// e ha definito un altro utente come proprietario del record,
			// allora questo valore sara ridefinito durante $this->modulo->salva();
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			}
		else 
			{
			$this->checkPrivilegio(PRIV_INTERVENTI_MODIFICA, PRIV_INTERVENTI_MODIFICA_PROPRI, $riga);
//			fanda!!!
//			la gestione del lock sarebbe carina....
//			$this->verificaViolazioneLock($this->modulo);
			}
			
		$riga->inserisciValore("id_argomento", $_GET['id_argomento']);
			
		$this->setEditorData($riga);
		$this->modulo->salva();
		// controllo sanitario
		$this->sanificaHTML($riga, "testo");
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		
		$this->creaRSSInterventi($this->modulo->righeDB->connessioneDB, $_GET['id_argomento']);
		
		$id_intervento = $idInserito ? $idInserito : $_GET['id_intervento'];
		$this->ridireziona("argomento.php?id_argomento=$_GET[id_argomento]#intervento_$id_intervento");
		}
		
	
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new argomento();
	