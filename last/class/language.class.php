<?php
/*
 * @author Alex10336
 * Dernière modification: $Id: login.php 29 2010-02-06 00:39:19Z Alex10336 $
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 */
class language {
    static protected $instance;
    protected $lngs;

    public function  __construct() {
        if (!defined('LNG_CODE')) define('LNG_CODE','fr');
        define ('LNG_PATH', TEMPLATE_PATH.'lng'.DIRECTORY_SEPARATOR.LNG_CODE.DIRECTORY_SEPARATOR);
        if (!file_exists(LNG_PATH))
            trigger_error(sprintf('Language pack "%s" not found', LNG_PATH), E_USER_ERROR);
        $this->lngs = array();
    }

    /**
     * Charge et retourne la partie demandé.
     * @param string $who
     * @return array
     */
    function GetLngBlock($who) {
        if (is_array($this->lngs[$who])) {
            if (IN_DEV) FB::info($who.'('.count($this->lngs[$who]).')', 'i18n, cache');
            return $this->lngs[$who];
        }

        $lng_file = LNG_PATH.$who.'.lng.php';
        if (!file_exists($lng_file))
            trigger_error(sprintf('Language block "%s" not found in pack "%s"', $who, $lng_file), E_USER_ERROR);

        include($lng_file);
        $this->lngs[$who] = $lng;
        if (IN_DEV) FB::info($who.'('.count($lng).')', 'i18n');
        return $lng;
    }

    /**
     * @return language
     */
    static public function getinstance() {
        if ( ! self::$instance )
            self::$instance = new self();

        return self::$instance;
    }
}