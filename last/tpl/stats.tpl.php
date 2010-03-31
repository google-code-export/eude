<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_stats extends output {
    protected $BASE_FILE = '';

    private $colsid=0;
    protected $total = null;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL."stats.php";
        parent::__construct();
        $this->total = array_fill(-1,8,0);
        $actived_pts = ( isset($_GET['act']) ? 'titre':'header link');
        $actived_de  = (!isset($_GET['act']) ? 'titre':'header link');
        $out = <<<h
        <br/>
<table class="table_center table_nospacing" width="900px">
<tr>
<td class="color_{$actived_de} text_center" width="450px" OnClick="location.href='{$this->BASE_FILE}';">Stats Data Engine</td>
<td class="color_{$actived_pts} text_center" width="450px" OnClick="location.href='{$this->BASE_FILE}?act=pts';">Points</td></tr>
<tr><td colspan="2">
h;
        $this->PushOutput($out);
    }

    public function SetRowtpl() {
        $this->currow = <<<ROW
            <TR class="color_%%class%% text_center">
		<TD class="color_header">%%-2%%</TD>
		<TD>%%0%%</TD>
		<TD>%%3%%</TD>
		<TD>%%5%%</TD>
		<TD>%%6%%</TD>
		<TD>%%1%%</TD>
		<TD>%%2%%</TD>
		<TD>%%4%%</TD>
		<TD>%%-1%%</TD>
	</TR>
ROW;

    }
    public function Setheader() {
        $this->curtpl = 'SetRowtpl';
        $this->PushOutput('<TABLE class="table_nospacing" width=900px>');
        $this->AddToRow('Membres', -2);
        $this->AddToRow('Joueurs', 0);
        $this->AddToRow('Alliés', 3);
        $this->AddToRow('Ennemis', 5);
        $this->AddToRow('Flotte PNJ', 6);
        $this->AddToRow('Vortex', 1);
        $this->AddToRow('Planètes', 2);
        $this->AddToRow('Astéroïdes', 4);
        $this->AddToRow('Total', -1);
        $this->PushRow(true);

    }

    public function footer() {
        $this->AddToRow('Total', -2);
        for ($i=-1; $i<7; $i++) $this->AddToRow($this->total[$i], $i); // valeur totales
        $this->PushRow();
    }

    public function SetRowtplPoints() {
        $this->currow = <<<ROW
            <TR class="color_%%class%% text_center">
		<TD class="color_header">%%-2%%</TD>
		<TD>%%Points%%</TD>
		<TD>%%pts_architecte%%</TD>
		<TD>%%pts_mineur%%</TD>
		<TD>%%pts_science%%</TD>
		<TD>%%pts_commercant%%</TD>
		<TD>%%pts_amiral%%</TD>
		<TD>%%pts_guerrier%%</TD>
	</TR>
ROW;

        $this->curtpl = 'SetRowtplPoints';
    }

    public function SetheaderPoints() {
        $this->SetRowtplPoints();
        $this->PushOutput('<TABLE class="color_header table_nospacing" width=900px>');
        $this->AddToRow('Joueurs', -2);
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%Points%%">Points total</a>', 'Points');
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%pts_architecte%%">Architecte</a>', 'pts_architecte');
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%pts_mineur%%">Mineur</a>', 'pts_mineur');
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%pts_science%%">Science</a>', 'pts_science');
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%pts_commercant%%">Commerçant</a>', 'pts_commercant');
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%pts_amiral%%">Amiral</a>', 'pts_amiral');
        $this->AddToRow('<a href="'.$this->BASE_FILE.'?%%pts_guerrier%%">Guerrier</a>', 'pts_guerrier');

    }

    public function AddToRow($value, $key) {
        if (is_numeric($value)) {
            $this->total[$key] += $value;
            $value =DataEngine::format_number($value, true);
        }
        $this->currow=str_replace("%%$key%%", $value, $this->currow);
    }
    public function PushRow($bgcls=false) {
        if ($bgcls) {
            $this->AddToRow('header', 'class');
            $colsid = 0;
        } else {
            $this->colsid++;
            $this->AddToRow('row'.($this->colsid%2), 'class');
            for ($i=-2; $i<8; $i++) $this->AddToRow('-', $i); // valeur par défaut
        }
        $this->PushOutput($this->currow);
        call_user_func(array($this,$this->curtpl), $this);
    }

    public function DoOutput($include_menu=true, $include_header=true) {

        $this->PushOutput('</table></td></tr></table>');
        parent::DoOutput();
    }

    /**
     * @return tpl_stats
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}