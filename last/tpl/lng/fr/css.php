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
include ROOT_PATH.'Script/Script.php';

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
ob_start();
?>
/*<style type="hack ide syntax">*/
    body {
        background-color: black;
        background-image: url(%IMAGES_URL%Fond-Site.jpg);
        background-repeat: repeat;
        font-family: Geneva,Arial,Helvetica,sans-serif;
        font-size:12px;
    }

    address, a:link, a:active, a:visited {
        color: #ffffff;
        text-decoration: none;
    }
    a:hover {
        color: #00FF00;
    }

    img {
        border: 0px;
    }

    /* @since 1.4.2 */

    .base_h {
        background-color: #aaaaaa;
    }
    .base_t {
        color: red;
    }
    .base_row0 {
        background-color: #cccccc;
    }
    .base_row1 {
        background-color: #d6d6d6;
    }
    .color_cibleur {
        background-color: white;
    }

    .color_bg {
        background-color: #330033;
        color: white;
    }
    .color_header {
        background-color: #800040;
        color: white;
        font-weight: bold;
    }
    .color_bigheader {
        background-color: #800040;
        color: #CB9E03;
        font-size: 20px;
        font-weight: bold;
    }

    .color_row0 {
        background-color: #660050;
        color: white;
    }
    .color_row1 {
        background-color: #480038;
        color: white;
    }
    .table_center {
        margin-left:auto;
        margin-right:auto;
    }
    .table_nospacing {
        border-width: 0px;
        border-spacing: 0px;
        border-collapse: collapse;
    }
    .spacing_row {
        border-right: none !important;
        padding: 2px 6px;
    }
    td.spacing_row0 {
        border-right: solid 3px #480038;
        padding: 2px 6px;
    }
    td.spacing_row1 {
        border-right: solid 3px #660050;
        padding: 2px 6px;
    }
    tr.spacing_header {
        border-top: solid 3px #800040;
    }
    tr.spacing_header td {
        padding: 2px 6px;
        border-right: solid 3px #660050;
    }

    .color_titre {
        background-color: #800040;
        color: #FFCCCC;
        font-weight: bold;
    }
    .color_cols {
        background-color: #480038;
        color: #FFCCCC;
        font-weight: bold;
    }
    .color_pagination {
        color: #FF944E;
    }
    .text_center {
        text-align: center;
    }
    .text_right {
        text-align: right;
    }

    .link {
        cursor: pointer;
    }

    .size20  {width:20px;}
    .size40  {width:40px;}
    .size60  {width:60px;}
    .size80  {width:80px;}
    .size110 {width:110px;}
    .size180 {width:180px;}
    .size250 {width:250px;}

    /* Vous avez un nouveau message, cliquez pour supprimer, ne rien toucher pour conserver... */
    div.messagerbg {
        background-color:#330033;
        bottom:0;
        height:100%;
        left:0;
        opacity:0.75;
        position:fixed;
        top:0;
        width:100%;
        z-index:89;
    }
    div.newmessage {
        position:absolute;
        top:25%;
        height:50%;
        width: 100%;
        opacity: 1;
        z-index:90;
    }
    table.newmessage {
        background-color: #800040;
        color: white;
        font-weight: bold;
        text-align: center;
        margin-left:auto;
        margin-right:auto;
        width: 700px;
    }

    .ress_0 {
        position: absolute;
        background-color: red;
        width: 3px;
    }
    .ress_1 {
        position: absolute;
        background-color: red;
        width: 10px;
    }

    .ress_2 {
        position: absolute;
        background-color: red;
        width: 20px;
    }

    .ress_3{
        position: absolute;
        background-color: red;
        width: 20px;
    }

    .ress_4 {
        position: absolute;
        background-color: darkorange;
        width: 40px;
    }

    .ress_5 {
        position: absolute;
        background-color: darkorange;
        width: 50px;
    }

    .ress_6 {
        position: absolute;
        background-color: darkorange;
        width: 60px;
    }

    .ress_7 {
        position: absolute;
        background-color: darkgreen;
        width: 70px;
    }

    .ress_8 {
        position: absolute;
        background-color: darkgreen;
        width: 80px;
    }

    .ress_9 {
        position: absolute;
        background-color: darkgreen;
        width: 90px;
    }

    .ress_10 {
        position: absolute;
        background-color: darkgreen;
        width: 100px;
    }
    #ress_text {
        position: relative;
        color: transparent;
    }
    #ress_text:hover {
        color: white;
    }
    .ress_img {
        position: relative;
        padding-right: 3px;
        padding-left: 20px;
    }

    .infobulle {
        z-index:7;
        position:absolute;
        visibility:hidden;
        border: 1px solid White;
        padding: 10px;
        background-color: #FFE38F;
        white-space:nowrap;
    }

    .listing {
        padding:0px;
        list-style-type:none;
        list-style-position:inside;
    }

    /*</style>*/
<?php
$css = ob_get_clean();

header('Content-type: text/css');
header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime(__FILE__)) . " GMT");

$css = str_replace('%ROOT_URL%', ROOT_URL, $css);
$css = str_replace('%INCLUDE_URL%', INCLUDE_URL, $css);
$css = str_replace('%IMAGES_URL%', IMAGES_URL, $css);
$css = str_replace('%TEMPLATE_URL%', TEMPLATE_URL, $css);
$css = str_replace('%ADDONS_URL%', ADDONS_URL, $css);
$css = str_replace('%LNG_URL%', TEMPLATE_URL.'lng'.DIRECTORY_SEPARATOR.LNG_CODE.DIRECTORY_SEPARATOR, $css);
echo $css;
