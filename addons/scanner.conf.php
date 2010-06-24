<?php

/**
 * @Author: Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 * */
class scanner_addons implements addon_config {

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
        if (!Members::CheckPermsKey('CARTOGRAPHIE_SCANNER'))
            Members::CheckPermsKeyAdd('CARTOGRAPHIE_SCANNER', AXX_ROOTADMIN);

        return true;
    }

    public function CheckPerms() {
        return Members::CheckPerms('CARTOGRAPHIE_SCANNER');
    }

    public function Get_Menu() {

        // juste la partie 'sous-menu'
        $submenu = array(
//                array('%ROOT_URL%cartographie.php','%BTN_URL%cartographie.png','true'), // sous-menu 1
            array('%ADDONS_URL%scanner/index.php', '%BTN_URL%addons_scanner.png', 'DataEngine::CheckPerms("CARTOGRAPHIE_SCANNER")'), // sous-menu 1
        );

        return array('insertafter' => 'carto', // empty for first.(ceux déjà inclus: carto,perso,addon,admin,forum, et logout)
            'id' => 'idsample', // doit être unique ! (pas écraser qui que ce soit d'autre)
            'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
            'menu' => $submenu);
    }

    public function InSubAddonMenu() {
        return false;
    }

    public function OnDeleteUser($user) {
        return true;
    }

    public function OnNewUser($user) {
        return true;
    }

    public function OnVortexCleaned() {
        return true;
    }

    public function OnButtonRegen(&$listing, $defaultsetting) {
        // $defaultsetting = array(fontfile, fontsize, alphacolor, textcolor);
        switch (LNG_CODE) {
            case 'en':
                $listing['addons_scanner'] = array($defaultsetting,'SCANNER');
            default:
                $listing['addons_scanner'] = array($defaultsetting,'SCANNEUR');
        }
        return true;
    }

    public function GetCustomPerms() {
        switch (LNG_CODE) {
            case 'en':
                return array('CARTOGRAPHIE_SCANNER' => 'Automated wormhole scanner');
            default:
                return array('CARTOGRAPHIE_SCANNER' => 'Scanneur de vortex automatique');
        }
    }

}