-- ------------------------
-- Structure...
-- ------------------------

-- ------------------------
-- Valeurs par défaut ---
-- ------------------------

INSERT INTO `Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (1, 'Leader'   , 1, 0);
INSERT INTO `Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (2, 'Co-leader', 2, 1);
INSERT INTO `Grade` (`GradeId`, `Grade`, `Niveau`, `Rattachement`) VALUES (3, 'Membres'  , 3, 2);

-- Ne pas toucher, le tuto du forum n'est pas a jour, instructions dans le lisezmoi.txt
INSERT INTO `Membres` (`Joueur`, `Grade`) VALUES ('admin', 1);
INSERT INTO `Users` (`Login`, `Password`, `Permission`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', 600);


-- Ne pas décommenter la ligne suivante ;)
-- DROP TABLE `Coordonnee`, `Coordonnee_Planetes`, `Grade`, `itineraire`, `Log`, `Membres`, `Membres_log`, `ownuniverse`, `Users`;