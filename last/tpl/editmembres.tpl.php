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
$tpl = tpl_editmembres::getinstance();
$tpl->DoOutput();
*/
class tpl_editmembres extends output {
    protected $BASE_FILE = '';

    private $cols_cls=array('#d6d6d6','#CCCCCC','#AAAAAA');

    public function __construct() {
        $this->BASE_FILE = ROOT_URL."editmembres.php";

        parent::__construct();
    }

    public function header($myget, $TriMembre, $triPermission,
            $TriPoints, $TriRace, $TriShip, $TriModif) {
        $out = <<<h
<TABLE align="center">
<TR>
<TD class="color_bigheader text_center">Liste des membres</TD>
</TR><TR>
<TD>
<TABLE bgcolor="#d6d6d6" style="font-size:11;">
<form name="ModifJoueur" method="post" action="editmembres.php{$myget}" onSubmit="return validateform(this);">
<TR valign="top" bgcolor="{$this->cols_cls[2]}">
<TD onclick="location.href='./editmembres.php?TriMembre={$TriMembre}';">Membre</TD>
<TD onclick="location.href='./editmembres.php?TriGrade={$TriGrade}';">Grade</TD>
<TD onclick="location.href='./editmembres.php?TriPermission={$TriPermission}';">Accès</TD>
<TD onclick="location.href='./editmembres.php?TriPoints={$TriPoints}';">Points</TD>
<TD>Eco/ Rec/ Comb/ Cons/ Navi</TD>
<TD onclick="location.href='./editmembres.php?TriRace={$TriRace}';">Race</TD>
<TD onclick="location.href='./editmembres.php?TriShip={$TriShip}';">Dernier Chassis dispo</TD>
<TD onclick="location.href='./editmembres.php?TriModif={$TriModif}';">Dernière connexion</TD>
h;
        $this->PushOutput($out);
        if(DataEngine::CheckPerms('MEMBRES_NEWPASS'))
            $this->PushOutput('<td>nouveau mot de passe</td>');
        if(DataEngine::CheckPerms('MEMBRES_DELETE'))
            $this->PushOutput('<td>Effacement</td>');
    }

    public function row($i, $ligne, $Grades, $tabrace, $axx) {
        $combograde='';
        $comborace='';
        $clr = $this->cols_cls[$i%2];
        foreach($Grades as $v) {
            $combograde.="<option value='".$v["GradeId"]."' "
                    .($v["GradeId"]==$ligne["Grade"] ? "selected" : "").">"
                    .$v["Grade"]."</option>";
        }
        /* Liste des Inputs
                ModifJoueur : Caché id du joueur
                ModifGrade  - OldGrade
                ModifPermission    - OldPermission
                ModifPoints - OldPoints
                ModifDon    - OldDon
                ModifRace   - OldRace
        */
        $out = <<<r
    <input type="hidden" name="ModifJoueur{$i}" value="{$ligne['Joueur']}">
    <input type="hidden" name="OldGrade{$i}" value="{$ligne['Grade']}">
    <input type="hidden" name="OldPermission{$i}" value="{$ligne['Permission']}">
    <input type="hidden" name="OldPoints{$i}" value="{$ligne['Points']}">
    <input type="hidden" name="OldRace{$i}" value="{$ligne['Race']}">
    <TR bgcolor={$clr}>
    <TD>{$ligne['Joueur']}</TD>
    <TD><select style="font-size:10;" name="ModifGrade{$i}">
                {$combograde}
    </TD><td><select name='ModifPermission{$i}'>
r;
        $this->PushOutput($out);

        if (array_key_exists($ligne['Permission'], $axx))
            $this->SelectOptions($axx, $ligne['Permission']);
        else
            $this->PushOutput('<option value="'.$ligne['Permission'].'">[Valeur verrouillé]</option>');

        foreach ($tabrace as $v) {
            $comborace.=	'<option value="'.$v.'"'.($ligne['Race']==$v ? ' selected' : '').'>'.$v.'</option>';
        }
        $out = <<<r2
    </select></td>
    <TD><INPUT style="font-size:10; width=80;" type="text" name="ModifPoints{$i}" value="{$ligne['Points']}"></TD>
    <TD>{$ligne['Economie']}/{$ligne['Commerce']}/{$ligne['Recherche']}/{$ligne['Combat']}/{$ligne['Construction']}/{$ligne['Navigation']}</TD>
    <TD><select id="INtableau" name="ModifRace{$i}">
    <option value="">&nbsp;</option>
                {$comborace}
    </TD>
    <TD>{$ligne['ship']}</TD>
    <TD>{$ligne['Date']}</TD>
r2;
        $this->PushOutput($out);

        if(DataEngine::CheckPerms('MEMBRES_NEWPASS'))
            $this->PushOutput('<TD><INPUT id="INTableau110" type="password" name="pass'.$i.'" value=""/></TD>');
        if(DataEngine::CheckPerms('MEMBRES_DELETE'))
            $this->PushOutput('<TD><INPUT type="checkbox" name="Suppr'.$i.'" value="1">Effacer</TD>');


        $this->PushOutput('</tr>');
    }

    public function footer() {
        $cols = 9;
        if(DataEngine::CheckPerms('MEMBRES_NEWPASS')) $cols++;
        if(DataEngine::CheckPerms('MEMBRES_DELETE')) $cols++;
        
        $out = <<<f
            <TR align=right><TD Colspan=$cols><input id="INBTtableau" type="submit" value="Modifier"></TD></TR>
</Form>
</TABLE>
</TD>
</TR></TABLE>
f;
        $this->PushOutput($out);
    }
    public function DoOutput($include_menu=true, $include_header=true) {
        parent::DoOutput($include_menu, $include_header);
    }

    /**
     * @return tpl_editmembres
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}