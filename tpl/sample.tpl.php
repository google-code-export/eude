<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_sample extends output {
    protected $BASE_FILE = '';

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'sample.php';

        parent::__construct();
    }

    public function AddRow($ligne) {
        $this->PushOutput($out);
    }

    public function Setheader() {
        $this->curtpl = 'SetRowtpl';        
        
        $this->currow = <<<ROW
    <tr class="%%class%%">
        <td colspan="2">%%Title header%%</td>
    </tr>
ROW;
    }

    private function SetRowtpl() {
        $this->currow = <<<ROW
    <tr class="%%class%%">
        <td>%%Name%%</td>
        <td>%%value%%</td>
    </tr>
ROW;
    }
   
    /**
     * Génère la page
     * @param boolean,array $include_menu Inclure le menu ? (voir son propre menu)
     * @param boolean $include_header Inclure l'entete ?
     */
    public function DoOutput($include_menu=true, $include_header=true) {
        $this->PushOutput('');
        parent::DoOutput($include_menu, $include_header);
    }
    /**
     * Next row, same tpl
     */
    public function PushRow() {
        $this->PushOutput($this->currow);
        call_user_func(array($this,$this->curtpl), $this);
    }
    /**
     *
     * @return tpl_sample
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}