<?php

/**
 * @Author: Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 * */
class advanced_scanner_addons implements addon_config {

    public function ScanServer() {
        return 'australis.eu2.looki.fr';
//        return 'borealis.eu2.looki.fr';
//        return 'eu2.looki.com';
    }

    public function ScanRay($type) {
        switch ($type) {
            case 'vortex':
                return 5;
                break;
            case 'planets':
                return 20;
                break;
            default:
                return 5;
        }
    }

    public function Is_Enabled() {
        if (!Members::CheckPermsKey('ADVANCED_SCANNER'))
            Members::CheckPermsKeyAdd('ADVANCED_SCANNER', AXX_DISABLED);

        define('SCANNER_PATH', ADDONS_PATH . 'advanced_scanner' . DIRECTORY_SEPARATOR);
        define('SCANNER_URL', ADDONS_URL . 'advanced_scanner/');

        return true;
    }

    public function CheckPerms() {
        return Members::CheckPerms('ADVANCED_SCANNER');
    }

    public function Get_Menu() {
        // juste la partie 'sous-menu'
        $submenu = array(
            array(SCANNER_URL, '%BTN_URL%addons_advancedscanner.png', 'DataEngine::CheckPerms("CARTOGRAPHIE_SCANNER")'), // sous-menu 1
        );
        return array('insertafter' => 'carto', // empty for first.(ceux déjà inclus: carto,perso,addon,admin,forum, et logout)
            'id' => 'idsample', // doit être unique ! (pas écraser qui que ce soit d'autre)
            'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
            'menu' => $submenu);
    }

    public function InSubAddonMenu() {
        return false;
    }

    public function OnButtonRegen(&$listing, $defaultsetting) {
        $lng = language::getinstance()->GetLngBlock(LNG_CODE, SCANNER_PATH);

        // $defaultsetting = array(fontfile, fontsize, alphacolor, textcolor);
        $defaultsetting[3] = '#00FF00';
        $listing['addons_advancedscanner'] = array($defaultsetting, $lng['conf_btn']);
        return true;
    }

    public function GetCustomPerms() {
        $lng = language::getinstance()->GetLngBlock(LNG_CODE, SCANNER_PATH);
        $value = array();
        $value[1000] = $lng['conf_perms'];
        $value['ADVANCED_SCANNER'] = $lng['conf_perms_global'];
        return $value;
    }

}