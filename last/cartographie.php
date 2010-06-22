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
require_once(CLASS_PATH.'cartographie.class.php');
require_once(CLASS_PATH.'map.class.php');

if (!DataEngine::CheckPermsOrDie('CARTOGRAPHIE'));

$map = map::getinstance();
$carto = cartographie::getinstance();
$lng = language::getinstance()->GetLngBlock('cartographie');

if (isset($_POST['massedit'])) {

    foreach($_POST['item'] as $k => $arr) {
        if ($arr['delete']) {
            $carto->Delete_Entry($k, $arr['type']);
        } else
        if ($arr['edit']) {
            unset($arr['edit']);
            if (isset($arr['TROOP'])) $arr['TROOP'] = DataEngine::strip_number($arr['TROOP']);
            $carto->Edit_Entry($k,$arr);
        }
    }
    $carto->Boink(ROOT_URL.basename(__file__).'?'.Get_string());
}

//--- Modification manuelle ----------------------------------------------------
//------------------------------------------------------------------------------
//--- Insertion des données ----------------------------------------------------


if (isset($_POST['Type'])) {

    if (isset ($_POST['COORIN']))      $_POST['COORIN']       = gpc_esc($_POST['COORIN']);
    if (isset ($_POST['COOROUT']))     $_POST['COOROUT']      = gpc_esc($_POST['COOROUT']);
    if (isset ($_POST['USER']))        $_POST['USER']         = gpc_esc($_POST['USER']);
    if (isset ($_POST['EMPIRE']))      $_POST['EMPIRE']       = gpc_esc($_POST['EMPIRE']);
    if (isset ($_POST['INFOS']))       $_POST['INFOS']        = gpc_esc($_POST['INFOS']);

    // SS brut
    if ($_POST['phpparser'] == 1) {
        $carto->add_solar_ss(gpc_esc($_POST['importation']));
        $carto->Boink(ROOT_URL.basename(__file__).'?'.Get_string());
    } // SS brut

    // check if all needed fields...
    if ($_POST['phpparser'] != 1) {
        if ($_POST['Type'] != 1 and $_POST['COORIN'] == '')  $carto->AddErreur($lng['err_coorin_needed']);
        if ($_POST['Type'] != 1 and $_POST['COOROUT'] != '') $carto->AddErreur($lng['err_coorout_filled']);
        if ($_POST['Type'] == 1 and $_POST['COOROUT'] == '') $carto->AddErreur($lng['err_coorout_needed']);
        if ($_POST['Type'] == 0 and $_POST['USER'] == '')    $carto->AddErreur($lng['err_player_needed']);

        if ($carto->Messages()>0) $carto->Boink(ROOT_URL.basename(__file__).'?'.Get_string());
    }

    switch ($_POST['Type']) {
        case '0': // Joueur
        case '3': // Allié
        case '5': // Ennemi
            $carto->add_player($_POST['COORIN'], $_POST['INFOS'], $_POST['USER'],$_POST['EMPIRE']);
            break;
        case '1': // vortex
            $carto->add_vortex($_POST['COORIN'],$_POST['COOROUT']);
            break;
        case '2': // planet
            foreach(DataEngine::a_Ressources() as $id => $dummy) $Ress[$id] = gpc_esc($_POST['RESSOURCE'.$id]);
            $carto->add_planet($_POST['COORIN'], $Ress);
            break;
        case '4': // asteroid
            foreach(DataEngine::a_Ressources() as $id => $dummy) $Ress[$id] = gpc_esc($_POST['RESSOURCE'.$id]);
            $carto->add_asteroid($_POST['COORIN'], $Ress);
            break;
        case '6': // flotte PNJ
            $carto->add_PNJ($_POST['COORIN'], $_POST['USER'],$_POST['INFOS']);
            break;
        default:
            $carto->AddWarn($lng['err_unknown_type']);

    }
    if ($carto->Messages()>0) $carto->Boink(ROOT_URL.basename(__file__).'?'.Get_string());
}

//--- Insertion des données ----------------------------------------------------
//------------------------------------------------------------------------------
//--- Listing ------------------------------------------------------------------

