<?php
/*
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
*/

class DataEngine extends Members {
    /**#@+
     * Variables initialisé, pouvant être utilisé en dehors de la class
     * @access public
     * @var mixed
     */
    static public $lastsql='';
    static public $browser;
    /**#@-*/
    /**#@+
     * Utilisation interne uniquement
     * @access protected
     * @var mixed
     */
    static protected $initialized;
    static protected $sql_spool=array();
    static protected $tpls=array();
    static protected $sqls= array( array('Msec', 'SQL') );
    static protected $conf_loaded;
    static protected $conf_load=array();
    static protected $conf_save=array();
    static protected $settings=array();
    /**#@-*/

    static public function init() {
        if (self::$initialized) return true;

        if ( ! self::$browser) self::$browser = new Browser();

        if (!file_exists(INCLUDE_PATH.'Entete.php'))
            self::jump_install();
        else
            require_once(INCLUDE_PATH.'Entete.php');

        if (!class_exists('Config'))
            self::ErrorAndDie('Invalid file "entete.php" (see entete.dist.php)...',false);

        Config::init();
        Config::DB_Connect();

        return self::minimalinit();
    }

    static public function minimalinit() {
        if (!defined('ROOT_URL')) {
            if (strlen(ROOT_PATH) > strlen($_SERVER['DOCUMENT_ROOT']))
                define( 'ROOT_URL', substr(ROOT_PATH, strlen($_SERVER['DOCUMENT_ROOT'])) );
            else
                define( 'ROOT_URL', '/' );
        }
        define('INCLUDE_URL', ROOT_URL .'Script/' );
        define('IMAGES_URL', ROOT_URL .'Images/' );
        define('ADDONS_URL', ROOT_URL .'addons/' );
        define('TEMPLATE_URL', ROOT_URL .'tpl/' );

        DataEngine::conf_cache('perms');
        DataEngine::conf_cache('config');

        // Fix multiple installations/conflit avec forum ou autre soft utilisant les sessions
        session_set_cookie_params(0,ROOT_URL);

        if (!NO_SESSIONS)
            session_start() or trigger_error('Erreur de session', E_USER_ERROR);
        return (self::$initialized=true);
    }
    static public function jump_install() {
        if (file_exists(ADDONS_PATH.'install/'))
            output::Boink(ADDONS_PATH.'install/');
        elseif (file_exists(ROOT_PATH.'install/'))
            output::Boink(ROOT_PATH.'install/');
        else
            self::ErrorAndDie('Installation non effectué', false);
    }
    /**
     * Fait une requète au serveur sql
     * @param string $query requète sql
     * @param boolean $die Stope en cas d'erreur
     * @param mysqli_driver $link_identifier
     * @return mysqli_result
     */
    static public function sql($query,$die=true,$link_identifier=null) {
        $time = microtime(true);
        $sql = str_replace( 'SQL_PREFIX_', SQL_PREFIX_, $query );
        DataEngine::$lastsql = $sql;
        if (is_null($link_identifier))
            $result = mysql_query($sql) or self::mysql_die(self::parse_backtrace(),$die);
        else
            $result = mysql_query($sql,$link_identifier) or self::mysql_die(self::parse_backtrace(),$die);
        $time = round((microtime(true)-$time)*1000,3);
        if (IN_DEV) self::$sqls[] = array($time,$sql);
        return $result;
    }

    /**
     * @return boolean
     */
    static public function has_sql_spool() {
        return (count(self::$sql_spool)>0 || count(self::$conf_save)>0);
    }
    /**
     * Ajoute une requète sql a effectuer en différé
     * @param string $query requète sql
     */
    static public function sql_spool($query) {
        array_push(self::$sql_spool, $query);
    }

