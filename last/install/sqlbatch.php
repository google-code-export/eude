<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
define('USE_AJAX', true);
define('DEBUG_PLAIN', false); // true => pas de sql de fait !
require ('../init.php');
define('INSTALLED', file_exists(INCLUDE_PATH . 'Entete.php'));

header('Content-Type: text/xml;charset=utf-8');

$xml = <<<xml
<sql>
    <msg><![CDATA[%msg%]]></msg>
    <haserror>%haserror%</haserror>
    <done>%done%</done>
</sql>
xml;

if (INSTALLED) {
    error('Déjà installé.');
} else {

    function gpc_esc($value) {
        if (!get_magic_quotes_gpc())
            return $value;
        else
            return stripslashes($value);
    }

    require_once(CLASS_PATH . 'dataengine.class.php');
    if (!file_exists('./Entete.php'))
        error('Utilisation incorrecte !');
    require_once('./Entete.php');

    require_once(CLASS_PATH . 'FirePHP.class.php');
    require_once(CLASS_PATH . 'fb.php');

    Config::init();
    Config::DB_Connect();
}


$file = gpc_esc($_POST['file']);
$sqlfile = ROOT_PATH . 'install' . DIRECTORY_SEPARATOR . $file . '.sql';
$lockfile = ROOT_PATH . 'install' . DIRECTORY_SEPARATOR . $file . '.lock';


if (preg_match('/[^a-zA-Z_0-9]+/', $file) > 0)
    error('Tentative d\'injection détecté.');
if (!file_exists($sqlfile))
    error('Mise à jour corrompue !');

if (file_exists($lockfile))
    $cur = (int) file_get_contents($lockfile);
else
    $cur = 0;
if (DEBUG_PLAIN)
    FB::log($cur, '$cur');

$sqls = preg_split('/;[\n\r]+/', file_get_contents($sqlfile));
if ($cur >= count($sqls))
    error('Incorrect $cur');

sql($sqls[$cur]);
if (file_put_contents($lockfile, $cur + 1) === false) error('I/O Error .lock '.$cur);

$xml = str_replace('%msg%', 'Installation de la base de donnée en cours', $xml);
$xml = str_replace('%haserror%', '0', $xml);
if ($cur + 1 >= count($sqls))
    $xml = str_replace('%done%', '1', $xml);
else
    $xml = str_replace('%done%', '0', $xml);


if (INSTALLED)
    DataEngine::sql_log ();
die($xml);

function sql($sql) {
    $sql = str_replace('%%username%%', mysql_real_escape_string(gpc_esc($_POST['username'])), $sql);
    $sql = str_replace('%%password%%', mysql_real_escape_string(gpc_esc($_POST['password'])), $sql);
    $empire = gpc_esc($_POST['empire']);
    $sql = str_replace('%%empirename%%', mysql_real_escape_string($empire), $sql);
    $sql = str_replace('%%empirenamelen%%', mb_strlen($empire, 'utf-8'), $sql);
    $board = gpc_esc($_POST['board']);
    $sql = str_replace('%%boardname%%', mysql_real_escape_string($board), $sql);
    $sql = str_replace('%%boardnamelen%%', mb_strlen($board, 'utf-8'), $sql);
    $sql = str_replace('SQL_PREFIX_', SQL_PREFIX_, $sql);
    FB::log($sql);
    if (!DEBUG_PLAIN)
        $result = mysql_query($sql) or sqlerror($sql);
}

function sqlerror($sql) {
    error($sql . '<br/>' . mysql_error());
}

function error($msg) {
    global $xml;
    $xml = str_replace('%msg%', $msg, $xml);
    $xml = str_replace('%haserror%', '1', $xml);
    $xml = str_replace('%done%', '0', $xml);
    die($xml);
}