$where = 'WHERE 1=1 ';
$Recherche = array();
if (DataEngine::CheckPerms('CARTOGRAPHIE_SEARCH')) {
    if(isset($_GET['ResetSearch']) && $_GET['ResetSearch']!='') {
        if (isset($_COOKIE['Recherche'])) {
            foreach ($_COOKIE['Recherche'] as $key => $value) {
                SetCookie('Recherche['.$key.']','',time()-1,ROOT_URL);
            }
        }
        $carto->boink(ROOT_URL.basename(__file__));
    }
    if (isset($_COOKIE['Recherche']))
        foreach ($_COOKIE['Recherche'] as $key => $value)
            $Recherche[$key] = stripslashes($value);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
            !isset ($_POST['Recherche']['Moi']))
        $_POST['Recherche']['Moi'] = '';

    if (isset($_POST['Recherche']['Troop']))
        $_POST['Recherche']['Troop'] = DataEngine::strip_number($_POST['Recherche']['Troop']);

    if (isset ($_POST['Recherche']))
        foreach ($_POST['Recherche'] as $key => $value) {
            $value = gpc_esc($value);
            if ($value != '') {
                SetCookie('Recherche['.$key.']',$value,time()+3600*24,ROOT_URL);
                $Recherche[$key] = $value;
            } else {
                SetCookie('Recherche['.$key.']','',time()-10,ROOT_URL);
                unset($Recherche[$key]);
            }
        }

    if ($Recherche['Troop']>0) $Recherche['Type'] = '0,3,5';

    $fieldtable = array();
    $fieldtable['Status'] = '`Inactif`=\'%s\' ';
    $fieldtable['Type']   = '`TYPE` IN (%s) ';
    $fieldtable['User']   = '`USER` like \'%%%s%%\' ';
    $fieldtable['Empire'] = '`EMPIRE` like \'%%%s%%\' ';
    $fieldtable['Infos']  = '`INFOS` like \'%%%s%%\' ';
    $fieldtable['Note']   = '`NOTE` like \'%%%s%%\' ';
    $fieldtable['Troop']   = '`TROOP`<%d AND `TROOP`>=0 ';
    foreach ($Recherche as $key => $value) {
        $value = sqlesc($value);

        switch ($key) {
            case 'Pos':
                if ($key=='Pos' && $Recherche['Rayon']!='') {
                    $Recherche['Rayon'] = min($Recherche['Rayon'],10);
                    $ListeCoor = implode(',',$map->Parcours()->GetListeCoorByRay($Recherche['Pos'],$Recherche['Rayon']));
                    $where.= 'AND (POSIN IN ('.$ListeCoor.') OR POSOUT IN ('.$ListeCoor.'))';
                } else if ($key=='Pos')
                    $where.= 'AND (POSIN=\''.$value.'\' OR POSOUT=\''.$value.'\') ';
                break;
            case 'Moi':
                $where.= ' AND UTILISATEUR=\''.mb_strtolower($_SESSION['_login'], 'utf8').'\' ';
                break;
            case 'Status':
            case 'Type':
                if ($value==-1) break;
                $where.= 'AND '.sprintf($fieldtable[$key], $value);
                break;
            default:
                if (isset ($fieldtable[$key])) {
                    switch ($key) {
                        case 'Troop':
                            $value = DataEngine::strip_number($value);
                            if ($value==0) break;
                        default:
                            $where.= 'AND '.sprintf($fieldtable[$key], $value);
                    }
                }
        }
    }

} // SEARCH

//--- Listing -----------------------------------------------------------------
//------------------------------------------------------------------------------
//--- partie html --------------------------------------------------------------

include_once(TEMPLATE_PATH.'cartographie.tpl.php');
$tpl = tpl_cartographie::getinstance();
$tpl->AddToRow(bulle($lng['add_items_bulle']), 'bulle');

