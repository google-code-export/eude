<?php
/*
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 */
include_once('../../init.php');
require_once(CLASS_PATH.'output.class.php');
require_once(CLASS_PATH.'dataengine.class.php');

if (isset($_SESSION['ROOT_URL']))
    define('ROOT_URL', $_SESSION['ROOT_URL']);

DataEngine::minimalinit();

if (file_exists(INCLUDE_PATH.'Entete.php'))
    DataEngine::ErrorAndDie('installation existante');

$_SESSION['install'] = 1;
$_SESSION['ROOT_URL'] = ROOT_URL;
?>
<html>
    <head>
        <title>EU2 Data Engine: Première Installation</title>
        <meta http-equiv="robots" content="no-index,no-follow"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <style type="text/css">
            table#install {
                border-width: 0px;
            }
            tr#header {
                background-color: #AAAAAA;
                text-align: center;
            }
            tr#row0 {
                background-color: #CCCCCC;
            }
            tr#row1 {
                background-color: #D6D6D6;
            }
            td#title {
                background-color: #C8C8E8;
            }
            .required {
                color: red;
                font-weight: bold;
            }
            .smalltext {
                font-size: smaller;
            }
        </style>
    </head>        
    <body>
        <form autocomplete="off" name="install" action="?" method="POST">
            <table id="install" cellspacing="0" cellpadding="5px">
                <tr id="header">
                    <td colspan="2">Installation du <b>D</b>ata <b>E</b>ngine</td>
                </tr>
                <tr id="header">
                    <td colspan="2"><b>Serveur mysql</b></td>
                </tr>
                <tr id="row0">
                    <td id="title"><span class="required">*</span> <b>Serveur</b></td>
                    <td><input type="text" name="server" value="localhost" /></td>
                </tr>
                <tr id="row1">
                    <td id="title"><span class="required">*</span> <b>Nom d'utilisateur</b></td>
                    <td><input type="text" name="sqluser" value="" /></td>
                </tr>
                <tr id="row0">
                    <td id="title"><span class="required">*</span> <b>Mot de passe</b></td>
                    <td><input type="text" name="sqlpass" value="" /></td>
                </tr>
                <tr id="row1">
                    <td id="title"><span class="required">*</span> <b>Base de donnée</b></td>
                    <td><input type="text" name="sqlbase" value="" /></td>
                </tr>
                <tr id="row0">
                    <td id="title"><b>Préfixe de table</b></td>
                    <td><input type="text" name="sqlprefix" value="de_" /></td>
                </tr>
                <tr id="header">
                    <td colspan="2"><b>Autre</b></td>
                </tr>
                <tr id="row0">
                    <td id="title"><span class="required">*</span> <b>Adresse d'installation</b></td>
                    <td><input type="text" name="sqlrooturl" value="<?php echo ROOT_URL; ?>" /></td>
                </tr>
                <tr id="row0" class="smalltext">
                    <td colspan="2">Cette adresse doit être valide. (modifiez si la valeur mise par défaut n'est pas bonne)
                        Celle ci doit commencer et finir par <b>/</b></td>
                </tr>
                <tr id="row1">
                    <td id="title"><b>Votre empire</b></td>
                    <td><input type="text" maxlength="100" name="empire" value="" /></td>
                </tr>
            </table>

        </form>
        <pre>
root_url
préfixe
empire

        </pre>
    </body>
</html>