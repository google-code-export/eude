<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

class devissues_addons implements addon_config {

    public function Is_Enabled () {
        if (!Members::CheckPermsKey('in_dev'))
            Members::CheckPermsKeyAdd('in_dev', AXX_DISABLED);
        return true;

    }

    public function CheckPerms () {
        return Members::CheckPerms('in_dev');
    }

    public function Get_Menu () {

        // menu simple.
        $menu = array('%ROOT_URL%cartographie.php','%IMAGES_URL%test.png',125,'Members::CheckPerms(\'in_dev\')', null);

        // menu + sous menu
        $menu2 = array('%ADDONS_URL%sample/index.php','%IMAGES_URL%test.png',125,'DataEngine::CheckPerms(AXX_ROOTADMIN)',
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
                'id' => 'iddev', // doit être unique ! (pas écraser qui que ce soit d'autre)
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
        srand();
        return array(rand()+100=>'Config de dev...',
            'in_dev'=>'Menu pour le dev',
            rand()+100=>'&nbsp;');
    }
}

