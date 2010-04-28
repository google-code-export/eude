<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

class sample_addons implements addon_config {

    public function Is_Enabled () {
        if (!Members::CheckPermsKey('addons_sample'))
            Members::CheckPermsKeyAdd('addons_sample', AXX_GUEST);
        return true;

    }

    public function CheckPerms () {
        return Members::CheckPerms('addons_sample');
    }

    public function Get_Menu () {

        // menu simple.
        $menu = array('%ADDONS_URL%sample/index.php','%IMAGES_URL%test.png',125,'Members::CheckPerms(\'addons_sample\')', null);

        // menu + sous menu
        $menu2 = array('%ADDONS_URL%sample/index.php','%IMAGES_URL%test.png',125,'DataEngine::CheckPerms(\'addons_sample\')',
                array(
                        array('%ADDONS_URL%sample/index.php','%IMAGES_URL%test.png','true'), // sous-menu 1
                        array('%ROOT_URL%index.php','%IMAGES_URL%btn-cartographie.png','true'), // sous-menu 2
                ),
        );

        // juste la partie 'sous-menu'
        $submenu = array(
                array('%ADDONS_URL%sample/index.php','%IMAGES_URL%test.png','true'), // sous-menu 1
                array('%ROOT_URL%index.php','%IMAGES_URL%btn-cartographie.png','true'), // sous-menu 2
        );

        return array('insertafter' => 'perso', // empty for first.(ceux déjà inclus: carto,perso,addon,admin,forum, et logout)
                'id' => 'idsample', // doit être unique ! (pas écraser qui que ce soit d'autre)
                'onlysub' => false, // ajout a la fin du menu existant (champ 'id' ignoré)
                'menu' => $menu);
    }

    /**
     * Si actif, le paramètre du menu 'insertafter' doit être 'addon'
     * ainsi que le paramètre 'onlysub' a true
     * @return boolean
     */
    public function InSubAddonMenu () {
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
    public function GetCustomPerms() {
        return array('addons_sample'=>'Addons d\'exemple...');
    }
}

