<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
define('IS_IMG', true);

require_once('./init.php');
require_once(INCLUDE_PATH . 'Script.php');
require_once(CLASS_PATH . 'map.class.php');
require_once(CLASS_PATH . 'parser.class.php');
require_once(CLASS_PATH . 'ownuniverse.class.php');

DataEngine::conf_cache('EmpireAllys');
DataEngine::conf_cache('EmpireEnnemy');
DataEngine::conf_cache('MapColors');

$map = map::getinstance(); // initialisation...
/// INIT ///

$NbCase = 100;
$TailleCase = floor($map->taille / $NbCase);
$image = initimg($NbCase, $NbCase, $TailleCase, $TailleCase);
$mysql_result = $map->init_map();

while ($line = mysql_fetch_assoc($mysql_result)) {
    if ($CurrSS == 0)
        $CurrSS = $line['POSIN'];

    if ($line['POSIN'] != $CurrSS) {
        $map->add_ss(false, 'img_addplot');
        $CurrSS = $line['POSIN'];
    }

    $ID = $line['ID'];
    $ss = $line['POSIN'];

    $CurrSS_a[$ID] = $line;
    $CurrSS_a[$ID]['type'] = $map->ss_type($line);
    if (isset($CurrSS_a[$CurrSS_a[$ID]['type']]))
        $CurrSS_a[$CurrSS_a[$ID]['type']]++;
    else
        $CurrSS_a[$CurrSS_a[$ID]['type']] = 1;
}
mysql_free_result($mysql_result);

$map->add_ss(false, 'img_addplot'); // last one
// Vortex restants...
if ($map->vortex && count($vortex_a) > 0)
    foreach ($vortex_a as $k => $v) {
        $map->add_ss($k, 'img_addplot');
    }
if ($map->itineraire) { // traitement du parcours s'il y en a un (tracé+point étape)
    img_parcours();   // tracé
    img_parcours_dot();  // Rond des étapes
}
unset($vortex_a);

function initimg($Nbx, $Nby, $taillex, $tailley) {
    global $colormap, $image, $debug_cl;

    $map = map::getinstance();

    $image = imagecreate($Nbx * $taillex, $Nby * $tailley);
    $background_color = imagecolorallocate($image, 0, 0, 0);
    $blanc = imagecolorallocate($image, 254, 254, 254);
    $rouge = imagecolorallocate($image, 85, 0, 64);

    imagefilledrectangle($image, 0, 0, $Nbx * $taillex, $Nby * $tailley, $background_color);

    // Tableau des couleurs...
    $colormap = DataEngine::config('MapColors');
    $colormap = $colormap[$map->itineraire ? 0 : $map->sc + 1];
    if ($map->itineraire)
        $map->load_prefs('1;0;0;0;' . $map->sc . ';' . $map->taille . ';0;0;0');
    foreach ($colormap as $k => $c) {
        $R = hexdec(substr($c, 1, 2));
        $V = hexdec(substr($c, 3, 2));
        $B = hexdec(substr($c, 5, 2));
        $colormap[$k] = imagecolorallocate($image, $R, $V, $B);
    }
    $colormap[-1] = $debug_cl = $blanc;
    $colormap[-2] = $rouge;

    // Centre de communication...
    $comlevel = ownuniverse::getinstance()->get_comlevel();
    if (is_array($comlevel))
        foreach ($comlevel as $k => $planet) {
            list($CoordsY, $CoordsX) = map::ss2xy($planet['ss']);
            $level = $planet['level'];
            if ($level > 0)
                ImageFilledEllipse($image, 1 + ($CoordsY - 1) * $taillex + round($taillex / 2) + 1,
                        1 + ($CoordsX - 1) * $tailley + round($tailley / 2) + 1,
                        (($level) * 20) * $taillex, (($level) * 20) * $tailley, $colormap["0"]);
        }
    return $image;
}

function img_addplot($ss, $data) {
    global $colormap, $image, $TailleCase, $map;
    $spacing = 1;

    $color = $colormap[$map->ss_colors($data)];

    if (isset($data['Chemin']))
        return false; // Pas de carré pour le parcours

        list($PosX, $PosY) = map::ss2xy($ss);

    $p1 = (($PosX - 1) * $TailleCase) + $spacing;
    $p2 = ($PosY * $TailleCase) + $spacing;
    $p3 = ($p1 + $TailleCase) - ($spacing * 2);
    $p4 = ($p2 + $TailleCase) - ($spacing * 2);

    ImageFilledRectangle($image, $p1, $p2, $p3, $p4, $color);
}

function img_parcours_dot() {
    global $colormap, $image; //, $Parcours;

    $map = map::getinstance();
    $parcours = $map->parcours[1];

    $last = count($parcours) - 1;

    foreach ($parcours as $i => $ssdet) {
        $ssdet = $map->Parcours()->get_coords_part($ssdet);
        switch ($i) {
            case 0: $c = 20;
                break;
            case $last: $c = 21;
                break;
            default: $c = 22;
        }

        img_dot($image, $ssdet, $colormap[$c]);
    }
}

function img_parcours() {
    global $colormap, $image; //, $Parcours;

    $map = map::getinstance();
    $parcours = $map->parcours[1];

    $last = count($parcours) - 1;
    $chemin = array();

    foreach ($parcours as $ssdet)
        array_push($chemin, $map->Parcours()->get_coords_part($ssdet));

    foreach ($chemin as $k => $v) {
        if (!is_numeric($k))
            continue;
        if (!isset($chemin[$k + 1]))
            break;
        img_line($image, $chemin[$k], $chemin[$k + 1], $colormap[(($k % 2) + 24)]);
    }
}

function img_line($image, $in, $out, $clr) {
    $tc = map::getinstance()->taille / 100;

    list($sX, $sY) = map::ss2xy($in);
    list($sX2, $sY2) = map::ss2xy($out);
    $x1 = floor(($tc * $sX) - $tc / 2);
    $y1 = floor(($tc * ($sY + 1)) - $tc / 2);
    $x2 = floor(($tc * $sX2) - $tc / 2);
    $y2 = floor(($tc * ($sY2 + 1)) - $tc / 2);
    imageline($image, $x1, $y1, $x2, $y2, $clr);
}

function img_dot($image, $coord, $clr) {
    $tc = map::getinstance()->taille / 100;
    $td = floor(($tc / 2));
    $td = ($td % 2) ? $td + 3 : $td + 2;

    list($sX, $sY) = map::ss2xy($coord);
    $x1 = floor(($tc * $sX) - $tc / 2);
    $y1 = floor(($tc * ($sY + 1)) - $tc / 2);
    imagefilledellipse($image, $x1, $y1, $td, $td, $clr);
}

DataEngine::sql_do_spool();
header('Content-type: image/png');
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
imagepng($image);

imagedestroy($image);
