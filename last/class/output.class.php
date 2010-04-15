<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * @abstract
 */
abstract class output {

    protected $debug='';
    protected $output='';
    /**
     * version du DataEngine
     * @access public
     * @var string
     */
    public $version;
    /**
     * Titre de la page
     * @access public
     * @var string
     */
    public $page_title;
    /**
     * Lien du fichier css
     * @var string
     */
    public $css_file;

    /**
     * Generic template routines
     * @since 1.4.2
     */
    protected $currow = '';
    protected $curtpl;

    public function __construct() {
        $this->version = DataEngine::Get_Version();
        $this->page_title = 'EU2: Data Engine ('.$this->version.')';
        $this->css_file = INCLUDE_URL.'EU2DE.css';
    }

    static public function Messager($msg) {
        if (preg_replace('/<[^>]*>/', '', $msg) == '') return false;
        if (!is_array($_SESSION['messager'])) $_SESSION['messager'] = array();
        array_push($_SESSION['messager'], $msg);
        return true;
    }
    /**
     * Redirige vers une autre page
     * @param string $url
     */
    static public function Boink($data='./', $messager='') {
        if ($messager!='') self::Messager($messager);
        
        DataEngine::sql_log();
        // Remplacement de variable...
        $data = str_replace('%ROOT_URL%', ROOT_URL, $data);
        $data = str_replace('%INCLUDE_URL%', INCLUDE_URL, $data);
        $data = str_replace('%IMAGES_URL%', IMAGES_URL, $data);
        $data = str_replace('%TEMPLATE_URL%', TEMPLATE_URL, $data);
        $data = str_replace('%ADDONS_URL%', ADDONS_URL, $data);
        header('location: '.$data);
        exit(0);
    }
    /**
     * Génère une liste déroulante générique
     * @param array $array Tableau de donnée ($k=>$v)
     * @param integer $id id selectionné ($id=$k)
     * @param string $key clé pour les formulaires ($key$k)
     * @param boolean $flip inverser les clé/valeurs ($v=>$k $id=$v)
     */
    public function SelectOptions($array,$id,$key='',$flip=false) {
        if ($flip) $array = array_flip($array);
        foreach($array as $k => $v) {
            $selected = ($flip) ? (($v==$id) ? ' selected':'') : (($k==$id) ? ' selected':'');
            if ($key!='')
                $this->PushOutput("\t\t<option value='$k' name='$key$k'$selected>$v</option>\n");
            else
                $this->PushOutput("\t\t<option value='$k'$selected>$v</option>\n");
        }
    }

    /**
     * Returne une liste déroulante générique
     * @param array $array Tableau de donnée ($k=>$v)
     * @param integer $key id selectionné ($selected=$k)
     * @param boolean $flip inverser les clé/valeurs ($v=>$k $id=$v)
     */
    public function SelectOptions2($array,$key,$flip=false) {
        $result='';
        if ($flip) $array = array_flip($array);
        foreach($array as $k => $v) {
            $selected = ($flip) ? (($v==$key) ? ' selected':'') : (($k==$key) ? ' selected':'');
            $result .= "\t\t<option value='$k'$selected>$v</option>\n";
        }
        return $result;
    }

    public function AddToRow($value, $key) {
        $this->currow = str_replace("%%$key%%", $value, $this->currow);
    }
//    public function PushRow() {
//        $this->PushOutput($this->currow);
//        call_user_func(array($this,$this->curtpl), $this);
//    }
    /**
     * Ajout de donnée au début de document
     * @param mixed $value
     * @return output
     *
     */
    public function ShiftOutput($value) {
        $this->output = $value.$this->output;
        return $this;
    }
    /**
     * Ajout de donnée en fin de document
     * @param mixed $value
     * @return output
     */
    public function PushOutput($value) {
        $this->output .= $value;
        return $this;
    }
    /**
     * Ajout de donnée au 'debug'
     * @param mixed $value
     * @return output
     */
    public function ShiftDebug($value) {
        if (is_array($value)) $value = print_r($value,true);
        $this->debug = $value.$this->debug;
        return $this;
    }
    /**
     * Ajout de donnée 'debug'
     * @param mixed $value
     * @return output
     */
    public function PushDebug($value) {
        if (is_array($value)) $value = print_r($value,true);
        $this->debug .= $value;
        return $this;
    }
    /**
     * @return output
     */
    public function FlushDebug() {
        if (!IN_DEV) return $this;
        $this->PushOutput('<pre><font color=white>'.$this->debug.'</pre>');
        $this->debug ='';
        return $this;
    }
    /**
     * @return mixed Données du buffer
     */
    public function GetOutput() {
        return $this->output;
    }
    /**
     * Génère la page
     * @param boolean,array $include_menu Inclure le menu ?
     * @param boolean $include_header Inclure l'entete ?
     */
    public function DoOutput($include_menu=true, $include_header=true) {
        if (!USE_AJAX) {
            if ($include_menu) {
                include_once(TEMPLATE_PATH.'menu.tpl.php');
                if (is_array($include_menu))
                    $menu = addons::getinstance()->Parse_Menu($include_menu);
                else
                    $menu = addons::getinstance()->Parse_Menu(tpl_menu::DefaultMenu());
                $this->ShiftOutput(tpl_menu::Gen_Menu($menu));
            }

            if ($include_header) {
                include_once(TEMPLATE_PATH.'header.tpl.php');
                $this->ShiftOutput(tpl_header::Get_Header());
            }
        }
        output::_DoOutput($this->output);
    }

    static public function _DoOutput($data) {
        DataEngine::sql_log();

        // Remplacement de variable...
        $data = str_replace('%ROOT_URL%', ROOT_URL, $data);
        $data = str_replace('%INCLUDE_URL%', INCLUDE_URL, $data);
        $data = str_replace('%IMAGES_URL%', IMAGES_URL, $data);
        $data = str_replace('%TEMPLATE_URL%', TEMPLATE_URL, $data);
        $data = str_replace('%ADDONS_URL%', ADDONS_URL, $data);
        $data = str_replace('%LNG_URL%', TEMPLATE_URL.'lng'.DIRECTORY_SEPARATOR.LNG_CODE.DIRECTORY_SEPARATOR, $data);
        if (!USE_AJAX) tpl_header::messager($data, $_SESSION['messager']);
        
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 16 Jul 2008 04:21:44 GMT'); // HTTP/1.0 Date dans le passé

        if (DEBUG_PLAIN) {
            header('Content-Type: text/plain;charset=utf-8');
            echo $data;
        } else if (USE_AJAX) {
                header('Content-Type: text/xml;charset=utf-8');
                echo $data;
            } else if (IS_IMG) {
                    if (DEBUG_IMG) {
                        header('Content-Type: text/plain;charset=utf-8');
                        echo $data;
                    } else {
                        header('Content-Type: image/png');
                        imagepng($GLOBALS['image']);
                    }
                    imagedestroy($GLOBALS['image']);
                } else {
                    header('Content-Type: text/html;charset=utf-8');
                    echo $data;
                }
        exit(0);
    }

}