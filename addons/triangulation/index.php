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

// Check si activé
if (!addons::getinstance()->Get_Addons('triangulation')->CheckPerms()) DataEngine::NoPermsAndDie();
if (isset($_POST['sys1']) && isset ($_POST['dist1'])&& isset($_POST['sys2']) && isset ($_POST['dist2']) && isset($_POST['sys3']) && isset ($_POST['dist3'])) {
    $_SESSION['coord_syst1'] = gpc_esc($_POST['sys1']);
    $_SESSION['ccdistance1'] = gpc_esc($_POST['dist1']);
    $_SESSION['coord_syst2'] = gpc_esc($_POST['sys2']);
    $_SESSION['ccdistance2'] = gpc_esc($_POST['dist2']);
    $_SESSION['coord_syst3'] = gpc_esc($_POST['sys3']);
    $_SESSION['ccdistance3'] = gpc_esc($_POST['dist3']);
}
$coordvalid = 1;
 
$syst1= $_SESSION['coord_syst1']; 
$dist1= $_SESSION['ccdistance1']; 
$syst2= $_SESSION['coord_syst2'];
$dist2= $_SESSION['ccdistance2'];
$syst3= $_SESSION['coord_syst3'];
$dist3= $_SESSION['ccdistance3'];

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


<form name="settings" action="index.php" method="POST">
Pour remplir les cases ci-dessous, allez dans le jeu et dans le batiment centre de communication,<br />
Puis faites des recherches avec le nom ou la planète du joueur (dans le jeu dans deux ou trois systèmes différents). <br />
Remplissez ensuite le champs reservés ci-dessous avec le coodonnée du système dans lequel se trouve le centre de communication.<br /> 
Remplissez aussi le champs reservés ci-dessous à la distance séparant le centre de communication à la planète sans son unité <i>[Pc]<i />.
<br />
<br />
<i> Coordonnées du système du centre --------------------------- Distance en [Pc] séparant le centre de <br />
de communications ------------------------------------------------ communication de la planète à trianguler</i><br /><br />
    Coord. du syst. du centre de com. 1 : <input type="text" name="sys1" value="{$_SESSION['coord_syst1']}" size="16" />
    distance sans unité entre CC1 et la planète : <input type="text" name="dist1" value="{$_SESSION['ccdistance1']}" size="16" /><br />
    Coord. du syst. du centre de com. 2 : <input type="text" name='sys2' value="{$_SESSION['coord_syst2']}" size="16" />
    distance sans unité entre CC2 et la planète : <input type="text" name="dist2" value="{$_SESSION['ccdistance2']}" size="16" /><br />
    Coord. du syst. du centre de com. 3 : <input type="text" name="sys3" value="{$_SESSION['coord_syst3']}" size="16" />
    distance sans unité entre CC3 et la planète : <input type="text" name="dist3" value="{$_SESSION['ccdistance3']}" size="16" /><br /> 
 
    <br /> <font>L'exactitude des champ ci-dessus ne sont pas vérifiés, et considérés systématiquement comme bon.</font'><br />

    <br /><font color=green size='4'><b>Triangulateur version beta </b> </font><br /><br />

    Déterminer les coordonnées de la planète du joueur en cliquant sur le bouton "<b>Trianguler</b>". <br /><br />
    <input type="submit" value="Trianguler" /><br /> <br /> 
    
</form>
form;
$tpl->PushOutput($out); // ajoute le texte précédant à la sortie qui sera affiché.

