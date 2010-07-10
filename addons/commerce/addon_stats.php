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
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_STATS'))
    output::Boink('./index.php');

// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->

<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr class="text_center color_bigheader"><th>Chiffres en tout genre</th></tr>
<?php
$sqlreq = 'SELECT
(SELECT COUNT(`Login`) FROM `SQL_PREFIX_Modules_Users_Config`) AS TotalUser,
(SELECT COUNT(`ID`) FROM `SQL_PREFIX_Modules_Template` WHERE `Categorie` <> 4) AS TotalModules,
(SELECT COUNT(DISTINCT `Module_ID`) FROM `SQL_PREFIX_Modules_Users` u
LEFT JOIN `SQL_PREFIX_Modules_Template` t ON u.`Module_ID` = t.`ID`
LEFT JOIN `SQL_PREFIX_Modules_Users_Config` c ON u.`Login` = c.`Login`
WHERE t.`Categorie` <> 4 AND c.`CommerceType` <= 1) AS TotalBuildModules';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $datas = mysql_fetch_array($mysql_result)
?>
<?php
  if ($datas['TotalBuildModules'] > 0) {
  $TotalSellModules = number_format($datas['TotalBuildModules'] / $datas['TotalModules'] * 100, 2);
  } else {
  $TotalSellModules = 0;
  }
 ?>
  <tr class="text_center color_row0"><td>
Nombre de membres inscrits: <b><?php echo $datas['TotalUser']; ?></b><br />
<br />
Nombre de schémas de modules: <b><?php echo $datas['TotalModules']; ?></b><br />
Nombre de modules différents vendus: <b><?php echo $datas['TotalBuildModules']; ?></b><br />
Vendus par l'Empire: <b><?php echo $TotalSellModules; ?>%</b> des modules totaux.<br />
  </td></tr></table>
<br />

  </table>
<br />
<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr class="text_center color_bigheader"><th colspan=3>Top 10 des vendeurs</th></tr>
  <tr class="text_center color_header"><td>Rang</td><td>Nombre de modules</td><td>Membres</td></tr>
<?php
  $sqlreq = 'SELECT DISTINCT u.`Login`, COUNT(u.`Module_ID`) AS TotalMade FROM `SQL_PREFIX_Modules_Users` u
LEFT JOIN `SQL_PREFIX_Modules_Template` t ON u.`Module_ID` = t.`ID`
LEFT JOIN `SQL_PREFIX_Modules_Users_Config` c ON u.`Login` = c.`Login`
WHERE t.`Categorie` <> 4 AND c.`CommerceType` <= 1
GROUP BY u.`Login` ORDER BY TotalMade DESC
  ';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $rang = 0;
  $oldrang = 0;
  while(($datas = mysql_fetch_array($mysql_result)) && $rang < 11) {
    if($oldrang != $datas['TotalMade'])
      $rang++;
      
    if($rang < 11) {
      if($oldrang != $datas['TotalMade']) {
        $oldrang = $datas['TotalMade'];
        
        if($rang != 1)
          echo("</td></tr>\n");
        
        echo('<tr class="text_center color_row0"><td>'.$rang.($rang == 1 ? 'er' : 'ème').'</td><td>'.$datas['TotalMade'].'</td><td>');
      }
      else
        echo('<br />');

      echo($datas['Login']);
    }
  }
?>
  </td></tr></table>
<br />
<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr class="text_center color_bigheader"><th colspan=2>Modules vendus que par un membre</th></tr>
  <tr class="text_center color_header"><td>Membres</td><td>Module(s)</td></tr>
<?php
$sqlreq = '
SELECT DISTINCT t.`ID`, t.`Nom`, u.`Login`, `URLIcone` FROM `SQL_PREFIX_Modules_Template` t
LEFT JOIN `SQL_PREFIX_Modules_Users` u ON t.`ID` = u.`Module_ID`
LEFT JOIN `SQL_PREFIX_Modules_Users_Config` c ON u.`Login` = c.`Login`
WHERE t.`Categorie` <> 4 AND c.`CommerceType` <= 1
GROUP BY `Nom`, `ID`, `URLIcone` HAVING COUNT(u.`Module_ID`) = 1
ORDER BY u.`Login`, t.`Nom`';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $oldrang = '';
  while($datas = mysql_fetch_array($mysql_result)) {
    if($oldrang != $datas['Login']) {
      if(!empty($oldrang))
        echo("</td></tr>\n");
      
      $oldrang = $datas['Login'];
      
      echo('<tr valign=top><td class="text_center color_row0"><b>'.$datas['Login'].'</b></td><td class="color_row0">');
    }
    else
      echo('<br />');

    echo('<a href="template_view.php?viewid='.$datas['ID'].'">'.(!empty($datas['URLIcone']) ? '<img src="'.$datas['URLIcone'].'" border=0 align=absmiddle>' : '<img src="images/nopicture.png" border=0 align=absmiddle>').'&nbsp;<font color="#ffff00" weight=large>'.$datas['Nom'].'</font></a>');
  }
?>
  </td></tr></table>
<br />
<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr class="text_center color_bigheader"><th colspan=3>Top 10 des modules les plus vendus</th></tr>
  <tr class="text_center color_header"><td>Rang</td><td>Nombre de vendeur</td><td>Modules</td></tr>
<?php
  $sqlreq = 'SELECT DISTINCT t.`ID`, t.`Nom`, COUNT(u.`Module_ID`) AS TotalMade, t.`URLIcone` FROM `SQL_PREFIX_Modules_Users` u
LEFT JOIN `SQL_PREFIX_Modules_Template` t ON u.`Module_ID` = t.`ID`
LEFT JOIN `SQL_PREFIX_Modules_Users_Config` c ON u.`Login` = c.`Login`
WHERE t.`Categorie` <> 4 AND c.`CommerceType` <= 1
GROUP BY t.`Nom`, t.`URLIcone`, t.`ID` ORDER BY TotalMade DESC, t.`Nom`';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $i = 0;
  $j = 1;
  $oldvalue = null;
  while(($datas = mysql_fetch_array($mysql_result)) && $j <= 10) {
    $i++;
    
    if($oldvalue != $datas['TotalMade']) {
      if(!is_null($oldvalue))
        echo('</td></tr>');
      echo('<tr valign=top><td class="text_center color_row0">'.$j.($j == 1 ? 'er' : 'ème').'</td><td class="text_center color_row0">'.$datas['TotalMade'].'</b> vendeur'.($datas['TotalMade'] > 1 ? 's' : '').'</td><td class="color_row0">');
      
      $oldvalue = $datas['TotalMade'];
      $j++;
      $i = 0;
    }
    
    if($i > 0)
      echo("<br>\n");
    echo('<a href="template_view.php?viewid='.$datas['ID'].'">'.(!empty($datas['URLIcone']) ? '<img src="'.$datas['URLIcone'].'" border=0 align=absmiddle>' : '<img src="images/nopicture.png" border=0 align=absmiddle>').'&nbsp;'.$datas['Nom'].'</a>');
  }
?>
  </td></tr></table>


<!-- FIN CODE LIBRE -->
</BODY></HTML>

<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();