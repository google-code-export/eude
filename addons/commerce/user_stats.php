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

<br /><br /><?php cnhTB(0) ?><br /><?php cnhTB(0,null,null,"Stats") ?><hr><br />
<!-- DEBUT CODE LIBRE -->

<table border=1 align=center cellspacing=0 cellpadding=5>
  <tr><th>Chiffres en tout genre</th></tr>
<?php
  $sqlreq = '
SELECT
(SELECT `DateLast` FROM SQL_PREFIX_Users_Config WHERE `Login` = "'.$Joueur.'" ORDER BY `DateLast` DESC LIMIT 1) AS DateLastConnect,
(SELECT `DateCreated` FROM SQL_PREFIX_Users_Config WHERE `Login` = "'.$Joueur.'") AS DateCreate
';
  $mysql_result = DataEngine::sql($sqlreq) or die(mysql_error());
  $datas = mysql_fetch_array($mysql_result)
?>
  <tr><td>
Votre date de création: le <b><?php echo date_format(date_create($datas['DateCreate']), "j F Y \à G:i"); ?></b><br />
Votre dernière connexion: le <b><?php echo date_format(date_create($datas['DateLastConnect']), "j F Y \à G:i"); ?></b>
  </td></tr></table>

<!-- FIN CODE LIBRE -->
</BODY></HTML>


<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();