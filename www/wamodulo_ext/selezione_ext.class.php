<?php
if (!defined('_WA_SELEZIONE_EXT'))
{
/**
* @ignore
*/
define('_WA_SELEZIONE_EXT',1);

/**
* @ignore
*/
//include_once(dirname(__FILE__) . "/../walibs3.x/controllo.class.php");

//***************************************************************************
//****  classe waSelezione_ext *******************************************************
//***************************************************************************
/**
* waSelezione_ext
*
* classe per la gestione di una select, ossia una lista all'interno
* della quale e' possibile selezionare un solo elemento.
* 
* @version 0.1
* @author G.Gaiba, F.Monti
* @copyright (C) 2007  - WebAppls
*/
class waSelezione_ext extends waControllo
	{
	/**
	* testo da mostrare
	*
	* @var string
	*/
	var $testo			= '';
	
	/**
	* tabella in cui effettuare la ricerca delle corrispondenze
	*
	* @var string
	*/
	var $tabella = '';
	
	/**
	* nome del campo chiave della tabella
	*
	* @var string
	*/
	var $campoChiave = '';
	
	/**
	* nome del campo descrizione della tabella (quello il cui valore verra' mostrato all'utente)
	*
	* @var string
	*/
	var $campoDescrizione = '';
	
	/**
	* query SQL per la ricerca del testo
	*
	* @var string
	*/
	var $sql	= '';
	
	/**
	* input
	* 
	* contiene l'array chiave/testo di cio' che e' stato selezionato in input
	*
	* @var array
	*/
	var $input	= array();
	
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'selezione_ext';

	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* @ignore
	*/
	function mostra()
		{
		$this->definisciValoreIniziale();
		
		// se mi viene passata una query, costruisco la lista sulla
		// base della query
		$this->testo = $this->GetTextFromDBQuery();
			
		$this->xmlOpen();
		$this->xmlAdd("valore", $this->valore);
		$this->xmlAdd("testo", $this->testo);
		$this->xmlClose();
			
		}

	//***************************************************************************
	/**
	 * @param string $sql_or_params può essere una stringa o un oggetto json
	 *		ancora sotto forma di stringa; se è una 
	 *		stringa, allora è la query sql che va eseguita; altrimenti sono i 
	 *		valori inseriti nel modulo dall'utente lato client, sulla base dei
	 *		quali costruire la query sql standard
	 * 
	*/
	public function ricaricaOpzioniSelezioneExt($sql_or_params)
		{
		$toret = array();

		if (!$this->modulo->righeDB)
			return $toret;
		$dbconn = $this->modulo->righeDB->connessioneDB;
		
		$valori_form = @json_decode($sql_or_params, true);
		if (!$valori_form)
			$sql = $sql_or_params;
		else
			$sql = "SELECT $this->campoChiave, $this->campoDescrizione" .
					" FROM $this->tabella" .
					" WHERE $this->campoDescrizione LIKE " . $dbconn->stringaSql("%" . $valori_form[$this->nome . "_testo"] . "%") .
					" AND NOT sospeso" .
					" ORDER BY $this->campoDescrizione";
			
		$rs = new waRigheDB($dbconn);
		$righe = $rs->caricaDaSql($sql, 50);
		if ($rs->nrErrore())
			return $toret;
		foreach ($righe as $riga) 
			$toret[$riga->valore(0)] = $riga->valore(1);

		return $toret;
		}

//	//***************************************************************************
//	/**
//	*/
//	public function ricaricaOpzioniSelezioneExt(waConnessioneDB $dbconn, $parametri_ricerca, $valore, $dipendeDaValore, $applWhereClause = '')
//		{
//		$toret = array();
//		list($tabella, $campoChiave, $campoDescrizione, $dipendeDaCampo) = unserialize(base64_decode($parametri_ricerca));
//
//		if (!$valore || !$tabella || !$campoChiave || !$campoDescrizione)
//			return $toret;
//			
//		$sql = "SELECT $campoChiave, $campoDescrizione" .
//				" FROM $tabella" .
//				" WHERE $campoDescrizione LIKE " . $dbconn->stringaSql("%$valore%") .
//				" AND NOT sospeso" ;
//		if ($dipendeDaCampo && $dipendeDaValore)
//			$sql .= " AND $dipendeDaCampo=" . $dbconn->stringaSql($dipendeDaValore);
//		if ($applWhereClause)
//			$sql .= " AND $applWhereClause";
//		$sql .= " ORDER BY $campoDescrizione";
//		$rs = new waRigheDB($dbconn);
//		$righe = $rs->caricaDaSql($sql, 10);
//		if ($rs->nrErrore())
//			return $toret;
//		foreach ($righe as $riga) 
//			$toret[$riga->valore(0)] = $riga->valore(1);
//
//		return $toret;
//		}
//
	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/
	protected function GetTextFromDBQuery()
		{

		if (!$this->modulo->righeDB || !$this->valore || 
			!$this->tabella || !$this->campoChiave || !$this->campoDescrizione)
			// se l'applicazione non ha messo in bind un recordset alla form, non
			// abbiamo le informazioni per connetterci al db
			return ('');

		$rs = new waRigheDB($this->modulo->righeDB->connessioneDB);
		$sql = "SELECT $this->campoDescrizione FROM $this->tabella WHERE $this->campoChiave=" .
				$this->modulo->righeDB->connessioneDB->stringaSql($this->valore);
		$righe = $rs->caricaDaSql($sql);
		if ($rs->nrErrore() || ! $righe)
			return ('');
		return $righe[0]->valore(0);

		}

	//***************************************************************************
	/**
	 * converte il valore proveniente dal post nelvalore logico del controllo
	 * 
	 * in questo caso fa una piccola maialata: oltre a tornare il valore,
	 * carica nell'array di input del modulo anche il testo selezionato (che puo'
	 * sempre servire)
	* @ignore
	*/	
	function input2valoreInput($valoreIn)
		{
		$this->input = $valoreIn;
		$this->modulo->input[$this->nome . "_testo"] = $valoreIn['testo'];
		return $this->valoreInput = $valoreIn['valore'];
		}
	
	}	// fine classe waSelezione_ext

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_SELEZIONE_EXT'))
?>