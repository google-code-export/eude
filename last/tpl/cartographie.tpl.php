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
                        <input class="color_header" onclick='interpreter(document.getElementsByName("importation")[0].value, true); GestionFormulaire("Type");' type='button' value='Automatique'>
                        <br/>
                        <br/>
                        <input class="color_header" onclick='interpreter(document.getElementsByName("importation")[0].value, false); GestionFormulaire("Type");' type='button' value='Manuel'>
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
                    <select class="color_row1" onchange="GestionFormulaire('Type');" name="Type">

ROW;
        $this->curtpl = 'SetRowInsertManual';
    }

    private function SetRowInsertManual() {
        $this->currow = <<<ROW
                    </select>
		</TD>
	 	<TD class="spacing_row1"><input id='INtableau110' maxlength=16 type='text' name='COORIN'  value='' $bulle1/></TD>
	 	<TD class="spacing_row1"><input id='INtableau110' maxlength=16 type='text' name='COOROUT' value='' $bulle2/></TD>
	 	<TD class="spacing_row1"><input id='INtableau110' maxlength=30 type='text' name='USER'    value='' $bulle3/></TD>
	 	<TD class="spacing_row1"><input id='INtableau110' maxlength=100 type='text' name='EMPIRE'  value='' $bulle4/></TD>
	 	<TD class="spacing_row1"><input id='INtableau110' maxlength=100 type='text' name='INFOS'  value='' $bulle4/></TD>
	 	<TD class="color_header"><input class="color_header" type='submit' value='Insérer' $bulle6/></TD>
	 	</TR>	 
            </table>
        </td>
    </tr>
ROW;
        $this->curtpl = 'SetRowInsertManualExtended';
    }

    private function SetRowInsertManualExtended() {
        $this->currow = <<<ROW
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
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE0" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE1" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE2" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE3" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE4" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE5" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE6" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE7" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE8" value="" /></td>
                <td><INPUT class="color_row0 size60" type="text" name="RESSOURCE9" value="" /></td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</form>
<SCRIPT type="text/javascript">
GestionFormulaire("Type");
</SCRIPT>
ROW;
        $this->curtpl = '';
    }
    /**
     * Génère la page
     * @param boolean,array $include_menu Inclure le menu ? (voir son propre menu)
     * @param boolean $include_header Inclure l'entete ?
     */
    public function DoOutput($include_menu=true, $include_header=true) {
        $this->PushOutput('');
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