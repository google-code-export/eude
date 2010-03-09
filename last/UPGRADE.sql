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
