<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * @since 1.4.1
 */
class parser {

    static protected $instance;
    private $data_sep;
    private $strlen, $stripos, $strripos, $substr;

    /**
     *
     * @param string $data données brut
     * @param string $label clé a trouver
     * @return string valeur trouvé
     */
    public function GetValueByLabel($data, $label) {
        $result = '';
        $length = call_user_func($this->strlen, $label);
        $start = call_user_func($this->stripos, $data, $label, 0);
        $end = call_user_func($this->stripos, $data, "\n", $start + $length);

        if ($start !== false && $end !== false)
            $result = trim(call_user_func($this->substr, $data, $start + $length, $end - $start - $length));

        return $result;
    }

    public function GetValueByLabelInverted($data, $label) {
        $result = '';
        $end = call_user_func($this->stripos, $data, $label, 0);
        $part1 = call_user_func($this->substr, $data, 0, $end);
        $start = call_user_func($this->strripos, $part1, "\n", 0);

        if ($start !== false && $end !== false)
            $result = trim(call_user_func($this->substr, $data, $start, $end - $start));

        return $result;
    }

    /**
     *
     * @param string $data
     * @param string $label
     * @return boolean
     */
    public function LabelExist($data, $label) {

        if (call_user_func($this->stripos, $data, $label, 0) !== false)
            return true;

        return false;
    }

    /**
     * Récupération d'une partie d'un tableau sur demande.
     * @param array $data
     * @param string $from
     * @param string $to
     * @return array
     */
    public function Slice($data, $from, $to=null) {
        $from_pos = array_search($from, $data);
        $to_pos = array_search($to, $data);
        if ($from_pos === false)
            $slice = $data;
        elseif ($to_pos === false)
            $slice = array_slice($data, $from_pos + 1);
        else
            $slice = array_slice($data, $from_pos + 1, $to_pos - $from_pos - 1);
        return $slice;
    }

    function GetInner($data, $from, $to='') {
        $l = call_user_func($this->strlen, $from);
        $f = call_user_func($this->stripos, $data, $from, 0);
        $t = call_user_func($this->stripos, $data, $to, $l + $f);

        if ($f === false)
            $inner = $data;
        elseif ($t === false)
            $inner = call_user_func($this->substr, $data, $f + $l, -1);
        else
            $inner = call_user_func($this->substr, $data, $f + $l, $t - $f - $l);

        return trim($inner);
    }

    /**
     * trim all entry in array
     * @param array $array
     */
    public function cleaning_array($array) {
        foreach ($array as $k => $v) {
            $v = trim($v);
            if ($v == '')
                unset($array[$k]);
            else
                $array[$k] = trim($v);
        }
        return $array;
    }

    public function __construct() {
        if (DataEngine::$browser->getBrowser() == Browser::BROWSER_IE)
            $this->data_sep = '  '; // IE
        else
            $this->data_sep = "\t\t"; // gecko, 'Webkit'
            // Rétrocompatibilité avec php < 5.2.0
            // Risque de bugger quand même...
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            $this->strlen = 'strlen';
            $this->stripos = 'stripos';
            $this->strripos = 'stripos';
            $this->substr = 'substr';
        } else {
            mb_internal_encoding('utf-8');
            $this->strlen = 'mb_strlen';
            $this->stripos = 'mb_stripos';
            $this->strripos = 'mb_strripos';
            $this->substr = 'mb_substr';
        }
    }

    /**
     * @return parser
     */
    static public function getinstance() {
        if (!self::$instance)
            self::$instance = new self();

        return self::$instance;
    }

}
