<?php
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_interventi extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_INTERVENTI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_INTERVENTI_ELIMINA, PRIV_INTERVENTI_ELIMINA_PROPRI, $this->modulo->record);
			$this->eliminaRecord($this->modulo->record);
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
		$this->aggiungiElemento("Scheda intervento", "titolo");
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
						(!$riga && $this->haPrivilegio(PRIV_INTERVENTI_INSERIMENTO)) ||
						(!$riga && $this->haPrivilegio(PRIV_INTERVENTI_INSERIMENTO_PROPRI)) ||
						($riga && $this->haPrivilegio(PRIV_INTERVENTI_MODIFICA, PRIV_INTERVENTI_MODIFICA_PROPRI, $riga))
						);
		

		// l'utente di un intervento è modificabile solo da chi ha privilegio 
		// generico di inserimento/modifica
		if (
			(!$riga && $this->haPrivilegio(PRIV_INTERVENTI_INSERIMENTO)) || 
			($riga && $this->haPrivilegio(PRIV_INTERVENTI_MODIFICA))
			)
			{
			$ctrl = $this->modulo->aggiungiSelezione("id_utente", "Autore", $solaLettura, !$solaLettura);
				$ctrl->sql = "SELECT id_utente, nickname FROM utenti" .
						" WHERE NOT sospeso" .
						($_GET['id_utente'] ? " AND id_utente=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_utente']) : '') .
						" ORDER BY nickname";
				$ctrl->valore = $_GET['id_utente'] ? $_GET['id_utente'] : $this->utente['id_utente'];
			}
			
		$ctrl = $this->modulo->aggiungiSelezione("id_argomento", "Argomento", $solaLettura, !$solaLettura);
			$ctrl->sql = "SELECT id_argomento, CONCAT(DATE_FORMAT(data_ora_inizio_pubblicazione, '%d/%m/%Y'), ' - ', STRIP_TAGS(titolo))" .
							" FROM argomenti" .
							" WHERE NOT sospeso" .
							" AND data_ora_inizio_pubblicazione IS NOT NULL" .
							($_GET['id_argomento'] ? " AND id_argomento=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_argomento']) : '') .
							" ORDER BY data_ora_inizio_pubblicazione desc";
			$ctrl->valore = $_GET['id_argomento'];
			$this->modulo->giustificaControllo($ctrl, false);
		
		$ctrl = $this->modulo->aggiungiSelezione("id_intervento_genitore", "In risposta a", $solaLettura);
		$this->modulo->giustificaControllo($ctrl, false);
		if ($riga)
			$ctrl->sql = $this->dammiSqlInterventiGenitori ($riga->valore("id_argomento"));
		elseif ($_GET['id_argomento'])
			$ctrl->sql = $this->dammiSqlInterventiGenitori ($_GET['id_argomento']);
		
		$this->aggiungiAreaTesto_ext($this->modulo, "testo", "Testo", $solaLettura, true);
		
		if ($riga)
			{
			$ctrl = $this->modulo->aggiungiDataOra("data_ora_creazione", "Data/Ora creazione", true);
			$ctrl->mostraSecondi = true;
			}

		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
								$riga && $this->haPrivilegio(PRIV_INTERVENTI_ELIMINA, PRIV_INTERVENTI_ELIMINA_PROPRI, $riga));

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
	function aggiornaRecord()
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
		
		$this->creaRSSInterventi($this->modulo->righeDB->connessioneDB, $riga->id_argomento);
		$this->mailNuovoIntervento($riga->id_argomento, $idInserito, $this->modulo->righeDB->connessioneDB);
		
		$dbconn->confermaTransazione();
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	
	//***************************************************************************
	/**
	 */
	function dammiSqlInterventiGenitori($id_argomento)
		{
		$dbconn = $this->modulo->righeDB->connessioneDB;
		return "SELECT interventi.id_intervento, CONCAT(utenti.nickname, ' - ', DATE_FORMAT(interventi.data_ora_creazione, '%d/%m/%Y %H.%i.%s'), ' - ', LEFT(STRIP_TAGS(interventi.testo), 100)) AS descrizione_intervento" .
						" FROM interventi" .
						" INNER JOIN utenti ON interventi.id_utente=utenti.id_utente" .
						" WHERE NOT interventi.sospeso" .
						" AND interventi.id_argomento=" . $dbconn->interoSql($id_argomento) .
						($_GET['id_intervento'] ? " AND interventi.id_intervento!=" . $dbconn->interoSql($_GET['id_intervento']) : '') .
						" ORDER BY interventi.data_ora_creazione desc";
		}
		
	//***************************************************************************
	/**
	 * funzione rpc: restituisce la lista dei interventi dato un id_argomento
	 */
	function rpc_dammiListaInterventi($id_argomento)
		{
		$retval = array();
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$sql = $this->dammiSqlInterventiGenitori($id_argomento);
		
		$rs = $this->dammiRigheDB($sql, $dbconn);
		foreach ($rs->righe as $riga)
			$retval[$riga->valore("id_intervento")] = $riga->valore("descrizione_intervento");
		
		return $retval;
		
		}
		
	//***************************************************************************
	/**
	 */
	function dammiChiaveOrdinamentoGenitore(waRecord $riga)
		{
		if (!$riga->valore("id_intervento_genitore"))
			return '';
		
		$dbconn = $riga->righeDB->connessioneDB;
		$sql = "SELECT interventi.chiave_ordinamento" .
						" FROM interventi" .
						" WHERE interventi.id_intervento=" . $dbconn->interoSql($riga->valore("id_intervento_genitore")) ;
		return $this->dammiRigheDB($sql, $dbconn, 1)->righe[0]->valore(0) . "_";
		}
		
		
		
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_interventi();
