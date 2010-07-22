<?php
// Partie standard d'EU2de
  define("CHECK_LOGIN", false);	// désactive le besoin d'avoir un compte pour la page
  require_once("../../init.php");
  require_once(INCLUDE_PATH."Script.php");
  require_once(TEMPLATE_PATH."sample.tpl.php");
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  require_once("cnh_fonctions.php");
  $Joueur = $_SESSION["_login"];
  $title = "CNH Space Fight Statistiques";
  
// DEBUT CODE LIBRE
//  DataEngine::CheckPermsOrDie(AXX_GUEST);

  if(isset($_POST['situation']) && isset($_POST['log'])) {
    $sit = stripslashes(trim($_POST['situation']));
    $log = stripslashes(trim($_POST['log']));
  
    // Listage des vaisseaux

    ExtractVessels($sit, $vallie, $vennemi);
    $_SESSION['CNHFS_ALLIE'] = $vallie;
    $_SESSION['CNHFS_ENNEMI'] = $vennemi;
    
    $actions = ExtractLog($log);
    $_SESSION['CNHFS_ACTIONS'] = $actions;

    $records = ExtractStats();
    $_SESSION['CNHFS_RECORDS'] = $records;
    
    $menu = "vessels";
  }
  elseif(isset($_GET['menu']))
  {
    $vallie = $_SESSION['CNHFS_ALLIE'];
    $vennemi = $_SESSION['CNHFS_ENNEMI'];
    $actions = $_SESSION['CNHFS_ACTIONS'];
    $records = $_SESSION['CNHFS_RECORDS']; 
    
    $menu = $_GET['menu'];
  }
  else
    $menu = "top";
    
  if($menu == "top")  
  {
    $_SESSION['CNHFS_ALLIE'] = null;
    $_SESSION['CNHFS_ENNEMI'] = null;
    $_SESSION['CNHFS_ACTIONS'] = null;
    $_SESSION['CNHFS_RECORDS'] = null;
  }
  elseif($menu == "vessel")
  {
    if(!isset($_GET['id']))
      $menu = 'vessels';
  }

  $linkmenu = '<center>[&nbsp;<a href="?menu=vessels">Liste&nbsp;vaisseaux</a>&nbsp;] - [&nbsp;<a href="?menu=actions">Liste&nbsp;des&nbsp;Actions</a>&nbsp;] - [&nbsp;<a href="?menu=records">Records</a>&nbsp;] - [&nbsp;<a href="?menu=top">Autre&nbsp;combat</a>&nbsp;]</center>';

// FIN CODE LIBRE  
?>

<HTML>
  <title><?=$title?></title>
<HEAD>
  <link rel="stylesheet" type="text/css" href="/addons/cnh_fightstats/EU2DE.css" media="screen" />
  <link href="cnh_addon.css" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<!-- DEBUT CODE LIBRE -->
<font color=#ffffff>

<h1><?=$title?><br /></h1>

