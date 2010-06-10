<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_troops extends output {
    protected $BASE_FILE = '';
    private $lng_main;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'pillage.php';
        $this->lng_main = DataEngine::a_ressources();
        parent::__construct();
    }

    public function AddRow($ligne) {
        $this->PushOutput($out);
    }

    public function Setheader() {
        $this->currow = <<<ROW
   <table class="table_center table_nospacing">
    <tr class="%%class%%">
        <td colspan="7">Légende sur deux lignes.</td>
    </tr>
    <tr class="color_row1">
        <td class="color_header">&nbsp;</td>
        <td class="spacing_row1">Type</td>
        <td class="spacing_row1">Date</td>
        <td class="spacing_row1">Coordonnées</td>
        <td class="spacing_row1">Attaquants</td>
        <td class="spacing_row1">Défenseurs</td>
        <td class="spacing_row">Pertes</td>
    </tr>
 <tr><td colspan="7">
     <table class="table_nospacing" width="100%">
    <tr class="color_row0">
        <td class="spacing_row0">Joueur</td>
        <td class="spacing_row0">Date</td>
        <td class="spacing_row0">{$this->lng_main[0]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[1]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[2]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[3]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[4]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[5]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[6]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[7]['Nom']}</td>
        <td class="spacing_row0">{$this->lng_main[8]['Nom']}</td>
        <td class="spacing_row">{$this->lng_main[9]['Nom']}</td>
    </tr></td>
    </table>
    </tr>
    <tr class="color_bg">
        <td colspan="7" height="1"></td>
    </tr>
    <tr class="%%class%%">
        <td colspan="7">%%Title header%%</td>
    </tr>
ROW;
       $this->curtpl = 'SetBattleRow';
    }

    public function SetBattleRow() {
        $this->currow = <<<ROW
    <tr class="color_header">
        <td colspan="7" height="1"></td>
    </tr>
    <tr class="color_row%%rowid%%">
        <td class="color_header">&nbsp;</td>
        <td class="spacing_row%%rowid%%">%%Type%%</td>
        <td class="spacing_row%%rowid%%">%%Date%%</td>
        <td class="spacing_row%%rowid%%">%%Coords%%</td>
        <td class="spacing_row%%rowid%%">%%Attaquants%%</td>
        <td class="spacing_row%%rowid%%">%%Defenseurs%%</td>
        <td class="spacing_row">%%Pertes%%</td>
    </tr>
ROW;
       $this->curtpl = 'SetBattleRow';
    }

    public function SetlogRow_header() {
        $this->currow = <<<ROW
 <tr><td colspan="7">
     <table class="table_nospacing" width="100%">
ROW;
    }
    
    public function SetlogRow() {
        $this->currow .= <<<ROW
    <tr class="color_row%%class%%">
        <td class="spacing_row%%class%%">%%Player%%</td>
        <td class="spacing_row%%class%%">%%date%%</td>
        <td class="spacing_row%%class%%">%%ress0%%</td>
        <td class="spacing_row%%class%%">%%ress1%%</td>
        <td class="spacing_row%%class%%">%%ress2%%</td>
        <td class="spacing_row%%class%%">%%ress3%%</td>
        <td class="spacing_row%%class%%">%%ress4%%</td>
        <td class="spacing_row%%class%%">%%ress5%%</td>
        <td class="spacing_row%%class%%">%%ress6%%</td>
        <td class="spacing_row%%class%%">%%ress7%%</td>
        <td class="spacing_row%%class%%">%%ress8%%</td>
        <td class="spacing_row">%%ress9%%</td>
    </tr>
ROW;
        $this->curtpl='SetlogRow';
    }
    
    public function SetlogRow_footer() {
        $out = <<<ROW
 </table>
     </td></tr>
ROW;
        $this->PushOutput($out);
        $this->curtpl='';
    }

    /**
     * Génère la page
     * @param boolean,array $include_menu Inclure le menu ? (voir son propre menu)
     * @param boolean $include_header Inclure l'entete ?
     */
    public function DoOutput($include_menu=true, $include_header=true) {
        $this->PushOutput('</table>');
        parent::DoOutput($include_menu, $include_header);
    }
    /**
     * Next row, same tpl
     */
    public function PushRow() {
        $this->PushOutput($this->currow);
//        if (is_callable(array($this,$this->curtpl)))
            call_user_func(array($this,$this->curtpl), $this);
    }
    /**
     *
     * @return tpl_troops
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}