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
        $this->SetheaderInput();
        $this->lng = language::getinstance()->GetLngBlock('cartographie');
        parent::__construct();
    }

    private function SetheaderInput() {
        $this->currow = <<<ROW
<table class="table_nospacing table_center">
    <tr><td>
        <form name="data" method="post" action="{$this->BASE_FILE}?{$this->BASE_GET}">
        <table class="table_nospacing table_center color_row0">
        <tr><td>
            <table class="table_nospacing color_row0">
                <tr><TD class="text_center color_bigheader" colspan="7">Ajout des corps célestes</TD></tr>
                <tr class="text_center">
                    <TD %%bulle%% colspan="6">
                    <input name="phpparser" type="hidden" value="0"/>
                    <TEXTAREA class="color_row0" cols="50" rows="4" name="importation"></TEXTAREA>
                    </TD>
                    <TD class="color_header">
                        <input class="color_header" onclick="interpreter(document.getElementsByName('importation')[0].value, true); GestionFormulaire();" type="button" value="Automatique">
                        <br/>
                        <br/>
                        <input class="color_header" onclick="interpreter(document.getElementsByName('importation')[0].value, false); GestionFormulaire();" type="button" value="Manuel">
                    </TD>
                </tr>
		<TR class="color_cols text_center">
                    <TD class="spacing_row1">Type</TD>
                    <TD class="spacing_row1">Coordonnée Entrée</TD>
                    <TD class="spacing_row1">Coordonnée Sortie</TD>
                    <TD class="spacing_row1">Nom du joueur</TD>
                    <TD class="spacing_row1">Empire</TD>
                    <TD class="spacing_row1">Planète/Flotte</TD>
                    <TD class="color_header">&nbsp;</TD>
		</TR>
		<TR class="text_center color_row1">
                    <TD class="spacing_row1">
                    <select class="color_row1" onchange="affichage_formulaire(this.value);" name="Type">
                        %%Type%%
                    </select>
		</TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="16" type="text" name="COORIN"  value="" $bulle1/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="16" type="text" name="COOROUT" value="" $bulle2/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="30" type="text" name="USER"    value="" $bulle3/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="100" type="text" name="EMPIRE"  value="" $bulle4/></TD>
	 	<TD class="spacing_row1"><input class="color_row1 size110" maxlength="100" type="text" name="INFOS"  value="" $bulle4/></TD>
	 	<TD class="color_header"><input class="color_header" type="submit" value="Insérer" $bulle6/></TD>
	 	</TR>	 
            </table>
        </td>
    </tr>
    <tr class="color_row0">
        <td>
        <table class="table_center color_header table_nospacing text_center" name="AddTabRessource" width="100%">
            <tr class="color_header">
                <td colspan="10">Informations détaillées planète/Astéroïde</td>
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
        <form name="search" method="post" action="{$this->BASE_FILE}?{$this->BASE_GET}">
        <table class="table_center table_nospacing color_row0" width="100%">
            <tr>
                <TD class="text_center color_bigheader" colspan="9">Recherche des corps célestes</TD>
                <td class="text_center color_header"><input OnClick="location.href='{$this->BASE_FILE}?ResetSearch=1';" class="color_header" type="button" value="Afficher tout"/></td>
            </tr>
            <tr class="color_cols text_center">
                <td class="spacing_row1">Status</td>
                <td class="spacing_row1">Type</td>
                <td class="spacing_row1">SS</td>
                <td class="spacing_row1">Rayon</td>
                <td class="spacing_row1">Joueur</td>
                <td class="spacing_row1">Empire</td>
                <td class="spacing_row1">Planète/Flotte</td>
                <td class="spacing_row1">Note</td>
                <td class="spacing_row1">Moi</td>
                <td class="color_header">&nbsp;</td>
            </tr>
            <tr class="color_row1 text_center">
                <td class="spacing_row1">
                    <select class="color_row1" name="Recherche[Status]">
                            <option value="-1"%%status-1%%>&nbsp;</option>
                            <option value="0"%%status0%%>Actif</option>
                            <option value="1"%%status1%%>Inactif</option>
                    </select>
                </td>
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
                <td class="spacing_row1"><INPUT class="color_row1" type="checkbox" name="Recherche[Moi]" value="1"%%checkedmoi%%/></td>
                <td class="color_header"><input class="color_header" type="submit" value="Rechercher"/></td>
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
        if ($current>2)      $result .= '<a href="'.$this->BASE_FILE.'?page=1"><img src="%IMAGES_URL%Btn-Debut.png"/></a>';
        if ($current>1)      $result .= '<a href="'.$this->BASE_FILE.'?page='.($current-1).'"><img src="%IMAGES_URL%Btn-Precedent.png"/></a>';
        $result .= $current.' / '.$max;
        if ($current<$max)   $result .= '<a href="'.$this->BASE_FILE.'?page='.($current+1).'"><img src="%IMAGES_URL%Btn-Suivant.png"/></a>';
        if ($current<$max-1) $result .= '<a href="'.$this->BASE_FILE.'?page='.($max).'"><img src="%IMAGES_URL%Btn-Fin.png"/></a>';

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
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_type%%">Type</a></TD>
                <TD class="spacing_row0">Coordonnées</TD>
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_user%%">Joueur</a>/<a href="{$this->BASE_FILE}?%%sort_empire%%">Empire</a></TD>
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_infos%%">Nom de la planète/flotte</a><br/>
                                         <a href="{$this->BASE_FILE}?%%sort_note%%">Notes</a></TD>
                <TD class="spacing_row0"><a href="{$this->BASE_FILE}?%%sort_water%%">% d'eau</a><br/>
                                         <a href="{$this->BASE_FILE}?%%sort_troop%%">Soldats</a></TD>
                <TD class="text_right">&nbsp;</td>
            </tr>

ROW;
        $this->curtpl = '';
    }

    public function SetRowModelTypeA () {
        $this->currow = <<<ROW
            
                
            <tr class="text_center color_row%%rowA%% spacing_row">
                <TD class="color_bg spacing_row%%rowA%%" %%userdate%%>%%type%%</TD>
                <TD class="spacing_row%%rowA%%">%%coords%%</TD>
                <TD class="spacing_row%%rowA%%">-<br/>-</TD>
                <TD class="spacing_row%%rowA%%">%%infos%% <br/>
                            <input class="color_row%%rowA%%" type="text" name="item[%%id%%][NOTE]" value="%%notes%%" OnChange="CheckOn('item[%%id%%][edit]');"/></TD>
                <TD class="spacing_row%%rowA%%">-<br/>-</TD>
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
                <TD class="color_bg spacing_row%%rowA%%" %%userdate%%>%%type%%</TD>
                <TD class="spacing_row%%rowA%%">%%coords%%</TD>
                <TD class="spacing_row%%rowA%%">%%player%%</TD>
                <TD class="spacing_row%%rowA%%">%%infos%%<br/>
                    <input class="color_row%%rowA%%" type="text" name="item[%%id%%][NOTE]" value="%%notes%%" OnChange="CheckOn('item[%%id%%][edit]');"/>
                </TD>
                <TD class="spacing_row%%rowA%%">
                    <input class="color_row%%rowA%% text_center size40" type="text" name="item[%%id%%][WATER]" value="%%water%%" OnChange="CheckOn('item[%%id%%][edit]');"/> %<br/>
                    <input class="color_row%%rowA%% text_center size110" type="text" name="item[%%id%%][TROOP]" value="%%troop%%" OnChange="CheckOn('item[%%id%%][edit]');"/></TD>
                <TD class="text_right color_row%%rowA%%">
                    <input type="hidden" name="item[%%id%%][type]" value="%%typeid%%" />
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
                <TD class="color_bg spacing_row%%rowA%%" %%userdate%%>%%type%%</TD>
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
            if (strpos($value,'%') !== false) $percent = floor(substr($value,0,-1)/10);
            else
                switch (strtolower($value)) {
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
                    <input class="color_row0" type="submit" value="Valider Les modifications"/></TD>
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