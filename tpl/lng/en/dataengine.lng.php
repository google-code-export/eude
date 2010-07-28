<?php
/*
 * @author Alex10336
 * @translator Curtis
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
*/

setlocale(LC_ALL, 'en_EN.utf8', 'en_EN', 'en');

$lng = array();
$lng['Titane'] = 'Titanium';
$lng['Cuivre'] = 'Copper';
$lng['Fer'] = 'Iron';
$lng['Aluminium'] = 'Aluminium';
$lng['Mercure'] = 'Mercury';
$lng['Silicium'] = 'Silicone';
$lng['Uranium'] = 'Uranium';
$lng['Krypton'] = 'Krypton';
$lng['Azote'] = 'Nitrogen';
$lng['Hydrogene'] = 'Hydrogen';

$lng['races']['Human']       = 'Human';
$lng['races']['Ozoid']       = 'Ozoid';
$lng['races']['Mosorian']    = 'Mosorian';
$lng['races']['Zuup']        = 'Zuup';
$lng['races']['Plentropian'] = 'Plentropian';
$lng['races']['Magumian']    = 'Magumian';
$lng['races']['Weganian']    = 'Weganian';
$lng['races']['Cyborg']      = 'Cyborg';
$lng['races']['Jamozoid']    = 'Jamozoid';

$lng['batiments']['control']       = 'Control Center';
$lng['batiments']['communication'] = 'Communication Center';
$lng['batiments']['university']    = 'University';
$lng['batiments']['technology']    = 'Reserch Center';
$lng['batiments']['gouv']          = 'Goverment Building';
$lng['batiments']['defense']       = 'Barracks';
$lng['batiments']['shipyard']      = 'Shipyard';
$lng['batiments']['spacedock']     = 'Space dock';
$lng['batiments']['bunker']        = 'Bunker';
$lng['batiments']['tradepost']     = 'Trading Post';
$lng['batiments']['ressource']     = 'Resource Mine';

$lng['shiplist'][]='Probe';
$lng['shiplist'][]='Lancer';
$lng['shiplist'][]='Cutter';
$lng['shiplist'][]='Corvette';
$lng['shiplist'][]='Frigate';
$lng['shiplist'][]='Destroyer';
$lng['shiplist'][]='Nighthawk';
$lng['shiplist'][]='Cruiser';
$lng['shiplist'][]='Medium Cruiser';
$lng['shiplist'][]='Battle Cruiser';
$lng['shiplist'][]='Battleship';
$lng['shiplist'][]='Dreadnought';
$lng['shiplist'][]='Titan';
$lng['shiplist'][]='Dreadnought SL';
$lng['shiplist'][]='Behemoth';
$lng['shiplist'][]='Aurel';
$lng['shiplist'][]='Mach';
$lng['shiplist'][]='Leviathan';
$lng['shiplist'][]='Trayan';
$lng['shiplist'][]='Doombringer';


