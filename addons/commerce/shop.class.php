<?php
/* #############################################################################
   *****************************************************************************

   Objet Module Basket
   -------------------------------------------------------------------------
   Liste de modules commandés

   *****************************************************************************
   ############################################################################# */


class BasketClass {
  // Définition des variables
  public $items;          // Basket d'objets
  public $total = 0;      // Total d'articles différents
  public $nbtotal = 0;    // Total du nombre d'articles en tout
  public $totalitems;     // Total des ressources par ressource
  public $totalress = 0;  // Total des ressources en tout
  public $vendors;        // Liste de vendeurs
  public $selvendors;     // Liste des vendeurs sélectionnés


  /* #########################################################################
     #########################################################################
     
     Fonctions de récupération des données (Par formulaire ou variable)
     
     #########################################################################
     ###################################################################### */

  // Récupère le basket d'un formulaire HTML
  // Argument: IsFullInfo
  // -------------------------------------------------------------------------
  public function BasketRecup() {
    $extendedinfos = false;            
    if(func_num_args() > 0)
      if(func_get_arg(0))
        $extendedinfos = true;

    $this->total = 0;
    foreach($_POST as $key => $value) {
      if(mb_substr($key, 0, 4) == "Mod_" && is_numeric($value)) {
        if($value > 0) {
          $this->items[$this->total]['ID'] = (int)mb_substr($key, 4);
          $this->items[$this->total]['NB'] = (int)$value;
          $this->total++;
        }
      }
    }
    if($this->total <= 0)
      return false;
    
    self::ItemsPopulate($extendedinfos);
    
    if($extendedinfos)
      self::FillVendors();
      
    return true;
  }

  // Récupère le basket de la variable de session
  // Argument: IsFullInfo
  // -------------------------------------------------------------------------
  public function BasketVar() {
    $extendedinfos = false;            
    if(func_num_args() > 0)
      $extendedinfos = func_get_arg(0);

    $i = 0;
    foreach($_SESSION['basket'] as $key => $value) {
      $this->items[$i]['ID'] = $key;
      $this->items[$i]['NB'] = $value;
      
      $i++;
    }

    if($i <= 0)
      return false;

    $this->total = $i;

    self::ItemsPopulate($extendedinfos);
    
    if($extendedinfos)
      self::FillVendors();
      
    return true;
  }
  
  // Récupère le checkout d'un formulaire HTML
  // Argument: IsFullInfo
  // -------------------------------------------------------------------------
  public function CheckoutRecup() {
    $extendedinfos = false;            
    if(func_num_args() > 0)
      if(func_get_arg(0))
        $extendedinfos = true;
    
    $total = 0;
    foreach($_POST as $key => $value) {
      if(mb_substr($key, 0, 10) == "modvendor_") {
        $modid = (int)mb_substr($key, 10);
        $this->selvendors[$modid] = $value;
        $total++;
      }
    }
    
    return ($total > 0);
  }

  // Récupère le checkout d'une variable
  // Argument: Array, IsFullInfo
  // -------------------------------------------------------------------------
  public function CheckoutVar() {
    $extendedinfos = false;            
    if(func_num_args() > 0)
      if(func_get_arg(0))
        $extendedinfos = true;
    
    $total = 0;
    foreach($_SESSION['checkout'] as $key => $value) {
      $this->selvendors[$key] = $value;
      $total++;
    }
    
    return ($total > 0);
  }

  
  /* #########################################################################
     #########################################################################
     
     Fonctions d'exportation ou de concénation de données
     
     #########################################################################
     ###################################################################### */

  // Retourne dans un array le contenu du basket
  // -------------------------------------------------------------------------
  public function GetBasketArray() {
    for($i = 0; $i < sizeof($this->items); $i++)
      $result[$this->items[$i]['ID']] = $this->items[$i]['NB'];
    
    return $result;       
  }

  // Retourne dans un array le contenu du checkout
  // -------------------------------------------------------------------------
  public function GetCheckoutArray() {
    return $this->selvendors;
  }


  /* #########################################################################
     #########################################################################
     
     Fonctions de remplissage ou d'extension des informations
     
     #########################################################################
     ###################################################################### */

