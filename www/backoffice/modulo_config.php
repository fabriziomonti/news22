<?php
include "backoffice.inc.php";

//*****************************************************************************
class modulo_config extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct();
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->eliminaRecord();
			}
		else
			{
			$this->mostraPagina();
			}
		}

	//*****************************************************************************
	/**
	* mostra
	* 
	* costruisce la pagina contenente il modulo e la manda in output
	* @return void
	*/
	function mostraPagina()
		{
		$this->aggiungiElemento("Configurazione interfaccia", "titolo");
		$this->aggiungiElemento($this->modulo);
		$this->mostra();
			
		}
		
	//***************************************************************************
	function creaModulo()
		{
		
		$this->modulo = $this->dammiModulo();
		
		$ctrl = $this->modulo->aggiungiLogico("navigazione_finestre", "Navigazione a finestre", false);
			$ctrl->valore = $this->preferenzeUtente["navigazione_finestre"];
		$ctrl = $this->modulo->aggiungiSelezione("max_righe_tabella", "Nr. righe tabella per pagina", false, true, false);
		$ctrl->rigaVuota = false;
		for ($i = 10; $i <= 100; $i += 10)
			$ctrl->lista[$i] = $i;
		$ctrl->valore = $this->preferenzeUtente["max_righe_tabella"] ? $this->preferenzeUtente["max_righe_tabella"] : WATBL_LISTA_MAX_REC;

		$ctrl = $this->modulo->aggiungiSelezione("azioni_tabella", "Azioni su righe tabella", false, true, false);
		$ctrl->rigaVuota = false;
		$ctrl->lista = array("sx_default" => "bottoni a sinistra", "context" => "menu contestuale", "dx" => "bottoni a destra");
		$ctrl->valore = $this->preferenzeUtente["azioni_tabella"];
		
		$this->modulo_bottoniSubmit($this->modulo, false, false);

		$this->modulo->leggiValoriIngresso();

		}

	//***************************************************************************
	function aggiornaRecord()
		{
		// blob della configurazione da mandare via cookie al browser
		unset($_POST["wamodulo_nome_modulo"]);
		unset($_POST["cmd_invia"]);
		$cookieVal = base64_encode(serialize($_POST));
		setcookie($this->nome . "_$this->siglaSezione" . "_prefs", $cookieVal, mktime(0,0,0, date('n'), date('j'), date('Y') + 10), $this->httpwd, $this->dominio);
			
		$this->ritorna();
		}
		
	
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_config();
