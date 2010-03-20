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
require_once(CLASS_PATH.'parser.class.php');
require_once(CLASS_PATH.'cartographie_new.class.php');
require_once(CLASS_PATH.'map.class.php');

//$_SESSION['messager'] = 'Rien a voir pour l\'instant, OMG';
//output::Boink('%ROOT_URL%');

if (!DataEngine::CheckPerms('CARTOGRAPHIE')) {
    if (DataEngine::CheckPerms('CARTE'))
        output::Boink(ROOT_URL.'Carte.php');
    else
        output::Boink(ROOT_URL.'Mafiche.php');
}

$map = map::getinstance();
$carto = cartographie::getinstance();


//------------------------------------------------------------------------------
//--- Insertion des données ----------------------------------------------------


if (isset($_POST['Type'])) {

    if (isset ($_POST['importation'])) $_POST['importation']  = gpc_esc($_POST['importation']);
    if (isset ($_POST['COORIN']))      $_POST['COORIN']       = gpc_esc($_POST['COORIN']);
    if (isset ($_POST['COOROUT']))     $_POST['COOROUT']      = gpc_esc($_POST['COOROUT']);
    if (isset ($_POST['USER']))        $_POST['USER']         = gpc_esc($_POST['USER']);
    if (isset ($_POST['EMPIRE']))      $_POST['EMPIRE']       = gpc_esc($_POST['EMPIRE']);
    if (isset ($_POST['INFOS']))       $_POST['INFOS']        = gpc_esc($_POST['INFOS']);
    
//    // SS brut
//    if ($_POST['phpparser'] == 1) {
//        $carto->add_solar_ss($_POST['importation']);
//        _Boink(ROOT_URL.basename(__file__));
//    } // SS brut
//
//    // check if all needed fields...
//    if ($_POST['phpparser'] != 1) {
//        if ($_POST['Type'] != 1 and $_POST['COORIN'] == '') $erreur = 'Les coordonnés d\'entrée doivent-être renseigné';
//        if ($_POST['Type'] != 1 and $_POST['COOROUT'] != '') $erreur = 'Les coordonnés de sortie ne sont à renseigner que pour les Vortex';
//        if ($_POST['Type'] == 1 and $_POST['COOROUT'] == '') $erreur = 'Il faut impérativement renseigner Les coordonnés de sortie pour les Vortex';
//        if ($_POST['Type'] == 0 and $_POST['USER'] == '') $erreur = 'Merci de renseigner le nom du joueur';
//    }

    // TODO ....

    /*
$stype[0] = 'Joueur';
$stype[1] = 'Vortex';
$stype[2] = 'Planète';
$stype[3] = 'Alliés';
$stype[4] = 'Astéroïde';
$stype[5] = 'Ennemi';
$stype[6] = 'PNJ';
    */
    switch ($_POST['Type']) {
        case '0': // Joueur
        case '3': // Allié
        case '5': // Ennemi
            $carto->add_player($_POST['COORIN'], $_POST['INFOS'], $_POST['USER'],$_POST['EMPIRE']);
            _Boink(ROOT_URL.basename(__file__));
            break;
        case '1': // vortex
            $carto->add_vortex($_POST['COORIN'],$_POST['COOROUT']);
            _Boink(ROOT_URL.basename(__file__));
            break;
        case '2': // planet
            foreach(DataEngine::a_Ressources() as $id => $dummy) $Ress[$id] = gpc_esc($_POST['RESSOURCE'.$id]);
            $carto->add_planet($_POST['COORIN'], $Ress);
            _Boink(ROOT_URL.basename(__file__));
            break;
        case '4': // asteroid
            foreach(DataEngine::a_Ressources() as $id => $dummy) $Ress[$id] = gpc_esc($_POST['RESSOURCE'.$id]);
            $carto->add_asteroid($_POST['COORIN'], $Ress);
            _Boink(ROOT_URL.basename(__file__));
            break;
        case '6': // flotte PNJ
            $carto->add_PNJ($_POST['COORIN'], $_POST['USER'],$_POST['EMPIRE']);
            _Boink(ROOT_URL.basename(__file__));
            break;
        default:
            $carto->AddWarn('Type demandé non pris en charge !');
//            _Boink(ROOT_URL.basename(__file__));

    }
}

//--- Insertion des données ----------------------------------------------------
//------------------------------------------------------------------------------
//--- Listing & tri ------------------------------------------------------------

