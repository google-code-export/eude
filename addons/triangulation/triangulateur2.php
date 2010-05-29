<?php
/**
 * @Author: Wilfried.Winner
 * $Revision: Triangulation v1.4.2.1
 * info svn: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/

require_once('../../init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'map.class.php'); // requis par ownuniverse
require_once('./fonction.php');

// les cinq planètes
$Planete1 = 1;
$Planete2 = 2;
$Planete3 = 3;
$Planete4 = 4;
$Planete5 = 5;

$Pla1 = false;
$Pla2 = false;
$Pla3 = false;
$Pla4 = false;
$Pla5 = false;

//Check si activé
$coordvalid = false;	

if (!addons::getinstance()->Get_Addons('triangulation')->CheckPerms()) DataEngine::NoPermsAndDie();

// Si les coordonnées de deux centre de communications avec deux mesure de distance correspondante sont rentrée dans le tableau alors $coordvalid = true;
if (isset($_POST['sys1']) && isset($_POST['sys2']) && isset ($_POST['dist11'])&& isset ($_POST['dist21']) ||
 		isset($_POST['sys1']) && isset($_POST['sys3']) && isset ($_POST['dist11'])&& isset ($_POST['dist31']) ||
 		isset($_POST['sys3']) && isset($_POST['sys2']) && isset ($_POST['dist31'])&& isset ($_POST['dist21']) ){
 		
 		$_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);    
		$_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
		$_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
		$_SESSION['ccdistance11'] = gpc_esc($_POST['dist11']); 
		$_SESSION['ccdistance21'] = gpc_esc($_POST['dist21']); 
		$_SESSION['ccdistance31'] = gpc_esc($_POST['dist31']);
		
 		$coordvalid = true;   // Faire des calculs que s'il y a une entrée
 		$Pla1 = ($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance11'] != "")&&($_SESSION['ccdistance21'] != "") ||
 						($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst3'] != "")&&($_SESSION['ccdistance11'] != "")&&($_SESSION['ccdistance31'] != "") ||
 						($_SESSION['coord_syst3'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance31'] != "")&&($_SESSION['ccdistance21'] != "");
		$dist11= $_SESSION['ccdistance11']; 
		$dist21= $_SESSION['ccdistance21'];
		$dist31= $_SESSION['ccdistance31']; 						
}
if (isset($_POST['sys1']) && isset($_POST['sys2']) && isset ($_POST['dist12'])&& isset ($_POST['dist22']) ||
 		isset($_POST['sys1']) && isset($_POST['sys3']) && isset ($_POST['dist12'])&& isset ($_POST['dist32']) ||
 		isset($_POST['sys3']) && isset($_POST['sys2']) && isset ($_POST['dist32'])&& isset ($_POST['dist22']) ){
 		
 		$_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);    
		$_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
		$_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
		$_SESSION['ccdistance12'] = gpc_esc($_POST['dist12']); 
		$_SESSION['ccdistance22'] = gpc_esc($_POST['dist22']); 
		$_SESSION['ccdistance32'] = gpc_esc($_POST['dist32']);
		
 		$coordvalid = true;//Faire des calculs que s'il y a une entrée
 		$Pla2 = ($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance12'] != "")&&($_SESSION['ccdistance22'] != "") ||
 						($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst3'] != "")&&($_SESSION['ccdistance12'] != "")&&($_SESSION['ccdistance32'] != "") ||
 						($_SESSION['coord_syst3'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance32'] != "")&&($_SESSION['ccdistance22'] != "");
		$dist12= $_SESSION['ccdistance12']; 
		$dist22= $_SESSION['ccdistance22'];
		$dist32= $_SESSION['ccdistance32'];
}

if (isset($_POST['sys1']) && isset($_POST['sys2']) && isset ($_POST['dist13'])&& isset ($_POST['dist23']) ||
 		isset($_POST['sys1']) && isset($_POST['sys3']) && isset ($_POST['dist13'])&& isset ($_POST['dist33']) ||
 		isset($_POST['sys3']) && isset($_POST['sys2']) && isset ($_POST['dist33'])&& isset ($_POST['dist23']) ){
 		
 		$_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);    
		$_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
		$_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
		$_SESSION['ccdistance13'] = gpc_esc($_POST['dist13']); 
		$_SESSION['ccdistance23'] = gpc_esc($_POST['dist23']); 
		$_SESSION['ccdistance33'] = gpc_esc($_POST['dist33']);
 		
 		$coordvalid = true;//Faire des calculs que s'il y a une entrée
 		$Pla3 = ($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance13'] != "")&&($_SESSION['ccdistance23'] != "") ||
 						($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst3'] != "")&&($_SESSION['ccdistance13'] != "")&&($_SESSION['ccdistance33'] != "") ||
 						($_SESSION['coord_syst3'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance33'] != "")&&($_SESSION['ccdistance23'] != "");
 		$dist13= $_SESSION['ccdistance13']; 
		$dist23= $_SESSION['ccdistance23'];
		$dist33= $_SESSION['ccdistance33'];
}

if (isset($_POST['sys1']) && isset($_POST['sys2']) && isset ($_POST['dist14'])&& isset ($_POST['dist24']) ||
 		isset($_POST['sys1']) && isset($_POST['sys3']) && isset ($_POST['dist14'])&& isset ($_POST['dist34']) ||
 		isset($_POST['sys3']) && isset($_POST['sys2']) && isset ($_POST['dist34'])&& isset ($_POST['dist24']) ){
 		
 		$_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);    
		$_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
		$_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
		$_SESSION['ccdistance14'] = gpc_esc($_POST['dist14']); 
		$_SESSION['ccdistance24'] = gpc_esc($_POST['dist24']); 
		$_SESSION['ccdistance34'] = gpc_esc($_POST['dist34']);
 		
 		$coordvalid = true;//Faire des calculs que s'il y a une entrée
 		$Pla4 = ($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance14'] != "")&&($_SESSION['ccdistance24'] != "") ||
 						($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst3'] != "")&&($_SESSION['ccdistance14'] != "")&&($_SESSION['ccdistance34'] != "") ||
 						($_SESSION['coord_syst3'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance34'] != "")&&($_SESSION['ccdistance24'] != "");
 		$dist14= $_SESSION['ccdistance14']; 
		$dist24= $_SESSION['ccdistance24'];
		$dist34= $_SESSION['ccdistance34'];
}

if (isset($_POST['sys1']) && isset($_POST['sys2']) && isset ($_POST['dist15'])&& isset ($_POST['dist25']) ||
 		isset($_POST['sys1']) && isset($_POST['sys3']) && isset ($_POST['dist15'])&& isset ($_POST['dist35']) ||
 		isset($_POST['sys3']) && isset($_POST['sys2']) && isset ($_POST['dist35'])&& isset ($_POST['dist25']) ){
 		
 		$_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);    
		$_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
		$_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
		$_SESSION['ccdistance15'] = gpc_esc($_POST['dist15']); 
		$_SESSION['ccdistance25'] = gpc_esc($_POST['dist25']); 
		$_SESSION['ccdistance35'] = gpc_esc($_POST['dist35']);
 		
 		$coordvalid = true;//Faire des calculs que s'il y a une entrée
 		$Pla5 = ($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance15'] != "")&&($_SESSION['ccdistance25'] != "") ||
 						($_SESSION['coord_syst1'] != "") && ($_SESSION['coord_syst3'] != "")&&($_SESSION['ccdistance15'] != "")&&($_SESSION['ccdistance35'] != "") ||
 						($_SESSION['coord_syst3'] != "") && ($_SESSION['coord_syst2'] != "")&&($_SESSION['ccdistance35'] != "")&&($_SESSION['ccdistance25'] != "");
	 	$dist15= $_SESSION['ccdistance15'];
	 	$dist25= $_SESSION['ccdistance25'];
	 	$dist35= $_SESSION['ccdistance35'];

}

$syst1= $_SESSION['coord_syst1']; 
$syst2= $_SESSION['coord_syst2'];
$syst3= $_SESSION['coord_syst3'];

require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = 'EU2: Addons triangulation';

$out = <<<form
<br />
<br />
<font color='red', size='2',>
<b>Attention!!</b> <br />Plus les distances en <i>[Pc]</i> séparant les centres de communications sont grandes plus la triangulation sera bonne <i>(min 10[Pc])</i></font>
<br />
<br />

<font color=white>


<form name="settings" action="?" method="POST">
Pour remplir les cases ci-dessous, allez dans le jeu et dans le batiment centre de communication,<br />
Puis faites des recherches avec le nom ou la planète du joueur (dans le jeu dans deux ou trois systèmes différents). <br />
Remplissez ensuite le champs reservés ci-dessous avec le coodonnée du système dans lequel se trouve le centre de communication.<br /> 
Remplissez aussi le champs reservés ci-dessous à la distance séparant le centre de communication à la planète sans son unité <i>[Pc]<i />.
<br />
<br />
<i> Coordonnées du système du centre ------- Distance en [Pc] séparant le centre de <br />
de communications -----------------------------communication de la planète à trianguler</i><br /><br />
    Centre Com. Planète 1 Planète 2 Planète 3 Planète 4 Planète 5 <br />
    Coordonnée distances distances distances distances distances <br />
    <input type="text" name="sys1" value="{$_SESSION['coord_syst1']}" size="6" />
    <input type="text" name="dist11" value="{$_SESSION['ccdistance11']}" size="4" />
    <input type="text" name="dist12" value="{$_SESSION['ccdistance12']}" size="4" />
    <input type="text" name="dist13" value="{$_SESSION['ccdistance13']}" size="4" />
    <input type="text" name="dist14" value="{$_SESSION['ccdistance14']}" size="4" />
    <input type="text" name="dist15" value="{$_SESSION['ccdistance15']}" size="4" /><br />
    
 		<input type="text" name="sys2" value="{$_SESSION['coord_syst2']}" size="6" />
    <input type="text" name="dist21" value="{$_SESSION['ccdistance21']}" size="4" />
    <input type="text" name="dist22" value="{$_SESSION['ccdistance22']}" size="4" />
    <input type="text" name="dist23" value="{$_SESSION['ccdistance23']}" size="4" />
    <input type="text" name="dist24" value="{$_SESSION['ccdistance24']}" size="4" />
    <input type="text" name="dist25" value="{$_SESSION['ccdistance25']}" size="4" /><br />

 		<input type="text" name="sys3" value="{$_SESSION['coord_syst3']}" size="6" />
    <input type="text" name="dist31" value="{$_SESSION['ccdistance31']}" size="4" />
    <input type="text" name="dist32" value="{$_SESSION['ccdistance32']}" size="4" />
    <input type="text" name="dist33" value="{$_SESSION['ccdistance33']}" size="4" />
    <input type="text" name="dist34" value="{$_SESSION['ccdistance34']}" size="4" />
    <input type="text" name="dist35" value="{$_SESSION['ccdistance35']}" size="4" /><br />    
    
    <br /> <font>L'exactitude des champ ci-dessus ne sont pas vérifiés, et considérés systématiquement comme bon.</font'><br />

    <br /><font color=green size='3'><b>Triangulateur version beta </b> </font><br /><br />

    Déterminer les coordonnées de la planète du joueur en cliquant sur le bouton "<b>Trianguler</b>". <br /><br />
    <input type="submit" value="Trianguler" /><br /> <br /> 
    
</form>
form;
$tpl->PushOutput($out);
//Debut de la triangulation
/* 
Dans le centre de communication la distance en Pc séparant deux système se calcul en faisant en utilisant la formule mathématique de la distance.
Ainsi soit un système A(ax,ay) et système B(bx,by) la distance séparant des système est D = Arrondie{[(ax-bx)^2+(ay-by)^2]^(1/2),0} en posant 
une condition sur les coordonnées 00 = 100; Prenons pour exemple le système A = 2456 ==> (ax=24, ay=56) et B = 3400 ==> (bx=34, by=00) 
avec la condition sur les coordonnées 00 égale à 100, B = 3400 ==> (bx=34, by=100). La distance séparant les deux système vaut 
D = Arrondie{[(24-34)^2+(56-100)^2]^(1/2),0} = 45[pc].
A parte de cette équation et la mesure des distances obtenues avec au moins 2 au plus 3 centres de communitation du jeu EU2 se trouvant dans des 
système différents nous pouvons retrouver les coordonnées de la planète dont on a déterminer la distance à l'aide des centres de communications.
*/
echo('<font color=white>');

