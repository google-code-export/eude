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
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_EDIT'))
    output::Boink('./index.php');
  
  if(isset($_GET['delid']) && DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE')) {
    $datas_id=$_GET['delid'];
    $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Template` WHERE `ID`='.$datas_id) or die(mysql_error());
    $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Users` WHERE `Module_ID`='.$datas_id) or die(mysql_error());
    header('Location: template_list.php?commande=false');
    exit;
  }
  elseif(isset($_GET['editid']) && !isset($_POST['ID'])) {
    $mysql_result = DataEngine::sql('SELECT `ID`, `Nom`, `Abreviation`, `Description`, `Categorie`, `Taille`, `PAChasseur`, `PAGVG`, `URLIcone`,
	`URLMedium`, `URLImage`, `Temps`, `Titane`, `Cuivre`, `Fer`, `Aluminium`, `Mercure`, `Silicium`, `Uranium`, `Krypton`, `Azote`, `Hydrogene`,
	`PropImpulsion`, `PropWarp`, `PropConsommation`, `ArmType`, `ArmDegat`, `ArmManiabilite`, `ProtType`, `ProtChasseur`, `ProtGVG`, `EquipType`,
	`EquipNiv` FROM `SQL_PREFIX_Modules_Template` WHERE `ID`='.$_GET['editid']) or die(mysql_error());
    $datas=mysql_fetch_array($mysql_result);
    
    $datas['Nom'] = stripslashes($datas['Nom']); 
    $datas['Abreviation'] = stripslashes($datas['Abreviation']); 
    $datas['Description'] = stripslashes($datas['Description']); 
    $datas['URLIcone'] = stripslashes($datas['URLIcone']); 
    $datas['URLImage'] = stripslashes($datas['URLImage']); 
    
    $datas_id=$_GET['editid'];
  }
  elseif(isset($_POST['ID'])) {
    $datas_id=$_POST['ID'];
    $datas['Nom']=sqlesc(stripslashes($_POST['Nom']));
    $datas['Abreviation']=sqlesc(stripslashes($_POST['Abreviation']));
    $datas['Description']=sqlesc(stripslashes($_POST['Description']));
    $datas['Categorie']=$_POST['Categorie'];
    $datas['Taille']=$_POST['Taille'];
    $datas['PAChasseur']=$_POST['PAChasseur'];
    $datas['PAGVG']=$_POST['PAGVG'];
    $datas['URLIcone']=stripslashes($_POST['URLIcone']);
    $datas['URLMedium']=stripslashes($_POST['URLMedium']);
    $datas['URLImage']=stripslashes($_POST['URLImage']);
    $datas['Temps']=$_POST['Temps'];
    $datas['Titane']=$_POST['Titane'];
    $datas['Cuivre']=$_POST['Cuivre'];
    $datas['Fer']=$_POST['Fer'];
    $datas['Aluminium']=$_POST['Aluminium'];
    $datas['Mercure']=$_POST['Mercure'];
    $datas['Silicium']=$_POST['Silicium'];
    $datas['Uranium']=$_POST['Uranium'];
    $datas['Krypton']=$_POST['Krypton'];
    $datas['Azote']=$_POST['Azote'];
    $datas['Hydrogene']=$_POST['Hydrogene'];
    $datas['PropImpulsion']=$_POST['PropImpulsion'];
    $datas['PropWarp']=$_POST['PropWarp'];
    $datas['PropConsommation']=$_POST['PropConsommation'];
    $datas['ArmType']=$_POST['ArmType'];
    $datas['ArmDegat']=$_POST['ArmDegat'];
    $datas['ArmManiabilite']=$_POST['ArmManiabilite'];
    $datas['ProtType']=$_POST['ProtType'];
    $datas['ProtChasseur']=$_POST['ProtChasseur'];
    $datas['ProtGVG']=$_POST['ProtGVG'];
    $datas['EquipType']=$_POST['EquipType'];
    $datas['EquipNiv']=$_POST['EquipNiv'];
  }
  else {
    $datas_id=0;
    $datas['Nom']='';
    $datas['Abreviation']='';
    $datas['Description']='';
    $datas['Categorie']=0;
    $datas['Taille']=0;
    $datas['PAChasseur']=0;
    $datas['PAGVG']=0;
    $datas['URLIcone']='';
    $datas['URLMedium']='';
    $datas['URLImage']='';
    $datas['Temps']='';
    $datas['Titane']=0;
    $datas['Cuivre']=0;
    $datas['Fer']=0;
    $datas['Aluminium']=0;
    $datas['Mercure']=0;
    $datas['Silicium']=0;
    $datas['Uranium']=0;
    $datas['Krypton']=0;
    $datas['Azote']=0;
    $datas['Hydrogene']=0;
    $datas['PropImpulsion']=0;
    $datas['PropWarp']=0;
    $datas['PropConsommation']=0;
    $datas['ArmType']=0;
    $datas['ArmDegat']=0;
    $datas['ArmManiabilite']=0;
    $datas['ProtType']=0;
    $datas['ProtChasseur']=0;
    $datas['ProtGVG']=0;
    $datas['EquipType']=0;
    $datas['EquipNiv']=0;
  }

  if(isset($_POST['submit'])){
    if(($_POST['submit'] == 'Sauver' || $_POST['submit'] == 'Sauver et retour') && $datas_id > 0) {
      mysql_update_array('SQL_PREFIX_Modules_Template', '`ID`='.$datas_id, $datas);
      if($_POST['submit'] == 'Sauver et retour') {
        header('Location: template_list.php#ID'.$datas_id);
        exit;
      }
    }
    elseif($_POST['submit'] == 'SUPPRIMER' && $datas_id > 0) {
      $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Template` WHERE `ID`='.$datas_id) or die(mysql_error());
      $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Users` WHERE `Module_ID`='.$datas_id) or die(mysql_error());
      header('Location: template_list.php');
      exit;
    }
    elseif($_POST['submit'] == 'Sauver sous nouveau') {
      $datas_id = mysql_insert_array('SQL_PREFIX_Modules_Template', $datas);
    }
  }
  
// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->
<center><a href='template_list.php'>Retourner à la liste des templates de module...</a></center><br />
<form name="form1" method="post" action="">
<p align="center">
<?php if($datas_id > 0) { ?>
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver">
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver et retour">
<?php } ?>
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver sous nouveau">
<?php if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE')) { ?>
<input class="color_row0" type="submit" name="submit" id="submit" value="SUPPRIMER">
<?php } ?>
<input class="color_row0" type="reset" name="Reset" id="Reset" value="Réinitialiser">
</p>
<table border="1" align="center" cellpadding="3" cellspacing="0">
  <tr class="color_header">
    <th>Information</th>
    <th>Contenu</th>
  </tr>
  <tr class="color_row0">
    <td>ID</td>
    <td><input class="color_row0" type="hidden" name="ID" id="ID" value="<?php echo $datas_id; ?>"><?php echo $datas_id; ?></td>
  </tr>
  <tr class="color_row0">
    <td>Nom long du module</td>
    <td><input class="color_row0" type="text" name="Nom" id="Nom" size=60 maxlength=125 value="<?php echo stripslashes($datas['Nom']); ?>"></td>
  </tr>
  <tr class="color_row0">
    <td>Abréviation (ou nom alternatif) du module</td>
    <td><input class="color_row0" type="text" name="Abreviation" id="Abreviation" size=60 maxlength=125 value="<?php echo $datas['Abreviation']; ?>"></td>
  </tr>
  <tr class="color_row0">
    <td>Description</td>
    <td><textarea class="color_row0" name="Description" cols="45" rows="5" wrap="virtual" id="Description"><?php echo $datas['Description']; ?></textarea></td>
  </tr>
  <tr class="color_row0">
    <td>URL Icône</td>
    <td><input class="color_row0" type="text" name="URLIcone" id="URLIcone" size=60 maxlength=245 value="<?php echo $datas['URLIcone']; ?>">
