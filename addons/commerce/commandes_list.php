<?php                               
// Partie standard d'EU2de
require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();

// Déclaration variables
$erreur='';
$Joueur = $_SESSION['_login'];
require_once('cnh_fonctions.php');
Init_Addon();
  
// DEBUT CODE LIBRE
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_GESTION'))
    output::Boink('./index.php');

if(isset($_GET['livraisonMe'])) {
	// livraison effectuée
	$sqlq = 'SELECT `ID` FROM `SQL_PREFIX_modules_commandes` WHERE `ID` = \''.intval($_GET['livraisonMe']).'\' AND `DateLivraison` IS NULL AND `Login` = \''.$Joueur.'\'';
	$mysql_result = DataEngine::sql($sqlq);
	if (mysql_num_rows($mysql_result) != 1) {
		$erreur .= 'Pas de résultat retourné pour '.$sqlq;
	} else {
		// UPDATE mytable SET used=1 WHERE id < 10
		$mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_modules_commandes` SET `DateLivraison` = now() WHERE `ID` = \''.intval($_GET['livraisonMe']).'\'') or die(mysql_error());
	}
} elseif(isset($_GET['paiementMe'])) {
	// paiement effectué
	$sqlq = 'SELECT `ID` FROM `SQL_PREFIX_modules_commandes` WHERE `ID` = \''.intval($_GET['paiementMe']).'\' AND `DatePaiement` IS NULL AND `Login` = \''.$Joueur.'\'';
	$mysql_result = DataEngine::sql($sqlq);
	if (mysql_num_rows($mysql_result) != 1) {
		$erreur .= 'Pas de résultat retourné pour '.$sqlq;
	} else {
		// UPDATE mytable SET used=1 WHERE id < 10
		$mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_modules_commandes` SET `DatePaiement` = now() WHERE `ID` = \''.intval($_GET['paiementMe']).'\'') or die(mysql_error());
	}
}

