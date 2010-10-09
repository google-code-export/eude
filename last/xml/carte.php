<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
define('USE_AJAX', true);

require_once('../init.php');
require_once(INCLUDE_PATH . 'Script.php');

if (!Members::CheckPerms('CARTE_SEARCH')) {
    $msg = language::getinstance()->GetLngBlock('dateengine');
    $msg = $msg['nopermsanddie'];
    $out = <<<o
<carte>
    <script>
    Carte.DetailsShow(false);
    alert('{$msg}');
    </script>
</carte>
o;
    output::_DoOutput($out);
}

// Partie recherche
$_SESSION['emp'] = ( ($_POST['s'] != '') && $_POST['type'] == 'emp') ? stripslashes($_POST['s']) : '';
$_SESSION['jou'] = ( ($_POST['s'] != '') && $_POST['type'] == 'jou') ? stripslashes($_POST['s']) : '';
$_SESSION['inf'] = ( ($_POST['s'] != '') && $_POST['type'] == 'inf') ? stripslashes($_POST['s']) : '';

$search = '';
$typeeval = 'return false;';

if ($_SESSION['emp'] != "") {
    $search = "j.`EMPIRE`='" . sqlesc($_SESSION['emp']) . "' ";
    $typeeval = 'return $line[\'EMPIRE\'] == $_SESSION[\'emp\'];';
}
if ($_SESSION['jou'] != "") {
    $search = "j.`USER`='" . sqlesc($_SESSION['jou']) . "' ";
    $typeeval = 'return $line[\'USER\'] == $_SESSION[\'jou\'];';
}
if ($_SESSION['inf'] != "") {
    $search = "j.`INFOS`='" . sqlesc($_SESSION['inf']) . "' ";
    $typeeval = 'return $line[\'INFOS\'] == $_SESSION[\'inf\'];';
}

$sql = <<<sql
SELECT c.`POSIN`
FROM SQL_PREFIX_Coordonnee as c
    LEFT JOIN SQL_PREFIX_Coordonnee_Joueurs as j on (c.id=j.jid)
WHERE {$search}
ORDER BY c.`POSIN` ASC
sql;
$mysql_result = DataEngine::sql($sql);
$ss_result = array();
while ($line = mysql_fetch_assoc($mysql_result)) {
    $ss_result[$line['POSIN']] = $line['POSIN'];
}
mysql_free_result($mysql_result);
$tabdata = array();

if (isset($_POST['ss']) && $_POST['ss'] != "") {
    foreach (explode(',', $_POST['ss']) as $v) {
        if (!isset($ss_result[$v]))
            $tabdata[] = 'Carte.Remove_SS(' . $v . ');';
    }
}
$currentsearch = implode(',', $ss_result);


/** @export job_map_tooltips::RunJob */ {

    $vortex_a = array();
    $CurrSS_a = array();
    $empire = trim(DataEngine::config_key('config', 'MyEmpire'));
    $cxx_empires = Members::CheckPerms('CARTE_SHOWEMPIRE');

    /* Récupérations des vortex */ {
        $sql = 'SELECT `ID`, `POSIN`, `POSOUT` from `SQL_PREFIX_Coordonnee` where `Type`=1';
        $mysql_result = DataEngine::sql($sql);
        while ($line = mysql_fetch_assoc($mysql_result)) {
            $vortex_a[$line['POSOUT']][$line['ID']]['POSIN'] = $line['POSOUT'];
            $vortex_a[$line['POSOUT']][$line['ID']]['POSOUT'] = $line['POSIN'];
            $vortex_a[$line['POSOUT']][$line['ID']]['TYPE'] = 1;
        }
        mysql_free_result($mysql_result);
    }
    $sql = <<<sql
SELECT c.`ID`, c.`TYPE`, c.`POSIN`, c.`POSOUT`, j.`USER`, j.`INFOS`, j.`EMPIRE`,
    IFNULL(g.`Grade`,'') as Grade, IFNULL(m.`Joueur`,'') as Joueur
FROM SQL_PREFIX_Coordonnee as c
    LEFT JOIN SQL_PREFIX_Coordonnee_Joueurs as j on (c.id=j.jid)
    LEFT JOIN `SQL_PREFIX_Membres` m on (j.`USER`=m.`Joueur`)
    LEFT JOIN `SQL_PREFIX_Grade` g on (m.`Grade`=g.`GradeId`)
WHERE c.`POSIN` IN ({$currentsearch})
ORDER BY c.`POSIN` ASC
sql;

    $mysql_result = DataEngine::sql($sql);
    $CurrSS = 0;
    while ($line = mysql_fetch_assoc($mysql_result)) {
        if ($CurrSS == 0)
            $CurrSS = $line['POSIN'];

        if ($line['POSIN'] != $CurrSS) {
            if (isset($vortex_a[$CurrSS]) && is_array($vortex_a[$CurrSS])) {
                foreach ($vortex_a[$CurrSS] as $k => $v) {
                    $CurrSS_a[$k] = $v;
                    $CurrSS_a[$k]['type'] = 'Vortex';
                    $CurrSS_a['Vortex'] = isset($CurrSS_a['Vortex']) ? $CurrSS_a['Vortex']++ : 1;
                }
                unset($vortex_a[$CurrSS]); // destruction du vortex...
            }
            $tabdata[] = job_map_tooltips__add_ss($CurrSS, $CurrSS_a);
            $CurrSS = $line['POSIN'];
            $CurrSS_a = array();
        }

        $ID = $line['ID'];
        $ss = $line['POSIN'];

        $CurrSS_a[$ID] = $line;
        /* map::ss_type */ {

            switch ($line['TYPE']) {
                case 0: $type = 'Joueur';
                    break;
                case 1: $type = 'Vortex';
                    break;
                case 2: $type = 'Planète';
                    break;
                case 3: $type = 'alliance';
                    break;
                case 4: $type = 'Astéroïde';
                    break;
                case 5: $type = 'Ennemi';
                    break;
                case 6: $type = 'pnj';
                    break;
                default: $type = 'na';
            }
            if ($empire != '' && $line['EMPIRE'] == $empire && $cxx_empires)
                $type = 'empire';
            if (eval($typeeval))
                $type = 'search';

            if (stristr($line['USER'], $_SESSION['_login']) !== FALSE)
                $type = 'moi';
            $CurrSS_a[$ID]['type'] = $type;
        }

        if (isset($CurrSS_a[$CurrSS_a[$ID]['type']]))
            $CurrSS_a[$CurrSS_a[$ID]['type']]++;
        else
            $CurrSS_a[$CurrSS_a[$ID]['type']] = 1;
    }
    mysql_free_result($mysql_result);

    if (isset($vortex_a[$CurrSS]) && is_array($vortex_a[$CurrSS])) {
        foreach ($vortex_a[$CurrSS] as $k => $v) {
            $CurrSS_a[$k] = $v;
            $CurrSS_a[$k]['type'] = 'Vortex';
            $CurrSS_a['Vortex'] = isset($CurrSS_a['Vortex']) ? $CurrSS_a['Vortex']++ : 1;
        }
        unset($vortex_a[$CurrSS]); // destruction du vortex...
    }
    $tabdata[] = job_map_tooltips__add_ss($CurrSS, $CurrSS_a);
}

