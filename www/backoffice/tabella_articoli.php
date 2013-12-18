<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_articoli extends backoffice
	{


	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_ARTICOLI_VEDI);
		$this->finestraFiglia = ($_GET['id_utente'] || $_GET['id_categoria_articolo']);
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
		$this->aggiungiElemento("Elenco articoli" . 
					$this->dammiTitoloGenitore($_GET['id_utente'] ? 'id_utente' : 'id_categoria_articolo'), 
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
		$sql = "SELECT articoli.*," .
			" categorie_articoli.nome as nome_categoria," .
			" utenti.nickname" . 
			" FROM articoli" .
			" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
			" INNER JOIN utenti ON articoli.id_utente=utenti.id_utente" .
			" WHERE NOT articoli.sospeso" .
			($_GET['id_utente'] ? " AND articoli.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
			($_GET['id_categoria_articolo'] ? " AND articoli.id_categoria_articolo=" . $dbconn->interoSql($_GET['id_categoria_articolo']) : '') .
			" ORDER BY articoli.id_articolo DESC";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_ARTICOLI_INSERIMENTO, PRIV_ARTICOLI_INSERIMENTO_PROPRI));
		$tabella->paginaModulo = "modulo_articoli.php?id_utente=$_GET[id_utente]&id_categoria_articolo=$_GET[id_categoria_articolo]";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if (!$_GET['html'])
			$tabella->aggiungiAzione("HTML");
		else
			$tabella->aggiungiAzione("NO_HTML", false, "NO HTML");
		if ($this->haPrivilegio(PRIV_COMMENTI_VEDI))
			$tabella->aggiungiAzione("Commenti", true);
		
		$tabella->aggiungiColonna("id_articolo", "ID");
		
		$col = $tabella->aggiungiColonna("nome_categoria", "Categoria");
			$col->aliasDi = "categorie_articoli.nome";
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_categoria_articolo'] ? false : true;
		
		$col = $tabella->aggiungiColonna("nickname", "Autore");
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_utente'] ? false : true;
		
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
			$col->aliasDi = "articoli.titolo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTitolo");
		$col = $tabella->aggiungiColonna("abstract", "Abstract");
			$col->aliasDi = "articoli.abstract";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlAbstract");
		$col = $tabella->aggiungiColonna("testo", "Testo");
			$col->aliasDi = "articoli.testo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTesto");
			
		$col = $tabella->aggiungiColonna("data_ora_creazione", "Data creazione");
			$col->aliasDi = "articoli.data_ora_creazione";
		$tabella->aggiungiColonna("data_ora_inizio_pubblicazione", "Data/Ora inizio pubblicazione");
		$tabella->aggiungiColonna("data_ora_fine_pubblicazione", "Data/Ora fine pubblicazione");

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_ARTICOLI_MODIFICA, PRIV_ARTICOLI_MODIFICA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_ARTICOLI_ELIMINA, PRIV_ARTICOLI_ELIMINA_PROPRI, $tabella->record);
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

	//*****************************************************************************
	// dato un campo che potrebbe contenere html, lo ritorna bovinamente
	// strippato
	function checkHtmlTesto(waTabella $tabella)
		{
		return $this->checkHtmlCampo($tabella->record->valore("testo"));
		}

	}

// fine classe pagina
//*****************************************************************************
// istanzia la pagina
new tabella_articoli();