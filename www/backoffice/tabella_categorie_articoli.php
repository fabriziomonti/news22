<?php
//******************************************************************************
include "backoffice.inc.php";

//******************************************************************************
/**
 */
//******************************************************************************
class tabella_categorie_articoli extends backoffice
	{


	//*****************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_CATEGORIE_ARTICOLI_VEDI);
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
		$this->aggiungiElemento("Elenco categorie articoli", "titolo");
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
		$sql = "SELECT categorie_articoli.*" .
			" FROM categorie_articoli" .
			" WHERE NOT categorie_articoli.sospeso" .
			" ORDER BY categorie_articoli.nome";
		
		$tabella = parent::dammiTabella($sql, array(PRIV_CATEGORIE_ARTICOLI_INSERIMENTO));
		$tabella->paginaModulo = "modulo_categorie_articoli.php";
		
		// le azioni dipendono dai privilegi
		$tabella->azioni["Modifica"]->funzioneAbilitazione = array($this, "rigaModificabile");
		$tabella->azioni["Elimina"]->funzioneAbilitazione = array($this, "rigaEliminabile");
		
		if ($this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_VEDI))
			$tabella->aggiungiAzione("Articoli", true);
		
		$tabella->aggiungiColonna("id_categoria_articolo", "ID");
		
		$col = $tabella->aggiungiColonna("nome", "Nome");
			
		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe()) $this->mostraErroreDB($tabella->righeDB->connessioneDB);

		return $tabella;
		}

	//*****************************************************************************
	function rigaModificabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_MODIFICA);
		}
		
	//*****************************************************************************
	function rigaEliminabile(waTabella $tabella)
		{
		return $this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_ELIMINA);
		}
		
	}

// fine classe pagina
//*****************************************************************************
// istanzia la pagina
new tabella_categorie_articoli();