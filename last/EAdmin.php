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

$lng = language::getinstance()->GetLngBlock('admin');

// Modification 'base'
if(isset($_POST['cleanvortex'])) {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE INACTIF=1 AND TYPE=1 AND `DATE`<'{$_POST['cleanvortex_inactif']}'");
    $cleanvortex_delete = mysql_affected_rows();
    $sql="UPDATE SQL_PREFIX_Coordonnee SET INACTIF=1 WHERE TYPE=1 AND `DATE`< '{$_POST['cleanvortex']}'";
    $mysql_result = DataEngine::sql($sql);
    $cleanvortex_inactif = mysql_affected_rows();

    $tmp = DataEngine::config('wormhole_cleaning');
    $tmp['lastrun'] = time();
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
    $cleaning['cleaning_asteroides_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_asteroides_result'] > 0) {
        while ($row = mysql_fetch_assoc($mysql_result)) $tmp[] = $row['ID'];
        $tmp = implode(',',$tmp);
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE ID in ($tmp)");
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee_Planetes WHERE pID in ($tmp)");
    }
}
if(isset($_POST['planetes']) && $_POST['planetes'] != '-1') {
    $tmp = array();
    $mysql_result = DataEngine::sql("SELECT ID FROM SQL_PREFIX_Coordonnee WHERE TYPE=2 AND `DATE`<'{$_POST['planetes']}'");
    $cleaning['cleaning_planetes_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_planetes_result'] > 0) {
        while ($row = mysql_fetch_assoc($mysql_result)) $tmp[] = $row['ID'];
        $tmp = implode(',',$tmp);
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE ID in ($tmp)");
        DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee_Planetes WHERE pID in ($tmp)");
    }
}
if(isset($_POST['pnj']) && $_POST['pnj'] != '-1') {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE TYPE=6 AND `DATE`<'{$_POST['pnj']}'");
    $cleaning['cleaning_pnj_result'] = mysql_affected_rows();
}
if(isset($_POST['joueurs']) && $_POST['joueurs'] != '-1') {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE TYPE = 0 AND `DATE`<'{$_POST['joueurs']}'");
    $cleaning['cleaning_joueurs_result'] = mysql_affected_rows();
}
if(isset($_POST['inactif']) && $_POST['inactif'] != '-1') {
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Coordonnee WHERE inactif=1");
    $cleaning['cleaning_inactif_result'] = mysql_affected_rows();
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
if(isset($_POST['emp_war_add']) && $_POST['emp_war_add'] != '') {
    $emp = sqlesc($_POST['emp']);
    if ($emp != "") {
        $wars = DataEngine::config('EmpireEnnemy');
        if (!in_array(gpc_esc($_POST['emp']), $wars)) {
            $mysql_result = DataEngine::sql("UPDATE SQL_PREFIX_Coordonnee SET TYPE=5 WHERE TYPE in (0,3,5) AND `EMPIRE` LIKE '{$emp}'");
            $wars[] = gpc_esc($_POST['emp']);
            DataEngine::conf_update('EmpireEnnemy', $wars);
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
    }
}

// Modification 'couleurs'
if(isset($_POST['majcolors']) && $_POST['majcolors']) {
    DataEngine::conf_update('MapColors', $_POST['cls']);
}

// Modification 'Permissions'
if (isset ($_POST['cxx'])) {
    DataEngine::conf_update('perms', $_POST['cxx']);
}

// exécution du spooleur sql...
DataEngine::sql_do_spool();
///---------------------------------------------------------------------------------------------------------------

include_once(TEMPLATE_PATH.'eadmin.tpl.php');
$tpl = tpl_eadmin::getinstance();
$tpl->page_title = $lng['page_title'];

$version[0] = @mysql_get_server_info();
$version[1] = PHP_VERSION;
$version[2] = @gd_info();
$version[2] = $version[2]['GD Version'];
$tpl->admin_header($version);

if (!isset($_REQUEST['act'])) {
///---

    $dates[0] = date('Y-m-d H:i:s');
    $dates[1] = date('Y-m-d H:i:s', mktime(2, 10, 0, date('m')  , date('d')-date('w')));
    $dates[2] = date('Y-m-d H:i:s', mktime(2, 10, 0, date('m')  , date('d')-date('w')-7));
    $cleanvortex=null;
    if (isset($_POST['cleanvortex']))
        $cleanvortex = array($cleanvortex_delete, $cleanvortex_inactif);

    $tpl->admin_vortex($dates,$cleanvortex, DataEngine::config('wormhole_cleaning'));
//---

    $empire = array();
    $mysql_result = DataEngine::sql('SELECT EMPIRE from SQL_PREFIX_Coordonnee GROUP BY EMPIRE ASC');
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
    $dates[$lng['dates'][0]]  = '-1';
    $dates[$lng['dates'][1]]  = date('Y-m-d H:i:s');
    $dates[$lng['dates'][2]]  = date('Y-m-d H:i:s', mktime(3, 0, 0, date('m'), date('d')-date('w')  ) );
    $dates[$lng['dates'][3]]  = date('Y-m-d H:i:s', mktime(3, 0, 0, date('m'), date('d')-date('w')-7) );
    $dates[$lng['dates'][4]]  = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-1	    ) );
    $dates[$lng['dates'][5]]  = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-2	    ) );
    $dates[$lng['dates'][6]]  = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-3	    ) );
    $dates[$lng['dates'][7]]  = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-4	    ) );
    $dates[$lng['dates'][8]]  = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-5	    ) );
    $dates[$lng['dates'][9]]  = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-6	    ) );
    $dates[$lng['dates'][10]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-7	    ) );
    $dates[$lng['dates'][11]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-15	    ) );
    $dates[$lng['dates'][12]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m')-1, date('d')	    ) );
    $dates[$lng['dates'][13]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m')-2, date('d')	    ) );
    $dates[$lng['dates'][14]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m')-3, date('d')	    ) );
    $dates[$lng['dates'][15]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m')-6, date('d')	    ) );
    $dates[$lng['dates'][16]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m')-9, date('d')	    ) );
    $dates[$lng['dates'][17]] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m')-12, date('d')	    ) );

    $tpl->cleaning_header(5);
    $tpl->cleaning_row('asteroides',$lng['cleaning_asteroides'], $dates);
    $tpl->cleaning_row('planetes',$lng['cleaning_planetes'], $dates);
    $tpl->cleaning_row('joueurs',$lng['cleaning_joueurs'], $dates);
    $tpl->cleaning_row('pnj',$lng['cleaning_pnj'], $dates);
    $tpl->cleaning_row('inactif', $lng['cleaning_inactif'],
            array($lng['dates'][0] => '-1', $lng['dates'][20] => '1'));
    if (is_array($cleaning)) $tpl->cleaning_msg($cleaning);
    $tpl->cleaning_footer();

    $tpl->admin_footer();
}
//---

if ($_REQUEST['act'] == 'mapcolor' && Members::CheckPerms('MEMBRES_ADMIN_MAP_COLOR')) {
    $tpl->page_title = $lng['mapcolor_title'];
    $cls = DataEngine::config('MapColors');
    $tpl->map_color_header();

    foreach ($lng['colorsgroup'] as $i => $title) {
        $tpl->map_color_rowheader($title);
        foreach($lng['colorslegend'][$i] as $v => $legend)
            $tpl->map_color_row($cls,$i,$v,$legend);
    }
    $tpl->map_color_footer();
}
///---

///---
if ($_REQUEST['act'] == 'logs' && Members::CheckPerms('MEMBRES_ADMIN_LOG')) {
    $tpl->page_title = $lng['logs_title'];
    $tpl->log_header();
    $mysql_result = DataEngine::sql('SELECT * from SQL_PREFIX_Log ORDER BY ID DESC LIMIT 40');
    while ($ligne=mysql_fetch_array($mysql_result))
        $tpl->log_row($ligne);
    $tpl->log_footer();
}

if ($_REQUEST['act'] == 'perms' && Members::CheckPerms(AXX_ROOTADMIN)) {
    $cxx_name = DataEngine::s_cperms();
    $axx_name = DataEngine::s_perms();
    $cxx_conf = DataEngine::config('perms');
    $axx_num  = count($axx_name);

    $tpl->page_title = $lng['perms_title'];
    $tpl->perms_header();
    $i=0;

// Loop par CXX
    foreach ($cxx_name as $cxx_k => $cxx_v) {
        $class = 'color_row'.$i%2;

        if (is_numeric($cxx_k)) {
            $tpl->perms_category($cxx_v);
            continue;
        } else {
            $tpl->perms_row($cxx_k, $cxx_v, $axx_name, $cxx_conf);
        }

        $tpl->PushOutput('</tr>');
        if (is_numeric($cxx_k)) $i=1;
        $i++;
    }
    $tpl->perms_footer();

}
///---

$tpl->DoOutput();


