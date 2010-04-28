<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

// Constantes par défaut:
// define('IS_IMG',false);		// mode image
// define('DEBUG_IMG',false);	// demande au navigateur de ne pas afficher comme une image
// define('DEBUG_PLAIN',false);	// traitement comme étant du texte par le navigateur
// define('USE_AJAX',false);	// mode xml
// define('CHECK_LOGIN',true);	// désactive le besoin d'avoir un compte pour la page

require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');


// Check si activé / permissions
if (!addons::getinstance()->Is_installed('sample')) DataEngine::NoPermsAndDie();

require_once(TEMPLATE_PATH.'sample.tpl.php');

$tpl = tpl_sample::getinstance();
$tpl->page_title = 'EU2: Addons sample';

$out = <<<sample_text
    <pre><font color=white>

Simple example d'addons

la classe "tpl_sample" pouvant servir de transition à l'utilisation des 'templates'

<i>exemple (ce fichier déjà pour plus de détails):</i>
<b>
require_once("../../init.php");
	require_once(INCLUDE_PATH.'Script.php');
[...]
\$site = "&lt;html&gt;[...]&lt/html&gt;";

require_once(TEMPLATE_PATH.'sample.tpl.php');
\$tpl = tpl_sample::getinstance();
\$tpl->page_title = "EU2: Addons sample";
\$tpl->PushOutput(\$site);
\$tpl->doOutput();
</b>

Fichier a voir:
%TEMPLATE_URL%sample.tpl.php
%ADDONS_URL%sample.conf.php
%ADDONS_URL%sample/index.php
</font></pre>
sample_text;

$tpl->PushOutput($out); // ajoute le texte précédant à la sortie qui sera affiché.

// Un petit menu perso pour l'addons
$menu = array(
    'carte' => array('%ROOT_URL%Carte.php','%IMAGES_URL%btn-cartographie.png',180,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'mafiche' => array('%ROOT_URL%Mafiche.php','%IMAGES_URL%Btn-Mafiche.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'moi' => array('%ADDONS_URL%sample/index.php','%IMAGES_URL%test.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
);

$tpl->DoOutput($menu,true); // stoppe toute execution du script et transmet les sorties html/xml/...
// les deux 'true' étant
// 1- Inclusion du menu (html, sans effet sur xml/img)
// 2- Inclusion de l'entete de base (html, sans effet sur xml/img)


