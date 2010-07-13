<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
include ('../init.php');
if (file_exists('../Script/Entete.php'))
    trigger_error('Installation existante', E_USER_ERROR);
if (!file_exists('./install.conf.php'))
    trigger_error('Fichier de configuration principal manquant (install.conf.php)', E_USER_ERROR);

$file = 'install';

$sqlfile = ROOT_PATH . 'install' . DIRECTORY_SEPARATOR . $file . '.sql';
$lockfile = ROOT_PATH . 'install' . DIRECTORY_SEPARATOR . $file . '.lock';

if (file_exists($lockfile))
    trigger_error('Fichier de vérouillage trouvé, installation partielle/en cours ? (' . $lockfile . ')', E_USER_WARNING);

$max = count(preg_split('/;[\n\r]+/', file_get_contents($sqlfile)));

if (file_exists($lockfile))
    $cur = (int) file_get_contents($lockfile);
else
    $cur = 0;
$max = $max - $cur;

//-- repiquage script/script.php
function bulle ($texte,$addover='',$addout='') {
    if(is_array($addover))
        $addover=implode($addover,'');
    if(is_array($addout)) $addout=implode($addout,'');
    $texte=htmlspecialchars(str_replace("\n", '', $texte),ENT_QUOTES,'UTF-8');
    return ("onmouseover='montre(\"".$texte."\");$addover' onmouseout='cache();$addout'");
}
//-- fin repiquage

$bulle_sqlrooturl = bulle('<u>Exemple:</u><br/>Site: http://app216.free.fr<b>/eu2/test/</b><br/>Emplacement sur le serveur: <b>/eu2/test/</b><br/>Commence et finit par <b>/</b>');
?><html xmlns="http://www.w3.org/1999/html" lang="fr" xml:lang="fr">
    <head>
        <title>EU2: DataEngine, Installation</title>
        <link rel="stylesheet" type="text/css" href="./template.css?install" media="screen" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="../tpl/lng/fr/btn/eude.png">
        <script type="text/javascript" src="../Script/prototype.js?1.6.1"></script>
        <script type="text/javascript" src="../Script/Script.js?install"></script>
        <script type="text/javascript" src="../tpl/lng/fr/eude.local.js?install"></script>
        <script type="text/javascript" src="./sqlbatch.js?install"></script>
    </head>
    <body>
    <style type="text/css">
        .required {
            color: red;
            font-weight: bold;
        }
    </style>
    <div id="curseur" class="infobulle"></div>
    <form autocomplete="off" name="install" action="?" method="POST" Onsubmit="return false;">
        <input type="hidden" id="sqlmax" value="<?php echo $max; ?>" />
        <table id="install" class="table_nospacing table_center color_bg size500">
            <tr class="color_bigheader text_center">
                <td rowspan="18" width="2px">&nbsp;</td>
                <td colspan="2">Installation du <b>D</b>ata <b>E</b>ngine</td>
                <td rowspan="18" width="2px">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" height="2px"></td>
            </tr>
            <tr class="color_header">
                <td colspan="2"><b>Serveur mysql</b></td>
            </tr>
            <tr>
                <td><span class="required">*</span> <b>Serveur</b></td>
                <td><input class="color_row0" type="text" id="sqlserver" value="localhost" /></td>
            </tr>
            <tr>
                <td><span class="required">*</span> <b>Base de donnée</b></td>
                <td><input class="color_row0" type="text" id="sqlbase" value="" /></td>
            </tr>
            <tr>
                <td><span class="required">*</span> <b>Nom d'utilisateur</b></td>
                <td><input class="color_row0" type="text" id="sqluser" value="" /></td>
            </tr>
            <tr>
                <td><span class="required">*</span> <b>Mot de passe</b></td>
                <td><input class="color_row0" type="text" id="sqlpass" value="" /></td>
            </tr>
            <tr>
                <td><b>Préfixe de table</b></td>
                <td><input class="color_row0 size80" type="text" id="sqlprefix" value="de_" /></td>
            </tr>
            <tr class="color_header">
                <td colspan="2"><b>Compte</b></td>
            </tr>
            <tr>
                <td><b>Nom d'utilisateur administrateur</b></td>
                <td><input class="color_row0" type="text" maxlength="30" id="username" value="admin" /></td>
            </tr>
            <tr>
                <td><b>Mot de passe</b></td>
                <td><input class="color_row0" type="text" id="password" value="admin" /></td>
            </tr>
            <tr class="color_header">
                <td colspan="2"><b>Informations complémentaire</b></td>
            </tr>
            <tr <?php echo $bulle_sqlrooturl; ?>>
                <td><b>Emplacement sur le serveur</b></td>
                <td><input class="color_row0" type="text" id="sqlrooturl" value="<?php echo dirname(dirname($_ENV['SCRIPT_URL'])) . '/'; ?>" /></td>
            </tr>
            <tr>
                <td><b>Votre empire</b></td>
                <td><input class="color_row0" type="text" maxlength="100" id="empire" value="" /></td>
            </tr>
            <tr>
                <td><b>Votre forum</b></td>
                <td><input class="color_row0" type="text" maxlength="256" id="board" value="https://eude.googlecode.com/" /></td>
            </tr>
            <tr class="color_header text_center">
                <td colspan="2"><a href="javascript:test_mysql();">Tester la connexion mysql</a></td>
            </tr>
            <tr class="color_header text_center">
                <td colspan="2"><div id="sqlbatchmsg"></div>
                    <div id="sqlbatchmsgstep"></div></td>
            </tr>
        </table>
    </form>
</body></html>