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

    public function Setheader($include_form) {
        if ($include_form) {
            $bulle = bulle($this->lng['tips_header']);
            $out =<<<h
	<TABLE class="table_center table_nospacing color_row0 text_center">
	<form name='data' method='post' action='{$this->BASE_FILE}'>
	<TR>
		<TD class="color_bigheader" colspan=12><img {$bulle} src='%IMAGES_URL%help.png'/> {$this->lng['header']}</TD>
	</tr>
	<TR>
		<td>&nbsp;</TD>
		<TD colspan=8>
			<TEXTAREA class="color_row0" cols="50" rows="4" name='importation'></TEXTAREA>
		</TD>		
		<TD colspan=2><input class="color_row0" type=submit value='{$this->lng['btn_submit']}'><br/>
                <input class="color_row0" type=button onclick="location.href='{$this->BASE_FILE}?reset={$_SESSION['_permkey']}';" value='{$this->lng['btn_reset']}'></td>
		<td>&nbsp;</TD>
	</tr>
        </form>
h;
            $this->PushOutput($out);
        } else {
            $this->PushOutput('<br/><TABLE class="table_center table_nospacing color_row0 text_center">');
        }
    }

    public function RowHeader() {
        $out=<<<h
            <TR class="color_header spacing_header">
		<td>&nbsp;</TD>
		<td><img src='%IMAGES_URL%Titane.png'>&nbsp;{$this->ress[0]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Cuivre.png'>&nbsp;{$this->ress[1]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Fer.png'>&nbsp;{$this->ress[2]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Aluminium.png'>&nbsp;{$this->ress[3]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Mercure.png'>&nbsp;{$this->ress[4]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Silicium.png'>&nbsp;{$this->ress[5]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Uranium.png'>&nbsp;{$this->ress[6]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Krypton.png'>&nbsp;{$this->ress[7]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Azote.png'>&nbsp;{$this->ress[8]['Nom']}</TD>
		<td><img src='%IMAGES_URL%Hydrogene.png'>&nbsp;{$this->ress[9]['Nom']}</TD>
		<td class="spacing_row">{$this->lng['cols_Total']}</TD>
	</TR>
h;
        $this->PushOutput($out);
    }

    private function RessToImgAndText($key) {
        $inf = false;
        foreach($this->ress as $v)
            if (mb_stripos($v['Field'],$key,0, 'utf8')!==false)
                $inf = $v;

        if (!$inf) return '';

        return "<img src='{$inf['Image']}'/>&nbsp;{$inf['Nom']}";
    }

    public function Planet_Header($planet) {
        $water_style = '';
        if ($planet['percent_water']) {
            $bulle = bulle($this->lng['planet_key_4']);
            $planet['percent_water'] .= '%';
            $water_style = 'spacing_row1';
        } else $planet['percent_water'] = '&nbsp;';
        $out=<<<ph
	<TR class="color_row1">
		<td class="color_header spacing_header" colspan=2>{$planet['Name']}: {$planet['Coord']}</td>
		<td class="{$water_style}" {$bulle}>{$planet['percent_water']}</td>
		<td colspan=9>&nbsp;</td>
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

        $this->Add_RessRow($row, $this->lng['row_stocks'], '', 'row0', $bulles);
    }

    public function Add_RessRow($data, $title, $key='',$style='header',$bulles=array()) {
        $out=<<<h
	<TR class="color_{$style}">
		<td class="color_header spacing_header">{$title}</td>
h;

        foreach($this->keys as $vals) {
            $number = DataEngine::format_number($data["{$key}{$vals}"]);
            if ($bulles[$vals]) {
                $out.=<<<r1
		<td class="spacing_{$style}" {$bulles["{$vals}"]}>{$number}</td>
r1;
            } else {
                $out.=<<<r2
		<td class="spacing_{$style}" >{$number}</td>
r2;
            }
        }

        $number = DataEngine::format_number($data["{$key}total"]);

        if ($bulles['total']) {
            $out.=<<<f1
		<td class="spacing_row" {$bulles['total']}>{$number}</td>
f1;
        } else {
            $out.=<<<f2
		<td class="spacing_row">{$number}</td>
f2;
        }
        $this->PushOutput($out."</tr>");
    }

    public function Add_PercentRow($data, $title, $key='',$style='header',$bulles=array()) {
        $out=<<<h
	<TR class="color_{$style}">
		<td class="color_header spacing_header">{$title}</td>
h;

        foreach($this->keys as $vals) {
            $number = $data["{$key}{$vals}"].'%';
            if ($bulles[$vals]) {
                $out.=<<<r1
		<td class="spacing_{$style}" {$bulles["{$vals}"]}>{$number}</td>
r1;
            } else {
                $out.=<<<r2
		<td class="spacing_{$style}">{$number}</td>
r2;
            }
        }

        $number = '-';

        if ($bulles['total']) {
            $out.=<<<f1
		<td class="spacing_row" {$bulles['total']}>{$number}</td>
f1;
        } else {
            $out.=<<<f2
		<td class="spacing_row">{$number}</td>
f2;
        }
        $this->PushOutput($out.'	</tr>');
    }


    private function SetRowtplBatiments() {
        $this->currow = <<<ROW
    <tr class="color_row%%class%%">
        <td class="color_header spacing_row">%%Name%%</td>
        <td class="spacing_row%%class%%">%%control%%</td>
        <td class="spacing_row%%class%%">%%communication%%</td>
        <td class="spacing_row%%class%%">%%university%%</td>
        <td class="spacing_row%%class%%">%%technology%%</td>
        <td class="spacing_row%%class%%">%%gouv%%</td>
        <td class="spacing_row%%class%%">%%defense%%</td>
        <td class="spacing_row%%class%%">%%shipyard%%</td>
        <td class="spacing_row%%class%%">%%spacedock%%</td>
        <td class="spacing_row%%class%%">%%bunker%%</td>
        <td class="spacing_row%%class%%">%%tradepost%%</td>
        <td class="spacing_row">%%ressource%%</td>
    </tr>
ROW;

    }
    public function SetheaderBatiments() {
        $this->curtpl = 'SetRowtplBatiments';        
        
        $this->currow = <<<ROW
    <tr class="color_bg">
        <td colspan=12>&nbsp;</td>
    </tr>
    <tr class="color_header spacing_header">
        <td>{$this->lng['cols_planets']}</td>
        <td><img src="%IMAGES_URL%control.gif" title="%%control%%"/></td>
        <td><img src="%IMAGES_URL%communication.gif" title="%%communication%%"/></td>
        <td><img src="%IMAGES_URL%university.gif" title="%%university%%"/></td>
        <td><img src="%IMAGES_URL%technology.gif" title="%%technology%%"/></td>
        <td><img src="%IMAGES_URL%gouv.gif" title="%%gouv%%"/></td>
        <td><img src="%IMAGES_URL%defense.gif" title="%%defense%%"/></td>
        <td><img src="%IMAGES_URL%shipyard.gif" title="%%shipyard%%"/></td>
        <td><img src="%IMAGES_URL%spacedock.gif" title="%%spacedock%%"/></td>
        <td><img src="%IMAGES_URL%bunker.gif" title="%%bunker%%"/></td>
        <td><img src="%IMAGES_URL%tradepost.gif" title="%%tradepost%%"/></td>
        <td cass="spacing_row"><img src="%IMAGES_URL%ressource.gif" title="%%ressource%%"/></td>
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
