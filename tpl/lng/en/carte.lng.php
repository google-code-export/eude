<?php
/**
 * @author Alex10336
 * @translator Curtis
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

$lng = array();

// Inforamtion Bubbles 'Carte.php'
$lng['helpmsg'] = <<<MSG
            <b>Using The Map:</b><br/>
Left Click: Select Start Point<br/>
Right Click: Select Finish Point<br/>
Shift / Ctrl + Click: View the details of the system
MSG;
$lng['msg_taill_inc'] = 'Increase Map Size +';
$lng['msg_taill_dec'] = 'Decrease Map Size &#151;';
$lng['msg_cls']       = 'Map Colours';
$lng['msg_all_on']    = 'Activate All';
$lng['msg_all_off']   = 'Deactivate All';
$lng['msg_vortex']    = 'Wormhole: %s';
$lng['msg_joueur']    = 'Players: %s';
$lng['msg_planete']   = 'Planets: %s';
$lng['msg_asteroide'] = 'Asteroide: %s';
$lng['msg_ennemis']   = 'Enemy: %s';
$lng['msg_allys']     = 'Alliance: %s';
$lng['msg_pirate']    = 'Reaper Fleet: %s';
$lng['msg_coords']    = 'Coordinate';
$lng['msg_search1']   = 'Choose Player,Empire,Planet,Reaper';
$lng['msg_search2']   = 'Enter Here To Search';
$lng['msg_search_emp']= 'Empire';
$lng['msg_search_jou']= 'Player';
$lng['msg_search_info']= 'Name Of Planet/Fleet';

// Menu For Calculation
$lng['parcours_header']= 'Navigation';
$lng['parcours_select']= 'Route';
$lng['parcours_option']= '[Select Your Route]';
$lng['parcours_msg_load']= 'Load Route';
$lng['parcours_msg_save']= 'Save Route';
$lng['parcours_msg_del']= 'Remove Route';
$lng['parcours_msg_inv']= 'Reverse The Route';
$lng['parcours_start_ss']= 'System Of departure';
$lng['parcours_end_ss']= 'System Of Arrival';
$lng['parcours_nointrass']= 'Select Jumps';
$lng['parcours_method_1']= '1 Wormhole Max';
$lng['parcours_method_2']= '2 Wormhole Max';
$lng['parcours_method_3']= '3 Wormhole Max';
$lng['parcours_method_10']= 'Auto';
$lng['parcours_start']= 'Start';
$lng['parcours_bywormhole']= 'Wormhole %s';
$lng['parcours_end']= 'Finish';
$lng['parcours_diff']= 'Difference';

// Bubbles On Map
// Do Not Use Quotes
$lng['map_ownplanet']= '<b>My Planet: %s</b>'; // planet name
$lng['map_empire_header']= '<b>%d Member(s) %s</b>'; // number / empire
$lng['map_alliance_header']= '<b>%d Member(s) d\'Allaince</b>'; // number
$lng['map_search_header']= '<b>Search: %d Result(s):</b>'; // number
$lng['map_player_header']= '<b> %d Player(s)</b>'; // number
$lng['map_ennemy_header']= '<b> %d Enemy(s)</b>'; // number
$lng['map_pnj_header']= '<b> %d Reaper Fleet (s)</b>'; // number
$lng['map_wormhole_header']= '<b> %d Wormhole</b>'; // number
$lng['map_planet_header']= '<b> %d Planet(s)</b>'; // number
$lng['map_asteroid_header']= '<b> %d Asteroide(s)</b>'; // number

$lng['map_row_player1']= '%s (%s)'; // player / eude grade
$lng['map_row_player2']= '%s <i>(Not Registered)</i>'; // player
$lng['map_row_player3']= '%s (%s)'; // player / empire
$lng['map_row_player4']= '<font color=red>%s</font> (%s)'; // player(ennemy) / empire

$lng['map_parcours_start']= 'Start';
$lng['map_parcours_end']= 'Finish';
$lng['map_parcours_wormhole']= 'Wormhole Route';
$lng['map_parcours']= '<font color=\'darkgreen\'><b>%s</b></font>';


// Legend Displayed On The Page 'Carte.php'
$lng['legend'] = 'Key';
$lng['maplegend']        = array ();
// Mode calcul de parcours
$lng['maplegend'][0]     = array();
//$lng['maplegend'][0][0]  = 'Radar Scope';
//$lng['maplegend'][0][1]  = 'Astre quelconque';
$lng['maplegend'][0][2]  = 'My Planets';
//$lng['maplegend'][0][3]  = 'N/A';
//$lng['maplegend'][0][4]  = 'N/A';
//$lng['maplegend'][0][5]  = 'N/A';
//$lng['maplegend'][0][6]  = 'N/A';
//$lng['maplegend'][0][7]  = 'N/A';
//$lng['maplegend'][0][11] = 'N/A';
//$lng['maplegend'][0][8]  = 'N/A';
//$lng['maplegend'][0][9]  = 'N/A';
//$lng['maplegend'][0][10] = 'N/A';
$lng['maplegend'][0][20] = 'Start...';
$lng['maplegend'][0][21] = 'Finish...';
$lng['maplegend'][0][22] = 'Wormhole Route';
//$lng['maplegend'][0][24] = 'Navigation \'Warp\' normale';
//$lng['maplegend'][0][25] = 'Navigation par vortex.';

// Color Palette 1
$lng['maplegend'][1]     = array();
//$lng['maplegend'][1][0]  = 'Radar Scope';
$lng['maplegend'][1][1]  = 'Empire Member';
$lng['maplegend'][1][2]  = 'My Planets';
$lng['maplegend'][1][3]  = 'Player';
$lng['maplegend'][1][4]  = 'Wormhole';
$lng['maplegend'][1][5]  = 'Asteroide';
//$lng['maplegend'][1][6]  = 'Planètes vide / Autre';
//$lng['maplegend'][1][7]  = 'Joueur de l\'empire + autres';
$lng['maplegend'][1][11] = 'Alliance';
$lng['maplegend'][1][8]  = 'Enemy Player';
$lng['maplegend'][1][9]  = 'Reaper Fleet';
$lng['maplegend'][1][10] = 'Search Result';
//$lng['maplegend'][1][20] = 'N/A';
//$lng['maplegend'][1][21] = 'N/A';
//$lng['maplegend'][1][22] = 'N/A';
//$lng['maplegend'][1][24] = 'N/A';
//$lng['maplegend'][1][25] = 'N/A';

// Color Palette 2 (basic Colors)
$lng['maplegend'][2]     = array();
//$lng['maplegend'][2][0]  = 'Portée du radar';
$lng['maplegend'][2][1]  = 'Empire Member';
$lng['maplegend'][2][2]  = 'My Planets';
$lng['maplegend'][2][3]  = 'All Planets';
//$lng['maplegend'][2][4]  = 'Vortex';
//$lng['maplegend'][2][5]  = 'Astéroïdes';
//$lng['maplegend'][2][6]  = 'Planètes vide / Autre';
//$lng['maplegend'][2][7]  = 'Joueur de l\'empire + autres';
//$lng['maplegend'][2][11] = 'Alliés';
//$lng['maplegend'][2][8]  = 'Joueurs ennemi';
//$lng['maplegend'][2][9]  = 'Flottes PNJ';
$lng['maplegend'][2][10] = 'Search Results';
//$lng['maplegend'][2][20] = 'N/A';
//$lng['maplegend'][2][21] = 'N/A';
//$lng['maplegend'][2][22] = 'N/A';
//$lng['maplegend'][2][24] = 'N/A';
//$lng['maplegend'][2][25] = 'N/A';
