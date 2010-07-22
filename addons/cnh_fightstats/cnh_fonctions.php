<?php
/************************************************************
                         Constantes
************************************************************/

$version_addon = "0.5 - 06/01/2010";

// Paramètres de la fonction Log_Activity()
define("DATE_SQL_FORMAT", "Y-m-d H:i:s"); 
define("LOG_LOGON", 0); 

// Liste des actions
define('ACT_DESTROY', 0);
define('ACT_MISS', 1);
define('ACT_HIT', 2);
define('ACT_JOIN', 3);
define('ACT_RETREAT', 4);
define('ACT_DAMAGE', 5);

$listacts = Array(
  ACT_DESTROY => "a été détruit",
  ACT_MISS => "n'a pas touché",
  ACT_HIT => "a touché",
  ACT_JOIN => "a rejoint le combat",
  ACT_RETREAT => "se retire du combat",
  ACT_DAMAGE => "et a causé"
  );

/************************************************************
                     Fonctions fightstats
************************************************************/


// --------------------------------------------------------------------------
// Donne l'état d'un vaisseau
function Is_Ally($nom) {
  $res = null;
  
  for($i = 0; $i < sizeof($_SESSION['CNHFS_ALLIE']); $i++) {
    if($_SESSION['CNHFS_ALLIE'][$i]['Nom'] == $nom) {
      $res = true;
      break;
    }
  }

  if(is_null($res)) {
    for($i = 0; $i < sizeof($_SESSION['CNHFS_ENNEMI']); $i++) {
      if($_SESSION['CNHFS_ENNEMI'][$i]['Nom'] == $nom) {
        $res = false;
        break;
      }
    }
  }
  
  return $res;
}

function Is_Multiple($nom) {
  $res = 0;
  
  for($i = 0; $i < sizeof($_SESSION['CNHFS_ALLIE']); $i++) {
    if($_SESSION['CNHFS_ALLIE'][$i]['Nom'] == $nom)
      $res++;
  }

  for($i = 0; $i < sizeof($_SESSION['CNHFS_ENNEMI']); $i++) {
    if($_SESSION['CNHFS_ENNEMI'][$i]['Nom'] == $nom)
      $res++;
  }
  
  return $res;
}


// --------------------------------------------------------------------------
// Extract log du combat
function ExtractLog($log) {
  global $listacts;

  $i = strpos($log, '-', strpos($log, '-') + 1);
  if($i > 0) $log = trim(substr($log, $i));

  $nboucle = 0;
  $nactions = 0;
  while(strlen($log) > 0 && $nboucle < 100000) {
    $i = strpos($log, 13);
    if($i <= 0) $i = strlen($log);
    $tmp = trim(substr($log, 1, $i));
    $log = trim(substr($log, $i + 1));
    
    if(!empty($tmp))
    {
      $j = null;
      foreach ($listacts as $key => $value) {
        if(strpos($tmp, $value) !== false) {
          $j = $key;
          break;
        }
      }
        
      if(is_null($j))
        pdebug('UNKNOW_ACTION{'.$tmp.'}');
      else {
        $i = strpos($tmp, $listacts[$j]);
      
        switch($j) {
          case ACT_DESTROY: // a été détruit
            $actions[$nactions]['Source'] = trim(substr($tmp, 0, $i - 1));
            $actions[$nactions]['Action'] = ACT_DESTROY;   
          break;
          case ACT_MISS: // a raté son tir
            $i2 = strlen($listacts[ACT_MISS]);
          
            $actions[$nactions]['Source'] = trim(substr($tmp, 0, $i - 1));
            $actions[$nactions]['Target'] = trim(substr($tmp, $i + $i2, strlen($tmp) - $i - $i2 - 1));
            $actions[$nactions]['Action'] = ACT_MISS;   
          break;
          case ACT_JOIN: // troupe a rejoint le combat
            $actions[$nactions]['Source'] = trim(substr($tmp, 10, $i - 10));
            $actions[$nactions]['Action'] = ACT_JOIN;   
          break;
          case ACT_RETREAT: // troupe a fait retraite
            $actions[$nactions]['Source'] = trim(substr($tmp, 0, $i - 1));
            $actions[$nactions]['Action'] = ACT_RETREAT;   
          break;
          case ACT_HIT: // a touché
            $actions[$nactions]['Source'] = trim(substr($tmp, 0, $i - 1));
            $actions[$nactions]['Action'] = ACT_HIT;   
            
            $tmp = trim(substr($tmp, $i + strlen($listacts[ACT_HIT])));
            $i = strpos($tmp, $listacts[ACT_DAMAGE]);
            $actions[$nactions]['Target'] = trim(substr($tmp, 0, $i - 1));

            $tmp = trim(substr($tmp, $i + strlen($listacts[ACT_DAMAGE])));
            $ndam = 0;
            $nboucle2 = 0;
            while(strlen($tmp) > 0 && $nboucle2 < 5) {
              $i = strpos($tmp, ',');
              
              if($i !== false) {
                $dam[$ndam] = trim(substr($tmp, 0, $i));
                $tmp = trim(substr($tmp, $i + 1));
                $ndam++;
              }
              else {
                $dam[$ndam] = $tmp;
                $tmp = '';
                $ndam++;
              }
              
              $nboucle2++;
            }
            
            for($i = 0; $i < $ndam; $i++) {
              $deg = (int)trim(substr($dam[$i], 0, strpos($dam[$i], "dégâts")));
              
              if(strpos($dam[$i], "coque") !== false)
                $actions[$nactions]['Coque'] = $deg;
              elseif(strpos($dam[$i], "bouclier") !== false)  
                $actions[$nactions]['Bouclier'] = $deg;
              else  
                $actions[$nactions]['Ion'] = $deg;
            }
          break;
        }
        
        $nactions++;
      }
    }
    
    $nboucle++;
  }
  return $actions;
}


