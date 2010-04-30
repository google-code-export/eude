<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * @todo Upgrade
 **/

require_once('./init.php');
require_once(INCLUDE_PATH.'Script.php');

DataEngine::CheckPerms('PERSO_RESEARCH');

$Rech[0][0]=0.1;
$Rech[0][1]=2.6;
$Rech[1][0]=2.6;
$Rech[1][1]=5.2;
  $Rech[2][0]=5.2;
  $Rech[2][1]=11;
  $Rech[3][0]=11;
  $Rech[3][1]=16;
  $Rech[4][0]=16;
  $Rech[4][1]=21;
  $Rech[5][0]=21;
  $Rech[5][1]=26;
  $Rech[6][0]=26;
  $Rech[6][1]=32;
  $Rech[7][0]=32;
  $Rech[7][1]=37;
  $Rech[8][0]=37;
  $Rech[8][1]=42;
  $Rech[9][0]=42;
  $Rech[9][1]=47;
  $Rech[10][0]=47;
  $Rech[10][1]=53;
  $Rech[11][0]=53;
  $Rech[11][1]=58;
  $Rech[12][0]=58;
  $Rech[12][1]=63;
  $Rech[13][0]=63;
  $Rech[13][1]=68;
  $Rech[14][0]=68;
  $Rech[14][1]=74;
  $Rech[15][0]=74;
  $Rech[15][1]=79;
  $Rech[16][0]=79;
  $Rech[16][1]=84;
  $Rech[17][0]=84;
  $Rech[17][1]=89;
  $Rech[18][0]=89;
  $Rech[18][1]=95;
  $Rech[19][0]=95;
  $Rech[19][1]=99;
  $Rech[20][0]=99.1;
  $Rech[20][1]=99.99;
  
  
  

  
  //Systeme de calcul de la date
  if(isset($_POST["Etat"])) {
  	$jour = $_POST["Jour"];
  	$heure = $_POST["Heure"];
  	$minute = $_POST["Minute"];
  	$level = $_POST["Etat"];
  	
  	$temps = $jour*1440 + $heure*60 + $minute;
  	
  	$minrestant = ($temps * 100 / $Rech[$level][0])-$temps;
  	$maxrestant = ($temps * 100 / $Rech[$level][1])-$temps;
  	
  	$minjour = floor($minrestant/1440);  	  
  	$minheure = floor(($minrestant%1440)/60);
  	$minminute = (($minrestant%1440)%60);
  	
  	$maxjour = floor($maxrestant/1440);  	  
  	$maxheure = floor(($maxrestant%1440)/60);
  	$maxminute = (($maxrestant%1440)%60);
  }

  require_once(TEMPLATE_PATH.'recherche.tpl.php');
  $tpl = tpl_recherche::getinstance();
  $tpl->page_title = "EU2: Recherche";

  $tpl->estimations($maxjour, $maxheure, $maxminute, $minjour, $minheure, $minminute);