if (is_array($currentsearch))
    $currentsearch = implode(',', $currentsearch);
$tabdata = implode('', $tabdata);
$out = <<<o
<carte>
    <currentsearch><![CDATA[$currentsearch]]></currentsearch>
    <tabdata><![CDATA[$tabdata]]></tabdata>
</carte>
o;
output::_DoOutput($out);

/** crontab.php/array_js */
function array_js(&$item1, $key) {
    if (!is_numeric($item1))
        $item1 = '"' . addslashes($item1) . '"';
}

function job_map_tooltips__add_ss($ss, $data) {
    $line = array();
    $tmp = '';
    // map::ss_info
    foreach ($data as $k => $v) { /// $k = ID mysql/nb type @@ $v = array...
        if (isset($v['EMPIRE']))
            $v['EMPIRE'] = htmlspecialchars(addcslashes($v['EMPIRE'], '"'));

        switch ($v['type']) {
            case 'moi':
                $line['ownplanet'][] = $v['INFOS'];
                break;
            case 'empire':
                if (!isset($line['empire']))
                    $line['empire'] = array();

                if ($v['Joueur'] != '')
                    $line['empire'][] = $v['Joueur'] . ' (' . $v['Grade'] . ')';
                else
                    $line['empire'][] = $v['USER'];
                break;
            case 'Vortex':
                if (!isset($line['wormholes']))
                    $line['wormholes'] = array();
                $line['wormholes'][] = $v['POSOUT'];
                break;
            case 'Joueur':
                if (!isset($line['players']))
                    $line['players'] = array();

                if ($v['EMPIRE'] != '')
                    $line['players'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                else
                    $line['players'][] = $v['USER'];
                break;
            case 'search':
                if (!isset($line['searchresult']))
                    $line['searchresult'] = array();

                if ($v['EMPIRE'] != '')
                    $line['searchresult'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                else
                    $line['searchresult'][] = $v['USER'];
                break;
            case 'alliance':
                if (!isset($line['alliance']))
                    $line['alliance'] = array();

                $line['alliance'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                break;
            case 'Ennemi':
                if (!isset($line['ennemys']))
                    $line['ennemys'] = array();

                if ($v['EMPIRE'] != '')
                    $line['ennemys'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                else
                    $line['ennemys'][] = $v['USER'];
                break;
            case 'pnj':
                if (!isset($line['reaperfleet']))
                    $line['reaperfleet'] = array();

                $line['reaperfleet'][] = $v['INFOS'];
                break;
            case 'Planète':
                if (!isset($line['planets']))
                    $line['planets'] = 1;
                else
                    $line['planets']++;
                break;
            case 'Astéroïde':
                if (!isset($line['asteroids']))
                    $line['asteroids'] = 1;
                else
                    $line['asteroids']++;
                break;
            case 'cdr':
                if (!isset($line['cdr']))
                    $line['cdr'] = 1;
                else
                    $line['cdr']++;
                break;
        }
    }
    if (true) {
        $tmp = 'Carte.Set_SS(' . $ss . ',{';

        foreach ($line as $k => $v) {
            if (is_array($v) && count($v) > 0) {
                array_walk($v, 'array_js');
                $tmp .= $k . ':[' . implode(',', $v) . '],';
            } elseif (!is_array($v))
                $tmp .= $k . ':' . (is_numeric($v) ? $v : '"' . $v . '"') . ',';
        }
        return substr($tmp, 0, strlen($tmp) - 1) . ' });';
    } else {
        $tmp = 'Carte.Set_SS(' . $ss . ',{' . PHP_EOL;

        foreach ($line as $k => $v) {
            if (is_array($v) && count($v) > 0) {
                array_walk($v, 'array_js');
                $tmp .= $k . ':[' . implode(',', $v) . '],' . PHP_EOL;
            } elseif (!is_array($v))
                $tmp .= $k . ':' . (is_numeric($v) ? $v : '"' . $v . '"') . ',' . PHP_EOL;
        }
        return substr($tmp, 0, strlen($tmp) - strlen(PHP_EOL) - 1) . ' });' . PHP_EOL;
    }
}