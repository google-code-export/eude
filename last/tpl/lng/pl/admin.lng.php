<?php
/**
 * @author Alex10336
 * @translator Jhonny, Cthulhu
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

$lng = array();


$lng['page_links1']         = 'Général';
$lng['page_links2']         = 'Gestion des droits utilisateurs';
$lng['page_links3']         = 'Couleurs de la carte';
$lng['page_links4']         = 'Configuration';
$lng['page_links5']         = 'Logs';

//------------------------------------------------------------------------------
$lng['page_title']          = 'EU2: Administration, Général';
$lng['page_hlink']          = 'Un bug, une suggestion ?';
$lng['dates']               = array();
$lng['dates'][0]            = '[Aucun changement]';
$lng['dates'][1]            = 'Aujourd\'hui (tout)';
$lng['dates'][2]            = 'Dimanche dernier';
$lng['dates'][3]            = 'Dimanche précédent';
$lng['dates'][4]            = 'Hier';
$lng['dates'][5]            = 'Avant-hier';
$lng['dates'][6]            = '3 Jours';
$lng['dates'][7]            = '4 Jours';
$lng['dates'][8]            = '5 Jours';
$lng['dates'][9]            = '6 Jours';
$lng['dates'][10]           = '7 Jours';
$lng['dates'][11]           = '15 Jours';
$lng['dates'][12]           = '1 Mois (premier du mois)';
$lng['dates'][13]           = '2 Mois (premier du mois)';
$lng['dates'][14]           = '3 Mois (premier du mois)';
$lng['dates'][15]           = '6 Mois (premier du mois)';
$lng['dates'][16]           = '9 Mois (premier du mois)';
$lng['dates'][17]           = '12 Mois (premier du mois)';
$lng['dates'][20]           = 'Tous';

$lng['vortex_cron_enable']    = 'Mode auto inactif (<a href="%ROOT_URL%EAdmin.php?switch=vortex_cron">Activer ?</a>)';
$lng['vortex_cron_enabled']   = 'Dernière fois le <font color=green>%s</font>';
$lng['vortex_cron_disable']   = 'Mode auto actif (<a href="%ROOT_URL%EAdmin.php?switch=vortex_cron">désactiver</a>)';
$lng['vortex_title']          = 'Nettoyage des vortex:';
$lng['vortex_do_now']         = "Nettoyer\nmaintenant";
$lng['vortex_servertime']     = 'Temps serveur:';
$lng['vortex_whathappen']     = 'Les Vortex plus anciens que "%s" seront désactivé.<br/>Les Vortex inactifs (depuis %s) seront supprimés.';
$lng['vortex_result']         = '%d vortex supprimé(s), et %d désactivé(s).';


$lng['empire_switch']         = 'Changement des nom d\'empire: (Noms simplifiés)';
$lng['empire_switch_btn']     = 'Changer';
$lng['empire_switch_current'] = 'Original:';
$lng['empire_switch_current_sel'] = '[Selectionner un empire]';
$lng['empire_switch_new']     = 'Nouveau';
$lng['empire_switch_new_sel'] = '[Supprimer l\'empire]';
$lng['empire_switch_result']  = '%d joueurs modifié avec le nouvel empire.';

$lng['empire_allys']          = 'Déclaration d\'alliance à un empire: (Noms simplifiés)';
$lng['empire_allys_sel']      = '[Selectionner un empire]';
$lng['empire_allys_add']      = 'Ajouter';
$lng['empire_allys_del']      = 'Enlever';
$lng['empire_allys_empty']    = 'Pas d\'alliance en cours...';

$lng['empire_wars']           = 'Déclaration de guerre à un empire: (Noms simplifiés)';
$lng['empire_wars_sel']       = '[Selectionner un empire]';
$lng['empire_wars_add']       = 'Ajouter';
$lng['empire_wars_del']       = 'Enlever';
$lng['empire_wars_empty']     = 'Pas de guerre en cours...';

$lng['empire_allyswars']      = 'Forcer la mise à jour les information sur les alliés/guerres/neutre';
$lng['empire_allyswars_upd']  = 'MAJ';
$lng['empire_allyswars_result0'] = '%d joueurs modifié avec le \'nouveau\' status d\'allié.';
$lng['empire_allyswars_result1'] = '%d joueurs modifié avec le \'nouveau\' status d\'ennemis.';

$lng['cleaning_items']        = 'Nettoyage divers...';
$lng['cleaning_act']          = 'Plus anciens que';
$lng['cleaning_btn']          = 'Nettoyer';
$lng['cleaning_joueurs']      = 'Suppression des Joueurs/Alliés/Ennemis';
$lng['cleaning_joueurs_result'] = '%d joueurs supprimé';
$lng['cleaning_pnj']          = 'Suppression des Flottes PNJ';
$lng['cleaning_pnj_result']   = '%d flottes PNJ supprimé';
$lng['cleaning_wormshole']          = 'Suppression des vortex';
$lng['cleaning_wormshole_result']   = '%d vortex supprimé';
$lng['cleaning_planetes']     = 'Suppression des Planètes';
$lng['cleaning_planetes_result'] = '%d planètes supprimé';
$lng['cleaning_asteroides']   = 'Suppression des Astéroïdes';
$lng['cleaning_asteroides_result'] = '%d astéroïdes supprimé';
$lng['cleaning_inactif']      = 'Suppression des éléments inactifs';
$lng['cleaning_inactif_result'] = '%d éléments inactifs supprimé';

$lng['cleaning_add_coords_unique_index'] = 'Rechercher des doublons dans la base de donnée (cartographie)';
$lng['cleaning_orphan_planets'] = 'Rechercher des éléments orphelin dans la base de donnée (cartographie)';
$lng['regen_buttons']     = 'Regénérer les boutons (en cas de modification ou Mise à jour)';
$lng['regen_buttons_inwork'] = 'Régénération des bouton en cours...<br/>Pensez a vider votre cache après.';
$lng['regen_buttons_btn'] = 'Regénérer';

//------------------------------------------------------------------------------
$lng['perms_title']         = 'EU2: Administration, permissions';
$lng['perms_col1']          = 'Élements conserné';
$lng['perms_col2']          = 'Niveau minimum d\'accès';
$lng['perms_apply']         = 'Enregistrer';

//------------------------------------------------------------------------------
$lng['mapcolor_title']      = 'EU2: Administration, Couleurs de carte';
$lng['mapcolor_header']     = 'Modification des couleurs de la carte';
$lng['mapcolor_btn']        = 'Enregistrer';
$lng['colorslegend']        = array();
$lng['colorsgroup']         = array();

// Couleurs utilisé sur la page 'Carte.php'
$lng['colorsgroup'][0]      = 'Couleurs itinéraire';
$lng['colorslegend'][0]     = array();
$lng['colorslegend'][0][0]  = 'Portée du radar';
$lng['colorslegend'][0][1]  = 'Astre quelconque';
$lng['colorslegend'][0][2]  = 'Mes colonies';
//$lng['colorslegend'][0][3]  = 'N/A';
//$lng['colorslegend'][0][4]  = 'N/A';
//$lng['colorslegend'][0][5]  = 'N/A';
//$lng['colorslegend'][0][6]  = 'N/A';
//$lng['colorslegend'][0][7]  = 'N/A';
//$lng['colorslegend'][0][11] = 'N/A';
//$lng['colorslegend'][0][8]  = 'N/A';
//$lng['colorslegend'][0][9]  = 'N/A';
//$lng['colorslegend'][0][10] = 'N/A';
$lng['colorslegend'][0][20] = 'Départ...';
$lng['colorslegend'][0][21] = 'Arrivée.';
$lng['colorslegend'][0][22] = 'Passage par vortex.';
$lng['colorslegend'][0][24] = 'Navigation \'Warp\' normale';
$lng['colorslegend'][0][25] = 'Navigation par vortex.';

$lng['colorsgroup'][1]      = 'Palette de couleurs 1';
$lng['colorslegend'][1]     = array();
$lng['colorslegend'][1][0]  = 'Portée du radar';
$lng['colorslegend'][1][1]  = 'Joueur de l\'empire';
$lng['colorslegend'][1][2]  = 'Mes colonies';
$lng['colorslegend'][1][3]  = 'Joueurs';
$lng['colorslegend'][1][4]  = 'Vortex';
$lng['colorslegend'][1][5]  = 'Astéroïdes';
$lng['colorslegend'][1][6]  = 'Planètes vide / Autre';
$lng['colorslegend'][1][7]  = 'Joueur de l\'empire + autres';
$lng['colorslegend'][1][11] = 'Alliés';
$lng['colorslegend'][1][8]  = 'Joueurs ennemi';
$lng['colorslegend'][1][9]  = 'Flottes PNJ';
$lng['colorslegend'][1][10] = 'Résultat(s) de recherche';
//$lng['colorslegend'][1][20] = 'N/A';
//$lng['colorslegend'][1][21] = 'N/A';
//$lng['colorslegend'][1][22] = 'N/A';
//$lng['colorslegend'][1][24] = 'N/A';
//$lng['colorslegend'][1][25] = 'N/A';

$lng['colorsgroup'][2]      = 'Palette de couleurs 2';
$lng['colorslegend'][2]     = array();
$lng['colorslegend'][2][0]  = 'Portée du radar';
$lng['colorslegend'][2][1]  = 'Joueur de l\'empire';
$lng['colorslegend'][2][2]  = 'Mes colonies';
$lng['colorslegend'][2][3]  = 'Joueurs';
$lng['colorslegend'][2][4]  = 'Vortex';
$lng['colorslegend'][2][5]  = 'Astéroïdes';
$lng['colorslegend'][2][6]  = 'Planètes vide / Autre';
$lng['colorslegend'][2][7]  = 'Joueur de l\'empire + autres';
$lng['colorslegend'][2][11] = 'Alliés';
$lng['colorslegend'][2][8]  = 'Joueurs ennemi';
$lng['colorslegend'][2][9]  = 'Flottes PNJ';
$lng['colorslegend'][2][10] = 'Résultat(s) de recherche';
//$lng['colorslegend'][2][20] = 'N/A';
//$lng['colorslegend'][2][21] = 'N/A';
//$lng['colorslegend'][2][22] = 'N/A';
//$lng['colorslegend'][2][24] = 'N/A';
//$lng['colorslegend'][2][25] = 'N/A';

//------------------------------------------------------------------------------

$lng['config_title']           = 'EU2: Administration, Configuration';
$lng['config_header']          = 'Configuration de eude:';
$lng['config_forumlink']       = 'Lien forum:';
$lng['config_canregister']     = 'Enregistrement de compte:';
$lng['config_canregister_off'] = 'Désactivé';
$lng['config_canregister_on']  = 'Autorisé';
$lng['config_defaultgrade']    = 'Grade par défaut:';
$lng['config_defaultgrade_tip']= bulle('Grade par défaut lors de la création d\'un nouveau compte.');
$lng['config_myempire']    = 'Mon Empire:';
$lng['config_myempire_tip']= bulle('Nom de votre empire (nom exact requis)');
$lng['config_Parcours_Max_Time']    = 'Temps max de calcul d\'un parcours (sec.):';
$lng['config_Parcours_Max_Time_tip']= bulle('Temps max avant avortement du calcul...');
$lng['config_Parcours_Nearest']    = 'Nb de pc pour le calcul "Au plus proche":';
$lng['config_Parcours_Nearest_tip']= bulle('Rayon en PC inclus dans le calcul');
$lng['config_greasemonkey']    = 'Serveur de jeu autorisé pour GreaseMonkey:';
$lng['config_greasemonkey_tip']= bulle('<b>Format</b>:<br/>[Préfixe].looki.[domaine]<br/><b>Exemples</b>:<br/>australis.fr<br/>polaris.fr<br/>eu2.com</br/>beta.de');
$lng['config_closed']    = 'Fermer le site ?';
$lng['config_closed_no']    = 'Ouvert';
$lng['config_closed_yes']    = 'Fermé (Hors Super-administrateur)';

$lng['config_apply']         = 'Enregistrer';

//------------------------------------------------------------------------------
$lng['logs_title']          = 'EU2: Administration, logs';
$lng['logs_date']           = 'Date';
$lng['logs_msg']            = 'Message';
$lng['logs_ip']             = 'IP';