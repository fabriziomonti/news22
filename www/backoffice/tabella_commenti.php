<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_commenti extends backoffice
	{

	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_COMMENTI);
		$this->finestraFiglia = ($_GET['id_articolo'] || $_GET['id_utente']);
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
		$this->aggiungiElemento("Elenco commenti" . 
					$this->dammiTitoloGenitore($_GET['id_utente'] ? 'id_utente' : 'id_articolo'), 
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
		$sql = "SELECT commenti.*," .
			" articoli.titolo AS titolo_articolo," .
			" utenti.nickname," . 
			" IF(commenti.id_commento_genitore, CONCAT(utenti_commenti_genitori.nickname, ' - ', DATE_FORMAT(commenti_genitori.data_ora_creazione, '%d/%m/%Y %H.%i.%s')), '') AS commento_genitore" .
			" FROM commenti" .
			" INNER JOIN articoli ON commenti.id_articolo=articoli.id_articolo" .
			" INNER JOIN utenti ON commenti.id_utente=utenti.id_utente" .
			" LEFT JOIN commenti AS commenti_genitori ON commenti.id_commento_genitore=commenti_genitori.id_commento" . 
			" LEFT JOIN utenti AS utenti_commenti_genitori ON commenti_genitori.id_utente=utenti_commenti_genitori.id_utente" .
			" WHERE NOT commenti.sospeso" .
			($_GET['id_articolo'] ? " AND commenti.id_articolo=" . $dbconn->interoSql($_GET['id_articolo']) : '') .
			($_GET['id_utente'] ? " AND commenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
			" ORDER BY commenti.id_commento DESC";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_COMMENTI_INSERIMENTO, PRIV_COMMENTI_INSERIMENTO_PROPRI));
		$tabella->paginaModulo = "modulo_commenti.php?id_articolo=$_GET[id_articolo]&id_utente=$_GET[id_utente]";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if (!$_GET['html'])
			$tabella->aggiungiAzione("HTML");
		else
			$tabella->aggiungiAzione("NO_HTML", false, "NO HTML");
		
		$col = $tabella->aggiungiColonna("id_commento", "ID");
			$col->aliasDi = "commenti.id_commento";
		
		$col = $tabella->aggiungiColonna("titolo_articolo", "Articolo");
			$col->aliasDi = "articoli.titolo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTitolo");

		$col = $tabella->aggiungiColonna("nickname", "Autore");
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_utente'] ? false : true;
			$col->aliasDi = "utenti.nickname";
			
		$col = $tabella->aggiungiColonna("commento_genitore", "In risposta a");
			$col->aliasDi = "IF(commenti.id_commento_genitore, CONCAT(utenti_commenti_genitori.nickname, ' - ', DATE_FORMAT(commenti_genitori.data_ora_creazione, '%d/%m/%Y %H.%i.%s')), '')";

		$col = $tabella->aggiungiColonna("testo", "Testo");
			$col->aliasDi = "commenti.testo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTesto");
		
		$col = $tabella->aggiungiColonna("data_ora_creazione", "Data creazione");
			$col->aliasDi = "commenti.data_ora_creazione";

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_COMMENTI_MODIFICA, PRIV_COMMENTI_MODIFICA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_COMMENTI_ELIMINA, PRIV_COMMENTI_ELIMINA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	// dato un campo che potrebbe contenere html, lo ritorna bovinamente
	// strippato
	function checkHtmlTitolo(waTabella $tabella)
		{
		return $this->checkHtmlCampo($tabella->record->valore("titolo_articolo"));
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
new tabella_commenti();