$where = 'WHERE 1=1 ';
$Recherche = array();
if (DataEngine::CheckPerms('CARTOGRAPHIE_SEARCH')) {
    if(isset($_GET['ResetSearch']) && $_GET['ResetSearch']!='') {
        if (isset($_COOKIE['Recherche'])) {
            foreach ($_COOKIE['Recherche'] as $key => $value) {
                SetCookie('Recherche['.$key.']','',time()-1,ROOT_URL);
            }
        }
        output::boink(ROOT_URL.basename(__file__));
    }
    if (isset($_COOKIE['Recherche']))
        foreach ($_COOKIE['Recherche'] as $key => $value)
            $Recherche[$key] = $value;

    $fieldtable = array();
    $fieldtable['Statut'] = '`Inactif`=\'%s\' ';
    $fieldtable['Type']   = '`TYPE` IN (%d) ';
    $fieldtable['User']   = '`USER` like \'%%%s%%\' ';
    $fieldtable['Empire'] = '`EMPIRE` like \'%%%s%%\' ';
    $fieldtable['Infos']  = '`INFOS` like \'%%%s%%\' ';
    $fieldtable['Note']   = '`NOTE` like \'%%%s%%\' ';
    if (isset ($_POST['Recherche']))
        foreach ($_POST['Recherche'] as $key => $value) {
            $value = gpc_esc($value);

            switch ($key) {
                case 'Pos':
                case 'Rayon':
                    SetCookie('Recherche['.$key.']',$_POST['Recherche'][$key],time()+3600*24,ROOT_URL);
                    $Recherche[$key] = $_POST['Recherche'][$key];
                    break;
                case 'Moi':
                    SetCookie('Recherche['.$key.']',$_POST['Recherche'][$key],time()+3600*24,ROOT_URL);
                    $Recherche[$key] = $_POST['Recherche'][$key];
                    $where.= ' AND UTILISATEUR=\''.strtolower($_SESSION['_login']).'\' ';
                    break;
                default:
                    SetCookie('Recherche['.$key.']',$_POST['Recherche'][$key],time()+3600*24,ROOT_URL);
                    $Recherche[$key] = $_POST['Recherche'][$key];
                    $where.= 'AND '.sprintf($fieldtable[$key], sqlesc($value, true));
            }
        }

    //Traitement de recherche par position et rayon
    if ($Recherche['Pos'] != '') {
        if(!is_numeric($Recherche['Rayon']) ||($Recherche['Rayon']<0) ) $Recherche['Rayon']='';
        if($Rech['Rayon']=='') {
            $where.= 'AND (POSIN like \''.$Recherche['Pos'].'\' OR POSOUT like \''.$Recherche['Pos'].'\') ';
        } else {
            $ListeCoor = implode(',',$map->Parcours()->GetListeCoorByRay($Recherche['Pos'],$Recherche['Rayon']));
            $where.= 'AND (POSIN IN ('.$ListeCoor.') OR POSOUT IN ('.$ListeCoor.')) ';
        }
    }

    // TOD ? Traitement recherche planete uniquement si type = planete
} // SEARCH

$sort=array();
$sort[] = 'INACTIF ASC';
// TODO Check security issues... /!\
if (isset ($_GET['SORT']))
    foreach ($_GET['SORT'] as $key => $value) {
        if ($value != 'ASC' && $value != 'DESC') continue;
        $sort[] = ''.$key.' '.$value;
    }
$sort[] = 'ID DESC';
$sort = 'ORDER BY '.implode(', ', $sort);

//--- Listing & tri ------------------------------------------------------------
//------------------------------------------------------------------------------
//--- partie html --------------------------------------------------------------

include_once(TEMPLATE_PATH.'cartographie.tpl.php');
$tpl = tpl_cartographie::getinstance();
$tpl->AddToRow(bulle("Coller ici les détails d'une planète, joueur ou d'un vortex<br/>(Ctrl+A puis Ctrl+C après avoir ouvert une fiche)"), 'bulle');
$tpl->PushRow(); // -> SetRowInsertManual

$tpl->SelectOptions($cctype,$_POST['Type'],'Type');
$tpl->PushRow(); // -> SetRowInsertManualExtended



$tpl->PushRow(); // -> null
$tpl->DoOutput();

function _Boink() {
    $carto = cartographie::getinstance();
    $w = $carto->Warns();
    $i = $carto->Infos();
    $m = ($w != '' && $i != '') ? $w.'<br/>'.$i: '';
    $m = ($w != '' && $i == '') ? $w: $m;
    $m = ($w == '' && $i != '') ? $i: $m;
    $_SESSION['messager'] = $m;
    output::Boink(ROOT_URL.basename(__file__));
}