// --------------------------------------------------------------------------
// Extract liste des vaisseaux
function ExtractVessels($sit, &$vallie, &$vennemi) {
  $i = strpos($sit, 'Bouclier');
  if($i > 0) $sit =  trim(substr($sit, $i + 8));
  
/*  if($_SESSION["_login"] == 'yelm') {
    echo("<font color=white>".substr($sit, 0, 100)."<br />");
    for($i=0;$i<100;$i++)
      echo("[".substr($sit, $i, 1)."=".ord(substr($sit, $i, 1))."]");
    echo("</font><br/>");
  }*/
  
  $totalv = 0;
  $nboucle = 0;
  $totala = 0;
  $totale = 0; 
  $allie = true;
  while(strlen($sit) > 0 && $nboucle < 10000) {
    // Passage aux vaisseaux ennemis
    if($allie && substr($sit, 0, 17) == 'Vaisseaux ennemis') {
      $allie = false;
      $i = strpos($sit, 'Bouclier');
      if($i > 0) $sit =  trim(substr($sit, $i + 8));
    }
    
    // Nom du vaisseau (v2)
    if(strpos($sit, 9) !== false) {
      $i = strpos($sit, 9);
      if($i === false) { $nboucle++; continue; }

      $tname = trim(substr($sit, 0, $i));
      $sit = trim(substr($sit, $i + 1));
    }
    else { // IE
      $i = strpos($sit, '/');
      if($i === false) { $nboucle++; continue; }
      $i = strrpos(trim(substr($sit, 0, $i)), 32);

      $tname = trim(substr($sit, 0, $i - 1));
      $sit = trim(substr($sit, $i));
    }
    
    // Coque
    if(strpos($sit, 9) !== false) {
      $i = strpos($sit, 9);
      if($i === false) { $nboucle++; continue; }

      $tmp = trim(substr($sit, 0, $i));
      $sit = trim(substr($sit, $i));
      
      $i = strpos($tmp, '/');
      if($i > 0) {
        $coque1 = trim(substr($tmp, 0, $i - 1)); 
        $coque2 = trim(substr($tmp, $i + 1)); 
      }
      else {
        $coque1 = $tmp;
        $coque2 = $tmp;
      }
    }
    else { // IE
      $i = strpos($sit, '/', strpos($sit, '/') + 1);
      if($i === false) { $nboucle++; continue; }
      $i = strrpos(trim(substr($sit, 0, $i)), 32);

      $tmp = trim(substr($sit, 0, $i - 1));
      $sit = trim(substr($sit, $i));
      
      $i = strpos($tmp, '/');
      if($i > 0) {
        $coque1 = trim(substr($tmp, 0, $i)); 
        $coque2 = trim(substr($tmp, $i + 1)); 
      }
      else {
        $coque1 = $tmp;
        $coque2 = $tmp;
      }
    }
        
    // Bouclier
    if(strpos($sit, 9) !== false) {
      $i = strpos($sit, 13, strpos($sit, 13) + 1);
      if($i === false) $i = strlen($sit);
      
      if($i > 0) {
        $tmp = trim(substr($sit, 0, $i));
        $sit = trim(substr($sit, $i));
        
        $i = strpos($tmp, '/');
        if($i > 0) {
          $shield1 = trim(substr($tmp, 0, $i - 1)); 
          $shield2 = trim(substr($tmp, $i + 1)); 
        }
        else {
          $shield1 = $tmp;
          $shield2 = $tmp;
        }
      }
    }
    else { // IE
      $i = strpos($sit, 13, strpos($sit, 13) + 1);
      if($i === false) $i = strlen($sit);
      
      if($i > 0) {
        $tmp = trim(substr($sit, 0, $i));
        $sit = trim(substr($sit, $i));
        
        $i = strpos($tmp, '/');
        if($i > 0) {
          $shield1 = trim(substr($tmp, 0, $i)); 
          $shield2 = trim(substr($tmp, $i + 1)); 
        }
        else {
          $shield1 = $tmp;
          $shield2 = $tmp;
        }
      }
    }

    $totalv++;
    if($allie) {
      $vallie[$totala]['Nom'] = $tname;
      $vallie[$totala]['CoqueStart'] = (int)$coque1;
      $vallie[$totala]['CoqueFull'] = (int)$coque2;
      $vallie[$totala]['Coque'] = (int)$coque1;
      $vallie[$totala]['ShieldStart'] = (int)$shield1;
      $vallie[$totala]['ShieldFull'] = (int)$shield2;
      $vallie[$totala]['Shield'] = (int)$shield1;
      $totala++;
    }
    else {
      $vennemi[$totale]['Nom'] = $tname;
      $vennemi[$totale]['CoqueStart'] = (int)$coque1;
      $vennemi[$totale]['CoqueFull'] = (int)$coque2;
      $vennemi[$totale]['ShieldStart'] = (int)$shield1;
      $vennemi[$totale]['ShieldFull'] = (int)$shield2;
      $totale++;
    }
    
    $nboucle++;    
  }

  array_sort($vallie,'Nom','!CoqueStart');
  array_sort($vennemi,'Nom','!ShieldStart','!CoqueStart');
}


