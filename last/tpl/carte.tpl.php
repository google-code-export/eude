<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_carte extends output {
    protected $BASE_FILE = '';
    private $map;
    private $lng;

    public function __construct() {
        $this->BASE_FILE = ROOT_URL."Carte.php";
        $this->map = map::getinstance();
        $this->lng = language::getinstance()->GetLngBlock('carte');
        parent::__construct();
    }

    public function navigation() {
        $ennemis_bool		= array('1' => 'enemy', '0' => 'ga');
        $allys_bool		= array('1' => 'own', '0' => 'neutral');
        $pnj_bool		= array('1' => 'npc', '0' => 'neutral');
        $onoff_bool		= array('0' => 'Off', '1' => 'On');
        $invert_bool		= array('0' => '1', '1' => '0');

        $onoff_sc		= $onoff_bool[(($this->map->sc+1)%2)];
        $onoff_vortex           = $onoff_bool[$this->map->vortex];
        $onoff_joueur		= $onoff_bool[$this->map->joueur];
        $onoff_planete		= $onoff_bool[$this->map->planete];
        $onoff_asteroide	= $onoff_bool[$this->map->asteroide];
        $onoff_ennemis		= $onoff_bool[$this->map->ennemis];
        $onoff_allys		= $onoff_bool[$this->map->allys];
        $onoff_pnj		= $onoff_bool[$this->map->pnj];
        $img_ennemis		= $ennemis_bool[$this->map->ennemis];
        $img_allys		= $allys_bool[$this->map->allys];
        $img_pnj		= $pnj_bool[$this->map->pnj];

        $get_sc			= $invert_bool[$this->map->sc];
        $get_vortex		= $invert_bool[$this->map->vortex];
        $get_joueur		= $invert_bool[$this->map->joueur];
        $get_planete		= $invert_bool[$this->map->planete];
        $get_asteroide          = $invert_bool[$this->map->asteroide];
        $get_ennemis		= $invert_bool[$this->map->ennemis];
        $get_allys		= $invert_bool[$this->map->allys];
        $get_pnj		= $invert_bool[$this->map->pnj];
        $helpmsg 	= bulle($this->lng['helpmsg']);
        $msg_taill_inc	= bulle($this->lng['msg_taill_inc']);
        $msg_taill_dec	= bulle($this->lng['msg_taill_dec']);
        $msg_cls	= bulle($this->lng['msg_cls']);
        $msg_all_on	= bulle($this->lng['msg_all_on']);
        $msg_all_off	= bulle($this->lng['msg_all_off']);
        $msg_vortex	= bulle(sprintf($this->lng['msg_vortex'], $onoff_vortex));
        $msg_joueur	= bulle(sprintf($this->lng['msg_joueur'], $onoff_joueur));
        $msg_planete	= bulle(sprintf($this->lng['msg_planete'], $onoff_planete));
        $msg_asteroide	= bulle(sprintf($this->lng['msg_asteroide'], $onoff_asteroide));
        $msg_ennemis	= bulle(sprintf($this->lng['msg_ennemis'], $onoff_ennemis));
        $msg_allys	= bulle(sprintf($this->lng['msg_allys'], $onoff_allys));
        $msg_pirate	= bulle(sprintf($this->lng['msg_pirate'], $onoff_pnj));
        $msg_search1	= bulle($this->lng['msg_search1']);
        $msg_search2	= bulle($this->lng['msg_search2']);
        $taille_inc	= ($this->map->taille+100);
        $taille_dec	= ($this->map->taille-100);
        $search         = ( isset ($_SESSION['search']) ? $_SESSION['search']: '');

        $can_search     = (DataEngine::CheckPerms('CARTE_SEARCH'));
        $nav_size       = $can_search ? 1000:540;
        $out = <<<NAV
<div  class="color_header" style="width:100%; height:30px; top:50px; position:absolute;">
	<TABLE class="color_header" width={$nav_size}px>
		<TR>	
			<TD>
				<A {$msg_taill_inc} HREF="{$this->BASE_FILE}?taille={$taille_inc}">&nbsp;+&nbsp;</A>
				&nbsp;&nbsp;
				<A {$msg_taill_dec} HREF="{$this->BASE_FILE}?taille={$taille_dec}">&nbsp;&ndash;&nbsp;</A>
			</TD>
			<TD><img {$helpmsg} width=20 height=20 src='%IMAGES_URL%help.png'></TD>
			<TD>
				<A {$msg_cls} HREF="{$this->BASE_FILE}?sc={$get_sc}">
				<img width=20 height=20 src="%IMAGES_URL%Btn-Couleur-{$onoff_sc}.png"></A>
			</TD>
			<TD>
                            <A {$msg_all_on} HREF="{$this->BASE_FILE}?AllSwitch=1"><img width=10 height=10 src="%IMAGES_URL%Btn-Vortex-On.png"><img width=10 height=10  src="%IMAGES_URL%Btn-Planete-On.png"><img width=10 height=10  src="%IMAGES_URL%Btn-Joueur-On.png"><img width=10 height=10  src="%IMAGES_URL%Btn-Asteroide-On.png"></A><br/>
                            <A {$msg_all_off} HREF="{$this->BASE_FILE}?AllSwitch=0"><img width=10 height=10 src="%IMAGES_URL%Btn-Vortex-Off.png"><img width=10 height=10  src="%IMAGES_URL%Btn-Planete-Off.png"><img width=10 height=10  src="%IMAGES_URL%Btn-Joueur-Off.png"><img width=10 height=10  src="%IMAGES_URL%Btn-Asteroide-Off.png"></A></TD>
			<TD>
				<A {$msg_vortex} HREF="{$this->BASE_FILE}?vortex={$get_vortex}">
				<img width=18 height=18 src="%IMAGES_URL%Btn-Vortex-{$onoff_vortex}.png"></A>
			</TD>
			<TD>
				<A {$msg_joueur} HREF="{$this->BASE_FILE}?joueur={$get_joueur}">
				<img width=18 height=18 src="%IMAGES_URL%Btn-Joueur-{$onoff_joueur}.png"></A>
			</TD>
			<TD>
				<A {$msg_planete} HREF="{$this->BASE_FILE}?planete={$get_planete}">
				<img width=18 height=18 src="%IMAGES_URL%Btn-Planete-{$onoff_planete}.png"></A>
			</TD>
			<TD>
				<A {$msg_asteroide} HREF="{$this->BASE_FILE}?asteroide={$get_asteroide}">
				<img width=18 height=18 src="%IMAGES_URL%Btn-Asteroide-{$onoff_asteroide}.png"></A>
			</TD>
			<TD>
				<A {$msg_ennemis} HREF="{$this->BASE_FILE}?ennemis={$get_ennemis}">
				<img width=18 height=18 src="%IMAGES_URL%fleet_{$img_ennemis}.gif"></A>
			</TD>
			<TD>
				<A {$msg_allys} HREF="{$this->BASE_FILE}?allys={$get_allys}">
				<img width=18 height=18 src="%IMAGES_URL%fleet_{$img_allys}.gif"></A>
			</TD>
			<TD>
				<A {$msg_pirate} HREF="{$this->BASE_FILE}?pnj={$get_pnj}">
				<img width=18 height=18 src="%IMAGES_URL%fleet_{$img_pnj}.gif"></A>
			</TD>
			<TD class="size110 text_right">&nbsp;{$this->lng['msg_coords']}&nbsp;</td>
			<td id="Coord" class="size60 color_row0 text_center">- - - -</td>
NAV;
        if (DataEngine::CheckPerms('CARTE_SEARCH'))
            $out.=<<<SEARCH
<form id="searchempire" action="Carte.php" method="post" OnSubmit="return Navigateur.DoSearch();">
			<td {$msg_search1} align=right>
                        <select class="color_header" name="type">
                            <option value="emp">{$this->lng['msg_search_emp']}</option>
                            <option value="jou" selected="true">{$this->lng['msg_search_jou']}</option>
                            <option value="inf">{$this->lng['msg_search_info']}</option>
                        </select></td>
			<td {$msg_search2}><input class="color_header" type=text name=search value="{$search}"></td>
</form>
SEARCH;
        $out.=<<<f
                        </TR>
	</TABLE>
</div>
f;

        $this->PushOutput($out);
    }

    public function maparea() {
        $img = 'img.php';
//        $img = '%IMAGES_URL%Btn-Joueur-Off.png';
        $rnd	= time();
        $out = <<<MAP

	<!--// Tracé du fond //-->
	<div id="ajaxstatus" style="font-size:14px; top:80px; left:0px; position:absolute; color:white"></div>
	<div id="divcarteunivers" style="font-size:2px; top:80px; left:0px; width:{$this->map->taille}px; height:{$this->map->taille}Px; position:absolute; cursor: crosshair; text-align:center">
	<img id="carteunivers" src="{$img}?{$rnd}">
	</div>   			
	<div id="cibleurV" class="color_cibleur" style="width:1px; height:{$this->map->taille}px; z-index:3; position:absolute; visibility:hidden;cursor: crosshair"></div>
	<div id="cibleurH" class="color_cibleur" style="width:{$this->map->taille}px; height:1px; z-index:3; position:absolute; visibility:hidden;cursor: crosshair"></div>

MAP;
        $this->PushOutput($out);
    }

    public function itineraire_header() {
        $out = <<<iti_h
	<div id="AjaxCarteDetails"
	style="z-index:6; position:absolute; left:{$this->map->taille}px; top:90px; visibility:hidden; background-color:black; color:white">
	</div>
	<div id="Map_Itineraire" style="position:absolute; left:{$this->map->taille}px; top:90px;">
		<form name="calculer" method="post" action="Carte.php">
			<Table class="color_row0 table_nospacing" style="width:425px">
				<tr>
					<td colspan=4 class="color_header text_center">{$this->lng['parcours_header']}</td>
				</tr>
				<tr>
					<td class="spacing_row0">
						{$this->lng['parcours_select']}:
					</td>
					<td class="spacing_row0" colspan=2>
						<select id="fleet" name="loadfleet" class="color_row1">
							<option value=0>{$this->lng['parcours_option']}</option>
iti_h;
        $this->PushOutput($out);
    }


    public function itineraire_form() {
        $checked = $this->map->inactif ? " checked": "";
        $checked2 = $this->map->nointrass ? " checked": "";
        $msg_load=bulle($this->lng['parcours_msg_load']);
        $msg_save=bulle($this->lng['parcours_msg_save']);
        $msg_del =bulle($this->lng['parcours_msg_del']);
        $msg_inv =bulle($this->lng['parcours_msg_inv']);
        $out = <<<iti_h
					</select>
					</td>
					<td align=right nowrap>
						<a href="javascript:void(0);" OnClick="Navigateur.LoadFleet();" {$msg_load}>C</a>-<a href="javascript:void(0);" OnClick="Navigateur.SaveFleet();" {$msg_save}>E</a>-<a  href="javascript:void(0);" OnClick="Navigateur.DelFleet();" {$msg_del}>S</a></td>
				</tr>
				<tr>
					<td class="spacing_row0" nowrap>{$this->lng['parcours_start_ss']}:</td>
					<td class="spacing_row0" colspan=2 align=center>
						<input class="color_row1" MAXLENGTH=5 type="text" name="coorin" style="width:50;" value="{$this->map->IN}">
					</td>
					<td rowspan=2 align=right style="cursor: pointer" OnClick="Navigateur.invertcoords();" {$msg_inv}><a href="javascript:void(0);"><-<br/>|<br/><-</a></td>
				</tr>
				<tr>
					<td class="spacing_row0" nowrap>{$this->lng['parcours_end_ss']}:</td>
					<td class="spacing_row0" colspan=2 align=center>
						<input class="color_row1" MAXLENGTH=5 type="text" name="coorout" style="width:50;" value="{$this->map->OUT}">
					</td>
				</tr>
				<tr>
					<td colspan="3" class="color_row1 spacing_row0" height="1px"></td>
					<td colspan="3" class="color_row0" height="1px"></td>
				</tr>
				<tr>
					<td colspan="3" class="spacing_row0">{$this->lng['parcours_old_wormhole']}</td>
					<td align=center>
						<input class="color_row1" type="checkbox" name="inactif" value="1"{$checked}>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="spacing_row0">{$this->lng['parcours_nointrass']}</td>
					<td align=center>
						<input class="color_row1" type="checkbox" name="nointrass" value="1"{$checked2}>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="color_row1" height="1px"></td>
				</tr>
				<tr>
					<td colspan=4>
						<select name="method" class="color_row1" align=left>
							<option value="1">{$this->lng['parcours_method_1']}</option>
							<option value="2">{$this->lng['parcours_method_2']}</option>
							<option value="3">{$this->lng['parcours_method_3']}</option>
							<option value="10" selected>{$this->lng['parcours_method_10']}</option>
                                                </select>
						<input align=right border=0 src="%BTN_URL%do_parcours.png" type=image Value=submit align="middle" onclick="document.getElementsByName('loadfleet')[0].selectedIndex=0;">
					</td>
				</tr>
            </Table>
iti_h;
        $this->PushOutput($out);
    }

    public function Parcours_Start($ss) {
        $out = <<<ps
		<td>&nbsp;</td>
			<Table class="color_row0 table_nospacing" style="width:425px">
				<tr>
					<td colspan=4 class="color_header text_center">Parcours</td>
				</tr>
				<TR>
					<TD class="spacing_row0">{$this->lng['parcours_start']}</TD>
					<TD class="spacing_row0" align=center colspan=2>{$ss}</TD>
					<TD align=center>0 pc</TD>
				</TR>
ps;
        $this->PushOutput($out);
    }

    public function Parcours_Row($vortex,$IN,$OUT,$dist) {
        $vortex = sprintf($this->lng['parcours_bywormhole'], $vortex);
        $out = <<<pr
				<TR>
					<TD class="spacing_row0">{$vortex}</TD>
					<TD class="spacing_row0" align=center colspan=2>{$IN} <-> {$OUT}</TD>
					<TD align=center>{$dist} pc</TD>
				</TR>
pr;
        $this->PushOutput($out);
    }

    public function Parcours_End($d,$db,$dt,$dd,$p) {
        $out = <<<pe
				<TR>
					<TD class="spacing_row0">{$this->lng['parcours_end']}</TD>
					<TD class="spacing_row0" align=center colspan=2>{$p}</TD>
					<TD align=center>{$d} pc</TD>
				</TR>
				<TR>
					<TD class="spacing_row0">{$this->lng['parcours_diff']}</TD>
					<TD class="spacing_row0" align=center colspan=2>{$db} pc - {$dd} pc =</TD>
					<TD align=center>{$dt} pc</TD>
				</TR>
pe;
        $this->PushOutput($out);
    }

    public function Legend() {
        $cls = DataEngine::config('MapColors');
        $id  = $this->map->itineraire ? 0: $this->map->sc+1;
        $out = <<<h
            </Table>
		</form>
	<Table class="color_row0">
		<tr>
			<td class="color_header text_center" colspan=2>{$this->lng['legend']}</td>
		</tr>
h;
        $legend  = $this->lng['maplegend'][$id];
        $i=0;
        foreach($legend as $k => $v) {
            $Ð = $i%2;
            $out .= <<<l
		<tr class="color_row{$Ð}">
			<td class="size20" style="background-color: {$cls[$id][$k]};">&nbsp;</td>
			<td>&nbsp;{$v}&nbsp;</td>
		</tr>
l;
           $i++;
        }
        $out .=<<<f
            </Table>
	</div>
f;
        $this->PushOutput($out);
    }

    public function javascript($TabData) {
        $TailleCase = floor($this->map->taille/100);
        $out = <<<JS

<script type="text/javascript" src="./Script/carte.js?{$this->version}"></script>
<script type='text/javascript'>
                {$TabData}
	var Carte = new CCarte({$TailleCase},100,TabData);
	Carte.init();
	delete TabData;
</script>
JS;
        $this->PushOutput($out);
    }

    public function DoOutput($include_menu=true, $include_header=true) {
        parent::DoOutput();
    }

    /**
     * @return tpl_carte
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}