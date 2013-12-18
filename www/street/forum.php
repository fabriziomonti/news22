<?
include "street.inc.php";

//*****************************************************************************
class forum extends street 
	{

	//*************************************************************************
	function __construct()
		{
		parent::__construct();
		
		$dbconn = $this->dammiConnessioneDB();
		$this->aggiungiElemento($this->dammiArgomenti($dbconn), "argomenti", "XML");
		parent::mostra();
		
		}
	
	//*************************************************************************
	function dammiargomenti(waConnessioneDB $dbconn)
		{
		$sql = "SELECT argomenti.*," .
				" categorie_argomenti.nome as nome_categoria," .
				" utenti.nickname," .
				" COUNT(interventi.id_intervento) AS nr_interventi" . 
				" FROM argomenti" .
				" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
				" INNER JOIN utenti ON argomenti.id_utente=utenti.id_utente" .
				" LEFT JOIN interventi ON argomenti.id_argomento=interventi.id_argomento AND NOT interventi.sospeso" .
				" WHERE NOT argomenti.sospeso" .
				" AND argomenti.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (argomenti.data_ora_fine_pubblicazione>=NOW() OR argomenti.data_ora_fine_pubblicazione IS NULL)" .
				($_GET['id_utente'] ? " AND argomenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) : '') .
				($_GET['id_categoria_argomento'] ? " AND argomenti.id_categoria_argomento=" . $dbconn->interoSql($_GET['id_categoria_argomento']) : '') .
				($_GET['tag'] ? " AND argomenti.tags LIKE " . $dbconn->stringaSql("%$_GET[tag]%") : '') .
				($_GET['ricerca_libera'] 
					? 
						" AND (" .
						" argomenti.titolo LIKE " . $dbconn->stringaSql("%$_GET[ricerca_libera]%")  .
						" OR argomenti.abstract LIKE " . $dbconn->stringaSql("%$_GET[ricerca_libera]%")  .
						")"
					: 
						''
				) .
				" GROUP BY argomenti.id_argomento" . 
				" ORDER BY argomenti.data_ora_inizio_pubblicazione DESC";
				
		$pagina = intval($_GET['pag_argomenti']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);

		$buffer = $this->rs2XML($rs, $pagina);
		
		return $buffer;
		}
						
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new forum();
	