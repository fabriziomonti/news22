<?php
//*****************************************************************************
include "street.inc.php";

//*****************************************************************************
class registrazione extends street
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;

	//**************************************************************************
	function __construct()
		{
		parent::__construct(false);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		else
			{
			$this->aggiungiElemento($this->modulo->controlliInput["captcha"]->valore, "captcha_key");
			$this->mostra();
			}

		}
		
	//***************************************************************************
	function creaModulo()
		{
		$this->modulo = new waModulo(null, $this);
		$this->modulo->righeDB = $this->dammiRecordset();
		$solaLettura = false;

		$this->modulo->aggiungiEmail("email", "E-Mail", $solaLettura, true);

		$this->modulo->aggiungiTesto("nickname", "Nickname", $solaLettura, true);
		$ctrl = $this->modulo->aggiungiAreaTesto("condizioni_servizio", "Condizioni del servizio", true);
			$ctrl->corrispondenzaDB = false;
		$ctrl = $this->modulo->aggiungiLogico("ho_letto_condizioni_servizio", "Accetto le condizioni del servizio", $solaLettura, true);
		$ctrl = $this->modulo->aggiungiAreaTesto("informativa_privacy", "Informativa privacy", true);
			$ctrl->corrispondenzaDB = false;
		$ctrl = $this->modulo->aggiungiLogico("ho_letto_informativa_privacy", "Ho letto l'informativa privacy", $solaLettura, true);
			
		$ctrl = $this->modulo->aggiungiCaptcha("captcha", "Codice di controllo", $solaLettura, true);
		if (!$_POST)
			$ctrl->valore = $this->setGetCaptcha ();

		$button = new waBottone($this->modulo, 'cmd_invia', 'Accedi');
		$this->modulo->leggiValoriIngresso();
		}

	//***************************************************************************
	/**
	* - restituiamo un recordset (si spera) vuoto in modo da avere il binding dei campi
	*
	* @return waRigheDB
	*/
	function dammiRecordset()
		{
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT utenti.*" .
				" FROM utenti" .
				" WHERE email=" . $dbconn->stringaSql($_POST['email']);
			
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		if ($rs->righe)
			$this->mostraMessaggio("Utente già censito", "Utente già censito", false, true);
		
		return $rs;
		}
		
	//***************************************************************************
	function aggiornaRecord()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->aggiungi();
			
		$this->setEditorData($riga);
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$dbconn->iniziaTransazione();
		$this->modulo->salva();
		$riga->inserisciValore("email_originale", $this->modulo->input['email']);
		$riga->inserisciValore("pwd", $this->encryptPassword($this->getPassword()));
		$riga->inserisciValore("data_ora_ultima_modifica_pwd", time());

		// sanificazione dei testi che vanno pubblicati e in cui non vogliamo html
		$this->sanificaHTML($riga, "nickname");
		$this->sanificaHTML($riga, "descrizione");
		
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $dbconn->ultimoIdInserito();
		$this->aggiungiPrivilegi($idInserito);
		
//		$this->inviaMailCredenziali($riga);
		
		$dbconn->confermaTransazione();

		$this->mostraMessaggioOk ("Registrazione avvenuta correttamente", 
								"La tua richiesta di resgistrazione è avvenuta correttamente:" .
								" le credenziali di accesso sono state inviate alla tua casella di posta elettronica.");
		
		
		}
		
	
	//***************************************************************************
	/**
	 * @access protected
	 * @ignore
	 */
	function setGetCaptcha()

		{
		// creiamo la stringa che andrà mostrata nell'immagine e la
		// salviamo in sessione
		$mt = microtime();
		$elems = explode(" ", microtime());
		$chiave = chr((substr($elems[1], -1) + ord('a'))) . substr($elems[0], 2, 4) . substr($elems[1], -3);
		$chiave = substr($chiave, 0, -1) . chr(substr($chiave, -1) + ord('l'));
		// sostituisco un'eventuale 0 con 1 in modo da non potersi 
		// confondere con la lettera "o" maiuscola
		$chiave = str_replace("0", "1", substr($chiave, 0, 5));

		// il valore in questo caso è la chiave del parametro di sessione 
		// che nasconde il vero valore
		$valore = microtime(true) * rand(2, 5);
		$_SESSION["WAMODULO_CODICE_CAPTCHA_$valore"] = $chiave;

		return $valore;
		}

	//*****************************************************************************
	function aggiungiPrivilegi($id_utente)
		{
		$dbconn = $this->modulo->righeDB->connessioneDB;
		
		$sql = "SELECT * FROM privilegi_utenti";
		$rs = $this->dammiRigheDB($sql, $dbconn, 0);
		foreach ($this->privilegi_iniziali as $id_privilegio)
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("id_utente", $id_utente);
			$riga->inserisciValore("id_privilegio", $id_privilegio);
			}
		$this->salvaRigheDB($rs);
		
		}
		
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new registrazione();
