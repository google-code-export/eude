<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_cartographie extends output {
    protected $BASE_FILE = '';
    protected $BASE_GET = '';
    protected $lng;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'cartographie.php';
        $this->BASE_GET  = Get_string();
        $this->lng = language::getinstance()->GetLngBlock('cartographie');
        $this->SetheaderInput();
        parent::__construct();
    }

    private function SetheaderInput() {
        $this->currow = <<<ROW
<table class="table_nospacing table_center">
    <tr><td>
        <form name="data" method="post" action="{$this->BASE_FILE}?{$this->BASE_GET}">
        <table class="table_nospacing table_center">
        <tr><td>
            <table class="table_nospacing color_row1">
                <tr><TD class="text_center color_bigheader" colspan="7">{$this->lng['add_items_header']}</TD></tr>
                <tr class="text_center">
                    <TD %%bulle%% colspan="6">
                    <input name="phpparser" type="hidden" value="0"/>
                    <TEXTAREA class="color_row1" cols="50" rows="4" name="importation"></TEXTAREA>
                    </TD>
                    <TD class="color_header">
                        <input class="color_header" onclick="interpreter(document.getElementsByName('importation')[0].value, true); GestionFormulaire();" type="button" value="{$this->lng['add_items_btn_auto']}">
                        <br/>
                        <br/>
                        <input class="color_header" onclick="interpreter(document.getElementsByName('importation')[0].value, false); GestionFormulaire();" type="button" value="{$this->lng['add_items_btn_manual']}">
                    </TD>
                </tr>
		<TR class="color_cols text_center">
                    <TD class="spacing_row1">{$this->lng['add_items_col_type']}</TD>
                    <TD class="spacing_row1">{$this->lng['add_items_col_corin']}</TD>
                    <TD class="spacing_row1">{$this->lng['add_items_col_corout']}</TD>
                    <TD class="spacing_row1">{$this->lng['add_items_col_player']}</TD>
                    <TD class="spacing_row1">{$this->lng['add_items_col_empire']}</TD>
                    <TD class="spacing_row1">{$this->lng['add_items_col_infos']}</TD>
                    <TD class="color_header">&nbsp;</TD>
		</TR>
		<TR class="text_center color_row1">
                    <TD class="spacing_row1">
                    <select class="color_row1" onchange="affichage_formulaire(this.value);" name="Type">
                        %%Type%%
                    </select>
		</TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="16" type="text" name="COORIN"  value="" %%bulle1%%/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="16" type="text" name="COOROUT" value="" %%bulle2%%/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="30" type="text" name="USER"    value="" %%bulle3%%/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="100" type="text" name="EMPIRE"  value="" %%bulle4%%/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="100" type="text" name="INFOS"  value="" %%bulle5%%/></TD>
	 	<TD class="color_header"><input class="color_header" type="submit" value="{$this->lng['add_items_btn_add']}"/></TD>
	 	</TR>	 
            </table>
        </td>
    </tr>
    <tr class="color_row0">
        <td>
        <table class="table_center color_header table_nospacing text_center" name="AddTabRessource" width="100%">
            <tr class="color_header">
                <td colspan="10">{$this->lng['add_items_planet_header']}</td>
            </tr>
            <tr class="color_row1">
                <td><img width="15" height="15" src="%IMAGES_URL%Titane.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Cuivre.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Fer.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Aluminium.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Mercure.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Silicium.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Uranium.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Krypton.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Azote.png"/></td>
                <td><img width="15" height="15" src="%IMAGES_URL%Hydrogene.png"/></td>
            </tr>
            <tr class="color_row1">
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE0" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE1" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE2" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE3" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE4" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE5" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE6" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE7" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE8" value="" /></td>
                <td><INPUT class="color_row0 size80" type="text" name="RESSOURCE9" value="" /></td>
            </tr>
        </table>
    </td></tr>
    </table>
    </form>
        </td>
    </tr>  

ROW;
        $this->curtpl = '';
    }

    public function SearchForm() {
        $this->currow = <<<ROW
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>
        <form name="search" method="post" action="{$this->BASE_FILE}?page=1">
        <table class="table_center table_nospacing color_row0" width="100%">
            <tr>
                <TD class="text_center color_bigheader" colspan="11">{$this->lng['search_header']}</TD>
            </tr>
            <tr class="color_cols text_center">
                <td class="spacing_row1">{$this->lng['search_col_type']}</td>
                <td class="spacing_row1">{$this->lng['search_col_ss']}</td>
                <td class="spacing_row1">{$this->lng['search_col_rayon']}</td>
                <td class="spacing_row1">{$this->lng['search_col_player']}</td>
                <td class="spacing_row1">{$this->lng['search_col_empire']}</td>
                <td class="spacing_row1">{$this->lng['search_col_fleet']}</td>
                <td class="spacing_row1">{$this->lng['search_col_note']}</td>
                <td class="spacing_row1">{$this->lng['search_col_maxtroops']}</td>
                <td class="spacing_row1">{$this->lng['search_col_self']}</td>
                <td class="color_header"><input OnClick="location.href='{$this->BASE_FILE}?ResetSearch=1';" class="color_header" type="button" value="{$this->lng['search_col_showall']}"/></td>
            </tr>
            <tr class="color_row1 text_center">
                <td class="spacing_row1">
                    <select class="color_row1" name="Recherche[Type]">
                            <option value="-1">&nbsp;</option>
                    %%Type%%
                    </select>
                </td>
                <td class="spacing_row1"><INPUT class="color_row1 size40" type="text" name="Recherche[Pos]" value="%%Pos%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size60" type="text" name="Recherche[Rayon]" value="%%Rayon%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[User]" value="%%User%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[Empire]" value="%%Empire%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[Infos]" value="%%Infos%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[Note]" value="%%Note%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80 text_center" type="text" name="Recherche[Troop]" value="%%Troop%%" /></td>
                <td class="spacing_row1"><INPUT class="color_row1" type="checkbox" name="Recherche[Moi]" value="1"%%checkedmoi%%/></td>
                <td class="color_header"><input class="color_header" type="submit" value="{$this->lng['search_col_btnsearch']}"/></td>
            </tr>
        </table>
        </form>
        </td>
    </tr>
ROW;
        $this->curtpl = '';
    }

    public function GetPagination($current, $max) {
        $result = '';
        if ($current>2)      $result .= '<a href="'.$this->BASE_FILE.'?'.Get_string(array('page'=>1)).'"><img src="%IMAGES_URL%Btn-Debut.png"/></a>';
        if ($current>1)      $result .= '<a href="'.$this->BASE_FILE.'?'.Get_string(array('page'=>($current-1))).'"><img src="%IMAGES_URL%Btn-Precedent.png"/></a>';
        $result .= $current.' / '.$max;
        if ($current<$max)   $result .= '<a href="'.$this->BASE_FILE.'?'.Get_string(array('page'=>($current+1))).'"><img src="%IMAGES_URL%Btn-Suivant.png"/></a>';
        if ($current<$max-1) $result .= '<a href="'.$this->BASE_FILE.'?'.Get_string(array('page'=>($max))).'"><img src="%IMAGES_URL%Btn-Fin.png"/></a>';

        return $result;
    }

    public function SearchResult() {
        $this->currow = <<<ROW
    <tr>
        <td>
        <table class="table_center table_nospacing" width="100%">
        <form name="searchresult" method="post" action="{$this->BASE_FILE}?{$this->BASE_GET}">
            <tr>
                <TD colspan="4">&nbsp;</TD>
                <TD class="text_right color_pagination" colspan="2">
                    %%pagination%%
                </TD>
            </tr>
            <tr class="text_center color_header">
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_type%%">{$this->lng['search_col_type']}</a><br/>
                                         <a href="{$this->BASE_FILE}?%%sort_udate%%">{$this->lng['search_col_date']}</a></TD>
                <TD class="spacing_row0">{$this->lng['add_items_col_corrds']}</TD>
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_user%%">{$this->lng['search_col_player']}</a>/<a href="{$this->BASE_FILE}?%%sort_empire%%">{$this->lng['search_col_empire']}</a></TD>
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_infos%%">{$this->lng['search_col_fleet']}</a><br/>
                                         <a href="{$this->BASE_FILE}?%%sort_note%%">{$this->lng['search_col_note']}</a></TD>
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_water%%">{$this->lng['search_col_water']}</a> / <a href="{$this->BASE_FILE}?%%sort_batiments%%">Bâtiments</a><br/>
                                         <a href="{$this->BASE_FILE}?%%sort_troop%%">{$this->lng['search_col_troops']}</a></TD>
                <TD class="text_right">&nbsp;</td>
            </tr>

ROW;
        $this->curtpl = '';
    }

    public function SetRowModelTypeA () {
        $this->currow = <<<ROW
            
                
            <tr class="text_center color_row%%rowA%% spacing_row">
                <TD class="color_bg spacing_row%%rowA%%" %%userdate%%>%%type%%
                    <br/>%%udate%%</TD>
                <TD class="spacing_row%%rowA%%">%%coords%%</TD>
                <TD class="spacing_row%%rowA%%">-<br/>-</TD>
                <TD class="spacing_row%%rowA%%">-<br/>
                            <input class="color_row%%rowA%%" type="text" name="item[%%id%%][NOTE]" value="%%notes%%" OnChange="CheckOn('item[%%id%%][edit]');"/></TD>
                <TD class="spacing_row%%rowA%%">%%water%%<br/>-</TD>
                <TD class="text_right color_row%%rowA%%" rowspan="3">
                    <input type="hidden" name="item[%%id%%][type]" value="%%typeid%%" />
                    %%cmd_edit%%
                    %%cmd_delete%%
                </TD>
            </tr>
            <tr class="color_row%%rowA%%">
                <TD class="spacing_row%%rowA%%">%%Titane%%</TD>
                <TD class="spacing_row%%rowA%%">%%Cuivre%%</TD>
                <TD class="spacing_row%%rowA%%">%%Fer%%</TD>
                <TD class="spacing_row%%rowA%%">%%Aluminium%%</TD>
                <TD class="spacing_row%%rowA%%">%%Mercure%%</TD>
            </tr>
            <tr class="color_row%%rowA%%">
                <TD class="spacing_row%%rowA%%">%%Silicium%%</TD>
                <TD class="spacing_row%%rowA%%">%%Uranium%%</TD>
                <TD class="spacing_row%%rowA%%">%%Krypton%%</TD>
                <TD class="spacing_row%%rowA%%">%%Azote%%</TD>
                <TD class="spacing_row%%rowA%%">%%Hydrogene%%</TD>
            </tr>
ROW;
        $this->curtpl = 'SetRowModelTypeA';

    }

    public function SetRowModelTypeB () {
        $this->currow = <<<ROW

            <tr class="text_center color_row%%rowA%% spacing_row">
                <TD class="color_bg spacing_row%%rowA%%" %%userdate%%>
                    <select class="color_row%%rowA%%" name="item[%%id%%][type]" OnChange="CheckOn('item[%%id%%][edit]');">
                        <option value="%%typeid%%"">%%type%%</option>
                        <option value="0">Joueur</option>
                        <option value="3">Allié</option>
                        <option value="5">Ennemi</option>
                    </select>
                    <br/>%%udate%%
                </TD>
                <TD class="spacing_row%%rowA%%">%%coords%%</TD>
                <TD class="spacing_row%%rowA%%">%%player%%</TD>
                <TD class="spacing_row%%rowA%%">%%infos%%<br/>
                    <input class="color_row%%rowA%%" type="text" name="item[%%id%%][NOTE]" value="%%notes%%" OnChange="CheckOn('item[%%id%%][edit]');"/>
                </TD>
                <TD class="spacing_row%%rowA%%">
                    <input class="color_row%%rowA%% text_center size40" type="text" name="item[%%id%%][WATER]" value="%%water%%" OnChange="CheckOn('item[%%id%%][edit]');"/>&nbsp;&nbsp;
					<input class="color_row%%rowA%% text_center size40" type="text" name="item[%%id%%][BATIMENTS]" value="%%batiments%%" OnChange="CheckOn('item[%%id%%][edit]');"/><br/>
                    <input class="color_row%%rowA%% text_center size110" type="text" name="item[%%id%%][TROOP]" value="%%troop%%" OnChange="CheckOn('item[%%id%%][edit]');" %%troop_date%%/></TD>
                <TD class="text_right color_row%%rowA%%">
                    %%cmd_edit%%
                    %%cmd_delete%%
                </TD>
            </tr>
ROW;
        $this->curtpl = 'SetRowModelTypeB';

    }

    public function SetRowModelTypeC () {
        $this->currow = <<<ROW

            <tr class="text_center color_row%%rowA%% spacing_row">
                <TD class="color_bg spacing_row%%rowA%%" %%userdate%%>%%type%%
                    <br/>%%udate%%</TD>
                <TD class="spacing_row%%rowA%%">%%coords%%</TD>
                <TD class="spacing_row%%rowA%%">%%player%%</TD>
                <TD class="spacing_row%%rowA%%">%%infos%%<br/>
                    <input class="color_row%%rowA%%" type="text" name="item[%%id%%][NOTE]" value="%%notes%%" OnChange="CheckOn('item[%%id%%][edit]');"/>
                </TD>
                <TD class="spacing_row%%rowA%%">-<br/>-</TD>
                <TD class="text_right color_row%%rowA%%">
                    <input type="hidden" name="item[%%id%%][type]" value="%%typeid%%" />
                    %%cmd_edit%%
                    %%cmd_delete%%
                </TD>
            </tr>
ROW;
        $this->curtpl = 'SetRowModelTypeC';

    }

    public function GetRessources($value, $a_ress) {
        $percent=0;

        if (is_numeric($value)) $percent = min(floor(max(4000,$value)/4000),10);
        elseif ($value=='') {
            return '<div class="text_center">-</div>';
        } else {
            if (mb_strpos($value,'%',0,'utf8') !== false) $percent = floor(mb_substr($value,0,-1,'utf8')/10);
            else
                switch (mb_strtolower($value, 'utf8')) {
                    case $this->lng['ress10%']: $percent = 1; break;
                    case $this->lng['ress20%']: $percent = 2; break;
                    case $this->lng['ress40%']: $percent = 4; break;
                    case $this->lng['ress50%']: $percent = 5; break;
                    case $this->lng['ress70%']: $percent = 7; break;
                    case $this->lng['ress80%']: $percent = 8; break;
                    case $this->lng['ress90%']: $percent = 9; break;
                }
        }

        $bulle = <<<o
   <img class='ress_text' src='{$a_ress['Image']}'/> &nbsp;{$a_ress['Nom']}
o;
        $bulle = bulle($bulle);
        $result = <<<o
   <span {$bulle}>
   <span class="ress_img"><img class="ress_text" src="{$a_ress['Image']}"/></span>
   <span class="ress_{$percent}">&nbsp;</span>
   <span id="ress_text">{$value}</span>
   </span>
o;

        return $result;
    }

    public function SearchResult_End () {
        $this->currow = <<<ROW
            <tr class="color_bg spacing_row">
                <TD class="text_center" colspan="4">
                    <input type="hidden" name="massedit" value="1"/>
                    <input class="color_row0" type="submit" value="{$this->lng['search_col_btndoedit']}"/></TD>
                <TD class="text_right color_pagination" colspan="2">
                    %%pagination%%
                </TD>
            </tr>
        </table>
        </form>
        
ROW;
        $this->curtpl = '';

    }

    /**
     * Génère la page
     * @param boolean,array $include_menu Inclure le menu ? (voir son propre menu)
     * @param boolean $include_header Inclure l'entete ?
     */
    public function DoOutput($include_menu=true, $include_header=true) {
        $out = <<<ROW
        </td>
    </tr>
</table>
<SCRIPT type="text/javascript">
masquer("COOROUT",0);
afficher("USER",0);
afficher("EMPIRE",0);
afficher("INFOS",0);
masquer("AddTabRessource",0);
</SCRIPT></body></html>
ROW;
        $this->PushOutput($out);
        parent::DoOutput($include_menu, $include_header);
    }
    /**
     * Next row, same tpl
     */
    public function PushRow() {
        $this->PushOutput($this->currow);
        if ($this->curtpl) call_user_func(array($this,$this->curtpl), $this);
    }
    /**
     *
     * @return tpl_cartographie
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}