<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
define('IS_IMG', true);
define('CHECK_LOGIN', false);
include('init.php');
require_once(INCLUDE_PATH . 'Script.php');
require_once(CLASS_PATH . 'img.class.php');

include_once(INCLUDE_PATH . 'crontab.php');
$cron->Run();

if (IS_IMG)
    img::Create(1, 1)->SetColorHexa('000000')->Fill()->Render();