<?
  if($menu == "vessels") {
    // ----------------------------------------------------------------------------------------
    // 
    // MENU: VESSELS
    // 
    // ----------------------------------------------------------------------------------------

    echo($linkmenu);
    echo('<br />');
?>

<table border=0 align=center cellpadding=5 cellspacing=0>
<tr align=center><td><h2>Vaisseaux alliés</h2></td><td><h2>Vaisseaux ennemis</h2></td></tr>
<tr align=center valign=top><td><? DisplayVessels($vallie); ?></td><td><? DisplayVessels($vennemi); ?></td></tr>
</table>

<?
  }
  elseif($menu == "actions") {
    // ----------------------------------------------------------------------------------------
    // 
    // MENU: ACTIONS
    // 
    // ----------------------------------------------------------------------------------------

    echo($linkmenu);
    echo('<br />');

    DisplayActions($actions);

  }
  elseif($menu == "vessel") {
    // ----------------------------------------------------------------------------------------
    // 
    // MENU: VESSEL
    // 
    // ----------------------------------------------------------------------------------------

    echo($linkmenu);
    echo('<br />');

    $vesselid = urldecode($_GET['id']);

    $brec = &$records[$vesselid]; 
    for($i = 0; $i < sizeof($records); $i++) {
      if($records[$i]['Nom'] == $vesselid) {
        $brec = &$records[$i];
        break;
      }
    }

    if($brec['Side'])
      $bves = &$vallie;
    else
      $bves = &$vennemi;
      
    for($i = 0; $i < sizeof($bves); $i++) {
      if($bves[$i]['Nom'] == $vesselid) {
        $bves = &$bves[$i];
        break;
      }
    }

    // Titre

    echo('<center><h2><font color='.GetForceColor($brec['Side']).'>'.$vesselid.'</font></h2></center>');


    // Caractéristiques

    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=2>Caractéristiques</th></tr>');

    echo('<tr><td>Alignement</td><td><font color=');
    if($brec['Side'])
      echo('green>Allié');
    else
      echo('red>Ennemi');
    echo('</font></td></tr>');

    if($brec['Duplicate'] > 1)
      echo("<tr><td>Nombre d'exemplaires</td><td align=right>".number_format($brec['Duplicate'],0,'.',"'")." x</td></tr>");

    echo('<tr><td>Coque</td><td>'.number_format($bves['CoqueStart'],0,'.',"'")." / ".number_format($bves['CoqueFull'],0,'.',"'").'</td></tr>');
    echo('<tr><td>Bouclier</td><td>'.number_format($bves['ShieldStart'],0,'.',"'")." / ".number_format($bves['ShieldFull'],0,'.',"'").'</td></tr>');

    echo('</table><br />');    


    // Statistiques
    
    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=2>Statistiques</th></tr>');

    echo("<tr><td>Nombre total d'actions</td><td align=right>".number_format($brec['TotActions'],0,'.',"'").'</td></tr>');

    echo("<tr><td>Nombre de tir réussis</td><td align=right>".number_format($brec['TotHit'],0,'.',"'").'</td></tr>');
    echo("<tr><td>Moyenne de tir réussis</td><td align=right>".number_format($brec['MoyHit'],2,'.',"'").'%</td></tr>');
    echo("<tr><td>Nombre d'échecs</td><td align=right>".number_format($brec['TotMiss'],0,'.',"'").'</td></tr>');
    echo("<tr><td>Moyenne de tir râtés</td><td align=right>".number_format($brec['MoyMiss'],2,'.',"'").'%</td></tr>');

    if($brec['MaxCoque'] > 0) {
      echo('<tr><td colspan=2 align=center><b>Dégâts sur la Coque</b></td></tr>');
      echo('<tr><td>Nombre de tirs</td><td align=right>'.number_format($brec['TotHit_C'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Total des dégâts</td><td align=right>'.number_format($brec['TotCoque'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Maximum effectué</td><td align=right>'.number_format($brec['MaxCoque'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Moyenne des dégâts</td><td align=right>'.number_format($brec['MoyCoque'],0,'.',"'").'</td></tr>');
    }

    if($brec['MaxBouclier'] > 0) {
      echo('<tr><td colspan=2 align=center><b>Dégâts sur le Bouclier</b></td></tr>');
      echo('<tr><td>Nombre de tirs</td><td align=right>'.number_format($brec['TotHit_B'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Total des dégâts</td><td align=right>'.number_format($brec['TotBouclier'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Maximum effectué</td><td align=right>'.number_format($brec['MaxBouclier'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Moyenne des dégâts</td><td align=right>'.number_format($brec['MoyBouclier'],0,'.',"'").'</td></tr>');
    }
    
    if($brec['MaxIon'] > 0) {
      echo('<tr><td colspan=2 align=center><b>Dégâts en Ion</b></td></tr>');
      echo('<tr><td>Nombre de tirs</td><td align=right>'.number_format($brec['TotHit_I'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Total des dégâts</td><td align=right>'.number_format($brec['TotIon'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Maximum effectué</td><td align=right>'.number_format($brec['MaxIon'],0,'.',"'").'</td></tr>');
      echo('<tr><td>Moyenne des dégâts</td><td align=right>'.number_format($brec['MoyIon'],0,'.',"'").'</td></tr>');
    }
    
    echo('</table><br />');    


    // Actions
     
    DisplayActions($actions, $vesselid);

  }
  elseif($menu == "records") {
    // ----------------------------------------------------------------------------------------
    // 
    // MENU: RECORDS
    // 
    // ----------------------------------------------------------------------------------------

    echo($linkmenu);
    echo('<br><center><i>Nombre de sous-rang affichés:</i> [&nbsp;<a href="?menu=records&nrang=5">5&nbsp;lignes</a>&nbsp;] - [&nbsp;<a href="?menu=records&nrang=10">10&nbsp;lignes</a>&nbsp;] - [&nbsp;<a href="?menu=records&nrang=25">25&nbsp;lignes</a>&nbsp;] - [&nbsp;<a href="?menu=records&nrang=-1">Toutes</a>&nbsp;]</center>');
    echo('<br />');

    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=3 align=center>Records de dégâts</th></tr>');  
    echo('<tr align=center>');
    echo("<th>Dans la Coque</th>");  
    echo("<th>Dans le Bouclier</th>");  
    echo("<th>En Ion</h2>");  
    echo('</tr><tr valign=top>');
  
    echo('<td>');
    DisplayRecord('MaxCoque');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('MaxBouclier');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('MaxIon');
    echo("</td></tr></table>\n");
  
  
    echo('<br />');
    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=3 align=center>Totaux des dégâts</th></tr>');  
    echo('<tr align=center>');
    echo("<th>Dans la Coque</th>");  
    echo("<th>Dans le Bouclier</th>");  
    echo("<th>En Ion</h2>");  
    echo('</tr><tr valign=top>');
  
    echo('<td>');
    DisplayRecord('TotCoque');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('TotBouclier');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('TotIon');
    echo("</td></tr></table>\n");
  
  
    echo('<br />');
    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=3 align=center>Moyenne des dégâts</th></tr>');  
    echo('<tr align=center>');
    echo("<th>Dans la Coque</th>");  
    echo("<th>Dans le Bouclier</th>");  
    echo("<th>En Ion</h2>");  
    echo('</tr><tr valign=top>');
  
    echo('<td>');
    DisplayRecord('MoyCoque');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('MoyBouclier');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('MoyIon');
    echo("</td></tr></table>\n");
  
  
    echo('<br />');
    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=2 align=center>Précision</th></tr>');  
    echo('<tr align=center>');
    echo("<th>Total des tirs réussis</th>");  
    echo("<th>Total des tirs ratés</th>");  
    echo('</tr><tr valign=top>');
  
    echo('<td>');
    DisplayRecord('TotHit', false, ' tirs réussis par');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('TotMiss', false, ' tirs ratés par');
    echo("</td></tr>");
  
    echo('<tr align=center>');
    echo("<th>Moyenne des tirs réussis</th>");  
    echo("<th>Moyenne des tirs ratés</th>");  
    echo('</tr><tr valign=top>');
  
    echo('<td>');
    DisplayRecord('MoyHit', true, ' des tirs réussis par');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('MoyMiss', true, ' des tirs ratés par');
    echo("</td></tr></table>\n");
  
  
    echo('<br />');
    echo('<table border=1 align=center cellpadding=2 cellspacing=0>');
    echo('<tr><th colspan=2 align=center>Divers</th></tr>');  
    echo('<tr align=center>');
    echo("<th>Total des actions</th>");  
    echo("<th>Vaisseaux au même nom</th>");  
    echo('</tr><tr valign=top>');
  
    echo('<td>');
    DisplayRecord('TotActions');
    echo("</td>\n");
  
    echo('<td>');
    DisplayRecord('Duplicate', false, 'x sous le nom');
    echo("</td></tr></table>\n");

    echo('<br />');

  }
  else {
    // ----------------------------------------------------------------------------------------
    // 
    // MENU: TOP
    // 
    // ----------------------------------------------------------------------------------------
?>
<p><font color="#dddddd"><b>Analyseur de logs de combat pour Empire Universe 2</b></font></p><br />

<h2>Saisie du log de combat</h2>

<p><form method="post">
Copiez et collez le log du combat que vous venez de mener.<br />
<br />
Onglet Interactif (Tout copier):<br />
<textarea cols="80" rows="5" name="situation"></textarea>
<br />
Onglet Text Only (Tout copier):<br />
<textarea cols="80" rows="5" name="log"></textarea>
<br />
<input type=submit value="Analyser" />
</form></p>

<br />
<h2>Log version</h2>
<font color='#ff0000'>Bêta version</font><br />

<p><b><?=$version_addon?></b>
<ul><li>Permet d'avoir des informations sur un seul vaisseau.</li>
<li>Plus grande lisibilité dans l'affichage.</li>
<li>Permet de régler la précision des records.</li></ul>

<b>Versions précédentes</b>
<ul><li>Validé uniquement avec Firefox et Internet Explorer.</li>
<li>N'analyses que les combats spatiaux.</li>
<li>Ne fait pas encore la différence entre les différentes flottes (gère qu'allié et ennemi).</li>
<li>Au niveau statistique, ne gère pas les arrivées des flottes en cours de combat.</li>
<li>Gère les flottes avec vaisseaux du même nom.</li></ul>
</p>

<?
  } 
?>

<br /><hr /><div align=center><font color="#dddddd" size="-1">(c)Yelm, <a href="http://cnh.yelmos.com" target=_blank>Confédération Néo-Helvétique</a>, Serveur Australis.</div>

</font>
<!-- FIN CODE LIBRE -->
</BODY></HTML>
