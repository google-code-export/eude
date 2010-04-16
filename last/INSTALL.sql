-- ------------------------
-- Structure...
-- ------------------------
-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 16 Avril 2010 à 16:33
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.4-2ubuntu5.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `eu2`
--

-- --------------------------------------------------------

--
-- Structure de la table `Config`
--

CREATE TABLE IF NOT EXISTS `Config` (
  `key` varchar(25) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Coordonnee`
--

CREATE TABLE IF NOT EXISTS `Coordonnee` (
  `ID` bigint(20) NOT NULL auto_increment,
  `TYPE` int(11) NOT NULL default '3',
  `POSIN` varchar(5) NOT NULL,
  `POSOUT` varchar(5) NOT NULL,
  `COORDET` varchar(10) NOT NULL,
  `COORDETOUT` varchar(10) NOT NULL,
  `USER` varchar(30) NOT NULL,
  `EMPIRE` varchar(100) NOT NULL,
  `INFOS` varchar(100) NOT NULL,
  `DATE` datetime NOT NULL,
  `NOTE` varchar(100) NOT NULL,
  `water` varchar(3) NOT NULL default '',
  `troop` int(10) unsigned NOT NULL,
  `INACTIF` tinyint(1) NOT NULL default '0',
  `UTILISATEUR` varchar(30) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `POSIN` (`POSIN`),
  KEY `POSOUT` (`POSOUT`),
  KEY `COORDET` (`COORDET`),
  KEY `COORDETOUT` (`COORDETOUT`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `Coordonnee_Planetes`
--

CREATE TABLE IF NOT EXISTS `Coordonnee_Planetes` (
  `pID` int(10) unsigned NOT NULL,
  `Titane` varchar(50) NOT NULL,
  `Cuivre` varchar(50) NOT NULL,
  `Fer` varchar(50) NOT NULL,
  `Aluminium` varchar(50) NOT NULL,
  `Mercure` varchar(50) NOT NULL,
  `Silicium` varchar(50) NOT NULL,
  `Uranium` varchar(50) NOT NULL,
  `Krypton` varchar(50) NOT NULL,
  `Azote` varchar(50) NOT NULL,
  `Hydrogene` varchar(50) NOT NULL,
  PRIMARY KEY  (`pID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Grade`
--

CREATE TABLE IF NOT EXISTS `Grade` (
  `GradeId` int(11) NOT NULL auto_increment,
  `Grade` varchar(50) NOT NULL,
  `Niveau` int(11) NOT NULL,
  `Rattachement` int(11) NOT NULL,
  PRIMARY KEY  (`GradeId`),
  KEY `Niveau` (`Niveau`,`Rattachement`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `itineraire`
--

CREATE TABLE IF NOT EXISTS `itineraire` (
  `ID` int(11) NOT NULL auto_increment,
  `Joueur` varchar(30) NOT NULL,
  `Flotte` varchar(50) NOT NULL,
  `Start` varchar(5) NOT NULL,
  `End` varchar(5) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `Joueur` (`Joueur`,`Flotte`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `ID` int(11) NOT NULL auto_increment,
  `DATE` datetime NOT NULL,
  `LOGIN` varchar(30) NOT NULL,
  `IP` varchar(30) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Membres`
--

CREATE TABLE IF NOT EXISTS `Membres` (
  `Joueur` varchar(30) NOT NULL,
  `carte_prefs` varchar(30) NOT NULL default '',
  `Points` int(11) NOT NULL,
  `Don` int(11) NOT NULL,
  `Date` datetime NOT NULL,
  `Economie` smallint(8) NOT NULL,
  `Commerce` smallint(8) NOT NULL,
  `Recherche` smallint(8) NOT NULL,
  `Combat` smallint(8) NOT NULL,
  `Construction` smallint(8) NOT NULL,
  `Navigation` smallint(8) NOT NULL,
  `Grade` smallint(8) NOT NULL,
  `Race` varchar(30) NOT NULL default '',
  `ship` varchar(50) NOT NULL default '',
  `Titre` varchar(50) NOT NULL default '',
  `GameGrade` varchar(50) NOT NULL default '',
  `pts_architecte` int(10) NOT NULL,
  `pts_mineur` int(10) NOT NULL,
  `pts_science` int(10) NOT NULL,
  `pts_commercant` int(10) NOT NULL,
  `pts_amiral` int(10) NOT NULL,
  `pts_guerrier` int(10) NOT NULL,
  PRIMARY KEY  (`Joueur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Membres_log`
--

CREATE TABLE IF NOT EXISTS `Membres_log` (
  `ID` int(11) NOT NULL auto_increment,
  `Joueur` varchar(30) NOT NULL,
  `Date` datetime NOT NULL,
  `Points` int(11) NOT NULL,
  `Dons` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ownuniverse`
--

CREATE TABLE IF NOT EXISTS `ownuniverse` (
  `UTILISATEUR` varchar(30) NOT NULL default '',
  `planet0` varchar(50) NOT NULL,
  `coord0` varchar(13) NOT NULL,
  `data0` blob NOT NULL,
  `ress0` blob NOT NULL,
  `planet1` varchar(50) NOT NULL,
  `coord1` varchar(13) NOT NULL,
  `data1` blob NOT NULL,
  `ress1` blob NOT NULL,
  `planet2` varchar(50) NOT NULL,
  `coord2` varchar(13) NOT NULL,
  `data2` blob NOT NULL,
  `ress2` blob NOT NULL,
  `planet3` varchar(50) NOT NULL,
  `coord3` varchar(13) NOT NULL,
  `data3` blob NOT NULL,
  `ress3` blob NOT NULL,
  `planet4` varchar(50) NOT NULL,
  `coord4` varchar(13) NOT NULL,
  `data4` blob NOT NULL,
  `ress4` blob NOT NULL,
  PRIMARY KEY  (`UTILISATEUR`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `Login` varchar(30) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Permission` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`Login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------
-- Valeurs par défaut ---
-- ------------------------

INSERT INTO `Config` (`key`, `value`) VALUES('MapColors', 'a:3:{i:0;a:8:{i:0;s:7:"#232323";i:1;s:7:"#444444";i:2;s:7:"#3333FF";i:20;s:7:"#FF0080";i:21;s:7:"#00DD00";i:22;s:7:"#FF9933";i:24;s:7:"#FF9933";i:25;s:7:"#787878";}i:1;a:12:{i:0;s:7:"#232323";i:1;s:7:"#FF8000";i:2;s:7:"#008800";i:3;s:7:"#444444";i:4;s:7:"#3333FF";i:5;s:7:"#787878";i:6;s:7:"#00DD00";i:7;s:7:"#FF8000";i:11;s:7:"#00DD00";i:8;s:7:"#DD0000";i:9;s:7:"#FFFF00";i:10;s:7:"#FF00FF";}i:2;a:12:{i:0;s:7:"#232323";i:1;s:7:"#FF8000";i:2;s:7:"#008800";i:3;s:7:"#444444";i:4;s:7:"#444444";i:5;s:7:"#444444";i:6;s:7:"#444444";i:7;s:7:"#FF8000";i:11;s:7:"#444444";i:8;s:7:"#444444";i:9;s:7:"#444444";i:10;s:7:"#FF00FF";}}');
INSERT INTO `Config` (`key`, `value`) VALUES('EmpireAllys', 'a:0:{}');
INSERT INTO `Config` (`key`, `value`) VALUES('EmpireEnnemy', 'a:0:{}');
INSERT INTO `Config` (`key`, `value`) VALUES('wormhole_cleaning', 'a:2:{s:7:"enabled";b:0;s:7:"lastrun";i:0;}');
INSERT INTO `Config` (`key`, `value`) VALUES('perms', 'a:27:{s:13:"MEMBRES_ADMIN";s:3:"600";s:17:"MEMBRES_ADMIN_LOG";s:3:"600";s:23:"MEMBRES_ADMIN_MAP_COLOR";s:3:"600";s:11:"MEMBRES_NEW";s:3:"500";s:12:"MEMBRES_EDIT";s:3:"500";s:15:"MEMBRES_NEWPASS";s:3:"600";s:14:"MEMBRES_DELETE";s:3:"600";s:13:"MEMBRES_STATS";s:3:"210";s:18:"MEMBRES_HIERARCHIE";s:3:"200";s:5:"CARTE";s:3:"200";s:12:"CARTE_SEARCH";s:3:"210";s:12:"CARTE_JOUEUR";s:3:"210";s:16:"CARTE_SHOWEMPIRE";s:3:"210";s:5:"PERSO";s:3:"100";s:14:"PERSO_RESEARCH";s:3:"100";s:17:"PERSO_OWNUNIVERSE";s:3:"100";s:26:"PERSO_OWNUNIVERSE_READONLY";s:3:"600";s:12:"CARTOGRAPHIE";s:3:"200";s:21:"CARTOGRAPHIE_ASTEROID";s:3:"200";s:20:"CARTOGRAPHIE_PLANETS";s:3:"200";s:20:"CARTOGRAPHIE_PLAYERS";s:3:"200";s:19:"CARTOGRAPHIE_SEARCH";s:3:"210";s:19:"CARTOGRAPHIE_DELETE";s:3:"600";s:17:"CARTOGRAPHIE_EDIT";s:3:"300";s:19:"CARTOGRAPHIE_GREASE";s:3:"200";s:6:"in_dev";s:5:"32767";s:13:"addons_sample";s:5:"32767";}');

INSERT INTO `Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (1, 'Leader'   , 1, 0);
INSERT INTO `Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (2, 'Co-leader', 2, 1);
INSERT INTO `Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (3, 'Membres'  , 3, 2);

-- Ne pas toucher, le tuto du forum n'est pas à jour, instructions dans le lisezmoi.txt ou sur http://eude.googlecode.com/
INSERT INTO `Membres` (`Joueur`, `Grade`) VALUES ('admin', 1);
INSERT INTO `Users` (`Login`, `Password`, `Permission`) VALUES ('admin', md5('admin'), 600);

-- Ne pas décommenter la ligne suivante ;)
-- DROP TABLE `Coordonnee`, `Coordonnee_Planetes`, `Grade`, `itineraire`, `Log`, `Membres`, `Membres_log`, `ownuniverse`, `Users`;