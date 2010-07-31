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


$lng['page_links1']         = 'General';
$lng['page_links2']         = 'User rights';
$lng['page_links3']         = 'Map Color';
$lng['page_links4']         = 'Configuration';
$lng['page_links5']         = 'Logs';

//------------------------------------------------------------------------------
$lng['page_title']          = 'E.U.D.E. Administration';
$lng['page_hlink']          = 'Official support ?';
$lng['dates']               = array();
$lng['dates'][0]            = '[No change]';
$lng['dates'][1]            = 'Today (All)';
$lng['dates'][2]            = 'Last Sunday';
$lng['dates'][3]            = 'Sunday Before';
$lng['dates'][4]            = 'Yesterday';
$lng['dates'][5]            = 'Day Before Yesterday';
$lng['dates'][6]            = '3 Days';
$lng['dates'][7]            = '4 Days';
$lng['dates'][8]            = '5 Days';
$lng['dates'][9]            = '6 Days';
$lng['dates'][10]           = '7 Days';
$lng['dates'][11]           = '15 Days';
$lng['dates'][12]           = '1 Month (First of the month)';
$lng['dates'][13]           = '2 Month (First of the month)';
$lng['dates'][14]           = '3 Month (First of the month)';
$lng['dates'][15]           = '6 Month (First of the month)';
$lng['dates'][16]           = '9 Month (First of the month)';
$lng['dates'][17]           = '12 Month(First of the month)';
$lng['dates'][20]           = 'All';

$lng['vortex_cron_enable']    = 'Auto Mode Active (<a href="%ROOT_URL%EAdmin.php?switch=vortex_cron">Activate ?</a>)';
$lng['vortex_cron_enabled']   = 'Last Time <font color=green>%s</font>';
$lng['vortex_cron_disable']   = 'Auto Mode Active (<a href="%ROOT_URL%EAdmin.php?switch=vortex_cron">Deactivate</a>)';
$lng['vortex_title']          = 'Clean Wormholes:';
$lng['vortex_do_now']         = "Clean Now";
$lng['vortex_servertime']     = 'Server Time:';
$lng['vortex_whathappen']     = 'Wormholes older than "%s" will be deleted.';
$lng['vortex_result']         = '%d Deleted wormholes(s)';


$lng['empire_switch']         = 'Change name of an Empire: ';
$lng['empire_switch_btn']     = 'Change';
$lng['empire_switch_current'] = 'Original:';
$lng['empire_switch_current_sel'] = '[Select an Empire]';
$lng['empire_switch_new']     = 'New';
$lng['empire_switch_new_sel'] = '[Delete the Empire]';
$lng['empire_switch_result']  = '%d Planets change with the new empire.';

$lng['empire_allys']          = 'Declare Alliance with an empire: ';
$lng['empire_allys_sel']      = '[Select Empire]';
$lng['empire_allys_add']      = 'Add';
$lng['empire_allys_del']      = 'remove';
$lng['empire_allys_empty']    = 'No Alliance(s)...';

$lng['empire_wars']           = 'Declare war on an Empire: ';
$lng['empire_wars_sel']       = '[Select an empire]';
$lng['empire_wars_add']       = 'Add';
$lng['empire_wars_del']       = 'Remove';
$lng['empire_wars_empty']     = 'No Current War...';

$lng['empire_allyswars']      = 'Force Update Information On Allaince/War/Neutral';
$lng['empire_allyswars_upd']  = 'Update';
$lng['empire_allyswars_result0'] = '%d planets updated with the \' new \' status \' s ally.';
$lng['empire_allyswars_result1'] = '%d planets changed with the \' new \'status \' s enemies.';

$lng['cleaning_items']        = 'Clean Various...';
$lng['cleaning_act']          = 'Older Than';
$lng['cleaning_btn']          = 'Clean';
$lng['cleaning_joueurs']      = 'Deleting Players / Allies / Enemies';
$lng['cleaning_joueurs_result'] = '%d Players Deleted';
$lng['cleaning_pnj']          = 'Deleting Reapers';
$lng['cleaning_pnj_result']   = '%d Reapers Deleted';
$lng['cleaning_wormshole']          = 'Deleteing Wormholes';
$lng['cleaning_wormshole_result']   = '%d Wormholes Deleted';
$lng['cleaning_planetes']     = 'Deleting Planets';
$lng['cleaning_planetes_result'] = '%d Planets deleted';
$lng['cleaning_asteroides']   = 'Deleting Asteroids';
$lng['cleaning_asteroides_result'] = '%d Asteroids Deleted';
$lng['cleaning_inactif']      = 'Deleting inactive elements';
$lng['cleaning_inactif_result'] = '%d Inactive elements Deleted';

$lng['cleaning_add_coords_unique_index'] = 'Search for duplicates in E.U.D.E.';
$lng['cleaning_orphan_planets'] = 'Search for single items in the E.U.D.E.';
$lng['regen_buttons']     = 'Regenerate the buttons';
$lng['regen_buttons_inwork'] = 'Regenerating...<br/>Press F5 to Refresh';
$lng['regen_buttons_btn'] = 'Regenerate';

//------------------------------------------------------------------------------
$lng['perms_title']         = 'E.U.D.E. Admin';
$lng['perms_col1']          = 'Elements';
$lng['perms_col2']          = 'Minimum \'access';
$lng['perms_apply']         = 'Save';

//------------------------------------------------------------------------------
$lng['mapcolor_title']      = 'E.U.D.E. Administration, Color Card';
$lng['mapcolor_header']     = 'Change Map Colors';
$lng['mapcolor_btn']        = 'Save';
$lng['colorslegend']        = array();
$lng['colorsgroup']         = array();

