<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
include ('../init.php');
require_once(INCLUDE_PATH . 'Script.php');
if (!Members::CheckPerms(AXX_ROOTADMIN))
    Members::NoPermsAndDie ();
include_once ('upgrade.tpl.php');
$tpl = tpl_upgrade::getinstance();
$tpl->Setheader();

//------------------------------------------------------------------------------
$inf_title = 'Mise à jour depuis 1.4.2.x vers 1.4.5.1';
$file = 'upgrade142';
//------------------------------------------------------------------------------

$path = ROOT_PATH . 'upgrade' . DIRECTORY_SEPARATOR;
$sqlfile = $path . $file . '.sql';
$lockfile = $path . $file . '.lock';
if (file_put_contents($path.'test.lock', $cur + 1) === false) {
    @chmod('../upgrade', '777');
    @unlink($path.'test.lock');
    stop_on_error('I/O Error test.lock chmod "/upgrade" directory to 777');
}

if (!file_exists($sqlfile))
    stop_on_error('Mise à jour corrompue !');

if (file_exists($lockfile))
    $cur = (int) file_get_contents($lockfile);
else
    $cur = 0;

$tpl->AddToRow ($inf_title, 'value');
$tpl->PushRow();

$sqls = preg_split('/;[\n\r]+/', file_get_contents($sqlfile));
FB::log(count($sqls), 'Nb sql');
FB::log($cur, 'cur');
FB::log($sqls);
$max = count($sqls) - $cur;
if ($max == 0)
    stop_on_error('Déjà fait.');

$out = <<<x
<script type="text/javascript" src="./sqlbatch.js"></script>
    <div id="sqlbatchmsg"><a href="javascript:void(0);" Onclick="sql_run('{$file}', {$max});">Lancer maintenant ! </a></div>
    <div id="sqlbatchmsgstep"></div>
x;

$tpl->AddToRow($out, 'value');
$tpl->DoOutput(false);

function stop_on_error($value) {
    global $tpl;
    $tpl->AddToRow($value, 'value');
    $tpl->DoOutput(false);
}