  // Etend le contenu des variables items
  // Argument: IsFullInfo
  // -------------------------------------------------------------------------
  public function ItemsPopulate() {
    global $cnhMineraisName;

    $extendedinfos = false;            
    if(func_num_args() > 0)
      if(func_get_arg(0))
        $extendedinfos = true;

    foreach($cnhMineraisName as $value)
      $this->totalitems[$value] = 0;
    $this->totalress = 0;
    $this->nbtotal = 0;
    $this->total = sizeof($this->items);

    for($ni = 0; $ni < sizeof($this->items); $ni++) {
      $this->nbtotal += $this->items[$ni]['NB']; 
        
      if($extendedinfos) {
        $results = self::GetModInfos($this->items[$ni]['ID']);
        $this->items[$ni]['Nom'] = $results['Nom'];

        $this->items[$ni]['RessourcesTotal'] = 0;
        $this->items[$ni]['RessourcesNBTotal'] = 0;
        foreach($cnhMineraisName as $value) {
          $this->items[$ni]['Ressources'][$value] = (int)$results[$value];
          $this->items[$ni]['RessourcesTotal'] += $this->items[$ni]['Ressources'][$value];
          
          $this->items[$ni]['RessourcesNB'][$value] = $this->items[$ni]['Ressources'][$value] * $this->items[$ni]['NB'];
          $this->items[$ni]['RessourcesNBTotal'] += $this->items[$ni]['RessourcesNB'][$value];
          
          $this->totalitems[$value] += $this->items[$ni]['RessourcesNB'][$value];
          $this->totalress += $this->items[$ni]['RessourcesNB'][$value];
        }
      }
      else {
        $this->items[$ni]['Nom'] = null;
        $this->items[$ni]['RessourcesTotal'] = null;
        $this->items[$ni]['RessourcesNBTotal'] = null;
        for($i = 0; $i < sizeof($cnhMineraisName); $i++) {
          $this->items[$ni]['Ressources'][$cnhMineraisName[$i]] = null;
          $this->items[$ni]['RessourcesNB'][$cnhMineraisName[$i]] = null;
        }
      }
    }
  }

