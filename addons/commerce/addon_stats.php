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
  <link href="cnh_addon.css" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><? cnhTB(0) ?><br /><? cnhTB(0,null,null,"Stats") ?><hr><br />
<!-- DEBUT CODE LIBRE -->

<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr><th>Chiffres en tout genre</th></tr>
<?
  $sqlreq = 'SELECT
(SELECT COUNT(`Login`) FROM SQL_PREFIX_Users_Config) AS TotalUser,
(SELECT `DateLast` FROM SQL_PREFIX_Users_Config WHERE `Login` <> "'.$Joueur.'" ORDER BY `DateLast` DESC LIMIT 1) AS DateLastConnect,
(SELECT `Login` FROM SQL_PREFIX_Users_Config WHERE `Login` <> "'.$Joueur.'" ORDER BY `DateLast` DESC LIMIT 1) AS UserLastConnect,
(SELECT MAX(`DateCreated`) FROM SQL_PREFIX_Users_Config) AS DateLastCreate,
(SELECT `Login` FROM SQL_PREFIX_Users_Config ORDER BY `DateCreated` DESC LIMIT 1) AS UserLastCreate,
(SELECT COUNT(`ID`) FROM SQL_PREFIX_Modules_Template WHERE `Categorie` <> 4) AS TotalModules,
(SELECT COUNT(DISTINCT SQL_PREFIX_Modules_Users.Module_ID)
FROM SQL_PREFIX_Modules_Users
LEFT JOIN SQL_PREFIX_Modules_Template ON SQL_PREFIX_Modules_Users.Module_ID = SQL_PREFIX_Modules_Template.ID
LEFT JOIN SQL_PREFIX_Users_Config ON SQL_PREFIX_Modules_Users.Login = SQL_PREFIX_Users_Config.Login
WHERE SQL_PREFIX_Modules_Template.Categorie <> 4 AND SQL_PREFIX_Users_Config.CommerceType <= 1) AS TotalBuildModules';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $datas = mysql_fetch_array($mysql_result)
?>
  <tr><td>
Nombre de membres inscrits: <b><?=$datas['TotalUser']?></b><br />
Dernier membre inscrit: <b><?=$datas['UserLastCreate']?></b> le <b><?=$datas['DateLastCreate']?></b><br />
Dernière connexion: <b><?=$datas['UserLastConnect']?></b> le <b><?=$datas['DateLastConnect']?></b><br />
<br />
Nombre de schémas de modules: <b><?=$datas['TotalModules']?></b><br />
Nombre de modules différents vendus: <b><?=$datas['TotalBuildModules']?></b><br />
Vendus par l'Empire: <b><?=number_format($datas['TotalBuildModules'] / $datas['TotalModules'] * 100, 2)?>%</b> des modules totaux.<br />
  </td></tr></table>
<br />

  </table>
<br />
<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr><th colspan=3>Top 10 des vendeurs</th></tr>
  <tr align=center bgcolor=#333366 STYLE="font-weight:bold;"><td>Rang</td><td>Nombre de modules</td><td>Membres</td></tr>
<?
  $sqlreq = 'SELECT DISTINCT SQL_PREFIX_Modules_Users.Login, COUNT(`Module_ID`) AS TotalMade
FROM SQL_PREFIX_Modules_Users
LEFT JOIN SQL_PREFIX_Modules_Template ON SQL_PREFIX_Modules_Users.Module_ID = SQL_PREFIX_Modules_Template.ID
LEFT JOIN SQL_PREFIX_Users_Config ON SQL_PREFIX_Modules_Users.Login = SQL_PREFIX_Users_Config.Login
WHERE Categorie <> 4 AND SQL_PREFIX_Users_Config.CommerceType <= 1
GROUP BY SQL_PREFIX_Modules_Users.Login
ORDER BY TotalMade DESC
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
        
        echo('<tr valign=top align=center><td>'.$rang.($rang == 1 ? 'er' : 'ème').'</td><td>'.$datas['TotalMade'].'</td><td>');
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
  <tr><th colspan=2>Modules vendus que par un membre</th></tr>
  <tr align=center bgcolor=#333366 STYLE="font-weight:bold;"><td>Membres</td><td>Module(s)</td></tr>
