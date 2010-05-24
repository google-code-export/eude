<?php
// Partie standard d'EU2de
  require_once("../../init.php");
  require_once(INCLUDE_PATH.'Script.php');
  require_once(TEMPLATE_PATH.'sample.tpl.php');
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  $Joueur = $_SESSION['_login'];
  require_once("cnh_fonctions.php");
  Init_Addon();
  
// DEBUT CODE LIBRE
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_MODULES'))
    output::Boink('./index.php');
  
  if(!isset($_GET["viewid"])) {
    header('Location: template_list.php');
    exit;
  }

  $mysql_result = DataEngine::sql("SELECT * FROM SQL_PREFIX_Modules_Template WHERE ID=".$_GET["viewid"]) or die(mysql_error());
  $datas=mysql_fetch_array($mysql_result);
  
  $datas["Nom"] = stripslashes($datas["Nom"]); 
  $datas["Abreviation"] = stripslashes($datas["Abreviation"]); 
  $datas["Description"] = stripslashes($datas["Description"]); 
  $datas["URLIcone"] = stripslashes($datas["URLIcone"]); 
  $datas["URLMedium"] = stripslashes($datas["URLMedium"]); 
  $datas["URLImage"] = stripslashes($datas["URLImage"]); 
  
  $datas_id=$_GET["viewid"];

// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <link href="cnh_addon.css" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->

<table border="1" align="center" cellpadding="3" cellspacing="0" width=80%>
  <tr>
    <th>Information</th>
    <th>Contenu</th>
  </tr>
<?php
      if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_EDIT')) {
        echo ('<tr><td>Option(s)</td><td>');
        echo("<a href='template_edit.php?editid=".$datas["ID"]."'><img src='images/edit.png' align=absmiddle border=0> Editer</a>");
      }
      if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE'))	{
          echo("<br /><a href='template_edit.php?delid=".$datas["ID"]."' onclick=\"javascript:return confirm('Confirmez-vous réellement la suppression de l\'élément?')\"><img src='images/delete.png' align=absmiddle border=0> Supprimer</a>");
        echo('</td></tr>');
      }
?>    
  <tr>
    <td>Nom du module</td>
    <td><?php echo $datas["Nom"]; ?><?php if(!empty($datas["Abreviation"])) echo(" (<i>".$datas["Abreviation"]."</i>)"); ?></td>
  </tr>
  <tr>
    <td>Description</td>
    <td><?php echo $datas["Description"]; ?></td>
  </tr>
<?php if(!empty($datas["URLIcone"])) { ?>    
  <tr>
    <td>Icône</td>
    <td align=center><img align=absmiddle src='<?php echo $datas["URLIcone"]; ?>'></td>
  </tr>
<?php }
   if(!empty($datas["URLMedium"])) { 
?>    
  <tr>
    <td>Medium Image</td>
    <td align=center><img align=absmiddle src='<?php echo $datas["URLMedium"]; ?>'></td>
  </tr>
<?php }
   if(!empty($datas["URLImage"])) { 
?>    
  <tr>
    <td>Large Image</td>
    <td align=center><img align=absmiddle src='<?php echo $datas["URLImage"]; ?>'></td>
  </tr>
<?php } ?>
  <tr>
    <td>Catégorie</td>
    <td><?php echo $cnhCategorieName[$datas["Categorie"]]; ?></td>
  </tr>
  <tr>
    <td>Taille de transport</td>
    <td><?php echo $datas["Taille"]; ?></td>
  </tr>
  <tr>
    <td>PA</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Chasseur </td>
        <td><?php echo $datas["PAChasseur"]; ?></td>
      </tr>
      <tr>
        <td>GVG</td>
        <td><?php echo $datas["PAGVG"]; ?></td>
      </tr>
    </table>    </td>
  </tr>
  <tr>
    <td>Ressources</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
<?php
  $totalress = 0;

  for($i = 0; $i < sizeof($cnhMineraisName); $i++) {
    echo("<tr><td><img src='".IMAGES_URL.$cnhMineraisName[$i].".png' /> ".$cnhMineraisName[$i]."</td>");
    echo("<td>".$datas[$cnhMineraisName[$i]]."</td></tr>");
    $totalress += $datas[$cnhMineraisName[$i]];
  }
?>
      <tr>
        <td><b>Total</b></td>
        <td><b><?php echo $totalress; ?></b></td>
      </tr>
    </table>
      </td>
  </tr>
