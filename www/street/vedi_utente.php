<?php
include "street.inc.php";

//*****************************************************************************
class vedi_utente extends street 
	{

	//*************************************************************************
	function __construct()
		{
		parent::__construct();
		
		$dbconn = $this->dammiConnessioneDB();
		$this->aggiungiElemento($this->dammiUtente($dbconn), "utente_visualizzato", "XML");
		$this->aggiungiElemento($this->dammiArticoli($dbconn), "articoli", "XML");
		$this->aggiungiElemento($this->dammiArgomenti($dbconn), "argomenti", "XML");
		$this->aggiungiElemento($this->dammiCommenti($dbconn), "commenti", "XML");
		$this->aggiungiElemento($this->dammiInterventi($dbconn), "interventi", "XML");
		parent::mostra();
		
		}
	
	//*************************************************************************
	function dammiUtente(waConnessioneDB $dbconn)
		{
		$sql = "SELECT utenti.id_utente, utenti.nickname, utenti.avatar," .
						" utenti.data_ora_creazione, utenti.supervisore," .
						" utenti.descrizione" .
				" FROM utenti" .
				" WHERE NOT utenti.sospeso" .
				" AND utenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']);
				
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		if (!$rs->righe)
			$this->mostraMessaggio ("Utente non trovato", "Utente non trovato");

		return $this->rs2XML($rs);
		
		}
						
	//*************************************************************************
	function dammiArticoli(waConnessioneDB $dbconn)
		{
		$sql = "SELECT articoli.id_articolo," .
				" articoli.titolo," .
				" articoli.id_categoria_articolo," .
				" articoli.data_ora_inizio_pubblicazione," .
				" categorie_articoli.nome as nome_categoria" .
				" FROM articoli" .
				" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
				" WHERE NOT articoli.sospeso" .
				" AND articoli.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (articoli.data_ora_fine_pubblicazione>=NOW() OR articoli.data_ora_fine_pubblicazione IS NULL)" .
				" AND articoli.id_utente=" . $dbconn->interoSql($_GET['id_utente']) .
				" ORDER BY articoli.data_ora_inizio_pubblicazione DESC";
				
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
		$sql = "SELECT argomenti.id_argomento," .
				" argomenti.titolo," .
				" argomenti.id_categoria_argomento," .
				" argomenti.data_ora_inizio_pubblicazione," .
				" categorie_argomenti.nome as nome_categoria" .
				" FROM argomenti" .
				" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
				" WHERE NOT argomenti.sospeso" .
				" AND argomenti.data_ora_inizio_pubblicazione<=NOW()" .
				" AND (argomenti.data_ora_fine_pubblicazione>=NOW() OR argomenti.data_ora_fine_pubblicazione IS NULL)" .
				" AND argomenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) .
				" ORDER BY argomenti.data_ora_inizio_pubblicazione DESC";
				
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
		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		
		$sql = "SELECT commenti.id_commento," .
				" commenti.testo," .
				" commenti.data_ora_creazione," .
				" articoli.id_articolo, " .
				" articoli.titolo as titolo_articolo," .
				" articoli.id_categoria_articolo," .
				" articoli.data_ora_inizio_pubblicazione," .
				" categorie_articoli.nome as nome_categoria," .
				" @posizione := (select count(*) from commenti as c" .
					" where not c.sospeso" .
					" and c.id_articolo=commenti.id_articolo" .
					" and c.data_ora_creazione<commenti.data_ora_creazione)" .
					" AS posizione," .
				" FLOOR(@posizione / $max_ap) AS nr_pagina" .
				" FROM commenti" .
				" INNER JOIN articoli ON commenti.id_articolo=articoli.id_articolo" .
				" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
				" WHERE NOT commenti.sospeso" .
				" AND NOT articoli.sospeso" .
				" AND commenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) .
				" ORDER BY commenti.data_ora_creazione DESC";
				
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
		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		
		$sql = "SELECT interventi.id_intervento," .
				" interventi.testo," .
				" interventi.data_ora_creazione," .
				" argomenti.id_argomento, " .
				" argomenti.titolo as titolo_argomento," .
				" argomenti.id_categoria_argomento," .
				" argomenti.data_ora_inizio_pubblicazione," .
				" categorie_argomenti.nome as nome_categoria," .
				" @posizione := (select count(*) from interventi as c" .
					" where not c.sospeso" .
					" and c.id_argomento=interventi.id_argomento" .
					" and c.data_ora_creazione<interventi.data_ora_creazione)" .
					" AS posizione," .
				" FLOOR(@posizione / $max_ap) AS nr_pagina" .
				" FROM interventi" .
				" INNER JOIN argomenti ON interventi.id_argomento=argomenti.id_argomento" .
				" INNER JOIN categorie_argomenti ON argomenti.id_categoria_argomento=categorie_argomenti.id_categoria_argomento" .
				" WHERE NOT interventi.sospeso" .
				" AND NOT argomenti.sospeso" .
				" AND interventi.id_utente=" . $dbconn->interoSql($_GET['id_utente']) .
				" ORDER BY interventi.data_ora_creazione DESC";
				
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
new vedi_utente();
	