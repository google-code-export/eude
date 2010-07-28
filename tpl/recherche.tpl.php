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
    protected $lng;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL."Recherche.php";
        $this->lng = language::getinstance()->GetLngBlock('recherche');

        parent::__construct();
    }

    public function estimations($maxjour,$maxheure,$maxminute,
        $minjour,$minheure,$minminute) {

        $info='';
        if (isset($minminute)) {
            $this->lng['result_min_time'] = sprintf($this->lng['result_min_time'], $maxjour, $maxheure, $maxminute);
            $this->lng['result_max_time'] = sprintf($this->lng['result_max_time'], $minjour, $minheure, $minminute);
            $info = <<<i
<tr>
    <td colspan="3" class="color_header">{$this->lng['result']}</td>
</tr>
<tr>
    <td class="color_header">{$this->lng['result_min']}</td>
    <td colspan="2">{$this->lng['result_min_time']}</td>
</tr>
<tr>
    <td class="color_header">{$this->lng['result_max']}</td>
    <td colspan="2">{$this->lng['result_max_time']}</td>
</tr>
i;
        }
        $out = <<<h
<div style="position:absolute; top:50px; left:0px; width:100%; font-size: 10px;">
    <form method="post" name="Recherche" action="{$this->BASE_FILE}">
        <TABLE class="color_row1 table_center text_center">
            <tr>
                <td class="color_header" colspan=3>{$this->lng['time_done']}</td>
            </tr><tr>
                <td>{$this->lng['day']}</td>
                <td>{$this->lng['hour']}</td>
                <td>{$this->lng['minute']}</td>
            </tr><tr>
                <td>
                    <input class="color_row1 size40" type="Text" Name="Jour"/>
                </td><td>
                    <input class="color_row1 size40" type="Text" Name="Heure"/>
                </td>
                <td>
                    <input class="color_row1 size40" type="Text" Name="Minute"/>
                </td>
            <tr>
                <td colspan="3">
                    <input class="color_row0" type="submit" value="{$this->lng['btn_submit']}"/>
                </td>
            </tr>
            {$info}
        </TABLE>
        <br><br>
        <table class="color_row1 table_center">
            <tr class="color_row0">
                <td><input type="radio" value=0 name="Etat"></td>
                <td>{$this->lng['ratio_0']}</td>
                <td>0% - 2.6%</td>
            </tr><tr>
                <td><input type="radio" value=1 name="Etat"></td>
                <td>{$this->lng['ratio_1']}</td>
                <td>2.6% - 5.2%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=2 name="Etat"></td>
                <td>{$this->lng['ratio_2']}</td>
                <td>5.2% - 11%</td>
            </tr><tr>
                <td><input type="radio" value=3 name="Etat"></td>
                <td>{$this->lng['ratio_3']}</td>
                <td>11% - 16%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=4 name="Etat"></td>
                <td>{$this->lng['ratio_4']}</td>
                <td>16% - 21%</td>
            </tr><tr>
                <td><input type="radio" value=5 name="Etat"></td>
                <td>{$this->lng['ratio_5']}</td>
                <td>21% - 26%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=6 name="Etat"></td>
                <td>{$this->lng['ratio_6']}</td>
                <td>26% - 32%</td>
            </tr><tr>
                <td><input type="radio" value=7 name="Etat"></td>
                <td>{$this->lng['ratio_7']}</td>
                <td>32% - 37%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=8 name="Etat"></td>
                <td>{$this->lng['ratio_8']}</td>
                <td>37% - 42%</td>
            </tr><tr>
                <td><input type="radio" value=9 name="Etat"></td>
                <td>{$this->lng['ratio_9']}</td>
                <td>42% - 47%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=10 name="Etat"></td>
                <td>{$this->lng['ratio_10']}</td>
                <td>47% - 53%</td>
            </tr><tr>
                <td><input type="radio" value=11 name="Etat"></td>
                <td>{$this->lng['ratio_11']}</td>
                <td>53% - 58%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=12 name="Etat"></td>
                <td>{$this->lng['ratio_12']}</td>
                <td>58% - 63%</td>
            </tr><tr>
                <td><input type="radio" value=13 name="Etat"></td>
                <td>{$this->lng['ratio_13']}</td>
                <td>63% - 68%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=14 name="Etat"></td>
                <td>{$this->lng['ratio_14']}</td>
                <td>68% - 74%</td>
            </tr><tr>
                <td><input type="radio" value=15 name="Etat"></td>
                <td>{$this->lng['ratio_15']}</td>
                <td>74% - 79%</td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=16 name="Etat"></td>
                <td>{$this->lng['ratio_16']}</td>
                <td>79% - 84%</td>
            </tr><tr>
                <td><input type="radio" value=17 name="Etat"></td>
                <td>{$this->lng['ratio_17']}</td>
                <td>84% - 89% </td>
            </tr><tr class="color_row0">
                <td><input type="radio" value=18 name="Etat"> </td>
                <td>{$this->lng['ratio_18']}</td>
                <td>89% - 95% </td>
            </tr> <tr>
                <td> <input type="radio" value=19 name="Etat"></td>
                <td>{$this->lng['ratio_19']}</td>
                <td>95% - 99% </td>
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