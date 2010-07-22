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
	
  require_once('shop.class.php');

  $basket = new BasketClass();

  $commandedef = false;

  $errback = false;
  if(isset($_POST['submit'])) {
    if($_POST['submit'] == 'Suivant >>') {
      $errback = true;
      if($basket->CheckoutRecup(true))
        $errback = !$basket->BasketVar(true);
      if(!$errback)
        $_SESSION['checkout'] = $basket->GetCheckoutArray();
    }
    elseif($_POST['submit'] == "<< Liste vendeurs")
      $errback = true;
    elseif($_POST['submit'] == "Envoyer commande") {
      $basket->BasketVar(true);
      $basket->CheckoutVar(true);
      $commandedef = true;
      /*
        CHECKOUTVAR
      */
    }
    elseif($_POST['submit'] == "<< Précédent") {
      header('Location: template_list.php');
      exit;
    }
  }

  if($errback) {
    header('Location: shop_checkout.php?action=back');
    exit;
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

<?php
  if($commandedef) {
    $vlist = $basket->VendorSelSort();	
	// echo("<center><font color=#ff0000>".nl2br(print_r($vlist,true))."</font></center><br>");

    $oldvendor = '';
    $nvendor = 0;
	$coordplanete = $_POST['coordplanete'];
	$acheteur = $_SESSION['_login'];
    for($i = 0; $i < $vlist['Total']; $i++) {
		$paiementme = intval($_POST['PaiementMe_'.$i]);
		$id = $vlist[$i]['Index'];	
		// Vendeur
		$vendeur = $vlist[$i]['Login'];
		// Article et Quantité
		$commande = $basket->items[$id]['NB'].'x'.$basket->items[$id]['Nom'];
		// ressources 1 pour 1
		$ressourcesitems = '';
		foreach($cnhMineraisName as $value) {
			if($vlist[$i]['RessourcesNB'][$value] > 0) {
				if($ressourcesitems != '') { 
					$ressourcesitems .= ','.$value.'='.DataEngine::format_number($vlist[$i]['RessourcesNB'][$value]);
				} else {
					$ressourcesitems .= $value.'='.DataEngine::format_number($vlist[$i]['RessourcesNB'][$value]);
				}
			}
		 }				
		// ressources total:
		$RessourcesNBTotal = $vlist[$i]['RessourcesNBTotal'];
		$typepaiement = '';
		// paiement choisi
		if ($paiementme & UP_CREDITS) {
			$typepaiement = '<img src="images/credits.gif" />&nbsp;Crédits';
			$paiementitem = 'RessourcesNBTotal:'.$RessourcesNBTotal;
		} elseif ($paiementme & UP_EXACT) {
			$typepaiement = '<img src="images/ressources.png" />&nbsp;Ressources utilisées';
			$paiementitem = 'ressourcesitems:'.$ressourcesitems;
		} else {
			$nm = 0;
			for($j = 0; $j < sizeof($cnhMineraisName); $j++)
				if($paiementbit & pow(2, $j + 3)) $nm++;
		
			if($nm == $j){
				$typepaiement = '<img src="images/ressources.png" />&nbsp;Toute ressource';
				$paiementitem = 'RessourcesNBTotal:'.$RessourcesNBTotal;
			} else {
				for($j = 0; $j < sizeof($cnhMineraisName); $j++) {
					if($paiementme & pow(2, $j + 3)) {
						$typepaiement = '<img src='.IMAGES_URL.$cnhMineraisName[$j].'.png />&nbsp;'.$cnhMineraisName[$j];
					}
				}
				$paiementitem = 'RessourcesNBTotal:'.$RessourcesNBTotal;
			}

		}
			
		$typepaiement = htmlentities($typepaiement, ENT_QUOTES);
		// $paiementitem = "RessourcesNBTotal:".$RessourcesNBTotal.";ressourcesitems:".$ressourcesitems;
        $query    = 'INSERT INTO SQL_PREFIX_modules_commandes (Login,LoginV,Items,TypePaiement,Paiement,DateCreated,CoordLivraison) ';
        $query   .= 'VALUES (\''.$acheteur.'\',\''.$vendeur.'\',\''.addslashes($commande).'\',\''.$typepaiement.'\',\''.$paiementitem.'\',now(),\''.$coordplanete.'\')';
		// echo("<center><font color=blue>DEBUG commande :<br>");
		// echo(nl2br(print_r($_POST, true)));
		// echo("paiementme:".$paiementme."<br>");
		// echo("acheteur:".$acheteur."<BR>");
		// echo("Livraison:".$coordplanete."<BR>");
		// echo("vendeur:".$vendeur."<BR>");
		// echo("commande:".$commande."<BR>");
		// echo("typepaiement:".$typepaiement."<BR>");
		// echo("ressourcesitems:".$ressourcesitems."<BR>");
		// echo("RessourcesNBTotal:".$RessourcesNBTotal."<BR>");
        // echo ("requete sql:".$query."<br>");
		// echo("</font></center><br>");
		DataEngine::sql($query);
	}
    echo("<center><font color=#ff0000><h1>Commande enregistrée</h1><br>
    Vous pourrez la suivre dans la partie <a href='commandes_list.php'>Gestion</a></font></center>");
    
    unset($_SESSION['checkout']);
    unset($_SESSION['basket']);
  }
  else
  {
    $vlist = $basket->VendorSelSort();
	$activecommande = false;

	$mysql_result = DataEngine::sql('SELECT planet0, coord0, planet1, coord1, planet2, coord2, planet3, coord3, planet4, coord4 FROM SQL_PREFIX_ownuniverse WHERE `UTILISATEUR`=\''.$Joueur.'\'') or die(mysql_error());
    $j = 0;
	$valider_form_cond='';
	$radiolist = '';
    if(($ownplanetes = mysql_fetch_array($mysql_result))) {
      for($i = 0; $ownplanetes['planet'.$i] != '' && $i < 5; $i++) {
        if($i > 0) {
			$radiolist .= '<br>';
			$valider_form_cond .= ' && ';
		}
        
		$radiolist .= '<input class="color_row0" name="coordplanete" value="'.$ownplanetes['coord'.$i].'" type="radio"> '.$ownplanetes['planet'.$i].' aux coordonnées '.$ownplanetes['coord'.$i];
		$valider_form_cond .= '(document.forms["formMe"].coordplanete['.$i.'].checked==false)';
		$j++;
      }
    }
    
	// PaiementMe_
	$valid_paiement_form = '';
	for($com = 0; $com < $vlist['Total']; $com++) {
		$valider_paiement .= <<<EOF
function valider_paiement_{$com}()
{
	var radioLength = document.forms["formMe"].PaiementMe_{$com}.length;
	if(radioLength == undefined)
		if(document.forms["formMe"].PaiementMe_{$com}.checked)
			return true;
		else
			return false;
	for(i = 0; i < radioLength; i++){
		if(document.forms["formMe"].PaiementMe_{$com}[i].checked) return true;
	}
	return false;
}

EOF;
		if ($com > 0)
			$valid_paiement_form .= ' && ';
		$valid_paiement_form .= '(valider_paiement_'.$com.'()==true)';
	}

    if($j == 0) {
		$radiolist = "<font color=red><b>Vous n'avez pas saisi vos informations dans la partie Ma fiche => Production, veuillez le faire à la page <a href=\"".ROOT_URL."ownuniverse.php\" target=\"_blank\">Production</a>.</b></font>";
	} else {
		$activecommande = true;
		$outjs = <<<EOF
{$valider_paiement}

function valider_formulaire()
{
    if ({$valider_form_cond})
    {
         alert("Vous n avez pas sélectionné de coordonnées pour la livraison");
         return false;
    } else {
		if ({$valid_paiement_form}) {
			return true;
		} else {
			alert("Vous n avez pas sélectionné de mode de paiement");
			return false;
		}
	}
}

EOF;
	}
	if ($activecommande) {
		$defaultbutton = '<input class="color_row0" type="submit" name="submit" value="&lt;&lt; Liste vendeurs"> <input class="color_row0" type="submit" name="submit" value="Envoyer commande" onClick="return valider_formulaire();">'; 
	} else {
		$defaultbutton = '<input class="color_row0" type="submit" name="submit" value="&lt;&lt; Liste vendeurs">'; 
	}

?>

<script type='text/javascript'>
<?php echo $outjs; ?>
</script>

<form name="formMe" method="post">
<table border="1" align="center" cellpadding="3" cellspacing="0">
<tr class="text_center color_bigheader"><th colspan=3>Livraison</th></tr>
<tr class="color_row0"><td colspan=3 align=left>

<?php echo $radiolist; ?>
</td></tr>
<tr class="text_center color_header"><th>Vendeurs</th><th>Articles</th><th>Total à payer</th></tr>
<tr class="text_center color_row0"><td colspan=3 align=right><?php echo $defaultbutton; ?></td></tr>
<?php
    $vlist = $basket->VendorSelSort();
    $oldvendor = '';
    $nvendor = 0;
    
    for($i = 0; $i < $vlist['Total']; $i++) {
      $id = $vlist[$i]['Index'];
  
      // Début de ligne  
      echo('<tr class="text_center color_row0" valign=top>');
      
      // Vendeur
      if($oldvendor != $vlist[$i]['Login']) {
        echo('<td rowspan='.($vlist[$i]['Total'] + 1).'><b>'.$vlist[$i]['Login'].'</b><br>');
        
        if($vlist[$i]['Modifier'] != 0) {
          echo('<br><font size=-1>');
          if($vlist[$i]['Modifier'] > 0)
            echo('Surtaxe&nbsp;: <font color=#ff0000>+');
          elseif($vlist[$i]['Modifier'] < 0)
            echo('Rabais&nbsp;: <font color=#00ff00>');
          echo($vlist[$i]["Modifier"].'%</font></font><br>');
        }

        echo('<br><font size=-1><b>Paiement choisi&nbsp;:</b><br>');
		
		// echo('<input class="color_row0" type="checkbox" name="PaiementMe_'.$i.'" '.$vlist[$i]["PaiementMe"] & UP_CREDITS ? "checked " : "".'/>')
        echo($basket->StringPaiements($vlist[$i]["Paiement"],'PaiementMe_'.$i , "<br>").'</font>');
        
        echo('</td>');
        
        $oldvendor = $vlist[$i]['Login'];
        $oldi = $i;
        $nvendor++;
      }
      
      // Article et Quantité
      echo('<td align=center>'.$basket->items[$id]['NB'].'x <b>'.$basket->items[$id]['Nom'].'</b></td>');
// $sortie=nl2br(print_r($basket,true));
// pDebug($sortie);
      
      // Total à payer par article
      echo("<td><table border=0 width=100% cellpadding=0 cellspacing=0>");
      
      foreach($cnhMineraisName as $value) {
        if($vlist[$i]['RessourcesNB'][$value] > 0)
          echo('<tr class="color_row0"><td><img src='.IMAGES_URL.$value.'.png>&nbsp;'.$value.'</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($vlist[$i]['RessourcesNB'][$value]).'</td></tr>');
      }
      
      echo('<tr class="color_row0"><td colspan=3><hr size=1></td></tr><tr class="color_row0" STYLE="font-weight:bold;"><td><img src="images/ressources.png" />&nbsp;TOTAL</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($vlist[$i]["RessourcesNBTotal"])."</td></tr>");
      echo("</table></td>");
      
      $nex = false;
      if($i + 1 > $vlist['Total'])
        $nex = true;
      elseif($oldvendor !== $vlist[$i + 1]['Login'])
        $nex = true;
        
      if($nex) {
        // Total vendeur
        echo('</tr><tr class="text_center color_row0"><td>TOTAL VENDEUR</td>');
        echo("<td><table border=0 width=100% cellpadding=0 cellspacing=0>");
        
        foreach($cnhMineraisName as $value) {
          if($vlist[$oldi]['Ress'][$value] > 0)
            echo('<tr class="color_row0"><td><img src='.IMAGES_URL.$value.'.png>&nbsp;'.$value.'</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($vlist[$oldi]['Ress'][$value]).'</td></tr>');
        }
        
        echo('<tr class="color_row0"><td colspan=3><hr size=1></td></tr><tr class="color_row0" STYLE="font-weight:bold;"><td><img src="images/ressources.png" />&nbsp;TOTAL</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($vlist[$oldi]['RessTotal']).'</td></tr>');
        echo("</table></td>");
      }
      
      echo("</tr>\n");
    }
  
    // Total général
    echo('<tr class="text_center color_row0" valign=top><td>TOTAL GENERAL</td><td align=center>'.$nvendor.' vendeur'.($nvendor > 1 ? 's' : '').'<br>'.$basket->total.' article'.($basket->total > 1 ? 's' : '').'<br>'.$basket->nbtotal.' module'.($basket->nbtotal > 1 ? 's' : '').'</td><td><table border=0 width=100% cellpadding=0 cellspacing=0>');
    
    foreach($cnhMineraisName as $value) {
      if($vlist['RessourcesNB'][$value] > 0)
        echo('<tr class="color_row0"><td><img src='.IMAGES_URL.$value.'.png>&nbsp;'.$value.'</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($vlist['RessourcesNB'][$value]).'</td><td colspan=2>&nbsp;</td></tr>');
    }
    echo('<tr><td colspan=3><hr size=1></td></tr><tr class="color_row0" STYLE="font-weight:bold;"><td><img src="images/ressources.png" />&nbsp;TOTAL</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($vlist['RessourcesNBTotal']).'</td></tr>');
    echo("</table></td></tr>\n");
?>
<tr class="text_center color_row0"><td colspan=3 align=right><?php echo $defaultbutton; ?></td></tr>
</table></form>
<?php
  }
?>

<!-- FIN CODE LIBRE -->
</BODY></HTML>

<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();