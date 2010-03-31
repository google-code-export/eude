<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

require_once('./init.php');
require_once(INCLUDE_PATH.'Script.php');

if (!DataEngine::CheckPerms('CARTOGRAPHIE')) {
    if (DataEngine::CheckPerms('CARTE'))
        output::Boink(ROOT_URL.'Carte.php');
    else
        output::Boink(ROOT_URL.'Mafiche.php');
} else output::Boink(ROOT_URL.'cartographie.php');
