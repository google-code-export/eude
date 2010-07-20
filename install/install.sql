--
-- @author Alex10336
-- Dernière modification: $Id$
-- @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
-- @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
--
-- ------------------------
-- Structure...
-- ------------------------

--
-- Structure de la table `SQL_PREFIX_Config`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Config` (
  `key` varchar(25) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Coordonnee`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Coordonnee` (
  `ID` bigint(20) NOT NULL auto_increment,
  `TYPE` int(11) NOT NULL default '3',
  `POSIN` varchar(5) NOT NULL,
  `POSOUT` varchar(5) NOT NULL,
  `COORDET` varchar(10) NOT NULL,
  `COORDETOUT` varchar(10) NOT NULL,
  `NOTE` varchar(100) NOT NULL,
  `UTILISATEUR` varchar(30) NOT NULL,
  `udate` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `coords` (`POSIN`,`COORDET`),
  KEY `POSIN` (`POSIN`),
  KEY `POSOUT` (`POSOUT`),
  KEY `COORDET` (`COORDET`),
  KEY `COORDETOUT` (`COORDETOUT`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Coordonnee_Joueurs`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Coordonnee_Joueurs` (
  `jID` bigint(20) NOT NULL default '0',
  `USER` varchar(30) NOT NULL,
  `EMPIRE` varchar(100) NOT NULL,
  `INFOS` varchar(100) NOT NULL,
  `batiments` tinyint(3) unsigned default NULL,
  `troop` int(10) unsigned default NULL,
  `troop_date` int(10) unsigned default NULL,
  PRIMARY KEY  (`jID`),
  KEY `batiments` (`batiments`),
  KEY `troop` (`troop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Coordonnee_Planetes`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Coordonnee_Planetes` (
  `pID` int(10) unsigned NOT NULL,
  `Titane` varchar(50),
  `Cuivre` varchar(50),
  `Fer` varchar(50),
  `Aluminium` varchar(50),
  `Mercure` varchar(50),
  `Silicium` varchar(50),
  `Uranium` varchar(50),
  `Krypton` varchar(50),
  `Azote` varchar(50),
  `Hydrogene` varchar(50),
  `water` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`pID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Grade`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Grade` (
  `GradeId` int(11) NOT NULL auto_increment,
  `Grade` varchar(50) NOT NULL,
  `Niveau` smallint(5) unsigned NOT NULL,
  `Rattachement` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`GradeId`),
  KEY `Niveau` (`Niveau`,`Rattachement`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_itineraire`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_itineraire` (
  `ID` int(11) NOT NULL auto_increment,
  `Joueur` varchar(30) NOT NULL,
  `Flotte` varchar(50) NOT NULL,
  `Start` smallint(5) unsigned NOT NULL,
  `End` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `Joueur` (`Joueur`,`Flotte`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Log`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Log` (
  `ID` int(11) NOT NULL auto_increment,
  `DATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `LOGIN` text NOT NULL,
  `IP` varchar(30) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Membres`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Membres` (
  `Joueur` varchar(30) NOT NULL,
  `carte_prefs` varchar(30) NOT NULL default '',
  `Points` int(10) unsigned NOT NULL,
  `Date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Economie` smallint(3) unsigned NOT NULL,
  `Commerce` smallint(3) unsigned NOT NULL,
  `Recherche` smallint(3) unsigned NOT NULL,
  `Combat` smallint(3) unsigned NOT NULL,
  `Construction` smallint(3) unsigned NOT NULL,
  `Navigation` smallint(3) unsigned NOT NULL,
  `Grade` smallint(5) unsigned NOT NULL,
  `Race` varchar(30) NOT NULL default '',
  `ship` varchar(50) NOT NULL default '',
  `Titre` varchar(50) NOT NULL default '',
  `GameGrade` varchar(50) NOT NULL default '',
  `pts_architecte` int(10) unsigned NOT NULL,
  `pts_mineur` int(10) unsigned NOT NULL,
  `pts_science` int(10) unsigned NOT NULL,
  `pts_commercant` int(10) unsigned NOT NULL,
  `pts_amiral` int(10) unsigned NOT NULL,
  `pts_guerrier` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`Joueur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_ownuniverse`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_ownuniverse` (
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
-- Structure de la table `SQL_PREFIX_troops_attack`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_troops_attack` (
  `ID` int(11) NOT NULL auto_increment,
  `type` enum('defender','attacker') NOT NULL,
  `nb_assault` int(11) NOT NULL,
  `players_attack` text NOT NULL,
  `players_defender` text NOT NULL,
  `players_pertes` text NOT NULL,
  `when` int(11) NOT NULL,
  `coords_ss` varchar(5) NOT NULL,
  `coords_3p` varchar(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `coords` (`coords_ss`,`coords_3p`),
  FULLTEXT KEY `players_attack` (`players_attack`),
  FULLTEXT KEY `players_defender` (`players_defender`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_troops_pillage`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_troops_pillage` (
  `pid` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL,
  `Player` varchar(30) NOT NULL,
  `ress0` int(11) NOT NULL,
  `ress1` int(11) NOT NULL,
  `ress2` int(11) NOT NULL,
  `ress3` int(11) NOT NULL,
  `ress4` int(11) NOT NULL,
  `ress5` int(11) NOT NULL,
  `ress6` int(11) NOT NULL,
  `ress7` int(11) NOT NULL,
  `ress8` int(11) NOT NULL,
  `ress9` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY  (`pid`),
  KEY `mid` (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `SQL_PREFIX_Users`
--

CREATE TABLE IF NOT EXISTS `SQL_PREFIX_Users` (
  `Login` varchar(30) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Permission` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------
-- Valeurs par défaut ---
-- ------------------------
INSERT INTO `SQL_PREFIX_Config` (`key`, `value`) VALUES('config', 'a:9:{s:7:"version";s:5:"1.4.5";s:9:"ForumLink";s:%%boardnamelen%%:"%%boardname%%";s:11:"CanRegister";s:1:"1";s:12:"DefaultGrade";s:1:"3";s:8:"MyEmpire";s:%%empirenamelen%%:"%%empirename%%";s:17:"Parcours_Max_Time";i:0;s:16:"Parcours_Nearest";i:5;s:8:"eude_srv";s:12:"australis.fr";s:6:"closed";s:1:"0";}');
INSERT INTO `SQL_PREFIX_Config` (`key`, `value`) VALUES('MapColors', 'a:3:{i:0;a:8:{i:0;s:7:"#232323";i:1;s:7:"#444444";i:2;s:7:"#3333FF";i:20;s:7:"#FF0080";i:21;s:7:"#00DD00";i:22;s:7:"#FF9933";i:24;s:7:"#FF9933";i:25;s:7:"#787878";}i:1;a:12:{i:0;s:7:"#232323";i:1;s:7:"#FF8000";i:2;s:7:"#008800";i:3;s:7:"#444444";i:4;s:7:"#3333FF";i:5;s:7:"#787878";i:6;s:7:"#00DD00";i:7;s:7:"#FF8000";i:11;s:7:"#00DD00";i:8;s:7:"#DD0000";i:9;s:7:"#FFFF00";i:10;s:7:"#FF00FF";}i:2;a:12:{i:0;s:7:"#232323";i:1;s:7:"#FF8000";i:2;s:7:"#008800";i:3;s:7:"#444444";i:4;s:7:"#444444";i:5;s:7:"#444444";i:6;s:7:"#444444";i:7;s:7:"#FF8000";i:11;s:7:"#444444";i:8;s:7:"#444444";i:9;s:7:"#444444";i:10;s:7:"#FF00FF";}}');
INSERT INTO `SQL_PREFIX_Config` (`key`, `value`) VALUES('EmpireAllys', 'a:0:{}');
INSERT INTO `SQL_PREFIX_Config` (`key`, `value`) VALUES('EmpireEnnemy', 'a:0:{}');
INSERT INTO `SQL_PREFIX_Config` (`key`, `value`) VALUES('wormhole_cleaning', 'a:2:{s:7:"enabled";b:0;s:7:"lastrun";i:0;}');
INSERT INTO `SQL_PREFIX_Config` (`key`, `value`) VALUES('perms', 'a:29:{s:13:"MEMBRES_ADMIN";s:3:"600";s:17:"MEMBRES_ADMIN_LOG";s:3:"600";s:23:"MEMBRES_ADMIN_MAP_COLOR";s:3:"600";s:11:"MEMBRES_NEW";s:3:"500";s:12:"MEMBRES_EDIT";s:3:"500";s:15:"MEMBRES_NEWPASS";s:3:"600";s:14:"MEMBRES_DELETE";s:3:"600";s:13:"MEMBRES_STATS";s:3:"210";s:18:"MEMBRES_HIERARCHIE";s:3:"200";s:5:"CARTE";s:3:"200";s:12:"CARTE_SEARCH";s:3:"210";s:12:"CARTE_JOUEUR";s:3:"210";s:16:"CARTE_SHOWEMPIRE";s:3:"210";s:5:"PERSO";s:3:"100";s:14:"PERSO_RESEARCH";s:3:"100";s:17:"PERSO_OWNUNIVERSE";s:3:"100";s:26:"PERSO_OWNUNIVERSE_READONLY";s:3:"600";s:19:"PERSO_TROOPS_BATTLE";s:3:"200";s:12:"CARTOGRAPHIE";s:3:"200";s:21:"CARTOGRAPHIE_ASTEROID";s:3:"200";s:20:"CARTOGRAPHIE_PLANETS";s:3:"200";s:20:"CARTOGRAPHIE_PLAYERS";s:3:"200";s:16:"CARTOGRAPHIE_PNJ";s:3:"200";s:19:"CARTOGRAPHIE_SEARCH";s:3:"210";s:19:"CARTOGRAPHIE_DELETE";s:3:"600";s:17:"CARTOGRAPHIE_EDIT";s:3:"300";s:19:"CARTOGRAPHIE_GREASE";s:3:"200";s:13:"EMPIRE_GREASE";s:3:"200";s:13:"addons_sample";s:5:"32767";}');

INSERT INTO `SQL_PREFIX_Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (1, 'Leader'   , 1, 0);
INSERT INTO `SQL_PREFIX_Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (2, 'Co-leader', 2, 1);
INSERT INTO `SQL_PREFIX_Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (3, 'Membres'  , 3, 2);

-- Ne pas toucher, le tuto du forum n'est pas à jour, instructions dans le lisezmoi.txt ou sur http://eude.googlecode.com/
INSERT INTO `SQL_PREFIX_Membres` (`Joueur`, `Grade`) VALUES ('%%username%%', 1);
INSERT INTO `SQL_PREFIX_Users` (`Login`, `Password`, `Permission`) VALUES ('%%username%%', md5('%%password%%'), 600);

-- Ne pas décommenter la ligne suivante ;)
-- DROP TABLE `SQL_PREFIX_Config`, `SQL_PREFIX_Coordonnee`, `SQL_PREFIX_Coordonnee_joueurs`, `SQL_PREFIX_Coordonnee_Planetes`, `SQL_PREFIX_Grade`, `SQL_PREFIX_itineraire`, `SQL_PREFIX_Log`, `SQL_PREFIX_Membres`, `SQL_PREFIX_ownuniverse`, `SQL_PREFIX_troops_attack`, `SQL_PREFIX_troops_pillage`, `SQL_PREFIX_Users`;