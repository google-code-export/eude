<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/


// -- ownuniverse.php ----------------------------------------------------------

$lng = array();
$lng['page_title']     = 'EU2: Son univers';
$lng['start_info']     = 'Lisez les infos sur le <b>i</b> pour commencer';
$lng['data_error']     = 'Information collé non reconnue';
$lng['control_center_error'] = 'Le centre de contrôle en premier !';
$lng['universe_added'] = 'Informations de votre univers ajouté.';
$lng['universe_updated'] = 'Informations de votre univers mise a jour.';
$lng['universe_nochange'] = 'Aucune différence, donnée inchangée.';
// partie détection, par block,
$lng['control_center_ident'] = 'Zaopatrzenie ludności na dzień';
$lng['block_planet_0'] = 'Opis';
$lng['block_planet_1'] = 'Razem';
$lng['block_coords_0'] = 'Współrzędne';
$lng['block_coords_1'] = 'Odległość od';
$lng['block_batiments_0'] = 'Budowle (poziomy rozbudowy)';
$lng['block_batiments_1'] = 'Surowce';
$lng['block_ress_0'] = $lng['block_batiments_1'];
$lng['block_ress_1'] = 'Produkcja na godzine';
$lng['block_prod_0'] = $lng['block_ress_1'];
$lng['block_prod_1'] = 'Surowce w bunkrze';
$lng['block_bunker_0'] = $lng['block_prod_1'];
$lng['block_bunker_1'] = 'Zaopatrzenie ludności na dzień';
$lng['block_sell_0'] = $lng['block_bunker_1'];
$lng['block_sell_1'] = 'Oczekiwanie na następne zaopatrzenie';

$lng['batiments']['control']       = 'Centrum kontroli';
$lng['batiments']['communication'] = 'Centrum komunikacji';
$lng['batiments']['university']    = 'Uniwersytet';
$lng['batiments']['technology']    = 'Centrum badań';
$lng['batiments']['gouv']          = 'Centrum dowodzenia';
$lng['batiments']['defense']       = 'Koszary';
$lng['batiments']['shipyard']      = 'Stocznia';
$lng['batiments']['spacedock']     = 'Dok';
$lng['batiments']['bunker']        = 'Bunkier';
$lng['batiments']['tradepost']     = 'Placówka handlowa';
$lng['batiments']['ressource']     = 'Fabryka surowców';

$lng['planet_error'] = 'Cette planète ne fait pas partie de votre univers (voir centre de controle)';
$lng['planet_added']    = 'Information de la planète %s ajouté.';
$lng['planet_nochange'] = 'Aucune différence, donnée inchangée.';
// partie détection,
$lng['planet_ident'] = 'Podział surowców';
$lng['planet_key_0'] = 'Podział surowców';
$lng['planet_key_1'] = 'Informacje planet';
$lng['planet_key_2'] = 'Podział surowców';
$lng['planet_key_3'] = 'Współrzędne';
$lng['planet_key_4'] = 'Osłona wodna';

// Tableau...
$lng['tips_header'] = <<<tips_header
            Coller ici les détails du '<b>Centre de contrôle</b>' puis '<b>Planètes</b>'<br/>
(Ctrl+A puis Ctrl+C après avoir ouvert la page)<br/>
<br/>
Suivit après par planète un copier/coller de la page 'Aperçu' pour les informations complémentaire
tips_header;

$lng['header']            = 'Ajout de l\'info... (centre de contrôle niveau 2 mini)';
$lng['btn_submit']        = 'Interpréter';
$lng['btn_reset']         = 'Reset';

$lng['row_concentration'] = 'Concentration';
$lng['row_prod/h']        = 'Prod/h';
$lng['row_stocks']        = 'Stocks';
$lng['row_Total']         = 'Total';
$lng['row_race_needed']   = 'Consommation';
$lng['cols_planets']      = 'Planète(s)';
$lng['cols_Total']        = 'Total';

// bulles
$lng['current_ress_row_1']=<<<crr1
%s<br/>
Sur planète: %s<br/>
Dans le bunker: %s
crr1;

$lng['current_ress_row_2']=<<<crr2
Sur planète: %s
<br/>Dans le bunker: %s
<br/>En sécurité: %s%%
<br/>Utilisation bunker: %s%%
crr2;



