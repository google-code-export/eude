<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 */
define('CHECK_LOGIN', false);
include '../../../init.php';
include ROOT_PATH . 'Script/Script.php';

// http://www.w3schools.com/tags/ref_colorpicker.asp
// http://css4design.com/choisir-sa-palette-de-couleur
$color_txt = 'white';
$color_1 = '#2d3a40'; // alias row0 bg, couleur 'claire'
$color_2 = '#202c32'; // alias row1 bg, couleur 'plus foncé'
$color_3 = '#3f3b38'; // alias header bg
$color_4 = '#666666'; // alias header text
$color_5 = '#CC7A00'; // alias big header text
$color_6 = '#202c32'; // alias bg discret...
$color_infobulle = '#ffe38f'; // alias infobulle bg

$cls['color_lnk'] = $color_txt;
$cls['color_lnk_hover'] = '#00FF00';
$cls['color_bg'] = $color_6;
$cls['color_cibleur'] = $color_txt; // bg
$cls['color_pagination'] = $color_4;
$cls['color_header'] = $color_txt;
$cls['color_header_bg'] = $color_3;
$cls['color_bigheader'] = $color_5;
$cls['color_bigheader_bg'] = $color_3;
$cls['color_titre'] = $color_4;
$cls['color_titre_bg'] = $color_3;
$cls['color_cols'] = $color_4;
$cls['color_cols_bg'] = $color_2;
$cls['color_row0'] = $color_txt;
$cls['color_row0_bg'] = $color_1;
$cls['color_row1'] = $color_txt;
$cls['color_row1_bg'] = $color_2;
$cls['color_spacing_row0'] = $cls['color_row1_bg'];
$cls['color_spacing_row1'] = $cls['color_row0_bg'];
$cls['color_spacing_h_tr'] = $color_3;
$cls['color_spacing_h_td'] = $color_1;
$cls['color_infobulle'] = $color_infobulle;


/*
  Listing couleurs...
  - nom css ----------|- couleur -|- bgc -|- Autre -------------------------------
  color_cibleur       | white     |       |
  color_pagination    | ff944e    |       |
  color_header        | white     |800040 |
  color_bigheader     | cb9e03    |800040 |
  color_titre         | ffcccc    |800040 |
  color_cols          | ffcccc    |480038 |
  color_row0          | white     |660050 |
  color_row1          | white     |480038 |
  spacing_row0        |           |       | bordure droite 480038
  spacing_row1        |           |       | bordure droite 660050
  tr.spacing_header   |           |       | bordure top    800040
  tr.spacing_header td|           |       | bordure droite 660050
  infobulle           |           |ffe38f |
  messager & co       |           |330033 & 800040 |

 */
$css = file_get_contents('./template.css');

$mtime = max(filemtime(__FILE__), filemtime('./template.css'));
header('Content-type: text/css');
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $mtime) . " GMT");

foreach ($cls as $key => $color)
    $css = str_replace("%$key%", $color, $css);

$css = str_replace('%ROOT_URL%', ROOT_URL, $css);
$css = str_replace('%INCLUDE_URL%', INCLUDE_URL, $css);
$css = str_replace('%IMAGES_URL%', IMAGES_URL, $css);
$css = str_replace('%TEMPLATE_URL%', TEMPLATE_URL, $css);
$css = str_replace('%ADDONS_URL%', ADDONS_URL, $css);
$css = str_replace('%LNG_URL%', TEMPLATE_URL . 'lng/' . LNG_CODE . '/', $css);
echo $css;
