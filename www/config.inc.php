<?php
if (!defined('__CONFIG_VARS'))
{
	define('__CONFIG_VARS',1);
	
	$__DIR__ = __DIR__;

	// file contenente i veri valori di configurazione, che non devono finire
	// su git, mentre il presente file rimane per documentazione
	@include "$__DIR__/myconfig.inc.php";
	
	// file contenente i parametri della versione (che cambiano e devono essere 
	// inviati al server, a differenza di questi)
	include "$__DIR__/versionconfig.inc.php";
	
	
	define('APPL_DOMAIN', 						$_SERVER['HTTP_HOST']);
//	define('APPL_DIRECTORY', 					'');
	define('APPL_TMP_DIRECTORY', 				"$__DIR__/web_files/tmp");
	define('APPL_DOC_DIRECTORY', 				"$__DIR__/web_files");
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
	
	define("WAMODULO_EXTENSIONS_DIR", 			"$__DIR__/wamodulo_ext");	// directory estensioni classe modulo
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
	
	set_include_path(get_include_path() . ":$__DIR__/third_party_libs:$__DIR__/../../third_party_libs:$__DIR__/../../third_party_libs/moduli_pear");
} //  if (!defined('__CONFIG_VARS'))
