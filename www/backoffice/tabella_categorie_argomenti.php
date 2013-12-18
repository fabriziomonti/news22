<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_categorie_argomenti extends backoffice
	{


	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_CATEGORIE_ARGOMENTI_VEDI);
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
		$this->aggiungiElemento("Elenco categorie argomenti", "titolo");
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
		$sql = "SELECT categorie_argomenti.*" .
			" FROM categorie_argomenti" .
			" WHERE NOT categorie_argomenti.sospeso" .
			" ORDER BY categorie_argomenti.nome";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_CATEGORIE_ARGOMENTI_INSERIMENTO));
		$tabella->paginaModulo = "modulo_categorie_argomenti.php";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if ($this->haPrivilegio(PRIV_CATEGORIE_ARGOMENTI_VEDI))
			$tabella->aggiungiAzione("Argomenti", true);
		
		$tabella->aggiungiColonna("id_categoria_argomento", "ID");
		
		$col = $tabella->aggiungiColonna("nome", "Nome");
			
		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_CATEGORIE_ARGOMENTI_MODIFICA);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_CATEGORIE_ARGOMENTI_ELIMINA);
		}
		
	}

// fine classe pagina
//*****************************************************************************
// istanzia la pagina
new tabella_categorie_argomenti();