    /**
     * lance le spool de requète sql
     */
    static public function sql_do_spool() {
        if (count(self::$conf_save)>0) {
            foreach(self::$conf_save as $key => $value) {
                self::sql_spool('UPDATE `SQL_PREFIX_Config` SET `value` =\''.sqlesc(serialize($value)).'\' WHERE `key`=\''.$key.'\' LIMIT 1');
            }
            self::$conf_save = array();
        }
        if (count(self::$sql_spool)>0) {
            if (IN_DEV) self::$sqls[] = array(0,'Spooler...');
            foreach (self::$sql_spool as $sql) {
                $time = microtime(true);
                $sql = str_replace( 'SQL_PREFIX_', SQL_PREFIX_, $sql );
                mysql_unbuffered_query($sql);
                $time = round((microtime(true)-$time)*1000,3);
                if (IN_DEV) self::$sqls[] = array($time,$sql);
            }
            if (IN_DEV) self::$sqls[] = array(0,'...Spooler');
            self::$sql_spool = array();
        }
    }

    static public function sql_log() {
        self::sql_do_spool();
        if (IN_DEV) {
            if (count(self::$sqls)>1) {
                $time = 0;
                foreach(self::$sqls as $v) $time += $v[0];
                FB::table((count(self::$sqls)-1).' SQL '.$time.'msec', self::$sqls);
            }
        }
    }
    static protected function mysql_die($debug,$die=true) {
        if ($die) {
            if (IS_IMG) {
                header('Content-Type: image/png');
                header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
                header('Expires: Mon, 16 Jul 2008 04:21:44 GMT'); // HTTP/1.0 Date dans le passé

                require_once(CLASS_PATH.'map.class.php');
                $map = map::getinstance();

                $image 		= imagecreate($map->taille,$map->taille);
                $background_color = imagecolorallocate ($image, 0, 0, 0);
                imagefilledrectangle($image, 0, 0, $map->taille, $map->taille, $background_color);
                $debug_cl = imagecolorallocate ($image, 254, 254, 254);
                map_debug($debug);
                imagepng($image);
                imagedestroy($image);
                exit();
            } else
                header('Content-Type: text/plain;charset=utf-8');
            $linebreak = "\n";
        } else {
            echo '<pre>';
            $linebreak = '<br/>';
            $sql = str_replace( "\n", $linebreak, $debug );
        }
        echo mysql_error().$linebreak.$debug.$linebreak;
        if ($die) die();
    }

    static public function conf_load() {
        if (!self::$conf_loaded) {
            self::$conf_loaded=true;
            $keys = '\''.implode('\',\'',self::$conf_load).'\'';
            $mysql_result = self::sql('SELECT * FROM SQL_PREFIX_Config WHERE `key` IN ('.$keys.')');
            while ($ligne=mysql_fetch_assoc($mysql_result)) {
                if (trim($ligne['key'])=='') continue;
                self::$settings[$ligne['key']] = unserialize(stripslashes($ligne['value']));
            }


            // Initialisations particulières
            {
                if (self::$conf_load['wormhole_cleaning']) {
                    $wormhole_cleaning = self::$settings['wormhole_cleaning'];
                    if (date('w')==0 && $wormhole_cleaning['enabled']) {
                        $runat = mktime(3, 01, 0, date("m"), date("d"), date("Y"));
                        $now   = time();
                        if ($now > $runat && $runat > $wormhole_cleaning['lastrun']) {
                            self::sql('DELETE FROM SQL_PREFIX_Coordonnee WHERE `TYPE` = 1 AND `INACTIF` = 1');
                            self::sql('UPDATE SQL_PREFIX_Coordonnee SET `INACTIF` = 1 WHERE `TYPE` = 1');
                            self::sql('INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),\'vortex_reset_by:'.$_SESSION['_login'].'\' ,\''.Get_IP().'\')');
                            $wormhole_cleaning['lastrun'] = $now;
                            self::conf_update('wormhole_cleaning', $wormhole_cleaning);
                            self::sql_do_spool(); // Mettre à jour maintenant, pas que deux membres le fasse a 1/2sec d'intervalle.
                            addons::getinstance()->VortexCleaned();
                        }
                    }
                }
                if (self::$conf_load['config'] && !is_array(self::$settings['config'])) {
                    $conf                      = array();
                    $conf['ForumLink']         = '';
                    $conf['DefaultGrade']      = 3;
                    $conf['CanRegister']       = 0;
                    $conf['MyEmpire']          = '';
                    $conf['Parcours_Max_Time'] = 0;
                    $conf['Parcours_Nearest']  = 5;
                    $conf['eude_srv']          = '';
                    $conf['version']           = self::Get_Version();
                    $conf['closed']            = 0;
                    self::conf_add('config', $conf);

                }

            }
            self::$conf_load=array();
        }
        return self::$conf_loaded;
    }

