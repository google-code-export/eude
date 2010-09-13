<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
require(CLASS_PATH . 'cron.class.php');

class job_vortex extends phpcron_job {

    public function __construct() {
        parent::__construct();

        $lng = language::getinstance()->GetLngBlock('dataengine');
        $this->CronPattern = $lng['wormholes_cron'];
        $this->lastrun = DataEngine::config_key('wormhole_cleaning', 'lastrun');
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

        if (file_exists(CACHE_PATH . 'eude.css'))
            $this->lastrun = filemtime(CACHE_PATH . 'eude.css');
        $this->__wakeup();
    }

    public function Actived() {
        return true;
    }

    public function RunJob() {
        $this->_lock();
        include(LNG_PATH . 'css.php');
        file_put_contents(CACHE_PATH . 'eude.css', $css);
        parent::RunJob();
    }

    public function __wakeup() {
        $time = max(filemtime(LNG_PATH . 'css.php'), filemtime(LNG_PATH . 'template.css'));
        $this->CronPattern = strftime("%M %H %d %m %w", $time);
    }

}

class job_buttons extends phpcron_job {

    public function __construct() {
        parent::__construct();
        $files = scandir(CACHE_PATH);
        foreach ($files as $file)
            if (substr($file, -4) == '.png' && substr($file, 0, 3) == 'btn-') {
                $this->lastrun = filemtime($file);
                break;
            }
        $this->__wakeup();
    }

    public function Actived() {
        return true;
    }

    public function RunJob() {
        $this->_lock();
        include(LNG_PATH . 'btn.php');
//        file_put_contents(CACHE_PATH . 'eude.css', $css);
        $files = scandir(CACHE_PATH);
        foreach ($files as $file)
            if (substr($file, -4) == '.png' && substr($file, 0, 3) == 'btn-')
                unlink(CACHE_PATH . $file);
        foreach ($listing as $key => $dummy)
            do_btn($key, $listing);
        parent::RunJob();
    }

    public function __wakeup() {
        $this->CronPattern = strftime("%M %H %d %m %w", filemtime(LNG_PATH . 'btn.php'));
    }

}

function array_js(&$item1, $key) {
    if (!is_numeric($item1))
        $item1 = '"' . $item1 . '"';
}

class job_map_tooltips extends phpcron_job {

    private $fp = false;
    private $filename = false;

    public function __construct() {
        parent::__construct();
        $this->__wakeup();
    }

    public function Actived() {
        return isset($_SESSION['_login']) && $_SESSION['_login'] != '';
    }

