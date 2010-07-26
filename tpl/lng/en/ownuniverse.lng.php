<?php
/**
 * @author Alex10336
 * @translator Curtis
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/


// -- ownuniverse.php ----------------------------------------------------------

$lng = array();
$lng['page_title']     = 'EU2: Production';
$lng['data_empty']     = 'Information Not Entered !';
$lng['start_info']     = 'Read info <b>i</b> to start';
$lng['data_error']     = 'Information not recognized';
$lng['control_center_error'] = 'Access Your Control Center For Information !';
$lng['universe_added'] = 'Your Control Center Information has been added';
$lng['universe_updated'] = 'Your Control Center Information has been updated';
$lng['universe_nochange'] = 'No change';
// partie détection, par block,
$lng['control_center_ident'] = 'Population supply each day';
$lng['block_planet_0'] = 'Description';
$lng['block_planet_1'] = 'Total';
$lng['block_coords_0'] = 'Coordinates';
$lng['block_coords_1'] = 'Distance';
$lng['block_batiments_0'] = 'Building (Level Of Development)';
$lng['block_batiments_1'] = 'Resources';
$lng['block_ress_0'] = $lng['block_batiments_1'];
$lng['block_ress_1'] = 'Production per hour';
$lng['block_prod_0'] = $lng['block_ress_1'];
$lng['block_prod_1'] = 'Resources in the bunker';
$lng['block_bunker_0'] = $lng['block_prod_1'];
$lng['block_bunker_1'] = 'Population supply each day';
$lng['block_sell_0'] = $lng['block_bunker_1'];
$lng['block_sell_1'] = 'Next supply';

$lng['planet_error']    = 'This planet is not yours (See Control Center)';
$lng['planet_added']    = 'Information of %s Added';
$lng['planet_nochange'] = 'No change';
// partie détection,
$lng['planet_ident'] = 'Resource Details';
$lng['planet_key_0'] = 'Resource Details';
$lng['planet_key_1'] = 'Planet Information';
$lng['planet_key_2'] = 'Resource Details';
$lng['planet_key_3'] = 'Coordinates';
$lng['planet_key_4'] = 'Surface d\'Water';

// Tableau...
$lng['tips_header'] = <<<tips_header
            Paste Details Here '<b>Control Center</b>' then '<b>Planets</b>'<br/>
(Ctrl+A then Ctrl+C after you open the page)<br/>
<br/>
 copy/paste for more information see preview
tips_header;

$lng['header']            = 'Add info... (Control Center Level 2 minimum)';
$lng['btn_submit']        = 'Add';
$lng['btn_reset']         = 'Reset';

$lng['row_concentration'] = 'Percentage';
$lng['row_prod/h']        = 'Prod/h';
$lng['row_stocks']        = 'Stocks';
$lng['row_Total']         = 'Total';
$lng['row_race_needed']   = 'Consumption';
$lng['cols_planets']      = 'Planet(s)';
$lng['cols_Total']        = 'Total';

// bulles
$lng['current_ress_row_1']=<<<crr1
%s<br/>
On Planet: %s<br/>
In Bunker: %s
crr1;

$lng['current_ress_row_2']=<<<crr2
On Planet: %s
<br/>In Bunker: %s
<br/>Safe: %s%%
<br/>Bunker Useage: %s%%
crr2;



