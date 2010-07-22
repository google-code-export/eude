<?php
/**
 * $Author: alex $
 * $Revision: 97 $
 **/

class cnh_fightstats_addons implements addon_config {

    public function Is_Enabled () { return true && $this->CheckPerms(); }

    public function Get_Menu () {

    $submenu = array(
                array(
                  '%ADDONS_URL%cnh_fightstats/index.php',
                  '%BTN_URL%btn-stats.png',
                  'DataEngine::CheckPerms(AXX_GUEST)')
        );

      
    return array(
      'insertafter' => 'addon', // empty for first.(ceux déjà inclus: carto,perso,admin,forum, et logout)
      'id' => 'cnhmod', // doit être unique ! (pas écraser qui que ce soit d'autre)
      'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
      'menu' => $submenu); // 					"onlysub" => "key", // hmm...
    }

    /**
     * Si actif, le paramètre du menu 'insertafter' doit être 'addon'
     * ainsi que le paramètre 'onlysub' a true
     * @return boolean
     */
    public function InSubAddonMenu () { return false; }
    
    public function CheckPerms () { return DataEngine::CheckPerms(AXX_MEMBER); }

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
	
    public function OnNewUser($user) {
        FB::info($user,'addons::OnNewUser');
        return true;
    }
}
