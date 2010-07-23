<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * */
define('CHECK_LOGIN', false);
require_once('./init.php');
require_once(INCLUDE_PATH . 'Script.php');

if (DataEngine::config_key('config', 'closed'))
    output::Boink ();
if (!DataEngine::config_key('config', 'CanRegister'))
    DataEngine::NoPermsAndDie();

$lng = language::getinstance()->GetLngBlock('login');

$erreur = '';
if (isset($_POST['login']) && $_POST['login'] != '' && $_POST['mdp'] != '') {
    $login = gpc_esc($_POST['login']);
    $qlogin = sqlesc($_POST['login']);
    $pass = md5($_POST['mdp']);

    $query = 'SELECT LOWER(`Login`) as `Login` from `SQL_PREFIX_Users` WHERE LOWER(`Login`)=LOWER(\'' . $qlogin . '\')';
    $mysql_result = DataEngine::sql($query);
    $ligne = mysql_fetch_array($mysql_result);

    if ($ligne['Login'] == $login) { // joueur existe déjà...
        $erreur = $lng['user_exists'];
    } else {
        if (DE_DEMO)
            $axx = AXX_MEMBER;
        else
            $axx = AXX_VALIDATING;
        DataEngine::NewUser($login, $pass, $axx, 0, DataEngine::config_key('config', 'DefaultGrade'));
        // TODO: Redir, no sign in.
        $_SESSION['_login'] = $login;
        $_SESSION['_pass'] = $pass;
        $_SESSION['_Perm'] = $axx;
        $_SESSION['_IP'] = Get_IP();
        $query = 'INSERT INTO `SQL_PREFIX_Log` (`DATE`,`LOGIN`,`IP`) VALUES(NOW(),\'' . $qlogin . '\',\'' . $_SESSION['_IP'] . '\')';
        DataEngine::sql($query);
        output::boink('./');
    }
}
require_once(TEMPLATE_PATH . 'login.tpl.php');
$tpl = tpl_login::getinstance();
$tpl->page_title = $lng['signin_page_title'];
$tpl->DoOutput($erreur, true);
