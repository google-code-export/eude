<?php
// Partie standard d'EU2de
  require_once("../../init.php");
  require_once(INCLUDE_PATH."Script.php");
  require_once(TEMPLATE_PATH."sample.tpl.php");
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  $Joueur = $_SESSION["_login"];
  require_once("cnh_fonctions.php");
  Init_Addon();
  
// DEBUT CODE LIBRE
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_PREF'))
    output::Boink(ROOT_URL.'index.php');

/*
CREATE TABLE IF NOT EXISTS `cnhUsers_Config` (
  `Login` varchar(30) NOT NULL,
  `Modifier` smallint(5) default '0',
  `ListSort` tinyint(3) default '0',
  `ActivatedSort` tinyint(1) default '0',
  `CommerceType` tinyint(3) default '0',
  `ChassisSecret` tinyint(3) default '0',
  PRIMARY KEY  (`Login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/  

  if(isset($_GET["delid"]) && $_SESSION["_Perm"] > AXX_ADMIN) {
    $datas_id=stripslashes($_GET["delid"]);
    
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Users_Config WHERE Login='".$datas_id."'") or die(mysql_error());
    $mysql_result = DataEngine::sql("DELETE FROM SQL_PREFIX_Modules_Users WHERE Login='".$datas_id."'") or die(mysql_error());
    header("Location: index.php");
    exit;
  }
  elseif(!isset($_POST["ID"])) {
    $mysql_result = DataEngine::sql("SELECT * FROM SQL_PREFIX_Users_Config WHERE Login='".$Joueur."'") or die(mysql_error());
    if(!($datas=mysql_fetch_array($mysql_result))) {
      $datas["Login"] = $Joueur;
      $datas["Modifier"]=0;
      $datas["ListSort"]=0;
      $datas["ActivatedSort"]=false;
      $datas["CommerceType"]=0;
      $datas["ChassisSecret"]=1;
      $datas["Paiement"]=1;
      $datas["Planetes"]=0;
      $datas_id = "";  
    }
    else {
      $datas["Login"] = stripslashes($datas["Login"]);
      $datas_id = $datas["Login"];  
      
      if(empty($datas["Paiement"]))
        $datas["Paiement"] = 1;
      if(empty($datas["Planetes"]))
        $datas["Planetes"] = 0;
    }
  }
  elseif(isset($_POST["ID"])) {
    $datas_id = $_POST["ID"];  
    
    if(isset($_POST["Login"]) && $_SESSION["_Perm"] > AXX_ADMIN)
      $datas["Login"]=stripslashes($_POST["Login"]);
    else
      $datas["Login"]=$Joueur;

    if($_POST["ActivatedSort"] == "on")
      $datas["ActivatedSort"]=true;
    else
      $datas["ActivatedSort"]=false;
    
    $datas["Modifier"]=$_POST["Modifier"];
    $datas["ListSort"] = $_POST["ListSort"];
    $datas["CommerceType"]=$_POST["CommerceType"];
    $datas["ChassisSecret"]=$_POST["ChassisSecret"];
    
    $datas["Paiement"] = 0;
    for($i = 0; $i < 15; $i++) {
      if($_POST["Paiement_".$i] == "on")
        $datas["Paiement"] += pow(2, $i);
    }

    $datas["Planetes"] = 0;
    for($i = 0; $i < 5; $i++) {
      if($_POST["Planete_".$i] == "on")
        $datas["Planetes"] += pow(2, $i);
    }
    
  }
  else {
    header("Location: index.php");
    exit;
  }

  if(isset($_POST["submit"])){
    if(($_POST["submit"] == "Sauver" || $_POST["submit"] == "Sauver et retour") && !empty($datas_id)) {
      mysql_update_array("SQL_PREFIX_Users_Config", "Login='".$datas["Login"]."'", $datas);
      Load_Prefs();
      Update_Modifier($datas["Login"], $datas["Modifier"]);
      
      if($_POST["submit"] == "Sauver et retour") {
        header("Location: index.php");
        exit;
      }
    }
    elseif(($_POST["submit"] == "Sauver" || $_POST["submit"] == "Sauver et retour")) {
      mysql_insert_array("SQL_PREFIX_Users_Config", $datas);
      $datas["DateCreated"] = date(DATE_SQL_FORMAT);
      $datas["DateLast"] = $datas["DateCreated"];
      $datas_id = $datas["Login"]; 
      Load_Prefs();
      Update_Modifier($datas["Login"], $datas["Modifier"]);
      
      if($_POST["submit"] == "Sauver et retour") {
        header("Location: index.php");
        exit;
      }
    }
  }

// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <link href="cnh_addon.css" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><? cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->

<form name="form1" method="post" action=""><input type="hidden" name="ID" id="ID" value='<?=$datas_id?>'><input type="hidden" name="Login" id="Login" value='<?=$datas["Login"]?>'>
<table border="1" align="center" cellpadding="3" cellspacing="0">
  <tr>
    <th>Information</th>
    <th>Contenu</th>
  </tr>
  <tr>
    <td>Modificateur du prix des modules et chassis</td>
    <td>+/- <input type="text" name="Modifier" id="Modifier" size=20 maxlength=15 value="<?=$datas["Modifier"]?>"> %</td>
  </tr>
  <tr>
    <td>Tri par défaut de la liste des modules</td>
    <td><? DisplayListSelect("ListSort", $cnhListSort, $datas["ListSort"]); ?></td>
  </tr>
  <tr>
    <td>Modification du tri</td>
    <td><input type="checkbox" name="ActivatedSort" id="ActivatedSort" <?=($datas[ActivatedSort] ? "checked " : "" )?>/> Mettre en haut de liste les modules que vous pouvez construire.</td>
  </tr>
  <tr>
    <td>A qui voulez-vous vendre vos modules?</td>
    <td><? DisplayListSelect("CommerceType", $cnhCommerceType, $datas["CommerceType"]); ?></td>
  </tr>
  <tr>
    <td>Qui peut voir vos chassis?</td>
    <td><? DisplayListSelect("ChassisSecret", $cnhCommerceType, $datas["ChassisSecret"]); ?></td>
  </tr>
  <tr>
    <td>Quels paiements acceptez-vous?</td>
    <td><table border="0" cellspacing="0" cellpadding="0">
    <tr><td><input type="checkbox" name="Paiement_0" <?=($datas["Paiement"] & UP_CREDITS ? "checked " : "" )?>/></td><td>Vous acceptez les crédits comme paiement.</td></tr>
    <tr><td><input type="checkbox" name="Paiement_1" <?=($datas["Paiement"] & UP_EXACT ? "checked " : "" )?>/></td><td>Vous acceptez un paiement en ressources identiques aux ressources utilisées.</td></tr>
    <tr valign=top><td><input type="checkbox" name="Paiement_2" <?=($datas["Paiement"] & UP_CHOIX ? "checked " : "" )?>/></td><td>Vous acceptez un paiement dans une sélection de ressources ci-dessous:
<?
  for($i = 0; $i < sizeof($cnhMineraisName); $i++)
    echo("<br /><input type='checkbox' name='Paiement_".($i+3)."' ".($datas["Paiement"] & pow(2, $i + 3) ? "checked " : "" )."/> <img src='".IMAGES_URL.$cnhMineraisName[$i].".png' /> ".$cnhMineraisName[$i]);
?>
    </td></tr>
    </table></td>
  </tr>
  <tr>
    <td>Sur quelle(s) planète(s) vous appartenant<br /> acceptez-vous les paiements en ressources?</td>
    <td>
<?
    $mysql_result = DataEngine::sql("SELECT * FROM SQL_PREFIX_ownuniverse WHERE `UTILISATEUR`='".$Joueur."'") or die(mysql_error());

    $i = 0;
    if(($ownplanetes = mysql_fetch_array($mysql_result))) {
      for($i = 0; $ownplanetes["planet".$i] != "" && $i < 5; $i++) {
        if($i > 0)
          echo("<br />");
        
        echo("<input type='checkbox' name='Planete_".$i."' ".($datas["Planetes"] & pow(2, $i) ? "checked " : "" )."/> ".$ownplanetes["planet".$i]." aux coordonnées ".$ownplanetes["coord".$i]);
      }
    }
    
    if($i == 0)
      echo("<font color=red><b>Vous n'avez pas saisi vos informations dans la partie Ma fiche => Production, veuillez le faire à la page <a href=\"".ROOT_URL."ownuniverse.php\" target=\"_blank\">Production</a>.</b></font>");
?>
    </td>
  </tr>
</table>
<p align="center">
<input type="submit" name="submit" id="submit" value="Sauver">
<input type="submit" name="submit" id="submit" value="Sauver et retour">
<input type="reset" name="Reset" id="Reset" value="Réinitialiser">
</p>
</form>

<!-- FIN CODE LIBRE -->
</BODY></HTML>


<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();