$cxx = array();
$cxx[] = 'Partie administrative';
$cxx['MEMBRES_ADMIN'] = 'Page admin';
$cxx['MEMBRES_ADMIN_LOG'] = 'Connection Log';
$cxx['MEMBRES_ADMIN_MAP_COLOR'] = 'Change map colors';
$cxx['MEMBRES_NEW'] = 'Add Member (Includeing Grades)';
$cxx['MEMBRES_EDIT'] = 'Modify Member';
$cxx['MEMBRES_NEWPASS'] = 'Change Password';
$cxx['MEMBRES_DELETE'] = 'Delete Member';
$cxx['MEMBRES_STATS'] = 'View Stats';
$cxx['MEMBRES_HIERARCHIE'] = 'Members Ranks';
$cxx[] = 'Carte';
$cxx['CARTE'] = 'Page Carte';
$cxx['CARTE_SEARCH'] = 'Search';
$cxx['CARTE_JOUEUR'] = 'View Player';
$cxx['CARTE_SHOWEMPIRE'] = 'view Player\Empire';
$cxx[] = 'Ma fiche & co';
$cxx['PERSO'] = 'Page Mafiche';
$cxx['PERSO_RESEARCH'] = 'Search';
$cxx['PERSO_OWNUNIVERSE'] = 'Production';
$cxx['PERSO_OWNUNIVERSE_READONLY'] = 'Production (Read Only)';
$cxx['PERSO_TROOPS_BATTLE'] = 'Combat Results';
$cxx[] = 'Cartographie';
$cxx['CARTOGRAPHIE'] = 'Page Cartographie';
$cxx['CARTOGRAPHIE_ASTEROID'] = 'Adding Asteroid';
$cxx['CARTOGRAPHIE_PLANETS'] = 'Adding Planet';
$cxx['CARTOGRAPHIE_PLAYERS'] = 'Adding Player';
$cxx['CARTOGRAPHIE_PNJ'] = 'Adding Reaper fleet';
$cxx['CARTOGRAPHIE_SEARCH'] = 'Search';
$cxx['CARTOGRAPHIE_DELETE'] = 'Access Removed';
$cxx['CARTOGRAPHIE_EDIT']   = 'Access Updated';
$cxx['CARTOGRAPHIE_GREASE'] = 'Use "GreaseMonkey"';
$cxx['EMPIRE_GREASE'] = 'Empire GreaseMonkey';
$cxx[] = 'Addons:';
$lng['cxx'] = $cxx;

$lng['axx'] = array(
                AXX_VALIDATING	=>'Not Vailidated',
                AXX_GUEST	=>'Guest',
                AXX_MEMBER	=>'Member',
                AXX_POWERMEMBER	=>'Member+',
                AXX_MODO	=>'Moderator',
                AXX_SUPMODO	=>'Super-Moderator',
                AXX_ADMIN	=>'Administrator',
                AXX_ROOTADMIN	=>'Super-Administrator',
                AXX_DISABLED	=>'Deactivate'
        );

$lng['minimalpermsneeded'] = 'You Do Not Have Permission (<small>%s</small>)';
$lng['nopermsanddie'] = 'Permission Missing';

// Types pris en charge !
$lng['types'] = array();
$lng['types']['dropdown'] = array(); // alias cctype
$lng['types']['dropdown'][0] = 'Player';
$lng['types']['dropdown'][3] = 'Alliance';
$lng['types']['dropdown'][5] = 'Enemy';
$lng['types']['dropdown'][6] = 'Reaper Fleet';
$lng['types']['dropdown'][1] = 'Wormhole';
$lng['types']['dropdown'][2] = 'Planet';
$lng['types']['dropdown'][4] = 'Asteroid';

$lng['types']['imgurl'] = array(); // alias ccimg (xml/cartedetail.php)
$lng['types']['imgurl'][0] = IMAGES_URL.'Joueur.jpg';
$lng['types']['imgurl'][3] = IMAGES_URL.'fleet_own.gif';
$lng['types']['imgurl'][5] = IMAGES_URL.'fleet_enemy.gif';
$lng['types']['imgurl'][6] = IMAGES_URL.'fleet_npc.gif';
$lng['types']['imgurl'][1] = IMAGES_URL.'Vortex.jpg';
$lng['types']['imgurl'][2] = IMAGES_URL.'Planete.jpg';
$lng['types']['imgurl'][4] = IMAGES_URL.'Asteroide.jpg';

$lng['types']['string'] = array(); // alias stype (utilisé pour les messages cartographie)
$lng['types']['string'][0] = 'Player';
$lng['types']['string'][3] = 'Alliance';
$lng['types']['string'][5] = 'Enemy';
$lng['types']['string'][6] = 'Reaper';
$lng['types']['string'][1] = 'Wormhole';
$lng['types']['string'][2] = 'Planet';
$lng['types']['string'][4] = 'Asteroid';

// Nettoyage votex...
$lng['wormholes_day'] = 6; // php.net/date => date ('w');
$lng['wormholes_hour'] = 7;
$lng['wormholes_minute'] = 1;
