<?php
// Partie standard d'EU2de
  require_once('../../init.php');
  require_once(INCLUDE_PATH.'Script.php');
  require_once(TEMPLATE_PATH.'sample.tpl.php');
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  $Joueur = $_SESSION['_login'];
  require_once('cnh_fonctions.php');
  Init_Addon();
  
// DEBUT CODE LIBRE
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_MODULES'))
    output::Boink('./index.php');
  
  if(!isset($_GET['viewid'])) {
    header('Location: template_list.php');
    exit;
  }

  $mysql_result = DataEngine::sql('SELECT `ID`, `Nom`, `Abreviation`, `Description`, `Categorie`, `Taille`, `PAChasseur`, `PAGVG`, `URLIcone`,
	`URLMedium`, `URLImage`, `Temps`, `Titane`, `Cuivre`, `Fer`, `Aluminium`, `Mercure`, `Silicium`, `Uranium`, `Krypton`, `Azote`, `Hydrogene`,
	`PropImpulsion`, `PropWarp`, `PropConsommation`, `ArmType`, `ArmDegat`, `ArmManiabilite`, `ProtType`, `ProtChasseur`, `ProtGVG`, `EquipType`,
	`EquipNiv` FROM `SQL_PREFIX_Modules_Template` WHERE `ID`='.$_GET['viewid']) or die(mysql_error());
  $datas=mysql_fetch_array($mysql_result);
  
  $datas['Nom'] = stripslashes($datas['Nom']); 
  $datas['Abreviation'] = stripslashes($datas['Abreviation']); 
  $datas['Description'] = stripslashes($datas['Description']); 
  $datas['URLIcone'] = stripslashes($datas['URLIcone']); 
  $datas['URLMedium'] = stripslashes($datas['URLMedium']); 
  $datas['URLImage'] = stripslashes($datas['URLImage']); 
  
  $datas_id=$_GET['viewid'];

// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->
<center><a href='template_list.php?commande=false'>Retourner à la liste des templates de module...</a></center><br />
<table border="1" align="center" cellpadding="3" cellspacing="0" width=80%>
  <tr class="text_center color_header">
    <th>Information</th>
    <th>Contenu</th>
  </tr>
<?php
      if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_EDIT')) {
        echo ('<tr class="color_row0"><td>Option(s)</td><td>');
        echo('<a href="template_edit.php?editid='.$datas['ID'].'"><img src="images/edit.png" align=absmiddle border=0> Editer</a>');
      }
      if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE'))	{
          echo("<br /><a href='template_edit.php?delid=".$datas['ID']."' onclick=\"javascript:return confirm('Confirmez-vous réellement la suppression de l\'élément?')\"><img src='images/delete.png' align=absmiddle border=0> Supprimer</a>");
        echo('</td></tr>');
      }
?>    
  <tr class="color_row0">
    <td>Nom du module</td>
    <td><?php echo $datas['Nom']; ?><?php if(!empty($datas['Abreviation'])) echo(' (<i>'.$datas['Abreviation'].'</i>)'); ?></td>
  </tr>
  <tr class="color_row0">
    <td>Description</td>
    <td><?php echo $datas['Description']; ?></td>
  </tr>
<?php if(!empty($datas['URLIcone'])) { ?>    
  <tr class="color_row0">
    <td>Icône</td>
    <td align=center><img align=absmiddle src='<?php echo $datas['URLIcone']; ?>'></td>
  </tr>
<?php }
   if(!empty($datas['URLMedium'])) { 
?>    
  <tr class="color_row0">
    <td>Medium Image</td>
    <td align=center><img align=absmiddle src='<?php echo $datas['URLMedium']; ?>'></td>
  </tr>
<?php }
   if(!empty($datas['URLImage'])) { 
?>    
  <tr class="color_row0">
    <td>Large Image</td>
    <td align=center><img align=absmiddle src='<?php echo $datas['URLImage']; ?>'></td>
  </tr>
<?php } ?>
  <tr class="color_row0">
    <td>Catégorie</td>
    <td><?php echo $cnhCategorieName[$datas['Categorie']]; ?></td>
  </tr>
  <tr class="color_row0">
    <td>Taille de transport</td>
    <td><?php echo $datas['Taille']; ?></td>
  </tr>
  <tr class="color_row0">
    <td>PA</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr class="color_row0">
        <td>Chasseur </td>
        <td><?php echo $datas['PAChasseur']; ?></td>
      </tr>
      <tr class="color_row0">
        <td>GVG</td>
        <td><?php echo $datas['PAGVG']; ?></td>
      </tr>
    </table>    </td>
  </tr>
  <tr class="color_row0">
    <td>Ressources</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