if($coordvalid) {

	$Tab = array();
	
	$Tab = findSyst($syst1,$syst2,$syst3,$dist11,$dist21,$dist31);
	if ($Pla1){
		$out = DisplayResult($Tab, $Planete1);
		//$tpl->PushOutput($out);	
	}
	$Tab = findSyst($syst1,$syst2,$syst3,$dist12,$dist22,$dist32);
	if ($Pla2){
		$out = $out . DisplayResult($Tab, $Planete2);	
	}
	$Tab = findSyst($syst1,$syst2,$syst3,$dist13,$dist23,$dist33);
	if ($Pla3){
		$out = $out . DisplayResult($Tab, $Planete3);
	}	
	$Tab = findSyst($syst1,$syst2,$syst3,$dist14,$dist24,$dist34);
	if ($Pla4){
		$out = $out . DisplayResult($Tab, $Planete4);	
	}
	$Tab = findSyst($syst1,$syst2,$syst3,$dist15,$dist25,$dist35);
	if ($Pla5){
		$out = $out . DisplayResult($Tab, $Planete5);
	}	
	$tpl->PushOutput($out);
}

//------------------------------------------------
// Un petit menu perso pour l'addons
$menu = array(
    'carte' => array('%ROOT_URL%Carte.php','%IMAGES_URL%Btn-Carte.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    //'prod' => array('%ROOT_URL%ownuniverse.php','%IMAGES_URL%Btn-Production.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'triangulation' => array('%ADDONS_URL%triangulation/index.php','%ADDONS_URL%triangulation/Images/Btn-triangulation1.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'triangulation2' => array('%ADDONS_URL%triangulation/triangulateur2.php','%ADDONS_URL%triangulation/Images/Btn-triangulation2.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()));

$tpl->DoOutput($menu,true); // stoppe toute execution du script et transmet les sorties html/xml/...
// les deux 'true' étant
// 1- Inclusion du menu (html, sans effet sur xml/img)
// 2- Inclusion de l'entete de base (html, sans effet sur xml/img)


