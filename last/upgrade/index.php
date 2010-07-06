<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 * */
define('TEST_ONLY', false);
define('_CD', '../');
define('updfile', './upgrade.sql');
if (!file_exists(_CD . 'Script/Entete.php'))
    die('No install found !');
if (file_exists('./upgrade.lock'))
    if (TEST_ONLY)
        unlink('./upgrade.lock');
    else
        die('Upgrade should allready done, aborded.');
if (!file_exists(updfile))
    die('sql not found');

require_once(_CD . 'init.php');
require_once(INCLUDE_PATH . 'Script.php');
if (!Members::CheckPerms(AXX_ROOTADMIN))
    Members::NoPermsAndDie();

header('Content-Type: text/plain;charset=utf-8');

$file = file_get_contents(updfile);

$sqls = preg_split('/;[\n\r]+/', $file);
$nb = count($sqls);

if ($nb == 1 && $sqls[0] == '')
    die('Invalid sql file');

echo "Start upgrade... 0/$nb\n";
foreach ($sqls as $k => $sql) {
    $i = $k + 1;
    echo "Step $i/$nb";
    if (TEST_ONLY)
        FB::log ($sql, 'sql n°'.$k);
    else
        DataEngine::sql($sql);
    echo "...Ok\n";
}
echo "Upgrade done.\n";
echo "MAJ terminé.\n";
echo "** Please remove '/upgrade' directory ***\n";
echo "** Veuillez supprimer le dossier '/upgrade' ***";


if (!TEST_ONLY)
    file_put_contents('./upgrade.lock', time());