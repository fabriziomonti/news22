<?php
include "street.inc.php";

//*****************************************************************************
class login extends street
	{
	
	//**************************************************************************
	function __construct()
		{
		parent::__construct();
		
		$modulo = $this->creaModuloLogin();

		// alcuni elementi vengono mostrati sempre, anche se si esce con una
		// mostraMessaggio
		$this->aggiungiElemento($_POST["email"], "email");

		if ($modulo->daAggiornare())
			{
			// è da mandare da un'altr parte!!!!
			$this->eseguiLogin($modulo, "index.php");
			}
		else
			{
			$this->mostra();
			}
		}
		
//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new login();
