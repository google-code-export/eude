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

// \$lng\['(.*)'\]
// \{\$this->lng\['$1'\]\}

// tpl
$lng['legend_header']            = 'Ground Combat Reports!';
$lng['legend_type']              = 'Type';
$lng['legend_date']              = 'Date';
$lng['legend_coords']            = 'Coordinates';
$lng['legend_attack']            = 'Attacker';
$lng['legend_defend']            = 'Defender';
$lng['legend_lost']              = 'Losses';
$lng['legend_player']            = 'Player';

$lng['listing_header']           = 'List Of Lootings';
$lng['listing_btnfilter']        = 'Search';
$lng['listing_dateformat']       = '%a %d %B Ã  %R';
$lng['listing_playerrow']        = '<a href="?player=%s">%1$s</a>: %2$s';
$lng['listing_type']             = array();
$lng['listing_type']['defender'] = 'Defender';
$lng['listing_type']['attacker'] = 'Attacker';

// class (detections)
$lng['defender_regex'] = 'has left our planet (.*) The occupation has ended (.*)\./';
$lng['defender_regex_planetid'] = 1;
$lng['defender_regex_userid'] = 2;
$lng['defender_ident'] = 'He scavenged the following resources:';
$lng['attacker_regex'] = 'our troops have left the planet (.*) of (.*). The occupation has ended.\./';
$lng['attacker_regex_planetid'] = 1;
$lng['attacker_regex_userid'] = 2;
$lng['attacker_ident'] = 'We have taken the following commodities:';

// class (messages)
$lng['battle_allreadyexists'] = 'Battle Already Added  !';
$lng['battle_error_ownuniverse'] = 'Personal Info Needs To Be Added';
$lng['battle_updated'] = 'Battle Updated';
$lng['battle_added'] = 'Battle Added';

$lng['log_allreadyexists'] = 'Log Already Added';
$lng['log_battlenofound'] = 'Battle Not Found ? (';
$lng['log_coordsnotfound'] = 'Coordinates Not Found ? ()';
$lng['log_multiplecoords'] = 'Multiple Logs Added';
$lng['log_added'] = 'Looting Added.';