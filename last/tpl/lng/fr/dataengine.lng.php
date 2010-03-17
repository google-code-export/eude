<?php
/*
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
*/

$lng = array();
$lng['Titane'] = 'Titane';
$lng['Cuivre'] = 'Cuivre';
$lng['Fer'] = 'Fer';
$lng['Aluminium'] = 'Aluminium';
$lng['Mercure'] = 'Mercure';
$lng['Silicium'] = 'Silicium';
$lng['Uranium'] = 'Uranium';
$lng['Krypton'] = 'Krypton';
$lng['Azote'] = 'Azote';
$lng['Hydrogene'] = 'Hydrogène';

$lng['batiments']['control']       = 'Centre de contrôle';
$lng['batiments']['communication'] = 'Centre de communication';
$lng['batiments']['university']    = 'Université';
$lng['batiments']['technology']    = 'Centre de recherches';
$lng['batiments']['gouv']          = 'Centre gouvernemental';
$lng['batiments']['defense']       = 'Caserne';
$lng['batiments']['shipyard']      = 'Chantier spatial';
$lng['batiments']['spacedock']     = 'Hangar de maintenance';
$lng['batiments']['bunker']        = 'Bunker';
$lng['batiments']['tradepost']     = 'Poste de commerce';
$lng['batiments']['ressource']     = 'Complexe d\'extraction';

$cxx = array();
$cxx[] = 'Partie administrative';
$cxx['MEMBRES_ADMIN'] = 'Page admin';
$cxx['MEMBRES_ADMIN_LOG'] = 'Log des connexions';
$cxx['MEMBRES_ADMIN_MAP_COLOR'] = 'Modification des couleurs de la carte';
$cxx['MEMBRES_NEW'] = 'Ajout membre (inclus les grades)';
$cxx['MEMBRES_EDIT'] = 'Modification membre';
$cxx['MEMBRES_NEWPASS'] = 'Changer pass';
$cxx['MEMBRES_DELETE'] = 'Supprimer membre';
$cxx['MEMBRES_STATS'] = 'Affichage stats';
$cxx['MEMBRES_HIERARCHIE'] = 'Membres hiérarchie';
$cxx[] = 'Carte';
$cxx['CARTE'] = 'Page Carte';
$cxx['CARTE_SEARCH'] = 'Recherche';
$cxx['CARTE_JOUEUR'] = 'Affichage des Joueurs';
$cxx['CARTE_SHOWEMPIRE'] = 'Affichage des Joueurs de l\'empire';
$cxx[] = 'Ma fiche & co';
$cxx['PERSO'] = 'Page Mafiche';
$cxx['PERSO_RESEARCH'] = 'Page recherche';
$cxx['PERSO_OWNUNIVERSE'] = 'Page production';
$cxx['PERSO_OWNUNIVERSE_READONLY'] = 'Page production (mode lecture seule)';
$cxx[] = 'Cartographie';
$cxx['CARTOGRAPHIE'] = 'Page Cartographie';
$cxx['CARTOGRAPHIE_ASTEROID'] = 'Ajout Astéroïdes';
$cxx['CARTOGRAPHIE_PLANETS'] = 'Ajout planètes';
$cxx['CARTOGRAPHIE_PLAYERS'] = 'Ajout Joueur/Flottes PNJ';
$cxx['CARTOGRAPHIE_SEARCH'] = 'Fonction recherche';
$cxx['CARTOGRAPHIE_GREASE'] = 'Utilisation "GreaseMonkey"';
$cxx[] = 'Addons:';
$lng['cxx'] = $cxx;

$lng['axx'] = array(
                AXX_VALIDATING	=>'Non-validé',
                AXX_GUEST	=>'Invité',
                AXX_MEMBER	=>'Membre',
                AXX_POWERMEMBER	=>'Membre+',
                AXX_MODO	=>'Modérateur',
                AXX_SUPMODO	=>'Super-Modérateur',
                AXX_ADMIN	=>'Administrateur',
                AXX_ROOTADMIN	=>'Super-Administrateur',
                AXX_DISABLED	=>'Désactivé'
        );

$lng['minimalpermsneeded'] = 'Permission minimale manquante (<b>%s</b> ou supérieur)';
$lng['nopermsanddie'] = 'Permission minimale manquante ou option désactivée.';