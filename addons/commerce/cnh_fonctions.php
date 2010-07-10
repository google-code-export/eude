<?php
/************************************************************
                         Constantes
************************************************************/
require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');

$version_addon = "0.19 - 17/01/2010";

$cnhCategorieName = Array("Propulsion", "Armement", "Protection", "Equipement");
$cnhArmeName = Array("Laser", "Projectile", "Ion");
$cnhEquipementName = Array("Cargo", "Troupes", "Minage", "Scan", "Récupérateur", "Réservoir", "Colonisation","Camouflage");
$cnhProtectionName = Array("Renfort", "Bouclier");
$cnhPropulsionName = Array("Impulsion", "Warp");
$cnhMineraisName = Array("Titane", "Cuivre", "Fer", "Aluminium", "Mercure", "Silicium", "Uranium", "Krypton", "Azote", "Hydrogene");
$cnhNames = Array($cnhPropulsionName, $cnhArmeName, $cnhProtectionName, $cnhEquipementName);

$cnhMenus = Array(
            Array("Accueil", "index.php", "home", 48, "Retour à la page d'accueil.", ZZZ_COMMERCE_INDEX),
            Array("Modules", "template_list.php?commande=false", "listmodules", 48, "Liste de tous les modules disponibles dans la Galaxie ainsi que leur disponibilité au sein de la Confédération.", ZZZ_COMMERCE_MODULES),
//            Array("Rercherches", "", "recherches", 48, "Permet d'avoir accès à l'ensemble de l'arbre de recherche, de voir où vous en êtes et quels sont les modules qui vous manquent pour débloquer les suivants."),
            Array("Commerce", "template_list.php?commande=full", "commandes", 48, "Effectuer vos achats.", ZZZ_COMMERCE_MODULES),
            Array("Gestion", "commandes_list.php", "commandes", 48, "Gérer vos achats et vos ventes.", ZZZ_COMMERCE_GESTION),
            Array("Stats", "addon_stats.php", "top", 48, "Statistiques et chiffres en tout genre.", ZZZ_COMMERCE_STATS),
//            Array("Stats", "addon_stats.php", "top", 48, "Statistiques et chiffres en tout genre."),
            Array("Préférences", "user_config.php", "user", 48, "Régler vos paramètres personnels.", ZZZ_COMMERCE_PREF),
            Array("Importation", "user_importmod.php", "import", 48, "Vous permet d'importer toutes les données du jeu: templates, modules, liste de chassis ...", ZZZ_COMMERCE_IMPORT),
            );
            
$cnhSousMenus = Array(
                  "Stats" => Array(
                    Array("Globales", "addon_stats.php", "top", 48, "Statistiques et chiffres en tout genre.", ZZZ_COMMERCE_STATS),
                    Array("Personelles", "user_stats.php", "top", 48, "Statistiques personelles.", ZZZ_COMMERCE_STATS)
                  ),
                  "Logon" => Array(
                    Array("Connexion", "index.php?action=logon", "logon", 48, "Se connecter et avoir accès à toutes les fonctions de l'Addon.", ZZZ_COMMERCE_INDEX),
                  )
                );

$cnhListSort = Array("Par nom", "Par catégorie", "Par sous-catégorie", "Par niveau technologique");

$cnhCommerceType = Array("Tous", "Interne à l'Empire", "Pour moi uniquement");

// Paramètres de la fonction Log_Activity()
define("DATE_SQL_FORMAT", "Y-m-d H:i:s"); 
define("LOG_LOGON", 0); 

// Paramètres des préférences utilisateurs
define("UP_CREDITS", 1);
define("UP_EXACT", 2);
define("UP_CHOIX", 4);
define("UP_RESS", 8184);
define("UP_ALL", 8191);

/************************************************************
                     Fonctions générales
************************************************************/

// --------------------------------------------------------------------------
// Enlève un point d'un chiffre
function DelPoint($numb) {
  $i = strpos($numb, '.');

  if($i === false)
    return $numb;
  
  return mb_substr($numb, 0, $i).mb_substr($numb, $i + 1);  
}

