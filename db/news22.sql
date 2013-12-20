SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `argomenti` (
  `id_argomento` int(11) NOT NULL AUTO_INCREMENT,
  `id_categoria_argomento` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `titolo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `abstract` text COLLATE utf8_unicode_ci,
  `data_ora_creazione` datetime NOT NULL,
  `data_ora_inizio_pubblicazione` datetime DEFAULT NULL,
  `data_ora_fine_pubblicazione` datetime DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_argomento`),
  KEY `id_utente` (`id_utente`),
  KEY `id_categoria_argomento` (`id_categoria_argomento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `articoli` (
  `id_articolo` int(11) NOT NULL AUTO_INCREMENT,
  `id_categoria_articolo` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `titolo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `abstract` text COLLATE utf8_unicode_ci,
  `testo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `data_ora_creazione` datetime NOT NULL,
  `data_ora_inizio_pubblicazione` datetime DEFAULT NULL,
  `data_ora_fine_pubblicazione` datetime DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_articolo`),
  KEY `id_utente` (`id_utente`),
  KEY `id_categoria_articolo` (`id_categoria_articolo`),
  KEY `data_ora_inizio_pubblicazione` (`data_ora_inizio_pubblicazione`),
  KEY `data_ora_fine_pubblicazione` (`data_ora_fine_pubblicazione`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `categorie_argomenti` (
  `id_categoria_argomento` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_categoria_argomento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `categorie_articoli` (
  `id_categoria_articolo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_categoria_articolo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `commenti` (
  `id_commento` int(11) NOT NULL AUTO_INCREMENT,
  `id_articolo` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_commento_genitore` int(11) DEFAULT NULL,
  `chiave_ordinamento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `testo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `data_ora_creazione` datetime NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_commento`),
  KEY `id_utente` (`id_utente`),
  KEY `id_articolo` (`id_articolo`),
  KEY `data_ora_creazione` (`data_ora_creazione`),
  KEY `sospeso` (`sospeso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `documenti` (
  `id_documento` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `titolo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data_ora_creazione` datetime NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_documento`),
  KEY `id_utente` (`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `interventi` (
  `id_intervento` int(11) NOT NULL AUTO_INCREMENT,
  `id_argomento` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_intervento_genitore` int(11) DEFAULT NULL,
  `chiave_ordinamento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `testo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `data_ora_creazione` datetime NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_intervento`),
  KEY `id_utente` (`id_utente`),
  KEY `id_articolo` (`id_argomento`),
  KEY `data_ora_creazione` (`data_ora_creazione`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `privilegi` (
  `id_privilegio` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_privilegio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `privilegi` VALUES
(1, 'backoffice'),
(101, 'utenti - visualizzazione'),
(102, 'utenti - inserimento'),
(103, 'utenti - modifica'),
(104, 'utenti - modifica propri'),
(105, 'utenti - eliminazione'),
(106, 'utenti - eliminazione propri'),
(201, 'articoli - visualizzazione'),
(202, 'articoli - inserimento'),
(203, 'articoli - inserimento propri'),
(204, 'articoli - modifica'),
(205, 'articoli - modifica propri'),
(206, 'articoli - eliminazione'),
(207, 'articoli - eliminazione propri'),
(301, 'commenti - visualizzazione'),
(302, 'commenti - inserimento'),
(303, 'commenti - inserimento propri'),
(304, 'commenti - modifica'),
(305, 'commenti - modifica propri'),
(306, 'commenti - eliminazione'),
(307, 'commenti - eliminazione propri'),
(401, 'argomenti - visualizzazione'),
(402, 'argomenti - inserimento'),
(403, 'argomenti - inserimento propri'),
(404, 'argomenti - modifica'),
(405, 'argomenti - modifica propri'),
(406, 'argomenti - eliminazione'),
(407, 'argomenti - eliminazione propri'),
(501, 'interventi - visualizzazione'),
(502, 'interventi - inserimento'),
(503, 'interventi - inserimento propri'),
(504, 'interventi - modifica'),
(505, 'interventi - modifica propri'),
(506, 'interventi - eliminazione'),
(507, 'interventi - eliminazione propri'),
(601, 'documenti - visualizzazione'),
(602, 'documenti - inserimento'),
(603, 'documenti - inserimento propri'),
(604, 'documenti - modifica'),
(605, 'documenti - modifica propri'),
(606, 'documenti - eliminazione'),
(607, 'documenti - eliminazione propri'),
(1001, 'categorie articoli - visualizzazione'),
(1002, 'categorie articoli - inserimento'),
(1003, 'categorie articoli - modifica'),
(1004, 'categorie articoli - eliminazione'),
(1101, 'categorie argomenti - visualizzazione'),
(1102, 'categorie argomenti - inserimento'),
(1103, 'categorie argomenti - modifica'),
(1104, 'categorie argomenti - eliminazione'),
(10001, 'html - base'),
(10002, 'html - esteso (danger!)');

CREATE TABLE IF NOT EXISTS `privilegi_utenti` (
  `id_privilegio_utente` int(11) NOT NULL AUTO_INCREMENT,
  `id_privilegio` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  PRIMARY KEY (`id_privilegio_utente`),
  KEY `id_utente` (`id_utente`),
  KEY `id_privilegio` (`id_privilegio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `utenti` (
  `id_utente` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cognome` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `tel` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cell` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descrizione` text COLLATE utf8_unicode_ci,
  `data_ora_creazione` datetime NOT NULL,
  `email_originale` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `supervisore` int(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci,
  `data_ora_ultima_modifica_pwd` datetime NOT NULL,
  `pwd_2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd_3` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_utente_modificatore` int(11) NOT NULL,
  `sospeso` int(1) NOT NULL DEFAULT '0',
  `data_ora_ultima_modifica` datetime DEFAULT NULL,
  `ip_ultima_modifica` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_utente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
