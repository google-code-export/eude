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

// Mise en attente dans le cache de config.
DataEngine::conf_cache('wormhole_cleaning');
DataEngine::conf_cache('EmpireAllys');
DataEngine::conf_cache('EmpireEnnemy');
DataEngine::conf_cache('MapColors');

if (!Members::CheckPerms(AXX_ROOTADMIN) && !Members::CheckPerms('MEMBRES_ADMIN'))
    Members::NoPermsAndDie();

if(isset($_POST['log'])) {
    $login = strtolower($_POST['log']);
    $pass  = md5($_POST['pwd']);
    $perm  = $_POST['perm'];
    if($_POST['oldpass']==$_POST['pwd'])
        DataEngine::sql("UPDATE SQL_PREFIX_Users set Permission='".$perm."' WHERE Login='".$login."'");
    else
        DataEngine::sql("UPDATE SQL_PREFIX_Users set Password='".$pass."', Permission='".$perm."' WHERE Login='".$login."'");
}

if(isset($_POST['cleanvortex'])) {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE INACTIF=1 AND TYPE=1 AND `DATE`<'{$_POST['cleanvortex_inactif']}'");
    $cleanvortex_delete = mysql_affected_rows();
    $sql="UPDATE SQL_PREFIX_Coordonnee SET INACTIF=1 WHERE TYPE=1 AND `DATE`< '{$_POST['cleanvortex']}'";
    $mysql_result = DataEngine::sql($sql);
    $cleanvortex_inactif = mysql_affected_rows();

    $tmp = DataEngine::config('wormhole_cleaning');
    $tmp['lastrun'] = mktime();
    DataEngine::conf_update('wormhole_cleaning', $tmp);
}

if(isset($_GET['switch']) && $_GET['switch'] =='vortex_cron') {
    $tmp = DataEngine::config('wormhole_cleaning');
    $tmp['enabled'] = !$tmp['enabled'];
    DataEngine::conf_update('wormhole_cleaning', $tmp);
}

$cleaning=false;
if(isset($_POST['asteroides']) && $_POST['asteroides'] != '-1') {
    $tmp = array();
    $mysql_result = DataEngine::sql("SELECT ID FROM SQL_PREFIX_Coordonnee WHERE TYPE=4 AND `DATE`<'{$_POST['asteroides']}'");
    $cleaning['Asteroïdes'] = mysql_num_rows($mysql_result);
    if ($cleaning['Asteroïdes'] > 0) {
        while ($row = mysql_fetch_assoc($mysql_result)) $tmp[] = $row['ID'];
        $tmp = implode(',',$tmp);
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE ID in ($tmp)");
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee_Planetes WHERE pID in ($tmp)");
    }
}
if(isset($_POST['planetes']) && $_POST['planetes'] != '-1') {
    $tmp = array();
    $mysql_result = DataEngine::sql("SELECT ID FROM SQL_PREFIX_Coordonnee WHERE TYPE=2 AND `DATE`<'{$_POST['planetes']}'");
    $cleaning['Planètes'] = mysql_num_rows($mysql_result);
    if ($cleaning['Planètes'] > 0) {
        while ($row = mysql_fetch_assoc($mysql_result)) $tmp[] = $row['ID'];
        $tmp = implode(',',$tmp);
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE ID in ($tmp)");
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee_Planetes WHERE pID in ($tmp)");
    }
}
if(isset($_POST['pnj']) && $_POST['pnj'] != '-1') {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE TYPE=6 AND `DATE`<'{$_POST['pnj']}'");
    $cleaning['Flottes PNJ'] = mysql_affected_rows();
}
if(isset($_POST['joueurs']) && $_POST['joueurs'] != '-1') {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE TYPE = 0 AND `DATE`<'{$_POST['joueurs']}'");
    $cleaning['Joueurs'] = mysql_affected_rows();
}
if(isset($_POST['inactif']) && $_POST['inactif'] != '-1') {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE inactif=1");
    $cleaning['Inactifs'] = mysql_affected_rows();
}


$emp_upd = false;
if(isset($_POST['emp_upd']) && $_POST['emp_upd'] != '') {
    $old_emp = sqlesc($_POST['emp_orig']);
    $new_emp = sqlesc($_POST['emp_new']);
    if ($old_emp!=$new_emp && $old_emp != "") {
        $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET EMPIRE='{$new_emp}' WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$old_emp}'");
        $emp_upd = mysql_affected_rows();
    }
}

