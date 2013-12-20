<?php
if (!defined('__CONFIG_VARS'))
{
	define('__CONFIG_VARS',1);
	
	// file contenente i veri valori di configurazione, che non devono finire
	// su git, mentre il presente file rimane per documentazione
	@include dirname(__FILE__) . "/myconfig.inc.php";
	
	// file contenente i parametri della versione (che cambiano e devono essere 
	// inviati al server, a differenza di questi)
	include dirname(__FILE__) . "/versionconfig.inc.php";
	
	
	define('APPL_DOMAIN', 						$_SERVER['HTTP_HOST']);
//	define('APPL_DIRECTORY', 					'');
	define('APPL_TMP_DIRECTORY', 				dirname(__FILE__) . '/web_files/tmp');
	define('APPL_DOC_DIRECTORY', 				dirname(__FILE__) . '/web_files');
	define('APPL_NAME', 						'news22');
	define('APPL_TITLE', 						"News di 22 Passi d'Amore e Dintorni");
	define('APPL_SMTP_SERVER', 					'');
	define('APPL_SMTP_USER', 					'');
	define('APPL_SMTP_PWD', 					'');
	define('APPL_SMTP_SECURE',					'');
	define('APPL_SMTP_PORT',					'');
	define('APPL_SUPPORT_ADDR', 				'support_news22@webappls.com');
	define('APPL_INFO_ADDR', 					'info_news22@webappls.com');
	define('APPL_SUPPORT_TEL', 					'');
	
	define("WAMODULO_EXTENSIONS_DIR", 			dirname(__FILE__) . "/wamodulo_ext");	// directory estensioni classe modulo
	define("APPL_PWD_PWD", 						"");	// passphrase encryption password
	define("APPL_DEBUG",			 			true);	// define per la gestione del debug
	
	// nr massimo righe per pagina in street
	define('APPL_MAX_ARTICOLI_PAGINA',			20);

	// privilegi minimi 
	define("APPL_PRIVILEGI_INIZIALI", serialize(array(
											//		PRIV_UTENTI_VEDI,
													PRIV_UTENTI_MODIFICA_PROPRI,
													PRIV_UTENTI_ELIMINA_PROPRI,
													PRIV_ARTICOLI_VEDI, 
													PRIV_COMMENTI_VEDI, 
													PRIV_COMMENTI_INSERIMENTO_PROPRI,
													PRIV_COMMENTI_MODIFICA_PROPRI,
													PRIV_COMMENTI_ELIMINA_PROPRI,
													PRIV_ARGOMENTI_VEDI,
													PRIV_INTERVENTI_VEDI,
													PRIV_INTERVENTI_INSERIMENTO_PROPRI,
													PRIV_INTERVENTI_MODIFICA_PROPRI,
													PRIV_INTERVENTI_ELIMINA_PROPRI
												)));
	
} //  if (!defined('__CONFIG_VARS'))
