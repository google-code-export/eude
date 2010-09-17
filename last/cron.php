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

ignore_user_abort(true);

if (IS_IMG) {
    if ($cron->Run())
        img::Create(3, 3)->SetColorHexa('00FF00')->Fill()->Render();
    else
        img::Create(1, 1)->FillAlphaHexa('000000')->Render();
} else {
    if (($job = $cron->GetAJob()) !== false) {
        $job->RunJob();
        $cron->Save();
        DataEngine::sql_log();
        echo get_class($job);
    }
}
