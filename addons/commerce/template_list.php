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

  if(isset($_GET['Activate']))
    $mysql_result = DataEngine::sql('INSERT INTO `SQL_PREFIX_Modules_Users`(`Login`, `Module_ID`, `Modifier`) VALUES (\''.$Joueur.'\', \''.$_GET['Activate'].'\', 0)') or die(mysql_error());
  elseif(isset($_GET['Deactivate']))
    $mysql_result = DataEngine::sql('DELETE FROM `SQL_PREFIX_Modules_Users` WHERE `Login`=\''.$Joueur.'\' AND Module_ID=\''.$_GET['Deactivate'].'\'') or die(mysql_error());

  $modecommande = false;
  $alldisplay = false;
  $t = null;

  if(isset($_GET['commande'])) 
    $t = $_GET['commande']; 
  elseif(isset($_SESSION['commandmode']))
    $t = $_SESSION['commandmode']; 

  if(!is_null($t)) {
    if($t == 'true') {
      $modecommande = true;
      $_SESSION['commandmode'] = 'true';
    }
    elseif($t == 'full') {
      $alldisplay = true;
      $modecommande = true;
      $_SESSION['commandmode'] = 'full';
    }
  }

  if(isset($_POST['submit'])) {
    if($_POST['submit'] == 'Lister') {
      $_SESSION['up_ActivatedSort'] = ($_POST['OwnBuild'] == 'on');
      $_SESSION['up_ListSort'] = $_POST['TriPar'];
    }
  }
  
  $boutonscommande = '<input class="color_row0" type="submit" name="submit" value="Suivant &gt;&gt;"> <input class="color_row0" type="submit" name="submit" value="Vider panier">';
//  $boutonscommande = '<input class="color_row0" type="submit" name="submit" value="Vider panier"> <input class="color_row0" type="submit" name="submit" value="Suivant &gt;&gt;">';
  $tnbcol = 6;
  
// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->

<p align='center'><font color='#ffffff'>
<?php
  if($modecommande) {
    if(!$alldisplay) {
?>
Notez que dans ce mode "<i>commande</i>",<br />vous ne voyez que les modules, que vous ne pouvez pas fabriquer,<br /> et uniquement ceux proposés par les autres membres de l'Empire.<br />
<br />[&nbsp;<a href='?commande=full'>Voir la  liste de tous les modules...</a>&nbsp;]<br />
<?php
    }
    else {
?>
[&nbsp;<a href='?commande=true'>Voir seulement les modules que vous ne fabriquez pas...</a>&nbsp;]<br />
<?php
    }
  echo("<form method='post' action='shop_checkout.php'>");
  }
  else {
?>
<form name="form" method="post">Ordre de tri: <?php DisplayListSelect("TriPar", $cnhListSort, $_SESSION['up_ListSort']); ?> / <input class="color_row0" type="checkbox" name="OwnBuild" id="OwnBuild" <?php echo ($_SESSION['up_ActivatedSort'] ? ' checked' : ''); ?>/> Mettre en haut de liste les modules que vous pouvez fabriquer. <input class="color_row0" type="submit" name="submit" id="submit" value="Lister"></form>
<?php
  }
?>
</font></p>

<table border="1" align="center" cellpadding="3" cellspacing="0">
<tr class="text_center color_bigheader"><th>Icône</th><th>Nom</th><th>Catégorie</th><th>Type</th><th>Niveau</th><th><?php echo ($modecommande ? "Commande" : "Options"); ?></th></tr>
<!--
<tr><th><a href="?Tri=Categorie">Icône</a></th><th><a href="?Tri=Nom">Nom</a></th><th><a href="?Tri=Categorie">Catégorie</a></th><th><a href="?Tri=CatType">Type</a></th><th><a href="?Tri=NivTech">Niveau</a></th><th><a href="?AddTri=Options<?php if(isset($_GET["Tri"])) echo("&Tri=".$_GET["Tri"]); ?>">Options</a></th></tr>
-->

