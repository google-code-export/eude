<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

interface addon_config {
    /**
     * Active ou non l'extension
     * @return  boolean
     */
    public function Is_Enabled ();
    /**
     * Définit un menu pour l'addons
     * @return array array('page', 'image', taille(width), permissions/pré-requis, (array)sous-menu)
     */
    public function Get_Menu ();
    /**
     * Met en place un menu spécial 'addons'
     * @return boolean
     */
    public function InSubAddonMenu ();
    /**
     * Permission prérequise avant toute action.
     * @return boolean
     */
    public function CheckPerms ();

    /**
     * Routine de suppression d'utilisateur pour les addons
     * @param string Nom d'utilisateur
     * @return boolean
     */
    public function OnDeleteUser($user);
    /**
     * Routine de création pour les addons
     * @param string Nom d'utilisateur
     * @return boolean
     */
    public function OnNewUser($user);
    /**
     * La base de vortex vient de subir un nettoyage....
     * @return boolean
     */
    public function OnVortexCleaned();
    /**
     * Les boutons sont en cours de modification...
     * @param array &$listing
     * @return boolean
     */
//    public function OnButtonRegen(&$listing);
    /**
     * Routine de gestion d'accès utilisateur pour les addons
     * @param string Nom d'utilisateur
     * @return array (identifiant => valeur humainement lisible)
     */
    public function GetCustomPerms();
}
