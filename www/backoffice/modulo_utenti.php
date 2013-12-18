<?
//*****************************************************************************
include "backoffice.inc.php";

//*****************************************************************************
class modulo_utenti extends backoffice
	{
	/**
	 *
	 * @var waModulo
	 */
	var $modulo;
		
	//**************************************************************************
	function __construct()
		{
		parent::__construct(PRIV_UTENTI_VEDI);
		
		$this->creaModulo();

		if ($this->modulo->daAggiornare())
			{
			$this->aggiornaRecord();
			}
		elseif ($this->modulo->daEliminare())
			{
			$this->checkPrivilegio(PRIV_UTENTI_ELIMINA, PRIV_UTENTI_ELIMINA_PROPRI, $this->modulo->record);
			$this->eliminaRecord($this->modulo->record);
			}
		else
			{
			$this->mostraPagina();
			}
		}

	//*****************************************************************************
	/**
	* mostra
	* 
	* costruisce la pagina contenente il modulo e la manda in output
	* @return void
	*/
	function mostraPagina()
		{
		$this->aggiungiElemento("Scheda utente", "titolo");
		$this->aggiungiElemento($this->modulo);
		$this->mostra();
			
		}
		
	//***************************************************************************
	function creaModulo()
		{
		$this->modulo = $this->dammiModulo();
		$this->modulo->righeDB = $this->dammiRecordset();
		$riga = $this->modulo->righeDB->righe[0];
		// i controlli sono editabili se:
		// - non c'e' record (siamo in iserimento) e abbiamo privilegio di inserimento
		// - c'e' record (siamo in modifica) e abbiamo privilegio di modifica
		$solaLettura = !(
						(!$riga && $this->haPrivilegio(PRIV_UTENTI_INSERIMENTO)) ||
						($riga && $this->haPrivilegio(PRIV_UTENTI_MODIFICA, PRIV_UTENTI_MODIFICA_PROPRI, $riga))
						);

		$this->modulo->aggiungiTesto("nickname", "Nickname", $solaLettura, true);
		$this->modulo->aggiungiTesto("cognome", "Cognome", $solaLettura);
		$this->modulo->aggiungiTesto("nome", "Nome", $solaLettura);
		$this->modulo->aggiungiEmail("email", "E-Mail", $solaLettura, true);
		
		if ($riga && $riga->valore("id_utente") == $this->utente['id_utente'])
			{
			// la password e' modificabile solo dal titolare della scheda
			$ctrlP = $this->modulo->aggiungiPassword("pwd", "Password", $solaLettura, true);
			$ctrl = $this->modulo->aggiungiPassword("pwd_conferma", "Password per conferma", $solaLettura, true);
			$ctrlP->corrispondenzaDB = $ctrl->corrispondenzaDB = false;
			$ctrlP->valore = $ctrl->valore = $this->decryptPassword($riga->valore("pwd"));
			$ctrlP->caratteriMax = $ctrl->caratteriMax = 12;
			}
		
		$this->modulo->aggiungiTesto("tel", "Telefono", $solaLettura);
		$this->modulo->aggiungiTesto("cell", "Cellulare", $solaLettura);
		
		$ctrl = $this->modulo->aggiungiCaricaFile("avatar", "Avatar (max 100x100)", $solaLettura);
			$this->setUrlDoc($ctrl);
		$this->modulo->aggiungiAreaTesto("descrizione", "Dice di sè", $solaLettura);
			
		if ($riga)
			$this->modulo->aggiungiDataOra("data_ora_creazione", "Data/Ora creazione", true);
		
		if ($this->isSupervisore())
			{
			$this->modulo->aggiungiLogico("supervisore", "Supervisore", $solaLettura);
			}
		
		// solo un supervisore può cipollare i privilegi altrui, 
		// e se una scheda è marcata come supervisore, non ha senso agire sui suoi
		// privilegi
		$ctrl = $this->modulo->aggiungiMultiSelezione("id_privilegio", "Privilegi", !$this->isSupervisore() || ($riga && $riga->valore("supervisore")));
			$ctrl->sql = "SELECT id_privilegio, nome FROM privilegi order by nome";
			if ($riga)
				$ctrl->sqlSelezioni = "SELECT id_privilegio FROM privilegi_utenti WHERE id_utente=" . $riga->righeDB->connessioneDB->interoSql($riga->valore("id_utente"));
			$ctrl->altezza = 200;
			$this->modulo->giustificaControllo($ctrl, false);
			
		$this->modulo->aggiungiAreaTesto("note", "Note", $solaLettura);
		$this->modulo->aggiungiLogico("invia_credenziali", "Spedisci credenziali via email", $solaLettura);
								
		$this->modulo_bottoniSubmit($this->modulo, $solaLettura, 
						$riga && $this->haPrivilegio(PRIV_UTENTI_ELIMINA, PRIV_UTENTI_ELIMINA_PROPRI, $riga));

		$this->modulo->leggiValoriIngresso();
		}

	//***************************************************************************
	/**
	* -
	*
	* @return waRigheDB
	*/
	function dammiRecordset()
		{
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT utenti.*" .
				" FROM utenti" .
				" WHERE utenti.id_utente=" . $dbconn->interoSql($_GET['id_utente']) . 
				" AND NOT utenti.sospeso";
			
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $_GET['id_utente'] ? 1 : 0);
		if ($_GET['id_utente'] && !$righeDB->righe)
			$this->mostraMessaggio("Record non trovato", "Record non trovato", false, true);

		return $righeDB;
		}
		
	//***************************************************************************
	function aggiornaRecord()
		{
		// controlli obbligatorieta' e formali
		$this->verificaObbligo($this->modulo);
		
		$riga = $this->modulo->righeDB->righe[0];
			
		if (!$riga)
			{
			$this->checkPrivilegio(PRIV_UTENTI_INSERIMENTO);
			$riga = $this->modulo->righeDB->aggiungi();
			$riga->inserisciValore("email_originale", $this->modulo->input['email']);
			$riga->inserisciValore("pwd", $this->encryptPassword($this->getPassword()));
			}
		else 
			{
			$this->checkPrivilegio(PRIV_UTENTI_MODIFICA, PRIV_UTENTI_MODIFICA_PROPRI, $riga);
			$this->verificaViolazioneLock($this->modulo);
			if ($riga->valore("id_utente") == $this->utente['id_utente'] && 
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
			}
			
		$this->setEditorData($riga);
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$dbconn->iniziaTransazione();
		$this->modulo->salva();
		
		// sanificazione dei testi che vanno pubblicati e in cui non vogliamo html
		$this->sanificaHTML($riga, "nickname");
		$this->sanificaHTML($riga, "descrizione");
		
		$this->salvaRigheDB($riga->righeDB);
		$idInserito = $dbconn->ultimoIdInserito();
		
		// salvataggio avatar
		if ($this->modulo->controlliInput["avatar"]->daSalvare())
			{
			// verifichiamo che sia un'immagine e che non sia troppo grande
			if (!$avatar_info = @getimagesize($this->modulo->controlliInput["avatar"]->dammiValoreTmp()))
				{
				$this->mostraMessaggio("Il file non è un'immagine valida", "Operazione non permessa: Il file non è un'immagine valida");
				}
			if ($avatar_info[0] > 100 || $avatar_info[1] > 100)
				$this->mostraMessaggio("Il file non è un'immagine valida", "Operazione non permessa: Il file è troppo grande (max 100x100 pixel)");
			}
		$this->salvaDoc($this->modulo->controlliInput["avatar"]);
		
		
		// solo un supervisore può aggiornare i privilegi
		if ($this->isSupervisore())
			$this->aggiornaPrivilegi($idInserito ? $idInserito : $riga->valore("id_utente"));
		
		if ($this->modulo->input["invia_credenziali"])
			$this->inviaMail($riga);
		
		$dbconn->confermaTransazione();
		$valoriRitorno = $idInserito ? array_merge(array("idInserito" => $idInserito), $this->modulo->input) : $this->modulo->input;
		$this->ritorna($valoriRitorno);
		}
		
	//*****************************************************************************
	function aggiornaPrivilegi($id_utente)
		{
		$dbconn = $this->modulo->righeDB->connessioneDB;
		$sql = "DELETE FROM privilegi_utenti WHERE id_utente=" . $dbconn->interoSql($id_utente);
		$this->eseguiDB($sql, $dbconn);
		
		$sql = "SELECT * FROM privilegi_utenti";
		$rs = $this->dammiRigheDB($sql, $dbconn, 0);
		foreach ($this->modulo->controlliInput["id_privilegio"]->valoreInput as $id_privilegio)
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("id_utente", $id_utente);
			$riga->inserisciValore("id_privilegio", $id_privilegio);
			}
		$this->salvaRigheDB($rs);
		
		}
		
	//***************************************************************************
	function inviaMail(waRecord $riga)
		{
		// il server di next-data non ci dice se siamo sotto https o meno; per
		// capirlo usiamo HTTP_X_FORWARDED_FOR, che pero' non ci da una garanzia 
		// assoluta (dipende da come e' configurato il dominio nel pannello di 
		// controllo
		$protocol = $_SERVER["HTTP_X_FORWARDED_FOR"]? "https" : "http";

		
		$cr = "\r\n";
		$body = "Ciao " . $riga->valore("nickname") . ",$cr$cr" . 
				"queste sono le credenziali mediante cui potrai accedere a $this->titolo" . 
				" ($protocol://$this->dominio$this->httpwd/):$cr$cr" .
				
				"Login:\t" . $riga->valore("email") . $cr . 
				"Password:\t" . $this->decryptPassword($riga->valore("pwd")) . "$cr$cr" .
				
				"Una volta all'interno del programma potrai" .
				" modificare la tua password accedendo al tuo Profilo.$cr$cr".
				
				"Questa mail si genera automaticamente, non rispondere o scrivere" .
				" a questo indirizzo mail. Per necessità scrivi all'indirizzo" .
				" di assistenza (assistenza@$this->dominio).$cr$cr" .
				
				"Ti auguriamo buon 22, anche se oggi è il " . date('j') . " :-)$cr$cr" . 
				
				"lo staff tecnico $cr" .
				$this->titolo . " $cr";
		
		if (!$this->mandaMail($riga->valore("email"), "Credenziali accesso " . $this->titolo, $body))
			$this->mostraMessaggio("Errore invio email", 
									"Attenzione: si e' verificato" .
									" un errore durante l'invio del messaggio email contenente le credenziali di accesso.<p>" .
									"Sei pregato di avvisare l'assistenza tecnica e ripetere l'operazione" .
									" quando l'inconveniente sara' stato risolto.", false, true);
		}
	
	//*****************************************************************************
	}
		
		
//*****************************************************************************
// istanzia la pagina
new modulo_utenti();
