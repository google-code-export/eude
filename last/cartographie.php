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

//for ($i=0;$i<50;$i++)
//output::Messager('In work, OMG'.$i);
//output::Messager('really ;)');
//output::Boink('%ROOT_URL%');

if (!DataEngine::CheckPerms('CARTOGRAPHIE')) {
    if (DataEngine::CheckPerms('CARTE'))
        output::Boink(ROOT_URL.'Carte.php');
    else
        output::Boink(ROOT_URL.'Mafiche.php');
}

$map = map::getinstance();
$carto = cartographie::getinstance();

//$carto->Edit_Entry('31917',
//$carto->Edit_Entry('1-95-15-79',
//$carto->Edit_Entry('1:95:15:79',
//            array('INFOS'=> time(),
//                'NOTE'=> 'incrustation...',
//                'INFOS_'=> time(),
//                'xxx_999'=> time(),
//                'xxx/*-+'=> time(),
//                )
//        );
//    $carto->Boink('');


if (isset($_POST['massedit'])) {


        foreach($_POST['item'] as $k => $arr) {
            if ($arr['delete']) {
                $carto->Delete_Entry($k, $arr['type']);
            } else
            if ($arr['edit']) {
                unset($arr['edit']);
                $carto->Edit_Entry($k,$arr);
            }
            

        }
        $carto->Boink(ROOT_URL.basename(__file__), 'not implemented yet !');
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
        $carto->Boink(ROOT_URL.basename(__file__));
    } // SS brut

    // check if all needed fields...
    if ($_POST['phpparser'] != 1) {
        if ($_POST['Type'] != 1 and $_POST['COORIN'] == '')  $carto->AddErreur('Les coordonnés d\'entrée doivent-être renseigné');
        if ($_POST['Type'] != 1 and $_POST['COOROUT'] != '') $carto->AddErreur('Les coordonnés de sortie ne sont à renseigner que pour les Vortex');
        if ($_POST['Type'] == 1 and $_POST['COOROUT'] == '') $carto->AddErreur('Il faut impérativement renseigner Les coordonnés de sortie pour les Vortex');
        if ($_POST['Type'] == 0 and $_POST['USER'] == '')    $carto->AddErreur('Merci de renseigner le nom du joueur');

        if ($carto->Messages()>0) $carto->Boink(ROOT_URL.basename(__file__));
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
            $carto->add_PNJ($_POST['COORIN'], $_POST['USER'],$_POST['EMPIRE']);
            break;
        default:
            $carto->AddWarn('Type demandé non pris en charge !');

    }
    if ($carto->Messages()>0) $carto->Boink(ROOT_URL.basename(__file__));
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
        $carto->boink(ROOT_URL.basename(__file__));
    }
    if (isset($_COOKIE['Recherche']))
        foreach ($_COOKIE['Recherche'] as $key => $value)
            $Recherche[$key] = $value;

    if (isset ($_POST['Recherche']))
        foreach ($_POST['Recherche'] as $key => $value) {
            $value = gpc_esc($value);
            if ($value != '') {
                SetCookie('Recherche['.$key.']',$_POST['Recherche'][$key],time()+3600*24,ROOT_URL);
                $Recherche[$key] = $_POST['Recherche'][$key];
            } else {
                SetCookie('Recherche['.$key.']',$_POST['Recherche'][$key],time()-10,ROOT_URL);
                unset($Recherche[$key]);
            }
        }

    $fieldtable = array();
    $fieldtable['Status'] = '`Inactif`=\'%s\' ';
    $fieldtable['Type']   = '`TYPE` IN (%d) ';
    $fieldtable['User']   = '`USER` like \'%%%s%%\' ';
    $fieldtable['Empire'] = '`EMPIRE` like \'%%%s%%\' ';
    $fieldtable['Infos']  = '`INFOS` like \'%%%s%%\' ';
    $fieldtable['Note']   = '`NOTE` like \'%%%s%%\' ';
    foreach ($Recherche as $key => $value) {
        $value = gpc_esc($value);

        switch ($key) {
            case 'Pos':
                if ($key=='Pos' && $Recherche['Rayon']!='') {
                    $ListeCoor = implode(',',$map->Parcours()->GetListeCoorByRay($Recherche['Pos'],max($Recherche['Rayon'],10)));
                    $where.= 'AND (POSIN IN ('.$ListeCoor.') OR POSOUT IN ('.$ListeCoor.'))';
                } else if ($key=='Pos')
                    $where.= 'AND (POSIN=\''.$value.'\' OR POSOUT=\''.$value.'\') ';
                break;
            case 'Moi':
                $where.= ' AND UTILISATEUR=\''.strtolower($_SESSION['_login']).'\' ';
                break;
            case 'Status':
            case 'Type':
                if ($value==-1) break;
                $where.= 'AND '.sprintf($fieldtable[$key], $value);
                break;
            default:
                if (isset ($fieldtable[$key]))
                    $where.= 'AND '.sprintf($fieldtable[$key], $value);
        }
    }

} // SEARCH

