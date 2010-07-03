<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');
/*
$tpl = tpl_membre::getinstance();
$tpl->DoOutput();
*/
class tpl_membre extends output {
    protected $BASE_FILE = '';
    private $lng;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'Membre.php';
        $this->lng = language::getinstance()->GetLngBlock('membre');
        
        parent::__construct();
        $this->page_title = $this->lng['page_title'];
    }

    public function header() {
        $this->PushOutput('<table class="table_center">');
    }

    public function level_header() {
        $this->PushOutput('<tr><td style="width: 100%;"><table class="table_center"><tr>');
    }

    public function level_grade($name) {
        $this->PushOutput('<td class="color_bigheader spacing_row">'.$name.'</td>');
    }

    public function level_grade_sep() {
        $this->PushOutput('</tr><tr>');
    }

    public function level_players_empty() {
        $this->PushOutput('<td>&nbsp;</td>');
    }

    public function level_players_header() {
        $this->PushOutput('<td class="color_row1 spacing_row"><ul class="listing">');
    }

    public function level_player_row($joueur) {
//        FB::info($joueur);
        $ship = $editlink= '';
        if (DataEngine::CheckPerms(AXX_MODO) && $joueur['ship'] != '')
            $ship=sprintf ($this->lng['level_player_row_ship'], $joueur['ship']);
        if (DataEngine::CheckPerms(AXX_ADMIN))
            $editlink=' <a href="%ROOT_URL%editmembres.php?Joueur='.$joueur['Joueur']
            .'"><img src="%IMAGES_URL%edit.png"></a> &nbsp; ';

        $joueur['Points'] = DataEngine::format_number($joueur['Points'],true);
        if (Members::CheckPerms('PERSO_OWNUNIVERSE_READONLY'))
           $joueur['Joueur'] = sprintf('<a href="%%ROOT_URL%%ownuniverse.php?showuser=%s">%1$s</a>',$joueur['Joueur']);

        $bulle = bulle(sprintf($this->lng['level_player_row_bulle'], $ship,
                $joueur['Points'],$joueur['Commerce'], $joueur['Recherche'],
                $joueur['Combat'], $joueur['Construction'],$joueur['Economie'],
                $joueur['Navigation']));

        $out = <<<lpr
        <li {$bulle}>{$editlink}{$joueur['Joueur']}</li>
lpr;
        $this->PushOutput($out);
    }

    public function level_players_footer() {
        $this->PushOutput('</ul></td>');
    }

    public function level_footer() {
        $this->PushOutput('</tr></table></td></tr>');
    }

    public function  level_vs_grade () {
        $this->PushOutput('</table>');
    }

    public function Grade_Header() {
        $out = <<<gh
            <br/>
    <table class="table_center color_row1">
        <tr class="color_header">
            <TD>{$this->lng['add_member_header']}</TD>
            <TD>{$this->lng['edit_grade_header']}</TD>
        </TR>
        <TR>
            <TD>
gh;
        $this->PushOutput($out);
        return $this;
    }
    public function Grade_AddPlayer($Grades) {
        $cdg = DataEngine::config_key('config', 'DefaultGrade');
        $combograde='';
        foreach($Grades as $v) {
            $combograde.="<option value='".$v['GradeId']."'".($v['GradeId']==$cdg ? ' selected' : '').">".$v['Grade']."</option>";
        }
        $out = <<<ap
    <table class="color_row0">
    <form name='addjoueur' autocomplete='off' method='post' action='Membres.php'>
    <tr class="color_row1"><td>{$this->lng['add_member_player']}</td><td><input class="color_row1" type='text' name='Joueur'></td></tr>
    <tr><td>{$this->lng['add_member_password']}</td><td><input class="color_row0" type='password' name='Password'></td></tr>
    <tr class="color_row1"><td>{$this->lng['add_member_grade']}</td><td><select class="color_row1" name='Grade'>{$combograde}</td></tr>
    <tr><td>{$this->lng['add_member_axx']}</td><td>{$this->lng['add_member_axxlvl']}</td></tr>
    <tr class="color_row1"><td>{$this->lng['add_member_pts']}</td><td><input class="color_row1" type='text' name='Points'></td></tr>
    <tr><td class="text_center" colspan=2><input class="color_row1 text_center" type='submit' value='{$this->lng['add_member_btn']}'></td></tr>
    </table>
    </form>
ap;
        $this->PushOutput($out);
        return $this;
    }

    public function Grade_Sep() {
        $this->PushOutput('</td><TD valign="top">');
    }

    public function Grade_Modif_Header() {
        $out = <<<gmh
            <table class="color_row1">
    <tr class="color_header">
        <td>{$this->lng['edit_grade_id']}</td>
        <td>{$this->lng['edit_grade_name']}</td>
        <td>{$this->lng['edit_grade_sort']}</td>
        <td>{$this->lng['edit_grade_parent']}</td>
        <td colspan=2>&nbsp;</td>
    </tr>
gmh;
        $this->PushOutput($out);
        return $this;
    }

    public function Grade_Modif($Grades, $v) {
        $selected = $v['Rattachement']==0 ? ' selected' : '';
        $out = <<<mg
<form name='modifgrade{$v['GradeId']}' method='post' action='Membres.php'>
<input name='GradeId' type='hidden' value='{$v['GradeId']}'>
<tr class="text_center">
<td>{$v['GradeId']}</td>
<td><input class="color_row1" name='GradeNom' type='text' value="{$v['Grade']}"></td>
<td><input class="color_row1 size40" name='GradeNiv' value='{$v['Niveau']}'></td>
<td>
<select class="color_row1" name='GradePere'>
<option value='0'$selected></option>
mg;
        foreach($Grades as $v2) {
            if ($v['GradeId'] == $v2['GradeId']) continue;
            $out .= $this->_Grade_Modif($v2, $v['Rattachement']);
        }
        $msg = str_replace('\'', '\\\'', sprintf($this->lng['edit_grade_delete_warn'],$v['Grade']));
        $out .=<<<mg
</td>
<TD><INPUT style='font-size:10;' type='checkbox' name='GradeSuppr' value='1' onclick="alert('{$msg}');">{$this->lng['add_member_delete']}</TD>
<td><input class="color_row1" type='submit' value='{$this->lng['edit_grade_btn']}'></td>
</tr>
</form>
mg;
        $this->PushOutput($out);
        return $this;
    }

    private function _Grade_Modif($value,$selected) {
        return '<option value="'.$value['GradeId'].'"'
            .($value['GradeId']==$selected ? ' selected' : '').'>'
            .$value['Grade'].'</option>';
    }

    public function Grade_New($Grades) {
        $out = <<<gn
            <form name='modifgrade' method='post' action='Membres.php'>
    <input name='GradeId' type='hidden' value=-1>
    <tr><td>&nbsp;</td>
    <td><input class="color_row1" name='GradeNom' type='text' value=''></td>
    <td><input class="color_row1 size40" name='GradeNiv' type='text' value=''></td>
    <td>
    <select class="color_row1" name='GradePere'>
    <option value='0' selected></option>
gn;
        foreach($Grades as $value) {
            $out .= $this->_Grade_Modif($value, -1);
        }
        $out .= <<<gn
            </td>
    <td colspan=2 align=right><input class="color_row1" type='submit' value='{$this->lng['new_grade_btn']}'></td>
    </form>
    </tr>
gn;
        $this->PushOutput($out);
        return $this;
    }
    public function Grade_Modif_Footer() {
        $this->PushOutput('</table>');
        return $this;
    }
    public function Grade_Footer() {
        $this->PushOutput('</TD></TR></TABLE>');
        return $this;
    }

    public function DoOutput($include_menu=true, $include_header=true) {
        parent::DoOutput();
    }

    /**
     * @return tpl_membre
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}