--
-- Fichier de MAJ SQL
-- depuis 1.4.1
--

CREATE TABLE `SQL_PREFIX_Config` (
`key` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`perms` BIGINT NOT NULL ,
`EmpireAllys` TEXT NOT NULL ,
`EmpireEnnemy` TEXT NOT NULL ) ENGINE = MYISAM ;
