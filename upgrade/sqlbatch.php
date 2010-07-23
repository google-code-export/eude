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
    require_once(INCLUDE_PATH . 'Script.php');
    if (!Members::CheckPerms(AXX_ROOTADMIN))
        error('No perms');
} else
    error('Install me first =)');


$file = gpc_esc($_POST['file']);
$sqlfile = ROOT_PATH . 'upgrade' . DIRECTORY_SEPARATOR . $file . '.sql';
$lockfile = ROOT_PATH . 'upgrade' . DIRECTORY_SEPARATOR . $file . '.lock';

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

if ($_POST['skipone'] == '1')
    $xml = str_replace('%msg%', 'Étape ' . ($cur + 1) . ' ignoré', $xml);
elseif (trim($sqls[$cur]) != '')
    sql($sqls[$cur]);
if (file_put_contents($lockfile, $cur + 1) === false) error('I/O Error .lock '.$cur);

$xml = str_replace('%msg%', 'Mise à jour mysql (' . ($cur + 1) . ')', $xml);
$xml = str_replace('%haserror%', '0', $xml);
if ($cur + 1 >= count($sqls))
    $xml = str_replace('%done%', '1', $xml);
else
    $xml = str_replace('%done%', '0', $xml);


if (INSTALLED)
    DataEngine::sql_log ();
die($xml);

function sql($query) {
    if (DEBUG_PLAIN)
        return FB::log($query);
    if (SCRIPT_IN)
        return DataEngine::sql($query);
    $sql = str_replace('SQL_PREFIX_', SQL_PREFIX_, $query);
    $result = mysql_query($sql) or sqlerror($query);
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