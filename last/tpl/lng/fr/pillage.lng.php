<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

$lng = array();

// \$lng\['(.*)'\]
// \{\$this->lng\['$1'\]\}

// tpl
$lng['legend_header']            = 'Légende sur deux lignes.';
$lng['legend_type']              = 'Type';
$lng['legend_date']              = 'Date';
$lng['legend_coords']            = 'Coordonnées';
$lng['legend_attack']            = 'Attaquants';
$lng['legend_defend']            = 'Défenseurs';
$lng['legend_lost']              = 'Pertes';
$lng['legend_player']            = 'Joueur';

$lng['listing_header']           = 'Liste des pillages';
$lng['listing_btnfilter']        = 'Filtrer';
$lng['listing_dateformat']       = '%a %d %B à %R';
$lng['listing_playerrow']        = '<a href="?player=%s">%1$s</a>: %2$s';
$lng['listing_type']             = array();
$lng['listing_type']['defender'] = 'Défenseur';
$lng['listing_type']['attacker'] = 'Attaquant';

// class (détections)
$lng['defender_regex'] = '/Notre planète (.*) n\'est plus sous l\'occupation de (.*)\./';
$lng['defender_regex_planetid'] = 1;
$lng['defender_regex_userid'] = 2;
$lng['defender_ident'] = 'Il a volé les ressources suivantes :';
$lng['attacker_regex'] = '/Nos troupes ont quitté la planète (.*) de (.*), l\'occupation est terminée\./';
$lng['attacker_regex_planetid'] = 1;
$lng['attacker_regex_userid'] = 2;
$lng['attacker_ident'] = 'Nous avons pillé les ressources suivantes :';

// class (messages)
$lng['battle_allreadyexists'] = 'Existe déjà !';
$lng['battle_error_ownuniverse'] = 'Information personnelles insuffisantes';
$lng['battle_updated'] = 'Combat MAJ';
$lng['battle_added'] = 'Combat ajouté';

$lng['log_allreadyexists'] = 'Log déjà ajouté';
$lng['log_battlenofound'] = 'Bataille introuvable ? (flutte)';
$lng['log_coordsnotfound'] = 'Coordonnée du pillage introuvable ? (flutte)';
$lng['log_multiplecoords'] = 'Plusieurs coordonnée pour ce pillage ? (omg)';
$lng['log_added'] = 'Pillage ajouté.';