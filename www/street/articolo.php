<?
include "street.inc.php";

//*****************************************************************************
class articolo extends street 
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
			// un proprio commento, senno' che si registra a fare?
			$this->checkPrivilegio(PRIV_COMMENTI_INSERIMENTO_PROPRI);
			$this->creaModuloCommenti ();
			}

		// alcuni elementi vengono mostrati sempre, anche se si esce con una
		// mostraMessaggio
		$dbconn = $this->dammiConnessioneDB();
		$this->aggiungiElemento($this->dammiArticolo($dbconn), "articolo", "XML");
		$this->aggiungiElemento($this->dammiCommenti($dbconn), "commenti", "XML");
		$this->aggiungiElemento($_POST["email"], "email");

		if ($this->modulo->daAggiornare())
			{
			if (!$this->utente)
				$this->eseguiLogin($this->modulo);
			else
				$this->aggiornaRecordCommenti ();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_COMMENTI_ELIMINA, PRIV_COMMENTI_ELIMINA_PROPRI, $this->modulo->record);
			$this->eliminaRecord($this->modulo->record, false);
			$this->creaRSSCommenti($this->modulo->righeDB->connessioneDB, $_GET['id_articolo']);
			$this->ridireziona("?id_articolo=$_GET[id_articolo]#commenti");
			}
		else
			{
			$this->mostra();
			}
		
		}
	
	//*************************************************************************
	function dammiArticolo(waConnessioneDB $dbconn)
		{
		$sql = "SELECT articoli.*," .
				" categorie_articoli.nome as nome_categoria," .
				" utenti.nickname," .
				" COUNT(commenti.id_commento) AS nr_commenti" . 
				" FROM articoli" .
				" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
				" INNER JOIN utenti ON articoli.id_utente=utenti.id_utente" .
				" LEFT JOIN commenti ON articoli.id_articolo=commenti.id_articolo AND NOT commenti.sospeso" .
				" WHERE NOT articoli.sospeso" .
				" AND articoli.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (articoli.data_ora_fine_pubblicazione>=NOW() OR articoli.data_ora_fine_pubblicazione IS NULL)" .
				" AND articoli.id_articolo=" . $dbconn->interoSql($_GET['id_articolo']) .
				" GROUP BY articoli.id_articolo" . 
				" ORDER BY articoli.data_ora_inizio_pubblicazione DESC";
				
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		if (!$rs->righe)
			$this->mostraMessaggio ("Articolo non trovato", "Articolo non trovato");

		$buffer = $this->rs2XML($rs);
		// all'interno del buffer sostituiamo il tag [youtube:xxxx] con il codice fatto apposta
//		$this->youTubeJoke($buffer);
		
		return $buffer;
		}
						
	//*************************************************************************
	function dammiCommenti(waConnessioneDB $dbconn)
		{
		$sql = "SELECT commenti.*," .
			" utenti.nickname," . 
			" utenti.avatar," . 
			" IF(commenti.id_commento_genitore, CONCAT(utenti_commenti_genitori.nickname, ' - ', DATE_FORMAT(commenti_genitori.data_ora_creazione, '%d/%m/%Y %H.%i.%s')), '') AS commento_genitore" .
			" FROM commenti" .
			" INNER JOIN utenti ON commenti.id_utente=utenti.id_utente" .
			" LEFT JOIN commenti AS commenti_genitori ON commenti.id_commento_genitore=commenti_genitori.id_commento" . 
			" LEFT JOIN utenti AS utenti_commenti_genitori ON commenti_genitori.id_utente=utenti_commenti_genitori.id_utente" .
			" WHERE NOT commenti.sospeso" .
			" AND commenti.id_articolo=" . $dbconn->interoSql($_GET['id_articolo']) .
			" ORDER BY commenti.data_ora_creazione";
				
		$pagina = intval($_GET['pag_commenti']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);

		$buffer = $this->rs2XML($rs, $pagina);
		
		return $buffer;
		}
						
	//***************************************************************************
	function creaModuloCommenti()
		{
		$this->modulo = new waModulo(null, $this);
		$this->modulo->righeDB = $this->dammiRecordsetCommento();
		
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
	function dammiRecordsetCommento()
		{
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT commenti.*" .
				" FROM commenti" .
				" WHERE commenti.id_commento=" . $dbconn->interoSql($_GET['id_commento']) . 
				" AND NOT commenti.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_commento'] ? 1 : 0);
		if ($_GET['id_commento'] && !$righeDB->righe)
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);

		return $righeDB;
		}
		
	//***************************************************************************
	function aggiornaRecordCommenti()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->righe[0];
			
		if (!$riga)
			{
			if (!$this->haPrivilegio(PRIV_COMMENTI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_COMMENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			
			// inseriamo di default l'utente loggato; se l'utente ha i privilegi
			// e ha definito un altro utente come proprietario del record,
			// allora questo valore sara ridefinito durante $this->modulo->salva();
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			}
		else 
			{
			$this->checkPrivilegio(PRIV_COMMENTI_MODIFICA, PRIV_COMMENTI_MODIFICA_PROPRI, $riga);
//			fanda!!!
//			la gestione del lock sarebbe carina....
//			$this->verificaViolazioneLock($this->modulo);
			}
			
		$riga->inserisciValore("id_articolo", $_GET['id_articolo']);
			
		$this->setEditorData($riga);
		$this->modulo->salva();
		// controllo sanitario
		$this->sanificaHTML($riga, "testo");
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		
		$this->creaRSSCommenti($this->modulo->righeDB->connessioneDB, $_GET['id_articolo']);
		
		$id_commento = $idInserito ? $idInserito : $_GET['id_commento'];
		$this->ridireziona("articolo.php?id_articolo=$_GET[id_articolo]#commento_$id_commento");
		}
		
	
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new articolo();
	