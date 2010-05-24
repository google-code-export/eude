<?php
// Partie standard d'EU2de
  require_once("../../init.php");
  require_once(INCLUDE_PATH."Script.php");
  require_once(TEMPLATE_PATH."sample.tpl.php");
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  $Joueur = $_SESSION["_login"];
  require_once("cnh_fonctions.php");
  Init_Addon();
  
// DEBUT CODE LIBRE
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_MODULES'))
    output::Boink('./index.php');
	
  require_once("shop.class.php");

  $basket = new BasketClass();
  $retourpage = false;

  if(isset($_GET['action'])) {
    if($_GET['action'] == 'back')
      $retourpage = !$basket->BasketVar(true);
  }
  elseif(isset($_POST["submit"])) {
    if($_POST["submit"] == "Suivant >>") {
      if(!$basket->BasketRecup(true))
        $retourpage = true;
    }
    elseif($_POST["submit"] == "Vider panier") {
      unset($_SESSION["basket"]);
      
      $retourpage = true;
    }
  }

  if($retourpage) {
    header("Location: template_list.php");
    exit;
  }

  $_SESSION["basket"] = $basket->GetBasketArray();
  
  $defaultbutton = '<input type="submit" name="submit" value="&lt;&lt; Précédent"> <input type="submit" name="submit" value="Suivant >>">'; 
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

<form method="post" action="shop_confirm.php">
<table border="1" align="center" cellpadding="3" cellspacing="0">
<tr><th rowspan=2 valign=bottom>Articles</th><th rowspan=2 valign=bottom>Quantité</th><th rowspan=2 valign=bottom>Prix total</th><th colspan=2>Vendeurs</th></tr>
<tr><th>Noms</th><th>Paiements acceptés</th></tr>
<tr><td colspan=5 align=right><?php echo $defaultbutton; ?></td></tr>
<?php
  for($i = 0; $i < $basket->total; $i++) {
    $modid = $basket->items[$i]["ID"];

    echo('<tr valign=top>');
    
    // Article
    echo('<td rowspan='.($basket->vendors[$modid][0]["Total"]).'>'.$basket->items[$i]['Nom'].'</td>');
    
    // Quantité
    echo('<td align=center rowspan='.($basket->vendors[$modid][0]["Total"]).'>'.$basket->items[$i]["NB"].'</td>');
    
    // Prix total
    echo('<td rowspan='.($basket->vendors[$modid][0]["Total"]).'>');
    echo("<table border=0 width=100% cellpadding=0 cellspacing=0>");
    foreach($cnhMineraisName as $value) {
      if($basket->items[$i]["RessourcesNB"][$value] > 0)
        echo("<tr><td><img src='".IMAGES_URL.$value.".png' />&nbsp;".$value."</td><td>&nbsp;</td><td align=right>".DataEngine::format_number($basket->items[$i]["RessourcesNB"][$value])."</td></tr>");
    }
    echo('<tr><td colspan=3><hr size=1></td></tr><tr STYLE="font-weight:bold;"><td><img src="images/ressources.png" />&nbsp;TOTAL</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($basket->items[$i]["RessourcesNBTotal"])."</td></tr>");
    echo("</table></td>");

    // Vendeur
    
    for($j = 1; $j <= $basket->vendors[$modid][0]["Total"]; $j++) {
      if($j > 1)
        echo('</tr><tr>');

      echo('<td valign=middle>');

      $vcheck = '';
      if(isset($_SESSION["checkout"][$modid])) {
        if($_SESSION["checkout"][$modid] == $basket->vendors[$modid][$j]["Login"])
          $vcheck = "checked";
      }
      elseif($j == 1)
        $vcheck = "checked";

      echo('<input name="modvendor_'.$modid.'" value="'.$basket->vendors[$modid][$j]["Login"].'" '.$vcheck.' type="radio">');

      if($basket->vendors[$modid][$j]["Modifier"] != 0)
        $modifier = '&nbsp;(<font color="'.($basket->vendors[$modid][$j]["Modifier"] > 0 ? "#ff0000" : "#00ff00").'">'.$basket->vendors[$modid][$j]["Modifier"].'%</font>)';
      else
        $modifier = ''; 

      // 'modvendor_'.$modid.'_'.$j
      echo($basket->vendors[$modid][$j]["Login"].$modifier."</td><td valign=middle>".$basket->StringPaiements($basket->vendors[$modid][$j]["Paiement"],'' , "<br>")."</td></tr>");
    }
    echo("\n");
  }
  
  echo("<tr valign=top bgcolor=#272727><td>TOTAL</td><td align=center>".$basket->total." article".($basket->total > 1 ? "s" : "")."<br>".$basket->nbtotal." module".($basket->nbtotal > 1 ? "s" : "")."</td><td><table border=0 width=100% cellpadding=0 cellspacing=0>");
  
  foreach($cnhMineraisName as $value) {
    if($basket->totalitems[$value] > 0)
      echo("<tr><td><img src='".IMAGES_URL.$value.".png' />&nbsp;".$value."</td><td>&nbsp;</td><td align=right>".DataEngine::format_number($basket->totalitems[$value])."</td><td colspan=2>&nbsp;</td></tr>");
  }
  echo('<tr><td colspan=3><hr size=1></td></tr><tr STYLE="font-weight:bold;"><td><img src="images/ressources.png" />&nbsp;TOTAL</td><td>&nbsp;</td><td align=right>'.DataEngine::format_number($basket->totalress)."</td></tr>");
  echo("</table></td><td colspan=2>&nbsp;</td></tr>\n");

?>
<tr><td colspan=5 align=right><?php echo $defaultbutton; ?></td></tr>
</table></form>


<!-- FIN CODE LIBRE -->
</BODY></HTML>

<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();