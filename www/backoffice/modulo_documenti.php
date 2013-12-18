<?
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_documenti extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_DOCUMENTI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_DOCUMENTI_ELIMINA, PRIV_DOCUMENTI_ELIMINA_PROPRI, $this->modulo->record);
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
		$this->aggiungiElemento("Scheda documento", "titolo");
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
						(!$riga && $this->haPrivilegio(PRIV_DOCUMENTI_INSERIMENTO)) ||
						(!$riga && $this->haPrivilegio(PRIV_DOCUMENTI_INSERIMENTO_PROPRI)) ||
						($riga && $this->haPrivilegio(PRIV_DOCUMENTI_MODIFICA, PRIV_DOCUMENTI_MODIFICA_PROPRI, $riga))
						);
		
		// l'utente di un documento è modificabile solo da chi ha privilegio 
		// generico di inserimento/modifica
		if (
			(!$riga && $this->haPrivilegio(PRIV_DOCUMENTI_INSERIMENTO)) || 
			($riga && $this->haPrivilegio(PRIV_DOCUMENTI_MODIFICA))
			)
			{
			$ctrl = $this->modulo->aggiungiSelezione("id_utente", "Autore", $solaLettura, !$solaLettura);
				$ctrl->sql = "SELECT id_utente, nickname FROM utenti" .
						" WHERE NOT sospeso" .
						($_GET['id_utente'] ? " AND id_utente=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_utente']) : '') .
						" ORDER BY nickname";
				$ctrl->valore = $_GET['id_utente'] ? $_GET['id_utente'] : $this->utente['id_utente'];
			}
		
		$ctrl = $this->modulo->aggiungiTesto("titolo", "Titolo", $solaLettura);
			$this->modulo->giustificaControllo($ctrl);
		
		$ctrl = $this->modulo->aggiungiCaricaFile("nome", "Documento", $solaLettura, true);
			$this->setUrlDoc($ctrl);
			
			
		if ($riga)
			$this->modulo->aggiungiDataOra("data_ora_creazione", "Data/Ora caricamento", true);

		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
						$riga && $this->haPrivilegio(PRIV_DOCUMENTI_ELIMINA, PRIV_DOCUMENTI_ELIMINA_PROPRI, $riga));

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
		$sql = "SELECT documenti.*" .
				" FROM documenti" .
				" WHERE documenti.id_documento=" . $dbconn->interoSql($_GET['id_documento']) . 
				" AND NOT documenti.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_documento'] ? 1 : 0);
		if ($_GET['id_documento'] && !$righeDB->righe)
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
			if (!$this->haPrivilegio(PRIV_DOCUMENTI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_DOCUMENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			
			// inseriamo di default l'utente loggato; se l'utente ha i privilegi
			// e ha definito un altro utente come proprietario del record,
			// allora questo valore sara ridefinito durante $this->modulo->salva();
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			}
		else 
			{
			$this->checkPrivilegio(PRIV_DOCUMENTI_MODIFICA, PRIV_DOCUMENTI_MODIFICA_PROPRI, $riga);
			$this->verificaViolazioneLock($this->modulo);
			}
			
		$this->setEditorData($riga);
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$dbconn->iniziaTransazione();
		$this->modulo->salva();
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		$this->salvaDoc($this->modulo->controlliInput["nome"]);
		$dbconn->confermaTransazione();
		
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_documenti();
