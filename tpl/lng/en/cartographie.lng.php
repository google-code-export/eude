<?php
/**
 * @author Alex10336
 * @tranlator Curtis
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

$lng = array();

// Planets ressources text
$lng['ress10%'] = 'very few';
$lng['ress20%'] = 'few';
$lng['ress40%'] = 'normal';
$lng['ress50%'] = 'medium';
$lng['ress70%'] = 'many';
$lng['ress80%'] = 'quite many';
$lng['ress90%'] = 'great many';

$lng['add_items_header'] = 'Please Input Data Here';
$lng['add_items_btn_auto'] = 'Automatic';
$lng['add_items_btn_manual'] = 'Manual';
$lng['add_items_btn_add'] = 'Add';

$lng['add_items_bulle'] = 'Paste Here Details Of a Player, Planet Or Wormhole (Ctrl + A Ctrl + C Ctrl + V)';
$lng['add_items_bulle1'] = 'Start Coordinate';
$lng['add_items_bulle2'] = 'Exit Coordinate';
$lng['add_items_bulle3'] = 'Player Name';
$lng['add_items_bulle4'] = 'Empire Name';
$lng['add_items_bulle5'] = 'Name Of The Planet Or Fleet';

$lng['add_items_col_type'] = 'Type';
$lng['add_items_col_corin'] = 'Start Coordinate';
$lng['add_items_col_corout'] = 'Exit Coordinate';
$lng['add_items_col_corrds'] = 'Coordinate';
$lng['add_items_col_player'] = 'Player Name';
$lng['add_items_col_empire'] = 'Empire';
$lng['add_items_col_infos'] = 'Planet/Fleet';
$lng['add_items_planet_header'] = 'Information Of The Planet Or Asteroide';

$lng['search_header'] = 'Search DataCore';
$lng['search_col_date'] = 'Date';
$lng['search_col_status'] = 'Status';
$lng['search_col_status_on'] = 'Active';
$lng['search_col_status_off'] = 'Inactive';
$lng['search_col_type'] = 'Type';
$lng['search_col_ss'] = 'SS';
$lng['search_col_rayon'] = 'Radius';
$lng['search_col_player'] = 'Player';
$lng['search_col_empire'] = 'Empire';
$lng['search_col_fleet'] = 'Planet/Fleet';
$lng['search_col_note'] = 'Note';
$lng['search_col_water'] = '% Water';
$lng['search_col_maxtroops'] = 'Max Troops';
$lng['search_col_troops'] = 'Troops';
$lng['search_col_self'] = 'Me';
$lng['search_col_showall'] = 'Show All';
$lng['search_col_btnsearch'] = 'Search';
$lng['search_col_btndoedit'] = 'Submit changes';

$lng['search_bulle_cmd_delete']  = 'Delete The Line ?<br/><br/><b>Warning</b>: No Confirmation Required !';
$lng['search_bulle_cmd_edit']    = 'Edit This Line ?';

$lng['search_date_short_format'] = 'H:i d-m';
$lng['search_date_long_format']  = 'd-m-Y  H:i:s';
$lng['search_userdate']          = 'At <b>%s</b><br/> %s';
$lng['search_troopdate']         = ' %s';

$lng['err_coorin_needed']= 'The Coordinate Entry Must Be Filled';
$lng['err_coorout_filled']= 'The Output Coordinate Provide Information For The Wormhole';
$lng['err_coorout_needed']= 'It Is Important To Provide The Coordinate Output For The Wormhole';
$lng['err_player_needed']= 'Please Enter The Name Of The Player';
$lng['err_unknown_type']= 'Type Requested Unsupported !';

//------------------------------------------------------------------------------
//-- cartographie.class.php ----------------------------------------------------
//------------------------------------------------------------------------------

$lng['class_err_noaxx']         = 'Permissions Missing';
$lng['class_err_ress']          = 'Format Of The Resource Is Wrong %s Incorrect (%s), Format : very Few,Few,Normal,Medium,Many,Quite Many,Great Many,[...],xx,xx%';
$lng['class_err_coords']        = 'Error Coordinate Format (%s) Must Be In This Format xxxx-xx-xx-xx Or xxxx:xx:xx:xx';

$lng['class_vortex_msg1']       = 'Wormhole %s <> %s Updated';
$lng['class_vortex_msg2']       = 'Wormhole %s <> %s Already Stored';
$lng['class_vortex_msg3']       = 'Wormhole %s <> %s Added To The DataCore';
$lng['class_planet_msg1']       = 'Planet Data Is Already Upto Date : %s-%s';
$lng['class_planet_msg2']       = 'Planet Coordinate Updated : %s-%s';
$lng['class_planet_msg3']       = 'Planet Added To the DataCore : %s-%s';
$lng['class_asteroid_msg1']     = 'Asteroide Info updated : %s-%s';
$lng['class_asteroid_msg2']     = 'Asteroide Added To the DataCore : %s-%s';
$lng['class_player_type0']      = 'Player';
$lng['class_player_type3']      = 'Ally';
$lng['class_player_type5']      = 'Enemy';
$lng['class_player_msg1']       = 'Planet %s Deserted';
if (NO_SESSIONS && USE_AJAX) { // Alias GreaseMonkey
    $lng['class_player_msg2']   = 'Updated %4$s: %1$s %2$s'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg3']   = 'Ignored %4$s: %1$s %2$s'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg4']   = 'Adding %4$s: %1$s %2$s'; // $stype,$nom,$uni,$sys
} else {
    $lng['class_player_msg2']   = '%s %s Updated : %s-%s'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg3']   = '%s %s Already stored : %s-%s (Ignored)'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg4']   = '%s %s Added To the DataCore : %s-%s'; // $stype,$nom,$uni,$sys
}

if (NO_SESSIONS && USE_AJAX) { // Alias GreaseMonkey
    $lng['class_npc_msg1']      = 'Updated %3$s: Reaper Fleet %1$s'; // $nom,$uni,$sys
    $lng['class_npc_msg2']      = 'Ignored %3$s: Reaper Fleet %1$s'; // $nom,$uni,$sys
    $lng['class_npc_msg3']      = 'Added %3$s: Reaper Fleet %1$s'; // $nom,$uni,$sys
} else {
    $lng['class_npc_msg1']      = 'Reaper Fleet Updated : %s-%s'; // $nom,$uni,$sys
    $lng['class_npc_msg2']      = 'Reaper Already Stored : %s-%s (Ignored)'; // $nom,$uni,$sys
    $lng['class_npc_msg3']      = 'Reaper Added To The DataCore : %s-%s'; // $nom,$uni,$sys
}


$lng['class_solar_msg1']        = '%d Planet Is Now Unoccupied %s';
$lng['class_solar_msg2']        = 'Player changed Empire: \'%s\'';

$lng['class_edit_defmsg']       = 'Player Updated "%1$s" en %3$s'; // type,player,coords,[...]
$lng['class_delete_nofound']    = 'Element Not found (%s)'; // ident
$lng['class_delete_msg']        = '%s (%s) Deleted'; // type,ident