<?php
  $titlecat = 0;

  if($_SESSION['up_ActivatedSort'])
    $tritable = '(ISNULL(SQL_PREFIX_Modules_Own.Modifier)) DESC, Nom';
  else
    $tritable = 'Nom';

  switch($_SESSION['up_ListSort']) {
  case 1:
    if($_SESSION['up_ActivatedSort'])
      $tritable = 'Categorie, (ISNULL(SQL_PREFIX_Modules_Own.Modifier)) DESC, Nom';
    else
      $tritable = 'Categorie, Nom';
      
    $titlecat = 1;
    break;
  case 2:
    if($_SESSION['up_ActivatedSort'])
      $tritable = 'Categorie, CatType, (ISNULL(SQL_PREFIX_Modules_Own.Modifier)) DESC, Nom';
    else
      $tritable = 'Categorie, CatType, Nom';
    
    $titlecat = 2;
    break;
  case 3:
    if($_SESSION['up_ActivatedSort'])
      $tritable = 'Categorie, CatType, (ISNULL(SQL_PREFIX_Modules_Own.Modifier)) DESC, NivTech';
    else
      $tritable = 'Categorie, CatType, NivTech';
    
    $titlecat = 2;
    break;
  }


  $sqlreq = '
  SELECT ID, URLIcone, Categorie, Nom, Abreviation, SQL_PREFIX_Modules_Own.Modifier,
  (SELECT COUNT(SQL_PREFIX_Modules_Users.Login) FROM SQL_PREFIX_Modules_Users LEFT JOIN SQL_PREFIX_Modules_Users_Config ON SQL_PREFIX_Modules_Users.Login = SQL_PREFIX_Modules_Users_Config.Login WHERE Module_ID = ID AND IF(Categorie=4, ChassisSecret, CommerceType)<=1) AS TotalFab, 
  (IF(Categorie = 0, IF(PropWarp >= 1, 1, 0), IF(Categorie = 1, ArmType, IF(Categorie = 2, ProtType, IF(Categorie = 3, EquipType, ChassType))))) AS CatType,
  (IF(Categorie = 0, IF(PropWarp > 0, PropWarp, PropImpulsion), IF(Categorie = 1, ArmDegat, IF(Categorie = 2, ProtChasseur, IF(Categorie = 3, EquipNiv, ChassPA))))) AS NivTech,
  (IF(Categorie = 0, IF(PropWarp > 0, CONCAT(FORMAT(PropWarp, 0)," (",FORMAT(PropImpulsion, 0),")"), FORMAT(PropImpulsion, 0)), IF(Categorie = 1, CONCAT(FORMAT(ArmDegat, 0)," (",FORMAT(ArmManiabilite, 0),")"), IF(Categorie = 2, CONCAT(FORMAT(ProtChasseur, 0),", ",FORMAT(ProtGVG, 0)), IF(Categorie = 3, FORMAT(EquipNiv, 0), FORMAT(ChassPA,0)))))) AS NivDef
  FROM SQL_PREFIX_Modules_Template LEFT JOIN
(SELECT * FROM SQL_PREFIX_Modules_Users WHERE Login = "'.$Joueur.'") AS SQL_PREFIX_Modules_Own ON SQL_PREFIX_Modules_Template.ID = SQL_PREFIX_Modules_Own.Module_ID
  ORDER BY '.$tritable;
