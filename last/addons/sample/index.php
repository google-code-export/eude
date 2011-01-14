<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
// Constantes par défaut:
// define('IS_IMG',false);		// mode image
// define('DEBUG_IMG',false);	// demande au navigateur de ne pas afficher comme une image
// define('DEBUG_PLAIN',false);	// traitement comme étant du texte par le navigateur
// define('USE_AJAX',false);	// mode xml
// define('CHECK_LOGIN',true);	// désactive le besoin d'avoir un compte pour la page

require_once('../../init.php');
require_once(INCLUDE_PATH . 'Script.php');
require_once(CLASS_PATH . 'parser.class.php');
require_once(CLASS_PATH . 'cartographie.class.php');
require_once(CLASS_PATH . 'map.class.php');


// Check si activé / permissions
if (!addons::getinstance()->Is_installed('wormhole_import'))
    Members::NoPermsAndDie();

require_once(TEMPLATE_PATH . 'sample.tpl.php');

$tpl = tpl_sample::getinstance();
$tpl->page_title = 'EU2: Vortex Import';
$BASE_FILE = ROOT_URL . "addons/wormhole_import/index.php";

if (isset($_POST['act']) && $_POST['act'] == 'import') {

    // Purge au préalable....
    define('CRON_LOADONLY', true);
    include(INCLUDE_PATH . 'crontab.php');
    $cron->GetJob('job_vortex')->RunJob();
    $cron->Save();

    $vortex = explode("\n", $_POST['data']);
    $carto = cartographie::getinstance();
    foreach ($vortex as $item) {
        list($coordsin, $coordsout) = explode(',', $item);

        $carto->add_vortex($coordsin, $coordsout);
    }
    output::Boink($BASE_FILE, 'Whormhole import done. Enjoy.');
}

$out .= <<<text
<table class="table_nospacing table_center color_row1">
    <tr><td class="color_bigheader text_center">
            Wormhole import
    </td></tr>
    <tr><td class="text_center">
        <form method="post" action="{$BASE_FILE}">
            <textarea name="data" class="color_row1" cols="50" rows="4"></textarea><br/>
            <input class="color_header" type="submit" value="Import"/>
            <input type="hidden" name="act" value="import"/>
        </form>
    </td></tr>
</table>
text;

$tpl->PushOutput($out); // ajoute le texte précédant à la sortie qui sera affiché.

$tpl->DoOutput();


