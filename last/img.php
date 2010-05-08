<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
define('IS_IMG', true);
// 	define('DEBUG_IMG', true);

require_once('./init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'map.class.php');
require_once(CLASS_PATH.'parser.class.php');
require_once(CLASS_PATH.'ownuniverse.class.php');

DataEngine::conf_cache('EmpireAllys');
DataEngine::conf_cache('EmpireEnnemy');
DataEngine::conf_cache('MapColors');

//if (headers_sent($file,$line)) die("Erreur 'header': $file:$line");
//$connexion = Config::DB_Connect();

$map = map::getinstance(); // initialisation...

if (DEBUG_IMG) {
    header('Content-type: text/plain;charset=utf-8');
}

/// INIT ///

$NbCase			= 100;
$TailleCase	= floor($map->taille/$NbCase);
$image = initimg($NbCase,$NbCase,$TailleCase,$TailleCase);
// 		map::map_debug("IN:{$map->IN},OUT:{$map->OUT},loadfleet:{$map->loadfleet}");
// 		map_debug($Parcours);
// 		map_debug($Parcours_det);
// 		map_debug(compact("vortex","Joueur","Planete","Asteroide","SC","Taille","pnj","ennemis"));
$mysql_result = $map->init_map();

if (!true) { // activer le traçage des vortex... (pour tester/vérifier le calcul de trajets)
    $ray = 15;
    list($xa, $ya) = map::ss2xy(5050);
    $list = array();
    for($y=-$ray;$y<=$ray;$y++)
        for($x=-$ray;$x<=$ray;$x++)
            $list[] = ($ya+$y).($xa+$x);
    $list = implode(',',$list);
    // 		map_debug("$list");
    $mysql_result2 = DataEngine::sql("SELECT POSIN,POSOUT from SQL_PREFIX_Coordonnee WHERE Type=1 and ( POSIN IN ($list) or POSOUT IN ($list) ) AND inactif=".intval($map->inactif)."");
    while($line = mysql_fetch_assoc($mysql_result2)) {
        img_line($image, $line['POSIN'], $line['POSOUT'], $colormap[($map->itineraire ? '14':'5')]);
    }
    mysql_free_result($mysql_result2);
}

while ($line=mysql_fetch_assoc($mysql_result)) {
    if ($CurrSS == 0) $CurrSS = $line['POSIN'];

    if ($line['POSIN'] != $CurrSS) {
        $map->add_ss(false,'img_addplot');
        $CurrSS   = $line['POSIN'];
//        if ($CurrSS == '1') xdebug_break();
    }

    $ID   = $line['ID'];
    $ss   = $line['POSIN'];

    if ($ss==-1) {
        map::map_debug($line);
    }
    $CurrSS_a[$ID] = $line;
    $CurrSS_a[$ID]['type'] = $map->ss_type($line);
    if (isset ($CurrSS_a[$CurrSS_a[$ID]['type']]))
        $CurrSS_a[$CurrSS_a[$ID]['type']]++;
    else
        $CurrSS_a[$CurrSS_a[$ID]['type']]=1;
}
mysql_free_result($mysql_result);

$map->add_ss(false,'img_addplot'); // last one

// Vortex restants...
if ($map->vortex && count($vortex_a) >0) foreach($vortex_a as $k => $v) {
        $map->add_ss($k,'img_addplot');
    }
if ($map->itineraire) { // traitement du parcours s'il y en a un (tracé+point étape)
    img_parcours();			// tracé
    img_parcours_dot();		// Rond des étapes
}
unset($vortex_a);


function initimg($Nbx,$Nby, $taillex,$tailley) {
    global $colormap, $image, $debug_cl;

    $map = map::getinstance();

    $image = imagecreate($Nbx*$taillex+2,$Nby*$tailley+2);
    $background_color = imagecolorallocate ($image, 0, 0, 0);
    $blanc = imagecolorallocate ($image, 254, 254, 254);
    $rouge = imagecolorallocate ($image, 85, 0, 64);

    imagefilledrectangle($image, 2, 2, $Nbx*$taillex+2, $Nby*$tailley+2, $background_color);

    // Tableau des couleurs...
    $colormap = DataEngine::config('MapColors');
//            ( $map->itineraire ? 0: $map->sc+1 );
    $colormap = $colormap[$map->itineraire ? 0: $map->sc+1];
    foreach ($colormap as $k => $c) {
        $R = hexdec(substr($c,1,2));
        $V = hexdec(substr($c,3,2));
        $B = hexdec(substr($c,5,2));
        $colormap[$k] = imagecolorallocate($image,$R,$V,$B);
    }
    $colormap[-1] = $debug_cl = $blanc;
    $colormap[-2] = $rouge;

    // Centre de communication...
    $comlevel = ownuniverse::getinstance()->get_comlevel();
    if (is_array($comlevel))
        foreach($comlevel as $k => $planet) {
            list($CoordsY,$CoordsX) = map::ss2xy($planet['ss']);
            $level = $planet['level'];
            if ($level>0)
                ImageFilledEllipse ($image,	1+($CoordsY-1)*$taillex + round($taillex/2)+1,
                        1+($CoordsX-1)*$tailley + round($tailley/2)+1,
                        (($level)*20) * $taillex, (($level)*20) * $tailley, $colormap["0"]);
        }

    ImageLine($image,1,1,$taillex*$Nbx,1,$rouge);
    ImageLine($image,1,1,1,$tailley*$Nby,$rouge);
    ImageLine($image,$taillex*$Nbx,1,$taillex*$Nbx,$tailley*$Nby,$rouge);
    ImageLine($image,1,$tailley*$Nby,$taillex*$Nbx,$tailley*$Nby,$rouge);

    if (DEBUG_IMG) {
        echo "Comm coords: $Coords x$CoordsX y$CoordsY lvl$level x".(1+($CoordsY-1)*$taillex + round($taillex/2)+1)." y".(1+($CoordsX-1)*$tailley + round($tailley/2)+1)."\n";
        echo "Map color full: ".($itineraire==1 ? 0: $map->sc+1)."\n";
        print_r(Config::GetMapColor( $map->itineraire ? 0: $map->sc+1 ));
        echo "Map color: \n";
        print_r($colormap);
    }

    return $image;
}

function img_addplot($ss, $data) {
    global $colormap, $image, $TailleCase,$map;
    $spacing = 1;

    $color = $colormap[$map->ss_colors($data)];
// TODO: issue 49 (remove this line)
    if ($data['Vortex'] >1) $color = $colormap[-1];

    if (isset($data['Chemin'])) return false; // Pas de carré pour le parcours

    list($PosX, $PosY) = map::ss2xy($ss);

    if ($ss==-1) {
        map::map_debug("$ss ");
        // 	map_debug(ss_colors($data), false);
        map::map_debug($data['Chemin'], false);
        map::map_debug($data);
    }

    $p1 = (($PosX-1)*$TailleCase)+$spacing+1;
    $p2 = ($PosY*$TailleCase)+$spacing+1;
    $p3 = ($p1+$TailleCase) - ($spacing*2);
    $p4 = ($p2+$TailleCase) - ($spacing*2);

    ImageFilledRectangle($image, $p1,$p2,$p3,$p4, $color);
}

function img_parcours_dot() {
    global $colormap, $image;//, $Parcours;

    $map = map::getinstance();
    $parcours = $map->parcours[1];

    $last = count($parcours)-1;

    foreach($parcours as $i => $ssdet) {
        $ssdet = $map->Parcours()->get_coords_part($ssdet);
        switch($i) {
            case 0:			$c = 20;
                break;
            case $last:		$c = 21;
                break;
            default:		$c = 22;
        }

        img_dot($image, $ssdet, $colormap[$c]);
    }
}


function img_parcours() {
    global $colormap, $image;//, $Parcours;

    $map = map::getinstance();
    $parcours = $map->parcours[1];

    $last = count($parcours)-1;
    $chemin = array();

    foreach($parcours as $ssdet)
        array_push($chemin, $map->Parcours()->get_coords_part($ssdet));

    foreach($chemin as $k => $v) {
        if (!is_numeric($k)) continue;
        if (!isset($chemin[$k+1])) break;
        img_line($image, $chemin[$k], $chemin[$k+1], $colormap[(($k%2)+24)]);
    }
}

function img_line($image,$in,$out,$clr) {
    $tc = map::getinstance()->taille/100;
    // 		map_debug("img_line($in,$out,$clr)");

    list($sX, $sY) = map::ss2xy($in);
    list($sX2, $sY2) = map::ss2xy($out);
    $x1 = floor( ($tc*$sX) - $tc/2 )+1;
    $y1 = floor( ($tc*($sY+1)) - $tc/2 )+1;
    $x2 = floor( ($tc*$sX2) - $tc/2 )+1;
    $y2 = floor( ($tc*($sY2+1)) - $tc/2 )+1;
    imageline($image, $x1, $y1, $x2, $y2, $clr);
}
function img_dot($image,$coord,$clr) {
    $tc = map::getinstance()->taille/100;
    $td = floor(($tc/2));
    $td = ($td%2) ? $td+3: $td+2;
    // 		map_debug("img_dot($coord,$clr)");

    list($sX, $sY) = map::ss2xy($coord);
    $x1 = floor( ($tc*$sX) - $tc/2 )+1;
    $y1 = floor( ($tc*($sY+1)) - $tc/2 )+1;
    imagefilledellipse($image,$x1,$y1,$td,$td,$clr);
}

// 		mysql_close($connexion);

if (DEBUG_PLAIN)
    header('Content-Disposition: attachment; filename=tatayoyo.png');
if (!DEBUG_IMG) {
    DataEngine::sql_do_spool();
    header('Content-type: image/png');
    header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
    imagepng($image);
}
imagedestroy($image);

if (DEBUG_IMG) print_r($GLOBALS);

?>