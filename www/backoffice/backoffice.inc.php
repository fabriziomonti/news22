<?php
//error_reporting(E_ALL);
if (!defined('_APPLICATION_BACK_CLASS'))
{
/**
* @ignore
*/
define('_APPLICATION_BACK_CLASS',1);


//****************************************************************************************
include_once (dirname(__FILE__) . "/../news22.inc.php");


//****************************************************************************************
class backoffice extends news22
	{
	var $preferenzeUtente;
	
	var $paginaIniziale = 'tabella_articoli.php';
	var $paginaLogin = 'modulo_login.php';
	
	// indica se stiamo facendo vedere qualcosa in una finestra figlia
	var $finestraFiglia = false;

	
	//****************************************************************************************
	/**
	* costruttore
	*
	*/
	function __construct($id_privilegio = true)
		{
		$this->titoloSezione = "Backoffice";
		$this->siglaSezione = "backoffice";
		
		parent::__construct();

        $this->preferenzeUtente = unserialize(base64_decode($_COOKIE[$this->nome . "_$this->siglaSezione" . "_prefs"]));
        $this->modalitaNavigazione = $this->preferenzeUtente["navigazione_interna"] ?  WAAPPLICAZIONE_NAV_INTERNA : WAAPPLICAZIONE_NAV_FINESTRA;
        if ($id_privilegio)
			{
        	$this->verificaUtente();
			if (is_int($id_privilegio))
				$this->checkPrivilegio ($id_privilegio);
			}
		
		}
	
	//****************************************************************
	function verificaUtente ()
		{
			
	    if (empty($this->utente))
			$this->ridireziona($this->paginaLogin);

		}
	
	//***************************************************************************
	/**
	* @return void
	*/
	function dammiMenu()
		{
		if ($this->finestraFiglia)
			return false;
			
		$m = new waMenu();
		$m->apri();

		$m->apriSezione("Blog");
			if ($this->haPrivilegio(PRIV_CATEGORIE_ARTICOLI_VEDI))
				$m->aggiungiVoce("Categorie articoli", "tabella_categorie_articoli.php");
			if ($this->haPrivilegio(PRIV_ARTICOLI_VEDI))
				$m->aggiungiVoce("Articoli", "tabella_articoli.php");
			if ($this->haPrivilegio(PRIV_COMMENTI_VEDI))
				$m->aggiungiVoce("Commenti", "tabella_commenti.php");
		$m->chiudiSezione();
		
		$m->apriSezione("Forum");
			if ($this->haPrivilegio(PRIV_CATEGORIE_ARGOMENTI_VEDI))
				$m->aggiungiVoce("Categorie argomenti", "tabella_categorie_argomenti.php");
			if ($this->haPrivilegio(PRIV_ARGOMENTI_VEDI))
				$m->aggiungiVoce("Argomenti", "tabella_argomenti.php");
			if ($this->haPrivilegio(PRIV_INTERVENTI_VEDI))
				$m->aggiungiVoce("Interventi", "tabella_interventi.php");
		$m->chiudiSezione();

		if ($this->haPrivilegio(PRIV_DOCUMENTI_VEDI))
			{
			$m->apriSezione("Documenti", "tabella_documenti.php");
			$m->chiudiSezione();
			}

		if ($this->haPrivilegio(PRIV_UTENTI_VEDI))
			{
			$m->apriSezione("Utenti", "tabella_utenti.php");
			$m->chiudiSezione();
			}

		$m->apriSezione("Servizi");
			$m->aggiungiVoce("Configurazione", "javascript:document.wapagina.apriPagina(\"modulo_config.php\")");
			$m->aggiungiVoce("Street", "../street/");
			$m->aggiungiVoce("Logout", "logout.php");
		$m->chiudiSezione();
		$m->chiudi();
		return $m;
			
		}
		
	//*****************************************************************************
	// crea il blocco dei bottoni standard di una form
	//*****************************************************************************
	function modulo_bottoniSubmit(waModulo $modulo, 
									$solaLettura = false, 
									$showDeleteButton = true,
									$cmdOkCaption = 'Registra', 
									$cmdCancelCaption = 'Annulla', 
									$cmdDeleteCaption = 'Elimina',
									$cmdCloseCaption = 'Chiudi',
									$buttonWidth = 120)
		{
		$ultimo_controllo = $modulo->controlli[count($modulo->controlli) - 1];
		$top = $ultimo_controllo->alto + $ultimo_controllo->altezza +
				$modulo->altezzaLineaControlli + $modulo->interlineaControlli;
		$nextButtonLeft = $modulo->sinistraControlli;
		if (!$solaLettura)
			{
			$okCtrl = new waBottone($modulo, 'cmd_invia', $cmdOkCaption);
			$okCtrl->alto = $top;
			$okCtrl->sinistra = $nextButtonLeft;
			$okCtrl->larghezza = $buttonWidth;
			$okCtrl->altezza = $buttonHeight;
			$nextButtonLeft = $okCtrl->sinistra + $okCtrl->larghezza + 1;
			}
		if ($solaLettura)
			$cancelCtrl = new waBottone($modulo, 'cmd_annulla', $cmdCloseCaption);
		else 
			$cancelCtrl = new waBottone($modulo, 'cmd_annulla', $cmdCancelCaption);
		$cancelCtrl->alto = $top;
		$cancelCtrl->sinistra = $nextButtonLeft;
		$cancelCtrl->larghezza = $buttonWidth;
		$cancelCtrl->altezza = $buttonHeight;
		$cancelCtrl->annulla = TRUE;
		$cancelCtrl->invia = false;
		$nextButtonLeft = $cancelCtrl->sinistra + $cancelCtrl->larghezza + 1;
	
		if ($showDeleteButton && ! $solaLettura)
			{
			$ctrl = new waBottone($modulo, 'cmd_elimina', $cmdDeleteCaption);
			$ctrl->alto = $top;
			$ctrl->sinistra = $nextButtonLeft;
			$ctrl->larghezza = $buttonWidth;
			$ctrl->altezza = $buttonHeight;
			$ctrl->elimina = TRUE;
			$nextButtonLeft = $ctrl->sinistra + $ctrl->larghezza + 1;
			}
			
		return $nextButtonLeft;
		
		}
	
	//*****************************************************************************
	/**
	 * aggiunge a un controllo di tipo selezione (o selezione_ext) il bottone
	 * "Nuovo" che permette di creare un nuovo elemento in relazione col record
	 * senza chiudere la pagina (modulo) corrente
	 * 
	* @return waBottone
	*/
	function modulo_dammiBottoneNuovoSelezione(waModulo $modulo, 
								$campo, 
								$solaLettura = false)
		{
		// cerchiamo il controllo principale a cui affiancarci
		if (!($ctrl = $modulo->controlliInput[$campo]))
			return false;
			
		$btn = new waBottone($modulo, "btn_nuovo_$campo", "+");
		$btn->invia = false;
		$btn->larghezza = 40;
		$btn->alto = $ctrl->alto;
		$ctrl->larghezza -= $btn->larghezza + 6;
		$btn->sinistra = $ctrl->sinistra + $ctrl->larghezza + 10;
		$btn->solaLettura = $solaLettura;
		
		return $btn;
		}
		
	//*****************************************************************************
	/**
	 * aggiunge a un controllo di tipo selezione (o selezione_ext) il bottone
	 * "Modifica" che permette di modificare un  elemento in relazione col record
	 * senza chiudere la pagina (modulo) corrente
	 * 
	* @return waBottone
	*/
	function modulo_dammiBottoneModificaSelezione(waModulo $modulo, 
														$campo, 
														$solaLettura = false)
		{
		// cerchiamo il controllo principale a cui affiancarci
		if (!($ctrl = $modulo->controlliInput[$campo]))
			return false;
			
		$btn = new waBottone($modulo, "btn_modifica_$campo", "...");
		$btn->invia = false;
		$btn->larghezza = 40;
		$btn->alto = $ctrl->alto;
		$ctrl->larghezza -= $btn->larghezza + 4;
		$btn->sinistra = $ctrl->sinistra + $ctrl->larghezza + 4;
		if ($modulo->righeDB->righe && $modulo->righeDB->righe[0]->valore($campo))
			$btn->solaLettura = $solaLettura;
		else
			$btn->solaLettura = true;

		return $btn;
	
		}
		
	//***************************************************************************
	/**
	* -
	* @return waTabella
	*/
	function dammiTabella($sqlOArray, $id_privilegi_inserimento = false)
		{
		$table = new waTabella($sqlOArray, $this->fileConfigDB);
		
		$tipoXslt = $this->preferenzeUtente["azioni_tabella"] ? $this->preferenzeUtente["azioni_tabella"] : 'sx_default';
		$table->xslt = dirname(__FILE__) . "/../ui/xslt/$this->siglaSezione/watabella_azioni_$tipoXslt.xsl";
		if ($this->finestraFiglia)
			{
			$table->aggiungiAzione("Chiudi");
				
			// portiamo il bottone "chiudi" in prima posizione
			$swappo['Chiudi'] = $table->azioni['Chiudi'];
			foreach ($table->azioni as $k => $v)
				{
				if ($k != "Chiudi")
					$swappo[$k] = $v;
				}
			$table->azioni = $swappo;
			}
		$table->eliminaAzione("Vedi");
		
		// verifica se l'utente è abilitato all'inserimento di record nella tabella
		if (is_array($id_privilegi_inserimento))
			{
			$eliminaNuovo = true;
			foreach ($id_privilegi_inserimento as $id_privilegio_inserimento)
				{
				if ($this->haPrivilegio($id_privilegio_inserimento))
					{
					$eliminaNuovo = false;
					break;
					}
				}
			if ($eliminaNuovo)
				$table->eliminaAzione("Nuovo");
			}
		
		$table->listaMaxRec = $this->preferenzeUtente["max_righe_tabella"] ? 
								$this->preferenzeUtente["max_righe_tabella"] : 
								WATBL_LISTA_MAX_REC;
		
		return $table;
		}
		
	//***************************************************************************
	/**
	* restituisce il titolo di un argomento o di un articolo o di utente da
	 * mostrare nella tabella contenuta in una finestra figlia di questi 
	 * elementi
	*/
	function dammiTitoloGenitore($nome_id)
		{
		if (!$this->finestraFiglia)
			return;
		
		$campo_titolo = "titolo";
		if ($nome_id == "id_articolo") $tabella = "articoli";
		elseif ($nome_id == "id_argomento") $tabella = "argomenti";
		elseif ($nome_id == "id_categoria_articolo") 
			{
			$tabella = "categorie_articoli";
			$campo_titolo = "nome";
			}
		elseif ($nome_id == "id_categoria_argomento") 
			{
			$tabella = "categorie_argomenti";
			$campo_titolo = "nome";
			}
		elseif ($nome_id == "id_utente") 
			{
			$tabella = "utenti";
			$campo_titolo = "nickname";
			}
			
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT $campo_titolo FROM $tabella WHERE $nome_id=" . $dbconn->interoSql($_GET[$nome_id]);
		$riga = $this->dammiRigheDB($sql, $dbconn, 1)->righe[0];
		if (!$riga)
			return;
		
		return "\n" . $riga->valore(0);
		}
		
	//***************************************************************************
	/**
	* -
	* @return waModulo
	*/
	function dammiModulo($paginaDestinazione = null)
		{
		$modulo = new waModulo($paginaDestinazione, $this);
		$modulo->xslt = dirname(__FILE__) . "/../ui/xslt/$this->siglaSezione/wamodulo.xsl";
		$modulo->larghezza = 800;
		$modulo->nomeCampoModId = $this->moduloModId;
		
		return $modulo;
		}
		
	//*****************************************************************************
	// dato un campo di un record di una tabella che potrebbe contenere html, 
	// lo ritorna bovinamente strippato
	function checkHtmlCampo($testo)
		{
		if ($_GET['html'])
			return htmlspecialchars ($testo);
		
		$testo = $this->stripCampo($testo);
		return "<![CDATA[$testo]]>";
		}

//***************************************************************************
	} 	// fine classe backoffice
	
//***************************************************************************

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_APPLICATION_BACK_CLASS'))
