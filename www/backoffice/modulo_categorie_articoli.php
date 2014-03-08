<?php
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_categorie_articoli extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_CATEGORIE_ARTICOLI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_CATEGORIE_ARTICOLI_ELIMINA);
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
		$this->aggiungiElemento("Scheda categoria articolo", "titolo");
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
						(!$riga && $this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_INSERIMENTO)) ||
						($riga && $this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_MODIFICA))
						);
		
		$ctrl = $this->modulo->aggiungiTesto("nome", "Nome", $solaLettura, true);
		
		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
						$riga && $this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_ELIMINA));

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
		$sql = "SELECT categorie_articoli.*" .
				" FROM categorie_articoli" .
				" WHERE categorie_articoli.id_categoria_articolo=" . $dbconn->interoSql($_GET['id_categoria_articolo']) . 
				" AND NOT categorie_articoli.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_categoria_articolo'] ? 1 : 0);
		if ($_GET['id_categoria_articolo'] && !$righeDB->righe)
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
			$this->checkPrivilegio(PRIV_CATEGORIE_ARTICOLI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			}
		else 
			{
			$this->checkPrivilegio(PRIV_CATEGORIE_ARTICOLI_MODIFICA);
			$this->verificaViolazioneLock($this->modulo);
			}
			
		$this->setEditorData($riga);
		$this->modulo->salva();
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $this->modulo->righeDB->connessioneDB->ultimoIdInserito();
		
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_categorie_articoli();
