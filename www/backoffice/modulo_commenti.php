<?
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_commenti extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_COMMENTI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_COMMENTI_ELIMINA, PRIV_COMMENTI_ELIMINA_PROPRI, $this->modulo->record);
			$id_articolo = $this->modulo->record->valore("id_articolo");
			$this->eliminaRecord($this->modulo->record, false);
			$this->creaRSSCommenti($this->modulo->righeDB->connessioneDB, $id_articolo);
			}
		else
			{
			$this->mostraPagina();
			}
		}

	//*****************************************************************************
	/**
	* mostra
	* 
	* costruisce la pagina contenente il modulo e la manda in output
	* @return void
	*/
	function mostraPagina()
		{
		$this->aggiungiElemento("Scheda commento", "titolo");
		$this->aggiungiElemento($this->modulo);
		$this->mostra();
			
		}
		
	//***************************************************************************
	function creaModulo()
		{
		$this->modulo = $this->dammiModulo();
		$this->modulo->righeDB = $this->dammiRecordset();
		$riga = $this->modulo->righeDB->righe[0];
		// i controlli sono editabili se:
		// - non c'e' record (siamo in iserimento) e abbiamo privilegio di inserimento
		// - c'e' record (siamo in modifica) e abbiamo privilegio di modifica
		$solaLettura = !(
						(!$riga && $this->haPrivilegio(PRIV_COMMENTI_INSERIMENTO)) ||
						(!$riga && $this->haPrivilegio(PRIV_COMMENTI_INSERIMENTO_PROPRI)) ||
						($riga && $this->haPrivilegio(PRIV_COMMENTI_MODIFICA, PRIV_COMMENTI_MODIFICA_PROPRI, $riga))
						);
		

		// l'utente di un commento è modificabile solo da chi ha privilegio 
		// generico di inserimento/modifica
		if (
			(!$riga && $this->haPrivilegio(PRIV_COMMENTI_INSERIMENTO)) || 
			($riga && $this->haPrivilegio(PRIV_COMMENTI_MODIFICA))
			)
			{
			$ctrl = $this->modulo->aggiungiSelezione("id_utente", "Autore", $solaLettura, !$solaLettura);
				$ctrl->sql = "SELECT id_utente, nickname FROM utenti" .
						" WHERE NOT sospeso" .
						($_GET['id_utente'] ? " AND id_utente=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_utente']) : '') .
						" ORDER BY nickname";
				$ctrl->valore = $_GET['id_utente'] ? $_GET['id_utente'] : $this->utente['id_utente'];
			}
			
		$ctrl = $this->modulo->aggiungiSelezione("id_articolo", "Articolo", $solaLettura, !$solaLettura);
			$ctrl->sql = "SELECT id_articolo, CONCAT(DATE_FORMAT(data_ora_inizio_pubblicazione, '%d/%m/%Y'), ' - ', STRIP_TAGS(titolo))" .
							" FROM articoli" .
							" WHERE NOT sospeso" .
							" AND data_ora_inizio_pubblicazione IS NOT NULL" .
							($_GET['id_articolo'] ? " AND id_articolo=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_articolo']) : '') .
							" ORDER BY data_ora_inizio_pubblicazione desc";
			$ctrl->valore = $_GET['id_articolo'];
			$this->modulo->giustificaControllo($ctrl, false);
		
		$ctrl = $this->modulo->aggiungiSelezione("id_commento_genitore", "In risposta a", $solaLettura);
		$this->modulo->giustificaControllo($ctrl, false);
		if ($riga)
			$ctrl->sql = $this->dammiSqlCommentiGenitori ($riga->valore("id_articolo"));
		elseif ($_GET['id_articolo'])
			$ctrl->sql = $this->dammiSqlCommentiGenitori ($_GET['id_articolo']);
		
		$this->aggiungiAreaTesto_ext($this->modulo, "testo", "Testo", $solaLettura, true);
		
		if ($riga)
			{
			$ctrl = $this->modulo->aggiungiDataOra("data_ora_creazione", "Data/Ora creazione", true);
			$ctrl->mostraSecondi = true;
			}

		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
								$riga && $this->haPrivilegio(PRIV_COMMENTI_ELIMINA, PRIV_COMMENTI_ELIMINA_PROPRI, $riga));

		$this->modulo->leggiValoriIngresso();
		}

	//***************************************************************************
	/**
	* -
	*
	* @return waRigheDB
	*/
	function dammiRecordset()
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
	function aggiornaRecord()
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
			$this->verificaViolazioneLock($this->modulo);
			}
			
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
			$chiave_ordinamento = $this->dammiChiaveOrdinamentoGenitore($riga) . str_pad($idInserito, 11, '0', STR_PAD_LEFT);
			if (strlen($chiave_ordinamento) > $this->modulo->righeDB->lunghezzaMaxCampo("chiave_ordinamento"))
				$this->mostraMessaggio("Troppe nidificazioni", "Attenzione: il thread ha troppe nidificazioni; riiniziate!", false, true);
			$riga->inserisciValore("chiave_ordinamento", $chiave_ordinamento);
			$this->salvaRigheDB($riga->righeDB);
			}
		
		$this->creaRSSCommenti($this->modulo->righeDB->connessioneDB, $riga->valore("id_articolo"));
		
		$dbconn->confermaTransazione();
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	
	//***************************************************************************
	/**
	 */
	function dammiSqlCommentiGenitori($id_articolo)
		{
		$dbconn = $this->modulo->righeDB->connessioneDB;
		return "SELECT commenti.id_commento, CONCAT(utenti.nickname, ' - ', DATE_FORMAT(commenti.data_ora_creazione, '%d/%m/%Y %H.%i.%s'), ' - ', LEFT(STRIP_TAGS(commenti.testo), 100)) AS descrizione_commento" .
						" FROM commenti" .
						" INNER JOIN utenti ON commenti.id_utente=utenti.id_utente" .
						" WHERE NOT commenti.sospeso" .
						" AND commenti.id_articolo=" . $dbconn->interoSql($id_articolo) .
						($_GET['id_commento'] ? " AND commenti.id_commento!=" . $dbconn->interoSql($_GET['id_commento']) : '') .
						" ORDER BY commenti.data_ora_creazione desc";
		}
		
	//***************************************************************************
	/**
	 * funzione rpc: restituisce la lista dei commenti dato un id_articolo
	 */
	function rpc_dammiListaCommenti($id_articolo)
		{
		$retval = array();
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$sql = $this->dammiSqlCommentiGenitori($id_articolo);
		
		$rs = $this->dammiRigheDB($sql, $dbconn);
		foreach ($rs->righe as $riga)
			$retval[$riga->valore("id_commento")] = $riga->valore("descrizione_commento");
		
		return $retval;
		
		}
		
	//***************************************************************************
	/**
	 */
	function dammiChiaveOrdinamentoGenitore(waRecord $riga)
		{
		if (!$riga->valore("id_commento_genitore"))
			return '';
		
		$dbconn = $riga->righeDB->connessioneDB;
		$sql = "SELECT commenti.chiave_ordinamento" .
						" FROM commenti" .
						" WHERE commenti.id_commento=" . $dbconn->interoSql($riga->valore("id_commento_genitore")) ;
		return $this->dammiRigheDB($sql, $dbconn, 1)->righe[0]->valore(0) . "_";
		}
		
		
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_commenti();
