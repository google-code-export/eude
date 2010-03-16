<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
class tpl_ownuniverse extends output {
    protected $BASE_FILE = '';
    protected $lng = '';
    protected $ress = '';
    private $keys = array('Titane', 'Cuivre', 'Fer', 'Aluminium', 'Mercure', 'Silicium', 'Uranium', 'Krypton', 'Azote', 'Hydrogene');

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'ownuniverse.php';
        $this->lng = language::getinstance()->GetLngBlock('ownuniverse');
        $this->ress = DataEngine::a_ressources();
        
        parent::__construct();
    }

    public function Setheader($info, $warn, $include_form) {
        if ($include_form) {
            $bulle = bulle($this->lng['tips_header']);
            $out =<<<h
	<TABLE id='imperium_header'>
	<form name='data' method='post' action='{$this->BASE_FILE}'>
	<TR id='imperium_header'>
		<TD id='titreTDtableau' colspan=12><img {$bulle} src='%IMAGES_URL%help.png'/> {$this->lng['header']}</TD>
	</tr>
	<TR id='imperium_header'>
		<td id="TDtableau">&nbsp;</TD>
		<TD id="TDtableau" colspan=8>
			<TEXTAREA id="INTableau" cols="50" rows="4" name='importation'></TEXTAREA>
		</TD>		
		<TD id="TDtableau" colspan=2><input id="INTableau" type=submit value='{$this->lng['btn_submit']}'><br/>
                <input id="INTableau" type=button onclick="location.href='{$this->BASE_FILE}?reset={$_SESSION['_permkey']}';" value='{$this->lng['btn_reset']}'></td>
		<td id="TDtableau">&nbsp;</TD>
	</tr>
        </form>
h;
            $this->PushOutput($out);
            if ($info != "" or $warn !="") {
                $info = "<font color=green>$info</font>";
                $warn = "<font color=red>$warn</font>";
                $out=<<<info
	<TR id='imperium_header'>
		<TD colspan=12>{$info}{$warn}</td>
	</tr>
info;
                $this->PushOutput($out);
            }
        } else {
            $this->PushOutput('<br/><TABLE id="imperium_header">');
        }
    }

    public function RowHeader() {
        $out=<<<h
            <TR id='imperium_header'>
		<td id="TDtableau">&nbsp;</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Titane.png'>&nbsp;{$this->ress[0]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Cuivre.png'>&nbsp;{$this->ress[1]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Fer.png'>&nbsp;{$this->ress[2]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Aluminium.png'>&nbsp;{$this->ress[3]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Mercure.png'>&nbsp;{$this->ress[4]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Silicium.png'>&nbsp;{$this->ress[5]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Uranium.png'>&nbsp;{$this->ress[6]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Krypton.png'>&nbsp;{$this->ress[7]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Azote.png'>&nbsp;{$this->ress[8]['Nom']}</TD>
		<td id="TDtableau"><img src='%IMAGES_URL%Hydrogene.png'>&nbsp;{$this->ress[9]['Nom']}</TD>
		<td id="TDtableau">{$this->lng['cols_Total']}</TD>
	</TR>
h;
        $this->PushOutput($out);
    }

    private function RessToImgAndText($key) {
        $inf = false;
        foreach($this->ress as $v)
            if (stripos($v['Field'],$key)!==false)
                $inf = $v;

        if (!$inf) return '';

        return "<img src='{$inf['Image']}'/>&nbsp;{$inf['Nom']}";
    }

    public function Planet_Header($planet) {
        if ($planet['percent_water'])
            $bulle = bulle($this->lng['planet_key_4']);
        $out=<<<ph
	<TR id='imperium_header'>
		<td id="TDtableau" colspan=2>{$planet['Name']}: {$planet['Coord']}</td>
		<td id="TDtableau" {$bulle}>{$planet['percent_water']}&nbsp;</td>
		<td id="TDtableau" colspan=9>&nbsp;</td>
	</tr>
ph;
        $this->PushOutput($out);
    }

    public function Row_Ress($data) {
        $this->PushOutput($out);
    }

    /**
     Array (
     Name		=> "MaPlanète"
     Coord		=> xxxx-xx-xx-xx
     *			=> Ressources prod/h
     current_*	=> Ressources dispo
     bunker_*	=> Ressources dans le bunker
     total_*		=> current_* + bunker_*
     sell_*		=> Ressources vendu/j
     percent_*	=> Ratio d'exploitation des ressources
     )
     "*" => Nom de ressource
     **/

    public function Add_Current_Ress($data) {
        $maxbunker = ($data['bunker']>0) ? $data['bunker']*200000: 0;

        $row = array();
        $bulles = array();
        $tb = $tc = 0;
        foreach(array_merge($this->keys) as $v) {
            $b		 = DataEngine::format_number($data["bunker_{$v}"]);
            $c		 = DataEngine::format_number($data["current_{$v}"]);
            $tb		+= $data["bunker_{$v}"];
            $tc		+= $data["current_{$v}"];
            $row[$v] = $data["current_{$v}"]+$data["bunker_{$v}"];
            $bulles[$v] = bulle(sprintf($this->lng['current_ress_row_1'], $this->RessToImgAndText($v), $c, $b));
        }

        $row['total'] 	 = $tb+$tc;
        $tp		 = DataEngine::format_number( ($tb/$row['total'])*100 );
        $tf		 = ($maxbunker>0) ? DataEngine::format_number( ($tb/$maxbunker)*100 ):0;
        $tb		 = DataEngine::format_number($tb);
        $tc		 = DataEngine::format_number($tc);
        $bulles['total'] = bulle(sprintf($this->lng['current_ress_row_2'], $tc, $tb, $tp, $tf));
//        $bulles['total'] = bulle('Sur planète: '.$tc
//                .'<br/>Dans le bunker: '.$tb.'<br/>En sécurité: '.$tp.'%'
//                .'<br/>Utilisation bunker: '.$tf.'%');

        $this->Add_RessRow($row, $this->lng['row_stocks'], '', 'imperium_row0', $bulles);
    }

    public function Add_RessRow($data, $title, $key='',$style='imperium_header',$bulles=array()) {
        $out=<<<h
	<TR id="{$style}">
		<td id="{$style}">{$title}</td>
h;

        foreach($this->keys as $vals) {
            $number = DataEngine::format_number($data["{$key}{$vals}"]);
            if ($bulles[$vals]) {
                $out.=<<<r1
		<td {$bulles["{$vals}"]} id="{$style}">{$number}</td>
r1;
            } else {
                $out.=<<<r2
		<td id="{$style}">{$number}</td>
r2;
            }
        }

        $number = DataEngine::format_number($data["{$key}total"]);

        if ($bulles['total']) {
            $out.=<<<f1
		<td {$bulles['total']} id="{$style}">{$number}</td>
f1;
        } else {
            $out.=<<<f2
		<td id="{$style}">{$number}</td>
f2;
        }
        $this->PushOutput($out."	</tr>");
    }

    public function Add_PercentRow($data, $title, $key='',$style='imperium_header',$bulles=array()) {
        $out=<<<h
	<TR id="{$style}">
		<td id="{$style}">{$title}</td>
h;

        foreach($this->keys as $vals) {
            $number = $data["{$key}{$vals}"].'%';
            if ($bulles[$vals]) {
                $out.=<<<r1
		<td {$bulles["{$vals}"]} id="{$style}">{$number}</td>
r1;
            } else {
                $out.=<<<r2
		<td id="{$style}">{$number}</td>
r2;
            }
        }

        $number = '-';

        if ($bulles['total']) {
            $out.=<<<f1
		<td {$bulles['total']} id="{$style}">{$number}</td>
f1;
        } else {
            $out.=<<<f2
		<td id="{$style}">{$number}</td>
f2;
        }
        $this->PushOutput($out.'	</tr>');
    }


    private function SetRowtplBatiments() {
        $this->currow = <<<ROW
    <tr>
        <td id="imperium_row%%class%%">%%Name%%</td>
        <td id="imperium_row%%class%%">%%control%%</td>
        <td id="imperium_row%%class%%">%%communication%%</td>
        <td id="imperium_row%%class%%">%%university%%</td>
        <td id="imperium_row%%class%%">%%technology%%</td>
        <td id="imperium_row%%class%%">%%gouv%%</td>
        <td id="imperium_row%%class%%">%%defense%%</td>
        <td id="imperium_row%%class%%">%%shipyard%%</td>
        <td id="imperium_row%%class%%">%%spacedock%%</td>
        <td id="imperium_row%%class%%">%%bunker%%</td>
        <td id="imperium_row%%class%%">%%tradepost%%</td>
        <td id="imperium_row%%class%%">%%ressource%%</td>
    </tr>
ROW;

    }
    public function SetheaderBatiments() {
        $this->curtpl = 'SetRowtplBatiments';        
        
        $this->currow = <<<ROW
    <tr>
        <td id="TDtableau" colspan=12>&nbsp;</td>
    </tr>
    <tr id="imperium_header">
        <td id="TDtableau">{$this->lng['cols_planets']}</td>
        <td id="TDtableau">%%control%%</td>
        <td id="TDtableau">%%communication%%</td>
        <td id="TDtableau">%%university%%</td>
        <td id="TDtableau">%%technology%%</td>
        <td id="TDtableau">%%gouv%%</td>
        <td id="TDtableau">%%defense%%</td>
        <td id="TDtableau">%%shipyard%%</td>
        <td id="TDtableau">%%spacedock%%</td>
        <td id="TDtableau">%%bunker%%</td>
        <td id="TDtableau">%%tradepost%%</td>
        <td id="TDtableau">%%ressource%%</td>
    </tr>
ROW;

    }

    public function DoOutput($include_menu=true, $include_header=true) {
        $this->PushOutput('</table>');
        parent::DoOutput(); // false false ? header menu
    }

    public function PushRow() {
        $this->PushOutput($this->currow);
        call_user_func(array($this,$this->curtpl), $this);
    }
    /**
     * @return tpl_ownuniverse
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}
