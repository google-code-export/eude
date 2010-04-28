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
$tpl = tpl_recherche::getinstance();
$tpl->DoOutput();
*/
class tpl_recherche extends output {
    protected $BASE_FILE = '';

    public function __construct() {
        $this->BASE_FILE = ROOT_URL."Recherche.php";

        parent::__construct();
    }

    public function estimations($maxjour,$maxheure,$maxminute,
        $minjour,$minheure,$minminute) {

        $info='';
        if (isset($minminute))
            $info = <<<i
<tr id="TRtableau">
    <td id="TDtableau" colspan=3 align=center>
        <font color="#DDDDDD" size="3">
            Temps restant : {$maxjour} jour(s) {$maxheure} heure(s) {$maxminute} minute(s) &agrave;
                {$minjour} jour(s) {$minheure} heure(s) {$minminute} minute(s)
        </font>
    </td>
</tr>
i;

        $out = <<<h
<div style="position:absolute; top:50px; left:0px; width:580px; font-size: 10px;">
    <form method="post" name="Recherche" action="{$this->BASE_FILE}">
        <TABLE id="Ttableau" align=center>
            <tr id="TRtableau">
                <td id="TDtableau" colspan=3>D&eacute;j&agrave; effectu&eacute;</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau" colspan=3>Dur&eacute;e</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau">Jour(s)</td>
                <td id="TDtableau">Heure(s)</td>
                <td id="TDtableau">Minute(s)</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau">
                    <input style="width:80px;" id="INtableau" type="Text" Name="Jour"/>
                </td><td id="TDtableau">
                    <input style="width:80px;" id="INtableau" type="Text" Name="Heure"/>
                </td>
                <td id="TDtableau">
                    <input style="width:80px;" id="INtableau" type="Text" Name="Minute"/>
                </td>
            <tr id="TRtableau">
                <td id="TDtableau" colspan=3 align=center>
                    <input id="INBTtableau" type="submit" value="Calculer"/>
                </td>
            </tr>
            {$info}
        </TABLE>
        <br><br>
        <table id="Ttableau" width="580px">
            <tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=0 name="Etat"></td>
                <td id="TDtableau">La recherche de la technologie vient de d&eacute;buter</td>
                <td id="TDtableau">0% - 2.6%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=1 name="Etat"></td>
                <td id="TDtableau">Les premiers pas sont toujours les plus difficiles.<br>
                    Cependant, le lancement du projet de recherche s"est d&eacute;roul&eacute; sans encomres</td>
                <td id="TDtableau">2.6% - 5.2%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=2 name="Etat"></td>
                <td id="TDtableau">Mise &agrave; part quelques petites difficult&eacute;s techniques,<br>
                    notre travail se d&eacute;roule sans probl&egrave;me jusqu"&agrave; pr&eacute;sent</td>
                <td id="TDtableau">5.2% - 11%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=3 name="Etat"></td>
                <td id="TDtableau">Nous sommes en phase de conception et d"essai des premiers prototypes de cette technologie</td>
                <td id="TDtableau">11% - 16%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=4 name="Etat"></td>
                <td id="TDtableau">Nous avons subit quelques petits revers mais nous gardons nos objectif.<br>La recherche se poursuit</td>
                <td id="TDtableau">16% - 21%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=5 name="Etat"></td>
                <td id="TDtableau">Nous avons pass&eacute; le cap du premier tier du travail.<br>
                    Notre perception de la r&eacute;alit&eacute; a grandement chang&eacute;</td>
                <td id="TDtableau">21% - 26%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=6 name="Etat"></td>
                <td id="TDtableau">Nous avons r&eacute;ussi &agrave; int&eacute;grer les indications de plusieurs experts,<br>
                    ce qui nous a permis de perfectionner encore cette technologie</td><td id="TDtableau">26% - 32%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=7 name="Etat"></td>
                <td id="TDtableau">Il a fallu faire de nombreuses modifications &agrave; la conception de la technologie.<br>
                    Cel&agrave; a occasionn&eacute; quelques retards</td><td id="TDtableau">32% - 37%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=8 name="Etat"></td>
                <td id="TDtableau">Nous en avons termin&eacute; avec les plus gros probl&egrave;mes. <br>
                    Nous sommes d&eacute;sormais pr&ecirc;t &agrave; tester cette technologie en dehors des conditions de laboratoire.</td>
                <td id="TDtableau">37% - 42%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=9 name="Etat"></td>
                <td id="TDtableau">Le premier test a &eacute;t&eacute; un &eacute;chec. <br>
                    Nos chercheurs ont d&eacute;j&agrave; effectu&eacute; des corrections afin de r&eacute;soudre les probl&egrave;mes rencontr&eacute;s</td>
                <td id="TDtableau">42% - 47%</td></tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=10 name="Etat"></td>
                <td id="TDtableau">Nous sommes &agrave; mi-chemin. Il s"agit de recherches majeures et le fonctionnement n"est pas encore optimal.<br>
                    Nous pers&eacute;v&eacute;rons pour faire aboutir la technologie</td>
                <td id="TDtableau">47% - 53%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=11 name="Etat"></td>
                <td id="TDtableau">Plus de la moiti&eacute; de la recherche a d&eacute;j&agrave; abouti.<br>
                    Nous construisons les prototypes pour les premiers essais r&eacute;els</td>
                <td id="TDtableau">53% - 58%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=12 name="Etat"></td>
                <td id="TDtableau">Malgr&eacute; quelques &eacute;checs, les nombreuses heures de dur labeur portent leurs fruits.<br>
                    Le prototype semble fonctionner correctement</td>
                <td id="TDtableau">58% - 63%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=13 name="Etat"></td>
                <td id="TDtableau">Nous avons plusieurs &eacute;quipes de chercheurs charg&eacute;es de localiser d"&eacute;ventuelles erreurs de programme<br>
                    de la technologie et de les corriger d&eacute;finitivement afin d"&eacute;viter une d&eacute;faillance ulterieur</td>
                <td id="TDtableau">63% - 68%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=14 name="Etat"></td>
                <td id="TDtableau">Aucun probl&egrave;me majeur n"a &eacute;t&eacute; r&eacute;v&eacute;l&eacute; pendant la v&eacute;rification.<br>
                    Nous sommes donc pass&eacute; &agrave; la derni&egrave;re &eacute;tape de la r&eacute;alisation</td>
                <td id="TDtableau">68% - 74%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=15 name="Etat"></td>
                <td id="TDtableau">Le sprint final vient de commencer.<br>
                    Il ne durera pas longtemps. Nous aurons bient&ocirc;t terminer ce projet de recherche</td>
                <td id="TDtableau">74% - 79%</td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=16 name="Etat"></td>
                <td id="TDtableau">Les derniers tests se d&eacute;roulent comme pr&eacute;vu,<br>nous serons pr&ecirc;t dans les temps impartis</td>
                <td id="TDtableau">79% - 84%</td>
            </tr><tr id="TRtableau"><td id="TDtableau"><input id="INtableau" type="radio" value=17 name="Etat"></td>
                <td id="TDtableau">La majeure partie des recherches est termin&eacute;e maintenant. <br>
                    Nous devons encore v&eacute;rifier quelques d&eacute;tails techniques  </td> <td id="TDtableau">84% - 89% </td>
            </tr><tr id="TRtableau">
                <td id="TDtableau"><input id="INtableau" type="radio" value=18 name="Etat"> </td>
                <td id="TDtableau">Nos chercheurs continuent de travailler sous haute pression  </td>
                <td id="TDtableau">89% - 95% </td>
            </tr> <tr id="TRtableau">
                <td id="TDtableau"> <input id="INtableau" type="radio" value=19 name="Etat"></td>
                <td id="TDtableau">Bient&ocirc;t, la nouvelle technologie sera disponible  </td>
                <td id="TDtableau">95% - 99% </td>
            </tr>
        </table>
    </form>
</div>
<style type="text/css">
    th
    {
        -moz-opacity         : 0.7;
        -khtml-opacity       : 0.7;
        border               : 1px #545454 solid;
    }

    th
    {
        background-color     : #001536;
    }

    div.centre
    {
        width				 : 800px;
        background-color     : #000000;
        -moz-opacity         : 0.7;
        -khtml-opacity       : 0.7;
        margin-left		 : 10%;
    }

    li
    {

        color 				 : #EEEEEE;
        border				 : none;
        margin-left		 	 : 1px;
        list-style			 : none;

    }

    li.new:before {color: white;}
    li.gene:before {color: #00CC33;}
    li.chassis:before {color: #646464;}
    li.equipement:before {color: #0011EE;}
    li.moteur:before {color: #EEEE66;}
    li.arme:before {color: #990000;}
    li.protect:before {color: #AA11AA;}
    li.legend:before {color: transparent;}
    li.new:before,
    li.gene:before,
    li.chassis:before,
    li.equipement:before,
    li.moteur:before,
    li.arme:before,
    li.protect:before,
    li.legend:before
    {
        display		: marker;
        content		:  '█  ';
    }
    li:before
    {
        display		: marker;
        content		: ' ' ;
    }

    /*ul.arbre a:link,a:visited
	{
		color: green !important;
	}*/

    ul.arbre li
    {
        border-left          : 1px #545454 solid;
        white-space:nowrap;
    }

    div.centre td.req:before
    {
        display		: marker;
        content		: ' *   ' ;
    }
    div.centre td.image
    {
     -moz-opacity         : 1;
     -khtml-opacity       :1;
    }
</style>
h;
        $this->PushOutput($out);
    }

    public function legend() {
        $out = <<<l
<div id='centre' style='position:absolute; top:700px; left:550px; font-size: 12px;'>
<ul class='arbre'>
		<li class="legend"><b>Légende:</b></li>
		<li class="gene">Recherche G&eacute;n&eacute;rique</li>
		<li class="chassis">Recherche Chassis</li>
		<li class="equipement">Recherche Equipement</li>
		<li class="moteur">Recherche Propulsion</li>
		<li class="arme">Recherche Arme</li>
		<li class="protect">Recherche Protection</li>
		<li class="new">Élement débloqué</li>
		<li class="legend"><sup> (x)</sup>: Centre de recherche minimum et/ou prérequis</li>
</ul>
</div>
l;
        $this->PushOutput($out);
    }
    public function DoOutput($include_menu=true, $include_header=true) {
        parent::DoOutput($include_menu, $include_header);
    }

    /**
     * @return tpl_recherche
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}