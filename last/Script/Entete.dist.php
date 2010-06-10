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
        define('SQL_PREFIX_','');

        /**
         * Quel pack de langue utiliser ?
         */
        define('LNG_CODE','fr');
        setlocale(LC_ALL, "fr_FR");
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