//  pDebug($sqlreq);
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  
  $i = 0;
  $oldtitle = '';
  $nowtitle = '';
  $titremod = false;
  
  while($ligne = mysql_fetch_array($mysql_result))
	{
    $subcatname = $cnhNames[$ligne['Categorie']][$ligne['CatType']];
    $catname = $cnhCategorieName[$ligne['Categorie']];
    
    if($titlecat == 1)
      $nowtitle = $catname;
    elseif($titlecat == 2)
      $nowtitle = $subcatname.' ('.$catname.')';
    
    // En cas de mode commande
    if($modecommande) {
      if($i == 0 && !$titremod) {
        echo('<tr class="color_row0"><td colspan='.$tnbcol.' align=right>'.$boutonscommande.'</td></tr>');
        $titremod = true;
      }
      
      if(!$alldisplay && (!is_null($ligne['Modifier']) || $ligne['TotalFab'] <= 0 || $ligne['Categorie'] == 4)) continue;
    }
    
    // Changement de section
    if($oldtitle!=$nowtitle){
      echo('<tr class="text_center color_header"><td colspan='.$tnbcol.'>'.$nowtitle.'</td></tr>');
      $oldtitle = $nowtitle;
      $i = 0;
    }
	
    // Gestion de couleur de ligne
	  $i++;
		if($i%4==0)
      $bgcol=' class="color_row1"';
		else
      $bgcol=' class="color_row0"';

    // Début de ligne      
    echo('<tr '.$bgcol.'>');

    // Colonne 1 - Icône
    echo('<td align="center"><a name="ID'.$ligne['ID'].'">');
    if($ligne[URLIcone] != '')
      echo('<img src="'.$ligne['URLIcone'].'"></td>');
    else
      echo("&nbsp;</td>");

    // Colonne 2 - Nom
    echo('<td><a href="template_view.php?viewid='.$ligne['ID'].'"><font color="#FF6600">'.(!empty($ligne['Abreviation']) ? $ligne['Abreviation'] : $ligne['Nom']).'</font></a>');

    if ($ligne['TotalFab']>0)
      echo(' <font size=-1 style=italic>('.$ligne['TotalFab'].' fabricant'.($ligne['TotalFab']>1 ? 's' : '').')');
    
    echo("</td>");
    
    // Colonne 3 - Catégorie
    echo('<td>'.$catname.'</td>');
    
    // Colonne 4 - Sous-catégorie
    echo('<td>'.$subcatname.'</td>');
    
    // Colonne 5 - Niveau
    echo("<td>");

    switch($ligne['Categorie']) {
      case 0: // Propulsion
      case 1: // Protection
      case 2: // Armement
        echo($ligne['NivDef']);
        break;
      case 3: // Equipement
        switch($ligne['CatType']) {
          case 0: // Cargo
          case 1: // Troupes
          case 5: //Réservoir
            echo(number_format($ligne['NivTech'], 0, ',', '.'));
            break;
		  case 7: // Camouflage
          case 2: // Minage
          case 3: // Scan
          case 4: //Récupérateur
            echo($ligne['NivTech']);
            break;
          case 6: //Colonisation
            echo($ligne['NivTech'].' planète'); 
        }
    }
    echo("</td>");

    // Colonne 6 - Options OU Commande
    echo("<td valign='middle' align=center>");

    if($modecommande) {
    
      // MODE COMMANDE
      if(isset($_SESSION['basket']))
        $value = $_SESSION['basket'][$ligne['ID']];
      else
        $value = '';
      
      echo('<input class="color_row0" type=text name="Mod_'.$ligne['ID'].'" size=5 maxlength=5 value="'.$value.'">');
    }
    else {
    
      // MODE OPTIONS
        $defquerystring ='?';
        
        if(isset($_GET['Tri']))
          $defquerystring = $defquerystring.'Tri='.$_GET['Tri'].'&';
  
        if(isset($_GET['AddTri']))
          $defquerystring = $defquerystring.'AddTri='.$_GET['AddTri'].'&';
  
        if(!is_null($ligne['Modifier'])) {
          $defquerystring = $defquerystring.'Deactivate='.$ligne['ID'];
          echo('<a href="template_list.php'.$defquerystring.'#ID'.$ligne['ID'].'"><img src="images/thumbs_no_faded.png" align=middle border=0></a> <img src="images/thumbs_yes.png" align=middle border=0>');
        }
        else {
          $defquerystring = $defquerystring.'Activate='.$ligne['ID'];
          echo('<img src="images/thumbs_no.png" align=middle border=0> <a href="template_list.php'.$defquerystring.'#ID'.$ligne['ID'].'"><img src="images/thumbs_yes_faded.png" align=middle border=0></a>');
        }
      
      if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_EDIT')) {
        echo(' <a href="template_edit.php?editid='.$ligne['ID'].'"><img src="images/edit.png" align=middle border=0></a>');
      }
      if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_DELETE'))	{
          echo(" <a href='template_edit.php?delid=".$ligne['ID']."' onclick=\"javascript:return confirm('Confirmez-vous réellement la suppression de l\'élément?')\"><img src='images/delete.png' align=middle border=0></a>");
      }
    }
      
    echo("</td>");
    
    // Fin de ligne
    echo("</tr>\n");
  }

  if($modecommande)
    echo('<tr class="color_row0"><td colspan='.$tnbcol.' align=right>'.$boutonscommande.'</td></tr>');

?>

</table>

<?php
  if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_INSERT') && !$modecommande)
    echo("<p align=center>[&nbsp;<a href='template_edit.php'>Ajouter un template de module...</a>&nbsp;]</p>");
  elseif($modecommande)
    echo("</form>");
?>

<!-- FIN CODE LIBRE -->
</BODY></HTML>


<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();