<?
  $sqlreq = '
SELECT DISTINCT `ID`, `Nom`, SQL_PREFIX_Modules_Users.Login, `URLIcone`
FROM SQL_PREFIX_Modules_Template
LEFT JOIN SQL_PREFIX_Modules_Users ON SQL_PREFIX_Modules_Template.ID = SQL_PREFIX_Modules_Users.Module_ID
LEFT JOIN SQL_PREFIX_Users_Config ON SQL_PREFIX_Modules_Users.Login = SQL_PREFIX_Users_Config.Login
WHERE Categorie <> 4 AND SQL_PREFIX_Users_Config.CommerceType <= 1
GROUP BY `Nom`, `ID`, `URLIcone`
HAVING COUNT(`Module_ID`) = 1
ORDER BY SQL_PREFIX_Modules_Users.Login, `Nom`
  ';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $oldrang = '';
  while($datas = mysql_fetch_array($mysql_result)) {
    if($oldrang != $datas['Login']) {
      if(!empty($oldrang))
        echo("</td></tr>\n");
      
      $oldrang = $datas['Login'];
      
      echo('<tr valign=top><td><b>'.$datas['Login'].'</b></td><td>');
    }
    else
      echo('<br />');

    echo('<a href="template_view.php?viewid='.$datas['ID'].'">'.(!empty($datas['URLIcone']) ? '<img src="'.$datas['URLIcone'].'" border=0 align=absmiddle>' : '<img src="images/nopicture.png" border=0 align=absmiddle>').'&nbsp;<font color="#ffff00" weight=large>'.$datas['Nom'].'</font></a>');
  }
?>
  </td></tr></table>
<br />
<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr><th colspan=3>Top 10 des modules les plus vendus</th></tr>
  <tr align=center bgcolor=#333366 STYLE="font-weight:bold;"><td>Rang</td><td>Nombre de vendeur</td><td>Modules</td></tr>
<?
  $sqlreq = 'SELECT DISTINCT `ID`, `Nom`, COUNT(`Module_ID`) AS TotalMade, `URLIcone`
FROM SQL_PREFIX_Modules_Users
LEFT JOIN SQL_PREFIX_Modules_Template ON SQL_PREFIX_Modules_Users.Module_ID = SQL_PREFIX_Modules_Template.ID
LEFT JOIN SQL_PREFIX_Users_Config ON SQL_PREFIX_Modules_Users.Login = SQL_PREFIX_Users_Config.Login
WHERE Categorie <> 4 AND SQL_PREFIX_Users_Config.CommerceType <= 1
GROUP BY `Nom`, `URLIcone`, `ID`
ORDER BY TotalMade DESC, Nom';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $i = 0;
  $j = 1;
  $oldvalue = null;
  while(($datas = mysql_fetch_array($mysql_result)) && $j <= 10) {
    $i++;
    
    if($oldvalue != $datas['TotalMade']) {
      if(!is_null($oldvalue))
        echo('</td></tr>');
      echo('<tr valign=top><td>'.$j.($j == 1 ? 'er' : 'ème').'</td><td>'.$datas['TotalMade'].'</b> vendeur'.($datas['TotalMade'] > 1 ? 's' : '').'</td><td>');
      
      $oldvalue = $datas['TotalMade'];
      $j++;
      $i = 0;
    }
    
    if($i > 0)
      echo("<br>\n");
    echo('<a href="template_view.php?viewid='.$datas['ID'].'">'.(!empty($datas['URLIcone']) ? '<img src="'.$datas['URLIcone'].'" border=0 align=absmiddle>' : '<img src="images/nopicture.png" border=0 align=absmiddle>').'&nbsp;<font color="#ffff00" weight=large>'.$datas['Nom'].'</font></a>');
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