// --------------------------------------------------------------------------
// Initialisation de l'addon
function Init_Addon() {
  global $version_addon;

  $CheckSession = true;

  if(!isset($_SESSION['CheckSession']))
    $_SESSION['CheckSession'] = $version_addon;
  elseif($_SESSION['CheckSession'] != $version_addon || $_SESSION['_login'] != $_SESSION['up_Login'])
    $_SESSION['CheckSession'] = $version_addon;
  else
    $CheckSession = false;
  
  // Initialisation de l'Addon

    // User preference
    Load_Prefs();
}

// --------------------------------------------------------------------------
// Chargement des préférences de l'utilisateur
function Load_Prefs() {
  global $user_prefs;

  $_SESSION['up_Login'] = $_SESSION['_login'];
  $_SESSION['up_CommerceType'] = 0;
  $_SESSION['up_ChassisSecret'] = 1;
  $_SESSION['up_Modifier'] = 0;
  $_SESSION['up_ListSort'] = 0;
  $_SESSION['up_ActivatedSort'] = false;
  $_SESSION['up_DateCreated'] = date(DATE_SQL_FORMAT);
  $_SESSION['up_DateLast'] = $_SESSION['up_DateCreated'];
  $_SESSION['up_Paiement'] = 0x000000000000000;
  $_SESSION['up_Planetes'] = 0x00000;

  if(!empty($_SESSION['_login'])) {
    $mysql_result = DataEngine::sql('SELECT `DateCreated`, `DateLast`, `Modifier`, `ListSort`, `ActivatedSort`, `CommerceType`, `ChassisSecret`, `Paiement`, `Planetes` FROM `SQL_PREFIX_Modules_Users_Config` WHERE `Login`=\''.$_SESSION['_login'].'\'') or die(sql_error());
  
    if($datas = mysql_fetch_array($mysql_result)) {
      $_SESSION['up_CommerceType'] = $datas['CommerceType'];
      $_SESSION['up_ChassisSecret'] = $datas['ChassisSecret'];
      $_SESSION['up_Modifier'] = $datas['Modifier'];
      $_SESSION['up_ListSort'] = $datas['ListSort'];
      $_SESSION['up_ActivatedSort'] = $datas['ActivatedSort'];
      $_SESSION['up_DateCreated'] = $datas['DateCreated'];
      $_SESSION['up_DateLast'] = $datas['DateLast'];
      $_SESSION['up_Paiement'] = $datas['Paiement'];
      $_SESSION['up_Planetes'] = $datas['Planetes'];
    }
    else
    {
      // S'il n'existe pas, création de l'utilisateur
      $datas['Login'] = $_SESSION['_login'];
      $datas['DateCreated'] = $_SESSION['up_DateCreated'];
      $datas['DateLast'] = $_SESSION['up_DateLast'];
      mysql_insert_array('`SQL_PREFIX_Modules_Users_Config`', $datas);
    }
  }
}

