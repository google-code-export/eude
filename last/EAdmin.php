<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
require_once('./init.php');
require_once(INCLUDE_PATH . 'Script.php');

// Mise en attente dans le cache de config.
DataEngine::conf_cache('wormhole_cleaning');
DataEngine::conf_cache('EmpireAllys');
DataEngine::conf_cache('EmpireEnnemy');
DataEngine::conf_cache('MapColors');

if (!Members::CheckPerms(AXX_ROOTADMIN) && !Members::CheckPerms('MEMBRES_ADMIN'))
    Members::NoPermsAndDie();

$lng = language::getinstance()->GetLngBlock('admin');
$lngmain = language::getinstance()->GetLngBlock('dataengine');

// -----------------------------------------------------------------------------
// -- Nettoyage vortex périmé --------------------------------------------------
if (isset($_POST['cleanvortex'])) {
    define('CRON_LOADONLY', true);
    include(INCLUDE_PATH . 'crontab.php');
    $cron->GetJob('job_vortex')->RunJob();
    $cron->Save();
}

if (isset($_GET['switch']) && $_GET['switch'] == 'vortex_cron') {
    $tmp = DataEngine::config('wormhole_cleaning');
    $tmp['enabled'] = !$tmp['enabled'];
    DataEngine::conf_update('wormhole_cleaning', $tmp);
}

// -- Nettoyage vortex périmé --------------------------------------------------
// -----------------------------------------------------------------------------
// -- Partie Nettoyage ---------------------------------------------------------

