<?php
/**
 * @Author: Wilfried.Winner
 * $Revision: Triangulation v1.4.2.1
 * info svn: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/

//require_once('../../init.php');
//require_once(INCLUDE_PATH.'Script.php');
//require_once(CLASS_PATH.'map.class.php'); // requis par ownuniverse

//------------------------------------------------------------------------------
function carre($nbre) {
return $nbre*$nbre;
}
//------------------------------------------------------------------------------
function calcdist($sys1,$sys2) {
//Pour avoir l'abscisse des coordonnées du système 1 [syst1 = 2456 ax = 2456%100]
//Pour avoir l'ordonée des coordonnées du système 1 [syst1 = 2456 ay = (2456-2456%100)/100]
	list($syst1y, $syst1x)=map::ss2xy($sys1);
	list($syst2y, $syst2x)=map::ss2xy($sys2);
	return round(round(round(sqrt(carre($syst1x-$syst2x)+carre($syst1y-$syst2y)),2),1),0);
} 

//------------------------------------------------------------------------------
/* Tableau de resultat a deux dimensions. 
Les resultat de la triangulation de differentes planetes (A B C D E) 
Sur la meme ligne les resultats des la triangulation de chaque planete
Sur la collonne correspondante les resultats de $Resultat[1]s probrables. 

$$Resultat[1][1][1] ==> coordonnees 1 du $Resultat[1] de la planete A
$$Resultat[1][1][2] ==> coordonnees 2 du $Resultat[1] de la planete A
.
$$Resultat[1][1][n] ==> coordonnees n du $Resultat[1] de la planete A
..
$$Resultat[1][2] ==> flag de condition de la planete A 
..
$$Resultat[1][3] ==> reponse retournee? oui = 1, non = 0
..
$$Resultat[1][4] ==> reponse probrable de la planete A

*/

 function findSyst($syst1,$syst2,$syst3,$dist1,$dist2,$dist3){
	//L'une des coodonnees ou distance invalide. Triangulation faite en se basant sur quatre données
	 $Resultat = array();
	 $flag = 3;
	 $Rep = 1;
	 $Cmpt = 1;

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
					$Resultat[1][$cmpt] = $i;
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
					$Resultat[1][$cmpt] = $i;
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
					$Resultat[1][$cmpt] = $i;
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
					$Resultat[1][$cmpt] = $i;
					$flag = 1;
					$cmpt++;
				}
			}$Rep = 0;
		}	
		if ($Rep == 0)
		{
			$Resultat[2] = $flag;
			$Resultat[3] = $Rep;
			$Resultat[4] = $Cmpt;
			return $Resultat;
		}
		else 
		{ $Resultat[1][1] = 0;
			$Resultat[2] = $flag;
			$Resultat[3] = $Rep;
			$Resultat[4] = 0;
			return $Resultat;
		}
}
//------------------------------------------------------------------------------
 function DisplayResult($TabResult, $NumPla){
	if ($TabResult[2] == 0 && $TabResult[3] != 1 && $TabResult[1][2]==$bidon)
	{
		$Affichage = ('La planète '.$NumPla.' est dans le sytème : '.$TabResult[1][1].'<br />');
		$TabResult[3] = 1;
	} 
	else if ($TabResult[2] == 0 && $TabResult[3] != 1 && $TabResult[1][2]!='')
	{
			for($i=1; $i <= $TabResult[4]+1; $i++)
			{ 
				if($i == 1)
					$Affichage = ('La planète '.$NumPla.' est soit dans le sytème : '.$TabResult[1][$i].' <br />');
				else if ($TabResult[1][$i]!=''){
					$Affichage = $Affichage . (' ou '.$TabResult[1][$i].' <br />');
		  	}
		    $TabResult[3] = 1;
		  }
	} 
	else if ($TabResult[2] == 1 && $TabResult[3] != 1)
	{   
			for($i=1; $i <= $TabResult[4]+1; $i++)
			{ if($i == 1){
					$Affichage = ('<font color=orange>La distance ou les coordonnées d\'un des centre de communications <br />relatives à la planète '.$NumPla.' est erronée.<br /></font>');
					$Affichage = $Affichage .  ('La planète '.$NumPla.' peut être dans le sytème : '.$TabResult[1][$i].' <br />');	
				}
				else if ($TabResult[1][$i]!=$bidon){
					$Affichage = (' ou '.$TabResult[1][$i].' <br />');
				}
				$TabResult[3] = 1;
			}
	}
	else if ($TabResult[2] == 2 && $TabResult[3] != 1){
			$Affichage = ('Pas de solution, les données relatives à la planète '.$NumPla.' sont incorrectes.<br />');
			$TabResult[3] = 1;
	}
	else if (($TabResult[2] == 3)&& ($TabResult[3] == 1)){
			$Affichage = ('Les données relatives à la planète '.$NumPla.' sont hors intervalle. Triangulation impossible (:D).<br />');
			$TabResult[3] = 1;
		}
	else 
	{
		$Affichage = ('Erreur inconnue, désoler. Faites ressayer ou contacter freeman ..??..<br /><br />');
	}
return $Affichage;
}