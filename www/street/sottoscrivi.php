<?php
include "street.inc.php";

//*****************************************************************************
class sottoscrivi extends street 
	{

	//*************************************************************************
	function __construct()
		{
		parent::__construct();

		if (!$this->utente)
			$this->mostraMessaggio ("Utente non trovato", "Utente non trovato");
		
		$dbconn = $this->dammiConnessioneDB();
		$campo = $_GET['id_argomento'] ? 'id_argomento' : 'id_articolo';
		
		$sql = "select * from sottoscrizioni_via_email" .
				" where id_utente=" . $dbconn->interoSql($this->utente['id_utente']) . 
					" and $campo=" . $dbconn->interoSql($_GET[$campo]);
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		$riga = $rs->righe[0];
		if ($_GET['azione'] == 'sottoscrivi' && !$riga)
			{
			$riga = $rs->aggiungi();
			$riga->id_utente = $this->utente['id_utente'];
			$riga->$campo = $_GET[$campo];
			$riga->data_ora_creazione = time();
			$this->salvaRigheDB($rs);
			}
		elseif ($_GET['azione'] == 'smolla' && $riga)
			{
			$riga->elimina();
			$this->salvaRigheDB($rs);
			}
		$retpage = $_GET["retpage"];
		if (strpos($retpage, "#") == false)
			$retpage .= ($_GET['id_argomento'] ? '#interventi' : '#commenti');
		$this->ridireziona($retpage);
		}
		
	
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new sottoscrivi();
	