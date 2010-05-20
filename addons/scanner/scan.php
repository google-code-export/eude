<?php
/**
 * @Author: Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/

require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'map.class.php'); // requis par ownuniverse.class
require_once(CLASS_PATH.'ownuniverse.class.php'); // pour avoir les coords des planètes
require_once(CLASS_PATH.'parser.class.php'); 
require_once(CLASS_PATH.'cartographie.class.php'); // Ajout des vortex dans la base...
require_once('./inc.php');

// Check si activé
if (!addons::getinstance()->Is_installed('scanner')) DataEngine::NoPermsAndDie();

header('Content-Type: text/html;charset=utf-8');

error_reporting(E_ALL);
FB::setEnabled(false);
ob_end_flush();

if (!isset ($_GET['id']) || !isset ($_GET['step']) ||
    !isset($_SESSION['scanner_email']) || !isset ($_SESSION['scanner_session']) ||
    $_SESSION['scanner_email'] == '' || $_SESSION['scanner_session'] == '')
    DataEngine::ErrorAndDie('Paramètre(s) manquant(s)');

/**
 * @var scanner_addons 
 */
$scanner = addons::getinstance()->Get_Addons('scanner');
$host = $scanner->ScanServer();

$header = 'Cookie: login_email='.str_replace('@', '%40', $_SESSION['scanner_email']).'; testcookie=1; PHPSESSID='.$_SESSION['scanner_session']."\r\n";
$nbWormHole=0;
$skipnb = intval($_GET['step']);
$skipnext = $skipnb+1;

$carto = cartographie::getinstance();
$coord = ownuniverse::getinstance()->get_comlevel();
$coord = $coord[intval($_GET['id'])]['ss'];
$coords = map::getinstance()->Parcours()->GetListeCoorByRay($coord, $scanner->ScanRay('vortex'));
$maxpage = count($coords)-1;
$curcoord = $coords[$skipnb];

echo '<html><head><title>Scan N°'.$skipnb.'/'.$maxpage.' (base:'.$coord.')</title></head><body>';
echo 'Système '.$curcoord.':</br>';

if ( ($page=GetUrl($host, '/galaxy/galaxy_overview.php?area=galaxy&starsystem_id='.$curcoord.'&fleet_id=&from=',$header)) ===false) die('error2, reload window game ?');

preg_match_all("#sun,[^,]*,[^,]*,[^,]*,[^,]*,[^,]*,[^,]*,[^,]*,(.+)'#",$page,$sun);
if (!isset($sun[1][0])) die('omg');

preg_match_all("#wormhole_[^,]+,[^,]*,[^,]*,[^,]*,(\d+),[^,]*,[^,]*,[^,]*,(.+)'#",$page,$wormhole);
for ($i=0,$max=count($wormhole[1]);$i<$max;$i++) {
    if ($max>1)
        sleep(rand(8, 12));
    else
        sleep(rand(5, 8));
    $nbWormHole++;
    
    if ( ($page=GetUrl($host,'/wormhole/wormhole_info.php?side=end&fly_fleet_id=0&coordinate_select=undefined&wormhole_id='.$wormhole[1][$i].'&hash='.$wormhole[2][$i], $header)) === false) die('3');

    preg_match_all("#ID Système stellaire.*\n.*\n.*>(\d+)</td>#",$page,$tmpId);
    preg_match_all("#Coordonnées.*\n.*\n.*>(\d+:\d+:\d+)</td>#",$page,$tmpCoord);
    if (isset($tmpId[1]) and isset($tmpCoord[1])) {
        $tmp = $carto->add_vortex($tmpId[1][0].':'.$tmpCoord[1][0], $tmpId[1][1].':'.$tmpCoord[1][1]);
    } else die('apologized');
}
$tmp = array();
$tmp[0] = intval($skipnb/$maxpage*100);
$tmp[1] = intval($_GET['nbwormhole'])+$nbWormHole;
$autoboink = 'false';

if ($skipnext > $maxpage)
    $base_url = './index.php';
else {
    $base_url = './scan.php?id='.intval($_GET['id']).
    '&step='.$skipnext.'&nbwormhole='.$tmp[1];
    $autoboink = 'true';
}

if ($nbWormHole>0)
    $sec = rand(5,12+$nbWormHole);
else
    $sec= rand(3, 8);

echo $carto->Erreurs().'<br/>';
echo $carto->Warns().'<br/>';
echo $carto->Infos().'<br/>';
$footer =<<<f

<script language="javascript">
    var iTimer = false;
    function GoNow(){
        if ({$autoboink}) window.location.href="{$base_url}";
    }
iTimer = window.setTimeout('GoNow()', {$sec}000); // 3sec de latence, on est pas des brutes
</script>
<br/>--------------------------------<br/>
- Progression: {$tmp[0]}%</br>
- Total vortex: {$tmp[1]} (dans ce SS: {$nbWormHole})</br>
- <a href="{$base_url}" OnClick="javascript:window.clearTimeout(iTimer);">Suite</a> (automatiquement dans {$sec} secondes)</br>
- <a href="javascript:window.clearTimeout(iTimer);">Stop</a>
</body>
</html>
f;
echo $footer;