<?php
  if($datas['URLIcone']!='') echo('<br><div align=center><img align=absmiddle src='.$datas["URLIcone"].'></div>');
?>    
    </td>
  </tr>
  <tr class="color_row0">
    <td>URL Medium</td>
    <td><input class="color_row0" type="text" name="URLMedium" id="URLMedium" size=60 maxlength=245 value="<?php echo $datas['URLMedium']; ?>">
<?php
  if($datas['URLMedium']!='') echo('<br><div align=center><img align=absmiddle src='.$datas['URLMedium'].'></div>');
?>    
    </td>
  </tr>
  <tr class="color_row0">
    <td>URL Image</td>
    <td><input class="color_row0" type="text" name="URLImage" id="URLImage" size=60 maxlength=245 value="<?php echo $datas['URLImage']; ?>">
<?php
  if($datas['URLImage']!='') echo('<br><div align=center><img align=absmiddle src='.$datas['URLImage'].'></div>');
?>    
    </td>
  </tr>
  <tr class="color_row0">
    <td>Temps de construction</td>
    <td><input class="color_row0" type="text" name="Temps" id="Temps" size=15 maxlength=10 value="<?php echo $datas['Temps']; ?>"> (D HH:MM:SS)</td>
  </tr>
  <tr class="color_row0">
    <td>Catégorie</td>
    <td><select class="color_row0" name="Categorie" id="Categorie">
