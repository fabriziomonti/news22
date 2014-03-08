<?php
include "news22.inc.php";

//*****************************************************************************
class downloaddoc extends news22
	{
		
	//***************************************************************************
	function mostraPagina()
		{
		$dbconn = $this->dammiConnessioneDB();
		
		
//		list($tabella, $tipo, $id) = unserialize(base64_decode($_SERVER['QUERY_STRING']));
		$tabella = $_GET['tabella'];
		$tipo = $_GET['tipo'];
		$id = $_GET['id'];
		if ($tabella == "documenti" && $tipo == "nome")
			{
			$this->checkPrivilegio(PRIV_DOCUMENTI_VEDI);
			$sql = "SELECT $tipo FROM documenti" .
					" WHERE id_documento=" . $dbconn->interoSql($id) .
					" AND NOT Sospeso";
			}
			
		if ($tabella == "utenti" && $tipo == "avatar")
			{
			$sql = "SELECT $tipo FROM utenti" .
					" WHERE id_utente=" . $dbconn->interoSql($id) .
					" AND NOT Sospeso";
			}
			
		if (!$sql)
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);
		if (!($riga = $this->dammiRigheDB($sql, $dbconn, 1)->righe[0]))
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);

		$basename = $riga->valore($tipo);
		$file = "$this->directoryDoc/$tabella/$tipo/$id." . pathinfo($basename, PATHINFO_EXTENSION);
		
		header("Pragma: ");
		header("Expires: Fri, 15 Aug 1980 18:15:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0, false");
		header('Content-Length: '. filesize($file));
		if (function_exists("mime_content_type"))
			$mime = mime_content_type($file);
		else
			{
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mime = $finfo->file($file);
			}

		if ($mime)
			{
			header("Content-Type: $mime");
			header("Content-Disposition: inline; filename=\"$basename\""); 
			}
		else 
			{
			header("Content-Disposition: attachment; filename=\"$basename\";" );
			header("Content-Type: application/force-download");
			header("Content-Transfer-Encoding: binary");
			}
			
		readfile($file);
		}
		
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
$page = new downloaddoc();
$page->mostraPagina();