// --------------------------------------------------------------------------
// Mise à jour du modifier
function Update_Modifier($login, $modifier) {
  $mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_Modules_Users` SET `Modifier` = \''.$modifier.'\' WHERE `Login`=\''.$login.'\'') or die(sql_error());
  
  return true;
}

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


/************************************************************
                    Fonctions d'affichage
************************************************************/

// --------------------------------------------------------------------------
// Affiche une liste tirée d'un tableau...
function DisplayListSelect($selectname, $items, $selected) {
  if(!empty($selectname))
    echo('<select name='.$selectname.' id='.$selectname.'>');
  
  for($i = 0; $i < sizeof($items); $i++)
    echo('<option value='.$i.($selected == $i ? ' selected' : '').'>'.$items[$i].'</option>');

  if(!empty($selectname))
    echo('</select>');
}

// --------------------------------------------------------------------------
// Affiche la new toolbar ou une page de menu...
// 1) Nombre de lignes (avec 0 = Mini-Toolbar)
// 2) remapping icone (prefixe nom)
// 3) remapping icone (size)
// 4) Sous-menu (intitulé)
function cnhTB() {
  global $cnhMenus;
  global $cnhSousMenus;

  $menus      = null;
  $rplSize    = null;
  $prefixnom  = '';
  $nbrow      = 1;
  $minibar    = false;

  switch(func_num_args()) {
    case 4:
      if(func_get_arg(0) == 0)
        $menus = $cnhSousMenus[func_get_arg(3)];
      else {
        $menu0 = Array($cnhMenus[0]); 
        $menu1 = $cnhSousMenus[func_get_arg(3)]; 
        $menus =  array_merge($menu0, $menu1);
      } 
    case 3:
      if(!is_null(func_get_arg(2)))
        $rplSize = func_get_arg(2);
    case 2:
      if(!is_null(func_get_arg(1)))
        $prefixnom = func_get_arg(1);
    case 1:
      if(func_get_arg(0) == 0) {
        $nbrow = 1;
        $minibar = true;
      }
      else 
        $nbrow = func_get_arg(0);
  }

  if(empty($menus))
    $menus = $cnhMenus;

  $nreel = 0;
  for($i = 0; $i < sizeof($menus); $i++) {
    if(Members::CheckPerms($menus[$i][5]))
      $nreel++;
  }

  $def_table = '<table border=0 align=center cellpadding='.(!$minibar ? '15' : '3').' cellspacing=0>';
  $def_tr = '<tr valign=top>';

  echo("\n".$def_table.$def_tr);

  $nbcol = ceil($nreel / $nbrow);
  $wi = number_format(100 / $nbcol, 1);

  $j = 0;
  for($i = 0; $i < sizeof($menus); $i++) {
    if(Members::CheckPerms($menus[$i][5]))
    {
      if($j % $nbcol == 0 && $j > 0) {
        if($nreel - $j < $nbcol && number_format(100 / ($nreel - $j), 1) != $wi) {
          $wi = number_format(100 / ($nreel - $j), 1);
          
          echo('</tr></table>'.$def_table.$def_tr);
        }
        else
          echo('</tr>'.$def_tr);
      } 
    
      echo('<td align=center width='.$wi.'%>');
      
      if(!empty($menus[$i][4]))
        echo('<div title=\''.$menus[$i][4].'\'>');
      
      if($minibar)
        echo("<font size='-2'>");
      
      if(!empty($menus[$i][1]))
        echo('<a href='.$menus[$i][1].'>');
  
      if(!empty($menus[$i][2])) {
        $larg = null;
        if(!empty($rplSize))
          $larg = $rplSize;
        elseif(!empty($menus[$i][3]))
          $larg = $menus[$i][3];
  
        echo('<img '.(!empty($larg) ? ' width='.$larg.' height='.$larg.' ' : '').'src="images/menu/'.(!empty($prefixnom) ? $prefixnom : '').$menus[$i][2].'.png" border=0 align=absmiddle>');
      }
      
      if(!$minibar && !empty($menus[$i][4]))
        $t = (!empty($menus[$i][0]) ? '<b>'.$menus[$i][0].'</b><br>' : '').$menus[$i][4];
      elseif(!$minibar && empty($menus[$i][4]))
        $t = '<b>'.$menus[$i][0].'</b><br>';
      elseif(!empty($menus[$i][0]))
        $t = $menus[$i][0];
      else
        $t = $menus[$i][4];
      
      echo ('<br />'.$t.(!empty($menus[$i][1]) ? '</a>' : '').($minibar ? '</font>' : '').(!empty($menus[$i][4]) ? '</div>' : '').'</td>');

      $j++; 
    }
  }

  echo("</tr></table>\n");
}

// --------------------------------------------------------------------------
// Affiche un texte pour debug uniquement pour yelm
function pDebug($textedebug) {
  if($_SESSION['_login']=='docl88')
    echo('<br /><center><font color=#ffffff>[<tt>'.$textedebug.'</tt>]</font></center>');
}

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
    
    if(DataEngine::sql($sql)) {
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
	
	return DataEngine::sql($query);
}
?>
