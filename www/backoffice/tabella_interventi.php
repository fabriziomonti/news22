<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_interventi extends backoffice
	{

	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_INTERVENTI);
		$this->finestraFiglia = ($_GET['id_argomento'] || $_GET['id_utente']);
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
		$this->aggiungiElemento("Elenco interventi" . 
					$this->dammiTitoloGenitore($_GET['id_utente'] ? 'id_utente' : 'id_argomento'), 
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
		$sql = "SELECT interventi.*," .
			" argomenti.titolo AS titolo_argomento," .
			" utenti.nickname," . 
			" IF(interventi.id_intervento_genitore, CONCAT(utenti_interventi_genitori.nickname, ' - ', DATE_FORMAT(interventi_genitori.data_ora_creazione, '%d/%m/%Y %H.%i.%s')), '') AS intervento_genitore" .
			" FROM interventi" .
			" INNER JOIN argomenti ON interventi.id_argomento=argomenti.id_argomento" .
			" INNER JOIN utenti ON interventi.id_utente=utenti.id_utente" .
			" LEFT JOIN interventi AS interventi_genitori ON interventi.id_intervento_genitore=interventi_genitori.id_intervento" . 
			" LEFT JOIN utenti AS utenti_interventi_genitori ON interventi_genitori.id_utente=utenti_interventi_genitori.id_utente" .
			" WHERE NOT interventi.sospeso" .
			($_GET['id_argomento'] ? " AND interventi.id_argomento=" . $dbconn->interoSql($_GET['id_argomento']) : '') .
			($_GET['id_utente'] ? " AND interventi.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
			" ORDER BY interventi.id_intervento DESC";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_INTERVENTI_INSERIMENTO, PRIV_INTERVENTI_INSERIMENTO_PROPRI));
		$tabella->paginaModulo = "modulo_interventi.php?id_argomento=$_GET[id_argomento]&id_utente=$_GET[id_utente]";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if (!$_GET['html'])
			$tabella->aggiungiAzione("HTML");
		else
			$tabella->aggiungiAzione("NO_HTML", false, "NO HTML");
		
		$col = $tabella->aggiungiColonna("id_intervento", "ID");
			$col->aliasDi = "interventi.id_intervento";
			
		$col = $tabella->aggiungiColonna("titolo_argomento", "Argomento");
			$col->aliasDi = "argomenti.titolo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTitolo");
			
		$col = $tabella->aggiungiColonna("nickname", "Autore");
			$col->mostra = $col->filtra = $col->ordina = $_GET['id_utente'] ? false : true;
			$col->aliasDi = "utenti.nickname";
			
			
		$col = $tabella->aggiungiColonna("intervento_genitore", "In risposta a");
			$col->aliasDi = "IF(interventi.id_intervento_genitore, CONCAT(utenti_interventi_genitori.nickname, ' - ', DATE_FORMAT(interventi_genitori.data_ora_creazione, '%d/%m/%Y %H.%i.%s')), '')";

		$col = $tabella->aggiungiColonna("testo", "Testo");
			$col->aliasDi = "interventi.testo";
			$col->convertiHTML = !$_GET['html'];
			$col->funzioneCalcolo = array($this, "checkHtmlTesto");
		
		$col = $tabella->aggiungiColonna("data_ora_creazione", "Data creazione");
			$col->aliasDi = "interventi.data_ora_creazione";

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_INTERVENTI_MODIFICA, PRIV_INTERVENTI_MODIFICA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_INTERVENTI_ELIMINA, PRIV_INTERVENTI_ELIMINA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	// dato un campo che potrebbe contenere html, lo ritorna bovinamente
	// strippato
	function checkHtmlTitolo(waTabella $tabella)
		{
		return $this->checkHtmlCampo($tabella->record->valore("titolo_argomento"));
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
new tabella_interventi();