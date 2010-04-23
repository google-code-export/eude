--
-- Fichier de MAJ SQL
-- depuis 1.4.1
--
ALTER TABLE `Membres`
    CHANGE `carte_prefs` `carte_prefs` VARCHAR( 30 ) NOT NULL DEFAULT '',
    CHANGE `Race` `Race` VARCHAR( 30 ) NOT NULL DEFAULT '',
    CHANGE `ship` `ship` VARCHAR( 50 ) NOT NULL DEFAULT '',
    CHANGE `Titre` `Titre` VARCHAR( 50 ) NOT NULL DEFAULT '',
    CHANGE `GameGrade` `GameGrade` VARCHAR( 50 ) NOT NULL DEFAULT '';

ALTER TABLE `Coordonnee`
    ADD `water` VARCHAR( 3 ) NOT NULL DEFAULT '' AFTER `NOTE` ,
    ADD `troop` INT( 10 ) NOT NULL DEFAULT '-1' AFTER `water`;

UPDATE `Coordonnee` SET `INFOS`=`EMPIRE` WHERE `TYPE`=6;
UPDATE `Coordonnee` SET `EMPIRE`='' WHERE `TYPE`=6;


CREATE TABLE IF NOT EXISTS `Config` (
  `key` varchar(25) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `Config` (`key`, `value`) VALUES('MapColors', 'a:3:{i:0;a:8:{i:0;s:7:"#232323";i:1;s:7:"#444444";i:2;s:7:"#3333FF";i:20;s:7:"#FF0080";i:21;s:7:"#00DD00";i:22;s:7:"#FF9933";i:24;s:7:"#FF9933";i:25;s:7:"#787878";}i:1;a:12:{i:0;s:7:"#232323";i:1;s:7:"#FF8000";i:2;s:7:"#008800";i:3;s:7:"#444444";i:4;s:7:"#3333FF";i:5;s:7:"#787878";i:6;s:7:"#00DD00";i:7;s:7:"#FF8000";i:11;s:7:"#00DD00";i:8;s:7:"#DD0000";i:9;s:7:"#FFFF00";i:10;s:7:"#FF00FF";}i:2;a:12:{i:0;s:7:"#232323";i:1;s:7:"#FF8000";i:2;s:7:"#008800";i:3;s:7:"#444444";i:4;s:7:"#444444";i:5;s:7:"#444444";i:6;s:7:"#444444";i:7;s:7:"#FF8000";i:11;s:7:"#444444";i:8;s:7:"#444444";i:9;s:7:"#444444";i:10;s:7:"#FF00FF";}}');
INSERT INTO `Config` (`key`, `value`) VALUES('EmpireAllys', 'a:0:{}');
INSERT INTO `Config` (`key`, `value`) VALUES('EmpireEnnemy', 'a:0:{}');
INSERT INTO `Config` (`key`, `value`) VALUES('wormhole_cleaning', 'a:2:{s:7:"enabled";b:0;s:7:"lastrun";i:0;}');
INSERT INTO `Config` (`key`, `value`) VALUES('perms', 'a:27:{s:13:"MEMBRES_ADMIN";s:3:"600";s:17:"MEMBRES_ADMIN_LOG";s:3:"600";s:23:"MEMBRES_ADMIN_MAP_COLOR";s:3:"600";s:11:"MEMBRES_NEW";s:3:"500";s:12:"MEMBRES_EDIT";s:3:"500";s:15:"MEMBRES_NEWPASS";s:3:"600";s:14:"MEMBRES_DELETE";s:3:"600";s:13:"MEMBRES_STATS";s:3:"210";s:18:"MEMBRES_HIERARCHIE";s:3:"200";s:5:"CARTE";s:3:"200";s:12:"CARTE_SEARCH";s:3:"210";s:12:"CARTE_JOUEUR";s:3:"210";s:16:"CARTE_SHOWEMPIRE";s:3:"210";s:5:"PERSO";s:3:"100";s:14:"PERSO_RESEARCH";s:3:"100";s:17:"PERSO_OWNUNIVERSE";s:3:"100";s:26:"PERSO_OWNUNIVERSE_READONLY";s:3:"600";s:12:"CARTOGRAPHIE";s:3:"200";s:21:"CARTOGRAPHIE_ASTEROID";s:3:"200";s:20:"CARTOGRAPHIE_PLANETS";s:3:"200";s:20:"CARTOGRAPHIE_PLAYERS";s:3:"200";s:19:"CARTOGRAPHIE_SEARCH";s:3:"210";s:19:"CARTOGRAPHIE_DELETE";s:3:"600";s:17:"CARTOGRAPHIE_EDIT";s:3:"300";s:19:"CARTOGRAPHIE_GREASE";s:3:"200";s:6:"in_dev";s:5:"32767";s:13:"addons_sample";s:5:"32767";}');

-- Pour ceux qui utilise le svn
-- UPDATE `SQL_PREFIX_Coordonnee` SET `troop`=-1 WHERE `troop`=0;