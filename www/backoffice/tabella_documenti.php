<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_documenti extends backoffice
	{


	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_DOCUMENTI_VEDI);
		$this->finestraFiglia = ($_GET['id_utente']);
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
		$this->aggiungiElemento("Elenco documenti" . $this->dammiTitoloGenitore('id_utente'), "titolo");
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
		$sql = "SELECT documenti.*," .
			" utenti.nickname" . 
			" FROM documenti" .
			" INNER JOIN utenti ON documenti.id_utente=utenti.id_utente" .
			" WHERE NOT documenti.sospeso" .
			($_GET['id_utente'] ? " AND documenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
			" ORDER BY documenti.id_documento DESC";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_DOCUMENTI_INSERIMENTO, PRIV_DOCUMENTI_INSERIMENTO_PROPRI));
		$tabella->paginaModulo = "modulo_documenti.php?id_utente=$_GET[id_utente]";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		$tabella->aggiungiColonna("id_documento", "ID");
		$col = $tabella->aggiungiColonna("nickname", "Autore");
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_utente'] ? false : true;
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
		$col = $tabella->aggiungiColonna("nome", "Nome");
		$col = $tabella->aggiungiColonna("link", "Link");
			$col->funzioneCalcolo = array($this, "dammiLinkDocumento");
			$col->link = true;
			$col->convertiHTML = false;
			
		$tabella->aggiungiColonna("data_ora_creazione", "Data caricamento");

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_DOCUMENTI_MODIFICA, PRIV_DOCUMENTI_MODIFICA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_DOCUMENTI_ELIMINA, PRIV_DOCUMENTI_ELIMINA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function dammiLinkDocumento(waTabella $tabella)
		{
		$qs = "tabella=documenti&amp;tipo=nome&amp;id=" . $tabella->record->valore("id_documento");
		return "$this->httpwd/downloaddoc.php?$qs";
		}
		
	}

// fine classe pagina
//*****************************************************************************
// istanzia la pagina
new tabella_documenti();