<?
include "street.inc.php";

//*****************************************************************************
class ricerca extends street 
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		

	//*************************************************************************
	function __construct()
		{
		parent::__construct();

		$dbconn = $this->dammiConnessioneDB();
		$this->aggiungiElemento($this->dammiArticoli($dbconn), "articoli", "XML");
		$this->aggiungiElemento($this->dammiArgomenti($dbconn), "argomenti", "XML");
		$this->aggiungiElemento($this->dammiCommenti($dbconn), "commenti", "XML");
		$this->aggiungiElemento($this->dammiInterventi($dbconn), "interventi", "XML");
		$this->aggiungiElemento($this->array2xml($_GET), "request", "XML");
		parent::mostra();
		}
		
	//*************************************************************************
	function dammiArticoli(waConnessioneDB $dbconn)
		{
		if (!$_GET['id_categoria_articolo'])
			return;
		
		$sql = "SELECT articoli.id_articolo," .
				" articoli.titolo," .
				" articoli.id_categoria_articolo," .
				" articoli.data_ora_inizio_pubblicazione," .
				" categorie_articoli.nome as nome_categoria," .
				" utenti.nickname" .
				" FROM articoli" .
				" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
				" INNER JOIN utenti ON articoli.id_utente=utenti.id_utente" .
				" WHERE NOT articoli.sospeso" .
				" AND articoli.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (articoli.data_ora_fine_pubblicazione>=NOW() OR articoli.data_ora_fine_pubblicazione IS NULL)" .
				($_GET['nickname'] ? " AND utenti.nickname=" . $dbconn->stringaSql($_GET['nickname']) : '') .
				($_GET['tag'] ? " AND articoli.tags LIKE " . $dbconn->stringaSql("%$_GET[tag]%") : '') .
				($_GET['espressione'] 
					? 
						" AND (" .
						" articoli.titolo LIKE " . $dbconn->stringaSql("%$_GET[espressione]%")  .
						" OR articoli.abstract LIKE " . $dbconn->stringaSql("%$_GET[espressione]%")  .
						" OR articoli.testo LIKE " . $dbconn->stringaSql("%$_GET[espressione]%")  . 
						")"
					: 
						''
				);
		if ($_GET['dalla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['dalla_data']);
			$sql .= " AND DATE(articoli.data_ora_inizio_pubblicazione)>=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['alla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['alla_data']);
			$sql .= " AND DATE(articoli.data_ora_inizio_pubblicazione)<=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['id_categoria_articolo'])
			{
			$virgola = '';
			$sql .= " AND articoli.id_categoria_articolo IN (";
			foreach ($_GET['id_categoria_articolo'] as $id_categoria_articolo => $on)
				{
				$sql .= "$virgola$id_categoria_articolo";
				$virgola = ",";
				}
			$sql .= ")";
			}
				
		$pagina = intval($_GET['pag_articoli']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);
		// strip dei testi (so che non è bello fatto così, prima o poi lo cambierò)
		foreach ($rs->righe as $riga)
			{
			$riga->inserisciValore("titolo", $this->stripCampo($riga->valore("titolo")));
			}
		
		$buffer = $this->rs2XML($rs, $pagina);
		return $buffer;
		}
						
	//*************************************************************************
	function dammiArgomenti(waConnessioneDB $dbconn)
		{
		if (!$_GET['id_categoria_argomento'])
			return;
		
		$sql = "SELECT argomenti.id_argomento," .
				" argomenti.titolo," .
				" argomenti.id_categoria_argomento," .
				" argomenti.data_ora_inizio_pubblicazione," .
				" categorie_argomenti.nome as nome_categoria," .
				" utenti.nickname" .
				" FROM argomenti" .
				" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
				" INNER JOIN utenti ON argomenti.id_utente=utenti.id_utente" .
				" WHERE NOT argomenti.sospeso" .
				" AND argomenti.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (argomenti.data_ora_fine_pubblicazione>=NOW() OR argomenti.data_ora_fine_pubblicazione IS NULL)" .
				($_GET['nickname'] ? " AND utenti.nickname=" . $dbconn->stringaSql($_GET['nickname']) : '') .
				($_GET['tag'] ? " AND argomenti.tags LIKE " . $dbconn->stringaSql("%$_GET[tag]%") : '') .
				($_GET['espressione'] 
					? 
						" AND (" .
						" argomenti.titolo LIKE " . $dbconn->stringaSql("%$_GET[espressione]%")  .
						" OR argomenti.abstract LIKE " . $dbconn->stringaSql("%$_GET[espressione]%")  .
						")"
					: 
						''
				);
		if ($_GET['dalla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['dalla_data']);
			$sql .= " AND DATE(argomenti.data_ora_inizio_pubblicazione)>=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['alla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['alla_data']);
			$sql .= " AND DATE(argomenti.data_ora_inizio_pubblicazione)<=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['id_categoria_argomento'])
			{
			$virgola = '';
			$sql .= " AND argomenti.id_categoria_argomento IN (";
			foreach ($_GET['id_categoria_argomento'] as $id_categoria_argomento => $on)
				{
				$sql .= "$virgola$id_categoria_argomento";
				$virgola = ",";
				}
			$sql .= ")";
			}
				
		$pagina = intval($_GET['pag_argomenti']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);
		// strip dei testi (so che non è bello fatto così, prima o poi lo cambierò)
		foreach ($rs->righe as $riga)
			{
			$riga->inserisciValore("titolo", $this->stripCampo($riga->valore("titolo")));
			}
		
		$buffer = $this->rs2XML($rs, $pagina);
		return $buffer;
		}
						
	//*************************************************************************
	function dammiCommenti(waConnessioneDB $dbconn)
		{
		if (!$_GET["flag_commenti"])
			return;
		
		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		$sql = "SELECT commenti.id_commento," .
				" commenti.id_utente," .
				" commenti.testo," .
				" commenti.data_ora_creazione," .
				" articoli.id_articolo, " .
				" articoli.titolo as titolo_articolo," .
				" articoli.id_categoria_articolo," .
				" articoli.data_ora_inizio_pubblicazione," .
				" categorie_articoli.nome as nome_categoria," .
				" utenti.nickname," .
				" FLOOR(" .
					" (select count(*) from commenti as c" .
					" where not c.sospeso" .
					" and c.id_articolo=commenti.id_articolo" .
					" and c.chiave_ordinamento<commenti.chiave_ordinamento)" .
					" / $max_ap) AS nr_pagina" .
				" FROM commenti" .
				" INNER JOIN utenti ON commenti.id_utente=utenti.id_utente" .
				" INNER JOIN articoli ON commenti.id_articolo=articoli.id_articolo" .
				" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
				" WHERE NOT commenti.sospeso" .
				" AND NOT articoli.sospeso" .
				($_GET['nickname'] ? " AND utenti.nickname=" . $dbconn->stringaSql($_GET['nickname']) : '') .
				($_GET['tag'] ? " AND articoli.tags LIKE " . $dbconn->stringaSql("%$_GET[tag]%") : '') .
				($_GET['espressione'] ? " AND commenti.testo LIKE " . $dbconn->stringaSql("%$_GET[espressione]%") : '');
		if ($_GET['dalla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['dalla_data']);
			$sql .= " AND DATE(commenti.data_ora_creazione)>=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['alla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['alla_data']);
			$sql .= " AND DATE(commenti.data_ora_creazione)<=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['id_categoria_articolo'])
			{
			$virgola = '';
			$sql .= " AND articoli.id_categoria_articolo IN (";
			foreach ($_GET['id_categoria_articolo'] as $id_categoria_articolo => $on)
				{
				$sql .= "$virgola$id_categoria_articolo";
				$virgola = ",";
				}
			$sql .= ")";
			}
			$sql .= " ORDER BY commenti.data_ora_creazione DESC";
//exit($sql);				
		$pagina = intval($_GET['pag_commenti']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);
		// strip dei testi (so che non è bello fatto così, prima o poi lo cambierò)
		foreach ($rs->righe as $riga)
			{
			$riga->inserisciValore("titolo_articolo", $this->stripCampo($riga->valore("titolo_articolo")));
			$riga->inserisciValore("testo", $this->stripCampo($riga->valore("testo")));
			}

		$buffer = $this->rs2XML($rs, $pagina);
		return $buffer;
		}
						
	//*************************************************************************
	function dammiInterventi(waConnessioneDB $dbconn)
		{
		if (!$_GET["flag_interventi"])
			return;
		
		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		$sql = "SELECT interventi.id_intervento," .
				" interventi.id_utente," .
				" interventi.testo," .
				" interventi.data_ora_creazione," .
				" argomenti.id_argomento, " .
				" argomenti.titolo as titolo_argomento," .
				" argomenti.id_categoria_argomento," .
				" argomenti.data_ora_inizio_pubblicazione," .
				" categorie_argomenti.nome as nome_categoria," .
				" utenti.nickname," .
				" FLOOR(" .
					" (select count(*) from interventi as c" .
					" where not c.sospeso" .
					" and c.id_argomento=interventi.id_argomento" .
					" and c.chiave_ordinamento<interventi.chiave_ordinamento)" .
					" / $max_ap) AS nr_pagina" .
				" FROM interventi" .
				" INNER JOIN utenti ON interventi.id_utente=utenti.id_utente" .
				" INNER JOIN argomenti ON interventi.id_argomento=argomenti.id_argomento" .
				" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
				" WHERE NOT interventi.sospeso" .
				" AND NOT argomenti.sospeso" .
				($_GET['nickname'] ? " AND utenti.nickname=" . $dbconn->stringaSql($_GET['nickname']) : '') .
				($_GET['tag'] ? " AND argomenti.tags LIKE " . $dbconn->stringaSql("%$_GET[tag]%") : '') .
				($_GET['espressione'] ? " AND interventi.testo LIKE " . $dbconn->stringaSql("%$_GET[espressione]%") : '');
		if ($_GET['dalla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['dalla_data']);
			$sql .= " AND DATE(interventi.data_ora_creazione)>=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['alla_data'])
			{
			list($d, $m, $y) = explode("/", $_GET['alla_data']);
			$sql .= " AND DATE(interventi.data_ora_creazione)<=" . $dbconn->dataSql(mktime(0,0,0, $m, $d, $y));
			}
		if ($_GET['id_categoria_argomento'])
			{
			$virgola = '';
			$sql .= " AND argomenti.id_categoria_argomento IN (";
			foreach ($_GET['id_categoria_argomento'] as $id_categoria_argomento => $on)
				{
				$sql .= "$virgola$id_categoria_argomento";
				$virgola = ",";
				}
			$sql .= ")";
			}
			$sql .= " ORDER BY interventi.data_ora_creazione DESC";
				
		$pagina = intval($_GET['pag_interventi']);
		$rs = $this->dammiRigheDB($sql, $dbconn, APPL_MAX_ARTICOLI_PAGINA, $pagina * APPL_MAX_ARTICOLI_PAGINA);
		// strip dei testi (so che non è bello fatto così, prima o poi lo cambierò)
		foreach ($rs->righe as $riga)
			{
			$riga->inserisciValore("titolo_argomento", $this->stripCampo($riga->valore("titolo_argomento")));
			$riga->inserisciValore("testo", $this->stripCampo($riga->valore("testo")));
			}

		$buffer = $this->rs2XML($rs, $pagina);
		return $buffer;
		}
						
	//*****************************************************************************
	}
	
//*****************************************************************************
// istanzia la pagina
new ricerca();
	