// TODO MAJ arbre #1969
// http://aide.empireuniverse2.fr/index.php?title=Arbre_technologique&oldid=1969
// Élement = ("[Typederecherche]","NomDeRecherche/ÉlementDébloqué","[CentreDeRechercheRequis]","[LienWiki]", [sous élément(s)] )
$arbrerech = array(
	"gene","Vol spatial", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Vol_spatial",
		array("gene","Commerce interstellaire", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Commerce_interstellaire",
			array("new","Poste de commerce", "", "http://aide.empireuniverse2.fr/index.php/Poste_de_Commerce"),
			array("gene","Université", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Universit%C3%A9",
				array("new","Université", "", "http://aide.empireuniverse2.fr/index.php/Universit%C3%A9"),
				array("gene","Communication à longue distance", "", ""),
				array("gene","Centre de Communication", "", "http://aide.empireuniverse2.fr/index.php/Centre_de_Communication"),
				array("gene","Productions améliorées", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es",
					array("gene","Productions améliorées (Titane)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (silicium)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (cuivre)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (fer)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (Krypton)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (Mercure)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (aluminium)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (uranium)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (azote)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
					array("gene","Productions améliorées (hydrogène)", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Productions_am%C3%A9lior%C3%A9es"),
				),
				array("gene","Rendement d'exploitation", "2", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Rendement_d%27exploitation",
					array("new","Chaine d'attente des usines", "", ""),
					array("gene","Étude environnementale", "10", "http://aide.empireuniverse2.fr/index.php/Technologie_:_%C3%A9tude_environnementale"),
				),
				array("gene","Amélioration de la productivité", "5", "http://aide.empireuniverse2.fr/index.php/Technologie_:_am%C3%A9lioration_de_la_productivit%C3%A9"),
				array("chassis","Programme spatial", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_programme_spatial",
					array("gene","Chantier Spatial", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Chantier_Spatial",
						array("new","Chantier Spatial", "", "http://aide.empireuniverse2.fr/index.php/Chantier_Spatial"),
						array("moteur","Technique de propulsion", "2", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Technique_de_propulsion",
							array("chassis","Vol spatial habité", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_vol_spatial_habite",
								array("equipement","Soute spatial", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_soute_spatial",
									array("equipement","Augmentation de la capacité de transport", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_augmentation_de_la_capacite_de_transport",
										array("equipement","Transport de masse", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_transport_de_masse",
											array("equipement","Technologie de compression", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Technologie_de_compression"),
											array("equipement","Transport de troupes", "6", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Transport_de_troupes",
												array("equipement","Module compact de transports de troupes", "7", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Module_compact_de_transports_de_troupes",
													array("equipement","Optimisation du transport de troupes", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Optimisation_du_transport_de_troupes"),
													array("equipement","Extension du chargement", "", ""),
												),
											),
										array("equipement","Colonisation planétaire", "5", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Colonisation_plan%C3%A9taire"),
										),
									array("equipement","Champ de débris", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_champ_de_d%C3%A9bris"),
									array("equipement","Exploitation minière spatiale", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Exploitation_mini%C3%A8re_spatiale"),
									array("gene","Stockage sécurisé", "3", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Stockage_s%C3%A9curis%C3%A9",
										array("new","Bunker", "", "http://aide.empireuniverse2.fr/index.php/Bunker"),
										),
									),
									array("moteur","Propulsion electrothermique", "2", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Propulsion_electrothermique"),
									array("gene","Armée terrestre", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_arm%C3%A9e_terrestre",
										array("new","Caserne", "", "http://aide.empireuniverse2.fr/index.php/Caserne"),
									),
									array("arme","Resonateur de puissance", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Resonateur_de_puissance",
										array("arme","Système de refroidissement", "3", "http://aide.empireuniverse2.fr/index.php/Technologie_:_syst%C3%A8me_de_refroidissement"),
									),
									array(""," ", "Vol spatial habité + Resonateur de puissance", "",
										array("chassis","Vaisseaux d'interception", "2", "http://aide.empireuniverse2.fr/index.php/Technologie_:_vaisseaux_d%27interception",
											array("moteur","Propulsion supra luminique", "propulsion electrothermique / 3", "http://aide.empireuniverse2.fr/index.php/Technologie_:_propulsion_supra_luminique",
												array("moteur","Contrôle des champs gravitationnels", "3", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Contr%C3%B4le_des_champs_gravitationnels"),
											),
											array("moteur","Propulsion ionique", "propulsion electrothermique / 3", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Propulsion_ionique",
												array("moteur","Booster à impulsion", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Booster_%C3%A0_impulsion",
													array("moteur","Catalyseur à impulsion", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Catalyseur_%C3%A0_impulsion"),
												),
												array("arme","Canons à impulsions", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Canons_%C3%A0_impulsions",
													array("arme","Super Canons à impulsions", "5", "http://aide.empireuniverse2.fr/index.php/Super_Canons_%C3%A0_impulsions"),
													array("arme","Canon double à impulsions", "construction modulaire / 6", "http://aide.empireuniverse2.fr/index.php/Canon_double_%C3%A0_impulsions",
														array("arme","Canon à plasma", "9", ""),
													),
													array("arme","Canons à ions", "5", "http://aide.empireuniverse2.fr/index.php/Canons_%C3%A0_ions",
														array("arme","Canon à ions concentrés", "6", "",
															array("arme","Double canon à ions", "", ""),
														),
													),
												),
											),
											array("chassis","Chassis structurel", "2", "http://aide.empireuniverse2.fr/index.php/Technologie_:_chassis_structurel",
												array("protect","Blindage", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_blindage",
													array("protect","Conductivité thermique amoindrie", "5", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Conductivit%C3%A9_thermique_amoindrie",
														array("protect","Enrichissement des alliages", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Enrichissement_des_alliages",
															array("protect","Alliages optimisés", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Alliages_optimis%C3%A9s",
																array("protect","Coque renforcée", "", "",
																),
															),
														),
													),
												),
												array("arme","Torpilles spatiales", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Torpilles_spatiales",
													array("arme","Charge explosive améliorée", "9", ""),
												),
												array("chassis","Construction modulaire", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Construction_modulaire",
													array("gene","Hangar de maintenance", "3", "http://aide.empireuniverse2.fr/index.php/Technologie_:_hangar_de_maintenance",
														array("new","Hangar de maintenance", "", "http://aide.empireuniverse2.fr/index.php/Hangar_de_maintenance"),
													),
													array("gene","Tactique", "", "http://aide.empireuniverse2.fr/index.php/Tactique",
														array("equipement","Balayage Radar", "", "",
															array("equipement","Modulateur de fréquences", "", ""),
														),
														array("gene","Formation universelle", "", "http://aide.empireuniverse2.fr/index.php/Formation_universelle",
															array("gene","Formation chaotique", "", "http://aide.empireuniverse2.fr/index.php/Formation_chaotique"),
														),
														array("gene","Ciblage commun", "", "http://aide.empireuniverse2.fr/index.php/Ciblage_commun",
															array("gene","Escadrille défensive", "", "http://aide.empireuniverse2.fr/index.php/Escadrille_d%C3%A9fensive"),
														),
														array("gene","Contre attaque", "", "http://aide.empireuniverse2.fr/index.php/Contre_attaque",
															array("gene","Frappe Tactique", "", "http://aide.empireuniverse2.fr/index.php/Frappe_Tactique"),
														),
														array("gene","Stratégies de défense", "", "http://aide.empireuniverse2.fr/index.php/Strat%C3%A9gies_de_d%C3%A9fense",
															array("gene","Stratégies d'attaque", "", "http://aide.empireuniverse2.fr/index.php/Strat%C3%A9gies_d%27attaque"),
														),
													),
													array("chassis","Grand Vaisseau de guerre", "Booster à impulsion / 4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Grand_Vaisseau_de_guerre",
														array("chassis","Croiseur", "", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Croiseur",
															array("chassis","Système de défense", "4", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Syst%C3%A8me_de_d%C3%A9fense",
																array("chassis","Intercepteur", "", ""),
															),
															array("chassis","Croiseur interstellaire", "Enrichissement des alliages", "http://aide.empireuniverse2.fr/index.php/Technologie_:_Croiseur_interstellaire",
																array("","Niveau GVG atteint", "", ""),
																array("moteur","Densification des champs gravitationnels", "", "",
																	array("","Moteur PEW-1", "", ""),
																	array("moteur","Stabilisation du PEW-1", "", "",
																		array("moteur","Contrôleur Warp amélioré", "", ""),
																	),
																),
																array("chassis","Sentinelle", "Alliages optimisés", "",
																	array("equipement","Petit réservoir", "", "",
																		array("equipement","Réservoir", "", ""),
																	),
																	array("protect","Bouclier énergétique (25MV) ", "", ""),
																	array("chassis","Vaisseau de combat", "", "",
																		array("protect","Bouclier énergétique (50MV)", "", "",
																			array("protect","Bouclier énergétique amélioré(100MW) ", "", ""),
																		),
																		array("chassis","Nouveau vaisseau de combat", "", "",
																			array("new","Centaure", "", ""),
																			array("chassis","Centaure perfectionné", "", ""),
																			array("moteur","Propulseur magnétoplasmadynamique", "", ""),
																		),
																	),
																),
															),
														),
													),
												),
											),
										),
									),
								),
							),
						),
					),
				), 
			),
		),
	);
/*
Gé = gene
Ch = chassis
Eq = equipement
Pp = moteur
Ar = arme
Pt = protect
*/
$rechlevel = 0;
function parseitem($currrech)
{
	global $rechlevel;

	$result  = str_pad("", $rechlevel+1, " ", STR_PAD_LEFT)."<li";
	$result .= ($currrech[0] != "") ? " class='{$currrech[0]}'>": ">";
	$result .= ($currrech[3] != "") ? "<a href='{$currrech[3]}' target='_blank'>": "";
	$result .= $currrech[1];
	$result .= ($currrech[3] != "") ? "</a>": "";
	$result .= ($currrech[2] != "") ? "<sup> ({$currrech[2]})</sup>": "";
	$result .= "</li>\n";
	return $result;
}

function parsearbre($arr)
{
	global $rechlevel;
	if ($rechlevel==0)
		$result = str_pad("", $rechlevel, " ", STR_PAD_LEFT)."<ul class='arbre'>\n";
	else
		$result = str_pad("", $rechlevel, " ", STR_PAD_LEFT)."<ul>\n";

	$currrech = array();
	$hasentry = false;

	foreach($arr as $v)
	{
		if (is_array($v)) {
			if ($hasentry)	$result .= parseitem($currrech);
			$hasentry = false;
			$currrech = array();
			$rechlevel++;
			$result .= parsearbre($v);
			$rechlevel--;
		} else {
			array_push($currrech,$v);
			if (count($currrech)==2) $hasentry = true;
		}
	}
	if ($hasentry) $result .= parseitem($currrech);
	return "$result".str_pad("", $rechlevel, " ", STR_PAD_LEFT)."</ul>\n";
}

//$tpl->PushOutput('<div style="position:absolute; top:50px; left:550px; font-size: 12px;">');
//$tpl->PushOutput(parsearbre($arbrerech));
//$tpl->PushOutput('</div>');
//$tpl->legend();
/*
Routine	maximum une heure
Très facile	jusqu'à 16 heures (donc entre 1h01 et 16h)
facile	environ 24 heures
Simple	jusqu'à 2 jours
Moyen	jusqu'à 4 jours
Difficile	jusqu'à 6 jours
Très difficile	jusqu'à 9 jours
Complexe	jusqu'à 11 jours
Défi	très long
Impossible	euh...?! 

*/

$tpl->doOutput();


