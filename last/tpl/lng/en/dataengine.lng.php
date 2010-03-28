<?php
/*
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
*/

$lng = array();
$lng['Titane'] = 'Titanium';
$lng['Cuivre'] = 'Copper';
$lng['Fer'] = 'Iron';
$lng['Aluminium'] = 'Aluminium';
$lng['Mercure'] = 'Mercury';
$lng['Silicium'] = 'Silicon';
$lng['Uranium'] = 'Uranium';
$lng['Krypton'] = 'Krypton';
$lng['Azote'] = 'Nitrogen';
$lng['Hydrogene'] = 'Hydrogen';

$lng['batiments']['control']       = 'Control Center';
$lng['batiments']['communication'] = 'Communication Center';
$lng['batiments']['university']    = 'University';
$lng['batiments']['technology']    = 'Research Facility';
$lng['batiments']['gouv']          = 'Government Building';
$lng['batiments']['defense']       = 'Barracks';
$lng['batiments']['shipyard']      = 'Shipyard';
$lng['batiments']['spacedock']     = 'Dock';
$lng['batiments']['bunker']        = 'Bunker';
$lng['batiments']['tradepost']     = 'Trading Post';
$lng['batiments']['ressource']     = 'Resource Facilities';

$lng['shiplist'][]='Probe';
$lng['shiplist'][]='Lancer';
$lng['shiplist'][]='Chasseur';
$lng['shiplist'][]='Corvette';
$lng['shiplist'][]='Frégate';
$lng['shiplist'][]='Cargo';
$lng['shiplist'][]='Croiseur';
$lng['shiplist'][]='Intercepteur';
$lng['shiplist'][]='Croiseur interstellaire';
$lng['shiplist'][]='Sentinelle';
$lng['shiplist'][]='Vaisseau de guerre';
$lng['shiplist'][]='Centaure';
$lng['shiplist'][]='Minotaure';
$lng['shiplist'][]='Transporteur';
$lng['shiplist'][]='Cerbère';
$lng['shiplist'][]='Kraken';
$lng['shiplist'][]='Hadès';
$lng['shiplist'][]='Léviathan';
$lng['shiplist'][]='Transporteur intergalactique';
$lng['shiplist'][]='Station de guerre';
$lng['shiplist'][]='SG Armaggedon';

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

// Types pris en charge !
$lng['types'] = array();
$lng['types']['dropdown'] = array(); // alias cctype
$lng['types']['dropdown'][0] = 'Joueur';
$lng['types']['dropdown'][3] = 'Alliés';
$lng['types']['dropdown'][5] = 'Ennemis';
$lng['types']['dropdown'][6] = 'Flotte PNJ';
$lng['types']['dropdown'][1] = 'Vortex';
$lng['types']['dropdown'][2] = 'Planète';
$lng['types']['dropdown'][4] = 'Astéroïdes';

$lng['types']['imgurl'] = array(); // alias ccimg (xml/cartedetail.php)
$lng['types']['imgurl'][0] = IMAGES_URL.'Joueur.jpg';
$lng['types']['imgurl'][3] = IMAGES_URL.'fleet_own.gif';
$lng['types']['imgurl'][5] = IMAGES_URL.'fleet_enemy.gif';
$lng['types']['imgurl'][6] = IMAGES_URL.'fleet_npc.gif';
$lng['types']['imgurl'][1] = IMAGES_URL.'Vortex.jpg';
$lng['types']['imgurl'][2] = IMAGES_URL.'Planete.jpg';
$lng['types']['imgurl'][4] = IMAGES_URL.'Asteroide.jpg';

$lng['types']['string'] = array(); // alias stype (utilisé pour les messages cartographie)
$lng['types']['string'][0] = 'Joueur';
$lng['types']['string'][3] = 'Alliés';
$lng['types']['string'][5] = 'Ennemi';
$lng['types']['string'][6] = 'PNJ';
$lng['types']['string'][1] = 'Vortex';
$lng['types']['string'][2] = 'Planète';
$lng['types']['string'][4] = 'Astéroïde';