$allysnb = $warsnb = -1;
function array_fullsqlesc(&$item1, $key) {
    $item1 = '\''.mysql_escape_string($item1).'\'';
}
if(isset($_POST['emp_allywars']) && $_POST['emp_allywars'] != '') {
    $mysql_result = DataEngine::sql('UPDATE SQL_PREFIX_Coordonnee SET TYPE=0 WHERE TYPE in (3,5)');

    $tmp = DataEngine::config('EmpireAllys');
    array_walk($tmp, 'array_fullsqlesc');
    $tmp = implode(',', $tmp);
    if ($tmp!='') {
        $mysql_result = DataEngine::sql('UPDATE SQL_PREFIX_Coordonnee SET TYPE=3 WHERE TYPE in (0,5) AND `EMPIRE` in ('.$tmp.')');
        $allysnb = mysql_affected_rows();
    } else $allysnb = 0;

    $tmp = DataEngine::config('EmpireEnnemy');
    array_walk($tmp, 'array_fullsqlesc');
    $tmp = implode(',', $tmp);
    if ($tmp!='') {
        $mysql_result = DataEngine::sql('UPDATE SQL_PREFIX_Coordonnee SET TYPE=5 WHERE TYPE in (0,3) AND `EMPIRE` in ('.$tmp.')');
        $warsnb = mysql_affected_rows();
    } else $warsnb = 0;
}
//$emp_war = false;
//if(isset($_POST['emp_war']) && $_POST['emp_war'] != '') {
//    $emp = sqlesc($_POST['emp']);
//    if ($emp != "") {
//        $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET TYPE={$_POST['r_war']} WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$emp}'");
//        $emp_war = mysql_affected_rows();
//    }
//}
if(isset($_POST['emp_war_add']) && $_POST['emp_war_add'] != '') {
    $emp = sqlesc($_POST['emp']);
    if ($emp != "") {
        $wars = DataEngine::config('EmpireEnnemy');
        if (!in_array(gpc_esc($_POST['emp']), $wars)) {
            $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET TYPE=5 WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$emp}'");
            $wars[] = gpc_esc($_POST['emp']);
            DataEngine::conf_update('EmpireEnnemy', $wars);
            output::Boink('?');
        }
    }
}
if(isset($_GET['emp_war_rm']) && $_GET['emp_war_rm'] != '') {
    $wars = DataEngine::config('EmpireEnnemy');
    $emp = sqlesc($wars[$_GET['emp_war_rm']]);
    if ($emp != "") {
            $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET TYPE=0 WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$emp}'");
            unset ($wars[$_GET['emp_war_rm']]);
            DataEngine::conf_update('EmpireEnnemy', $wars);
            output::Boink('?');
    }
}
if(isset($_POST['emp_allys_add']) && $_POST['emp_allys_add'] != '') {
    $emp = sqlesc($_POST['emp']);
    if ($emp != "") {
        $allys = DataEngine::config('EmpireAllys');
        if (!in_array(gpc_esc($_POST['emp']), $allys)) {
            $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET TYPE=3 WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$emp}'");
            $allys[] = gpc_esc($_POST['emp']);
            DataEngine::conf_update('EmpireAllys', $allys);
            output::Boink('?');
        }
    }
}
if(isset($_GET['emp_allys_rm']) && $_GET['emp_allys_rm'] != '') {
    $allys = DataEngine::config('EmpireAllys');
    $emp = sqlesc($allys[$_GET['emp_allys_rm']]);
    if ($emp != "") {
            $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET TYPE=0 WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$emp}'");
            unset ($allys[$_GET['emp_allys_rm']]);
            DataEngine::conf_update('EmpireAllys', $allys);
            output::Boink('?');
    }
}

///---------------------------------------------------------------------------------------------------------------

// output::$css_file   = false;
// output::$page_title = "EU2: Admin";
include_once(TEMPLATE_PATH.'eadmin.tpl.php');
$tpl = tpl_eadmin::getinstance();
$tpl->css_file   = false;
$tpl->page_title = 'EU2: Admin';

///---
$version[0] = @mysql_get_server_info();
$version[1] = PHP_VERSION;
$version[2] = @gd_info();
$version[2] = $version[2]["GD Version"];
$tpl->admin_header($version);

