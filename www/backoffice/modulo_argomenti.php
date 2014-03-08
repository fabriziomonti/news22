<?php
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_argomenti extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_ARGOMENTI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_ARGOMENTI_ELIMINA, PRIV_ARGOMENTI_ELIMINA_PROPRI, $this->modulo->record);
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
		$this->aggiungiElemento("Scheda argomento", "titolo");
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
						(!$riga && $this->haPrivilegio(PRIV_ARGOMENTI_INSERIMENTO)) ||
						(!$riga && $this->haPrivilegio(PRIV_ARGOMENTI_INSERIMENTO_PROPRI)) ||
						($riga && $this->haPrivilegio(PRIV_ARGOMENTI_MODIFICA, PRIV_ARGOMENTI_MODIFICA_PROPRI, $riga))
						);
		
		// l'utente di un argomento è modificabile solo da chi ha privilegio 
		// generico di inserimento/modifica
		if (
			(!$riga && $this->haPrivilegio(PRIV_ARGOMENTI_INSERIMENTO)) || 
			($riga && $this->haPrivilegio(PRIV_ARGOMENTI_MODIFICA))
			)
			{
			$ctrl = $this->modulo->aggiungiSelezione("id_utente", "Autore", $solaLettura, !$solaLettura);
				$ctrl->sql = "SELECT id_utente, nickname FROM utenti WHERE NOT sospeso ORDER BY nickname";
				$ctrl->valore = $this->utente['id_utente'];
			}
		
		$ctrl = $this->modulo->aggiungiSelezione("id_categoria_argomento", "Categoria", $solaLettura, !$solaLettura);
			$ctrl->sql = "SELECT id_categoria_argomento, nome FROM categorie_argomenti" .
					" WHERE NOT sospeso" .
					($_GET['id_categoria_argomento'] ? " AND id_categoria_argomento=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_categoria_argomento']) : '') .
					" ORDER BY nome";
			$ctrl->valore = $_GET['id_categoria_argomento'];
			
		$ctrl = $this->modulo->aggiungiTesto("titolo", "Titolo", $solaLettura, true);
			$this->modulo->giustificaControllo($ctrl);
		$this->aggiungiAreaTesto_ext($this->modulo, "abstract", "Abstract", $solaLettura);
		
		if ($riga)
			$this->modulo->aggiungiDataOra("data_ora_creazione", "Data/Ora creazione", true);
		$this->modulo->aggiungiDataOra("data_ora_inizio_pubblicazione", "Data/Ora inizio pubblicazione", $solaLettura);
		$this->modulo->aggiungiDataOra("data_ora_fine_pubblicazione", "Data/Ora fine pubblicazione", $solaLettura);
		$this->modulo->aggiungiAreaTesto("tags", "Tags", $solaLettura);

		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
						$riga && $this->haPrivilegio(PRIV_ARGOMENTI_ELIMINA, PRIV_ARGOMENTI_ELIMINA_PROPRI, $riga));

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
		$sql = "SELECT argomenti.*" .
				" FROM argomenti" .
				" WHERE argomenti.id_argomento=" . $dbconn->interoSql($_GET['id_argomento']) . 
				" AND NOT argomenti.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_argomento'] ? 1 : 0);
		if ($_GET['id_argomento'] && !$righeDB->righe)
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
			if (!$this->haPrivilegio(PRIV_ARGOMENTI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_ARGOMENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			
			// inseriamo di default l'utente loggato; se l'utente ha i privilegi
			// e ha definito un altro utente come proprietario del record,
			// allora questo valore sara ridefinito durante $this->modulo->salva();
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			}
		else 
			{
			$this->checkPrivilegio(PRIV_ARGOMENTI_MODIFICA, PRIV_ARGOMENTI_MODIFICA_PROPRI, $riga);
			$this->verificaViolazioneLock($this->modulo);
			}
			
		$this->setEditorData($riga);
		$this->modulo->salva();
		// controllo sanitario
		$this->sanificaHTML($riga, "titolo");
		$this->sanificaHTML($riga, "abstract");
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_argomenti();