    static public function config($key) {
        if (!self::$conf_loaded) self::conf_load();

        if (isset (self::$settings[$key]))
            return self::$settings[$key];
        else
            return false;
    }
    
    static public function config_key($key, $subkey) {
        if (!self::$conf_loaded) self::conf_load();

        if (isset (self::$settings[$key]))
            return self::$settings[$key][$subkey];
        else
            return false;
    }
    /**
     * Met en liste une demande de config
     * @param string $key Clé identifiant une config
     */
    static public function conf_cache($key) {
        self::$conf_load[$key]=$key;
        self::$conf_loaded=false;
    }
    /**
     * Met a jour à la fin du script une config donnée
     * @param string $key Clé identifiant une config
     * @param mixed $value Valeur de cette config
     */
    static public function conf_update($key, $value) {
        self::$settings[$key] = $value;
        self::$conf_save[$key] = $value;
    }
    /**
     * Ajoute une config donnée au cache (dans le spool !)
     * @param string $key Clé identifiant une config
     * @param mixed $value Valeur de cette config
     */
    static public function conf_add($key, $value) {
        self::$settings[$key] = $value;
        self::sql_spool('INSERT INTO SQL_PREFIX_Config (`key`,`value`) VALUES (\''.$key.'\',\''.sqlesc(serialize($value)).'\')');
    }
    static public function conf_del($key) {
        unset(self::$settings[$key]);
        self::sql_spool('DELETE FROM SQL_PREFIX_Config WHERE `key`=\''.$key.'\' LIMIT 1');
    }

    static public function debug() {
        // 		header("Content-Type: text/plain;charset=utf-8");
        die(str_replace( ROOT_PATH, '/', DataEngine::parse_backtrace(debug_backtrace()) ) );
    }

    static public function parse_backtrace($row=-1) {
        $output="";
        if ($row==-1)
            $raw=debug_backtrace();
        else
            $raw=array_reverse(debug_backtrace());
        foreach($raw as $id => $entry) {
            if ($row != -1 && $id != $row) continue;
            $entry['file'] = str_replace( ROOT_PATH, "", $entry['file'] );
            $entry['args'] = str_replace( ROOT_PATH, "", $entry['args'] );
            $output.="\nFile: ".$entry['file']." (Line: ".$entry['line'].")\n";
            $output.="Function: ".$entry['function']."\n";
            $output.="Args: ".implode(", ", $entry['args'])."\n";
        }
        return $output;
    }

    static public function throw_error($msg, $e_level=E_USER_NOTICE) {
        $msg.="...Debug: ".self::parse_backtrace(1);
        trigger_error( $msg, $e_level);
    }

