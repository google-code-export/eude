<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
//-- Début partie personalisable -----------------------------------------------
$listing = array();
// http://www.dafont.com/
// http://www.w3schools.com/tags/ref_colorpicker.asp
// http://css4design.com/choisir-sa-palette-de-couleur
// $defaultsetting = array(fontfile, fontsize, alphacolor, textcolor);
$defaultsetting = array(CACHE_PATH . 'CGF Locust Resistance.ttf', 10, '202c32', '666666');

$listing['cartographie'] = array($defaultsetting, 'CARTOGRAPHIE');
$listing['tableau'] = array($defaultsetting, 'TABLEAU');
$listing['carte'] = array($defaultsetting, 'CARTE');

$listing['mafiche'] = array($defaultsetting, 'MA FICHE');
$listing['recherche'] = array($defaultsetting, 'RECHERCHE');
$listing['ownuniverse'] = array($defaultsetting, 'PRODUCTION');
$listing['pillage'] = array($defaultsetting, 'PILLAGE');

$listing['addon'] = array($defaultsetting, 'ADDONS');

$listing['membres'] = array($defaultsetting, 'MEMBRES');
$listing['hierarchie'] = array($defaultsetting, 'HIERARCHIE');
$listing['editmembres'] = array($defaultsetting, 'EDITIONS');
$listing['stats'] = array($defaultsetting, 'STATISTIQUES');
$listing['eadmin'] = array($defaultsetting, 'ADMIN');

$listing['forum'] = array($defaultsetting, 'FORUM');

$listing['logout'] = array($defaultsetting, 'DECONNEXION');

$listing['do_parcours'] = array($defaultsetting, 'ITINERAIRE');
$setting = $defaultsetting;
$setting[3] = '#FF9900';
$listing['testonly'] = array($setting, 'DEV ONLY');
$setting = $defaultsetting;
$setting[1] = 6;
$listing['eude'] = array($setting, "EU\nDE");

function do_btn($key, $listing) {
    list($param, $text) = $listing[$key];
    list($fontfile, $fontsize, $alphacolor, $textcolor) = $param;
    $width = 160;
    $height = 30;
    if ($key == 'eude')
        $width = $height = 16;

    $img = img::Create($width, $height)->FillAlphaHexa($alphacolor);

    $img->font = $fontfile;
    $img->SetColorHexa($textcolor);
    if ($key == 'eude')
        $img->Text($text, 0, 8, $fontsize);
    else
        $img->CenteredText($text, $fontsize);

//-- Fin partie personalisable. ------------------------------------------------
    $img->SaveAs(CACHE_PATH . 'btn-' . $key . '.png');
}

addons::getinstance()->ButtonRegen($listing, $defaultsetting);