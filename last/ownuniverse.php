<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
require_once('./init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'ownuniverse.class.php');
require_once(CLASS_PATH.'parser.class.php');
require_once(TEMPLATE_PATH.'ownuniverse.tpl.php');

DataEngine::CheckPermsOrDie('PERSO_OWNUNIVERSE');

$ownuniverse = ownuniverse::getinstance();
$lng = language::getinstance()->GetLngBlock('ownuniverse');

// initialisation des variables
$info = $warn ='';

if (isset($_GET['reset']) && $_GET['reset'] == $_SESSION['_permkey']) {
    DataEngine::sql('DELETE FROM `SQL_PREFIX_ownuniverse` WHERE `UTILISATEUR` = \''.$_SESSION['_login'].'\' LIMIT 1');
    output::Boink('%ROOT_URL%ownuniverse.php');
}

if ($_GET['showuser'] != '' && Members::CheckPerms('PERSO_OWNUNIVERSE_READONLY')) {
    $player = gpc_esc($_GET['showuser']);
    $include_form = false;
} else {
    $player=false;
    $include_form = true;
}

if (isset($_POST['importation'])) {

    $cleandata = $ownuniverse->get_universe(false); // initialise les infos (planet)

    $data = gpc_esc($_POST['importation']);

    // Partie centre de controle
    if ( $data != "" and strpos($data,$lng['control_center_ident']) !== false ) {
        $cleandata = $ownuniverse->parse_ownuniverse($data);
        if ($cleandata===false)
            $cleandata = $ownuniverse->get_universe(false);
        else
            list($info, $warn) = $ownuniverse->add_ownuniverse($cleandata);

        // Partie affichage planète
    } elseif ( $data != "" and strpos($data,$lng['planet_ident']) !== false ) {
        $cleandata = $ownuniverse->get_universe(false);
        if ($cleandata && is_array($cleandata[0])) {
            $result = $ownuniverse->parse_planet($data);
            $warn = $lng['planet_error'];
            foreach ($cleandata as $k => $planet) {
                if ($planet['Coord']==$result['Coord']) {
                    list($info, $warn) = $ownuniverse->add_planet($k,$result[0]);
                    $cleandata[$k]=array_merge($planet,$result[0]);
                    break;
                }
            }

        } else $warn = $lng['control_center_error'];
    } else $warn = $lng['data_error'];
} else $cleandata = $ownuniverse->get_universe($player);

$IsEnabled = ($cleandata && is_array($cleandata[0]));

require_once(TEMPLATE_PATH.'ownuniverse.tpl.php');
$tpl = tpl_ownuniverse::getinstance();
$tpl->page_title = $lng['page_title'];

if ($IsEnabled)
    $tpl->Setheader($info, $warn, $include_form);
else
    $tpl->Setheader($lng['ownuniverse_info'], $warn, $include_form);

/**
 Array (
 Name		=> "MaPlanète"
 Coord		=> xxxx-xx-xx-xx
 *			=> Ressources prod/h
 current_*	=> Ressources dispo
 bunker_*	=> Ressources dans le bunker
 total_*		=> current_* + bunker_*
 sell_*		=> Ressources vendu/j
 percent_*	=> Ratio d'exploitation des ressources
 )
 "*" => Nom de ressource
 **/
if ($cleandata && is_array($cleandata[0])) {
    $nb_planet = 0;
    foreach( $cleandata as $v )
        if (is_array($v)) $nb_planet++;
    if ($nb_planet>5) {
        //        FB::warn('$nb_planet>5, variable forcé...');
        $nb_planet=5;
    } //else FB::info($nb_planet,'NB planètes');
//        FB::info($cleandata[0],'Planète 1');

    $keys = array('Titane', 'Cuivre', 'Fer', 'Aluminium', 'Mercure', 'Silicium', 'Uranium', 'Krypton', 'Azote', 'Hydrogene');
    $total_all = array();

    for($i=0;$i<$nb_planet;$i++) {
        $pt = 0;
        for ($j=0;$j<10;$j++) {
            $v = $cleandata[$i]["current_{$keys[$j]}"]+$cleandata[$i]["bunker_{$keys[$j]}"];
            $cleandata[$i]["total_{$keys[$j]}"] = $v;
            $total_all["{$keys[$j]}"] +=$v;
            $pt+=$v;
        }
        $cleandata[$i]["total_total"] = $pt;
        $total_all["total"] +=$pt;
    }

//------------------------------------------------------------------------------

    $tpl->RowHeader();

    for($i=0;$i<$nb_planet;$i++) {

        $tpl->Planet_Header($cleandata[$i]);
        if (isset($cleandata[$i]['percent_'.$keys[0]]))
            $tpl->Add_PercentRow($cleandata[$i],$lng['row_concentration'],'percent_','imperium_row0');

        $tpl->Add_RessRow($cleandata[$i],$lng['row_prod/h'],'','imperium_row1');

        $tpl->Add_Current_Ress($cleandata[$i]);

    }
    $tpl->RowHeader();

    $tpl->Add_RessRow($total_all,$lng['row_Total'],'','imperium_row1');

    if ($ownuniverse->get_race()!='')
        $tpl->Add_PercentRow(DataEngine::a_race_ressources($ownuniverse->get_race()),$lng['row_race_needed'],'','imperium_row1');

//------------------------------------------------------------------------------

    $BatimentsName = DataEngine::a_batiments();
    $tpl->SetheaderBatiments();

    foreach ($BatimentsName as $k => $name)
        $tpl->AddToRow($name, $k);
    $tpl->PushRow();
    
    foreach ($cleandata as $k => $planet) {
        $id = $k%2;
        $tpl->AddToRow($id, 'class');
        $tpl->AddToRow($planet['Name'], 'Name');
        foreach ($BatimentsName as $k => $name)
            $tpl->AddToRow($planet[$k], $k);
        $tpl->PushRow();
    }
    
}
$tpl->DoOutput();


