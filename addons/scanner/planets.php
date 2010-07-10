<?php

/**
 * @Author: Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 * */
require_once('../../init.php');
require_once(INCLUDE_PATH . 'Script.php');
require_once(CLASS_PATH . 'map.class.php'); // requis par ownuniverse.class
require_once(CLASS_PATH . 'ownuniverse.class.php'); // pour avoir les coords des planètes
require_once(CLASS_PATH . 'parser.class.php'); // Ajout des vortex dans la base...
require_once(CLASS_PATH . 'cartographie.class.php'); // Ajout des vortex dans la base...
require_once('./inc.php');

// Check si activé
if (!addons::getinstance()->Is_installed('scanner'))
    DataEngine::NoPermsAndDie();

header('Content-Type: text/html;charset=utf-8');

error_reporting(E_ALL);
FB::setEnabled(false);
ob_end_flush();

if (!isset($_GET['id']) || !isset($_GET['step']) ||
        !isset($_SESSION['scanner_email']) || !isset($_SESSION['scanner_session']) ||
        $_SESSION['scanner_email'] == '' || $_SESSION['scanner_session'] == '')
    DataEngine::ErrorAndDie('Paramètre(s) manquant(s)');

$scanner = addons::getinstance()->Get_Addons('scanner');
$host = $scanner->ScanServer();

$header = 'Cookie: login_email=' . str_replace('@', '%40', $_SESSION['scanner_email']) . '; testcookie=1; PHPSESSID=' . $_SESSION['scanner_session'] . "\r\n";
$nbplanets = 0;
$skipnb = intval($_GET['step']);
$skipnext = $skipnb + 1;

$carto = cartographie::getinstance();
if (isset($_GET['ss']) && $_GET['ss'] != '') {
    $coord = $_GET['ss'];
} else {
    $coord = ownuniverse::getinstance()->get_comlevel();
    $coord = $coord[intval($_GET['id'])]['ss'];
}
$coords = map::getinstance()->Parcours()->GetListeCoorByRay($coord, $scanner->ScanRay('planets'));
$maxpage = count($coords) - 1;
$curcoord = $coords[$skipnb];

echo '<html><head><title>Scan N°' . $skipnb . '/' . $maxpage . ' (base:' . $coord . ')</title></head><body>';
echo 'Système ' . $curcoord . ':</br>';


if (($page = GetUrl($host, '/galaxy/galaxy_overview.php?area=galaxy&starsystem_id=' . $curcoord . '&fleet_id=&from=', $header)) === false)
    die('error sock 1');
//$page = file_get_contents('../../test/data/galaxy_1.txt');
preg_match_all("#sun,[^,]*,[^,]*,[^,]*,[^,]*,[^,]*,[^,]*,[^,]*,([a-fA-f0-9]{32})#", $page, $sun);
if (count($sun[0]) == 0)
    die('err preg 1');

sleep(rand(3, 8));

if (($page = GetUrl($host, '/galaxy/galaxy_info.php?starsystem_id=' . $curcoord . '&hash=' . $sun[1][0], $header)) === false)
    die('error sock 2');

if (stripos($page, '<font class="font_pink_bold">Erreur') !== false)
    die('Erreur max scan today...');

//preg_match_all('#class="table_entry_onclick".*width="100".*>(\d+-\d+-\d+-\d+)</td>\n'.
//        '.*\n.*width="150".*">(.+)</td>\n'.
//        '.*\n.*width="284".*">(.*)</td>#',
//        $page, $galaxy_info, PREG_SET_ORDER);
preg_match_all('#class="table_entry"[^>]*width="100"[^>]*>(\d+-\d+-\d+-\d+)</td>#',
        $page, $galaxy_coords, PREG_SET_ORDER);
preg_match_all('#class="table_entry"[^>]*width="150"[^>]*>([^<]*)</td>#',
        $page, $galaxy_planets, PREG_SET_ORDER);
preg_match_all('#<td\b[^>]*class="table_entry"[^>]*width="284"[^>]*>(.*?)</td>#',
        $page, $galaxy_players, PREG_SET_ORDER);

if (count($galaxy_coords[0]) == 0)
    die(__line__ . ' err preg 2: Session changé ? ');