<?php
  for($i=0;$i < sizeof($cnhCategorieName); $i++)
    echo('<option value='.$i.' '.($datas['Categorie']==$i ? 'selected' : '').'> '.$cnhCategorieName[$i].'</option>');
?>    
    </select>    </td>
  </tr>
  <tr class="color_row0">
    <td>Taille de transport</td>
    <td><input class="color_row0" type="text" name="Taille" id="Taille" value="<?php echo $datas['Taille']; ?>"></td>
  </tr>
  <tr class="color_row0">
    <td>PA</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="color_row0">
        <td>Chasseur </td>
        <td><input class="color_row0" type="text" name="PAChasseur" id="PAChasseur" value="<?php echo $datas['PAChasseur']; ?>"></td>
      </tr>
      <tr class="color_row0">
        <td>GVG</td>
        <td><input class="color_row0" type="text" name="PAGVG" id="PAGVG" value="<?php echo $datas['PAGVG']; ?>"></td>
      </tr>
    </table>    </td>
  </tr>
  <tr class="color_row0">
    <td>Ressources</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
  for($i = 0; $i < sizeof($cnhMineraisName); $i++) {
    echo('<tr class="color_row0"><td><img src='.IMAGES_URL.$cnhMineraisName[$i].'.png /> '.$cnhMineraisName[$i].'</td>');
    echo('<td><input class="color_row0" type="text" name='.$cnhMineraisName[$i].' id='.$cnhMineraisName[$i].' value='.$datas[$cnhMineraisName[$i]].'></td></tr>');
  }
