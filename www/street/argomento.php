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
			$this->ridireziona("?id_argomento=$_GET[id_argomento]&pag_interventi=$_GET[pag_interventi]#interventi");
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
			" utenti.avatar" . 
			" FROM interventi" .
			" INNER JOIN utenti ON interventi.id_utente=utenti.id_utente" .
			" WHERE NOT interventi.sospeso" .
			" AND interventi.id_argomento=" . $dbconn->interoSql($_GET['id_argomento']) .
			" ORDER BY interventi.chiave_ordinamento";
				
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
		$id_intervento = $_GET['id_intervento_genitore'] ? $_GET['id_intervento_genitore'] : $_GET['id_intervento'];
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT interventi.*" .
				" FROM interventi" .
				" WHERE interventi.id_intervento=" . $dbconn->interoSql($id_intervento) . 
				" AND NOT interventi.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $id_intervento ? 1 : 0);
		if ($id_intervento && !$righeDB->righe)
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);

		return $righeDB;
		}
		
	//***************************************************************************
	function aggiornaRecordInterventi()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->righe[0];
			
		if ($_GET['id_intervento_genitore'])
			{
			// stiamo inserendo una risposta ad un altro intervento
			// definiamo la chiave genitore del nuovo record
			$chiave_ordinamento = $riga->valore("chiave_ordinamento") . "_";
			$this->modulo->righeDB = $this->dammiRigheDB("SELECT * FROM interventi", $this->modulo->righeDB->connessioneDB, 0);
			$riga = $this->modulo->righeDB->righe[0];
			}
		
		if (!$riga)
			{
			if (!$this->haPrivilegio(PRIV_INTERVENTI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_INTERVENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			$riga->inserisciValore("id_intervento_genitore", $_GET['id_intervento_genitore']);
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
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$dbconn->iniziaTransazione();
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		if ($idInserito)
			{
			// se è un nuovo record ne calcoliamo la sua chiave di ordinamento
			$chiave_ordinamento .= str_pad($idInserito, 11, '0', STR_PAD_LEFT);
			if (strlen($chiave_ordinamento) > $this->modulo->righeDB->lunghezzaMaxCampo("chiave_ordinamento"))
				$this->mostraMessaggio("Troppe nidificazioni", "Attenzione: il thread ha troppe nidificazioni; riiniziate!", false, true);
			$riga->inserisciValore("chiave_ordinamento", $chiave_ordinamento);
			$this->salvaRigheDB($riga->righeDB);
			$id_intervento = $idInserito;
			}
		else
			$id_intervento = $_GET['id_intervento'];
			
		
		// creazione del file rss
		$this->creaRSSInterventi($this->modulo->righeDB->connessioneDB, $_GET['id_argomento']);
		
		$dbconn->confermaTransazione();
		
		// calcolo della pagina a cui ridirigere
		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		$sql = "SELECT FLOOR(posizione / $max_ap) AS nr_pagina" .
				" FROM" .
					"(select @rownum:=@rownum+1 AS posizione, interventi.id_intervento" .
					" from interventi, (SELECT @rownum:=-1) r" .
					" WHERE NOT interventi.sospeso" .
					" AND interventi.id_argomento=" . $dbconn->interoSql($_GET['id_argomento']) .
					" ORDER BY interventi.chiave_ordinamento" .
					") AS p" .
				" WHERE id_intervento=" . $dbconn->interoSql($id_intervento);
		$rs = $this->dammiRigheDB($sql, $dbconn);
		$nr_pagina =$rs->righe[0]->valore("nr_pagina");
		
		$this->ridireziona("argomento.php?id_argomento=$_GET[id_argomento]&pag_interventi=$nr_pagina#intervento_$id_intervento");
		}
		
	
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new argomento();
	