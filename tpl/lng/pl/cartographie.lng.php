<?php
/**
 * @author Alex10336
 * @translator
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

$lng = array();

// Planets ressources text
$lng['ress10%'] = 'très peu';
$lng['ress20%'] = 'peu';
$lng['ress40%'] = 'normal';
$lng['ress50%'] = 'moyennement';
$lng['ress70%'] = 'beaucoup';
$lng['ress80%'] = 'considérablement';
$lng['ress90%'] = 'énormément';

$lng['add_items_header'] = 'Ajout des corps célestes';
$lng['add_items_btn_auto'] = 'Automatique';
$lng['add_items_btn_manual'] = 'Manuel';
$lng['add_items_btn_add'] = 'Ajouter';

$lng['add_items_bulle'] = 'Coller ici les détails d\'une planète, joueur ou d\'un vortex<br/>(Ctrl+A puis Ctrl+C après avoir ouvert une fiche)';
$lng['add_items_bulle1'] = 'Position de départ';
$lng['add_items_bulle2'] = 'Position de de sortie (vortex)';
$lng['add_items_bulle3'] = 'Nom du Joueur';
$lng['add_items_bulle4'] = 'Nom de l\'empire';
$lng['add_items_bulle5'] = 'Nom de la planète<br/>ou<br/>Nom de la flotte';

$lng['add_items_col_type'] = 'Type';
$lng['add_items_col_corin'] = 'Coordonnée Entrée';
$lng['add_items_col_corout'] = 'Coordonnée Sortie';
$lng['add_items_col_corrds'] = 'Coordonnée(s)';
$lng['add_items_col_player'] = 'Nom du joueur';
$lng['add_items_col_empire'] = 'Empire';
$lng['add_items_col_infos'] = 'Planète/Flotte';
$lng['add_items_planet_header'] = 'Informations détaillées planète/Astéroïde';

$lng['search_header'] = 'Recherche des corps célestes';
$lng['search_col_date'] = 'Date';
$lng['search_col_status'] = 'Status';
$lng['search_col_status_on'] = 'Actif';
$lng['search_col_status_off'] = 'Inactif';
$lng['search_col_type'] = 'Type';
$lng['search_col_ss'] = 'SS';
$lng['search_col_rayon'] = 'Rayon';
$lng['search_col_player'] = 'Joueur';
$lng['search_col_empire'] = 'Empire';
$lng['search_col_fleet'] = 'Planète/Flotte';
$lng['search_col_note'] = 'Note';
$lng['search_col_water'] = '% d\'eau';
$lng['search_col_buildings'] = 'Bâtiments';
$lng['search_col_maxtroops'] = 'Troupes Max';
$lng['search_col_troops'] = 'Troupes';
$lng['search_col_self'] = 'Moi';
$lng['search_col_showall'] = 'Afficher tout';
$lng['search_col_btnsearch'] = 'Rechercher';
$lng['search_col_btndoedit'] = 'Valider Les modifications';

$lng['search_bulle_cmd_delete']  = 'Supprimer la ligne ?<br/><br/><b>Attention</b>: Aucune confirmation demandé !';
$lng['search_bulle_cmd_edit']    = 'Modifier cette ligne ?';

$lng['search_date_short_format'] = 'H:i d-m';
$lng['search_date_long_format']  = 'd-m-Y à H:i:s';
$lng['search_userdate']          = 'Par <b>%s</b><br/>Le: %s';
$lng['search_troopdate']         = 'Le: %s';

$lng['err_coorin_needed']= 'Les coordonnés d\'entrée doivent-être renseigné';
$lng['err_coorout_filled']= 'Les coordonnés de sortie ne sont à renseigner que pour les Vortex';
$lng['err_coorout_needed']= 'Il faut impérativement renseigner Les coordonnés de sortie pour les Vortex';
$lng['err_player_needed']= 'Merci de renseigner le nom du joueur';
$lng['err_unknown_type']= 'Type demandé non pris en charge !';

//------------------------------------------------------------------------------
//-- cartographie.class.php ----------------------------------------------------
//------------------------------------------------------------------------------

$lng['class_err_noaxx']         = 'Permissions manquante';
$lng['class_err_ress']          = 'Format de la valeur de la ressource %s incorrecte (%s), autorisé : peu,normal,beaucoup,[...],xx,xx%';
$lng['class_err_coords']        = 'Erreur, le format de coordonnée (%s) doit-être au format xxxx-xx-xx-xx ou xxxx:xx:xx:xx';

$lng['class_vortex_msg1']       = 'Le vortex %s <> %s a été réactivée';
$lng['class_vortex_msg2']       = 'Le vortex %s <> %s existe déjà';
$lng['class_vortex_msg3']       = 'Le vortex %s <> %s ajouté...';
$lng['class_planet_msg1']       = 'La planète est déjà à jour au coordonnée : %s-%s';
$lng['class_planet_msg2']       = 'La planète mis à jour au coordonnée : %s-%s';
$lng['class_planet_msg3']       = 'La planète ajouté au coordonnée : %s-%s';
$lng['class_asteroid_msg1']     = 'L\'astéroîde mis à jour au coordonnée : %s-%s';
$lng['class_asteroid_msg2']     = 'L\'astéroîde ajouté au coordonnée : %s-%s';
$lng['class_player_type0']      = 'Le joueur';
$lng['class_player_type3']      = 'L\'allié';
$lng['class_player_type5']      = 'L\'ennemi';
$lng['class_player_msg1']       = 'Planète %s désertée';
if (NO_SESSIONS && USE_AJAX) { // Alias GreaseMonkey
    $lng['class_player_msg2']   = 'MAJ %4$s: %1$s %2$s'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg3']   = 'Ignoré %4$s: %1$s %2$s'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg4']   = 'Ajout %4$s: %1$s %2$s'; // $stype,$nom,$uni,$sys
} else {
    $lng['class_player_msg2']   = '%s %s mis à jour au coordonnée : %s-%s'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg3']   = '%s %s existe déjà au coordonnée : %s-%s (ignoré)'; // $stype,$nom,$uni,$sys
    $lng['class_player_msg4']   = '%s %s ajouté au coordonnée : %s-%s'; // $stype,$nom,$uni,$sys
}

if (NO_SESSIONS && USE_AJAX) { // Alias GreaseMonkey
    $lng['class_npc_msg1']      = 'MAJ %3$s: La flotte %1$s'; // $nom,$uni,$sys
    $lng['class_npc_msg2']      = 'Ignoré %3$s: La flotte %1$s'; // $nom,$uni,$sys
    $lng['class_npc_msg3']      = 'Ajout %3$s: La flotte %1$s'; // $nom,$uni,$sys
} else {
    $lng['class_npc_msg1']      = 'La flotte %s mis à jour au coordonnée : %s-%s'; // $nom,$uni,$sys
    $lng['class_npc_msg2']      = 'La flotte %s existe déjà au coordonnée : %s-%s (Ignoré)'; // $nom,$uni,$sys
    $lng['class_npc_msg3']      = 'La flotte %s ajouté au coordonnée : %s-%s'; // $nom,$uni,$sys
}


$lng['class_solar_msg1']        = '%d planète(s) devenue inoccupée dans le système %s';
$lng['class_solar_msg2']        = 'Changement d\'empire du joueur: \'%s\'';

$lng['class_edit_defmsg']       = 'Mise à jour du "%1$s" en %3$s'; // type,player,coords,[...]
$lng['class_delete_nofound']    = 'Élément non trouvé (%s)'; // ident
$lng['class_delete_msg']        = '%s (%s) supprimé'; // type,ident