    /**
     * @link http://app216.free.fr/eu2/tracker/view.php?id=51
     * @param  string utf8
     */
    static function xml_fix51($value) {
        $fix51 = array("\x00" => "&#0;", "\x01" => "&#1;", "\x02" => "&#2;", "\x03" => "&#3;", "\x04" => "&#4;", "\x05" => "&#5;", "\x06" => "&#6;", "\x07" => "&#7;", "\x08" => "&#8;", "\x09" => "&#9;", "\x0a" => "&#10;", "\x0b" => "&#11;", "\x0c" => "&#12;", "\x0d" => "&#13;", "\x0e" => "&#14;", "\x0f" => "&#15;", "\x10" => "&#16;", "\x11" => "&#17;", "\x12" => "&#18;", "\x13" => "&#19;", "\x14" => "&#20;", "\x15" => "&#21;", "\x16" => "&#22;", "\x17" => "&#23;", "\x18" => "&#24;", "\x19" => "&#25;", "\x1a" => "&#26;", "\x1b" => "&#27;", "\x1c" => "&#28;", "\x1d" => "&#29;", "\x1e" => "&#30;", "\x1f" => "&#31;");
        return strtr($value, $fix51);
    }
    static function utf_strip($value) {
        $strip = array("\x00" => "", "\x01" => "", "\x02" => "", "\x03" => "", "\x04" => "", "\x05" => "", "\x06" => "", "\x07" => "", "\x08" => "", "\x09" => "", "\x0a" => "", "\x0b" => "", "\x0c" => "", "\x0d" => "", "\x0e" => "", "\x0f" => "", "\x10" => "", "\x11" => "", "\x12" => "", "\x13" => "", "\x14" => "", "\x15" => "", "\x16" => "", "\x17" => "", "\x18" => "", "\x19" => "", "\x1a" => "", "\x1b" => "", "\x1c" => "", "\x1d" => "", "\x1e" => "", "\x1f" => "");
        return strtr($value, $strip);
    }

    static public function _set_tpl($tpl, $obj) {
        //        FB::info($tpl,'Template loaded');
        self::$tpls[$tpl] = $obj;
    }
    static public function _tpl_defined($tpl) {
        return isset(self::$tpls[$tpl]);
    }

    static public function tpl($tpl) {
        if ($tpl=="") return reset(self::$tpls);

        if (! self::$tpls[$tpl] )
            trigger_error('global template "tpl('.$tpl.')" called before defined (update needed)', E_USER_ERROR);
        return self::$tpls[$tpl];
    }
    static public function ErrorAndDie($error, $link=true) {
        $out = <<<ead
            <br/><br/>
	<center>
		<a href='%ROOT_URL%'>
		<font color=red><i>
                {$error}
		</i></font></a>
	</center>
ead;
        if (!$link) $out = str_replace('%ROOT_URL%', '#', $out);
        output::_DoOutput($out);
    }

    static public function format_number($number, $full_num=false) {
        if ($number<0) {
            return 'n/a';
        } elseif ($number==0) {
            return '-';
        } elseif ($number >= 1000000 && !$full_num) {
            return number_format($number/1000000,2,',',' ').' M';
        } else {
            return number_format($number,0,',',' ');
        }
    }

    static public function strip_number($number) {
        if ($number=='n/a') return -1;
        return intval(preg_replace('/[^0-9\-]*/', '', $number));
    }

