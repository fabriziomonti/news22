<?php
include "backoffice.inc.php";

//*****************************************************************************
class modulo_login extends backoffice
	{
	
	//**************************************************************************
	function __construct()
		{
		parent::__construct(false);
		
		$modulo = $this->creaModuloLogin();

		if ($modulo->daAggiornare())
			{
			$this->eseguiLogin($modulo, $this->paginaIniziale);
			}
		else
			{
			$this->mostraPagina($modulo);
			}
		}

	//*****************************************************************************
	/**
	* mostra
	* 
	* costruisce la pagina contenente il modulo e la manda in output
	* @return void
	*/
	function mostraPagina(waModulo $modulo)
		{
		$this->aggiungiElemento("$this->titolo Login", "titolo");
		$this->aggiungiElemento($modulo);
		$this->mostra();
			
		}
		
//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
$page = new modulo_login();