<?php
  $totalress = 0;

  for($i = 0; $i < sizeof($cnhMineraisName); $i++) {
    echo('<tr class="color_row0"><td><img src="'.IMAGES_URL.$cnhMineraisName[$i].'.png" /> '.$cnhMineraisName[$i].'</td>');
    echo('<td>'.$datas[$cnhMineraisName[$i]].'</td></tr>');
    $totalress += $datas[$cnhMineraisName[$i]];
  }
?>
      <tr class="color_row0">
        <td><b>Total</b></td>
        <td><b><?php echo $totalress; ?></b></td>
      </tr>
    </table>
      </td>
  </tr>
<?php if($datas['Categorie'] == 0) { ?>
  <tr class="color_row0">
    <td>Propulsion</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr class="color_row0">
        <td>Impulsion</td>
        <td><?php echo $datas['PropImpulsion']; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Warp</td>
        <td><?php echo $datas['PropWarp']; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Consommation</td>
        <td><?php echo $datas['PropConsommation']; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Statistiques</td>
        <td>
<?php
  if($datas['PropImpulsion']>0) {
    echo('Impulsion/PA (Chasseur/GVG): '.($datas['PropImpulsion']/$datas['PAChasseur']).' / '.($datas['PropImpulsion']/$datas['PAGVG']).'<br>');
    echo('Impulsion/Consommation: '.($datas['PropConsommation']/$datas['PropImpulsion']).'<br>');
  }
  if($datas['PropWarp']>0) {
    echo('Warp/PA (Chasseur/GVG): '.($datas['PropWarp']/$datas['PAChasseur']).' / '.($datas['PropWarp']/$datas['PAGVG']).'<br>');
    echo('Warp/Consommation: '.($datas['PropConsommation']/$datas['PropWarp']).'<br>');
  }
?>
        </td>
      </tr>
    </table>
	</td>
  </tr class="color_row0">
<?php } 
elseif($datas['Categorie'] == 1) { ?>
  <tr class="color_row0">
    <td>Armement</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr class="color_row0">
        <td>Type</td>
        <td><?php echo $cnhArmeName[$datas['ArmType']]; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Dégâts</td>
        <td><?php echo $datas['ArmDegat']; ?></td>
      </tr class="color_row0">
      <tr class="color_row0">
        <td>Précision</td>
        <td><?php echo $datas['ArmManiabilite']; ?></td>
      </tr>
    </table>
      </td>
  </tr>
<?php } 
elseif($datas['Categorie'] == 2) { ?>
  <tr class="color_row0">
    <td>Protection</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr class="color_row0">
        <td>Type</td>
        <td><?php echo $cnhProtectionName[$datas['ProtType']]; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Protection Chasseur</td>
        <td><?php echo $datas['ProtChasseur']; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Protection GVG</td>
        <td><?php echo $datas['ProtGVG']; ?></td>
      </tr>
    </table>
      </td>
  </tr>
<?php } 
elseif($datas['Categorie'] == 3) { ?>
  <tr class="color_row0">
    <td>Equipement</td>
    <td><table width="0%" border="0" cellspacing="5" cellpadding="0">
      <tr class="color_row0">
        <td>Type</td>
        <td><?php echo $cnhEquipementName[$datas['EquipType']]; ?></td>
      </tr>
      <tr class="color_row0">
        <td>Capacité / Niveau</td>
        <td><?php echo $datas['EquipNiv']; ?></td>
      </tr>
    </table>
      </td>
  </tr>
<?php } ?>
  <tr class="color_row0">
    <td>Fourniture</td>
    <td>
<?php
  $mysql_fab = DataEngine::sql('SELECT u.`Login`, u.`Module_ID`, u.`Modifier` FROM `SQL_PREFIX_Modules_Users` u LEFT JOIN `SQL_PREFIX_Modules_Users_Config` c ON u.`Login`=c.`Login` WHERE `Module_ID`='.$_GET['viewid'].' AND '.($datas['Categorie']==4 ? 'ChassisSecret' : 'CommerceType').'<=1 ORDER BY u.`Login`') or die(mysql_error());
  $i=0; $minmod=0; $maxmod=0;
  
  while($ligne = mysql_fetch_array($mysql_fab))	{
    if($ligne['Modifier'] < $minmod) $minmod = $ligne['Modifier'];
    if($ligne['Modifier'] > $maxmod) $maxmod = $ligne['Modifier'];

    echo('<b>'.$ligne['Login'].'</b> ('.$ligne['Modifier'].'%)<br>');
      
    $i++;
  }
  
  echo('<hr>');
  echo($i.' fabricant'.($i>1 ? 's':'').' fourni'.($i>1 ? 'ssent':'t').' ce module.<br>');
  echo('Prix minimum minoré de '.$minmod.'%.<br>');
  echo('Prix maximum majoré de '.$maxmod.'%.<br>');
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