if(isset($_GET['livraisonHim'])) {
	// livraison effectuée
	$sqlq = 'SELECT `ID` FROM `SQL_PREFIX_modules_commandes` WHERE `ID` = \''.intval($_GET['livraisonHim']).'\' AND `DateLivraison` IS NULL AND `LoginV` = \''.$Joueur.'\'';
	$mysql_result = DataEngine::sql($sqlq);
	if (mysql_num_rows($mysql_result) != 1) {
		$erreur .= 'Pas de résultat retourné pour '.$sqlq;
	} else {
		// UPDATE mytable SET used=1 WHERE id < 10
		$mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_modules_commandes` SET `DateLivraison` = now() WHERE `ID` = \''.intval($_GET['livraisonHim']).'\'') or die(mysql_error());
	}
} elseif(isset($_GET['paiementHim'])) {
	// paiement effectué
	$sqlq = 'SELECT `ID` FROM `SQL_PREFIX_modules_commandes` WHERE `ID` = \''.intval($_GET['paiementHim']).'\' AND `DatePaiement` IS NULL AND `LoginV` = \''.$Joueur.'\'';
	$mysql_result = DataEngine::sql($sqlq);
	if (mysql_num_rows($mysql_result) != 1) {
		$erreur .= 'Pas de résultat retourné pour '.$sqlq;
	} else {
		// UPDATE mytable SET used=1 WHERE id < 10
		$mysql_result = DataEngine::sql('UPDATE `SQL_PREFIX_modules_commandes` SET `DatePaiement` = now() WHERE `ID` = \''.intval($_GET['paiementHim']).'\'') or die(mysql_error());
	}
}

if(isset($_POST['submitMe'])) {
    if($_POST['submitMe'] == 'Lister') {
		$_SESSION['cnhAffichageMe'] = $_POST['cnhAffichageMe'];
    }
}

if(isset($_POST['submitHim'])) {
    if($_POST['submitHim'] == 'Lister') {
		$_SESSION['cnhAffichageHim'] = $_POST['cnhAffichageHim'];
    }
}
  
$tnbcol = 7;
  
// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type='text/javascript'>  

function redirection(IDitem,TypeOpe,TextOp,TextOK,TextCancel)
{
   if (confirm(TextOp+"\n[Ok] = "+TextOK+"\n[Annuler] = "+TextCancel)) 
   {
      document.location.href='?'+TypeOpe+'='+IDitem;
   }
   else
   {
      document.location.href='#';
   }
}
</script>
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->
<p align='center'><font color=red><?php echo $erreur; ?></font></p><br />
<?php
$cnhAffichageMe = array();
$cnhAffichageMe[] = 'En attente de livraison ou a payer';
$cnhAffichageMe[] = 'En attente de livraison uniquement';
$cnhAffichageMe[] = 'A payer uniquement';
$cnhAffichageMe[] = 'Toutes';

$titlecat = 'Vos commandes';

switch($_SESSION['cnhAffichageMe']) {
	case 1:
		$filtretable = '`DateLivraison` IS NULL';
		  
		$titlecat = 'Vos commandes en attente de livraison';
		break;
	case 2:
		$filtretable = '`DatePaiement` IS NULL';
		
		$titlecat = 'Vos commandes que vous devez payer';
		break;
	case 3:
		$filtretable = '1';
		  
		$titlecat = 'Toutes vos commandes';
		break;
	default:
		$filtretable = '`DateLivraison` IS NULL OR `DatePaiement` IS NULL';
		
		$titlecat = 'Vos commandes en attente de livraison ou a payer';
		break;
}
?>
<table border="1" align="center" cellpadding="3" cellspacing="0" >
<tr class="text_center color_header"><td colspan=<?php echo $tnbcol; ?>><?php echo $titlecat; ?><BR><form action="commandes_list.php" name="form" method="post">Affichage: <select class="color_row0" class="color_row0" name='cnhAffichageMe'><?php DisplayListSelect("", $cnhAffichageMe, $_SESSION["cnhAffichageMe"]); ?></select> / <input class="color_row0" type="submit" name="submitMe" value="Lister"></form></td></tr>
<tr class="text_center color_header"><td>Date</td><td>Fournisseur</td><td>Commande</td><td>Paiement</td><td>Date Paiement</td><td>Date Livraison</td><td>Coordonnées</td></tr>
<?php
  $sqlreq = '
  SELECT `ID`, `Login`, `LoginV`, `Items`, `TypePaiement`, `Paiement`, `DateCreated`, `DateLivraison`, `DatePaiement`, `CoordLivraison` FROM `SQL_PREFIX_modules_commandes` 
  WHERE ('.$filtretable.') AND `Login` = \''.$Joueur.'\' ORDER BY `DateCreated`';

// pDebug($sqlreq);
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
    
  while($ligne = mysql_fetch_array($mysql_result))
  {
    // Début de ligne      
    echo('<tr class="text_center color_row0">');

	// echo("<a href='template_list.php".$defquerystring."#ID".$ligne["ID"]."'><img src='images/thumbs_no_faded.png' align=middle border=0></a> <img src='images/thumbs_yes.png' align=middle border=0>");
	// <a href='template_view.php?viewid=".$ligne["ID"]."'><font color='#FF6600'>".(!empty($ligne["Abreviation"]) ? $ligne["Abreviation"] : $ligne["Nom"])."</font></a>
	// <input class="color_row0" type="button" value="Enregistrer" onclick="location.href=../asp/PRaces.asp">
	
    // Colonne 1 - Date
    echo('<td>'.$ligne['DateCreated'].'</td>');
    // Colonne 2 - Fournisseur
    echo('<td>'.$ligne['LoginV'].'</td>');
    // Colonne 3 - Commande
    echo('<td>'.$ligne['Items'].'</td>');
    // Colonne 4 - Paiement
    echo('<td>');
	// $ligne['Paiement'] contient 'RessourcesNBTotal' ou 'ressourcesitems' selon le mode de paiement choisi
	if (substr_count($ligne['Paiement'],'RessourcesNBTotal') > 0) {
		$paiement = str_replace('RessourcesNBTotal', 'Total', $ligne['Paiement']);
	}
	if (substr_count($ligne['Paiement'],'ressourcesitems') > 0) {
		$paiement = str_replace('ressourcesitems:', '', $ligne['Paiement']);
	}
	echo ('Type de paiement: '.html_entity_decode($ligne['TypePaiement'],ENT_QUOTES).' <BR> '.$paiement);
	echo('</td>');
    // Colonne 5 - Date Paiement
    echo('<td>');
	if (is_null($ligne['DatePaiement'])) {
		echo('<input class="color_row0" type="button" value="Paiement effectué" onclick="javascript:redirection('.$ligne['ID'].',\'paiementMe\',\'Paiement de la commande '.$ligne['Items'].'\',\'Vous avez payé\',\'Vous n avez pas payé\')">');
	} else {
		echo($ligne['DatePaiement']);
	}
	echo('</td>');
    // Colonne 6 - Date Livraison
    echo('<td>');
	if (is_null($ligne['DateLivraison'])) {
		echo('En attente de livraison');
		} else {
		echo($ligne['DateLivraison']);
	}
	echo('</td>');
    // Colonne 7- Coordonnées
    echo('<td>'.$ligne['CoordLivraison'].'</td>');
    // Fin de ligne
    echo('</tr>\n');
  }

?>

</table>


<!-- Code pour les ventes que vous faites !-->
<?php
$cnhAffichageHim = array();
$cnhAffichageHim[] = 'A livrer ou en attente de paiement';
$cnhAffichageHim[] = 'A livrer uniquement';
$cnhAffichageHim[] = 'En attente de paiement uniquement';
$cnhAffichageHim[] = 'Toutes';
$titlecat = 'Vos modules';

switch($_SESSION['cnhAffichageHim']) {
	case 1:
		$filtretablehim = '`DateLivraison` IS NULL';
		$titlecathim = 'Vos ventes en attente de livraison';
		break;
	case 2:
		$filtretablehim = '`DatePaiement` IS NULL';
		$titlecathim = 'Vos ventes que vous devez payer';
		break;
	case 3:
		$filtretablehim = '1';
		$titlecathim = 'Toutes vos ventes';
		break;
	default:
		$filtretablehim = '`DateLivraison` IS NULL OR `DatePaiement` IS NULL';
		$titlecathim = 'Vos ventes en attente de livraison ou a payer';
		break;
}
?>
<br /><hr><br />
<table border="1" align="center" cellpadding="3" cellspacing="0">

<tr class="text_center color_header"><td colspan=<?php echo $tnbcol; ?>><?php echo $titlecathim; ?><BR><form ACTION="commandes_list.php" name="formHim" method="post">Affichage: <select class="color_row0" class="color_row0" name='cnhAffichageHim'><?php DisplayListSelect("", $cnhAffichageHim, $_SESSION["cnhAffichageHim"]); ?></select> / <input class="color_row0" type="submit" name="submitHim" value="Lister"></form></td></tr>
<tr class="text_center color_header"><td>Date</td><td>Client</td><td>Commande</td><td>Paiement</td><td>Date Paiement</td><td>Date Livraison</td><td>Coordonnées</td></tr>
<?php
  $sqlreq = '
  SELECT `ID`, `Login`, `LoginV`, `Items`, `TypePaiement`, `Paiement`, `DateCreated`, `DateLivraison`, `DatePaiement`, `CoordLivraison` FROM `SQL_PREFIX_modules_commandes` 
  WHERE ('.$filtretablehim.') AND `LoginV` = \''.$Joueur.'\' ORDER BY `DateCreated`';

// pDebug($sqlreq);
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
    
  while($ligne = mysql_fetch_array($mysql_result))
  {
    // Début de ligne      
    echo('<tr class="text_center color_row0">');
    // Colonne 1 - Date
    echo('<td>'.$ligne['DateCreated'].'</td>');
    // Colonne 2 - Fournisseur
    echo('<td>'.$ligne['Login'].'</td>');
    // Colonne 3 - Commande
    echo('<td>'.$ligne['Items'].'</td>');
    // Colonne 4 - Paiement
    echo('<td>');
	
	// $ligne['Paiement'] contient 'RessourcesNBTotal' ou 'ressourcesitems' selon le mode de paiement choisi
	if (substr_count($ligne['Paiement'],'RessourcesNBTotal') > 0) {
		$paiement = str_replace('RessourcesNBTotal', 'Total', $ligne['Paiement']);
	}
	if (substr_count($ligne['Paiement'],'ressourcesitems') > 0) {
		$paiement = str_replace('ressourcesitems:', '', $ligne['Paiement']);
	}
	echo ('Type de paiement: '.html_entity_decode($ligne['TypePaiement'],ENT_QUOTES).' <BR> '.$paiement);
	echo('</td>');
    // Colonne 5 - Date Paiement
    echo('<td>');
	if (is_null($ligne['DatePaiement'])) {
		if($_SESSION['_login'] == $ligne['LoginV']) {
			echo('<input class="color_row0" type="button" value="Paiement effectué" onclick="javascript:redirection('.$ligne["ID"].',\'paiementHim\',\'Paiement de la commande '.$ligne['Items'].'\',\'Vous avez été payé\',\'Vous n avez pas été payé\')">');
		} else {
			echo('En attente de paiement');
		}
	} else {
		echo($ligne['DatePaiement']);
	}
	echo('</td>');
    // Colonne 6 - Date Livraison
    echo('<td>');
	if (is_null($ligne['DateLivraison'])) {
		echo('<input class="color_row0" type="button" value="Livraison effectuée" onclick="javascript:redirection('.$ligne["ID"].',\'livraisonHim\',\'Livraison de la commande '.$ligne['Items'].'\',\'Vous avez livré\',\'Vous n avez pas livré\')">');
	} else {
		echo($ligne['DateLivraison']);
	}
	echo('</td>');
    // Colonne 7- Coordonnées
    echo('<td>'.$ligne['CoordLivraison'].'</td>');
    // Fin de ligne
    echo("</tr>\n");
  }

?>

</table>

<!-- FIN CODE LIBRE -->
</BODY></HTML>

<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();