<?php
/**
 * Empire Universe 2 - Data Engine
 *
 * Basé sur le travail de Christophe Couprie alias Elessar81
 * Corrigé, amélioré sur autorisation express par alex10336
 *
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

final class Config implements iDataEngine_Config {
    static private $connexion;
    /**
     * @var localhost Adresse du serveur
     */
    static protected $BaseMysql = 'localhost';
    /**
     * @var user Nom d'utilisateur pour le serveur mysql
     */
    static protected $BaseUser  = 'user';
    /**
     * @var pass Mot de passe pour le serveur mysql
     */
    static protected $BasePass  = 'pass';
    /**
     * @var database Nom de la base de donnée utilisé par le serveur mysql
     *   (en général le même que $BaseUser)
     */
    static protected $BaseName  = 'database';
    /**
     * fonction d'initialisation spéciale...
     */
    static function init() {
        /**
         * En cas de configuration serveur particulière.
         * Et que la détection automatique aurait échoué.
         * Symptome en cas d'échec:
         * - Page de login blanche
         * - La session ne reste pas (rien ne marche, donc)
         * @const ROOT_URL
         * @link http://app216.free.fr/eu2/tracker/view.php?id=160
         * @example
         * [code]
         * define('ROOT_URL', '/');
         * define('ROOT_URL', '/DataEngine/');
         * [/code]
         * enlever les '//' au début dun'e ligne pour activer le paramètre
         */

        //define('ROOT_URL', '/');


        /**
         * préfixe de table a changer pour permettre plusieurs DataEngine sur la même base...
         * par défaut vide
         * @staticvar SQL_PREFIX_ ''
         */
        define('SQL_PREFIX_','SQL_PREFIX_');

        /**
         * Quel pack de langue utiliser ?
         */
        define('LNG_CODE','fr');
    }
    /**
     * @return string Lien vers le forum
     */
    static function GetForumLink() {
        return '';
    }
    /**
     * Grade par défaut
     * 'id' affiché sur la page "Membre.php" a droite
     * @return integer grade id
     */
    static function GetDefaultGrade() {
        return 3;
    }
    /**
     * Active la possibilité au membre de l'empire de créer un compte eux même.
     * @return boolean
     */
    static function CanRegister() {
        return false;
    }
    /**
     * Définir ici le nom <b>exact</b> de votre empire
     * @return string(100) Nom de l'empire
     */
    static function GetMyEmpire() {
        return '';
    }
    /**
     * Temps max de recherche avant abandon pour le meilleurs recensé
     * @since 1.4.1
     * @return integer default: 0 (ie. Temps max autorisé par le serveur ~20/30sec.)
     */
    static public function Parcours_Max_Time() {
        return 0;
    }
    /**
     * Calcul "Au plus proche", définit le rayon max de recherche autour du point de départ/arrivé
     * Ne pas mettre trop haut. Ça n'aurait plus d'intérêts, et alourdirait plus qu'autre chose
     * @since 1.4.1
     * @return integer default: 5 , Max conseillé: 8, min conseillé: 3
     */
    static public function Parcours_Nearest() {
        return 5;
    }
    /**
     *
     * @example
     * [code]
     * return '';             // pour tout serveur sans restriction
     * return 'australis.fr'; // pour australis
     * return 'borealis.fr';  // pour borealis
     * return 'beta.de';      // pour test sur .de
     * return 'eu2.com';      // pour eu2 anglais
     * [/code]
     */
    static public function eude_srv() {
        return '';
    }
    /**
     * Connexion a la base de donnée.
     * @return mysqli_driver
     */
    static function DB_Connect() {
        if (self::$connexion) return self::$connexion;

        self::$connexion = mysql_connect(self::$BaseMysql,self::$BaseUser,self::$BasePass)
                or die(mysql_error());
        mysql_select_db(self::$BaseName)  or die(mysql_error());
        return self::$connexion;
    }
}