?>
    </table></td>
  </tr>
  <tr class="color_row0">
    <td>Propulsion</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="color_row0">
        <td>Impulsion</td>
        <td><input class="color_row0" type="text" name="PropImpulsion" id="PropImpulsion" value="<?php echo $datas['PropImpulsion']; ?>"></td>
      </tr>
      <tr class="color_row0">
        <td>Warp</td>
        <td><input class="color_row0" type="text" name="PropWarp" id="PropWarp" value="<?php echo $datas['PropWarp']; ?>"></td>
      </tr>
      <tr class="color_row0">
        <td>Consommation</td>
        <td><input class="color_row0" type="text" name="PropConsommation" id="PropConsommation" value="<?php echo $datas['PropConsommation']; ?>"></td>
      </tr>
    </table>
	</td>
  </tr>
  <tr class="color_row0">
    <td>Armement</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="color_row0">
        <td>Type</td>
        <td><select class="color_row0" name="ArmType" id="ArmType">
          <option value="0" <?php echo ($datas['ArmType']==0 ? 'selected' : ''); ?> >Laser</option>
          <option value="1" <?php echo ($datas['ArmType']==1 ? 'selected' : ''); ?> >Projectile</option>
          <option value="2" <?php echo ($datas['ArmType']==2 ? 'selected' : ''); ?> >Ion</option>
        </select></td>
      </tr>
      <tr class="color_row0">
        <td>Dégâts</td>
        <td><input class="color_row0" type="text" name="ArmDegat" id="ArmDegat" value="<?php echo $datas['ArmDegat']; ?>"></td>
      </tr>
      <tr class="color_row0">
        <td>Précision</td>
        <td><input class="color_row0" type="text" name="ArmManiabilite" id="ArmManiabilite" value="<?php echo $datas['ArmManiabilite']; ?>"></td>
      </tr>
    </table>
      </td>
  </tr>
  <tr class="color_row0">
    <td>Protection</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="color_row0">
        <td>Type</td>
        <td><select class="color_row0" name="ProtType" id="ProtType">
          <option value="0" <?php echo ($datas['ProtType']==0 ? 'selected' : ''); ?> >Renfort</option>
          <option value="1" <?php echo ($datas['ProtType']==1 ? 'selected' : ''); ?> >Bouclier</option>
        </select></td>
      </tr>
      <tr class="color_row0">
        <td>Protection Chasseur</td>
        <td><input class="color_row0" type="text" name="ProtChasseur" id="ProtChasseur" value="<?php echo $datas['ProtChasseur']; ?>"></td>
      </tr>
      <tr class="color_row0">
        <td>Protection GVG</td>
        <td><input class="color_row0" type="text" name="ProtGVG" id="ProtGVG" value="<?php echo $datas['ProtGVG']; ?>"></td>
      </tr>
    </table>
      </td>
  </tr>
  <tr class="color_row0">
    <td>Equipement</td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="color_row0">
        <td>Type</td>
        <td><select class="color_row0" name="EquipType" id="EquipType">
          <option value="0" <?php echo ($datas['EquipType']==0 ? 'selected' : ''); ?> >Cargo</option>
          <option value="1" <?php echo ($datas['EquipType']==1 ? 'selected' : ''); ?> >Troupes</option>
          <option value="2" <?php echo ($datas['EquipType']==2 ? 'selected' : ''); ?> >Minage</option>
          <option value="3" <?php echo ($datas['EquipType']==3 ? 'selected' : ''); ?> >Scan</option>
          <option value="4" <?php echo ($datas['EquipType']==4 ? 'selected' : ''); ?> >Récupérateur</option>
          <option value="5" <?php echo ($datas['EquipType']==5 ? 'selected' : ''); ?> >Réservoir</option>
          <option value="6" <?php echo ($datas['EquipType']==6 ? 'selected' : ''); ?> >Colonisation</option>
          <option value="7" <?php echo ($datas['EquipType']==7 ? 'selected' : ''); ?> >Camouflage</option>
                </select></td>
      </tr>
      <tr class="color_row0">
        <td>Capacité / Niveau</td>
        <td><input class="color_row0" type="text" name="EquipNiv" id="EquipNiv" value="<?php echo $datas['EquipNiv']; ; ?>"></td>
      </tr>
    </table>
      </td>
  </tr>
</table>
<p align="center">
<?php if($datas_id > 0) { ?>
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver">
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver et retour">
<?php } ?>
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver sous nouveau">
<?php if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE')) { ?>
<input class="color_row0" type="submit" name="submit" id="submit" value="SUPPRIMER">
<?php } ?>
<input class="color_row0" type="reset" name="Reset" id="Reset" value="Réinitialiser">
</p>
</form>
<br />
<center><a href='template_list.php'>Retourner à la liste des templates de module...</a></center>

<!-- FIN CODE LIBRE -->
</BODY></HTML>


<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();