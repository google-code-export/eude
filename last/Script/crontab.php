<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
require_once(CLASS_PATH . 'cron.class.php');

class job_vortex extends phpcron_job {

    public function __construct() {
        parent::__construct();

        $lng = language::getinstance()->GetLngBlock('dataengine');
        $this->CronPattern = $lng['wormholes_cron'];
        // Vérouiller sur sa première initialisation ou reset cron...
        $this->evaluate_job();
        $this->lastrun = $this->lastRan+1;
    }

    public function Actived() {
        return DataEngine::config_key('wormhole_cleaning', 'enabled');
    }

    public function RunJob() {
        $this->_lock();
        $now = time();
        $mysql_result = DataEngine::sql('SELECT ID FROM `SQL_PREFIX_Coordonnee` WHERE `TYPE`=1');
        while ($row = mysql_fetch_assoc($mysql_result))
            $tmp[] = $row['ID'];

        if (is_array($tmp) && count($tmp) > 0) {
            $tmp = implode(',', $tmp);
            DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee` WHERE `ID` in (' . $tmp . ')');
            DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee_Joueurs` WHERE `jID` in (' . $tmp . ')');
            DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee_Planetes` WHERE `pID` in (' . $tmp . ')');
        }
        $wormhole_cleaning = array('enabled' => true, 'lastrun' => $now);
        DataEngine::conf_update('wormhole_cleaning', $wormhole_cleaning);
        DataEngine::sql_do_spool(); // Mettre à jour maintenant, pas que deux membres le fasse a 1/2sec d'intervalle.
        addons::getinstance()->VortexCleaned();
        parent::RunJob();
    }

    public function __wakeup() {
        if (DataEngine::config_key('wormhole_cleaning', 'enabled'))
            $this->lastrun = DataEngine::config_key('wormhole_cleaning', 'lastrun');
    }

}

class job_css extends phpcron_job {

    public function __construct() {
        parent::__construct();
        
        if (file_exists(CACHE_PATH.'eude.css'))
            $this->lastrun = filemtime(CACHE_PATH.'eude.css');
        $this->__wakeup();
    }

    public function Actived() {
        return true;
    }

    public function RunJob() {
        $this->_lock();
        include(LNG_PATH . 'css.php');
        file_put_contents(CACHE_PATH . 'eude.css', $css);
sleep(6000);
        parent::RunJob();
    }

    public function __wakeup() {
        $time = max(filemtime(LNG_PATH . 'css.php'), filemtime(LNG_PATH . 'template.css'));
        $this->CronPattern = strftime("%M %H %d %m %w", $time);
    }
}


// -----------------------------------------------------------------------------


DataEngine::conf_cache('cron');

$cron_conf = DataEngine::config('cron');

if (is_object($cron_conf))
    $cron = $cron_conf;
else {
    $cron = phpcron_list::getinstance();
    $cron->AddJob(new job_vortex());
    $cron->AddJob(new job_css());
}

//DataEngine::Grab_Custom_Jobs();