$lngmain = language::getinstance()->GetLngBlock('dataengine');
$tpl->AddToRow($tpl->SelectOptions2($lngmain['types']['dropdown'],''), 'Type');
$tpl->AddToRow(bulle($lng['add_items_bulle1']), 'bulle1');
$tpl->AddToRow(bulle($lng['add_items_bulle2']), 'bulle2');
$tpl->AddToRow(bulle($lng['add_items_bulle3']), 'bulle3');
$tpl->AddToRow(bulle($lng['add_items_bulle4']), 'bulle4');
$tpl->AddToRow(bulle($lng['add_items_bulle5']), 'bulle5');

$tpl->PushRow();

//------------------------------------------------------------------------------

if (DataEngine::CheckPerms('CARTOGRAPHIE_SEARCH')) {
    $tpl->SearchForm();
    if (!isset ($Recherche['Status'])) $Recherche['Status'] = -1;
    if (!isset ($Recherche['Type'])) $Recherche['Type'] = -1;
    $tpl->AddToRow(($Recherche['Status']==-1 ? ' selected="true"':''), 'status-1');
    $tpl->AddToRow(($Recherche['Status']==0 ? ' selected="true"':''), 'status0');
    $tpl->AddToRow(($Recherche['Status']==1 ? ' selected="true"':''), 'status1');
    $tpl->AddToRow($tpl->SelectOptions2($lngmain['types']['dropdown'],$Recherche['Type']), 'Type');
    $tpl->AddToRow($Recherche['Pos'], 'Pos');
    $tpl->AddToRow($Recherche['Rayon'], 'Rayon');
    $tpl->AddToRow(htmlentities($Recherche['User'], ENT_QUOTES, 'utf-8'), 'User');
    $tpl->AddToRow(htmlentities($Recherche['Empire'], ENT_QUOTES, 'utf-8'), 'Empire');
    $tpl->AddToRow(htmlentities($Recherche['Infos'], ENT_QUOTES, 'utf-8'), 'Infos');
    $tpl->AddToRow(htmlentities($Recherche['Note'], ENT_QUOTES, 'utf-8'), 'Note');
    $tpl->AddToRow(DataEngine::format_number($Recherche['Troop'], true), 'Troop');
    $tpl->AddToRow(($Recherche['Moi']==1 ? ' checked':''), 'checkedmoi');
    $tpl->PushRow();
}
//------------------------------------------------------------------------------
$tpl->SearchResult();
$PageCurr = (isset($_GET['page'])) ? max(intval($_GET['page']),1): 1;
$Maxline = 20;
$limit = ' LIMIT '.(($PageCurr-1)*$Maxline).','.$Maxline;

$query = 'SELECT count(*) as Nb from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.ID=b.pID) '.$where;
$mysql_result = DataEngine::sql($query);

$ligne=mysql_fetch_assoc($mysql_result);
$NbLigne = $ligne['Nb'];
$MaxPage = ceil($NbLigne / $Maxline)-1;
if($PageCurr > $MaxPage+1)
    $PageCurr = $MaxPage+1;
else if ($PageCurr < 1)
    $PageCurr = 1;

$tpl->AddToRow($tpl->GetPagination($PageCurr, $MaxPage+1), 'pagination');

$invert_sort = array(''=>'ASC','DESC' => 'ASC', 'ASC' => 'DESC');
$sort_key = array('type', 'user', 'empire', 'infos', 'note', 'date', 'water', 'batiments', 'troop');

if ($Recherche['Troop']>0)
    $sort='ORDER BY Troop_date DESC';
else
    $sort='ORDER BY DATE DESC';

foreach($sort_key as $v) {
    if (isset($_GET['sort']) && in_array($_GET['sort'][$v],$invert_sort))
        $sort= 'ORDER BY '.$v.' '.$_GET['sort'][$v].' ';
    else if (isset($_GET['sort'][$v]))
        $_GET['sort'][$v] = '';

    $newvalue = array('sort' => array($v=>$invert_sort[$_GET['sort'][$v]]));
    $tpl->AddToRow(Get_string($newvalue), 'sort_'.$v);
}

$tpl->PushRow();

$sql='SELECT UNIX_TIMESTAMP(a.DATE) as udate,a.*,b.* from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.ID=b.pID) '.$where.$sort.$limit;
$mysql_result = DataEngine::sql($sql);

