<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_eadmin extends output {
    protected $BASE_FILE = '';
    protected $lng = '';

    private $idcols=0;
    private $cols_cls=array('#d6d6d6','#CCCCCC','#AAAAAA');

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'EAdmin.php';
        $this->lng = language::getinstance()->GetLngBlock('admin');

        parent::__construct();
        $out = <<<o
   <style>
       /* Redéfinition du style de base */
address, a:link, a:active, a:visited {
    color: blue;
    text-decoration: none;
}
a:hover {
    color: darkorange;
}
   </style>
   <br/>
o;
        $this->PushOutput($out);
    }

    public function admin_header($version) {
        $cols=4;
        $colsminus=$cols-1;
        $links_1 = ($_REQUEST['act'] == ''         ? 'titre': 'header link');
        $links_2 = ($_REQUEST['act'] == 'perms'    ? 'titre': 'header link');
        $links_3 = ($_REQUEST['act'] == 'mapcolor' ? 'titre': 'header link');
        $links_4 = ($_REQUEST['act'] == 'logs'     ? 'titre': 'header link');
        $out =<<<col2_h
<table class="table_center table_nospacing" width=750px>
	<TR class="base_row1 text_center">
		<TD colspan="{$cols}"><b>E</b>mpire <b>U</b>niverse 2: <b>D</b>ata <b>E</b>ngine ({$this->version})</TD>
	</TR>
        <TR class="base_row1">
            <TD colspan="{$cols}"><a href='http://eude.googlecode.com/' target='_blank'>Un bug, une suggestion ?</a></TD>
	</TR>
        <TR class="base_row1">
            <TD align=right><b>Mysql <br/>PHP <br/>GD </b></td><TD colspan="{$colsminus}">{$version[0]}<br/>{$version[1]}<br/>{$version[2]}</TD>
	</TR>
        <TR class="text_center">
		<TD OnClick="location.href='{$this->BASE_FILE}';" class="color_{$links_1}">{$this->lng['page_links1']}</td>
		<TD OnClick="location.href='{$this->BASE_FILE}?act=perms';" class="color_{$links_2}">{$this->lng['page_links2']}</td>
		<TD OnClick="location.href='{$this->BASE_FILE}?act=mapcolor';" class="color_{$links_3}">{$this->lng['page_links3']}</td>
		<TD OnClick="location.href='{$this->BASE_FILE}?act=logs';" class="color_{$links_4}">{$this->lng['page_links4']}</td>
	</TR>      
<tr class="color_row1">
<td colspan="{$cols}" height="1px"></td>
</tr>
<tr><td colspan="{$cols}">
col2_h;
        $this->PushOutput($out);
    }
    public function admin_vortex($dates,$cleanvortex,$cron_conf) {

        if ($cron_conf['enabled']) {
            $lastrun = date('Y-m-d H:i:s', $cron_conf['lastrun']);
            $this->lng['vortex_cron_enabled'] = sprintf($this->lng['vortex_cron_enabled'], $lastrun);
            $cron =<<<c
		<TR class"base_row1">
			<TD>{$this->lng['vortex_cron_disable']}</TD>
			<TD colspan="2">{$this->lng['vortex_cron_enabled']}</TD>
		</TR>
c;
        } else {
            $cron =<<<c
		<TR class"base_row1">
			<TD colspan="3">{$this->lng['vortex_cron_enable']}</TD>
		</TR>
c;
        }

        $this->lng['vortex_whathappen']     = sprintf($this->lng['vortex_whathappen'], $dates[1], $dates[2]);
        $this->lng['vortex_result']         = sprintf($this->lng['vortex_result'], $cleanvortex[0], $cleanvortex[1]);

        $out =<<<col2_vortex
            <table class="table_center table_nospacing base_row1">
		<form name=cleanvortex method='post' action='{$this->BASE_FILE}'>
		<TR class="color_header">
			<TD colspan=3>{$this->lng['vortex_title']}</TD>
			<TD rowspan=4 class="text_center">
				<input type="hidden" name="cleanvortex" value="{$dates[1]}">
                                <input type="hidden" name="cleanvortex_inactif" value="{$dates[2]}">
                                <input class="color_header" type="submit" value="{$this->lng['vortex_do_now']}">
			</TD>
		</TR>
		<TR class"base_row0">
			<TD>{$this->lng['vortex_servertime']}</TD>
			<TD colspan=2><font color=red>{$dates[0]}</font></TD>
		</TR>
                {$cron}
		<TR class"base_row0">
			<TD colspan=3>{$this->lng['vortex_whathappen']}</TD>
		</TR>

col2_vortex;
        if (is_array($cleanvortex))
            $out .=<<<col2_cleanvortex
		<TR class"base_row0">
			<TD colspan=4>{$this->lng['vortex_result']}</TD>
		</TR>

col2_cleanvortex;

        $out .= <<<o
</form>
<tr class="color_row1">
<td colspan=4 height="1px"></td>
</tr>
o;
        $this->PushOutput($out);
    }

    public function empire_switch($empire_list,$emp_upd) {

        $out =<<<col1_r
<form action="{$this->BASE_FILE}" method='post'>
	<TR class="color_header">
		<TD colspan=3>{$this->lng['empire_switch']}</TD>
		<TD rowspan="3" class="text_center"><input class="color_header" name='emp_upd' type=submit value="{$this->lng['empire_switch_btn']}"></TD>
	</TR>
	<TR class="base_row0">
		<TD>{$this->lng['empire_switch_current']}</TD>
		<TD colspan=2>
		<select class="base_row0" name='emp_orig'>
			<option name=''>{$this->lng['empire_switch_current_sel']}</option>
col1_r;
        $this->PushOutput($out);
        $this->SelectOptions($empire_list,-1);

        $out =<<<col2_r
		</select>
		</TD>
	</TR>
	<TR  class="base_row1">
		<TD>{$this->lng['empire_switch_new']}</TD>
		<TD colspan=2>
		<select class="base_row1" name='emp_new'>
			<option name=''>{$this->lng['empire_switch_new_sel']}</option>
col2_r;
        $this->PushOutput($out);
        $this->SelectOptions($empire_list,-1);

        $out =<<<col3_r
		</select>
		</TD>
	</TR>
</form>
<tr class="color_row1">
<td colspan=4 height="1px"></td>
</tr>
col3_r;
        if ($emp_upd) {
            $this->lng['empire_switch_result'] = sprintf($this->lng['empire_switch_result'], $emp_upd);
            $out =<<<upd
	<TR class="color_row1">
		<TD colspan=4>{$this->lng['empire_switch_result']}</TD>
	</TR>
upd;
        }

        $this->PushOutput($out);
    }

    public function empire_allys($empire_list) {

        $out =<<<col1_r
<form action="{$this->BASE_FILE}" method='post'>
	<TR class="color_header">
		<TD colspan=4>{$this->lng['empire_allys']}</TD>
	</TR>
	<TR class="base_row0">
		<TD colspan=3 align=center>
		<select class="base_row0" name='emp'>
			<option value=''>{$this->lng['empire_allys_sel']}</option>
col1_r;
        $this->PushOutput($out);
        $this->SelectOptions($empire_list,-1);

        $out =<<<col2_r
		</select>
		</TD>
                <td class="color_header text_center"><input class="color_header" name='emp_allys_add' type=submit value="{$this->lng['empire_allys_add']}"></td>
	</TR>
	<TR class="base_row1">
		<TD colspan=3><font color="darkgreen">
col2_r;
        $wars = DataEngine::config('EmpireAllys');
        $nb = count($wars);
        if (is_array($wars) && $nb>0) {
            $i=0;
            foreach ($wars as $key => $emp) {
                $i++;
                $emp = DataEngine::utf_strip($emp);
                $out .= '<a href="?emp_allys_rm='.$key.'">'.$this->lng['empire_allys_del'].'</a> &nbsp; &nbsp; '.$emp;
                if ($i<$nb) $out .='<br/>';
            }
        } else {
            $out .= $this->lng['empire_allys_empty'];
        }
        $out .=<<<col3_r
		</font></TD>
                <td class="color_header">&nbsp;</td>
	</TR>
</form>
<tr class="color_row1">
<td colspan=4 height="1px"></td>
</tr>
col3_r;
        $this->PushOutput($out);
    }

    public function empire_wars($empire_list) {

        $out =<<<col1_r
<form action="{$this->BASE_FILE}" method='post'>
	<TR class="color_header">
		<TD colspan=4>{$this->lng['empire_wars']}</TD>
	</TR>
	<TR class="base_row0">
		<TD colspan=3 align=center>
		<select class="base_row0" name='emp'>
			<option value=''>{$this->lng['empire_wars_sel']}</option>
col1_r;
        $this->PushOutput($out);
        $this->SelectOptions($empire_list,-1);

        $out =<<<col2_r
		</select>
		</TD>
                <td class="color_header text_center"><input class="color_header" name='emp_war_add' type=submit value="Ajouter"{$this->lng['empire_wars_add']}"></td>
	</TR>
	<TR class="base_row1">
		<TD colspan=3><font color="red">
col2_r;
        $wars = DataEngine::config('EmpireEnnemy');
        $nb = count($wars);
        if (is_array($wars) && $nb>0) {
            $i=0;
            foreach ($wars as $key => $emp) {
                $i++;
                $emp = DataEngine::utf_strip($emp);
                $out .= '<a href="?emp_war_rm='.$key.'">'.$this->lng['empire_wars_del'].'</a> &nbsp; &nbsp; '.$emp;
                if ($i<$nb) $out .='<br/>';
            }
        } else {
            $out .= $this->lng['empire_wars_empty'];
        }
        $out .=<<<col3_r
		</font></TD>
                <td class="color_header">&nbsp;</td>
	</TR>
</form>
<tr class="color_row1">
<td colspan=4 height="1px"></td>
</tr>
col3_r;
        $this->PushOutput($out);
    }

    public function empire_allywars($allysnb,$warsnb) {

        $out =<<<col1_r
<form action="{$this->BASE_FILE}" method='post'>
	<TR class="color_header">
		<TD colspan=3>{$this->lng['empire_allyswars']}</TD>
		<TD class="text_center"><input class="color_header" name='emp_allywars' type=submit value="{$this->lng['empire_allyswars_upd']}"></TD>
	</TR>
col1_r;

        if ($allysnb>=0 || $warsnb>=0) {
            $this->lng['empire_allyswars_result0'] = sprintf($this->lng['empire_allyswars_result0'], $allysnb);
            $this->lng['empire_allyswars_result1'] = sprintf($this->lng['empire_allyswars_result1'], $warsnb);
            $out .=<<<upd
	<TR class="color_row0">
		<TD colspan=4>{$this->lng['empire_allyswars_result0']}</TD>
	</TR>
	<TR class="color_row0">
		<TD colspan=4>{$this->lng['empire_allyswars_result1']}</TD>
	</TR>
upd;
        }
        $out .= <<<o
<tr class="color_row1">
<td colspan=4 height="1px"></td>
</tr>
o;
        $this->PushOutput($out);
    }

    public function cleaning_header($numrow=0) {
        $this->idcols=0;
        $numrow++;
        $out =<<<cleaning_header
		<form name="cleaning" method='post' action='{$this->BASE_FILE}'>
		<TR class="color_header">
			<TD colspan=2>{$this->lng['cleaning_items']}</TD>
			<TD>{$this->lng['cleaning_act']}</TD>
			<TD class="text_center" rowspan="{$numrow}">
				<input class="color_header" type="submit" value="{$this->lng['cleaning_btn']}">
			</TD>
		</TR>
cleaning_header;

        $this->PushOutput($out);

    }

    public function cleaning_row($key,$title,$select) {
        $this->idcols++;
        $cls= $this->idcols%2;

        $out =<<<cleaning_row
		<TR class="base_row{$cls}">
			<TD colspan=2>{$title}</TD>
			<td class="text_center"><select class="base_row{$cls}" name="{$key}">
cleaning_row;
        $this->PushOutput($out);

        $this->SelectOptions($select,-1,'',true);

        $out =<<<cleaning_row
			</select></td>
		</TR>
cleaning_row;

        $this->PushOutput($out);
    }

    public function cleaning_footer() {

        $out = <<<cleaning_footer
			</select></td>
		</TR>
		</form>
<tr class="color_row1">
<td colspan=4 height="1px"></td>
</tr>
cleaning_footer;

        $this->PushOutput($out);
    }

    public function cleaning_msg($array) {
        $msg='';
        foreach($array as $tp => $num)
            $msg[] = sprintf($this->lng[$tp], $num);
        //"$num $tp supprimé";
        $msg = implode('<br/>',$msg);
        $out .=<<<cleaning_msg
		<TR class="color_row0">
			<TD colspan=4>{$msg}</TD>
		</TR>

cleaning_msg;

        $this->PushOutput($out);
    }

    public function admin_footer() {
        $this->PushOutput('</table>');
    }

    public function perms_header() {
        $out = <<<x
<form method="post" action="?act=perms">
<table class="table_center color_bg table_nospacing">
    <tr class="color_titre">
        <td colspan="2">{$this->lng['perms_col1']}</td>
        <td>{$this->lng['perms_col2']}</td>
    </tr>
x;

        $this->PushOutput($out);
    }

    public function perms_category($cxx_v) {
        $this->PushOutput('<tr><td class="color_header" colspan="3">'.$cxx_v.'</td></tr>');
    }

    public function perms_row($cxx_k, $cxx_v, $axx_name, $cxx_conf) {
        $cols_cls = $this->idcols++%2;

        $out = <<<pr
   <tr class="color_row{$cols_cls}">
        <td class="color_header">&nbsp;</td><td>{$cxx_v}</td>
        <td class="text_center"><select class="color_row{$cols_cls}" name="cxx[{$cxx_k}]">
pr;
        $this->PushOutput($out);

        // loop par AXX
        foreach ($axx_name as $axx_k=>$axx_v) {
            $selected = ($cxx_conf[$cxx_k]==$axx_k) ? ' selected':'';
            $this->PushOutput('<option value="'.$axx_k.'"'.$selected.'>'.$axx_v.'</option>');
        }
        $this->PushOutput('</select></td>');
    }

    public function perms_footer() {
        $out = <<<x
    <tr class="color_header">
        <td class="text_right" colspan="3"><input class="color_titre" type="submit" value="{$this->lng['perms_apply']}" /></td>
   </tr>
 </table>
</form>
x;
        $this->PushOutput($out);
    }

    public function map_color_header() {
        $out=<<<o
<form method="post" action="{$this->BASE_FILE}?act=mapcolor">
<input type="hidden" name="majcolors" value="true"/>
<table class="table_center table_nospacing base_row1" width="450px">
<tr>
    <td class="color_titre text_center" colspan=5>{$this->lng['mapcolor_header']}</td>
</tr>
o;
        $this->PushOutput($out);
    }
    public function map_color_rowheader($text) {
        $this->PushOutput('<tr><td class="color_header" colspan=5>'.$text.'</td></tr>');
    }
    public function map_color_row($cls,$id,$v,$legend) {
        $cols_cls = $this->idcols++%2;
        $out = <<<o
            <tr class="color_row{$cols_cls}">
                <td class="color_header">&nbsp;</td>
                <td>{$legend}</td>
                <td style="background-color:{$cls[$id][$v]}; width: 20px;">&nbsp;</td>
                <td style="width: 80px;">
                <input style="width: 80px;" OnChange="document.getElementById('cls{$id}_{$v}').setAttribute('style', 'background-color: '+this.value);"
                type=text value="{$cls[$id][$v]}" name="cls[{$id}][{$v}]">
                </td>
                <td id="cls{$id}_{$v}" style="background-color:{$cls[$id][$v]}; width: 20px;">&nbsp;</td>
            </tr>
o;
        $this->PushOutput($out);
    }
    public function map_color_footer() {
        $out= <<<mcf
<tr>
    <td class="color_header text_right" colspan=5><input class="color_header" type=submit value="{$this->lng['mapcolor_btn']}"></td>
</tr>
</table>
    </form>
mcf;
        $this->PushOutput($out);
    }

    public function log_header() {
        $out =<<<col1_h
<table class="table_center base_h" width="650px">
	<TR>
		<TD>{$this->lng['logs_date']}</TD>
		<TD>{$this->lng['logs_msg']}</TD>
		<TD>{$this->lng['logs_ip']}</TD>
	</TR>

col1_h;
        $this->PushOutput($out);
    }

    public function log_row($ligne) {
        $this->idcols++;
        $cls = $this->idcols%2;
        $out =<<<col1_r
	<TR class="base_row{$cls}">
		<TD>{$ligne['DATE']}</TD>
		<TD>{$ligne['LOGIN']}</TD>
		<TD>{$ligne['IP']}</TD>
	</TR>

col1_r;
        $this->PushOutput($out);
    }

    public function log_footer() {
        $this->PushOutput('</table></td>');
    }

    public function DoOutput($include_menu=true, $include_header=true) {
        $this->PushOutput('</td></tr></table>');
        parent::DoOutput(); // false false ? header menu
    }

    /**
     *
     * @return tpl_eadmin
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}