<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!defined('DE_INIT')) die('Init error');
define('SCRIPT_IN',			true);
require_once(CLASS_PATH.'dataengine.class.php');
require_once(CLASS_PATH.'FirePHP.class.php');	// Debug
require_once(CLASS_PATH.'fb.php');				// Debug
require_once(CLASS_PATH.'browser.class.php');
require_once(CLASS_PATH.'output.class.php');
require_once(CLASS_PATH.'addons.class.php');
require_once(CLASS_PATH.'language.class.php');

DataEngine::init();

if (CHECK_LOGIN) require_once(INCLUDE_PATH.'/login.php');


if (date('w')==0) DataEngine::conf_cache('wormhole_cleaning');

/// ### Mode debug, root admin & dev ONLY ###
FB::setEnabled( !IS_IMG && IN_DEV && DataEngine::CheckPerms(AXX_ROOTADMIN));
FB::info(DataEngine::$browser->getBrowser(),'Browser');

function Get_IP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    elseif(isset($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    else
        return $_SERVER['REMOTE_ADDR'];
}

function bulle ($texte,$addover='',$addout='') {
    if(is_array($addover))
        $addover=implode($addover,'');
    if(is_array($addout)) $addout=implode($addout,'');
    $texte=htmlspecialchars(str_replace("\n", '', $texte),ENT_QUOTES,'UTF-8');
    return ("onmouseover='montre(\"".$texte."\");$addover' onmouseout='cache();$addout'");
}

parse_str($_SERVER["QUERY_STRING"], $current_get); // => http_build_query($current_get)

// TODO REMOVE THIS STUPID/USELESS(?) THINGS (AlterGet/$myget)
//Fonction permettant de modifier la valeur d'une clef GET dans un ensemble passé en parametre
function AlterGet($get,$clef,$valeur) {
    $toanalyse = substr($get,1); //enleve le ? forme de la chaine : var=x&var2=y ...
    $tabvar = explode('&',$toanalyse); //explosion en tableau
    $replace = 0;
    $tabresult[0]='';
    foreach ($tabvar as $key => $oldvalue) {
        $traitement = explode('=',$oldvalue);

        if($traitement[0] == $clef) {
            $oldvalue = $traitement[0].'='.$valeur;
            $replace=1;
            array_unshift($tabresult,$oldvalue);
        } else array_push($tabresult,$oldvalue);//array_push($tabvar,$oldvalue);
    }
    if(!$replace) array_unshift($tabresult, $clef.'='.$valeur);
    return ('?'.implode('&',$tabresult));
}

$myget = '';
foreach ($_GET as $key => $value) {
    $myget .= $key.'='.$value.'&';
//SC=ONVortex=ONJoueur=ONPlanete=ON
}
if ($myget!='') $myget = '?'.substr($myget,0,strlen($myget)-1);

/**
 * @param string $value from $_POST/$_GET
 * @param boolean $skip_gpc
 * @return string Value ready for mysql
 */
function sqlesc($value,$skip_gpc=true) {
    if (!get_magic_quotes_gpc() || $skip_gpc) {
        return mysql_real_escape_string($value);
    } else {
        return mysql_real_escape_string(stripslashes($value));
    }
}

function gpc_esc($value) {
    if (!get_magic_quotes_gpc())
        return $value;
    else
        return stripslashes($value);
}

// Codage en 'dur' des types...
// 'C'orps 'C'eleste 'type'
$cctype[0] = 'Joueur';
$cctype[3] = 'Alliés';
$cctype[5] = 'Ennemis';
$cctype[6] = 'Flotte PNJ';
$cctype[1] = 'Vortex';
$cctype[2] = 'Planète';
$cctype[4] = 'Astéroïdes';

// Même chose, utilisé par Cartedetail.php
$ccimg[0] = 'Joueur.jpg';
$ccimg[3] = 'fleet_own.gif';
$ccimg[5] = 'fleet_enemy.gif';
$ccimg[6] = 'fleet_npc.gif';
$ccimg[1] = 'Vortex.jpg';
$ccimg[2] = 'Planete.jpg';
$ccimg[4] = 'Asteroide.jpg';

// Même chose, `TYPE` en version hummainement lisible
$stype[0] = 'Joueur';
$stype[1] = 'Vortex';
$stype[2] = 'Planète';
$stype[3] = 'Alliés';
$stype[4] = 'Astéroïde';
$stype[5] = 'Ennemi';
$stype[6] = 'PNJ';
