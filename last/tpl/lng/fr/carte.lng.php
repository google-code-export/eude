<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

$lng = array();

// Bulles d'information 'Carte.php'
$lng['helpmsg'] = <<<MSG
            <b>Utilisation de la carte:</b><br/>
Clique Gauche: Sélection du point d'origine<br/>
Clique Droit: Sélection du point d'arrivé<br/>
Maj/Ctrl + Clique: Visualisation du détail du système
MSG;
$lng['msg_taill_inc'] = 'Taille de carte +';
$lng['msg_taill_dec'] = 'Taille de carte &#151;';
$lng['msg_cls']       = 'Couleurs de carte';
$lng['msg_all_on']    = 'Tout activer';
$lng['msg_all_off']   = 'Tout désactiver';
$lng['msg_vortex']    = 'Vortex: %s';
$lng['msg_joueur']    = 'Joueurs: %s';
$lng['msg_planete']   = 'Planètes: %s';
$lng['msg_asteroide'] = 'Astéroïdes: %s';
$lng['msg_ennemis']   = 'Ennemis: %s';
$lng['msg_allys']     = 'Alliés: %s';
$lng['msg_pirate']    = 'Flottes pirate: %s';
$lng['msg_search1']   = 'Choix empire/joueur';
$lng['msg_search2']   = 'Touche Entrée pour faire la recherche';
$lng['msg_search_emp']= 'Empire';
$lng['msg_search_jou']= 'Joueur';

// Menu pour le calcul
$lng['parcours_header']= 'Navigateur';
$lng['parcours_select']= 'Parcours';
$lng['parcours_option']= '[Sélectionner votre parcours]';
$lng['parcours_msg_load']= 'Charger parcours';
$lng['parcours_msg_save']= 'Enregistrer parcours';
$lng['parcours_msg_del']= 'Supprimer parcours';
$lng['parcours_msg_inv']= 'Intervertir les coords';
$lng['parcours_start_ss']= 'Système de départ';
$lng['parcours_end_ss']= 'Système d\'arrivée';
$lng['parcours_old_wormhole']= 'Utiliser les vortex "Inactif"';
$lng['parcours_method_1']= '1 vortex max (calcul rapide)';
$lng['parcours_method_2']= '2 vortex max (normal)';
$lng['parcours_method_3']= '3 vortex max (calcul \'très\' lent)';
$lng['parcours_method_10']= 'Au plus proche (très speed)';
$lng['parcours_start']= 'Départ';
$lng['parcours_bywormhole']= 'Vortex %s';
$lng['parcours_end']= 'Arrivée';
$lng['parcours_diff']= 'Différence';

// Bulles sur la carte...
// Ne pas utiliser de guillemets
$lng['map_ownplanet']= '<b>Votre planète: %s</b>'; // planet name
$lng['map_empire_header']= '<b>%d Membre(s) %s</b>'; // number / empire
$lng['map_alliance_header']= '<b>%d Membre(s) d\'une alliance/pna</b>'; // number
$lng['map_search_header']= '<b>Recherche: %d résultat(s):</b>'; // number
$lng['map_player_header']= '<b> %d Joueur(s)</b>'; // number
$lng['map_ennemy_header']= '<b> %d Ennemi(s)</b>'; // number
$lng['map_pnj_header']= '<b> %d Flotte(s) pirate</b>'; // number
$lng['map_wormhole_header']= '<b> %d Vortex</b>'; // number
$lng['map_planet_header']= '<b> %d Planète(s)</b>'; // number
$lng['map_asteroid_header']= '<b> %d Astéroïde(s)</b>'; // number

$lng['map_row_player1']= '%s (%s)'; // player / eude grade
$lng['map_row_player2']= '%s <i>(non inscrit)</i>'; // player
$lng['map_row_player3']= '%s (%s)'; // player / empire
$lng['map_row_player4']= '<font color=red>%s</font> (%s)'; // player(ennemy) / empire

$lng['map_parcours_start']= 'Départ imminent';
$lng['map_parcours_end']= 'Vous êtes arrivé';
$lng['map_parcours_wormhole']= 'Itinéraire (vortex)';
$lng['map_parcours']= '<font color=\'darkgreen\'><b>%s</b></font>';


// Légende affiché sur la page 'Carte.php'
$lng['legend'] = 'Légende';
$lng['maplegend'] = array (
        0 => array( // par calcul d'itinéraire
                1 => 'Astre quelconque',
                2 => 'Mes colonies',
                20 => 'Départ...',
                21 => 'Arrivée.',
                22 => 'Passage par vortex.',
        ),
        1 => array( // Affichage normal
                1 => 'Joueurs de l\'empire',
                2 => 'Mes colonies',
                3 => 'Joueurs',
                4 => 'Vortex',
                5 => 'Astéroïde',
//                6 => 'Planète vide / Colonie + Autre',
//                7 => 'Joueur de l\'empire + Autre',
                11 => 'Alliés',
                8 => 'Joueurs ennemi',
                9 => 'Flottes PNJ',
                10 => 'Résultat de recherche',
        ),
        2 => array( // Affichage normal
                1 => 'Joueurs de l\'empire',
                2 => 'Mes colonies',
                3 => 'Astre quelconque',
//                4 => 'Vortex',
//                5 => 'Astéroïde',
//                6 => 'Planète vide / Colonie + Autre',
//                7 => 'Joueur de l\'empire + Autre',
                11 => 'Alliés',
//                8 => 'Joueurs ennemi',
//                9 => 'Flottes PNJ',
                10 => 'Résultat de recherche',
        ),
);