<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
if (!SCRIPT_IN)
    die('Need by included');

class tpl_upgrade extends output {

    public function __construct() {
        parent::__construct();
    }

    public function Setheader() {
        $this->curtpl = 'SetRowtpl';

        $this->currow = <<<ROW
   <table class="table_center table_nospacing size500">
    <tr class="text_center color_bigheader">
        <td>%%value%%</td>
    </tr>
ROW;
    }

    private function SetRowtpl() {
        $this->currow = <<<ROW
    <tr class="color_row0 text_center">
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
        $this->PushRow();
        $this->PushOutput('</table>');
        parent::DoOutput($include_menu, $include_header);
    }

    /**
     * Next row, same tpl
     */
    public function PushRow() {
        $this->PushOutput($this->currow);
        call_user_func(array($this, $this->curtpl), $this);
    }

    /**
     *
     * @return tpl_upgrade
     */
    static public function getinstance() {
        if (!DataEngine::_tpl_defined(get_class()))
            DataEngine::_set_tpl(get_class(), new self());

        return DataEngine::tpl(get_class());
    }

}