  // Recherche les infos d'un module ID
  // Retourne: Nom, Ressources[]
  // -------------------------------------------------------------------------
  public function GetModInfos($modid) {
    global $cnhMineraisName;
  
    $mysql_result = DataEngine::sql('SELECT `Nom`, '.implode(", ", $cnhMineraisName).' FROM `SQL_PREFIX_Modules_Template` WHERE `ID` = \''.$modid.'\'');
    
    if($datas = mysql_fetch_array($mysql_result))
      return $datas;
  }
  
  
  // Recherche les vendeurs pour chaque module dans $items[] et y mets dans $vendors[]
  // -------------------------------------------------------------------------
  public function FillVendors() {
    for($i = 0; $i < sizeof($this->items); $i++) {
      $modid = $this->items[$i]['ID']; 
      
      $mysql_result = DataEngine::sql('
        SELECT u.`Login`, c.`Paiement`, u.`Modifier` FROM `SQL_PREFIX_Modules_Users_Config` c
        LEFT JOIN `SQL_PREFIX_Modules_Users` u ON c.`Login` = u.`Login`
        WHERE `CommerceType` <= 1 AND `Module_ID` = \''.$modid.'\' ORDER BY u.`Login`;
      ') or die(mysql_error());
      
      $this->vendors[$modid][0]['Paiement'] = 0;
      
      $j = 1;
      while($ligne = mysql_fetch_array($mysql_result)) {
        $this->vendors[$modid][$j]['Login'] = $ligne['Login'];
        $this->vendors[$modid][$j]['Paiement'] = $ligne['Paiement'];
        $this->vendors[$modid][$j]['Modifier'] = $ligne['Modifier'];
        
        $j++;
    	}

      $this->vendors[$modid][0]['Total'] = $j - 1;
    }
  }
 
  
  /* #########################################################################
     #########################################################################
     
     Fonctions cosmétiques ou utilitaires
     
     #########################################################################
     ###################################################################### */

  // Retourne une string pour exposer les possibilités de paiement (selon BIT)
  // Arguments: BitPaiementPossibles, [NomGroupeRadio], [Virgule], [Paramêtre]
  // -------------------------------------------------------------------------
  public function StringPaiements($paiementbit) {
    if($paiementbit == 0)
      return('Aucun moyen de paiement activé.');

    global $cnhMineraisName;
                
    $radiobouton = false;
    $groupname = '';
    if(func_num_args() >= 2) {
      $tmp = func_get_arg(1);
      if(!empty($tmp)) {
        $radiobouton = true;
        $groupname = $tmp;
      }
    } 

    $implodechar = ', ';
    if(func_num_args() >= 3) {
      $tmp = func_get_arg(2);
      if(!empty($tmp)) {
        $implodechar = $tmp;
      }
    }

    $oldparam = 0;
    if(func_num_args() >= 4) {
      $tmp = func_get_arg(3);
      if(!empty($tmp)) {
        $oldparam = $tmp;
      }
    }
    
    if($radiobouton)
      $addtxt = '<input name="'.$groupname.'" value="%s" %s type="radio">&nbsp;';
    else
      $addtxt = '';

    $i = 0;
    if($paiementbit & UP_CREDITS)
      $out[$i++] = sprintf($addtxt.'<img src="images/credits.gif" />&nbsp;Crédits', UP_CREDITS, ($oldparam == UP_CREDITS ? "checked" : ''));
    if($paiementbit & UP_EXACT)
      $out[$i++] = sprintf($addtxt.'<img src="images/ressources.png" />&nbsp;Ressources utilisées', UP_EXACT, ($oldparam == UP_EXACT ? "checked" : ''));
    if($paiementbit & UP_CHOIX) {
      $nm = 0;
      for($j = 0; $j < sizeof($cnhMineraisName); $j++)
        if($paiementbit & pow(2, $j + 3)) $nm++;
        
      if($nm == $j)
        $out[$i++] = sprintf($addtxt.'<img src="images/ressources.png" />&nbsp;Toute ressource', UP_RESS, ($oldparam == UP_RESS ? "checked" : ''));
      else {
        for($j = 0; $j < sizeof($cnhMineraisName); $j++) {
          if($paiementbit & pow(2, $j + 3))
            $out[$i++] = sprintf($addtxt.'<img src='.IMAGES_URL.$cnhMineraisName[$j].'.png' >'&nbsp;'.$cnhMineraisName[$j], pow(2, $j + 3), ($oldparam == pow(2, $j + 3) ? 'checked' : ''));
        }
      }
    }

    return(implode($implodechar, $out));
  }

  // Retourne un tableau trié des vendeurs sélectionnés et les articles liés
  // !!!!!!!!!! Revoir cette routine avec des fonctions tableaux PHP !!!!!!!!!!
  // -------------------------------------------------------------------------
  public function VendorSelSort() {
    global $cnhMineraisName;
    $maxi = sizeof($this->items);
    $tabtri = array();    

    foreach($cnhMineraisName as $value)
      $vss['RessourcesNB'][$value] = 0;
    $vss['RessourcesNBTotal'] = 0;

    $nb = 0;
    foreach($this->selvendors as $key => $value) {
      $okfound = false;
      
      for($i = 0; $i < $maxi && !$okfound; $i++) {
        if($this->items[$i]['ID'] == $key) {
          $vss[$nb]['ID'] = $key; 
          $vss[$nb]['Login'] = $value; 
          $vss[$nb]['Index'] = $i; 
          $vss[$nb]['Nom'] = $this->items[$i]['Nom'];
          
          for($j = 1; $j <= $this->vendors[$key][0]['Total']; $j++) {
            if($value == $this->vendors[$key][$j]['Login']) {
              $vss[$nb]['Paiement'] = $this->vendors[$key][$j]['Paiement']; 
              $vss[$nb]['Modifier'] = $this->vendors[$key][$j]['Modifier'];
              
              $j = $this->vendors[$key][0]['Total'] + 1;  
            }
          }

          $modifier = 1 + $vss[$nb]['Modifier'] / 100;
          
          foreach($cnhMineraisName as $value2) {
            $vss[$nb]['RessourcesNB'][$value2] = ($this->items[$i]['RessourcesNB'][$value2] * $modifier);
            $vss['RessourcesNB'][$value2] += $vss[$nb]['RessourcesNB'][$value2];
          }
          $vss[$nb]['RessourcesNBTotal'] = ($this->items[$i]['RessourcesNBTotal'] * $modifier);
          $vss['RessourcesNBTotal'] += $vss[$nb]['RessourcesNBTotal'];
          
          $tabtri[$nb] = $value;

          $nb++;
          $okfound = true;
        }
      }
    }

    if($nb == 0) return;
    $fvss['Total'] = $nb;
  
    asort($tabtri, SORT_LOCALE_STRING);

    $i = 0;
    $nb = 0;
    $oldkey = '';
    $oldi = 0;
    foreach($tabtri as $key => $value) {
      if(empty($oldkey))
        $oldkey = $value;
      elseif($oldkey != $value) {
        $fvss[$oldi]['Total'] = $nb;
        $oldi = $i;
        $nb = 0;
        $oldkey = $value;
      }
      
      if($nb == 0) {
        $fvss[$i]['RessTotal'] = 0; 
        foreach($cnhMineraisName as $value2)
          $fvss[$i]['Ress'][$value2] = 0; 
      }

      $fvss[$i] = $vss[$key]; 

      $fvss[$oldi]['RessTotal'] += $fvss[$i]['RessourcesNBTotal'];
      foreach($cnhMineraisName as $value2)
        $fvss[$oldi]['Ress'][$value2] += $fvss[$i]['RessourcesNB'][$value2];
       
      $i++;
      $nb++;
    }
    $fvss[$oldi]['Total'] = $nb;

    $fvss['RessourcesNB'] = $vss['RessourcesNB'];
    $fvss['RessourcesNBTotal'] = $vss['RessourcesNBTotal'];
    
    return $fvss;
  }

 
} // Fin class BasketClass
