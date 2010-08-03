<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

// Si le temps serveur correspond pas a celui du jeu, la liste:
// http://fr.php.net/manual/fr/timezones.europe.php
if (function_exists('date_default_timezone_set')) date_default_timezone_set('Europe/Berlin');

/**
 * Vous devriez pas en avoir besoin, néanmoins,
 * - Ce mode peut rendre le site "Inacessible" sur certaines page si vous passer par un proxy
 *    (trop d'info de debug qui transite ^^)
 * - Les infos de debug étendue utilise "FirePHP" (et Firebug),
 *      et est affiché uniquement au Sup-Admin. (voir Script.php)
 * @var boolean
 */
define('IN_DEV', false);

/// ### ### ### ### ### ### ### ### ### ///
/// ### NE RIEN CHANGER CI DESSOUS  ### ///
/// ### ### ### ### ### ### ### ### ### ///

ob_start();
define('START', microtime(true));
define('ROOT_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define('INCLUDE_PATH', ROOT_PATH .'Script'.DIRECTORY_SEPARATOR );
define('ADDONS_PATH', ROOT_PATH .'addons'.DIRECTORY_SEPARATOR );
define('TEMPLATE_PATH', ROOT_PATH .'tpl'.DIRECTORY_SEPARATOR );
define('CLASS_PATH', ROOT_PATH .'class'.DIRECTORY_SEPARATOR );

// constantes pouvant être définies avant "init.php".
if (!defined('IS_IMG'))			define('IS_IMG',false);
if (!defined('DEBUG_IMG'))		define('DEBUG_IMG',false);
if (!defined('DEBUG_PLAIN'))	define('DEBUG_PLAIN',false);
if (!defined('USE_AJAX'))		define('USE_AJAX',false);
if (!defined('CHECK_LOGIN'))	define('CHECK_LOGIN',true);
if (!defined('NO_SESSIONS'))	define('NO_SESSIONS',false);
if (!defined('E_DEPRECATED')) define('E_DEPRECATED', false);

define('AXX_DISABLED',    32767);
define('AXX_ROOTADMIN',     600);
define('AXX_ADMIN',         500);
define('AXX_SUPMODO',       400);
define('AXX_MODO',          300);
define('AXX_POWERMEMBER',   210);
define('AXX_MEMBER',        200);
define('AXX_GUEST',         100);
define('AXX_VALIDATING',    0);

if (version_compare(PHP_VERSION, '5.0.0', '<'))
    trigger_error('Pr&eacute;requis manquant (php v5 ou sup&eacute;rieur)', E_USER_ERROR);
if (version_compare(PHP_VERSION, '6.0.0', '>='))
    trigger_error('php v6+ n\'est pas actuellement support&eacute;', E_USER_WARNING);

define('DE_INIT', true);
define('DE_DEMO', false);


if (IN_DEV)
    error_reporting(E_ALL ^ E_NOTICE);
else
    error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (!function_exists('xdebug_break')) {
    /**
     * @ignore
     */
    function xdebug_break() {

    }
    function xdebug_start_trace() {

    }
    function xdebug_stop_trace() {

    }
}

function my_error_handler( $errno, $errstr, $errfile, $errline ) {
    $errfile = str_replace( ROOT_PATH, '', $errfile );
    $errstr = str_replace( ROOT_PATH, '', $errstr );


    switch ($errno) {
        case E_USER_WARNING:
        case E_WARNING:
            xdebug_break();
            echo "<font color=darkorange><b>ALERTE</b> [$errno] $errstr (Fichier $errfile:$errline)</font><br />\n";
            break;
        case E_ERROR:
        case E_PARSE:
        case E_USER_ERROR:
            xdebug_break();
            echo "<font color=red><b>ERREUR</b> [$errno] $errstr<br />\n"
                    ."  Erreur fatale sur la ligne $errline dans le fichier $errfile"
                    .", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n"
                    .'Arr&ecirc;t...</font><pre>';
            debug_print_backtrace();
            die();
            break;
        default:
        //Do nothing?
            if (!stristr($errstr,'Undefined') && !IS_IMG) {
                xdebug_break();
                echo "<font color=darkorange><b>NOTICE</b> [$errno] $errstr (Fichier $errfile:$errline)</font><br />\n";
            }
            break;
    }
}

$_my_xml_error_handler = '';
function my_xml_error_handler( $errno, $errstr, $errfile, $errline ) {
    global $_my_xml_error_handler;
    $errfile = str_replace( ROOT_PATH, '', $errfile );
    $errstr = str_replace( ROOT_PATH, '', $errstr );

    switch ($errno) {
        case E_USER_WARNING:
        case E_WARNING:
            xdebug_break();
            $_my_xml_error_handler .= "ALERTE [$errno] $errstr (Fichier $errfile:$errline)\n";
            break;
        case E_ERROR:
        case E_PARSE:
        case E_USER_ERROR:
            xdebug_break();
            $_my_xml_error_handler .= "ERREUR [$errno] $errstr\n"
                    ."  Erreur fatale sur la ligne $errline dans le fichier $errfile"
                    .", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n"
                    .'Arr&ecirc;t...';
            output::_DoOutput('<php><phperror><![CDATA[' . DataEngine::xml_fix51($_my_xml_error_handler) . ']]></phperror></php>');
            break;
        default:
        //Do nothing?
            if (!stristr($errstr,'Undefined')) {
                xdebug_break();
                $_my_xml_error_handler .= "NOTICE [$errno] $errstr (Fichier $errfile:$errline)\n";
            }
            break;
    }
}
function getxml_errors() {
    global $_my_xml_error_handler;
    return '<phperror><![CDATA[' . DataEngine::xml_fix51($_my_xml_error_handler) . ']]></phperror>';
}

if (USE_AJAX || DEBUG_PLAIN)
    set_error_handler('my_xml_error_handler');
else
    set_error_handler('my_error_handler');

// find ./ -name "*.php" | xargs -t svn ps "svn:keywords" "Id"
// find ./ -name "*.js" | xargs -t svn ps "svn:keywords" "Id"
// find ./ -name "*.css" | xargs -t svn ps "svn:keywords" "Id"
// svn ci