<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
//-- Début partie personalisable -----------------------------------------------
$listing = array();

$listing['cartographie'] = 'CARTOGRAPHIE';
$listing['tableau'] = 'TABLEAU';
$listing['carte'] = 'CARTE';

$listing['mafiche'] = 'MA FICHE';
$listing['recherche'] = 'RECHERCHE';
$listing['ownuniverse'] = 'PRODUCTION';
$listing['pillage'] = 'PILLAGE';

$listing['addon'] = 'ADDONS';

$listing['membres'] = 'MEMBRES';
$listing['hierarchie'] = 'HIERARCHIE';
$listing['editmembres'] = 'EDITIONS';
$listing['stats'] = 'STATISTIQUES';
$listing['eadmin'] = 'ADMIN';

$listing['forum'] = 'FORUM';

$listing['logout'] = 'DECONNEXION';

$listing['do_parcours'] = 'ITINERAIRE';
$listing['testonly'] = 'DEV ONLY';

function do_btn($key) {
    global $listing;
    $text = $listing[$key];

    list($r, $g, $b) = array(hexdec('1F'), hexdec('1F'), hexdec('99')); // #1F1F99
    $img = img::Create(160, 30)->FillAlpha($r + 1, $g + 1, $b + 1);

    $img->font = './hachicro.ttf';

    list($r, $g, $b) = array(hexdec('66'), hexdec('a3'), hexdec('ff')); // #66a3ff
    $img->SetColor($r, $g, $b)->CenteredText($text);


//-- Fin partie personalisable. ------------------------------------------------
    $img->SaveAs($key . '.png')->Render();
}

include_once('../../../../init.php');
include_once(INCLUDE_PATH . 'Script.php');

Members::CheckPermsOrDie(AXX_ROOTADMIN);

addons::getinstance()->ButtonRegen($listing);

if (isset($_GET['ident'])) {
    include CLASS_PATH . 'img.class.php';
    do_btn(gpc_esc($_GET['ident']));
} else {
    include TEMPLATE_PATH . 'sample.tpl.php';
    $tpl = tpl_sample::getinstance();

    $files = scandir('./');
    foreach ($files as $file)
        if (substr($file, -4) == '.png')
            unlink($file);

    foreach ($listing as $key => $dummy)
        $tpl->PushOutput('<span class="color_header"> &#37;BTN_URL%' . $key . '.png </span><img src="./gen.php?ident=' . $key . '"/><br/>');

    $tpl->DoOutput(false);
}