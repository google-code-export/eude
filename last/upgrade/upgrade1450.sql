--
-- @author Alex10336
-- Dernière modification: $Id: upgrade1450.sql 542 2010-07-23 09:39:39Z Alex10336 $
-- @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
-- @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
--
--
-- Fichier de MAJ SQL
-- depuis 1.4.5(.0)
--

ALTER TABLE `SQL_PREFIX_Coordonnee` ADD INDEX ( `udate` );
ALTER TABLE `SQL_PREFIX_Coordonnee` CHANGE `TYPE` `TYPE` INT( 2 ) NOT NULL DEFAULT '3';
ALTER TABLE `SQL_PREFIX_Coordonnee` ADD INDEX ( `TYPE` );

ALTER TABLE `SQL_PREFIX_Coordonnee_Joueurs` ADD INDEX ( `USER` );
ALTER TABLE `SQL_PREFIX_Coordonnee_Joueurs` ADD INDEX ( `EMPIRE` );
ALTER TABLE `SQL_PREFIX_Coordonnee_Joueurs` ADD INDEX ( `INFOS` );
ALTER TABLE `SQL_PREFIX_Coordonnee_Joueurs` ADD INDEX ( `batiments` ) ;
ALTER TABLE `SQL_PREFIX_Coordonnee_Joueurs` ADD INDEX ( `troop` ) ;

ALTER TABLE `SQL_PREFIX_Coordonnee_Planetes` ADD INDEX ( `water` ) ;