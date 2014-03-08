<?php
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_articoli extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_ARTICOLI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_ARTICOLI_ELIMINA, PRIV_ARTICOLI_ELIMINA_PROPRI, $this->modulo->record);
			$this->eliminaRecord($this->modulo->record, false);
			$this->creaRSSArticoli($this->modulo->righeDB->connessioneDB);
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
		$this->aggiungiElemento("Scheda articolo", "titolo");
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
						(!$riga && $this->haPrivilegio(PRIV_ARTICOLI_INSERIMENTO)) ||
						(!$riga && $this->haPrivilegio(PRIV_ARTICOLI_INSERIMENTO_PROPRI)) ||
						($riga && $this->haPrivilegio(PRIV_ARTICOLI_MODIFICA, PRIV_ARTICOLI_MODIFICA_PROPRI, $riga))
						);
		
		// l'utente di un articolo è modificabile solo da chi ha privilegio 
		// generico di inserimento/modifica
		if (
			(!$riga && $this->haPrivilegio(PRIV_ARTICOLI_INSERIMENTO)) || 
			($riga && $this->haPrivilegio(PRIV_ARTICOLI_MODIFICA))
			)
			{
			$ctrl = $this->modulo->aggiungiSelezione("id_utente", "Autore", $solaLettura, !$solaLettura);
				$ctrl->sql = "SELECT id_utente, nickname FROM utenti" .
						" WHERE NOT sospeso" .
						($_GET['id_utente'] ? " AND id_utente=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_utente']) : '') .
						" ORDER BY nickname";
				$ctrl->valore = $_GET['id_utente'] ? $_GET['id_utente'] : $this->utente['id_utente'];
			}
		
		$ctrl = $this->modulo->aggiungiSelezione("id_categoria_articolo", "Categoria", $solaLettura, !$solaLettura);
			$ctrl->sql = "SELECT id_categoria_articolo, nome FROM categorie_articoli" .
					" WHERE NOT sospeso" .
					($_GET['id_categoria_articolo'] ? " AND id_categoria_articolo=" . $this->modulo->righeDB->connessioneDB->interoSql($_GET['id_categoria_articolo']) : '') .
					" ORDER BY nome";
			$ctrl->valore = $_GET['id_categoria_articolo'];
			
		$ctrl = $this->modulo->aggiungiTesto("titolo", "Titolo", $solaLettura, true);
			$this->modulo->giustificaControllo($ctrl);
		$this->aggiungiAreaTesto_ext($this->modulo, "abstract", "Abstract", $solaLettura);
		$this->aggiungiAreaTesto_ext($this->modulo, "testo", "Testo", $solaLettura, true);
		
		if ($riga)
			$this->modulo->aggiungiDataOra("data_ora_creazione", "Data/Ora creazione", true);
		$this->modulo->aggiungiDataOra("data_ora_inizio_pubblicazione", "Data/Ora inizio pubblicazione", $solaLettura)->valore = time();
		$this->modulo->aggiungiDataOra("data_ora_fine_pubblicazione", "Data/Ora fine pubblicazione", $solaLettura);
		$this->modulo->aggiungiAreaTesto("tags", "Tags", $solaLettura);

		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
						$riga && $this->haPrivilegio(PRIV_ARTICOLI_ELIMINA, PRIV_ARTICOLI_ELIMINA_PROPRI, $riga));

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
		$sql = "SELECT articoli.*" .
				" FROM articoli" .
				" WHERE articoli.id_articolo=" . $dbconn->interoSql($_GET['id_articolo']) . 
				" AND NOT articoli.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_articolo'] ? 1 : 0);
		if ($_GET['id_articolo'] && !$righeDB->righe)
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
			if (!$this->haPrivilegio(PRIV_ARTICOLI_INSERIMENTO_PROPRI))
				$this->checkPrivilegio(PRIV_ARTICOLI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("data_ora_creazione", time());
			
			// inseriamo di default l'utente loggato; se l'utente ha i privilegi
			// e ha definito un altro utente come proprietario del record,
			// allora questo valore sara ridefinito durante $this->modulo->salva();
			$riga->inserisciValore("id_utente", $this->utente['id_utente']);
			}
		else 
			{
			$this->checkPrivilegio(PRIV_ARTICOLI_MODIFICA, PRIV_ARTICOLI_MODIFICA_PROPRI, $riga);
			$this->verificaViolazioneLock($this->modulo);
			}
			
		$this->setEditorData($riga);
		$this->modulo->salva();
		// controllo sanitario
		$this->sanificaHTML($riga, "titolo");
		$this->sanificaHTML($riga, "abstract");
		$this->sanificaHTML($riga, "testo");
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		$this->creaRSSArticoli($this->modulo->righeDB->connessioneDB);
		
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_articoli();