    private function add_ss($ss, $data) {
        $line = array();
        $tmp = '';
        // map::ss_info
        foreach ($data as $k => $v) { /// $k = ID mysql/nb type @@ $v = array...
            if (isset($v['EMPIRE']))
                $v['EMPIRE'] = htmlspecialchars(addcslashes($v['EMPIRE'], '"'));

            switch ($v['type']) {
                case 'moi':
                    $line['ownplanet'][] = $v['INFOS'];
                    break;
                case 'Vortex':
                    if (!isset($line['wormholes']))
                        $line['wormholes'] = array();
                    $line['wormholes'][] = $v['POSOUT'];
                    break;
                case 'Joueur':
                    if (!isset($line['players']))
                        $line['players'] = array();

                    if ($v['EMPIRE'] != '')
                        $line['players'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                    else
                        $line['players'][] = $v['USER'];
                    break;
                case 'alliance':
                    if (!isset($line['alliance']))
                        $line['alliance'] = array();

//                    if ($v['Joueur'] != '')
//                        $line['alliance'][] = $v['Joueur'] . ' (' . $v['Grade'] . ')';
//                    else
                    $line['alliance'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                    break;
                case 'Ennemi':
                    if (!isset($line['ennemys']))
                        $line['ennemys'] = array();

                    if ($v['EMPIRE'] != '')
                        $line['ennemys'][] = $v['USER'] . ' [' . $v['EMPIRE'] . ']';
                    else
                        $line['ennemys'][] = $v['USER'];
                    break;
                case 'pnj':
                    if (!isset($line['reaperfleet']))
                        $line['reaperfleet'] = array();

                    $line['reaperfleet'][] = $v['INFOS'];
                    break;
                case 'Planète':
                    if (!isset($line['planets']))
                        $line['planets'] = 1;
                    else
                        $line['planets']++;
                    break;
                case 'Astéroïde':
                    if (!isset($line['asteroids']))
                        $line['asteroids'] = 1;
                    else
                        $line['asteroids']++;
                    break;
                case 'cdr':
                    if (!isset($line['cdr']))
                        $line['cdr'] = 1;
                    else
                        $line['cdr']++;
                    break;
            }
        }

        $tmp = 'ss_info[' . $ss . ']={';

        foreach ($line as $k => $v) {
            if (is_array($v) && count($v) > 0) {
                array_walk($v, 'array_js');
                $tmp .= $k . ':[' . implode(',', $v) . '],';
            } elseif (!is_array($v))
                $tmp .= $k . ':' . (is_numeric($v) ? $v : '"' . $v . '"') . ',';
        }
        fwrite($this->fp, substr($tmp, 0, strlen($tmp) - 1) . ' };');

        /*
          bubulle[$ss] = { // ss
          ownplanet: {$line['ownplanet']},
          planets: 4, // Numbers
          asteroids: 2, // number
          //    cdr: 2, // number
          wormholes: [4433, 5050], // Out SS ^ x
          alliance: ['alliance'], // alliance members ^ x
          players: ['Name (Alliance)'], // Players ^ x
          ennemys: ['Name (Alliance)'], // Players ^ x
          allys: ['Name (Alliance)'], // Players ^ x
          reaperfleet: ['Fleet name'], // Fleets name ^ x
          playerfleet: ['Fleet name'], // Fleets owner/name ^ x
          searchresult: ['searchresult'] // Players ^ x
          };
          EOF; */
    }

    public function RunJob() {
        $this->_lock();

        $vortex_a = array();
        $CurrSS_a = array();
        $empire = trim(DataEngine::config_key('config', 'MyEmpire'));
        $cxx_empires = DataEngine::CheckPerms('CARTE_SHOWEMPIRE');

        $this->fp = fopen($this->filename, 'w');
        stream_set_write_buffer($this->fp, 0);
        fwrite($this->fp, 'var ss_info=Array();');

        /* Récupérations des vortex */ {
            $sql = 'SELECT `ID`, `POSIN`, `POSOUT` from `SQL_PREFIX_Coordonnee` where `Type`=1';
            $mysql_result = DataEngine::sql($sql);
            while ($line = mysql_fetch_assoc($mysql_result)) {
                $vortex_a[$line['POSOUT']][$line['ID']]['POSIN'] = $line['POSOUT'];
                $vortex_a[$line['POSOUT']][$line['ID']]['POSOUT'] = $line['POSIN'];
                $vortex_a[$line['POSOUT']][$line['ID']]['TYPE'] = 1;
            }
            mysql_free_result($mysql_result);
        }

        $sql = <<<sql
SELECT c.`ID`, c.`TYPE`, c.`POSIN`, c.`POSOUT`, j.`USER`, j.`INFOS`, j.`EMPIRE` FROM SQL_PREFIX_Coordonnee as c
LEFT JOIN SQL_PREFIX_Coordonnee_Joueurs as j on id=jid
ORDER BY c.`POSIN` ASC
sql;

        $mysql_result = DataEngine::sql($sql);
        $CurrSS = 0;
        while ($line = mysql_fetch_assoc($mysql_result)) {
            if ($CurrSS == 0)
                $CurrSS = $line['POSIN'];

            if ($line['POSIN'] != $CurrSS) {
                if (isset($vortex_a[$CurrSS]) && is_array($vortex_a[$CurrSS])) {
                    foreach ($vortex_a[$CurrSS] as $k => $v) {
                        $CurrSS_a[$k] = $v;
                        $CurrSS_a[$k]['type'] = 'Vortex';
                        $CurrSS_a['Vortex'] = isset($CurrSS_a['Vortex']) ? $CurrSS_a['Vortex']++ : 1;
                    }
                    unset($vortex_a[$CurrSS]); // destruction du vortex...
                }
                $this->add_ss($CurrSS, $CurrSS_a);
                $CurrSS = $line['POSIN'];
                $CurrSS_a = array();
//                if ($CurrSS == 1240)
//                    break;
//                if ($CurrSS == 1240)
//                    xdebug_break();
            }

            $ID = $line['ID'];
            $ss = $line['POSIN'];

            $CurrSS_a[$ID] = $line;
            /* map::ss_type */ {

                switch ($line['TYPE']) {
                    case 0: $type = 'Joueur';
                        break;
                    case 1: $type = 'Vortex';
                        break;
                    case 2: $type = 'Planète';
                        break;
                    case 3: $type = 'alliance';
                        break;
                    case 4: $type = 'Astéroïde';
                        break;
                    case 5: $type = 'Ennemi';
                        break;
                    case 6: $type = 'pnj';
                        break;
                    default: $type = 'na';
                }
                if ($empire != '' && $line['EMPIRE'] == $empire && $cxx_empires)
                    $type = 'empire';
                if (stristr($line['USER'], $_SESSION['_login']) !== FALSE)
                    $type = 'moi';
                $CurrSS_a[$ID]['type'] = $type;
            }

            if (isset($CurrSS_a[$CurrSS_a[$ID]['type']]))
                $CurrSS_a[$CurrSS_a[$ID]['type']]++;
            else
                $CurrSS_a[$CurrSS_a[$ID]['type']] = 1;
        }
        mysql_free_result($mysql_result);

        if (isset($vortex_a[$CurrSS]) && is_array($vortex_a[$CurrSS])) {
            foreach ($vortex_a[$CurrSS] as $k => $v) {
                $CurrSS_a[$k] = $v;
                $CurrSS_a[$k]['type'] = 'Vortex';
                $CurrSS_a['Vortex'] = isset($CurrSS_a['Vortex']) ? $CurrSS_a['Vortex']++ : 1;
            }
            unset($vortex_a[$CurrSS]); // destruction du vortex...
        }
        $this->add_ss($CurrSS, $CurrSS_a);

        fclose($this->fp);
        
        parent::RunJob();
    }

    public function __wakeup() {
        $this->filename = CACHE_PATH . 'map.' . md5($_SESSION['_login'] . $_SESSION['_pass']) . '.js';
        if (file_exists($this->filename)) {
            $this->lastrun = filemtime($this->filename);
            $sqlr = DataEngine::sql('SELECT udate FROM SQL_PREFIX_Coordonnee ORDER BY udate DESC LIMIT 1');
            $sqla = mysql_fetch_array($sqlr);
            $time = $this->lastrun > $sqla['udate'] ? time()+3600: $this->lastrun+1;
            $this->CronPattern = strftime("%M %H %d %m %w", $time);
        } else {
            $this->lastrun = 0;
            $this->CronPattern = '* * * * *';
        }
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
    $cron->AddJob(new job_buttons());
    $cron->AddJob(new job_css());
    $cron->AddJob(new job_map_tooltips());
}

//DataEngine::Grab_Custom_Jobs();
