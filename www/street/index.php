<?php
include "street.inc.php";

//*****************************************************************************
class index extends street 
	{

	//*************************************************************************
	function __construct()
		{
		parent::__construct();
		
		$dbconn = $this->dammiConnessioneDB();
		$this->aggiungiElemento($this->dammiArticoli($dbconn), "articoli", "XML");
		parent::mostra();
		
		}
	
	//*************************************************************************
	function dammiArticoli(waConnessioneDB $dbconn)
		{
		$sql = "SELECT articoli.*," .
				" categorie_articoli.nome as nome_categoria," .
				" utenti.nickname," .
				" COUNT(commenti.id_commento) AS nr_commenti" . 
				" FROM articoli" .
				" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
				" INNER JOIN utenti ON articoli.id_utente=utenti.id_utente" .
				" LEFT JOIN commenti ON articoli.id_articolo=commenti.id_articolo AND NOT commenti.sospeso" .
				" WHERE NOT articoli.sospeso" .
				" AND articoli.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (articoli.data_ora_fine_pubblicazione>=NOW() OR articoli.data_ora_fine_pubblicazione IS NULL)" .
				($_GET['id_utente'] ? " AND articoli.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
				($_GET['id_categoria_articolo'] ? " AND articoli.id_categoria_articolo=" . $dbconn->interoSql($_GET['id_categoria_articolo']) : '') .
				($_GET['tag'] ? " AND articoli.tags LIKE " . $dbconn->stringaSql("%$_GET[tag]%") : '') .
				($_GET['ricerca_libera'] 
					? 
						" AND (" .
						" articoli.titolo LIKE " . $dbconn->stringaSql("%$_GET[ricerca_libera]%")  .
						" OR articoli.abstract LIKE " . $dbconn->stringaSql("%$_GET[ricerca_libera]%")  .
						" OR articoli.testo LIKE " . $dbconn->stringaSql("%$_GET[ricerca_libera]%")  . 
						")"
					: 
						''
				) .
				" GROUP BY articoli.id_articolo" . 
				" ORDER BY articoli.data_ora_inizio_pubblicazione DESC";
				
		$pagina = intval($_GET['pag_articoli']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);
		
		// se non esiste ancora il file rss lo creiamo
		if (!file_exists("$this->directoryDoc/rss/$this->fileRSSArticoli"))
			$this->creaRSSArticoli($dbconn);

		$buffer = $this->rs2XML($rs, $pagina);
		
		return $buffer;
		}
						
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new index();
	