    static public function a_shiplist() {
        $lng = language::getinstance()->GetLngBlock('dataengine');
        return $lng['shiplist'];
    }
    static public function a_batiments() {
        $lng = language::getinstance()->GetLngBlock('dataengine');
        return $lng['batiments'];
    }
    static public function a_ressources() {
        $lng = language::getinstance()->GetLngBlock('dataengine');
        $i = 0;
        $Ressource[$i]['Nom']   = $lng['Titane'];
        $Ressource[$i]['Field'] = 'Titane';
        $Ressource[$i]['Image'] = IMAGES_URL.'Titane.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Cuivre'];
        $Ressource[$i]['Field'] = 'Cuivre';
        $Ressource[$i]['Image'] = IMAGES_URL.'Cuivre.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Fer'];
        $Ressource[$i]['Field'] = 'Fer';
        $Ressource[$i]['Image'] = IMAGES_URL.'Fer.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Aluminium'];
        $Ressource[$i]['Field'] = 'Aluminium';
        $Ressource[$i]['Image'] = IMAGES_URL.'Aluminium.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Mercure'];
        $Ressource[$i]['Field'] = 'Mercure';
        $Ressource[$i]['Image'] = IMAGES_URL.'Mercure.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Silicium'];
        $Ressource[$i]['Field'] = 'Silicium';
        $Ressource[$i]['Image'] = IMAGES_URL.'Silicium.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Uranium'];
        $Ressource[$i]['Field'] = 'Uranium';
        $Ressource[$i]['Image'] = IMAGES_URL.'Uranium.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Krypton'];
        $Ressource[$i]['Field'] = 'Krypton';
        $Ressource[$i]['Image'] = IMAGES_URL.'Krypton.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Azote'];
        $Ressource[$i]['Field'] = 'Azote';
        $Ressource[$i]['Image'] = IMAGES_URL.'Azote.png';
        $i++;
        $Ressource[$i]['Nom']   = $lng['Hydrogene'];
        $Ressource[$i]['Field'] = 'Hydrogene';
        $Ressource[$i]['Image'] = IMAGES_URL.'Hydrogene.png';
        return $Ressource;
    }
    static public function a_race_ressources($race) {
        $lng = language::getinstance()->GetLngBlock('dataengine');
        $lng = $lng['races'];
        $data = array(
                $lng['Cyborg'] => array('Titane'=>8, 'Cuivre'=>16.5, 'Fer'=>11,'Aluminium'=>12.5,'Mercure'=>10,'Silicium'=>12,'Uranium'=>5,'Krypton'=>9,'Azote'=>15,'Hydrogene'=>1),
                $lng['Jamozoid'] => array('Titane'=>12.5, 'Cuivre'=>26, 'Fer'=>10, 'Aluminium'=>12, 'Mercure'=>9, 'Silicium'=>5, 'Uranium'=>8, 'Krypton'=>11, 'Azote'=>16.5, 'Hydrogene'=>1),
                $lng['Magumian'] => array('Titane'=>15, 'Cuivre'=>12.5, 'Fer'=>16.5, 'Aluminium'=>5, 'Mercure'=>8, 'Silicium'=>9, 'Uranium'=>11, 'Krypton'=>10, 'Azote'=>12, 'Hydrogene'=>1),
                $lng['Human'] => array('Titane'=>16.5, 'Cuivre'=>12, 'Fer'=>8, 'Aluminium'=>11, 'Mercure'=>15, 'Silicium'=>10, 'Uranium'=>12.5, 'Krypton'=>5, 'Azote'=>9, 'Hydrogene'=>1),
                $lng['Mosorian'] => array('Titane'=>11, 'Cuivre'=>10, 'Fer'=>9, 'Aluminium'=>16.5, 'Mercure'=>5, 'Silicium'=>8, 'Uranium'=>12, 'Krypton'=>15, 'Azote'=>12.5, 'Hydrogene'=>1),
                $lng['Ozoid'] => array('Titane'=>12, 'Cuivre'=>9, 'Fer'=>12.5, 'Aluminium'=>8, 'Mercure'=>11, 'Silicium'=>15, 'Uranium'=>10, 'Krypton'=>16.5, 'Azote'=>5, 'Hydrogene'=>1),
                $lng['Plentropian'] => array('Titane'=>10, 'Cuivre'=>8, 'Fer'=>5, 'Aluminium'=>15, 'Mercure'=>16.5, 'Silicium'=>12.5, 'Uranium'=>9, 'Krypton'=>12, 'Azote'=>11, 'Hydrogene'=>1),
                $lng['Weganian'] => array('Titane'=>9, 'Cuivre'=>5, 'Fer'=>15, 'Aluminium'=>10, 'Mercure'=>12, 'Silicium'=>11, 'Uranium'=>16.5, 'Krypton'=>12.5, 'Azote'=>8, 'Hydrogene'=>1),
                $lng['Zuup'] => array('Titane'=>5, 'Cuivre'=>11, 'Fer'=>12, 'Aluminium'=>9, 'Mercure'=>12.5, 'Silicium'=>16.5, 'Uranium'=>15, 'Krypton'=>8, 'Azote'=>10, 'Hydrogene'=>1),
        );
        if (isset($data[$race]))
            return $data[$race];
        else if ($race=='')
            return '';
        else
            return $data;

    }
    /**
     * @return string version du Data Engine
     */
    static public function Get_Version() {
        if (DE_DEMO)
            return '1.4.2 démo';
        elseif (IN_DEV)
            return 'svn-'.time();
        else
            return '1.4.2';
    }
}

