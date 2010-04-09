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

ALTER TABLE `SQL_PREFIX_Coordonnee`
    ADD `water` VARCHAR( 3 ) NOT NULL DEFAULT '' AFTER `NOTE` ,
    ADD `troop` INT( 0 ) UNSIGNED NOT NULL AFTER `water`;

UPDATE `SQL_PREFIX_Coordonnee` SET `INFOS`=`EMPIRE` WHERE `TYPE`=6;
UPDATE `SQL_PREFIX_Coordonnee` SET `EMPIRE`='' WHERE `TYPE`=6;