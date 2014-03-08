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
			$this->eliminaRecordCommenti();
			}
		else
			{
			// se non esiste ancora il file rss lo creiamo
			if (!file_exists("$this->directoryDoc/rss/$this->fileRSSCommenti.$_GET[id_articolo].xml"))
				$this->creaRSSCommenti($dbconn, $_GET['id_articolo']);

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
		$sql = "SELECT DISTINCT commenti.*," .
			" utenti.nickname," . 
			" utenti.avatar," . 
			" (select count(*) from commenti as c" .
				" WHERE not c.sospeso" .
				" and c.id_commento_genitore=commenti.id_commento)" .
				" AS tengo_famiglia" .
			" FROM commenti" .
			" INNER JOIN utenti ON commenti.id_utente=utenti.id_utente" .
			" WHERE NOT commenti.sospeso" .
			" AND commenti.id_articolo=" . $dbconn->interoSql($_GET['id_articolo']) .
			" ORDER BY commenti.chiave_ordinamento";
				
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
		$id_commento = $_GET['id_commento_genitore'] ? $_GET['id_commento_genitore'] : $_GET['id_commento'];
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT commenti.*" .
				" FROM commenti" .
				" WHERE commenti.id_commento=" . $dbconn->interoSql($id_commento) . 
				" AND NOT commenti.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $id_commento ? 1 : 0);
		if ($id_commento && !$righeDB->righe)
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);

		return $righeDB;
		}
		
	//***************************************************************************
	function eliminaRecordCommenti()
		{
		$this->checkPrivilegio(PRIV_COMMENTI_ELIMINA, PRIV_COMMENTI_ELIMINA_PROPRI, $this->modulo->record);
		// occorre verificare che non tenga famiglia
		$r = $this->modulo->record;
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$sql = "select count(*) AS tengo_famiglia from commenti as c" .
					" WHERE not c.sospeso" .
					" and c.id_commento_genitore=" . $dbconn->interoSql($r->valore("id_commento"));
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		if ($rs->righe[0]->valore("tengo_famiglia"))
			$this->mostraMessaggio("Impossibile eliminare", "Il record è genitore in un thread", false, true);
		$this->eliminaRecord($r, false);
		$this->creaRSSCommenti($dbconn, $_GET['id_articolo']);
		$this->ridireziona("?id_articolo=$_GET[id_articolo]&pag_commenti=$_GET[pag_commenti]#commenti");
		
		}
		
	//***************************************************************************
	function aggiornaRecordCommenti()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->righe[0];
			
		if ($_GET['id_commento_genitore'])
			{
			// stiamo inserendo una risposta ad un altro commento
			// definiamo la chiave genitore del nuovo record
			$chiave_ordinamento = $riga->valore("chiave_ordinamento") . "_";
			$this->modulo->righeDB = $this->dammiRigheDB("SELECT * FROM commenti", $this->modulo->righeDB->connessioneDB, 0);
			$riga = $this->modulo->righeDB->righe[0];
			}
		
		if (!$riga)
			{
			if (!$this->haPrivilegio(PRIV_COMMENTI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_COMMENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			$riga->inserisciValore("id_commento_genitore", $_GET['id_commento_genitore']);
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
			$id_commento = $idInserito;
			}
		else
			$id_commento = $_GET['id_commento'];
			
		// creazione del file rss
		$this->creaRSSCommenti($this->modulo->righeDB->connessioneDB, $_GET['id_articolo']);
		
		$dbconn->confermaTransazione();
		
		// calcolo della pagina a cui ridirigere
		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		$sql = "SELECT FLOOR(posizione / $max_ap) AS nr_pagina" .
				" FROM" .
					"(select @rownum:=@rownum+1 AS posizione, commenti.id_commento" .
					" from commenti, (SELECT @rownum:=-1) r" .
					" WHERE NOT commenti.sospeso" .
					" AND commenti.id_articolo=" . $dbconn->interoSql($_GET['id_articolo']) .
					" ORDER BY commenti.chiave_ordinamento" .
					") AS p" .
				" WHERE id_commento=" . $dbconn->interoSql($id_commento);
		$rs = $this->dammiRigheDB($sql, $dbconn);
		$nr_pagina =$rs->righe[0]->valore("nr_pagina");
		
		$this->ridireziona("articolo.php?id_articolo=$_GET[id_articolo]&pag_commenti=$nr_pagina#commento_$id_commento");
		}
		
	
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new articolo();
	