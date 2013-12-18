<?php
if (!defined('_WA_AREATESTO_EXT'))
{
/**
* @ignore
*/
define('_WA_AREATESTO_EXT',1);

/**
* @ignore
*/
//include_once(dirname(__FILE__) . "/../walibs3/wamodulo/areatesto.class.php");

//***************************************************************************
//****  classe waAreaTesto_ext *******************************************************
//***************************************************************************
/**
* waAreaTesto_ext
*
* classe per la gestione di un controllo textarea.
*
*/
class waAreaTesto_ext extends waAreaTesto
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'areatesto_ext';
	
	//***************************************************************************
	/**
	* @ignore
	*/
	function mostra()
		{

		// questa cosa è una porcheria che va migliorata, ma non ho tempo!!!
		$this->altezza -= 30;
		$this->sinistra += 4;
		parent::mostra();
		$this->altezza += 30;
		$this->sinistra -= 4;
		}


	}	// fine classe waAreaTesto_ext
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_AREATESTO_EXT'))
?>