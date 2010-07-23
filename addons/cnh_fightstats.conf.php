<?php
/**
 * $Author: alex $
 * $Revision: 97 $
 **/

class cnh_fightstats_addons implements addon_config {

    public function Get_Menu () {
    $submenu = array(
                array('%ADDONS_URL%cnh_fightstats/index.php','%BTN_URL%btn-stats.png', 'DataEngine::CheckPerms("STATS_COMBAT")'));
      
    return array(
      'insertafter' => 'addon', // empty for first.(ceux déjà inclus: carto,perso,admin,forum, et logout)
      'id' => 'cnhmod', // doit être unique ! (pas écraser qui que ce soit d'autre)
      'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
      'menu' => $submenu); // 					"onlysub" => "key", // hmm...
    }

    public function InSubAddonMenu () { return false; }
 
    public function Is_Enabled() {
        if (!Members::CheckPermsKey('STATS_COMBAT'))
            Members::CheckPermsKeyAdd('STATS_COMBAT', AXX_ROOTADMIN);
    return true;
    }
	
    public function CheckPerms () 
	{ return DataEngine::CheckPerms('STATS_COMBAT'); 
	}

    public function OnDeleteUser($user) {
        FB::info($user,'addons::OnDeleteUser');
        return true;
    }
	
    public function OnButtonRegen(&$listing, $defaultsetting) {
        // $defaultsetting = array(fontfile, fontsize, alphacolor, textcolor);
        switch (LNG_CODE) {
            case 'en':
                $listing['btn-stats'] = array($defaultsetting,'BATLE STAT');
            default:
                $listing['btn-stats'] = array($defaultsetting,'STAT COMBAT');
        }
        return true;
    }	

    public function GetCustomPerms() {
        switch (LNG_CODE) {
            case 'en':
                return array('STATS_COMBAT' => 'Battle Stats Fights');
            default:
                return array('STATS_COMBAT' => 'Statistique de Combats');
        }
    }
	
    public function OnNewUser($user) {
        FB::info($user,'addons::OnNewUser');
        return true;
    }
}
