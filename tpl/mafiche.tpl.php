<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_mafiche extends output {
    protected $BASE_FILE = '';
    private $lng;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'Mafiche.php';
        $this->lng = language::getinstance()->GetLngBlock('personal');
        parent::__construct();
    }

    public function header($ligne) {
        $lng['page_intro'] = sprintf($this->lng['page_intro'], ucfirst($ligne['Joueur']));

        $out = <<<h
<Table align=center class="color_row1" width=500px>

    <tr><td class="color_header" colspan=2 align=center>{$this->lng['auto_intro']}</td></tr>
    <tr><td align=center colspan=2>
    <form method='post' action='Mafiche.php'>
        <TEXTAREA class="color_row0" cols="50" rows="4" name='importation'></TEXTAREA><br/>
        <input class="color_row0" type="submit" value="{$this->lng['auto_btn']}"/>
    </form>
        </td></tr>
    <tr><td class="color_bg" colspan=2>&nbsp;</td></tr>

    <tr><td class="color_header" colspan=2 align=center>{$lng['page_intro']}</td></tr>
    <form id='mafiche' method='post' action='Mafiche.php'>
        <input type='hidden' name='JOUEUR' value='{$ligne['Joueur']}'>
h;
        $this->PushOutput($out);
    }

    public function add_row($title, $key, $value) {
        if (is_numeric($value))
            $value = DataEngine::format_number($value, true);
            if ($value=='') $value='-';
        if ($key=='')
            $out = <<<ar
<tr class="color_row0">
    <td>{$title}</td>
    <td>{$value}</td>
</tr>
ar;
        else
            $out = <<<ar
<tr class="color_row0">
    <td>{$title}</td>
    <td><input class="color_row0 text_center" name='{$key}' value='{$value}'></td>
</tr>
ar;
        $this->PushOutput($out);
    }

    public function add_row_select($title, $key, $array, $selected) {
        $out = <<<ar
<tr class="color_row0">
    <td>{$title}</td>
    <td><select class="color_row0" name='{$key}'>
		<option value=''>&nbsp;</option>
ar;
        foreach ($array as $v)
            $out.= '<option value="'.$v.'" '.($selected==$v ? 'selected' : '').'>'.$v.'</option>';

        $out .= <<<ar
            </select></td>
</tr>
ar;
        $this->PushOutput($out);
    }

    public function footer() {
        $out = <<<f
<tr>
<td class="color_header text_center" colspan=2><input class="color_header" type='submit' value='{$this->lng['manual_btn']}'</td>
</tr>
</form>

    <tr><td class="color_bg" colspan=2>&nbsp;</td></tr>

<tr><td class="color_row0"align=center colspan=2>
<form method='post'>{$this->lng['pwd_new']}
<input class="color_row0" type='password' name='pwd'><input class="color_row0" type='submit' value='{$this->lng['pwd_btn']}'></form>
</td></tr>

    <tr><td class="color_bg" colspan=2>&nbsp;</td></tr>

<tr><td class="color_header text_center" colspan=2>
<input class="color_header" type='button' value='{$this->lng['logout_btn']}' onclick='location.href="%ROOT_URL%logout.php"'>
</td></tr>

</table>
f;
        $this->PushOutput($out);
    }

    public function DoOutput($include_menu=true, $include_header=true) {
    // 		$this->PushOutput("");
        parent::DoOutput($include_menu, $include_header);
    }

    /**
     * @return tpl_mafiche
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}