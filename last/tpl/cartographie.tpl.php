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

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'cartographie.php';
        $this->SetheaderInput();

        parent::__construct();
    }

    private function SetheaderInput() {
        $this->currow = <<<ROW
<form name="data" method="post" action="{$this->BASE_FILE}">
<table class="table_nospacing table_center">
    <tr class="color_header">
        <td>
            <table class="table_nospacing color_row0">
                <tr><TD class="text_center color_bigheader" colspan="7">Ajout des corps célestes</TD></tr>
                <input name="phpparser" type="hidden" value="0">
                <tr class="text_center">
                    <TD %%bulle%% colspan="6">
                    <TEXTAREA class="color_row0" cols="50" rows="4" name="importation"></TEXTAREA>
                    </TD>
                    <TD class="color_header">
                        <input class="color_header" onclick="interpreter(document.getElementsByName('importation')[0].value, true); GestionFormulaire('Type');" type="button" value="Automatique">
                        <br/>
                        <br/>
                        <input class="color_header" onclick="interpreter(document.getElementsByName('importation')[0].value, false); GestionFormulaire('Type');" type="button" value="Manuel">
                    </TD>
                </tr>
		</TR>
		<TR class="color_cols text_center">
                    <TD class="spacing_row1">Type</TD>
                    <TD class="spacing_row1">Coordonnée Entrée</TD>
                    <TD class="spacing_row1">Coordonnée Sortie</TD>
                    <TD class="spacing_row1">Nom du joueur</TD>
                    <TD class="spacing_row1">Empire/Flotte</TD>
                    <TD class="spacing_row1">Nom de planète</TD>
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
                <td><img width="15" height"15" src="%IMAGES_URL%Titane.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Cuivre.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Fer.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Aluminium.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Mercure.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Silicium.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Uranium.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Krypton.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Azote.png"/></td>
                <td><img width="15" height"15" src="%IMAGES_URL%Hydrogene.png"/></td>
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
</form>
<SCRIPT type="text/javascript">
masquer("COOROUT",0);
afficher("USER",0);
afficher("EMPIRE",0);
afficher("INFOS",0);
masquer("AddTabRessource",0);
</SCRIPT>
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
        <form name="search" method="post" action="{$this->BASE_FILE}">
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
                <td class="spacing_row1">Infos</td>
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
                <td class="spacing_row1"><INPUT class="color_row1 size40" type="text" name="Recherche[Pos]" value="" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size60" type="text" name="Recherche[Rayon]" value="" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[User]" value="" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[Empire]" value="" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[Infos]" value="" /></td>
                <td class="spacing_row1"><INPUT class="color_row1 size80" type="text" name="Recherche[Note]" value="" /></td>
                <td class="spacing_row1"><INPUT class="color_row1" type="checkbox" name="Recherche[Moi]" value="1"%%checkedmoi%%/></td>
                <td class="color_header"><input class="color_header" type="submit" value="Rechercher"/></td>
            </tr>
        </table>
        </form>
ROW;
        $this->curtpl = '';
    }


    public function SearchResult() {
        $this->currow = <<<ROW
        <form name="searchresult" method="post" action="{$this->BASE_FILE}">
        <table class="table_center table_nospacing" width="100%">
            <tr>
                <TD colspan="5">&nbsp;</TD>
                <TD class="text_right color_pagination size180">
                    <a href="{$this->BASE_FILE}?%%pg_0%%"><img src="%IMAGES_URL%/Btn-Debut.png"/></a>
                    <a href="{$this->BASE_FILE}?%%pg_prec%%"><img src="%IMAGES_URL%/Btn-Precedent.png"/></a>
                    1 / %%maxpage%%
                    <a href="{$this->BASE_FILE}?%%pg_prec%%"><img src="%IMAGES_URL%/Btn-Suivant.png"/></a>
                    <a href="{$this->BASE_FILE}?%%pg_end%%"><img src="%IMAGES_URL%/Btn-Fin.png"/></a>
                    </TD>
            </tr>
            <tr class="text_center color_header">
                <TD class="spacing_row0">Type</TD>
                <TD class="spacing_row0">Coords (in/out)</TD>
                <TD class="spacing_row0">Joueur/Empire</TD>
                <TD class="spacing_row0">Infos/Notes</TD>
                <TD class="spacing_row0">Date/User</TD>
                <TD class="text_right">Btns</td>
            </tr>

            </tr>
            <tr class="text_center color_row0 spacing_header">
                <TD class="spacing_row0">Type  (2,4)</TD>
                <TD class="spacing_row0">Coords (in)</TD>
                <TD class="spacing_row0">Joueur</TD>
                <TD class="spacing_row0">Infos/Notes</TD>
                <TD class="spacing_row0">Date/User</TD>
                <TD class="text_right color_row0" rowspan="2">Btns</td>
            </tr>
            <tr class="text_center color_row0">
                <TD class="spacing_row0">Titane<br/>Silicium</TD>
                <TD class="spacing_row0">Cuivre<br/>Uranium</TD>
                <TD class="spacing_row0">Fer<br/>Krypton</TD>
                <TD class="spacing_row0">Aluminium<br/>Azote</TD>
                <TD class="spacing_row0">Mercure<br/>Hydrogène</TD>
            </tr>

            <tr class="text_center color_row1 spacing_header">
                <TD class="spacing_row1">Type (0,1,3,5,6)</TD>
                <TD class="spacing_row1">Coords (in/out?)</TD>
                <TD class="spacing_row1">Joueur/Empire</TD>
                <TD class="spacing_row1">Infos/Notes</TD>
                <TD class="spacing_row1">Date/User</TD>
                <TD class="text_right color_row1">Btns</td>
            </tr>
            <tr class="text_center color_row0 spacing_header">
                <TD class="spacing_row0">Type (0,1,3,5,6)</TD>
                <TD class="spacing_row0">Coords (in/out?)</TD>
                <TD class="spacing_row0">Joueur/Empire</TD>
                <TD class="spacing_row0">Infos/Notes</TD>
                <TD class="spacing_row0">Date/User</TD>
                <TD class="text_right color_row0">Btns</td>
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
        $this->PushOutput('        </td>    </tr></table>');
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