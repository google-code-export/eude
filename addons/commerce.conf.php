<?php

class commerce_addons implements addon_config {

    public function Is_Enabled () {
        if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_INDEX'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_INDEX', AXX_MEMBER);
        if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_MODULES'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_MODULES', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_TPL_INSERT'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_TPL_INSERT', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_TPL_EDIT'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_TPL_EDIT', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_TPL_DELETE'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_TPL_DELETE', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_GESTION'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_GESTION', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_STATS'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_STATS', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_PREF'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_PREF', AXX_MEMBER);
		if (!DataEngine::CheckPermsKey('ZZZ_COMMERCE_IMPORT'))
            DataEngine::CheckPermsKeyAdd('ZZZ_COMMERCE_IMPORT', AXX_MEMBER);
        return true;
    }
    public function CheckPerms () {
        return DataEngine::CheckPerms('ZZZ_COMMERCE_INDEX');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_MODULES');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_INSERT');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_EDIT');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_GESTION');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_STATS');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_PREF');
        return DataEngine::CheckPerms('ZZZ_COMMERCE_IMPORT');
    }

public function GetCustomPerms() {
        srand();
        return array(rand()+666=>'Addon commerce',
                     'ZZZ_COMMERCE_INDEX'=>'Page accueil',
					 'ZZZ_COMMERCE_MODULES'=>'Pages module et commerce',
					 'ZZZ_COMMERCE_GESTION'=>'Page gestion des commandes',
					 'ZZZ_COMMERCE_STATS'=>'Pages statistique',
					 'ZZZ_COMMERCE_PREF'=>'Pages préférences',
					 'ZZZ_COMMERCE_IMPORT'=>'Pages importation',
					 'ZZZ_COMMERCE_TPL_INSERT'=>'Insertion de templates de modules',
					 'ZZZ_COMMERCE_TPL_EDIT'=>'Edition de templates de modules',
					 'ZZZ_COMMERCE_TPL_DELETE'=>'Suppression de templates de modules');
	}



    public function Get_Menu () {

		   $root_url=ROOT_URL;

		$Joueur = $_SESSION['_login'];
		$sqlreq = '
	  SELECT * FROM `SQL_PREFIX_modules_commandes` 
	  WHERE (`DateLivraison` IS NULL) 
	  AND `LoginV` = "'.$Joueur.'" 
	  ORDER BY `DateCreated`';
		$mysql_result = DataEngine::sql($sqlreq);
		if (mysql_num_rows($mysql_result) > 0) {
		$bouton = '%ADDONS_URL%commerce/images/menu/btn-modules-red.png';
		$menu = array('%ADDONS_URL%commerce/commandes_list.php',$bouton,155,'DataEngine::CheckPerms("ZZZ_COMMERCE_INDEX")');
		$submenu = array(
        array('%ADDONS_URL%commerce/commandes_list.php',$bouton,155,'DataEngine::CheckPerms("ZZZ_COMMERCE_INDEX")'), // sous-menu 2
        );
		} else {
		$bouton = '%ADDONS_URL%commerce/images/menu/btn-modules.png';
		$menu = array('%ADDONS_URL%commerce/index.php',$bouton,155,'DataEngine::CheckPerms("ZZZ_COMMERCE_INDEX")');
		$submenu = array(
        array('%ADDONS_URL%commerce/index.php',$bouton,155,'DataEngine::CheckPerms("ZZZ_COMMERCE_INDEX")'), // sous-menu 2
        );
		}
		
		return array(
		  'insertafter' => 'addon', // empty for first.(ceux déjà inclus: carto,perso,admin,forum, et logout)
		  'id' => 'commerce', // doit être unique ! (pas écraser qui que ce soit d'autre)
		  'onlysub' => true, // ajout a la fin du menu existant (champ 'id' ignoré)
		  'menu' => $submenu); // 					"onlysub" => "key", // hmm...
    }

    /**
     * Si actif, le paramètre du menu 'insertafter' doit être 'addon'
     * ainsi que le paramètre 'onlysub' a true
     * @return boolean
     */
    public function InSubAddonMenu () { 
	return true; 
	}
    
    public function OnDeleteUser($user) {
        FB::info($user,'addons::OnDeleteUser');
        return true;
    }
    public function OnNewUser($user) {
        FB::info($user,'addons::OnNewUser');
        return true;
    }
	public function OnButtonRegen(&$listing, $defaultsetting) {
        // $defaultsetting = array(fontfile, fontsize, alphacolor, textcolor);
        $listing['modules'] = array($defaultsetting, 'COMMERCE');
		// Partie à modifier
		$defaultsetting = array('./CGF Locust Resistance.ttf', 10, '1F1F99', 'ffffff');
        $listing['modules_red'] = array($defaultsetting, 'COMMERCE');
        return true;
    }
	public function OnVortexCleaned() {
//        FB::info('addons::OnVortexCleaned');
        return true;
    }
}