$cleaning = false;
$cleaningids = array();
if (isset($_POST['joueurs']) && $_POST['joueurs'] != '-1') {
    $mysql_result = DataEngine::sql('SELECT ID FROM `SQL_PREFIX_Coordonnee` WHERE `TYPE` IN (0,3,5) AND `udate`<' . intval($_POST['joueurs']));
    $cleaning['cleaning_joueurs_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_joueurs_result'] > 0)
        while ($row = mysql_fetch_assoc($mysql_result))
            $cleaningids[] = $row['ID'];
}
if (isset($_POST['pnj']) && $_POST['pnj'] != '-1') {
    $mysql_result = DataEngine::sql('SELECT ID FROM `SQL_PREFIX_Coordonnee` WHERE `TYPE`=6 AND `udate`<' . intval($_POST['pnj']));
    $cleaning['cleaning_pnj_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_pnj_result'] > 0)
        while ($row = mysql_fetch_assoc($mysql_result))
            $cleaningids[] = $row['ID'];
}
if (isset($_POST['wormshole']) && $_POST['wormshole'] != '-1') {
    $mysql_result = DataEngine::sql('SELECT ID FROM `SQL_PREFIX_Coordonnee` WHERE `TYPE`=1 AND `udate`<' . intval($_POST['wormshole']));
    $cleaning['cleaning_wormshole_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_wormshole_result'] > 0)
        while ($row = mysql_fetch_assoc($mysql_result))
            $cleaningids[] = $row['ID'];
}
if (isset($_POST['planetes']) && $_POST['planetes'] != '-1') {
    $tmp = array();
    $mysql_result = DataEngine::sql('SELECT `ID` FROM `SQL_PREFIX_Coordonnee` WHERE `TYPE`=2 AND `udate`<' . intval($_POST['planetes']));
    $cleaning['cleaning_planetes_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_planetes_result'] > 0)
        while ($row = mysql_fetch_assoc($mysql_result))
            $cleaningids[] = $row['ID'];
}
if (isset($_POST['asteroides']) && $_POST['asteroides'] != '-1') {
    $tmp = array();
    $mysql_result = DataEngine::sql('SELECT ID FROM `SQL_PREFIX_Coordonnee` WHERE `TYPE`=4 AND `udate`<' . intval($_POST['asteroides']));
    $cleaning['cleaning_asteroides_result'] = mysql_num_rows($mysql_result);
    if ($cleaning['cleaning_asteroides_result'] > 0)
        while ($row = mysql_fetch_assoc($mysql_result))
            $cleaningids[] = $row['ID'];
}
if (count($cleaningids) > 0) {
    $cleaningids = implode(',', $cleaningids);
    DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee` WHERE `ID` in (' . $cleaningids . ')');
    DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee_Joueurs` WHERE `jID` in (' . $cleaningids . ')');
    DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee_Planetes` WHERE `pID` in (' . $cleaningids . ')');
}
// -- Partie Nettoyage ---------------------------------------------------------
// -----------------------------------------------------------------------------
// -- Partie maintenance -------------------------------------------------------
//if (isset($_POST['add_coords_unique_index']) && $_POST['add_coords_unique_index'] != '') {
//
//    $sql = <<<sql
//SELECT COUNT(`POSIN`) as nb, `POSIN`, `COORDET`
//FROM  `SQL_PREFIX_Coordonnee`
//GROUP BY CONCAT_WS('-', `POSIN`, `COORDET`)
//ORDER BY nb DESC
//sql;
//
//    $result = DataEngine::sql($sql);
//    $delid = Array();
//    while ($line = mysql_fetch_assoc($result)) {
//        if ($line['nb'] < 2)
//            break;
//        $i = 0;
//        $sql = <<<sql
//SELECT `ID`, `POSIN`, `COORDET`
//FROM  `SQL_PREFIX_Coordonnee`
//WHERE `POSIN`={$line['POSIN']} AND `COORDET`='{$line['COORDET']}'
//ORDER BY CONCAT_WS('-', `POSIN`, `COORDET`), udate DESC
//sql;
//        $result2 = DataEngine::sql($sql);
//        while ($line2 = mysql_fetch_assoc($result2)) {
//            $i++;
//            if ($i == 1)
//                continue;
//            $delid[] = $line2['ID'];
//        }
//    }
//
//    $delid = implode(',', $delid);
//    if ($delid != '') {
//        $sql = 'DELETE FROM `SQL_PREFIX_Coordonnee` WHERE `ID` IN (' . $delid . ')';
//        $result = DataEngine::sql($sql);
//        $cleaning['num deleted'] = mysql_affected_rows();
//        $sql = 'DELETE FROM `SQL_PREFIX_Coordonnee_Joueurs` WHERE `jID` IN (' . $delid . ')';
//        $result = DataEngine::sql($sql);
//        $sql = 'DELETE FROM `SQL_PREFIX_Coordonnee_Planetes` WHERE `pID` IN (' . $delid . ')';
//        $result = DataEngine::sql($sql);
//    }
//
//    // Ajout de l'index 'unique' s'il n'y est pas déjà...
//    $result = DataEngine::sql('SHOW INDEXES FROM `SQL_PREFIX_Coordonnee` WHERE `key_name`=\'coords\'');
//    $cleaning['index_add'] = mysql_num_rows($result) < 1;
//    if ($cleaning['index_add'])
//        $result = DataEngine::sql('ALTER TABLE `SQL_PREFIX_Coordonnee` ADD UNIQUE `coords` (`POSIN`, `COORDET`)');
//
//    if ($cleaning['num deleted'] > 0)
//        output::Messager(sprintf('%d doublon(s) trouvé', $cleaning['num deleted']));
//    if ($cleaning['num index_add'])
//        output::Messager('Index ajouté (accélère les requêtes)');
//}

if (isset($_POST['clean_orphan_planets']) && $_POST['clean_orphan_planets'] != '') {

    $sql = <<<sql
SELECT p.`pID` FROM  `SQL_PREFIX_Coordonnee_Planetes` p
LEFT JOIN  `SQL_PREFIX_Coordonnee` c ON ( p.`pID` = c.`id` )
WHERE c.`id` IS NULL OR c.`Type` NOT in (0,2,3,5)
sql;

    $delid = array();
    $result = DataEngine::sql($sql);
    if (mysql_num_rows($result) > 0) {
        while ($line = mysql_fetch_assoc($result))
            $delid[] = $line['pID'];
        $delid = implode(',', $delid);
        $sql = 'DELETE FROM `SQL_PREFIX_Coordonnee_Planetes` WHERE `pID` IN (' . $delid . ')';
        $result = DataEngine::sql($sql);
        $cleaning['num deleted'] = mysql_affected_rows();
        if ($cleaning['num deleted'] > 0)
            output::Messager(sprintf('%d orphelin(s) trouvé', $cleaning['num deleted']));
    }
}

if (isset($_POST['ResetCron']) && $_POST['ResetCron'] != '') {

    $sql = <<<sql
DELETE FROM `SQL_PREFIX_Config` WHERE `key` = 'cron'
sql;

    $result = DataEngine::sql($sql);
    $files = scandir(CACHE_PATH);
    foreach ($files as $file) {
        if (preg_match('/.*\.(js|css|png|cron)$/', $file))
            @unlink(CACHE_PATH . $file);
    }
    include_once(INCLUDE_PATH . 'crontab.php');
    $cron->GetJob('job_css')->RunJob();
    $cron->Save();
    output::Messager($lng['reset_cron_done']);
}

// -- Partie maintenance -------------------------------------------------------
// -----------------------------------------------------------------------------
// -- Gestion des empires ------------------------------------------------------

$emp_upd = false;
if (isset($_POST['emp_upd']) && $_POST['emp_upd'] != '') {
    $old_emp = sqlesc($_POST['emp_orig'], false);
    $new_emp = sqlesc($_POST['emp_new'], false);
    if ($old_emp != $new_emp && $old_emp != '') {
        $mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_Coordonnee_Joueurs` SET `EMPIRE`=\'' . $new_emp . '\' WHERE `EMPIRE` LIKE \'' . $old_emp . '\'');
        $emp_upd = mysql_affected_rows();
    }
}