class Members {
    static public function Perms() {
        $cxx=array();
        $cxx['CARTOGRAPHIE'] = AXX_MEMBER;
        $cxx['CARTOGRAPHIE_SEARCH'] = AXX_MEMBER;
        $cxx['CARTOGRAPHIE_PLAYERS'] = AXX_MEMBER;
        $cxx['CARTOGRAPHIE_PLANETS'] = AXX_MEMBER;
        $cxx['CARTOGRAPHIE_ASTEROID'] = AXX_MEMBER;
        $cxx['CARTOGRAPHIE_PNJ'] = AXX_MEMBER;
        $cxx['CARTOGRAPHIE_DELETE'] = AXX_SUPMODO;
        $cxx['CARTOGRAPHIE_EDIT'] = AXX_MODO;
        $cxx['CARTOGRAPHIE_GREASE'] = AXX_MEMBER;

        $cxx['CARTE'] = AXX_MEMBER;
        $cxx['CARTE_JOUEUR'] = AXX_MEMBER;
        $cxx['CARTE_SHOWEMPIRE'] = AXX_MEMBER;
        $cxx['CARTE_SEARCH'] = AXX_MEMBER;

        $cxx['PERSO'] = AXX_MEMBER;
        $cxx['PERSO_RESEARCH'] = AXX_VALIDATING;
        $cxx['PERSO_OWNUNIVERSE'] = AXX_MEMBER;
        $cxx['PERSO_OWNUNIVERSE_READONLY'] = AXX_ADMIN;

        $cxx['MEMBRES_HIERARCHIE'] = AXX_MEMBER;
        $cxx['MEMBRES_NEW'] = AXX_ADMIN; // inclus les grades...
        $cxx['MEMBRES_EDIT'] = AXX_ADMIN;
        $cxx['MEMBRES_STATS'] = AXX_MEMBER;
        $cxx['MEMBRES_NEWPASS'] = AXX_ROOTADMIN;
        $cxx['MEMBRES_DELETE'] = AXX_ROOTADMIN;
        $cxx['MEMBRES_ADMIN_MAP_COLOR'] = AXX_ROOTADMIN;
        $cxx['MEMBRES_ADMIN_LOG'] = AXX_ROOTADMIN;
        $cxx['MEMBRES_ADMIN'] = AXX_ROOTADMIN;

        DataEngine::conf_add('perms', $cxx);
        // demande les addons, à eux de s'incruster dans le dataengine...
        addons::getinstance();
    }

    static public function s_perms() {
        $axx = language::getinstance()->GetLngBlock('dataengine');
        return $axx['axx'];
    }

    /**
     * Liste les niveau d'accès avec leurs valeur humainement lisible.
     * @return array
     */
    static public function s_cperms() {

        $cxx = array();
        // si clé numérique => séparateur nominatif.
        $tmp = language::getinstance()->GetLngBlock('dataengine');
        foreach($tmp['cxx'] as $k => $v) $cxx[$k] = $v;

        $tmp=addons::getinstance()->CustomPerms();
        foreach($tmp as $k => $v) $cxx[$k] = $v;

        return $cxx;
        ;
    }
    /**
     * @return AXX_*
     */
    static public function CurrentPerms() {
        return $_SESSION['_Perm'];
    }
    /**
     * @param integer $NeededAXX Constante du niveau d'accès
     * @return boolean
     */
    static public function CheckPermsKey($PermsKey) {
        $perms = DataEngine::config('perms');
        return (isset($perms[$PermsKey]));
    }