$lngmain = language::getinstance()->GetLngBlock('dataengine');
$a_Ress  = DataEngine::a_ressources();
$stype   = $lngmain['types']['string'];
$i=0;
$cmdinput = '<input class="color_row%%rowA%%" type="checkbox" value="1" id="item[%%id%%][%%cmd%%]" name="item[%%id%%][%%cmd%%]" %%bulle%%/>';
while ($ligne=mysql_fetch_assoc($mysql_result)) {
    $coords= $ligne['POSIN'].'-'.$ligne['COORDET'];
    switch ($ligne['TYPE']) {
        case 1:
            $coords= $ligne['POSIN'].'-'.$ligne['COORDET'].'<br/>'.$ligne['POSOUT'].'-'.$ligne['COORDETOUT'];
        case 6:
            $tpl->SetRowModelTypeC();
            break;
        case 2:
        case 4:
            $tpl->SetRowModelTypeA();
            foreach ($a_Ress as $k => $v)
                $tpl->AddToRow($tpl->GetRessources($ligne[$v['Field']], $v), $v['Field']);
            $ligne['water'] = $ligne['water'] != '' ? $ligne['water'].' %':'-';
            break;
        default:
            $tpl->SetRowModelTypeB();
    }


    $tpl->AddToRow($ligne['TYPE'], 'typeid');
    $tpl->AddToRow($stype[$ligne['TYPE']], 'type');
    $tpl->AddToRow($coords, 'coords');
    if ($ligne['EMPIRE']) {
        $shw_emp = wordwrap($ligne['EMPIRE'], 20, '<br/>', true);
        $tpl->AddToRow($ligne['USER'] ? $ligne['USER'].'<br/>'.$shw_emp : $shw_emp, 'player');
    } else
        $tpl->AddToRow($ligne['USER'] ? $ligne['USER'] : '-', 'player');
    $tpl->AddToRow($ligne['INFOS'] ? $ligne['INFOS'] : '-', 'infos');
    $tpl->AddToRow($ligne['NOTE'], 'notes');
    $tpl->AddToRow($ligne['water'], 'water');
    $tpl->AddToRow($ligne['batiments'], 'batiments');
    $tpl->AddToRow(DataEngine::format_number($ligne['troop'], true), 'troop');
    if (isset ($ligne['troop_date']))
        $tpl->AddToRow(bulle(sprintf($lng['search_troopdate'], date($lng['search_date_long_format']),$ligne['troop_date'])), 'troop_date');
    else
        $tpl->AddToRow('', 'troop_date');


    $tmp = sprintf($lng['search_userdate'], $ligne['UTILISATEUR'], date($lng['search_date_long_format'],$ligne['udate']));
    $tpl->AddToRow(bulle($tmp), 'userdate');
    $tpl->AddToRow(date($lng['search_date_short_format'],$ligne['udate']), 'udate');
//    $tpl->AddToRow($ligne['UTILISATEUR'], 'user');


    if (Members::CheckPerms('CARTOGRAPHIE_DELETE')) {
        $tpl->AddToRow($cmdinput, 'cmd_delete');
        $tpl->AddToRow('delete', 'cmd');
        $tpl->AddToRow(bulle($lng['search_bulle_cmd_delete']), 'bulle');
    } else $tpl->AddToRow('', 'cmd_delete');

    if (Members::CheckPerms('CARTOGRAPHIE_EDIT')) {
        $tpl->AddToRow($cmdinput, 'cmd_edit');
        $tpl->AddToRow('edit', 'cmd');
        $tpl->AddToRow(bulle($lng['search_bulle_cmd_edit']), 'bulle');
    } else $tpl->AddToRow('', 'cmd_edit');

    $tpl->AddToRow($i%2, 'rowA');
    $tpl->AddToRow(($i+1)%2, 'rowB');
    $tpl->AddToRow($ligne['ID'], 'id');
    $tpl->PushRow();
    $i++;
}

$tpl->SearchResult_End();
$tpl->AddToRow($tpl->GetPagination($PageCurr, $MaxPage+1), 'pagination');
$tpl->PushRow();

$tpl->DoOutput();