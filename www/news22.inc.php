<?php
//error_reporting(E_ALL);
if (!defined('_APPLICATION_CLASS'))
{
/**
* @ignore
*/
define('_APPLICATION_CLASS',1);


//****************************************************************************************
include_once (dirname(__FILE__) . "/defines.inc.php");
include_once (dirname(__FILE__) . "/config.inc.php");
include_once (dirname(__FILE__) . "/walibs3/waapplicazione/waapplicazione.inc.php");


//****************************************************************************************
class news22 extends waApplicazione
	{
	var $directoryDoc;
	var $datiSessione;
	var $fileConfigDB = '';
	
	// passphrase encryption password
	var $pwdPwd = '';
	
	// dati di editing da appiccicare a tutte le modifiche sulle tabelle del db
	var $moduloModId = "data_ora_ultima_modifica";
	var $moduloModIp = "ip_ultima_modifica";
	
	/**
	* contiene i dati dell'utente loggato alla sessione
	*/
	var $utente = array();
	
	// set dei privilegi che vengono attribuiti di default a ogni utente
	// al momento della registrazione
	var $privilegi_iniziali = array();
	
	/**
	 * file rss
	 */
	var $fileRSSArticoli = "articoli.xml";
	var $fileRSSCommenti = "commenti";
	var $fileRSSInterventi = "interventi";

	// istanza dell'applicazione (serve per essere trovata anche da "fuori" dell'applicazione
	public static $istanzaApplicazione;
	
	//****************************************************************************************
	/**
	* costruttore
	*
	*/
	function __construct()
		{
		$this->usaSessione = true;
		$this->dominio = APPL_DOMAIN;
//		$this->httpwd = APPL_DIRECTORY;
		$this->directoryTmp = APPL_TMP_DIRECTORY;
		$this->nome = APPL_NAME;
		$this->titolo = APPL_TITLE;
		$this->versione = APPL_REL;
		$this->dataVersione = APPL_REL_DATE;
		$this->serverSmtp = APPL_SMTP_SERVER;
		$this->utenteSmtp = APPL_SMTP_USER;
		$this->passwordSmtp = APPL_SMTP_PWD;
		$this->sicurezzaSmtp = APPL_SMTP_SECURE;
		$this->portaSmtp = APPL_SMTP_PORT;
		$this->emailSupporto = APPL_SUPPORT_ADDR;
		$this->emailInfo = APPL_INFO_ADDR;
		$this->telSupporto = APPL_SUPPORT_TEL;
		
		$this->directoryDoc = APPL_DOC_DIRECTORY;
		$this->pwdPwd = APPL_PWD_PWD;
		$this->privilegi_iniziali = unserialize(APPL_PRIVILEGI_INIZIALI);
		
		$this->fileConfigDB = dirname(__FILE__) . "/dbconfig.inc.php";
		$subdir = $this->siglaSezione ? "/$this->siglaSezione" : '';
		$nome_xslt = $this->siglaSezione ? $this->siglaSezione : $this->nome;
		$this->xslt = dirname(__FILE__) . "/ui/xslt$subdir/$nome_xslt.xsl";
		
		self::$istanzaApplicazione = $this;
		
		$this->inizializza();
        
		$this->datiSessione = &$_SESSION[$this->nome];
		$this->utente = &$this->datiSessione['utente'];
		
		}
	
	//*****************************************************************************
	/**
	* a seconda dello stato della form, chiama l'opportuno metodo
	*
	* @return waConnessiondDB
	*/
	function dammiConnessioneDB($fileConfigurazioneDB = '')
	    {
		if (empty($fileConfigurazioneDB))
			$fileConfigurazioneDB = $this->fileConfigDB;
	    return parent::dammiConnessioneDB($fileConfigurazioneDB);
		}
	    
	//*****************************************************************************
	function record2Array($record)
		{
		$retval = array();
		$rs = $record->righeDB;
		for ($i = 0; $i < $rs->nrCampi(); $i++)
			{
			if ($rs->tipoCampo($i) <> WADB_CONTENITORE)
				$retval[strtolower($rs->nomeCampo($i))] = $record->valore($i);
			}
		
		return $retval;
		}
		
	//***************************************************************************
	function decryptPassword($pwd)
		{
		// in mysql: aes_decrypt(unhex(campo, chiave)
		$dec = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->pwdPwd, pack("H*" , $pwd), MCRYPT_MODE_ECB);
		$toret = rtrim($dec, ((ord(substr($dec, strlen($dec) - 1, 1)) >= 0 and ord(substr($dec, strlen($dec) - 1, 1 ) ) <= 16 ) ? chr(ord(substr($dec, strlen($dec ) - 1, 1))): null) );			
		
		return $toret;
		}

	//***************************************************************************
	function encryptPassword($pwd)
		{
		// in mysql: hex(aes_encrypt(campo, chiave)
		// MySQL Padding
		$pad_len = 16 - (strlen($pwd) % 16);
		$pwd = str_pad($pwd, (16 * (floor(strlen($pwd) / 16) + 1)), chr($pad_len));
		 
		mt_srand();
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
		mcrypt_generic_init($td, $this->pwdPwd, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));
		$encrypted = mcrypt_generic($td, $pwd);
		mcrypt_generic_deinit($td);
		
		$toret = '';
		for ($i = 0; $i < strlen($encrypted); $i += 1)
			$toret .= str_pad(dechex(ord(substr($encrypted, $i, 1))), 2, '0', STR_PAD_LEFT);
			
		return strtoupper($toret);
		}
		
	//*****************************************************************************
	/**
	 * forse da spostare in backoffice?
	* @return waSelezione_ext
	*/
	function modulo_dammiSelezioneExt(waModulo $modulo, 
								$campo, 
								$etichetta, 
								$solaLettura = false,
								$obbligatorio = false,
								$justify = true,
								$tbl = '',
								$keyFld = '',
								$descrFld = '',
								$flagBottoneModifica = false,
								$flagBottoneNuovo = false
			)
		{
		$ctrl = $modulo->aggiungiGenerico('waSelezione_ext', 
								$campo, $etichetta, $solaLettura, $obbligatorio);
		
		$ctrl->tabella = $tbl;
		$ctrl->campoChiave = $keyFld == '' ? $campo : $keyFld;
		$ctrl->campoDescrizione = $descrFld;
				
		if ($justify)
			$ctrl->larghezza = ($modulo->larghezza - $ctrl->sinistra) - $modulo->sinistraEtichette * 2;
			
		if ($flagBottoneModifica)
			$this->modulo_dammiBottoneModificaSelezione($modulo, $campo, $solaLettura);
		if ($flagBottoneNuovo)
			$this->modulo_dammiBottoneNuovoSelezione($modulo, $campo, $solaLettura);
		
		return $ctrl;
		}
			
	//**************************************************************************
	/**
	 * forse da spostare in backoffice?
	 * crea un controllo di tipo AreaTesto_ext, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waTesto
	 */
	function aggiungiAreaTesto_ext(waModulo $modulo, 
									$nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		// solo l'utente con privilegi html estesi può usare il controllo
		if (!$this->haPrivilegio(PRIV_HTML_ESTESO))
			return $modulo->aggiungiAreaTesto ($nome, $etichetta, 
									$solaLettura, 
									$obbligatorio, 
									$altoEtichetta, 
									$altoControllo, 
									$sinistraEtichetta, 
									$sinistraControllo);
		
		$ctrl = $modulo->aggiungiGenerico('waAreaTesto_ext', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		$ctrl->altezza = 180;
		$modulo->giustificaControllo($ctrl, false);
		return $ctrl;
		}

	//***************************************************************************
	// ritorna una chiave personale da scrivere sul record
	//***************************************************************************
	function getPassword()
		{
		$mt = microtime();
		$elems = explode(" ", microtime());
		$pwd = chr((substr($elems[1], -1) + ord('A'))) . substr($elems[0], 2, 4) . substr($elems[1], -3);
		$pwd = substr($pwd, 0, -1) . chr(substr($pwd, -1) + ord('l'));
		return $pwd;
		}
		
	//*************************************************************************
	function verificaPassword($pwd, $pwdConferma, $oldpwd1 = '', $oldpwd2 = '', $oldpwd3 = '')
	    {
	    if (strlen($pwd) < 8) return false;
	    if (strlen($pwd) > 12) return false;
	       
	    if (!ereg("^[A-Za-z0-9]", $pwd)) return false;
	    if (!ereg("[A-Z]", $pwd)) return false;
	    if (!ereg("[a-z]", $pwd)) return false;
	    if (!ereg("[0-9]", $pwd)) return false;
	    if ($pwd != $pwdConferma) return false;
	       
	    // le vecchie password, se ci sono, sono encryptate
	    $pwd = $this->encryptPassword($pwd);
	    if ($pwd == $oldpwd1 || $pwd == $oldpwd2 || $pwd == $oldpwd3) return false;
	    
	    return true;
	    }
           
	//*****************************************************************************
	function setEditorData(waRecord $riga)
		{
		$riga->inserisciValore("id_operatore_modificatore", $this->utente['id_operatore']);
		$riga->inserisciValore($this->moduloModId, time());
		$riga->inserisciValore($this->moduloModIp, $_SERVER['REMOTE_ADDR']);
		}

	//**************************************************************************
	// l'eliminazione standard di un record è logica, non fisica
	function eliminaRecord(waRecord $riga, $fine_script = true)
		{
		if (!$riga)
			return;
		
		$riga->inserisciValore("sospeso", 1);
		$this->setEditorData($riga);
		$this->salvaRigheDB($riga->righeDB);
		if ($fine_script)
			$this->ritorna();
		}

	//**************************************************************************
	// verifica che un modulo soddisfi i requisiti di obbligatorietà, 
	// altrimenti interrompe l'operazione di salvataggio con un messaggio
	function verificaObbligo(waModulo $modulo)
		{
		if (!$modulo->verificaObbligo())
			$this->mostraErroreObbligatorieta ();
		}

	//***************************************************************************
	/**
	* manda in output la pagina
	* 
	* manda in output la pagina, compresi gli elementi aggiunti alla pagina 
	* tramite il metodo {@link aggiungiElemento}, utilizzando il foglio di 
	* stile indicato nella proprietà {@link xslt}.
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato 
	* l'output della pagina; altrimenti la funzione ritorna il buffer di output 
	 * 
	 * non si capisce perchè in php 5.2 (o forse è la versione di libxml)
	 * aggiunge in alcuni casi un cdata al tag <script type="text/javascript"...
	 * 
	 * per questo motivo facciamo l'overloading: per togliere sta roba dalle 
	 * palle
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		// se e' stato istanziato l'oggetto pfDoc, crea la documentazione della
		// pagina
		$this->creaDocumentazione();

		if (defined("APPL_DEBUG") && $_GET['xml'] == 1)
			{
			$this->mostraXML(); 
			exit();
			}
		
		$this->costruisciXML();

		$this->buffer = str_replace('<script type="text/javascript"><![CDATA[', '</table><script type="text/javascript">', $this->buffer);
		$this->buffer = str_replace(']]></script>', '</script>', $this->buffer);
		$html = $this->trasforma();
		
		if (stripos($html, "<html") !== false)
			// effettuiamo la correzione "strict" solo se stiamo
			// effettivamente mandando in output un'intera pagina HTML
			// (se non è una pagina intera - ad esempio il contenuto di un div -
			// o se l'output non è html, allora non si fa nessun controllo 
			// strict)
			$html = $this->correggiHTML($html);
		
		if ($bufferizza)
			return $html;
			
		header("Content-Type: text/html; charset=utf-8");			
		echo $html;

		}
		
	//*****************************************************************************
	function verificaViolazioneLock(waModulo $modulo)
		{
		if ($modulo->record->valore($this->moduloModId) != $modulo->dammiModId())
			$this->mostraMessaggio("Errore violazione lock",
							"Errore di violazione del lock del record: il record e'" .
							" stato modificato da un altro operatore tra il momento " .
							" della tua lettura e il momento della tua scrittura." .
							" Sei pregato di ricaricare la pagina di modifica e" .
							" ripetere le modifiche che hai effettuato");
		}

	//***************************************************************************
	function creaModuloLogin()
		{
		$modulo = $this->dammiModulo();
		$ctrl = new waTesto($modulo, "pagina_redirect");
		$ctrl->visibile = false;

		$ctrl = $modulo->aggiungiTesto("email", "E-mail", false, true);
		$modulo->larghezza = 400;
		$ctrl->caratteriVideo = 50;
		$ctrl->larghezza = 140;

		$ctrl = $modulo->aggiungiPassword("pwd", "Password");
		$ctrl->caratteriMax = 12;
		$ctrl->larghezza = 140;
		
		
		$button = new waBottone($modulo, 'cmd_invia', 'Accedi');
		$button->alto = $ctrl->alto + $ctrl->altezza +
						($modulo->altezzaLineaControlli * 2);
		$button->larghezza = 120;
		$button->sinistra = ($modulo->larghezza - $button->larghezza) / 2;
		
		$modulo->leggiValoriIngresso();
		
		return $modulo;
		}
	
	//***************************************************************************
	/**
	 * definisce l'array contenente i dati dell'utente loggato da tenere in 
	 * sessione
	 */
	function setDatiUtente(waRecord $riga)
		{
		$this->utente = $this->record2Array($riga);
		
		// togliamo dalla sessione le pwd (vabbe' che sono criptate, ma insomma...)
		foreach ($this->utente as $k => $v)
			{
			if (strpos($k, "pwd") !== false)
				unset($this->utente[$k]);
			}
		
		// aggiungiamo ai dati dell'utente la descrizione, che essendo un "contenitore"
		// non entrerebbe nell'array
		$this->utente["descrizione"] = $riga->valore("descrizione");
		
		}
		
	//***************************************************************************
	function eseguiLogin(waModulo $modulo, $paginaRedirect = '')
		{
		$this->verificaObbligo($modulo);
		
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT utenti.*" .
			" FROM utenti" .
			" WHERE utenti.email=" . $dbconn->stringaSql($modulo->input['email']) .
			" AND NOT utenti.sospeso";
		$rigaUtente = $this->dammiRigheDB($sql, $dbconn, 1)->righe[0];
		if (!$rigaUtente)
			$this->mostraMessaggio ("Accesso non abilitato", "Accesso non abilitato");
		
		if (!$modulo->input["pwd"])
			{
			// password vuota: da mandare via email
			$this->inviaMailCredenziali($rigaUtente);
			$this->mostraMessaggio("Credenziali inviate", "Le credenziali sono state inviate all'indirizzo email indicato.");
			}
			
		if ($this->decryptPassword($rigaUtente->valore("pwd")) != $modulo->input["pwd"])
			$this->mostraMessaggio ("Accesso non abilitato", "Accesso non abilitato");
		
		if (!$rigaUtente->valore("supervisore"))
			{
			// leggiamo i privilegi dell'utente
			$sql = "SELECT id_privilegio" .
					" FROM privilegi_utenti" .
					" WHERE id_utente=" . $dbconn->interoSql($rigaUtente->valore('id_utente')) .
					" ORDER BY id_privilegio";
			$rsPrivilegi = $this->dammiRigheDB($sql, $dbconn);
			
			// se c'e', la prima riga è quella che dice se l'utente ha il 
			// privilegio di backoffice
			if ($this->siglaSezione == "backoffice")
				{
				if (!$rsPrivilegi->righe[0] || $rsPrivilegi->righe[0]->valore("id_privilegio") != PRIV_BACKOFFICE)
					$this->mostraMessaggio ("Accesso non abilitato", "Accesso non abilitato");
				}

			$this->setDatiUtente($rigaUtente);
			foreach ($rsPrivilegi->righe as $rigaPrivilegio)
				$this->utente['privilegi'][$rigaPrivilegio->valore("id_privilegio")] = $rigaPrivilegio->valore("id_privilegio");
			}
		else
			$this->setDatiUtente($rigaUtente);
		
		$this->ridireziona($modulo->input["pagina_redirect"] ? $modulo->input["pagina_redirect"] : $paginaRedirect);
		}
			    
	//***************************************************************************
	function inviaMailCredenziali(waRecord $riga)
		{
		// il server di next-data non ci dice se siamo sotto https o meno; per
		// capirlo usiamo HTTP_X_FORWARDED_FOR, che pero' non ci da una garanzia 
		// assoluta (dipende da come e' configurato il dominio nel pannello di 
		// controllo
		$protocol = $_SERVER["HTTP_X_FORWARDED_FOR"]? "https" : "http";

		
		$cr = "\r\n";
		$body = "Ciao " . $riga->valore("nickname") . ",$cr$cr" . 
				"queste sono le credenziali mediante cui potrai accedere a $this->titolo:$cr$cr" .
				
				"Login:\t" . $riga->valore("email") . $cr . 
				"Password:\t" . $this->decryptPassword($riga->valore("pwd")) . "$cr$cr" .
				
				"Una volta all'interno del programma potrai" .
				" modificare la tua password accedendo al tuo Profilo.$cr$cr".
				
				"Questa mail si genera automaticamente, non rispondere o scrivere" .
				" a questo indirizzo mail. Per necessità scrivi all'indirizzo" .
				" di assistenza (assistenza@$this->dominio).$cr$cr" .
				
				"Ti auguriamo buoni passi,$cr$cr" . 
				
				"lo staff tecnico $cr" .
				"$this->titolo$cr";
		
		if (!$this->mandaMail($riga->valore("email"), "Credenziali accesso  $this->titolo", $body))
			$this->mostraMessaggio("Errore invio email", 
									"Attenzione: si e' verificato" .
									" un errore durante l'invio del messaggio email contenente le credenziali di accesso.<p>" .
									"Sei pregato di avvisare l'assistenza tecnica e ripetere l'operazione" .
									" quando l'inconveniente sara' stato risolto.");
		
		}
	
	//*****************************************************************************
	// verifica se l'utente loggato ha il privilegio richiesto
	function haPrivilegio($id_privilegio, $id_privilegio_proprio_record = false, waRecord $riga = NULL)
		{
		if ($this->isSupervisore())
			return true;
		
		// se ha il privilegio generico, ok
		if ($this->utente["privilegi"][$id_privilegio])
			return true;
		
		// controllo sul privilegio particolare e sull'appartenenza del record
		if ($id_privilegio_proprio_record && $riga)
			{
			if ($this->utente["privilegi"][$id_privilegio_proprio_record] &&
					$riga->valore("id_utente") == $this->utente['id_utente'])
				return true;
			}
		
		return false;
		}

	//*****************************************************************************
	// verifica se l'utente loggato ha il privilegio richiesto, senno esce con errore
	function checkPrivilegio($id_privilegio, $id_privilegio_proprio_record = false, waRecord $riga = NULL)
		{
		// se ha il privilegio generico, ok
		if (!$this->haPrivilegio($id_privilegio, $id_privilegio_proprio_record, $riga))
			// l'tente non ha il privilegio
			$this->mostraMessaggio("Operazione non permessa", "Operazione non permessa");
		}

	//*****************************************************************************
	// verifica se l'utente loggato è supervisore
	function isSupervisore()
		{
		return $this->utente["supervisore"] == 1;
		}

	//*****************************************************************************
	/**
	 * NON È PIÙ UTILIZZATA!!! la UDF sui campi longtext era lentissima
	 * 
	 * funzione che compone il casting del risultato di una strip_tags di mysql
	 * (UDF); c'e' un buco in mysql per cui occorre sempre fare questo casting
	 * per la ricerca, se vuoi usare una UDF
	 * 
	 * @param type $tocast
	 * @param type $as
	 * @return type
	 */
	function castrip($tocast, $as = false)
		{
		$toret = "(CAST(STRIP_TAGS($tocast) AS CHAR CHARACTER SET utf8) COLLATE utf8_unicode_ci)";
		if ($as)
			$toret .= " AS $as";
		return $toret;
		}
		
	//***************************************************************************
	/**
	* 
	* @return string
	*/
	function array2xml($arraglio)
		{
		if (is_array($arraglio))
			{
			foreach($arraglio as $k => $v)
				{
				if (is_numeric($k))
					$k = "elemento_id_$k";
				$toret .= "<$k>";
				if (is_array($v))
					$toret .= $this->array2xml($v);
				else
					$toret .= htmlspecialchars($v);
				$toret .= "</$k>\n";
				}
			}
		
		return $toret;
		}

	//*****************************************************************************
	function rs2xml(waRigheDB $rs, $nr_pagina = false, $righe_x_pagina = APPL_MAX_ARTICOLI_PAGINA)
		{
		$toret .= "<nr_righe_senza_limite>" . $rs->nrRigheSenzaLimite() . "</nr_righe_senza_limite>\n";
		if ($nr_pagina !== false)
			$toret .= "<nr_pagina>$nr_pagina</nr_pagina>\n" .
						"<righe_x_pagina>$righe_x_pagina</righe_x_pagina>\n";

		
		foreach ($rs->righe as $riga)
			{
			$toret .= "<riga>\n";
			for ($i = 0; $i < $rs->nrCampi(); $i++)
				{
				$toret .= "<" . strtolower($rs->nomeCampo($i)) . ">";
				if ($rs->tipoCampo($i) == WADB_DATA)
					$toret .= date("d/m/Y", $riga->valore($i));
				elseif ($rs->tipoCampo($i) == WADB_DATAORA)
					$toret .= date("d/m/Y H.i.s", $riga->valore($i));
				else
					$toret .= htmlspecialchars($riga->valore($i));
				$toret .= "</" . strtolower($rs->nomeCampo($i)) . ">\n";
				}
			$toret .= "</riga>\n";
			}
		
		return $toret;
		}
		
	//***************************************************************************
	/**
	* data una stringa inputata dall'utente, la sanifica secondo i privilegi
	* posseduti
	*/
	function sanificaHTML(waRecord $riga, $nome_campo)
		{
		if ($this->haPrivilegio(PRIV_HTML_ESTESO))
			return $riga->valore($nome_campo);
			
		require_once(dirname(__FILE__) . "/inputfilter/class.inputfilter_clean.php5");
		if ($this->haPrivilegio(PRIV_HTML_BASE))
			{
			// permessi base: italic bold e anchor
			$tags = array("i", "b", "u", "a");
			$attribs = array("href");
			}
		else
			{
			// nessun privilegio di scrittura html
			$tags = array();
			$attribs = array();
			}

		$filtro = new InputFilter($tags, $attribs, 0, 0, 1);
		$sanificata = $filtro->process($riga->valore($nome_campo));
		$sanificata = nl2br($sanificata, true);
		$riga->inserisciValore($nome_campo, $sanificata);
			
		return $riga->valore($nome_campo);

		}
		
	//*************************************************************************
	// ritorna l'url della pagina di visualizzazione di un documento per un
	// controllo waCaricaFile
	function setUrlDoc(waCaricaFile $ctrl)
		{
		$riga = $ctrl->modulo->righeDB->righe[0];
		if ($riga && $riga->valore($ctrl->nome))
			{
			$qs = "tabella=" .$riga->righeDB->nomeTabella(0) .
					"&tipo=" . $ctrl->nome .
					"&id=" . $riga->valore(0);
			$ctrl->paginaVisualizzazione=  "$this->httpwd/downloaddoc.php?$qs";
			}
		}
		
	//***************************************************************************** 
	// elimina eventuali documenti salvati inprecedenza
	//***************************************************************************** 
	function eliminaDoc(waCaricaFile $ctrl)
		{
		$rs = $ctrl->modulo->righeDB;

		$tabella = $rs->nomeTabella(0);
		$id = $rs->righe[0]->valore(0);
		$pattern = "$this->directoryDoc/$tabella/$ctrl->nome/$id.*";
		$files = glob($pattern);
		if ($files)
			{
			foreach ($files as $file)
				@unlink($file);
			}
		}
		
	//***************************************************************************** 
	// salva l'eventuale documento allegato
	//***************************************************************************** 
	function salvaDoc(waCaricaFile $ctrl)
		{
		// salvataggio del documento (se c'e'...)
		if ($ctrl->daEliminare())
			// cancelliamo un eventuale documento esistente
			$this->eliminaDoc($ctrl);
		elseif ($ctrl->erroreCaricamento())			
	    	$this->mostraMessaggio("Errore caricamento file", 
	    						"Si e' verificato l'errore " . 
	    						$ctrl->erroreCaricamento() . 
	    						" durante il caricamento del documento $ctrl->nome." .
	    						" Si prega di avvertire l'assistenza tecnica.", 
    							false, true);
		elseif ($ctrl->daSalvare())
			{
			$this->eliminaDoc($ctrl);
			$rs = $ctrl->modulo->righeDB;
			$tabella = $rs->nomeTabella(0);
			@mkdir($dest = "$this->directoryDoc/$tabella");
			@mkdir($dest = "$this->directoryDoc/$tabella/$ctrl->nome");
			$id = $rs->righe[0] ? $rs->righe[0]->valore(0) : $rs->connessioneDB->ultimoIdInserito();
			$dest = "$dest/$id." . pathinfo($ctrl->valoreInput, PATHINFO_EXTENSION);
			if (!$ctrl->salvaFile($dest))
	    		$this->mostraMessaggio("Errore spostamento file", 
	    						"Si e' verificato un errore " . 
	    						" durante lo spostamento del documento $ctrl->nome." .
	    						" Si prega di avvertire l'assistenza tecnica.", 
    							false, true);
			}
			
		return true;			
		}
	
	//***************************************************************************
	function creaRSSArticoli(waConnessioneDB $dbconn)
		{
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n" .
					"<rss version='2.0' xmlns:content='http://purl.org/rss/1.0/modules/content/'>\n" .
					"<channel>\n" .
					"<title>$this->titolo</title>\n" .
					"<link>http://$this->dominio$this->httpwd</link>\n" .
					"<description>$this->titolo</description>\n" .
					"<pubDate>" . gmdate("r") . "</pubDate>\n" .
					"<lastBuildDate>" . gmdate("r") . "</lastBuildDate>\n" .
					"<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n" .
					"<generator>$this->nome</generator>\n" .
					"<webMaster>$this->emailSupporto</webMaster>\n" .
					"<image>\n" .
					"<title>$this->titolo</title>\n" .
					"<url>http://$this->dominio$this->httpwd/ui/img/street/22.jpg</url>\n" .
					"<link>http://$this->dominio$this->httpwd</link>\n" .
					"</image>\n";

		$sql = "SELECT articoli.*," .
			" categorie_articoli.nome as nome_categoria," .
			" utenti.nickname" . 
			" FROM articoli" .
			" INNER JOIN categorie_articoli ON articoli.id_categoria_articolo=categorie_articoli.id_categoria_articolo" .
			" INNER JOIN utenti ON articoli.id_utente=utenti.id_utente" .
			" WHERE NOT articoli.sospeso" .
			" AND articoli.data_ora_inizio_pubblicazione<=NOW()" .
			" AND (articoli.data_ora_fine_pubblicazione>=NOW() OR articoli.data_ora_fine_pubblicazione IS NULL)" .
			" ORDER BY articoli.data_ora_inizio_pubblicazione DESC";
		
		$rs = $this->dammiRigheDB($sql, $dbconn, 50, 0);
		
		foreach ($rs->righe as $riga)
			{
			$link = "http://$this->dominio$this->httpwd/street/articolo.php?id_articolo=$riga->id_articolo";
			$xml .= "<item>\n" .
						"<title><![CDATA[$riga->titolo]]></title>\n" .
      					"<link>$link</link>\n" .
      					"<description><![CDATA[$riga->abstract]]></description>\n" .
      					"<content:encoded><![CDATA[<i>$riga->abstract</i><p />$riga->testo]]></content:encoded>\n" .
					    "<pubDate>" . gmdate("r", $riga->data_ora_inizio_pubblicazione) . "</pubDate>\n" .
      					"<guid isPermaLink='true'>$link</guid>\n" .
					    "<author>" . 
						    "<name>$riga->nickname</name>\n" .
					    "</author>\n" . 
						"</item>\n";
			}
			
		$xml .= "</channel>\n" . 
					"</rss>\n";
					
		@mkdir("$this->directoryDoc/rss/");
		@file_put_contents("$this->directoryDoc/rss/$this->fileRSSArticoli", $xml);
			
		}
		
	//***************************************************************************
	function creaRSSCommenti(waConnessioneDB $dbconn, $id_articolo)
		{
		$sql = "select titolo from articoli where id_articolo=" . $dbconn->interoSql($id_articolo);
		$rigaArticolo = $this->dammiRigheDB($sql, $dbconn, 1)->righe[0];
		$titolo = strip_tags($rigaArticolo->titolo);
		$titolo = substr($titolo, 0, 50) . (strlen($titolo) > 50 ? "..." : '');
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n" .
					"<rss version='2.0' xmlns:content='http://purl.org/rss/1.0/modules/content/'>\n" .
					"<channel>\n" .
					"<title>$this->titolo - commenti a $titolo</title>\n" .
					"<link>http://$this->dominio$this->httpwd/street/articolo.php?id_articolo=$id_articolo</link>\n" .
					"<description>$this->titolo - commenti a $titolo</description>\n" .
					"<pubDate>" . gmdate("r") . "</pubDate>\n" .
					"<lastBuildDate>" . gmdate("r") . "</lastBuildDate>\n" .
					"<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n" .
					"<generator>$this->nome</generator>\n" .
					"<webMaster>$this->emailSupporto</webMaster>\n" .
					"<image>\n" .
					"<title>$this->titolo</title>\n" .
					"<url>http://$this->dominio$this->httpwd/ui/img/street/22.jpg</url>\n" .
					"<link>http://$this->dominio$this->httpwd</link>\n" .
					"</image>\n";

		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		$sql = "SELECT commenti.*," .
			" utenti.nickname," . 
			" cg.data_ora_creazione AS data_ora_creazione_genitore," .
			" ug.nickname AS nickname_genitore," .
				" FLOOR(" .
					" (select count(*) from commenti as c" .
					" where not c.sospeso" .
					" and c.id_articolo=commenti.id_articolo" .
					" and c.chiave_ordinamento<commenti.chiave_ordinamento)" .
					" / $max_ap) AS nr_pagina" .
			" FROM commenti" .
			" INNER JOIN utenti ON commenti.id_utente=utenti.id_utente" .
			// cg = commento genitore
			" LEFT JOIN commenti AS cg ON commenti.id_commento_genitore=cg.id_commento" .
			" LEFT JOIN utenti AS ug ON cg.id_utente=ug.id_utente" .
			" WHERE NOT commenti.sospeso" .
			" AND commenti.id_articolo=" . $dbconn->interoSql($id_articolo) .
			" ORDER BY commenti.id_commento DESC";
		
		$rs = $this->dammiRigheDB($sql, $dbconn, 50, 0);
		
		foreach ($rs->righe as $riga)
			{
			$link = "http://$this->dominio$this->httpwd/street/articolo.php?id_articolo=$id_articolo&amp;pag_commenti=$riga->nr_pagina#commento_$riga->id_commento";
			$titolo = strip_tags($riga->testo);
			$titolo = substr($titolo, 0, 50) . (strlen($titolo) > 50 ? "..." : '');
			$dati_genitore = $riga->nickname_genitore ? 
								"<i>(in risposta al commento di " .
									$riga->nickname_genitore . 
									" del " . date("d/m/Y", $riga->data_ora_creazione_genitore) .
									" alle " . date("H.i.s", $riga->data_ora_creazione_genitore) .
									")</i><br/><br/>" 
									: '';
			$xml .= "<item>\n" .
						"<title><![CDATA[$titolo]]></title>\n" .
      					"<link>$link</link>\n" .
      					"<description></description>\n" .
      					"<content:encoded><![CDATA[$dati_genitore $riga->testo]]></content:encoded>\n" .
					    "<pubDate>" . gmdate("r", $riga->data_ora_creazione) . "</pubDate>\n" .
      					"<guid isPermaLink='true'>$link</guid>\n" .
					    "<author>" . 
						    "<name>$riga->nickname</name>\n" .
					    "</author>\n" . 
						"</item>\n";
			}
			
		$xml .= "</channel>\n" . 
					"</rss>\n";
					
		@mkdir("$this->directoryDoc/rss/");
		@file_put_contents("$this->directoryDoc/rss/$this->fileRSSCommenti.$id_articolo.xml", $xml);
			
		}
		
	//***************************************************************************
	function creaRSSInterventi(waConnessioneDB $dbconn, $id_argomento)
		{
		$sql = "select titolo from argomenti where id_argomento=" . $dbconn->interoSql($id_argomento);
		$rigaArgomento = $this->dammiRigheDB($sql, $dbconn, 1)->righe[0];
		$titolo = strip_tags($rigaArgomento->titolo);
		$titolo = substr($titolo, 0, 50) . (strlen($titolo) > 50 ? "..." : '');
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n" .
					"<rss version='2.0' xmlns:content='http://purl.org/rss/1.0/modules/content/'>\n" .
					"<channel>\n" .
					"<title>$this->titolo - interventi su $titolo</title>\n" .
					"<link>http://$this->dominio$this->httpwd/street/argomento.php?id_argomento=$id_argomento</link>\n" .
					"<description>$this->titolo - interventi a $titolo</description>\n" .
					"<pubDate>" . gmdate("r") . "</pubDate>\n" .
					"<lastBuildDate>" . gmdate("r") . "</lastBuildDate>\n" .
					"<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n" .
					"<generator>$this->nome</generator>\n" .
					"<webMaster>$this->emailSupporto</webMaster>\n" .
					"<image>\n" .
					"<title>$this->titolo</title>\n" .
					"<url>http://$this->dominio$this->httpwd/ui/img/street/22.jpg</url>\n" .
					"<link>http://$this->dominio$this->httpwd</link>\n" .
					"</image>\n";

		$max_ap = APPL_MAX_ARTICOLI_PAGINA;
		$sql = "SELECT interventi.*," .
			" utenti.nickname," . 
			" ig.data_ora_creazione AS data_ora_creazione_genitore," .
			" ug.nickname AS nickname_genitore," .
				" FLOOR(" .
					" (select count(*) from interventi as c" .
					" where not c.sospeso" .
					" and c.id_argomento=interventi.id_argomento" .
					" and c.chiave_ordinamento<interventi.chiave_ordinamento)" .
					" / $max_ap) AS nr_pagina" .
			" FROM interventi" .
			" INNER JOIN utenti ON interventi.id_utente=utenti.id_utente" .
			// ig = intervento genitore
			" LEFT JOIN interventi AS ig ON interventi.id_intervento_genitore=ig.id_intervento" .
			" LEFT JOIN utenti AS ug ON ig.id_utente=ug.id_utente" .
			" WHERE NOT interventi.sospeso" .
			" AND interventi.id_argomento=" . $dbconn->interoSql($id_argomento);
			" ORDER BY interventi.id_intervento DESC";
		
		$rs = $this->dammiRigheDB($sql, $dbconn, 50, 0);
		
		foreach ($rs->righe as $riga)
			{
			$link = "http://$this->dominio$this->httpwd/street/argomento.php?id_argomento=$id_argomento&amp;pag_interventi=$riga->nr_pagina#intervento_$riga->id_intervento";
			$titolo = strip_tags($riga->testo);
			$titolo = substr($titolo, 0, 50) . (strlen($titolo) > 50 ? "..." : '');
			$dati_genitore = $riga->nickname_genitore ? 
								"<i>(in risposta all' intervento di $riga->nickname_genitore" . 
									" del " . date("d/m/Y", $riga->data_ora_creazione_genitore) .
									" alle " . date("H.i.s", $riga->data_ora_creazione_genitore) .
									")</i><br/><br/>" 
									: '';
			$xml .= "<item>\n" .
						"<title><![CDATA[$titolo]]></title>\n" .
      					"<link>$link</link>\n" .
      					"<description></description>\n" .
      					"<content:encoded><![CDATA[$dati_genitore $riga->testo]]></content:encoded>\n" .
					    "<pubDate>" . gmdate("r", $riga->data_ora_creazione) . "</pubDate>\n" .
      					"<guid isPermaLink='true'>$link</guid>\n" .
					    "<author>" . 
						    "<name>$riga->nickname</name>\n" .
					    "</author>\n" . 
						"</item>\n";
			}
			
		$xml .= "</channel>\n" . 
					"</rss>\n";
					
		@mkdir("$this->directoryDoc/rss/");
		@file_put_contents("$this->directoryDoc/rss/$this->fileRSSInterventi.$id_argomento.xml", $xml);
			
		}
		
	//*****************************************************************************
	// dato un campo di un record di una tabella che potrebbe contenere html, 
	// lo ritorna bovinamente strippato
	function stripCampo($testo)
		{
		
		$testo = str_ireplace("<br", "\n<br", $testo);
		$testo = str_ireplace("<p", "\n\n<p", $testo);
		$testo = str_replace("\r\n", "\n", $testo);
		$testo = html_entity_decode(strip_tags($testo), ENT_COMPAT|ENT_HTML401, "UTF-8");
		$testo = trim($testo);
		while (strpos($testo, "\n\n\n")!== false)
			{
			$testo = str_replace("\n\n\n", "\n\n", $testo);
			}
		return $testo;
		}

//***************************************************************************
	} 	// fine classe news22
	
//***************************************************************************

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_APPLICATION_CLASS'))
