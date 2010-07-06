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
require_once(CLASS_PATH.'map.class.php');

DataEngine::conf_cache('EmpireAllys');
DataEngine::conf_cache('EmpireEnnemy');
DataEngine::conf_cache('MapColors');

if (!DataEngine::CheckPerms('CARTE'))
    output::Boink(ROOT_URL.'Mafiche.php');

$map = map::getinstance();

/// Modification de la configuration utilisateur...
if (isset($_GET['AllSwitch'])) {
    $newval = intval($_GET['AllSwitch']);
    $map->asteroide = $newval;
    $map->vortex = $newval;
    $map->ennemis = $newval;
    $map->allys = $newval;
    $map->joueur = $newval;
    $map->planete = $newval;
    $map->pnj = $newval;
    $map->save_prefs();
    output::boink('Carte.php');
}

if (isset($_GET['vortex'])) {
    if ($_GET['vortex']=='0') $map->vortex=0;
    else $map->vortex=1;
    $boink=true;
}

if (isset($_GET['joueur'])) {
    if ($_GET['joueur']=='0') $map->joueur=0;
    else $map->joueur=1;
    $boink=true;
}

if (isset($_GET['planete'])) {
    if ($_GET['planete']=='0') $map->planete=0;
    else $map->planete=1;
    $boink=true;
}

if (isset($_GET['asteroide'])) {
    if ($_GET['asteroide']=='0') $map->asteroide=0;
    else $map->asteroide=1;
    $boink=true;
}
if (isset($_GET['pnj'])) {
    if ($_GET['pnj']=='0') $map->pnj=0;
    else $map->pnj=1;
    $boink=true;
}
if (isset($_GET['ennemis'])) {
    if ($_GET['ennemis']=='0') $map->ennemis=0;
    else $map->ennemis=1;
    $boink=true;
}
if (isset($_GET['allys'])) {
    if ($_GET['allys']=='0') $map->allys=0;
    else $map->allys=1;
    $boink=true;
}

if (isset($_GET['sc'])) {
    if ($_GET['sc']=='1') $map->sc=1;
    else $map->sc=0;
    $boink=true;
}

//Taille de la Carte
if(isset($_GET['taille'])) {
    $map->taille = intval($_GET['taille']);
    $boink=true;
}
$map->taille = max(400, min($map->taille, 1200));

if (isset($boink)) {
    $map->save_prefs();
    output::boink('Carte.php');
}