// Couleurs utilisé sur la page 'Carte.php'
$lng['colorsgroup'][0]      = 'Route Color';
$lng['colorslegend'][0]     = array();
$lng['colorslegend'][0][0]  = 'Radar Scope';
$lng['colorslegend'][0][1]  = 'All Elements';
$lng['colorslegend'][0][2]  = 'My Planets';
//$lng['colorslegend'][0][3]  = 'N/A';
//$lng['colorslegend'][0][4]  = 'N/A';
//$lng['colorslegend'][0][5]  = 'N/A';
//$lng['colorslegend'][0][6]  = 'N/A';
//$lng['colorslegend'][0][7]  = 'N/A';
//$lng['colorslegend'][0][11] = 'N/A';
//$lng['colorslegend'][0][8]  = 'N/A';
//$lng['colorslegend'][0][9]  = 'N/A';
//$lng['colorslegend'][0][10] = 'N/A';
$lng['colorslegend'][0][20] = 'Start...';
$lng['colorslegend'][0][21] = 'Finish.';
$lng['colorslegend'][0][22] = 'Wormhole route.';
$lng['colorslegend'][0][24] = 'Navigation \'Warp\' normal';
$lng['colorslegend'][0][25] = 'Navigation Wormhole.';

$lng['colorsgroup'][1]      = 'Color Pallet 1';
$lng['colorslegend'][1]     = array();
$lng['colorslegend'][1][0]  = 'Radar Scope';
$lng['colorslegend'][1][1]  = 'Empire members';
$lng['colorslegend'][1][2]  = 'My planets';
$lng['colorslegend'][1][3]  = 'Players';
$lng['colorslegend'][1][4]  = 'Wormholes';
$lng['colorslegend'][1][5]  = 'Asteroids';
$lng['colorslegend'][1][6]  = 'Empty Planets';
$lng['colorslegend'][1][7]  = 'Players Empires';
$lng['colorslegend'][1][11] = 'Alliances';
$lng['colorslegend'][1][8]  = 'Enemy Players';
$lng['colorslegend'][1][9]  = 'Reaper Fleets';
$lng['colorslegend'][1][10] = 'Search Results';
//$lng['colorslegend'][1][20] = 'N/A';
//$lng['colorslegend'][1][21] = 'N/A';
//$lng['colorslegend'][1][22] = 'N/A';
//$lng['colorslegend'][1][24] = 'N/A';
//$lng['colorslegend'][1][25] = 'N/A';

$lng['colorsgroup'][2]      = 'Color Pallet 2';
$lng['colorslegend'][2]     = array();
$lng['colorslegend'][2][0]  = 'Radar Scope';
$lng['colorslegend'][2][1]  = 'Empire Members';
$lng['colorslegend'][2][2]  = 'My Planets';
$lng['colorslegend'][2][3]  = 'Players';
$lng['colorslegend'][2][4]  = 'Wormholes';
$lng['colorslegend'][2][5]  = 'Asteroids';
$lng['colorslegend'][2][6]  = 'Empty Planets';
$lng['colorslegend'][2][7]  = 'Players Empires';
$lng['colorslegend'][2][11] = 'Alliances';
$lng['colorslegend'][2][8]  = 'Enemy Player';
$lng['colorslegend'][2][9]  = 'Reaper Fleets';
$lng['colorslegend'][2][10] = 'Search Results';
//$lng['colorslegend'][2][20] = 'N/A';
//$lng['colorslegend'][2][21] = 'N/A';
//$lng['colorslegend'][2][22] = 'N/A';
//$lng['colorslegend'][2][24] = 'N/A';
//$lng['colorslegend'][2][25] = 'N/A';

//------------------------------------------------------------------------------

$lng['config_title']           = 'E.U.D.E. Administration, Configuration';
$lng['config_header']          = 'Configuration:';
$lng['config_forumlink']       = 'Forum Link:';
$lng['config_canregister']     = 'Account Registration:';
$lng['config_canregister_off'] = 'Deactivate';
$lng['config_canregister_on']  = 'Authorise';
$lng['config_defaultgrade']    = 'Default grade:';
$lng['config_defaultgrade_tip']= bulle('Default Grade For Accounts See Admin.');
$lng['config_myempire']    = 'My Empire:';
$lng['config_myempire_tip']= bulle('Please Enter The Name Of Your Empire');
$lng['config_Parcours_Max_Time']    = 'Maximum time for caulating a route (sec.):';
$lng['config_Parcours_Max_Time_tip']= bulle('Maximum Calculation Time...');
$lng['config_Parcours_Nearest']    = 'Number of pc For The Calculation Of The Route:';
$lng['config_Parcours_Nearest_tip']= bulle('Quick Route Finder');
$lng['config_greasemonkey']    = 'Game Server For GreaseMonkey:';
$lng['config_greasemonkey_tip']= bulle('<b>Format</b>:<br/>[Préfixe].looki.[domaine]<br/><b>Exemples</b>:<br/>australis.fr<br/>polaris.fr<br/>eu2.com</br/>beta.de');
$lng['config_closed']    = 'Deactivate The Data Engine ?';
$lng['config_closed_no']    = 'Open';
$lng['config_closed_yes']    = 'Close';

$lng['config_apply']         = 'Update';
$lng['config_done']          = 'Configuration updated';

//------------------------------------------------------------------------------
$lng['logs_title']          = 'E.U.D.E. Administration, logs';
$lng['logs_date']           = 'Date';
$lng['logs_msg']            = 'Message';
$lng['logs_ip']             = 'IP';