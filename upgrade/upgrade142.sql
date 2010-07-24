--
-- @author Alex10336
-- Dernière modification: $Id$
-- @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
-- @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
--
--
-- Fichier de MAJ SQL
-- depuis 1.4.2
--

DROP TABLE `TEST_Membres_log`;
ALTER TABLE `TEST_Coordonnee` CHANGE `water` `water` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `TEST_Coordonnee` CHANGE `water` `water` TINYINT( 3 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `TEST_Coordonnee` ADD `batiments` TINYINT( 3 ) UNSIGNED NULL DEFAULT NULL AFTER `water`;
ALTER TABLE `TEST_Coordonnee` CHANGE `troop` `troop` INT( 10 ) NULL DEFAULT '-1';
UPDATE `TEST_Coordonnee` SET `troop` = NULL WHERE `troop` =  -1;
ALTER TABLE `TEST_Coordonnee` CHANGE `troop` `troop` INT( 10 )  UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `TEST_Coordonnee` ADD  `troop_date` INT( 10 ) NOT NULL AFTER  `troop`;

 
ALTER TABLE `TEST_Grade` CHANGE `Niveau` `Niveau` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Grade` CHANGE `Rattachement` `Rattachement` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_itineraire` CHANGE `Start` `Start` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_itineraire` CHANGE `End` `End` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Log` CHANGE `DATE` `DATE` TIMESTAMP NOT NULL;
ALTER TABLE `TEST_Log` CHANGE `LOGIN` `LOGIN` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Points` `Points` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Date` `Date` TIMESTAMP NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Economie` `Economie` SMALLINT( 3 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Commerce` `Commerce` SMALLINT( 3 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Recherche` `Recherche` SMALLINT( 3 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Combat` `Combat` SMALLINT( 3 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Construction` `Construction` SMALLINT( 3 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Navigation` `Navigation` SMALLINT( 3 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `Grade` `Grade` SMALLINT( 5 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `pts_architecte` `pts_architecte` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `pts_mineur` `pts_mineur` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `pts_science` `pts_science` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `pts_commercant` `pts_commercant` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `pts_amiral` `pts_amiral` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Membres` CHANGE `pts_guerrier` `pts_guerrier` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Users` CHANGE `Permission` `Permission` SMALLINT( 4 ) UNSIGNED NOT NULL DEFAULT '0';
-- 
DELETE FROM `TEST_Coordonnee` WHERE `inactif`=1;
ALTER TABLE `TEST_Coordonnee` DROP `INACTIF`;


ALTER TABLE `TEST_Coordonnee` ADD `udate` INT( 10 ) UNSIGNED NOT NULL;
UPDATE `TEST_Coordonnee` SET `udate`=UNIX_TIMESTAMP(`DATE`);
ALTER TABLE  `TEST_Coordonnee` DROP `DATE`;
ALTER TABLE `TEST_Coordonnee` ADD `troop_udate` INT( 10 ) UNSIGNED NULL;
UPDATE `TEST_Coordonnee` SET `troop_udate`=UNIX_TIMESTAMP(`troop_date`);
ALTER TABLE  `TEST_Coordonnee` DROP `troop_date`;
ALTER TABLE  `TEST_Coordonnee` CHANGE `troop_udate` `troop_date` INT( 10 ) UNSIGNED NULL;


INSERT INTO `TEST_Config` (`key`, `value`) VALUES('config', 'a:9:{s:7:"version";s:5:"1.4.5";s:9:"ForumLink";s:31:"https://code.google.com/p/eude/";s:11:"CanRegister";s:1:"1";s:12:"DefaultGrade";s:1:"3";s:8:"MyEmpire";s:0:"";s:17:"Parcours_Max_Time";i:0;s:16:"Parcours_Nearest";i:5;s:8:"eude_srv";s:12:"australis.fr";s:6:"closed";s:1:"0";}');

CREATE TABLE `TEST_troops_attack` (
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



CREATE TABLE `TEST_troops_pillage` (
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


-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
-- -- -- -- -- -- -- --  Partie split =)  -- -- -- -- -- -- -- -- -- -- -- -- --
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

CREATE TABLE `TEST_Coordonnee_Joueurs` as SELECT `id` `jID`,`USER`,`EMPIRE`,`INFOS`,`batiments`,`troop`,`troop_date` FROM `TEST_Coordonnee`;
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD PRIMARY KEY ( `jID` );
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `batiments` );
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `troop` );


CREATE TABLE `TEST_Coordonnee_Planetes2` as
SELECT IFNULL(p.pID, c.ID) as pID, Titane,Cuivre,Fer,Aluminium,Mercure,Silicium,Uranium,Krypton,Azote,Hydrogene,c.`water` FROM `TEST_Coordonnee` c
 LEFT JOIN `TEST_Coordonnee_Planetes` p on (c.`ID`=p.`pID`);
DROP TABLE `TEST_Coordonnee_Planetes`;
RENAME TABLE `TEST_Coordonnee_Planetes2` TO `TEST_Coordonnee_Planetes`;

-- omfg
ALTER TABLE `TEST_Coordonnee_Planetes` CHANGE `pID` `pID` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE `TEST_Coordonnee_Planetes` ADD PRIMARY KEY ( `pID` );
--

ALTER TABLE `TEST_Coordonnee` DROP `USER`,DROP `EMPIRE`,DROP `INFOS`,DROP `water`,DROP `batiments`,DROP `troop`,DROP `troop_date`;

-- Version 1.4.5.1
ALTER TABLE `TEST_Coordonnee` ADD INDEX ( `udate` );
ALTER TABLE `TEST_Coordonnee` CHANGE `TYPE` `TYPE` TINYINT( 2 ) NOT NULL DEFAULT '3';
ALTER TABLE `TEST_Coordonnee` ADD INDEX ( `TYPE` );

ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `USER` );
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `EMPIRE` );
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `INFOS` );
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `batiments` ) ;
ALTER TABLE `TEST_Coordonnee_Joueurs` ADD INDEX ( `troop` ) ;

ALTER TABLE `TEST_Coordonnee_Planetes` ADD INDEX ( `water` ) ;