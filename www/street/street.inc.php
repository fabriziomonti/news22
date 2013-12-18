<?php
//error_reporting(E_ALL);
if (!defined('_APPLICATION_STREET_CLASS'))
{
/**
* @ignore
*/
define('_APPLICATION_STREET_CLASS',1);


//****************************************************************************************
include_once (dirname(__FILE__) . "/../news22.inc.php");


//****************************************************************************************
class street extends news22
	{
	
	/**
	 * eventuale url da cui prelevare la ui
	 * 
	 * @var string
	 */
	var $ui_url = '';
	
	//****************************************************************************************
	/**
	* costruttore
	*
	*/
	function __construct()
		{
		$this->titoloSezione = "street";
		$this->siglaSezione = "street";
		
		parent::__construct();
		
		// inizializzazione eventuale URL della ui
		if ($_GET['ui_url'])
			{
			setcookie("ui_url", $_GET['ui_url'], mktime(0,0,0, date('n'), date('j'), date('Y') + 10), "$this->httpwd/$this->siglaSezione", $this->dominio);
			$this->ui_url = base64_decode($_GET['ui_url']);
			}
		elseif($_COOKIE['ui_url'])
			{
			$this->ui_url = base64_decode($_COOKIE['ui_url']);
			}
		}

	//***************************************************************************
	/**
	* manda in output la pagina
	* 
	* aggiungiamo i dati dell'utente, se loggato
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		if ($this->ui_url)
			{
			// in caso di ui remota gestiamo l'eventuale errore di XSLT 
			// resettando il parametro e tornando alla ui di default
			$ui_dir = "$this->ui_url/ui" ;
			set_error_handler(array($this, "gestioneErroreXSLRemoto"), E_USER_ERROR);
			}
		else
			{
			$ui_dir = "../ui";
			}
			
		$this->aggiungiElemento($ui_dir, "ui_dir");
		$this->aggiungiElemento($this->dammiCategorieArticoli(), "categorie_articoli", "XML");
		$this->aggiungiElemento($this->dammiCategorieArgomenti(), "categorie_argomenti", "XML");
		if ($this->utente)
			{
			// aggiungiamo ai dati dell'utente i privilegi HTML che possiede
			$this->utente["privilegio_html_base"] = intval($this->haPrivilegio(PRIV_HTML_BASE));
			$this->utente["privilegio_html_esteso"] = intval($this->haPrivilegio(PRIV_HTML_ESTESO));
			$this->aggiungiElemento ($this->array2xml($this->utente), "dati_utente", "XML");
			}
			
			
		$this->xslt = "$ui_dir/xslt/$this->siglaSezione/" . pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) . ".xsl";
		parent::mostra($bufferizza);
		}

	//***************************************************************************
	/**
	* si è verificato un errore durante la trasformazione XSLT tramite file
	 * remoto: resettiamo al default
	*/
	function gestioneErroreXSLRemoto($errno , $errstr, $errfile = null, $errline = null, $errcontext = null)
		{
		$messaggio_errore = ob_get_contents();
		ob_clean();
		setcookie("ui_url", '', mktime(0,0,0, date('n'), date('j'), date('Y') + 10), "$this->httpwd/$this->siglaSezione", $this->dominio);
		$this->ui_url = '';
		$this->mostraMessaggio("Errore caricamento UI remota", 
					"Si è verificato un errore durante la trasformazione mediante il foglio di stile remoto." .
					" La configurazione della UI è stata ripristinata al suo default iniziale:" .
					" di seguito viene riportato il messaggio di errore:" .
					"<pre>$messaggio_errore</pre>");
		
		}
		
	//*************************************************************************
	function dammiCategorieArticoli()
		{
		$sql = "SELECT categorie_articoli.*," .
				" COUNT(articoli.id_categoria_articolo) AS nr_articoli" . 
				" FROM categorie_articoli" .
				" LEFT JOIN articoli ON categorie_articoli.id_categoria_articolo=articoli.id_categoria_articolo" .
					" AND NOT articoli.sospeso" .
					" AND articoli.data_ora_inizio_pubblicazione<=NOW()" .
					" AND (articoli.data_ora_fine_pubblicazione>=NOW() OR articoli.data_ora_fine_pubblicazione IS NULL)" .
				" WHERE NOT categorie_articoli.sospeso" .
				" GROUP BY categorie_articoli.id_categoria_articolo" . 
//				" HAVING nr_articoli>0" .
				" ORDER BY categorie_articoli.nome";

		return $this->rs2XML($this->dammiRigheDB($sql));
		}
						
	//*************************************************************************
	function dammiCategorieArgomenti()
		{
		$sql = "SELECT categorie_argomenti.*," .
				" COUNT(argomenti.id_categoria_argomento) AS nr_argomenti" . 
				" FROM categorie_argomenti" .
				" LEFT JOIN argomenti ON categorie_argomenti.id_categoria_argomento=argomenti.id_categoria_argomento" .
					" AND NOT argomenti.sospeso" .
					" AND argomenti.data_ora_inizio_pubblicazione<=NOW()" .
					" AND (argomenti.data_ora_fine_pubblicazione>=NOW() OR argomenti.data_ora_fine_pubblicazione IS NULL)" .
				" WHERE NOT categorie_argomenti.sospeso" .
				" GROUP BY categorie_argomenti.id_categoria_argomento" . 
//				" HAVING nr_argomenti>0" .
				" ORDER BY categorie_argomenti.nome";

		return $this->rs2XML($this->dammiRigheDB($sql));
		}
						
	//***************************************************************************
	function dammiModulo()
		{
		return new waModulo(null, $this);
		}
	
	//***************************************************************************
	/**
	* mostra un messaggio e termina l'esecuzione dello script corrente
	 * 
	 * tutto uguale a papa', che e' ok
	 * 
	* @param string $titolo intestazione del messaggio
	* @param string $messaggio testo del messaggio da mostrare
	* @param boolean $torna comanda alla UI di mostrare un bottone o analogo per tornare alla pagina precedente
	* @param boolean $chiudi comanda alla UI di mostrare un bottone o analogo per chiudere la finestra corrente
	* @return void
	*/
	function mostraMessaggioOk($titolo, $messaggio, $torna = true, $chiudi = false)
		{
		$this->aggiungiElemento($titolo, "titolo");
		$this->aggiungiElemento($messaggio, "messaggio_ok");
		if ($torna)
			$this->aggiungiElemento('', "azione_torna");
		if ($chiudi)
			$this->aggiungiElemento('', "azione_chiudi");
		$this->mostra();
	    exit();
		}
		
//***************************************************************************
	} 	// fine classe street
	
//***************************************************************************

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_APPLICATION_STREET_CLASS'))