<?php if($datas["Categorie"] == 0) { ?>
  <tr>
    <td>Propulsion</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Impulsion</td>
        <td><?php echo $datas["PropImpulsion"]; ?></td>
      </tr>
      <tr>
        <td>Warp</td>
        <td><?php echo $datas["PropWarp"]; ?></td>
      </tr>
      <tr>
        <td>Consommation</td>
        <td><?php echo $datas["PropConsommation"]; ?></td>
      </tr>
      <tr>
        <td>Statistiques</td>
        <td>
<?php
  if($datas["PropImpulsion"]>0) {
    echo("Impulsion/PA (Chasseur/GVG): ".($datas["PropImpulsion"]/$datas["PAChasseur"])." / ".($datas["PropImpulsion"]/$datas["PAGVG"])."<br>");
    echo("Impulsion/Consommation: ".($datas["PropConsommation"]/$datas["PropImpulsion"])."<br>");
  }
  if($datas["PropWarp"]>0) {
    echo("Warp/PA (Chasseur/GVG): ".($datas["PropWarp"]/$datas["PAChasseur"])." / ".($datas["PropWarp"]/$datas["PAGVG"])."<br>");
    echo("Warp/Consommation: ".($datas["PropConsommation"]/$datas["PropWarp"])."<br>");
  }
?>
        </td>
      </tr>
    </table>
	</td>
  </tr>
<?php } 
elseif($datas["Categorie"] == 1) { ?>
  <tr>
    <td>Armement</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Type</td>
        <td><?php echo $cnhArmeName[$datas["ArmType"]]; ?></td>
      </tr>
      <tr>
        <td>Dégâts</td>
        <td><?php echo $datas["ArmDegat"]; ?></td>
      </tr>
      <tr>
        <td>Précision</td>
        <td><?php echo $datas["ArmManiabilite"]; ?></td>
      </tr>
    </table>
      </td>
  </tr>
<?php } 
elseif($datas["Categorie"] == 2) { ?>
  <tr>
    <td>Protection</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Type</td>
        <td><?php echo $cnhProtectionName[$datas["ProtType"]]; ?></td>
      </tr>
      <tr>
        <td>Protection Chasseur</td>
        <td><?php echo $datas["ProtChasseur"]; ?></td>
      </tr>
      <tr>
        <td>Protection GVG</td>
        <td><?php echo $datas["ProtGVG"]; ?></td>
      </tr>
    </table>
      </td>
  </tr>
<?php } 
elseif($datas["Categorie"] == 3) { ?>
  <tr>
    <td>Equipement</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Type</td>
        <td><?php echo $cnhEquipementName[$datas["EquipType"]]; ?></td>
      </tr>
      <tr>
        <td>Capacité / Niveau</td>
        <td><?php echo $datas["EquipNiv"]; ?></td>
      </tr>
    </table>
      </td>
  </tr>
<?php } 
elseif($datas["Categorie"] == 4) { ?>
  <tr>
    <td>Chassis</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Type</td>
        <td><?php echo $cnhChassisName[$datas["ChassType"]]; ?></td>
      </tr>
      <tr>
        <td>PA<?php echo ($datas["ChassType"] != $datas["ChassCellules"] ? " / Cellules" : ""); ?></td>
        <td><?php echo $datas["ChassPA"]; ?><?php echo ($datas["ChassType"] != $datas["ChassCellules"] ? " / ".$datas["ChassCellules"] : ""); ?></td>
      </tr>
      <tr>
        <td>Structure (Coque)</td>
        <td><?php echo $datas["ChassStructure"]; ?></td>
      </tr>
      <tr>
        <td>Carburant<?php echo ($datas["ChassType"]==0 ? " / Consommation" : ""); ?></td>
        <td><?php echo $datas["ChassCarburant"]; ?><?php echo ($datas["ChassType"]==0 ? " / 0.1" : ""); ?></td>
      </tr>
<?php if($datas["ChassType"]==0) { ?>
      <tr>
        <td>Vitesse Impulsion / Warp</td>
        <td><?php echo $datas["ChassImpulsion"]." / ".$datas["ChassWarp"]; ?></td>
      </tr>
<?php } else { ?>
      <tr>
        <td>Modificateur Impulsion / Warp</td>
        <td><?php echo $datas["ChassImpulsion"]." / ".$datas["ChassWarp"]; ?></td>
      </tr>
      <tr>
        <td>Maniabilité</td>
        <td><?php echo $datas["ChassManiabilite"]; ?></td>
      </tr>
<?php } ?>
    </table>
      </td>
  </tr>
<?php } ?>
  <tr>
    <td>Fourniture</td>
    <td>
<?php
  $mysql_fab = DataEngine::sql("SELECT * FROM SQL_PREFIX_Modules_Users LEFT JOIN SQL_PREFIX_Users_Config ON SQL_PREFIX_Modules_Users.Login=SQL_PREFIX_Users_Config.Login WHERE Module_ID=".$_GET["viewid"]." AND ".($datas["Categorie"]==4 ? "ChassisSecret" : "CommerceType")."<=1 ORDER BY SQL_PREFIX_Modules_Users.Login") or die(mysql_error());
  $i=0; $minmod=0; $maxmod=0;
  
  while($ligne = mysql_fetch_array($mysql_fab))	{
    if($ligne["Modifier"] < $minmod) $minmod = $ligne["Modifier"];
    if($ligne["Modifier"] > $maxmod) $maxmod = $ligne["Modifier"];

    echo("<b>".$ligne["Login"]."</b> (".$ligne["Modifier"]."%)<br>");
      
    $i++;
  }
  
  echo("<hr>");
  echo($i." fabricant".($i>1 ? "s":"")." fourni".($i>1 ? "ssent":"t")." ce module.<br>");
  echo("Prix minimum minoré de ".$minmod."%.<br>");
  echo("Prix maximum majoré de ".$maxmod."%.<br>");
?>
    </td>
  </tr>
</table>
<br />
<center><a href='template_list.php?commande=false'>Retourner à la liste des templates de module...</a></center>

<!-- FIN CODE LIBRE -->
</BODY></HTML>


<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();