$sort=array();
$sort[] = 'INACTIF ASC';
if (isset ($_GET['sort']))
    foreach ($_GET['sort'] as $key => $value) {
        if ($value != 'ASC' && $value != 'DESC') continue;
        if (preg_match('/[^a-zA-Z_]+/', $key)>0) continue;
        $sort[] = $key.' '.$value;
    }
$sort[] = 'ID DESC';
$sort = 'ORDER BY '.implode(', ', $sort);

//--- Listing & tri ------------------------------------------------------------
//------------------------------------------------------------------------------
//--- partie html --------------------------------------------------------------

include_once(TEMPLATE_PATH.'cartographie.tpl.php');
$tpl = tpl_cartographie::getinstance();
$tpl->AddToRow(bulle("Coller ici les détails d'une planète, joueur ou d'un vortex<br/>(Ctrl+A puis Ctrl+C après avoir ouvert une fiche)"), 'bulle');

$lngmain = language::getinstance()->GetLngBlock('dataengine');
$tpl->AddToRow($tpl->SelectOptions2($lngmain['types']['dropdown'],''), 'Type');
$tpl->PushRow();

//------------------------------------------------------------------------------
$tpl->SearchForm();
$tpl->AddToRow($tpl->SelectOptions2($lngmain['types']['dropdown'],$Recherche['Type']), 'Type');
$tpl->PushRow();
//------------------------------------------------------------------------------

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


FB::info($NbLigne, 'NB lines');
FB::info($MaxPage, 'NB pages');
FB::info($limit, 'sql limit');
$tpl->AddToRow($tpl->GetPagination($PageCurr, $MaxPage+1), 'pagination');
//$tpl->AddToRow(max($PageCurr-1,1), 'pg_prec');
//$tpl->AddToRow(max($PageCurr,0), 'curpage');
//$tpl->AddToRow($MaxPage+1, 'maxpage');
//$tpl->AddToRow(min($PageCurr+1,$MaxPage+1), 'pg_next');
//$tpl->AddToRow($MaxPage+1, 'pg_end');
$tpl->PushRow();

$sql='SELECT * from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.ID=b.pID) '.$where.$sort.$limit;
$mysql_result = DataEngine::sql($sql);
$tpl->SearchResult();

$lngmain = language::getinstance()->GetLngBlock('dataengine');
$a_Ress  = DataEngine::a_ressources();
$stype   = $lngmain['types']['string'];
$i=0;
$cmdinput = '<input class="color_row%%rowA%%" type="checkbox" value="1" id="item[%%id%%][%%cmd%%]" name="item[%%id%%][%%cmd%%]" %%bulle%%/>';
while ($ligne=mysql_fetch_assoc($mysql_result)) {
    switch ($ligne['TYPE']) {
        case 1:
            $tpl->SetRowModelTypeA();
            $coords= $ligne['POSIN'].'-'.$ligne['COORDET'].'<br/>'.$ligne['POSOUT'].'-'.$ligne['COORDETOUT'];
            break;
        case 2:
        case 4:
            $tpl->SetRowModelTypeB();
            $coords= $ligne['POSIN'].'-'.$ligne['COORDET'];
            foreach ($a_Ress as $k => $v)
                $tpl->AddToRow($tpl->GetRessources($ligne[$v['Field']], $v), $v['Field']);
            break;
        default:
            $tpl->SetRowModelTypeA();
            $coords= $ligne['POSIN'].'-'.$ligne['COORDET'];
    }


    $tpl->AddToRow($ligne['TYPE'], 'typeid');
    $tpl->AddToRow($stype[$ligne['TYPE']], 'type');
    $tpl->AddToRow($coords, 'coords');
    if ($ligne['EMPIRE'])
        $tpl->AddToRow($ligne['USER'] ? $ligne['USER'].'<br/>'.$ligne['EMPIRE'] : $ligne['EMPIRE'], 'player');
    else
        $tpl->AddToRow($ligne['USER'] ? $ligne['USER'] : '-', 'player');
    $tpl->AddToRow($ligne['INFOS'] ? $ligne['INFOS'] : '-', 'infos');
    $tpl->AddToRow($ligne['NOTES'], 'notes');

    $tpl->AddToRow(bulle($ligne['DATE']), 'date');
    $tpl->AddToRow($ligne['UTILISATEUR'], 'user');


    if (Members::CheckPerms('CARTOGRAPHIE_DELETE')) {
        $tpl->AddToRow($cmdinput, 'cmd_delete');
        $tpl->AddToRow('delete', 'cmd');
        $tpl->AddToRow(bulle('Delete it ?!'), 'bulle');
    } else $tpl->AddToRow('', 'cmd_delete');

    if (Members::CheckPerms('CARTOGRAPHIE_EDIT')) {
        $tpl->AddToRow($cmdinput, 'cmd_edit');
        $tpl->AddToRow('edit', 'cmd');
        $tpl->AddToRow(bulle('Edit it ?!'), 'bulle');
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