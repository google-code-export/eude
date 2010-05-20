<?php
/**
 * @Author: Wilfried.Winner
 * $Revision: Triangulation v1.3
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/

class triangulation_addons implements addon_config {

    public function Is_Enabled () {
        if (!Members::CheckPermsKey('CARTOGRAPHIE_TRIANGULATION'))
            Members::CheckPermsKeyAdd('CARTOGRAPHIE_TRIANGULATION', AXX_MEMBER);
        return true;
    }
    public function CheckPerms () {
        return Members::CheckPerms('CARTOGRAPHIE_TRIANGULATION');
    }

    public function Get_Menu () {

        // juste la partie 'sous-menu'
        $submenu = array(
//                array('%ROOT_URL%cartographie.php','%IMAGES_URL%btn-cartographie.png','true'), // sous-menu 1
                array('%ADDONS_URL%triangulation/index.php','%IMAGES_URL%Btn-triangulation.png','DataEngine::CheckPerms("CARTOGRAPHIE_PLAYERS")'), // sous-menu 1
        );

        return array('insertafter' => 'carto', // empty for first.(ceux déjà inclus: carto,perso,addon,admin,forum, et logout)
                'id' => 'idsample', // doit être unique ! (pas écraser qui que ce soit d'autre)
                'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
                'menu' => $submenu);
    }

    public function InSubAddonMenu () {
        return false;
    }
    public function OnDeleteUser($user) {
        return true;
    }
    public function OnNewUser($user) {
        return true;
    }
    public function OnVortexCleaned() {
//        FB::info('addons::OnVortexCleaned');
        return true;
    }
    public function GetCustomPerms() {
        return array('CARTOGRAPHIE_TRIANGULATION'=>'Outils de triangulation');
    }
}