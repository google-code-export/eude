<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
class wormhole_import_addons implements addon_config {

    public function Is_Enabled() {
        if (!Members::CheckPermsKey('addons_wormhole_import'))
            Members::CheckPermsKeyAdd('addons_wormhole_import', AXX_ROOTADMIN);
        return true;
    }

    public function CheckPerms() {
        return Members::CheckPerms('addons_wormhole_import');
    }

    public function Get_Menu() {

        // menu simple.
        $menu = array('%ADDONS_URL%sample/index.php', '%BTN_URL%addons_sample.png', 160, 'Members::CheckPerms(\'addons_sample\')', null);

        // menu + sous menu
        $menu2 = array('%ADDONS_URL%sample/index.php', '%BTN_URL%testonly.png', 160, 'Members::CheckPerms(\'addons_sample\')',
            array(
                array('%ADDONS_URL%sample/index.php', '%BTN_URL%addons_sample.png', 'true'), // sous-menu 1
                array('%ROOT_URL%index.php', '%BTN_URL%cartographie.png', 'true'), // sous-menu 2
            ),
        );

        // juste la partie 'sous-menu'
        $submenu = array(
            array('%ADDONS_URL%wormhole_import/index.php', '%BTN_URL%addons_wormhole_import.png', 'true'),
        );

        return array('insertafter' => 'carto', // empty for first.(ceux déjà inclus: carto,perso,addon,admin,forum, et logout)
            'id' => 'idwormhole_import', // doit être unique ! (pas écraser qui que ce soit d'autre)
            'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
            'menu' => $submenu);
    }

    /**
     * Si actif, le paramètre du menu 'insertafter' doit être 'addon'
     * ainsi que le paramètre 'onlysub' a true
     * @return boolean
     */
    public function InSubAddonMenu() {
        return false;
    }

    public function OnDeleteUser($user) {
//        FB::info($user,'addons::OnDeleteUser');
        return true;
    }

    public function OnNewUser($user) {
//        FB::info($user,'addons::OnNewUser');
        return true;
    }

    public function OnVortexCleaned() {
//        FB::info('addons::OnVortexCleaned');
        return true;
    }

    public function OnButtonRegen(&$listing, $defaultsetting) {
        // $defaultsetting = array(fontfile, fontsize, alphacolor, textcolor);
        $defaultsetting[3] = '#FF9900';
        $listing['addons_wormhole_import'] = array($defaultsetting, 'IMPORT VORTEX');
        return true;
    }

    public function GetCustomPerms() {
        return array('addons_wormhole_import' => 'Importation des vortex');
    }

}