$allysnb = $warsnb = -1;

if (isset($_POST['emp_allywars']) && $_POST['emp_allywars'] != '') {
    $mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_Coordonnee` SET `TYPE`=0 WHERE `TYPE` in (3,5)');

    $tmp = DataEngine::config('EmpireAllys');
    if (is_array($tmp) && $tmp != '') {
        array_walk($tmp, 'array_fullsqlesc');
        $tmp = implode(',', $tmp);
        if ($tmp != '') {
            $sql = <<<sql
UPDATE `SQL_PREFIX_Coordonnee`
LEFT JOIN `SQL_PREFIX_Coordonnee_Joueurs` on id=jid
SET `TYPE`=3 WHERE `TYPE` in (0,5) AND `EMPIRE` in ($tmp)
sql;
            $mysql_result = DataEngine::sql($sql);
            $allysnb = mysql_affected_rows();
        } else
            $allysnb = 0;
    }

    $tmp = DataEngine::config('EmpireEnnemy');
    if (is_array($tmp) && $tmp != '') {
        array_walk($tmp, 'array_fullsqlesc');
        $tmp = implode(',', $tmp);
        if ($tmp != '') {
            $sql = <<<sql
UPDATE `SQL_PREFIX_Coordonnee`
LEFT JOIN `SQL_PREFIX_Coordonnee_Joueurs` on id=jid
SET `TYPE`=5 WHERE `TYPE` in (0,5) AND `EMPIRE` in ($tmp)
sql;
            $mysql_result = DataEngine::sql($sql);
            $warsnb = mysql_affected_rows();
        } else
            $warsnb = 0;
    }
}
if (isset($_POST['emp_war_add']) && $_POST['emp_war_add'] != '') {
    $emp = sqlesc($_POST['emp'], false);
    if ($emp != '') {
        $wars = DataEngine::config('EmpireEnnemy');
        if (!in_array(gpc_esc($_POST['emp']), $wars)) {
            $sql = <<<sql
UPDATE `SQL_PREFIX_Coordonnee`
LEFT JOIN `SQL_PREFIX_Coordonnee_Joueurs` on id=jid
SET `TYPE`=5 WHERE `TYPE` in (0,3,5) AND `EMPIRE` LIKE '{$emp}'
sql;
            $mysql_result = DataEngine::sql($sql);
            $wars[] = gpc_esc($_POST['emp']);
            DataEngine::conf_update('EmpireEnnemy', $wars);
        }
    }
}
if (isset($_GET['emp_war_rm']) && $_GET['emp_war_rm'] != '') {
    $wars = DataEngine::config('EmpireEnnemy');
    $emp = sqlesc($wars[$_GET['emp_war_rm']], false);
    if ($emp != "") {
        $sql = <<<sql
UPDATE `SQL_PREFIX_Coordonnee`
LEFT JOIN `SQL_PREFIX_Coordonnee_Joueurs` on id=jid
SET `TYPE`=0 WHERE `TYPE` in (0,3,5) AND `EMPIRE` LIKE '{$emp}'
sql;
        $mysql_result = DataEngine::sql($sql);
        unset($wars[$_GET['emp_war_rm']]);
        DataEngine::conf_update('EmpireEnnemy', $wars);
    }
}
if (isset($_POST['emp_allys_add']) && $_POST['emp_allys_add'] != '') {
    $emp = sqlesc($_POST['emp'], false);
    if ($emp != '') {
        $allys = DataEngine::config('EmpireAllys');
        if (!in_array(gpc_esc($_POST['emp']), $allys)) {
            $sql = <<<sql
UPDATE `SQL_PREFIX_Coordonnee`
LEFT JOIN `SQL_PREFIX_Coordonnee_Joueurs` on id=jid
SET `TYPE`=3 WHERE `TYPE` in (0,3,5) AND `EMPIRE` LIKE '{$emp}'
sql;
            $mysql_result = DataEngine::sql($sql);
            $allys[] = gpc_esc($_POST['emp']);
            DataEngine::conf_update('EmpireAllys', $allys);
        }
    }
}
if (isset($_GET['emp_allys_rm']) && $_GET['emp_allys_rm'] != '') {
    $allys = DataEngine::config('EmpireAllys');
    $emp = sqlesc($allys[$_GET['emp_allys_rm']], false);
    if ($emp != '') {
        $sql = <<<sql
UPDATE `SQL_PREFIX_Coordonnee`
LEFT JOIN `SQL_PREFIX_Coordonnee_Joueurs` on id=jid
SET `TYPE`=0 WHERE `TYPE` in (0,3,5) AND `EMPIRE` LIKE '{$emp}'
sql;
        $mysql_result = DataEngine::sql($sql);

        unset($allys[$_GET['emp_allys_rm']]);
        DataEngine::conf_update('EmpireAllys', $allys);
    }
}

// -- Gestion des empires ------------------------------------------------------
// -----------------------------------------------------------------------------
// -- Modification 'couleurs' --------------------------------------------------

if (isset($_POST['majcolors']) && $_POST['majcolors']) {
    DataEngine::conf_update('MapColors', $_POST['cls']);
}

// -- Modification 'couleurs' --------------------------------------------------
// -----------------------------------------------------------------------------
// -- Modification 'Permissions' -----------------------------------------------

if (isset($_POST['cxx'])) {
    DataEngine::conf_update('perms', $_POST['cxx']);
}

// -- Modification 'Permissions' -----------------------------------------------
// -----------------------------------------------------------------------------
// -- Changement dans la configuration -----------------------------------------
if (isset($_POST['configuration']) && $_POST['configuration']) {

    $data = array_map('gpc_esc', $_POST['data']);
    $data['DefaultGrade'] = DataEngine::strip_number($data['DefaultGrade']);
    $data['Parcours_Max_Time'] = DataEngine::strip_number($data['Parcours_Max_Time']);
    $data['Parcours_Nearest'] = DataEngine::strip_number($data['Parcours_Nearest']);

    DataEngine::conf_update('config', $data);
    output::Messager($lng['config_done']);
}
// -- Changement dans la configuration -----------------------------------------
// -----------------------------------------------------------------------------
// exécution du spooleur sql...
DataEngine::sql_do_spool();
///-----------------------------------------------------------------------------

include_once(TEMPLATE_PATH . 'eadmin.tpl.php');
$tpl = tpl_eadmin::getinstance();
$tpl->page_title = $lng['page_title'];

$version[0] = @mysql_get_server_info();
$version[1] = PHP_VERSION;
$version[2] = @gd_info();
$version[2] = $version[2]['GD Version'];
$tpl->admin_header($version);

if (!isset($_REQUEST['act'])) {
///---
    // TODO: Revoir l'affichage du cron vortex...
    $dates[0] = date('Y-m-d H:i:s');
    $dates[1] = mktime($lngmain['wormholes_hour'], $lngmain['wormholes_minute'], 0, date('m'), date('d') - date('w') + $lngmain['wormholes_day']);
//    $dates[2] = mktime(3, 01, 0, date('m'), date('d') - date('w') - 7);

    $tpl->admin_vortex($dates, $cleanvortex_delete, DataEngine::config('wormhole_cleaning'));
//---

    $empire = array();
    $mysql_result = DataEngine::sql('SELECT `EMPIRE` from `SQL_PREFIX_Coordonnee_Joueurs` GROUP BY `EMPIRE` ASC');
    while ($ligne = mysql_fetch_array($mysql_result)) {
        if (trim($ligne['EMPIRE']) == '')
            continue;
        $cur_emp = htmlentities(stripslashes($ligne['EMPIRE']), ENT_QUOTES, 'utf-8');
        $shw_emp = DataEngine::utf_strip($ligne['EMPIRE']);
        $shw_emp = (p_strlen($shw_emp) > 50) ? p_substr($shw_emp, 0, 47) . '...' : $shw_emp;
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
    $dates[$lng['dates'][0]] = '-1';
    $dates[$lng['dates'][1]] = time();
    $dates[$lng['dates'][2]] = mktime(3, 0, 0, date('m'), date('d') - date('w'));
    $dates[$lng['dates'][3]] = mktime(3, 0, 0, date('m'), date('d') - date('w') - 7);
    $dates[$lng['dates'][4]] = mktime(0, 0, 0, date('m'), date('d') - 1);
    $dates[$lng['dates'][5]] = mktime(0, 0, 0, date('m'), date('d') - 2);
    $dates[$lng['dates'][6]] = mktime(0, 0, 0, date('m'), date('d') - 3);
    $dates[$lng['dates'][7]] = mktime(0, 0, 0, date('m'), date('d') - 4);
    $dates[$lng['dates'][8]] = mktime(0, 0, 0, date('m'), date('d') - 5);
    $dates[$lng['dates'][9]] = mktime(0, 0, 0, date('m'), date('d') - 6);
    $dates[$lng['dates'][10]] = mktime(0, 0, 0, date('m'), date('d') - 7);
    $dates[$lng['dates'][11]] = mktime(0, 0, 0, date('m'), date('d') - 15);
    $dates[$lng['dates'][12]] = mktime(0, 0, 0, date('m') - 1, date('d'));
    $dates[$lng['dates'][13]] = mktime(0, 0, 0, date('m') - 2, date('d'));
    $dates[$lng['dates'][14]] = mktime(0, 0, 0, date('m') - 3, date('d'));
    $dates[$lng['dates'][15]] = mktime(0, 0, 0, date('m') - 6, date('d'));
    $dates[$lng['dates'][16]] = mktime(0, 0, 0, date('m') - 9, date('d'));
    $dates[$lng['dates'][17]] = mktime(0, 0, 0, date('m') - 12, date('d'));

    $tpl->cleaning_header(6);
    $tpl->cleaning_row('joueurs', $lng['cleaning_joueurs'], $dates);
    $tpl->cleaning_row('pnj', $lng['cleaning_pnj'], $dates);
    $tpl->cleaning_row('wormshole', $lng['cleaning_wormshole'], $dates);
    $tpl->cleaning_row('planetes', $lng['cleaning_planetes'], $dates);
    $tpl->cleaning_row('asteroides', $lng['cleaning_asteroides'], $dates);
//    $tpl->cleaning_row('inactif', $lng['cleaning_inactif'],
//            array($lng['dates'][0] => '-1', $lng['dates'][20] => '1'));
    if (is_array($cleaning))
        $tpl->cleaning_msg($cleaning);
    $tpl->cleaning_footer();

    $tpl->add_coords_unique_index();
    $tpl->clean_orphan_planets();
    $tpl->ResetCron();

    $tpl->admin_footer();
}
//---

if ($_REQUEST['act'] == 'mapcolor' && Members::CheckPerms('MEMBRES_ADMIN_MAP_COLOR')) {
    $tpl->page_title = $lng['mapcolor_title'];
    $cls = DataEngine::config('MapColors');
    $tpl->map_color_header();

    foreach ($lng['colorsgroup'] as $i => $title) {
        $tpl->map_color_rowheader($title);
        foreach ($lng['colorslegend'][$i] as $v => $legend)
            $tpl->map_color_row($cls, $i, $v, $legend);
    }
    $tpl->map_color_footer();
}
///---
///---
if ($_REQUEST['act'] == 'logs' && Members::CheckPerms('MEMBRES_ADMIN_LOG')) {
    $tpl->page_title = $lng['logs_title'];
    $tpl->log_header();
    $mysql_result = DataEngine::sql('SELECT `DATE`, `log`, `IP` from `SQL_PREFIX_Log` ORDER BY `ID` DESC LIMIT 40');
    while ($ligne = mysql_fetch_array($mysql_result))
        $tpl->log_row($ligne);
    $tpl->log_footer();
}

if ($_REQUEST['act'] == 'perms' && Members::CheckPerms(AXX_ROOTADMIN)) {
    $cxx_name = Members::s_cperms();
    $axx_name = Members::s_perms();
    $cxx_conf = DataEngine::config('perms');
    $axx_num = count($axx_name);

    $tpl->page_title = $lng['perms_title'];
    $tpl->perms_header();
    $i = 0;

// Loop par CXX
    foreach ($cxx_name as $cxx_k => $cxx_v) {
        $class = 'color_row' . $i % 2;

        if (is_numeric($cxx_k)) {
            $tpl->perms_category($cxx_v);
            continue;
        } else {
            $tpl->perms_row($cxx_k, $cxx_v, $axx_name, $cxx_conf);
        }

        $tpl->PushOutput('</tr>');
        if (is_numeric($cxx_k))
            $i = 1;
        $i++;
    }
    $tpl->perms_footer();
}
///---

if ($_REQUEST['act'] == 'config' && Members::CheckPerms(AXX_ROOTADMIN)) {

    $mysql_result = DataEngine::sql('SELECT * from `SQL_PREFIX_Grade` ORDER BY `Rattachement`, `Niveau`');
    while ($ligne = mysql_fetch_assoc($mysql_result))
        $Grades[] = $ligne;

    $tpl->page_title = $lng['config_title'];
    $tpl->config_header();
    $tpl->config_xxx($Grades);
    $tpl->config_footer();
}

$tpl->DoOutput();


