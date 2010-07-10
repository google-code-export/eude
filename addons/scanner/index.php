<?php
/**
 * @Author: Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/

// Constantes par défaut:
// define('IS_IMG',false);		// mode image
// define('DEBUG_IMG',false);	// demande au navigateur de ne pas afficher comme une image
// define('DEBUG_PLAIN',false);	// traitement comme étant du texte par le navigateur
// define('USE_AJAX',false);	// mode xml
// define('CHECK_LOGIN',true);	// désactive le besoin d'avoir un compte pour la page

require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'map.class.php'); // requis par ownuniverse
require_once(CLASS_PATH.'parser.class.php'); // requis par ownuniverse
require_once(CLASS_PATH.'ownuniverse.class.php');

// Check si activé
if (!addons::getinstance()->Get_Addons('scanner')->CheckPerms()) DataEngine::NoPermsAndDie();

//if (!isset ($_SESSION['scanner_email']) || $_SESSION['scanner_email'] == '')
//    $_SESSION['scanner_email'] = 'votre@email.fr';

if (isset($_POST['email']) && isset ($_POST['session'])) {
    $_SESSION['scanner_email']   = gpc_esc($_POST['email']);
    $_SESSION['scanner_session'] = gpc_esc($_POST['session']);
}

$ScannerEnabled = isset($_SESSION['scanner_email']) && isset ($_SESSION['scanner_session']) &&
                    $_SESSION['scanner_email'] != '' && $_SESSION['scanner_session'] != '';

require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->css_file = false;
$tpl->page_title = 'EU2: Addons scanner';

$out = <<<form
<form name="settings" action="index.php" method="POST">
    Votre email: <input type="text" name="email" value="{$_SESSION['scanner_email']}" size="36" /><br/>
    Votre id session: <input type="text" name="session" value="{$_SESSION['scanner_session']}" size="32" /><br/>
    <br/>
    L'id de session ce trouve dans le panneau des préférences: <br/>
    - "<b>Vie Privée</b>"<br/>
    - Règles de conservation: "<b>utiliser les paramètres personnalisés pour l'historique</b>"<br/>
    - Le bouton "<b>Afficher les cookies...</b>" apparait.<br/>
    - Dans le champ de recherche mettez "<b>australis</b>" ou "<b>borealis</b>".<br/>
    - Sélectionner celui qui porte le nom de "<b>PHPSESSID</b>"<br/>
    - Et copier/coller la partie "<b>Contenu</b>".<br/>
    - la valeur ressemble a ça: <b>694000333b545d5ffda746955eb6067a</b><br/>
    <input type="submit" value="Enregistrer" /><br/>
</form>
form;
$tpl->PushOutput($out); // ajoute le texte précédant à la sortie qui sera affiché.

$planets = ownuniverse::getinstance()->get_comlevelwithname();

if ($ScannerEnabled && is_array($planets)) {
    $tpl->PushOutput('<address>l\'exactitude des champ ci-dessus ne sont pas vérifié, et considéré systématiquement comme bon.</address><br/>');
    foreach ($planets as $id => $coord)
        $tpl->PushOutput('<a href="./scan.php?id='.$id.'&step=0">Vortex autour de la planète '.$coord['ss'].': '.$coord['name'].'</a><br/>');
    foreach ($planets as $id => $coord)
        $tpl->PushOutput('<a href="./planets.php?id='.$id.'&step=0">Planètes autour de la planète '.$coord['ss'].': '.$coord['name'].'</a><br/>');
    
} else {
    $tpl->PushOutput('Pour la suite veuillez remplir les champs ci-dessus, ainsi que votre page perso "Production"');
}
// Un petit menu perso pour l'addons
$menu = array(
    'carte' => array('%ROOT_URL%Carte.php','%BTN_URL%carte.png',160,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'prod' => array('%ROOT_URL%ownuniverse.php','%BTN_URL%ownuniverse.png',160,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
);

$tpl->DoOutput($menu,true); // stoppe toute execution du script et transmet les sorties html/xml/...
// les deux 'true' étant
// 1- Inclusion du menu (html, sans effet sur xml/img)
// 2- Inclusion de l'entete de base (html, sans effet sur xml/img)