if ($coordvalid) {
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
$systeme = array();
$flag = 3;
$Rep = 0;
$cmpt = 1;

	//L'une des coodonnees ou distance invalide. Triangulation faite en se basant sur quatre données
	if (($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) && 
			($syst2 >= 1) && ($syst2 <= 10000) && ($dist2 >= 1) && ($dist2 <= 140) && 
			($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140) )
		{
			$cmpt = 1;//Pour compter le nombre de système probable
			$flag = 2;
			for ($i = 1; $i <= 10000; $i++) 
			{
				if ((calcdist($i,$syst1) == $dist1) && (calcdist($i,$syst2) == $dist2)&& (calcdist($i,$syst3) == $dist3)) 
				{
					$Resultat[$cmpt] = $i;
					$flag = 0;
					$cmpt++;
				}
			}$Rep = 0; 
		}
		else if((($syst1 <= 0) || ($syst1 >= 10000) || ($dist1 < 1) || ($dist1 > 140)) && 
						($syst2 >= 1) && ($syst2 <= 10000) && ($dist2 >= 1) && ($dist2 <= 140) && 
						($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140))
		{	
			$cmpt = 1;
			$flag = 2; 
			for ($i = 1; $i <= 10000; $i++) 
			{
				if ((calcdist($i,$syst2) == $dist2) && (calcdist($i,$syst3) == $dist3)) 
				{
					$Resultat[$cmpt] = $i;
					$flag = 1;
					$cmpt++;
				}
			}$Rep = 0;
			
		}else if ((($syst2 <= 0) || ($syst2 >= 10000) || ($dist2 < 1) || ($dist2 > 140)) && 
							($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) && 
							($syst3 >= 1) && ($syst3 <= 10000) && ($dist3 >= 1) && ($dist3 <= 140))
		{	
			$cmpt = 1;
			$flag = 2;
			for ($i = 1; $i <= 10000; $i++) 
			{
				if ((calcdist($i,$syst1) == $dist1) && (calcdist($i,$syst3) == $dist3)) 
				{
					$Resultat[$cmpt] = $i;
					$flag = 1;
					$cmpt++;
				}
			}$Rep = 0;
		
		}else if ((($syst3 <= 0) || ($syst3 >= 10000) || ($dist3 < 1) || ($dist3 > 140)) && 
							($syst1 >= 1) && ($syst1 <= 10000) && ($dist1 >= 1) && ($dist1 <= 140) && 
							($syst2 >= 1) && ($syst2 <= 10000) && ($dist2 >= 1) && ($dist2 <= 140))
		{
			$cmpt = 1;
			$flag = 2;
			for ($i = 1; $i <= 10000; $i++) 
			{
				if ((calcdist($i,$syst1) == $dist1) && (calcdist($i,$syst2) == $dist2)) 
				{
					$Resultat[$cmpt] = $i;
					$flag = 1;
					$cmpt++;
				}
			}$Rep = 0;
		}

	if ($flag == 0 && $Rep != 1 && $Resultat[2]==$bidon)
	{
			$tpl->PushOutput('La planète est dans le sytème : '); $tpl->PushOutput($Resultat[1]); $tpl->PushOutput('<br />');
			$Rep = 1;
	} 
	else if ($flag == 0 && $Rep != 1 && $Resultat[2]!=$bidon)
	{
			for($i=1; $i <= $cmpt; $i++)
			{ 
				if($i == 1)
				{
					$tpl->PushOutput('La planète est soit dans le sytème : '); $tpl->PushOutput($Resultat[$i]);
				}
				else if ($Resultat[$i]!=$bidon){
					$tpl->PushOutput(' ou '); $tpl->PushOutput($Resultat[$i]);
		  	}
		  	$Rep = 1;
		  }
		  $tpl->PushOutput('<br />');
	} 
	else if ($flag == 1 && $Rep != 1)
	{   
			for($i=1; $i <= $cmpt; $i++)
			{ if($i == 1){
					$tpl->PushOutput('<font color=orange>La distance ou les coordonnées d\'un des centre de communications <br />relatives à la planète est erronée.<br /></font>');
					$tpl->PushOutput('La planète peut être dans le sytème : '); $tpl->PushOutput($Resultat[$i]); $tpl->PushOutput(' ');	
				}
				else if ($Resultat[$i]!=''){
					$tpl->PushOutput(' ou '); $tpl->PushOutput($Resultat[$i]); $tpl->PushOutput(' ');
				}
				$Rep = 1;
			}
			$tpl->PushOutput('<br />');
			
	}
	else if ($flag == 2 && $Rep != 1){
			$tpl->PushOutput('Pas de solution, les données relatives à la planète sont incorrectes.');
			$tpl->PushOutput('<br />');
			$Rep = 1;
	}
	else if (($flag == 3)&& ($Rep == 1)){
			$tpl->PushOutput('Les données relatives à la planète sont hors intervalle. Triangulation impossible (:D).');
			$tpl->PushOutput('<br />');
			$Rep = 1;
		}
	else 
	{
		$tpl->PushOutput('Erreur inconnue, désoler. Faites ressayer ou contacter freeman ..??..');
		$tpl->PushOutput('<br />');$tpl->PushOutput('<br />');
	}
}
$out;
//------------------------------------------------------------------------------
function carre($nbre) {
return $nbre*$nbre;
}

function calcdist($sys1,$sys2) {
//Pour avoir l'abscisse des coordonnées du système 1 [syst1 = 2456 ax = 2456%100]
//Pour avoir l'ordonée des coordonnées du système 1 [syst1 = 2456 ay = (2456-2456%100)/100]
	list($syst1y, $syst1x)=map::ss2xy($sys1);
	list($syst2y, $syst2x)=map::ss2xy($sys2);
	return round(round(round(sqrt(carre($syst1x-$syst2x)+carre($syst1y-$syst2y)),2),1));
} 

//------------------------------------------------
// Un petit menu perso pour l'addons
$menu = array(
    'carte' => array('%ROOT_URL%cartographie.php','%IMAGES_URL%Btn-Tableau.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    //'prod' => array('%ROOT_URL%ownuniverse.php','%IMAGES_URL%Btn-Production.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'triangulation' => array('%ADDONS_URL%triangulation/index.php','%ADDONS_URL%triangulation/Images/Btn-triangulation1.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()),
    'triangulation2' => array('%ADDONS_URL%triangulation/triangulateur2.php','%ADDONS_URL%triangulation/Images/Btn-triangulation2.png',125,'DataEngine::CheckPerms(AXX_MEMBER)', array()));

$tpl->DoOutput($menu,true); // stoppe toute execution du script et transmet les sorties html/xml/...
// les deux 'true' étant
// 1- Inclusion du menu (html, sans effet sur xml/img)
// 2- Inclusion de l'entete de base (html, sans effet sur xml/img)


