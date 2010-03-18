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



//--- Insertion des données ----------------------------------------------------
//------------------------------------------------------------------------------
//-- Listing & tri -------------------------------------------------------------

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

    //Traitement recherche planete uniquement si type = planete
    if($Recherche['Type']==2) {
        foreach(DataEngine::a_ressources() as $id => $Ress) {
            if (($Recherche['Ressource'.$id] != '') && ($Recherche['Type'] != '-1')) {
                $where.= ' AND (SELECT CASE WHEN '.$Ress['Field'].'="beaucoup" THEN 70 ';
                $where.= 'WHEN '.$Ress['Field'].'="normal" THEN 40 ';
                $where.= 'WHEN '.$Ress['Field'].'="peu" THEN 20 ';
                $where.= 'ELSE substring('.$Ress['Field'].',1,length('.$Ress['Field'].')-1) ';
                $where.= ' END) '.$Rech['Ressource'.$id];
            }
            else $Recherche['Ressource'.$id] = '-1';
        }
    } else
        for($id=0;$id<10;$id++)
            $Recherche['Ressource'.$id] = '-1';
} // SEARCH

$sort=array();
$sort[] = 'INACTIF ASC';
// TODO Check security issues... /!\
foreach ($_GET['SORT'] as $key => $value) {
    if ($value != 'ASC' && $value != 'DESC') continue;
    $sort[] = ''.$key.' '.$value;
}
$sort[] = 'ID DESC';
$sort = 'ORDER BY '.impode(', ', $sort);

//-- Listing & tri -------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//output::