    static public function CheckPermsKeyAdd($PermsKey, $value) {
        $perms = DataEngine::config('perms');
        $perms[$PermsKey] = $value;
        DataEngine::conf_update('perms', $perms);
    }
    /**
     * @param integer $NeededAXX Constante du niveau d'accès
     * @return boolean
     */
    static public function CheckPerms($NeededAXX=AXX_MEMBER) {
        if (is_numeric($NeededAXX))
            return $_SESSION['_Perm']>=$NeededAXX;

        $perms = DataEngine::config('perms');
        if (isset($perms[$NeededAXX]))
            return $_SESSION['_Perm']>=$perms[$NeededAXX] && $perms[$NeededAXX] != AXX_DISABLED;
        else {
            trigger_error('CXX not found '.$NeededAXX.'. option disabled for all instead. Verify installation, or configure it now by admin panel', E_USER_WARNING);
            return false;
        }
    }
    /**
     * @param integer $NeededAXX Constante du niveau d'accès
     */
    static public function CheckPermsOrDie($NeededAXX=AXX_MEMBER) {
        if (!self::CheckPerms($NeededAXX)) {
            $lng = language::getinstance()->GetLngBlock('dataengine');
            $perm = self::s_perms();
            $str = sprintf($lng['minimalpermsneeded'], $perm[$NeededAXX]);
            $out = <<<PERM
<br/><br/>
	<center>
		<a href='%ROOT_URL%'>
		<font color=red><i>
                    {$str}
		</i></font></a>
	</center>
PERM;
            output::_DoOutput($out);
        }
    }
    static public function NoPermsAndDie() {
        $lng = language::getinstance()->GetLngBlock('dataengine');
        DataEngine::ErrorAndDie($lng['nopermsanddie']);
    }
    /**
     * Supprimer un joueur ?
     * @param string $user Nom d'utilisateur
     */
    static public function DeleteUser($user) {
        DataEngine::sql('DELETE FROM SQL_PREFIX_Membres WHERE Joueur=\''.$user.'\'');
        DataEngine::sql('DELETE FROM SQL_PREFIX_Users WHERE Login=\''.$user.'\'');
        DataEngine::sql('DELETE FROM SQL_PREFIX_ownuniverse WHERE Utilisateur=\''.$user.'\'');
        addons::getinstance()->DeleteUser($user);
    }
    /**
     * Ajout d'un joueur
     * @param string $user Nom du joueur
     * @param md5 $pass pass en md5
     * @param integer $axx niveau d'accès souhaité
     * @param integer $points nombre de points
     * @param integer $grade id du grade dans l'empire
     */
    static public function NewUser($user, $md5pass, $axx=AXX_GUEST, $points=0, $grade=3) {
        DataEngine::sql('INSERT INTO SQL_PREFIX_Users VALUES(\''.$user.'\',\''.$md5pass.'\','.$axx.')');
        DataEngine::sql('INSERT INTO SQL_PREFIX_Membres(Joueur,Points,Date,Grade) '
                .'VALUES(\''.$user.'\',\''.$points.'\',now(),'.$grade.')');
        addons::getinstance()->NewUser($user);
    }
}

interface iDataEngine_Config {
    static public function DB_Connect();
    static public function init();
//    static public function GetForumLink();
//    static public function GetDefaultGrade();
//    static public function GetMyEmpire();
//
//    static public function Parcours_Max_Time();
//    static public function Parcours_Nearest();
//
//    static public function eude_srv();
}