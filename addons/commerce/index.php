<?php
// Partie standard d'EU2de
  $tologon = true;
  if(isset($_GET['action']))
    if($_GET['action'] == "logon")
      $tologon = true;
  define("CHECK_LOGIN", $tologon);	// désactive le besoin d'avoir un compte pour la page
  require_once("../../init.php");
  require_once(INCLUDE_PATH."Script.php");
  require_once(TEMPLATE_PATH."sample.tpl.php");
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  $Joueur = $_SESSION["_login"];
  require_once("cnh_fonctions.php");
  Init_Addon();

  if (!DataEngine::CheckPerms('ZZZ_COMMERCE_INDEX'))
    output::Boink(ROOT_URL.'index.php');
// DEBUT CODE LIBRE

// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <link href="cnh_addon.css" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<!-- DEBUT CODE LIBRE -->

<?
  if(isset($_GET['sousmenu']))
//    cnhToolbar(3, "menu_", 128, $_GET['sousmenu']);
    cnhTB(1, "menu_", 128, $_GET['sousmenu']);
  elseif(empty($Joueur))
    cnhTB(3, "menu_", 128, "Logon");
  else
//    cnhToolbar(3, "menu_", 128);
    cnhTB(3, "menu_", 128);
?>

<!-- FIN CODE LIBRE -->
</BODY></HTML>

<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();