// --------------------------------------------------------------------------
// Extract les stats du combat
function ExtractStats() {
  $actions = &$_SESSION['CNHFS_ACTIONS'];
  
  for($i = 0; $i < sizeof($actions); $i++) {
    $nv = $actions[$i]['Source'];

    if($actions[$i]['Action'] == ACT_JOIN || $actions[$i]['Action'] == ACT_DESTROY) continue;

    // S'il n'existe pas encore, l'ajoute dans la liste
    if(!isset($vessels[$nv]['Nom'])) {
      $vessels[$nv]['TotCoque'] = 0;
      $vessels[$nv]['MaxCoque'] = 0;
      $vessels[$nv]['TotBouclier'] = 0;
      $vessels[$nv]['MaxBouclier'] = 0;
      $vessels[$nv]['TotIon'] = 0;
      $vessels[$nv]['MaxIon'] = 0;
      $vessels[$nv]['TotActions'] = 0;
      $vessels[$nv]['TotMiss'] = 0;
      $vessels[$nv]['TotHit'] = 0;
      $vessels[$nv]['TotHit_C'] = 0;
      $vessels[$nv]['TotHit_B'] = 0;
      $vessels[$nv]['TotHit_I'] = 0;
      $vessels[$nv]['Nom'] = $actions[$i]['Source'];
      $vessels[$nv]['Duplicate'] = Is_Multiple($actions[$i]['Source']);

      if(!isset($actions[$i]['Side']))
        $actions[$i]['Side'] = Is_Ally($actions[$i]['Source']);
      $vessels[$nv]['Side'] = $actions[$i]['Side'];  
    }
    
    switch($actions[$i]['Action']) {
      case ACT_HIT:
        $vessels[$nv]['TotActions']++;
        $vessels[$nv]['TotHit']++;
      
        if(isset($actions[$i]['Coque'])) {
          $vessels[$nv]['TotHit_C']++;
          $vessels[$nv]['TotCoque'] += $actions[$i]['Coque'];
          if($actions[$i]['Coque'] > $vessels[$nv]['MaxCoque'])
            $vessels[$nv]['MaxCoque'] = (int)$actions[$i]['Coque'];
        }
        
        if(isset($actions[$i]['Bouclier'])) {
          $vessels[$nv]['TotHit_B']++;
          $vessels[$nv]['TotBouclier'] += $actions[$i]['Bouclier'];
          if($actions[$i]['Bouclier'] > $vessels[$nv]['MaxBouclier'])
            $vessels[$nv]['MaxBouclier'] = (int)$actions[$i]['Bouclier'];
        }
        
        if(isset($actions[$i]['Ion'])) {
          $vessels[$nv]['TotHit_I']++;
          $vessels[$nv]['TotIon'] += $actions[$i]['Ion'];
          if($actions[$i]['Ion'] > $vessels[$nv]['MaxIon'])
            $vessels[$nv]['MaxIon'] = (int)$actions[$i]['Ion'];
        }
      break;
      case ACT_MISS:
        $vessels[$nv]['TotActions']++;
        $vessels[$nv]['TotMiss']++;
      break;
    }
  }

  unset($nv);
  $nv = 0;
  foreach($vessels as $key => &$value) {
    foreach($value as $key2 => $value2) {
      $listv[$nv][$key2] = $value2;
    }
    if($listv[$nv]['Duplicate'] > 1) {
      $listv[$nv]['TotCoque'] = round($listv[$nv]['TotCoque'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotBouclier'] = round($listv[$nv]['TotBouclier'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotIon'] = round($listv[$nv]['TotIon'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotActions'] = round($listv[$nv]['TotActions'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotMiss'] = round($listv[$nv]['TotMiss'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotHit'] = round($listv[$nv]['TotHit'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotHit_C'] = round($listv[$nv]['TotHit_C'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotHit_B'] = round($listv[$nv]['TotHit_B'] / $listv[$nv]['Duplicate']);
      $listv[$nv]['TotHit_I'] = round($listv[$nv]['TotHit_I'] / $listv[$nv]['Duplicate']);
    }

    if($listv[$nv]['TotActions'] > 0) {
      $listv[$nv]['MoyMiss'] = $listv[$nv]['TotMiss'] / $listv[$nv]['TotActions'] * 100;
      $listv[$nv]['MoyHit'] = $listv[$nv]['TotHit'] / $listv[$nv]['TotActions'] * 100;
    }
    else {
      $listv[$nv]['MoyMiss'] = 0;
      $listv[$nv]['MoyHit'] = 0;
    }

    if($listv[$nv]['TotHit_C'] > 0)
      $listv[$nv]['MoyCoque'] = $listv[$nv]['TotCoque'] / $listv[$nv]['TotHit_C'];
    else
      $listv[$nv]['MoyCoque'] = 0;

    if($listv[$nv]['TotHit_B'] > 0)
      $listv[$nv]['MoyBouclier'] = $listv[$nv]['TotBouclier'] / $listv[$nv]['TotHit_B'];
    else
      $listv[$nv]['MoyBouclier'] = 0;

    if($listv[$nv]['TotHit_I'] > 0)
      $listv[$nv]['MoyIon'] = $listv[$nv]['TotIon'] / $listv[$nv]['TotHit_I'];
    else
      $listv[$nv]['MoyIon'] = 0;

    $nv++;
  }

  return $listv;
}


/************************************************************
                     Fonctions générales
************************************************************/


// --------------------------------------------------------------------------
// Retourne la position du premier caractère alphabétique
function strfirstcar($chaine) {
  $first = strlen($chaine) + 1;

  $charcomp = 'abcdefghijklmnopqrstuvwxyz';            
  if(func_num_args() > 1) {
    if(func_get_arg(1) != '')
      $charcomp = func_get_arg(1);
  }
  
  for($i = 0; $i < strlen($charcomp) && $first != 0; $i++) {
    $j = stripos($chaine, mb_substr($charcomp, $i, 1));
    if($j < $first && $j !== false)
      $first = $j;
  }
  
  if($first >= strlen($chaine) + 1)
    return -1;
  else
    return $first;
}


// --------------------------------------------------------------------------
// Trie un tableau multivaleur
// array_sort(&$array)
function array_sort_func($a,$b=NULL) {
   static $keys;
   if($b===NULL) return $keys=$a;
   foreach($keys as $k) {
      if(@$k[0]=='!') {
         $k=substr($k,1);
         if(@$a[$k]!==@$b[$k]) {
            return strcasecmp(@$b[$k],@$a[$k]);
         }
      }
      else if(@$a[$k]!==@$b[$k]) {
         return strcasecmp(@$a[$k],@$b[$k]);
      }
   }
   return 0;
}
function array_sort(&$array) {
   if(!$array) return $keys;
   $keys=func_get_args();
   array_shift($keys);
   array_sort_func($keys);
   usort($array,"array_sort_func");       
}
/**
 * Sorts an array according to a specified column
 * Params : array  $table
 *          string $colname
 *          bool   $numeric
 **/
function sort_col($table, $colname) {
  if(substr($colname, 0, 1) == "!") {
    $torder = SORT_DESC;
    $colname = substr($colname, 1);
  }
  else
    $torder = SORT_ASC;
    
  $tn = $ts = $temp_num = $temp_str = array();
  foreach ($table as $key => $row) {
    if(is_numeric(substr($row[$colname], 0, 1))) {
      $tn[$key] = $row[$colname];
      $temp_num[$key] = $row;
    }
    else {
      $ts[$key] = $row[$colname];
      $temp_str[$key] = $row;
    }
  }
  unset($table);

  array_multisort($tn, $torder, SORT_NUMERIC, $temp_num);
  array_multisort($ts, $torder, SORT_STRING, $temp_str);
  return array_merge($temp_num, $temp_str);
}


/************************************************************
                    Fonctions d'affichage
************************************************************/


// Affiche la liste des actions
function DisplayActions(&$liste) {
  $vres = null;
  if(func_num_args() > 1) {
    if(func_get_arg(1) != '')
      $vres = func_get_arg(1);
  }

  echo('<table border=1 cellpadding=3 cellspacing=0 align=center>');
  echo('<tr><th>Source</th><th>Cible</th><th>Résultat</th></tr>');

  $j = 0;
  for($i = 0; $i < sizeof($liste); $i++) {
    if(!is_null($vres) && $liste[$i]['Source'] != $vres && $liste[$i]['Target'] != $vres && $liste[$i]['Action'] != ACT_JOIN) continue;

    if($j % 4)
      echo('<tr bgcolor="#000000">');
    else
      echo('<tr bgcolor="#222222">');

    switch($liste[$i]['Action'])
    {
      case ACT_DESTROY:
        echo('<td colspan=2 align=center>'.DisplayVessel($liste[$i]['Source']).'</td>');
        echo('<td><font color=yellow>a été détruit</font></td>');
      break;
      case ACT_JOIN:
        if(!isset($liste[$i]['Side']))
          $liste[$i]['Side'] = Is_Ally($liste[$i]['Source']);
          
        echo('<td colspan=2 align=center>Flotte <font color='.GetForceColor($liste[$i]['Side']).'>'.$liste[$i]['Source'].'</td>');
        echo('<td><font color=yellow>a rejoint le combat</font></td>');
      break;
      case ACT_RETREAT:
        echo('<td colspan=2 align=center>'.DisplayVessel($liste[$i]['Source']).'</td>');
        echo('<td><font color=yellow>a fuit le combat</font></td>');
      break;
      case ACT_MISS:
        echo('<td>'.DisplayVessel($liste[$i]['Source']).'</td>');
        echo('<td>'.DisplayVessel($liste[$i]['Target']).'</td>');
        echo('<td><font color=yellow>a raté sa cible</font></td>');
      break;
      case ACT_HIT:
        echo('<td>'.DisplayVessel($liste[$i]['Source']).'</td>');
        echo('<td>'.DisplayVessel($liste[$i]['Target']).'</td>');
        echo('<td align=right>');
        
        $sep = '<br>';
        $isok = false;
        if(isset($liste[$i]['Coque'])) {
          echo(number_format($liste[$i]['Coque'],0,'.',"'").' sur coque');
          $isok = true;
        }
        
        if(isset($liste[$i]['Bouclier'])) {
          if($isok)
            echo($sep);
          else
            $isok = true;

          echo(number_format($liste[$i]['Bouclier'],0,'.',"'").' sur bouclier');
        }
        
        if(isset($liste[$i]['Ion'])) {
          if($isok) echo($sep);

          echo(number_format($liste[$i]['Ion'],0,'.',"'").' sur propulsion');
        }
        echo('</td>');
      break;
    }
    
    echo('</tr>');
    $j++;
  }  
  
  echo('</table>');
}

// Donne la couleur des vaisseaux
function GetForceColor($var) {
    if(is_null($var))
      return '#FFCC33';
    elseif($var)
      return '#00CC00';
    else
      return '#FF0000';
}

// Affiche la liste des vaisseaux
function DisplayVessels(&$liste) {
  echo('<table border=1 cellpadding=3 cellspacing=0 align=center>');
  echo('<tr><th>No</th><th>Nom</th><th>Coque</th><th>Bouclier</th></tr>'."\n");
  for($i = 0; $i < sizeof($liste); $i++) {
    if($i % 4)
      echo('<tr bgcolor="#000000">');
    else
      echo('<tr bgcolor="#333333">');
    
    echo('<td align=right>'.($i + 1).'</td><td>'.DisplayVessel($liste[$i]['Nom']).'</td>');

    echo('<td align=center>');
    if($liste[$i]['CoqueStart'] == $liste[$i]['CoqueFull'])
      echo(number_format($liste[$i]['CoqueStart'],0,'',"'"));
    else
      echo(number_format($liste[$i]['CoqueStart'],0,'',"'").' / '.number_format($liste[$i]['CoqueFull'],0,'',"'"));
    echo('</td>');

    echo('<td align=center>');
    if($liste[$i]['ShieldStart'] == 0 && $liste[$i]['ShieldFull'] == 0)
      echo('-');
    elseif($liste[$i]['ShieldStart'] == $liste[$i]['ShieldFull'])
      echo(number_format($liste[$i]['ShieldStart'],0,'',"'"));
    else
      echo(number_format($liste[$i]['ShieldStart'],0,'',"'").' / '.number_format($liste[$i]['ShieldFull'],0,'.',"'"));
    echo('</td>');

    echo("</tr>\n");
  }      
  echo('</table>');
}


// --------------------------------------------------------------------------
// Affiche un vaisseau avec son lien complet
function DisplayRecord($recordname)
{
  $affp = false;
  $texte = ' par';
  if(func_num_args() > 2) {
    $affp = func_get_arg(1); 
    $texte = func_get_arg(2); 
  }
  elseif(func_num_args() > 1)
    $affp = func_get_arg(1); 

  $records = sort_col(&$_SESSION['CNHFS_RECORDS'], '!'.$recordname);

  if(isset($_GET['nrang'])) {
    if($_GET['nrang'] == -1)
      $maxrang = 999999;
    else
      $maxrang = (int)$_GET['nrang'];
  }
  else     
    $maxrang = 5;
  
  $rang = 0;
  $nrang = 0;
  $oldi = null;
  for($i = 0; $i < sizeof($records) && $rang < 10; $i++) {
    if($records[$i][$recordname] > 0) {
      if($records[$i][$recordname] != $oldi) {
        if($rang == 0)
          echo('<table border=0 cellspacing=0 cellpading=0>');
        elseif($nrang > $maxrang)
          echo('<br />Et '.($nrang - $maxrang).' autre'.(($nrang - $maxrang) > 1 ? "s" : "").'...</td></tr>');
        else
          echo('</td></tr>');

        $rang++;
        $nrang = 0;
        $oldi = $records[$i][$recordname]; 
        
        echo('<tr align=right valign=top><td>'.$rang.')&nbsp;</td><td><font color=yellow>');
        if($affp)
          echo(number_format($records[$i][$recordname],2,".","'")."</font>%");
        else
          echo(number_format($records[$i][$recordname],0,".","'")."</font>");
        echo($texte."&nbsp;</td><td align=left>");
      }

      if($nrang < $maxrang) {
        if($nrang > 0) echo('<br />');
        echo(DisplayVessel($records[$i]['Nom']));
      }
      
      $nrang++;
    }
  }
  
  if($rang > 0) echo('</td></tr></table>');
}

// --------------------------------------------------------------------------
// Affiche un vaisseau avec son lien complet
function DisplayVessel($name) {
  $records = &$_SESSION['CNHFS_RECORDS']; 

  if(!isset($records[$name]['Side']))
    $records[$name]['Side'] = Is_Ally($name);

  return('<a href="?id='.urlencode($name).'&menu=vessel"><font color='.GetForceColor($records[$name]['Side']).'>'.$name.'</font></a>');
}


// --------------------------------------------------------------------------
// Affiche une liste tirée d'un tableau...
function DisplayListSelect($selectname, $items, $selected) {
  if(!empty($selectname))
    echo("<select name='".$selectname."' id='".$selectname."'>");
  
  for($i = 0; $i < sizeof($items); $i++)
    echo("<option value='".$i."'".($selected == $i ? " selected" : "").">".$items[$i]."</option>");

  if(!empty($selectname))
    echo("</select>");
}


// --------------------------------------------------------------------------
// Affiche un texte pour debug uniquement pour yelm
function pDebug($textedebug) {
  if($_SESSION['_login']=="yelm")
    echo("<br /><center><font color='#ffffff'>[<tt>".$textedebug."</tt>]</font></center>");
}


/************************************************************
                      Fonctions mysql
************************************************************/

// --------------------------------------------------------------------------
// Crée une entrée dans une table en sauvant la totalité d'un array
function mysql_insert_array($my_table, $my_array) {
/*	foreach ($my_array as $field=>$value) {
		$fields[] = sprintf("`%s` = '%s'", $field, mysql_real_escape_string($value));
	}
	$field_list = join(',', $fields);
	
	$sql = sprintf("INSERT INTO `%s` SET %s", $my_table, $field_list); */

    $keys = array_keys($my_array);
    $values = array_values($my_array);
    $sql = 'INSERT INTO ' . $my_table . '(' . implode(',', $keys) . ') VALUES ("' . implode('","', $values) . '")';
    
//    pDebug($sql);
    
    if(mysql_query($sql)) {
      return(mysql_insert_id());
    }
    else {
      return(0);
    }
} 

// --------------------------------------------------------------------------
// Met à jour une entrée dans une table avec la totalité d'un array
function mysql_update_array($my_table, $my_where, $my_array) {
	foreach ($my_array as $field=>$value) {
    if(is_numeric($value))
		  $fields[] = sprintf("`%s` = %s", $field, $value);
		else
		  $fields[] = sprintf("`%s` = '%s'", $field, mysql_real_escape_string($value));
//    $fields[] = sprintf("`%s` = '%s'", $field, str_replace("'", "\'", $value));
//    $fields[] = sprintf("`%s` = '%s'", $field, $value);
	}
	$field_list = join(',', $fields);
	
	$query = sprintf("UPDATE `%s` SET %s WHERE %s", $my_table, $field_list, $my_where);
	
	return mysql_query($query);
}
?>
