<?php
//*****************************************************************************
include "street.inc.php";

//*****************************************************************************
class profilo extends street
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;

	//**************************************************************************
	function __construct()
		{
		parent::__construct();
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_UTENTI_ELIMINA, PRIV_UTENTI_ELIMINA_PROPRI, $this->modulo->record);
			$this->eliminaRecord($this->modulo->record, false);
			unset($this->utente);
			$this->datiSessione = array();
			$this->mostraMessaggioOk("Utenza eliminata correttamente", "Utenza eliminata correttamente");
			}

		$this->mostra();
		}
		
	//***************************************************************************
	function creaModulo()
		{
		$this->modulo = new waModulo(null, $this);
		$this->modulo->righeDB = $this->dammiRecordset();
		$solaLettura = false;


		$this->modulo->aggiungiTesto("nickname", "Nickname", true);
		$this->modulo->aggiungiEmail("email", "E-Mail", $solaLettura, true);
		$ctrlP = $this->modulo->aggiungiPassword("pwd", "Password", $solaLettura);
		$ctrl = $this->modulo->aggiungiPassword("pwd_conferma", "Password per conferma", $solaLettura);
			$ctrlP->corrispondenzaDB = $ctrl->corrispondenzaDB = false;
		$ctrl = $this->modulo->aggiungiCaricaFile("avatar", "Avatar (max 100x100)", $solaLettura);
		$this->modulo->aggiungiAreaTesto("descrizione", "Dice di sè", $solaLettura);
		$this->modulo->aggiungiLogico("flag_sottoscrizione_articoli_via_email", "Inviami nuovi articoli via email", $solaLettura);
		$this->modulo->aggiungiLogico("flag_sottoscrizione_argomenti_via_email", "Inviami nuovi argomenti via email", $solaLettura);

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
				" WHERE utenti.id_utente=" . $dbconn->interoSql($this->utente["id_utente"]);
			
		$rs = $this->dammiRigheDB($sql, $dbconn, 1);
		if (!$rs->righe)
			$this->mostraMessaggio("Utente non trovato????", "Utente non trovato????", false, true);
		
		return $rs;
		}
		
	//***************************************************************************
	function aggiornaRecord()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->righe[0];
			
		if ($this->modulo->input["pwd"] && 
			$this->decryptPassword($riga->valore("pwd")) != $this->modulo->input["pwd"])
			{
			if (!$this->verificaPassword($this->modulo->input["pwd"], 
										$this->modulo->input["pwd_conferma"],
										$riga->valore("pwd"),
										$riga->valore("pwd2"),
										$riga->valore("pwd3")))
				$this->mostraMessaggio ("Composizione password errata", "Composizione password errata (min. 8 caratteri solo alfanumerici; almeno un numero; almeno una maiuscola; almeno una minuscola; diversa dalle ultime 3 utilizzate)");
			$riga->inserisciValore("pwd3", $riga->valore("pwd2"));
			$riga->inserisciValore("pwd2", $riga->valore("pwd"));
			$riga->inserisciValore("pwd", $this->encryptPassword($this->modulo->input["pwd"]));
			$riga->inserisciValore("data_ora_ultima_modifica_pwd", time());
			}
		
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$dbconn->iniziaTransazione();
		$this->modulo->salva();

		// sanificazione dei testi che vanno pubblicati e in cui non vogliamo html
		$this->sanificaHTML($riga, "nickname");
		$this->sanificaHTML($riga, "descrizione");
		
		$this->setEditorData($riga);
		$this->salvaRigheDB($riga->righeDB);
		
		// salvataggio del flag di ricezione 
		
		// salvataggio avatar
		if ($this->modulo->controlliInput["avatar"]->daSalvare())
			{
			if (!$avatar_info = @getimagesize($this->modulo->controlliInput["avatar"]->dammiValoreTmp()))
				{
				$this->mostraMessaggio("Il file non è un'immagine valida", "Operazione non permessa: Il file non è un'immagine valida");
				}
			if ($avatar_info[0] > 100 || $avatar_info[1] > 100)
				$this->mostraMessaggio("Il file non è un'immagine valida", "Operazione non permessa: Il file è troppo grande (max 100x100 pixel)");
			}
		$this->salvaDoc($this->modulo->controlliInput["avatar"]);
		
		$this->salvaRigheDB($riga->righeDB);
		$dbconn->confermaTransazione();
		
		// aggiorniamo i dati in sessione dell'utente
		$this->setDatiUtente($riga);
		
		$this->mostraMessaggioOk("Modifica avvenuta correttamente", "Modifica avvenuta correttamente");
		
		}
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new profilo();
;