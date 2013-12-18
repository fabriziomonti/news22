<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_argomenti extends backoffice
	{


	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_ARGOMENTI_VEDI);
		$this->finestraFiglia = ($_GET['id_utente'] || $_GET['id_categoria_argomento']);
		$this->mostraPagina();
		}

	//*****************************************************************************
	/**
	 * mostraPagina
	 * 
	 * costruisce la pagina contenente il modulo e la manda in output
	 * @return void
	 */
	function mostraPagina()
		{
		
		$this->aggiungiElemento($this->dammiMenu());
		$this->aggiungiElemento("Elenco argomenti" . 
					$this->dammiTitoloGenitore($_GET['id_utente'] ? 'id_utente' : 'id_categoria_argomento'), 
					"titolo");
		$this->aggiungiElemento($this->dammiTabella());
		$this->mostra();
		}

	//*****************************************************************************
	/**
	 * @return waTabella
	 */
	function dammiTabella()
		{
		// creazione della tabella
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT argomenti.*," .
			" categorie_argomenti.nome as nome_categoria," .
			" utenti.nickname" . 
			" FROM argomenti" .
			" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
			" INNER JOIN utenti ON argomenti.id_utente=utenti.id_utente" .
			" WHERE NOT argomenti.sospeso" .
			($_GET['id_utente'] ? " AND argomenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
			($_GET['id_categoria_argomento'] ? " AND argomenti.id_categoria_argomento=" . $dbconn->interoSql($_GET['id_categoria_argomento']) : '') .
			" ORDER BY argomenti.id_argomento DESC";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_ARGOMENTI_INSERIMENTO, PRIV_ARGOMENTI_INSERIMENTO_PROPRI));
		$tabella->paginaModulo = "modulo_argomenti.php?id_utente=$_GET[id_utente]&id_categoria_argomento=$_GET[id_categoria_argomento]";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if (!$_GET['html'])
			$tabella->aggiungiAzione("HTML");
		else
			$tabella->aggiungiAzione("NO_HTML", false, "NO HTML");
		if ($this->haPrivilegio(PRIV_INTERVENTI_VEDI))
			$tabella->aggiungiAzione("Interventi", true);
		
		$tabella->aggiungiColonna("id_argomento", "ID");
		$col = $tabella->aggiungiColonna("nickname", "Autore");
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_utente'] ? false : true;
			
		$col = $tabella->aggiungiColonna("nome_categoria", "Categoria");
			$col->aliasDi = "categorie_argomenti.nome";
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_categoria_argomento'] ? false : true;
		
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
			$col->aliasDi = "argomenti.titolo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTitolo");
		$col = $tabella->aggiungiColonna("abstract", "Abstract");
			$col->aliasDi = "argomenti.abstract";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlAbstract");
			
		$tabella->aggiungiColonna("data_ora_creazione", "Data creazione");
		$tabella->aggiungiColonna("data_ora_inizio_pubblicazione", "Data/Ora inizio pubblicazione");
		$tabella->aggiungiColonna("data_ora_fine_pubblicazione", "Data/Ora fine pubblicazione");

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_ARGOMENTI_MODIFICA, PRIV_ARGOMENTI_MODIFICA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_ARGOMENTI_ELIMINA, PRIV_ARGOMENTI_ELIMINA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	// dato un campo che potrebbe contenere html, lo ritorna bovinamente
	// strippato
	function checkHtmlTitolo(waTabella $tabella)
		{
		return $this->checkHtmlCampo($tabella->record->valore("titolo"));
		}

	//*****************************************************************************
	// dato un campo che potrebbe contenere html, lo ritorna bovinamente
	// strippato
	function checkHtmlAbstract(waTabella $tabella)
		{
		return $this->checkHtmlCampo($tabella->record->valore("abstract"));
		}

	}

// fine classe pagina
//*****************************************************************************
// istanzia la pagina
new tabella_argomenti();