/// DATABASE MODIFICATION ///
if (isset($_GET['savefleet'])) { // enregistrement
    $mysql_result = DataEngine::sql('SELECT `ID` from `SQL_PREFIX_itineraire` where `Flotte`=\''.sqlesc($_GET['savefleet']).'\' AND `Joueur`=\''.$_SESSION['_login'].'\'');
    if (mysql_num_rows($mysql_result) > 0) // MAJ du parcours
    {
        $ligne=mysql_fetch_array($mysql_result, MYSQL_ASSOC);
        $mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_itineraire` SET `Flotte`=\''.sqlesc($_GET['savefleet']).'\',`Start`=\''.intval($_GET['in']).'\',`End`=\''.intval($_GET['out']).'\' where `ID`=\''.$ligne['ID'].'\' LIMIT 1');
        output::boink('Carte.php?loadfleet='.$ligne['ID']);
    } else { // Nouveau parcours
        $sql = 'INSERT INTO `SQL_PREFIX_itineraire` (`Joueur`,`Flotte`,`Start`,`End`) VALUES (\''.$_SESSION['_login'].'\',\''.sqlesc($_GET['savefleet']).'\',\''.intval($_GET['in']).'\',\''.intval($_GET['out']).'\')';
        $mysql_result = DataEngine::sql($sql);
        output::boink('Carte.php?loadfleet='.mysql_insert_id());
    }
}
if (isset($_GET["delfleet"])) { // suppression
    $mysql_result = DataEngine::sql('SELECT `ID` from `SQL_PREFIX_itineraire` where `ID`=\''.intval($_GET['delfleet']).'\' AND `Joueur`=\''.$_SESSION['_login'].'\'');
    if (mysql_num_rows($mysql_result) > 0)
        DataEngine::sql('DELETE FROM `SQL_PREFIX_itineraire` WHERE `ID`=\''.intval($_GET['delfleet']).'\' LIMIT 1');
    output::boink("Carte.php");
}

$_SESSION['inactif'] = $map->inactif = (isset($_POST['inactif'])) ? true: false;
$_SESSION['emp']  = $_SESSION['jou']  = '';

/// CHARGEMENT PARCOURS ///

$title = $map->Parcours_loadfleet();
if ( $map->itineraire ) {
    $map->parcours = $map->Parcours()->Do_Parcours($map->IN,$map->OUT);
    $map->load_prefs('1;0;0;0;'.$map->sc.';'.$map->taille.';0;0;0');
}


include_once(TEMPLATE_PATH.'carte.tpl.php');
$tpl = tpl_carte::getinstance();

$tpl->page_title = ($title != '') ? 'Carte: '.$title.'': 'EU2: Carte';
$tpl->navigation(); // menu carte
$tpl->maparea();	// la carte

$map->update_session();

/// PARTIE NON EXPORTÉ ///

$mysql_result = $map->init_map();
$map->TabData = '';

function map_additem($ss, $data) {
    global $map;
    if ($map->itineraire) {
        if ($ss == $map->parcours[1][0]) $map->parcours[2][0] = true;
        if ($ss == $map->parcours[1][count($map->parcours[1])-1]) $map->parcours[2][1] = true;
    }
//  $map->TabData.='TabData['.$ss.'] =\''.$map->ss_info($ss,$data).'\';'."\n"; à voir
    $map->TabData.="TabData[\"$ss\"] =\"".$map->ss_info($ss,$data)."\";\n";
}

while ($line=mysql_fetch_assoc($mysql_result)) {
    if ($CurrSS == 0) $CurrSS = $line['POSIN'];

    if ($line['POSIN'] != $CurrSS) {
        $map->add_ss(false,'map_additem');
        $CurrSS   = $line['POSIN'];
    }

    $ID   = $line['ID'];
    $ss   = $line['POSIN'];

    $CurrSS_a[$ID] = $line;
    $CurrSS_a[$ID]['type'] = $map->ss_type($line);
    if (isset ($CurrSS_a[$CurrSS_a[$ID]['type']]))
        $CurrSS_a[$CurrSS_a[$ID]['type']]++;
        else
        $CurrSS_a[$CurrSS_a[$ID]['type']]=1;
}
mysql_free_result($mysql_result);

$map->add_ss(false,'map_additem'); // last one

// Vortex restants...
if ($map->vortex && count($vortex_a) >0)
    foreach($vortex_a as $k => $v) {
        $map->add_ss($k,'map_additem');
    }

if ($map->itineraire) { // forçage si le parcours est en zone "désertique"..
    if (!$map->parcours[2][0]) $map->add_ss($map->parcours[1][0],'map_additem'); // départ
    if (!$map->parcours[2][1]) $map->add_ss($map->parcours[1][count($map->parcours[1])-1],'map_additem'); // arrivée
}
unset($vortex_a);
/// PARTIE NON EXPORTÉ ///

//************
//Itinéraire
//************

$tpl->itineraire_header();


$mysql_result = DataEngine::sql('SELECT `ID`,`Flotte` from `SQL_PREFIX_itineraire` where `Joueur`=\''.$_SESSION['_login'].'\' ORDER BY `Flotte` ASC');
while ($ligne=mysql_fetch_assoc($mysql_result)) $array[$ligne['ID']] = $ligne['Flotte'];

if (is_array($array)) $tpl->SelectOptions($array,$map->loadfleet);


$tpl->itineraire_form();

if( $map->itineraire ) {
    $tpl->Parcours_Start($map->parcours[1][0]);

    $i = $dt = 0; $last = count($map->parcours[1])-1;
    foreach($map->parcours[1] as $k => $v) {
        if ( ($k%2)==0) continue;
        if ( $k==$last) continue;
        $i++;
        $ss1 = $map->Parcours()->get_coords_part($map->parcours[1][$k-1]);
        $ss2 = $map->Parcours()->get_coords_part($map->parcours[1][$k]);

        $d = $map->Parcours()->Calcul_Distance($ss1,$ss2);

        $tpl->Parcours_Row($i,$map->parcours[1][$k],$map->parcours[1][$k+1],$d);

        $dt += $d;
    }//foreach
    $ss1 = $map->Parcours()->get_coords_part($map->parcours[1][$last-1]);
    $ss2 = $map->Parcours()->get_coords_part($map->parcours[1][$last]);
    $d = $map->Parcours()->Calcul_Distance($ss1,$ss2);

    $dt += $d; $db = $map->Parcours()->Calcul_Distance($map->IN,$map->OUT);
    $dd = $db-$dt;
    $tpl->Parcours_End($d,$db,$dt,$dd,$map->parcours[1][$last]);

} // $map->itineraire 

$tpl->Legend();
$tpl->javascript($map->TabData);
$tpl->DoOutput();


