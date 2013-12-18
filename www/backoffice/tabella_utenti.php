<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_utenti extends backoffice
	{


	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_UTENTI_VEDI);

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
		$this->aggiungiElemento("Elenco utenti", "titolo");
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
		$sql = "SELECT utenti.*" .
			" FROM utenti" .
			" WHERE NOT utenti.sospeso" .
			" ORDER BY utenti.cognome, utenti.nome";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_UTENTI_INSERIMENTO));
		$tabella->paginaModulo = 'modulo_utenti.php';
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if ($this->haPrivilegio(PRIV_ARTICOLI_VEDI))
			$tabella->aggiungiAzione("Articoli", true);
		if ($this->haPrivilegio(PRIV_COMMENTI_VEDI))
			$tabella->aggiungiAzione("Commenti", true);
		if ($this->haPrivilegio(PRIV_ARGOMENTI_VEDI))
			$tabella->aggiungiAzione("Argomenti", true);
		if ($this->haPrivilegio(PRIV_INTERVENTI_VEDI))
			$tabella->aggiungiAzione("Interventi", true);
		if ($this->haPrivilegio(PRIV_DOCUMENTI_VEDI))
			$tabella->aggiungiAzione("Documenti", true);
		
		$tabella->aggiungiColonna("id_utente", "ID");
		$tabella->aggiungiColonna("nickname", "Nickname");
		$tabella->aggiungiColonna("cognome", "Cognome");
		$col = $tabella->aggiungiColonna("nome", "Nome");
		$col = $tabella->aggiungiColonna("email", "E-mail");
			$col->link = true;
		$col = $tabella->aggiungiColonna("tel", "Telefono");
		$col = $tabella->aggiungiColonna("cell", "Cellulare");

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_UTENTI_MODIFICA, PRIV_UTENTI_MODIFICA_PROPRI, $tabella->record);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_UTENTI_ELIMINA, PRIV_UTENTI_ELIMINA_PROPRI, $tabella->record);
		}
		
	}

// fine classe pagina
//*****************************************************************************
// istanzia la pagina
new tabella_utenti();