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
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_PREF'))
    output::Boink(ROOT_URL.'index.php');

  if(isset($_GET['delid']) && $_SESSION['_Perm'] > AXX_ADMIN) {
    $datas_id=stripslashes($_GET['delid']);
    
    $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Users_Config` WHERE `Login`='.$datas_id) or die(mysql_error());
    $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Users` WHERE `Login`='.$datas_id) or die(mysql_error());
    header('Location: index.php');
    exit;
  }
  elseif(!isset($_POST['ID'])) {
    $mysql_result = DataEngine::sql('SELECT `Login`, `Modifier`, `ListSort`, `ActivatedSort`, `CommerceType`, `ChassisSecret`, `Paiement`, `Planetes` FROM `SQL_PREFIX_Modules_Users_Config` WHERE `Login`=\''.$Joueur.'\'') or die(mysql_error());
    if(!($datas=mysql_fetch_array($mysql_result))) {
      $datas['Login'] = $Joueur;
      $datas['Modifier']=0;
      $datas['ListSort']=0;
      $datas['ActivatedSort']=false;
      $datas['CommerceType']=0;
      $datas['ChassisSecret']=1;
      $datas['Paiement']=1;
      $datas['Planetes']=0;
      $datas_id = '';  
    }
    else {
      $datas['Login'] = stripslashes($datas['Login']);
      $datas_id = $datas['Login'];  
      
      if(empty($datas['Paiement']))
        $datas['Paiement'] = 1;
      if(empty($datas['Planetes']))
        $datas['Planetes'] = 0;
    }
  }
  elseif(isset($_POST['ID'])) {
    $datas_id = $_POST['ID'];  
    
    if(isset($_POST['Login']) && $_SESSION['_Perm'] > AXX_ADMIN)
      $datas['Login']=stripslashes($_POST['Login']);
    else
      $datas['Login']=$Joueur;

    if($_POST['ActivatedSort'] == 'on')
      $datas['ActivatedSort']=true;
    else
      $datas['ActivatedSort']=false;
    
    $datas['Modifier']=$_POST['Modifier'];
    $datas['ListSort'] = $_POST['ListSort'];
    $datas['CommerceType']=$_POST['CommerceType'];
    $datas['ChassisSecret']=$_POST['ChassisSecret'];
    
    $datas['Paiement'] = 0;
    for($i = 0; $i < 15; $i++) {
      if($_POST['Paiement_'.$i] == 'on')
        $datas['Paiement'] += pow(2, $i);
    }

    $datas['Planetes'] = 0;
    for($i = 0; $i < 5; $i++) {
      if($_POST['Planete_'.$i] == 'on')
        $datas['Planetes'] += pow(2, $i);
    }
    
  }
  else {
    header('Location: index.php');
    exit;
  }

  if(isset($_POST['submit'])){
    if(($_POST['submit'] == 'Sauver' || $_POST['submit'] == 'Sauver et retour') && !empty($datas_id)) {
      mysql_update_array('SQL_PREFIX_Modules_Users_Config', '`Login`=\''.$datas['Login'].'\'', $datas);
      Load_Prefs();
      Update_Modifier($datas['Login'], $datas['Modifier']);
      
      if($_POST['submit'] == 'Sauver et retour') {
        header('Location: index.php');
        exit;
      }
    }
    elseif(($_POST['submit'] == 'Sauver' || $_POST['submit'] == 'Sauver et retour')) {
      mysql_insert_array('SQL_PREFIX_Modules_Users_Config', $datas);
      $datas['DateCreated'] = date(DATE_SQL_FORMAT);
      $datas['DateLast'] = $datas['DateCreated'];
      $datas_id = $datas['Login']; 
      Load_Prefs();
      Update_Modifier($datas['Login'], $datas['Modifier']);
      
      if($_POST['submit'] == 'Sauver et retour') {
        header('Location: index.php');
        exit;
      }
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

<form name="form1" method="post" action=""><input class="color_row0" type="hidden" name="ID" id="ID" value='<?php echo $datas_id; ?>'><input class="color_row0" type="hidden" name="Login" id="Login" value='<?php echo $datas['Login']; ?>'>
<table border="1" align="center" cellpadding="3" cellspacing="0">
  <tr class="text_center color_header">
    <th>Information</th>
    <th>Contenu</th>
  </tr>
  <tr class="color_row0">
    <td>Modificateur du prix des modules et chassis</td>
    <td>+/- <input class="color_row0" type="text" name="Modifier" id="Modifier" size=20 maxlength=15 value="<?php echo $datas['Modifier']; ?>"> %</td>
  </tr>
  <tr class="color_row0">
    <td>Tri par défaut de la liste des modules</td>
    <td><?php DisplayListSelect("ListSort", $cnhListSort, $datas['ListSort']); ?></td>
  </tr>
  <tr class="color_row0">
    <td>Modification du tri</td>
    <td><input class="color_row0" type="checkbox" name="ActivatedSort" id="ActivatedSort" <?php echo ($datas['ActivatedSort'] ? 'checked ' : '' ); ?>/> Mettre en haut de liste les modules que vous pouvez construire.</td>
  </tr>
  <tr class="color_row0">
    <td>A qui voulez-vous vendre vos modules?</td>
    <td><?php DisplayListSelect("CommerceType", $cnhCommerceType, $datas['CommerceType']); ?></td>
  </tr class="color_row0">
  <tr class="color_row0">
    <td>Quels paiements acceptez-vous?</td>
    <td><table border="0" cellspacing="0" cellpadding="0">
    <tr class="color_row0"><td><input class="color_row0" type="checkbox" name="Paiement_0" <?php echo ($datas['Paiement'] & UP_CREDITS ? 'checked ' : '' ); ?>/></td><td>Vous acceptez les crédits comme paiement.</td></tr>
    <tr class="color_row0"><td><input class="color_row0" type="checkbox" name="Paiement_1" <?php echo ($datas['Paiement'] & UP_EXACT ? 'checked ' : '' ); ?>/></td><td>Vous acceptez un paiement en ressources identiques aux ressources utilisées.</td></tr>
    <tr class="color_row0" valign=top><td><input class="color_row0" type="checkbox" name="Paiement_2" <?php echo ($datas['Paiement'] & UP_CHOIX ? 'checked ' : '' ); ?>/></td><td>Vous acceptez un paiement dans une sélection de ressources ci-dessous:
<?php
  for($i = 0; $i < sizeof($cnhMineraisName); $i++)
    echo('<br /><input class="color_row0" type="checkbox" name="Paiement_'.($i+3).'" '.($datas['Paiement'] & pow(2, $i + 3) ? 'checked ' : '' ).'/> <img src='.IMAGES_URL.$cnhMineraisName[$i].'.png /> '.$cnhMineraisName[$i]);
?>
    </td></tr>
    </table></td>
  </tr>
  <tr class="color_row0">
    <td>Sur quelle(s) planète(s) vous appartenant<br /> acceptez-vous les paiements en ressources?</td>
    <td>
<?php
    $mysql_result = DataEngine::sql('SELECT planet0, coord0, planet1, coord1, planet2, coord2, planet3, coord3, planet4, coord4 FROM SQL_PREFIX_ownuniverse WHERE `UTILISATEUR`=\''.$Joueur.'\'') or die(mysql_error());

    $i = 0;
    if(($ownplanetes = mysql_fetch_array($mysql_result))) {
      for($i = 0; $ownplanetes['planet'.$i] != '' && $i < 5; $i++) {
        if($i > 0)
          echo('<br />');
        
        echo('<input class="color_row0" type="checkbox" name="Planete_'.$i.'" '.($datas['Planetes'] & pow(2, $i) ? 'checked ' : '' ).'/> '.$ownplanetes['planet'.$i].' aux coordonnées '.$ownplanetes['coord'.$i]);
      }
    }
    
    if($i == 0)
      echo("<font color=red><b>Vous n'avez pas saisi vos informations dans la partie Ma fiche => Production, veuillez le faire à la page <a href=\"".ROOT_URL."ownuniverse.php\" target=\"_blank\">Production</a>.</b></font>");
?>
    </td>
  </tr>
</table>
<p align="center">
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver">
<input class="color_row0" type="submit" name="submit" id="submit" value="Sauver et retour">
<input class="color_row0" type="reset" name="Reset" id="Reset" value="Réinitialiser">
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