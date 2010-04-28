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

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'Membre.php';

        parent::__construct();
    }

    public function header() {
        $this->PushOutput('<table id="Membres_level">');
    }

    public function level_header() {
        $this->PushOutput('<tr><td id="Membres_level"><table id="Membres_grade"><tr>');
    }

    public function level_grade($name) {
        $this->PushOutput('<td id="Membres_grade">'.$name.'</td>');
    }

    public function level_grade_sep() {
        $this->PushOutput('</tr><tr>');
    }

    public function level_players_empty() {
        $this->PushOutput('<td id="TDtableau">&nbsp;</td>');
    }

    public function level_players_header() {
        $this->PushOutput('<td id="Membres_grade"><table id="Membres">');
    }

    public function level_player_row($joueur) {
//        FB::info($joueur);
        $ship = $editlink= '';
        if (DataEngine::CheckPerms(AXX_MODO) && $joueur['ship'] != '')
            $ship='Dernier chassis : '.$joueur['ship'].'<br/>';
        if (DataEngine::CheckPerms(AXX_ADMIN))
            $editlink=' <a href="%ROOT_URL%editmembres.php?Joueur='.$joueur['Joueur']
            .'"><img src="%IMAGES_URL%edit.png"></a> &nbsp; ';

        $joueur['Points'] = DataEngine::format_number($joueur['Points'],true);
        if (Members::CheckPerms('PERSO_OWNUNIVERSE_READONLY'))
           $joueur['Joueur'] = sprintf('<a href="%%ROOT_URL%%ownuniverse.php?showuser=%s">%1$s</a>',$joueur['Joueur']);

        $bulle = <<<bulle
{$ship}
Points : {$joueur['Points']}<br/>
Commerce : {$joueur['Commerce']}<br/>
Recherche : {$joueur['Recherche']}<br/>
Combat : {$joueur['Combat']}<br/>
Construction : {$joueur['Construction']}<br/>
Economie : {$joueur['Economie']}<br/>
Navigation : {$joueur['Navigation']}
bulle;
        $bulle = bulle($bulle);

        $out = <<<lpr
        <tr><td id="Membres"{$bulle}>{$editlink}{$joueur['Joueur']}</td></tr>
lpr;
        $this->PushOutput($out);
    }

    public function level_players_footer() {
        $this->PushOutput('</table></td>');
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
    <table>
        <tr>
            <TD id='Grade'>Ajout d'un membre</TD>
            <TD id='Grade'>Gestion des grades</TD>
        </TR>
        <TR>
            <TD id="TDtableau" valign='top'>
gh;
        $this->PushOutput($out);
        return $this;
    }
    public function Grade_AddPlayer($Grades) {
        $cdg = Config::GetDefaultGrade();
        $combograde='';
        foreach($Grades as $v) {
            $combograde.="<option value='".$v['GradeId']."'".($v['GradeId']==$cdg ? ' selected' : '').">".$v['Grade']."</option>";
        }
        $out = <<<ap
    <table style='color:#ffffff;'>
    <form name='addjoueur' autocomplete='off' method='post' action='Membres.php'>
    <tr><td id='TabMembre'>Joueur : </td><td id='TabMembre'><input type='text' name='Joueur'></td></tr>
    <tr><td id='TabMembre'>Pass : </td><td id='TabMembre'><input type='password' name='Password'></td></tr>
    <tr><td id='TabMembre'>Grade : </td>
    <td id='TabMembre'><select name='Grade'>{$combograde}</td></tr>
    <tr><td id='TabMembre'>Accès: </td><td id='TabMembre'>Membre</td></tr>
    <tr><td id='TabMembre'>Points : </td><td id='TabMembre'><input type='text' name='Points'></td></tr>
    <tr><td id='TabMembre' colspan=2><input type='submit' value='Ajouter'></tr>
    </table>
    </form>
ap;
        $this->PushOutput($out);
        return $this;
    }

    public function Grade_Sep() {
        $this->PushOutput('</td><TD id="TDtableau" valign="top" align="center">');
    }

    public function Grade_Modif_Header() {
        $out = <<<gmh
            <table style="color:#ffffff;" valign="top">
    <tr>
        <td id='TabMembre'>ID</td>
        <td id='TabMembre'>Nom</td>
        <td id='TabMembre'>Ordre</td>
        <td id='TabMembre'>Parent</td>
        <td id='TabMembre' colspan=2>&nbsp;</td>
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
<tr>
<td id='TabMembre'>{$v['GradeId']}</td>
<td id='TabMembre'><input name='GradeNom' type='text' value='{$v['Grade']}'></td>
<td id='TabMembre'><input style='width:50;' name='GradeNiv' value='{$v['Niveau']}'></td>
<td id='TabMembre'>
<select name='GradePere'>
<option value='0'$selected></option>
mg;
        foreach($Grades as $v2) {
            if ($v['GradeId'] == $v2['GradeId']) continue;
            $out .= $this->_Grade_Modif($v2, $v['Rattachement']);
        }
        $out .=<<<mg
</td>
<TD id='TabMembre'><INPUT style='font-size:10;' type='checkbox' name='GradeSuppr' value='1' onclick='alert(\"Vous allez supprimer {$v['Nom']}\")'))>Effacer</TD>
<td id='TabMembre'><input type='submit' value='Modifier'></td>
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
    <tr><td id='TabMembre'>&nbsp;</td>
    <td id='TabMembre'><input name='GradeNom' type='text' value=''></td>
    <td id='TabMembre'><input style='width:50;' name='GradeNiv' type='text' value=''></td>
    <td id='TabMembre'>
    <select name='GradePere'>
    <option value='0' selected></option>
gn;
        foreach($Grades as $value) {
            $out .= $this->_Grade_Modif($value, -1);
        }
        $out .= <<<gn
            </td>
    <td id='TabMembre' colspan=2 align=right><input type='submit' value='Insérer'></td>
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