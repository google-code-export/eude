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

/*
  $tpl = new tpl_cartedetails;
  $tpl->Setheader(intval($_GET["ID"]));
  $tpl->AddRow($ligne);
  $tpl->DoOutput();
 */

class tpl_cartedetails extends output {

    protected $BASE_FILE = '';
    private $bulle1, $bulle2;
    private $lng, $lngtype;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL . 'Cartedetail.php';
        $this->lng = language::getinstance()->GetLngBlock('cartedetails');
        $this->bulle1 = bulle($this->lng['bulle1']);
        $this->bulle2 = bulle($this->lng['bulle2']);
        $this->lngtype = language::getinstance()->GetLngBlock('dataengine');
        $this->lngtype = $this->lngtype['types']['imgurl'];
        parent::__construct();
    }

    public function Setheader($ID) {
        $sys = sprintf($this->lng['ss'], $ID);
        $out = <<<EOF
<CarteDetails><content><![CDATA[
<table class="table_nospacing" width="500px">
<tr class="text_center color_titre spacing_header">
	<td><font size="+1" width="450px">$sys</td>
	<td class="header link" onclick="Navigateur.SetStart($ID); return Carte.DetailsShow(false);">{$this->lng['start']}</td>
	<td class="header link" onclick="Navigateur.SetEnd($ID); return Carte.DetailsShow(false);">{$this->lng['end']}</td>
	<td class="header link spacing_row" colspan="2" class="spacing_row" onclick="return Carte.DetailsShow(false);">{$this->lng['close']}</td>
</tr>
		<tr class="text_center color_header spacing_header">
			<td class="spacing_row0">{$this->lng['cols_type']}</td>
			<td class="spacing_row0">{$this->lng['cols_coords']}</td>
			<td class="spacing_row0">{$this->lng['cols_user']}</td>
			<td class="spacing_row0">{$this->lng['cols_infos']}</td>
			<td class="spacing_row">{$this->lng['cols_notes']}</td>
		</tr>
EOF;
        $this->PushOutput($out);
    }

    public function AddRow($ligne) {
        $ligne["USER"] = htmlspecialchars($ligne["USER"], ENT_QUOTES, 'utf-8');
        $ligne["EMPIRE2"] = addslashes(DataEngine::xml_fix51($ligne["EMPIRE"]));
        $ligne["EMPIRE"] = DataEngine::xml_fix51(htmlspecialchars($ligne["EMPIRE"], ENT_QUOTES, 'utf-8'));
        $ligne["INFOS"] = htmlspecialchars($ligne["INFOS"], ENT_QUOTES, 'utf-8');
        $ligne["NOTE"] = htmlspecialchars($ligne["NOTE"], ENT_QUOTES, 'utf-8');

        $Image = $this->lngtype[$ligne["TYPE"]];
        $posout = ($ligne["POSOUT"] != "") ? "<br>" . $ligne["POSOUT"] . "-" . $ligne["COORDET"] : "";
        $user = ($ligne["USER"] == "" ? "-" : $ligne["USER"]);
        $info = ($ligne["INFOS"] == "" ? "-" : $ligne["INFOS"]);
        $note = ($ligne["NOTE"] == "" ? "-" : $ligne["NOTE"]);
        $empire = '';
        if ($ligne["EMPIRE"] != "") {
            $empire.= "<br><a href='javascript:void(0);' {$this->bulle2} OnClick=\"Navigateur.InitSearch('";
            $empire.= ( $ligne["EMPIRE2"]) . "',0); return false;\">{$ligne["EMPIRE"]}</a>";
        }
        $out = <<<EOF
		<tr class="text_center spacing_header">
			<td class="color_row0"><img width=48 height=48 src="{$Image}"></img></td>
			<td class="color_row0">{$ligne["POSIN"]}-{$ligne["COORDET"]}{$posout}</td>
			<td class="color_row0">
				<a href='javascript:void(0);' {$this->bulle1} Onclick="Navigateur.InitSearch('{$ligne["USER"]}',1);">{$user}</a>
				{$empire}
			</td>
			<td class="color_row0">{$info}</td>
			<td class="color_row0 spacing_row">{$note}</td>
		</tr>

EOF;
        $this->PushOutput($out);
    }

    public function DoOutput($include_menu=true, $include_header=true) {
        $this->PushOutput("</TABLE>]]></content></CarteDetails>");
        parent::DoOutput();
    }

    /**
     *
     * @return tpl_cartedetails
     */
    static public function getinstance() {
        if (!DataEngine::_tpl_defined(get_class()))
            DataEngine::_set_tpl(get_class(), new self());

        return DataEngine::tpl(get_class());
    }

}