for ($i = 0, $max = count($galaxy_coords); $i < $max; $i++) {
//    sleep(rand(5, 8));
    $nbplanets++;
    // $galaxy_info[$i][1] = coords xxxx-xx-xx-xx
    // $galaxy_info[$i][2] = nom planète
    // $galaxy_info[$i][3] = Nom joueur (et empire) brut
    if (trim($galaxy_players[$i][1]) != '') {
        preg_match_all('#<b>(.+)</b><br>(.*)#', $galaxy_players[$i][1], $player, PREG_SET_ORDER);
        if (count($player[0]) == 0)
            die('err preg 3');
        $joueur = $player[0][1];
        $empire = html_entity_decode(trim($player[0][2]), ENT_QUOTES, 'utf-8');
    } else {
        $joueur = '';
        $empire = '';
    }
    if (trim($galaxy_players[$i][3]) == '' && $carto->FormatId($galaxy_players[$i][1], $dummy, $sys, '')) // Planète inoccupée
        $del_planet[] = $sys;
    else
        $SS_A[] = array($galaxy_players[$i][1], $galaxy_planets[$i][1], $joueur, $empire);
}

// repiquage cartographie->add_solar_ss
if (count($del_planet) > 0) {
    $del_planet = '' . implode("','", $del_planet) . '';
    $query = 'UPDATE `SQL_PREFIX_Coordonnee` SET `Type`=2, `USER`=\'\', `EMPIRE`=\'\', `INFOS`=\'\', `batiments`=\'NULL\', `troop`=\'\' where `Type` in (0,3,5) AND `POSIN`=\'' . $cur_ss . '\' AND `COORDET` in (\'' . $del_planet . '\')';
    $array = DataEngine::sql($query);
    if (($num = mysql_affected_rows()) > 0)
        $carto->AddInfo(sprintf($lng['solar_msg1'], $num));
}

$query = 'SELECT `USER`, `EMPIRE` FROM `SQL_PREFIX_Coordonnee` where `POSIN`=\'' . $cur_ss . '\' AND `TYPE` in (0,3,5)';
$sql_result = DataEngine::sql($query);
while ($row = mysql_fetch_assoc($sql_result))
// par nom de joueur
    $curss_info[$row['USER']] = $row['EMPIRE'];

foreach ($SS_A as $v) {
    $result = $carto->add_player($v);
    if ($result) { // uniquement si changement, vide autrement.
        list($dummy, $dummy, $nom, $empire) = $v;
        if (isset($curss_info[$nom])) {
            if ($curss_info[$nom] != $empire) {
                $qnom = sqlesc($nom);
                $qempire = sqlesc($empire);
                $query = 'UPDATE `SQL_PREFIX_Coordonnee` SET `EMPIRE`=\'' . $qempire . '\', `UTILISATEUR`=\'' . $_SESSION['_login'] . '\', `DATE`=now() WHERE `USER`=\'' . $qnom . '\'';
                DataEngine::sql($query);
                $carto->AddInfo(sprintf($lng['solar_msg2'], $nom));
                unset($curss_info[$nom]);
            }
        }
    }
}
if ($max == 0) {
    xdebug_break();
    sleep(1);
    die('omg');
}
$tmp = array();
$tmp[0] = intval($skipnb / $maxpage * 100);
$tmp[1] = intval($_GET['nbplanets']) + $nbplanets;
$autoboink = 'false';

if ($skipnext > $maxpage)
    $base_url = './index.php';
else {
    $base_url = './planets.php?id=' . intval($_GET['id']) .
            '&step=' . $skipnext . '&nbplanets=' . $tmp[1] . '&ss=' . $_GET['ss'];
    $autoboink = 'true';
}

$sec = rand(3, 8);

echo $carto->Erreurs() . '<br/>';
echo $carto->Warns() . '<br/>';
echo $carto->Infos() . '<br/>';
$footer = <<<f

<script language="javascript">
    var iTimer = false;
    function GoNow(){
        if ({$autoboink}) window.location.href="{$base_url}";
    }
iTimer = window.setTimeout('GoNow()', {$sec}000); // 3sec de latence, on est pas des brutes
</script>
<br/>--------------------------------<br/>
- Progression: {$tmp[0]}%</br>
- Total planètes: {$tmp[1]} (dans ce SS: {$nbplanets})</br>
- <a href="{$base_url}" OnClick="javascript:window.clearTimeout(iTimer);">Suite</a> (automatiquement dans {$sec} secondes)</br>
- <a href="javascript:window.clearTimeout(iTimer);">Stop</a>
</body>
</html>
f;
echo $footer;
