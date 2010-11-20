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
//    private $strlen, $stripos, $strripos, $substr;

    /**
     *
     * @param string $data données brut
     * @param string $label clé a trouver
     * @return string valeur trouvé
     */
    public function GetValueByLabel($data, $label) {
        $result = '';
        $length = p_strlen($label);
        $start = p_stripos($data, $label, 0);
        $end = p_stripos($data, "\n", $start + $length);

        if ($start !== false && $end !== false)
            $result = trim(p_substr($data, $start + $length, $end - $start - $length));

        return $result;
    }

    public function GetValueByLabelInverted($data, $label) {
        $result = '';
        $end = p_stripos($data, $label, 0);
        $part1 = p_substr($data, 0, $end);
        $start = p_strripos($part1, "\n", 0);

        if ($start !== false && $end !== false)
            $result = trim(p_substr($data, $start, $end - $start));

        return $result;
    }

    /**
     *
     * @param string $data
     * @param string $label
     * @return boolean
     */
    public function LabelExist($data, $label) {

        if (p_stripos($data, $label, 0) !== false)
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
        $l = p_strlen($from);
        $f = p_stripos($data, $from, 0);
        $t = p_stripos($data, $to, $l + $f);

        if ($f === false)
            $inner = $data;
        elseif ($t === false)
            $inner = p_substr($data, $f + $l, -1);
        else
            $inner = p_substr($data, $f + $l, $t - $f - $l);

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