$dates[0] = date("Y-m-d H:i:s");
$dates[1] = date("Y-m-d H:i:s", mktime(2, 10, 0, date("m")  , date("d")-date("w")));
$dates[2] = date("Y-m-d H:i:s", mktime(2, 10, 0, date("m")  , date("d")-date("w")-7));
$cleanvortex=null;
if (isset($_POST['cleanvortex']))
    $cleanvortex = array($cleanvortex_delete, $cleanvortex_inactif);

$tpl->admin_vortex($dates,$cleanvortex, DataEngine::config('wormhole_cleaning'));
//---

$empire = array();
$mysql_result = DataEngine::sql("SELECT EMPIRE from SQL_PREFIX_Coordonnee GROUP BY EMPIRE ASC");
while ($ligne=mysql_fetch_array($mysql_result)) {
    if (trim($ligne['EMPIRE'])=='') continue;
    $cur_emp = htmlentities(stripslashes($ligne['EMPIRE']), ENT_QUOTES, 'utf-8');
    $shw_emp = DataEngine::utf_strip($ligne['EMPIRE']);
    $shw_emp = (strlen($shw_emp) > 50) ? substr($shw_emp,0,47).'...': $shw_emp;
    $shw_emp = htmlentities($shw_emp, ENT_QUOTES, 'utf-8');
    $empire[$cur_emp] = $shw_emp;
}
$tpl->empire_switch($empire, $emp_upd);
$tpl->empire_allys($empire);
$tpl->empire_wars($empire);


$tpl->empire_allywars($allysnb, $warsnb);
//$tpl->empire_wars($empire, $emp_war);
//

//---
$dates = array();
$dates["[Aucun changement]"]		= "-1";
$dates["Aujourd'hui (tout)"]		= date("Y-m-d H:i:s");
$dates["Dimanche dernier"]			= date("Y-m-d H:i:s", mktime(3, 0, 0, date("m"), date("d")-date("w")	) );
$dates["Dimanche précédent"]		= date("Y-m-d H:i:s", mktime(3, 0, 0, date("m"), date("d")-date("w")-7) );
$dates["Hier"]						= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-1			) );
$dates["Avant-hier"]				= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-2			) );
$dates["3 Jours"]					= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-3			) );
$dates["4 Jours"]					= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-4			) );
$dates["5 Jours"]					= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-5			) );
$dates["6 Jours"]					= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-6			) );
$dates["7 Jours"]					= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-7			) );
$dates["15 Jours"]					= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d")-15			) );
$dates["1 Mois (premier du mois)"]	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-1, date("d")			) );
$dates["2 Mois (premier du mois)"]	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-2, date("d")			) );
$dates["3 Mois (premier du mois)"]	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-3, date("d")			) );
$dates["6 Mois (premier du mois)"]	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-6, date("d")			) );
$dates["9 Mois (premier du mois)"]	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-9, date("d")			) );
$dates["12 Mois (premier du mois)"]	= date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")-12, date("d")			) );

$tpl->cleaning_header(5);
$tpl->cleaning_row('asteroides',"Suppression des Astéroïdes", $dates);
$tpl->cleaning_row('planetes',"Suppression des Planètes", $dates);
$tpl->cleaning_row('joueurs',"Suppression des Joueurs", $dates);
$tpl->cleaning_row('pnj',"Suppression des Flottes PNJ", $dates);
$tpl->cleaning_row('inactif', 'Suppression des éléments incatifs',
        array('[Aucun changement]' => '-1', 'Tous' => '1'));
if (is_array($cleaning)) $tpl->cleaning_msg($cleaning);
$tpl->cleaning_footer();

$tpl->admin_footer();
///---

///---
$tpl->log_header();
if (Members::CheckPerms('MEMBRES_ADMIN_LOG')) {
    $mysql_result = DataEngine::sql("SELECT * from SQL_PREFIX_Log ORDER BY ID DESC LIMIT 40");
    while ($ligne=mysql_fetch_array($mysql_result))
        $tpl->log_row($ligne);
} else
    $tpl->log_row(array('DATE' => date('Y-m-d H:i:s'),'LOGIN' => '...', 'IP'=>'...'));
$tpl->log_footer();
///---

$tpl->DoOutput();


