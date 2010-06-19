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
    private $ress;
    private $lng;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'pillage.php';
        $this->ress = DataEngine::a_ressources();
        $this->lng = language::getinstance()->GetLngBlock('pillage');
        parent::__construct();
    }

    public function AddRow($ligne) {
        $this->PushOutput($out);
    }

    public function Setheader() {
        $this->currow = <<<ROW
   <table class="table_center table_nospacing">
    <tr class="color_header text_center">
        <td colspan="7">{$this->lng['legend_header']}</td>
    </tr>
    <tr class="color_row1">
        <td class="color_header">&nbsp;</td>
        <td class="spacing_row1">{$this->lng['legend_type']}</td>
        <td class="spacing_row1">{$this->lng['legend_date']}</td>
        <td class="spacing_row1">{$this->lng['legend_coords']}</td>
        <td class="spacing_row1">{$this->lng['legend_attack']}</td>
        <td class="spacing_row1">{$this->lng['legend_defend']}</td>
        <td class="spacing_row">{$this->lng['legend_lost']}</td>
    </tr>
 <tr><td colspan="7">
     <table class="table_nospacing" width="100%">
    <tr class="color_row0">
        <td class="spacing_row0">{$this->lng['legend_player']}</td>
        <td class="spacing_row0">{$this->lng['legend_date']}</td>
        <td class="spacing_row0"><img src="{$this->ress[0]['Image']}"/> {$this->ress[0]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[1]['Image']}"/> {$this->ress[1]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[2]['Image']}"/> {$this->ress[2]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[3]['Image']}"/> {$this->ress[3]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[4]['Image']}"/> {$this->ress[4]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[5]['Image']}"/> {$this->ress[5]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[6]['Image']}"/> {$this->ress[6]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[7]['Image']}"/> {$this->ress[7]['Nom']}</td>
        <td class="spacing_row0"><img src="{$this->ress[8]['Image']}"/> {$this->ress[8]['Nom']}</td>
        <td class="spacing_row"><img src="{$this->ress[9]['Image']}"/> {$this->ress[9]['Nom']}</td>
    </tr></td>
    </table>
    </tr>
    <tr class="color_bg">
        <td colspan="7" height="1"></td>
    </tr>
    <tr class="color_header text_center">
        <td colspan="5">{$this->lng['listing_header']}</td>
        <td colspan="2">
            <form method="get" action="{$this->BASE_FILE}">
                <input class="color_header" type="text" name="player" value="%%player%%"/>
                <input class="color_header" type="submit" value="{$this->lng['listing_btnfilter']}"/>
            </form>
        </td>
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
        <td class="spacing_row%%rowid%%">%%type%%</td>
        <td class="spacing_row%%rowid%%">%%date%%</td>
        <td class="spacing_row%%rowid%%">%%coords%%</td>
        <td class="spacing_row%%rowid%%">%%attack%%</td>
        <td class="spacing_row%%rowid%%">%%defend%%</td>
        <td class="spacing_row">%%lost%%</td>
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
        <td class="spacing_row%%class%%">%%player%%</td>
        <td class="spacing_row%%class%%">%%date%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[0]['Image']}"/> %%ress0%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[1]['Image']}"/> %%ress1%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[2]['Image']}"/> %%ress2%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[3]['Image']}"/> %%ress3%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[4]['Image']}"/> %%ress4%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[5]['Image']}"/> %%ress5%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[6]['Image']}"/> %%ress6%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[7]['Image']}"/> %%ress7%%</td>
        <td class="spacing_row%%class%%"><img src="{$this->ress[8]['Image']}"/> %%ress8%%</td>
        <td class="spacing_row"><img src="{$this->ress[9]['Image']}"/> %%ress9%%</td>
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