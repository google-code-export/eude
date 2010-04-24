<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

$validsession=false; // false=nul, true=Ok, -1=Err?
$login_msg = ''; // Message en cas d'erreur.

/// ### Procédure de déconnexion ###
if (isset($do_logout) && $do_logout) {
    $_SESSION['_login'] = $_SESSION['_pass'] = $_SESSION['_Perm'] = $_SESSION['_IP'] = '';
    output::Boink(ROOT_URL);
}
$lng = language::getinstance()->GetLngBlock('login');

if (NO_SESSIONS) {
    $login = sqlesc(strtolower($_POST['user']), false);
    $mdp = $_POST['pass'];

    $query = "SELECT LOWER(Login) as Login,Permission, m.carte_prefs from SQL_PREFIX_Users, SQL_PREFIX_Membres m WHERE LOWER(Login)=LOWER('$login') AND Password='$mdp' AND (m.Joueur=LOWER('$login'))";

    $mysql_result = DataEngine::sql($query);
    $ligne=mysql_fetch_assoc($mysql_result);

    if(is_array($ligne) && $ligne['Login'] == $login) { // session valide
        $validsession=true;
        $_SESSION['_login'] = $login;
        $_SESSION['_pass']  = $mdp;
        $_SESSION['_Perm']  = $ligne['Permission'];
        $_SESSION['carte_prefs']  = $ligne['carte_prefs'];
        $_SESSION['_IP']  	= Get_IP();
        $_SESSION['_permkey']  = sha1($mdp.$_SESSION['_IP']);
//        $query = "INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),'$login','{$_SESSION['_IP']}')";
//        DataEngine::sql($query);
        return true;
    } else { // login/pass pas bon...
        $query = "INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),'err.eude:$login','".Get_IP()."')";
        DataEngine::sql($query);
        header('HTTP/1.1 403 Forbidden');
        $out = <<<o
<eude>
    <alert>{$lng['wronglogin']}</alert>
    <GM_active>0</GM_active>
    <logtype>raid</logtype>
    <log>{$lng['wronglogin_log']}</log>
</eude>
o;
        output::_DoOutput($out);
    }

}

// Procédure identification...
if($_POST && !empty($_POST['login']) && !empty($_POST['mdp'])) {
// Récup du login/pass...
    $login = sqlesc(strtolower($_POST['login']), false);
    $mdp = md5($_POST['mdp']);

    $query = "SELECT LOWER(Login) as Login,Permission, m.carte_prefs from SQL_PREFIX_Users, SQL_PREFIX_Membres m WHERE LOWER(Login)=LOWER('$login') AND Password='$mdp' AND (m.Joueur=LOWER('$login'))";

    $mysql_result = DataEngine::sql($query);
    $ligne=mysql_fetch_assoc($mysql_result);

    if(is_array($ligne) && $ligne['Login'] == $login) { // session valide
        $validsession=true;
        $_SESSION['_login'] = $login;
        $_SESSION['_pass']  = $mdp;
        $_SESSION['_Perm']  = $ligne['Permission'];
        $_SESSION['carte_prefs']  = $ligne['carte_prefs'];
        $_SESSION['_IP']  	= Get_IP();
        $_SESSION['_permkey']  = sha1($mdp.$_SESSION['_IP']);
        $query = "INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),'$login','{$_SESSION['_IP']}')";
        DataEngine::sql($query);
    } else { // login/pass pas bon...
        $validsession=-1;
        $login_msg = $lng['wronglogin'];
        $query = "INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),'Err:$login','".Get_IP()."')";
        DataEngine::sql($query);
    }
}

// Vérification de session, si existante et si elle viens pas d'être validé ;)
if( ($validsession===false) && isset($_SESSION['_login']) && $_SESSION['_login'] != "" ) {
    $login = $_SESSION['_login'];
    $mdp = $_SESSION['_pass'];

    $query = "SELECT LOWER(Login) as Login,Permission, m.carte_prefs from SQL_PREFIX_Users, SQL_PREFIX_Membres m WHERE LOWER(Login)=LOWER('$login') AND Password='$mdp' AND (m.Joueur=LOWER('$login'))";
    $mysql_result = DataEngine::sql($query);// or mysql_die($query,__file__,__line__);
    $ligne=mysql_fetch_array($mysql_result);
    if($ligne['Login'] == $login && $_SESSION['_IP'] == Get_IP()) {
        $validsession=true;
        $_SESSION['_Perm']  = $ligne['Permission']; // Maj les permission en cas de changement
        $_SESSION['carte_prefs']  = $ligne['carte_prefs'];
    } else {
        $validsession=-1;
        $query = "INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),'invalid:$login/{$_SESSION['_Perm']}/{$_SESSION['_IP']}','".Get_IP()."')";
        $_SESSION['_login'] = $_SESSION['_pass'] = $_SESSION['_Perm'] = $_SESSION['_IP'] = ''; // déconnexion...
        DataEngine::sql($query);
    }
}

// Message d'erreur par défaut...
if ( $login_msg =='' && $validsession == -1 ) $login_msg = $lng['session_lost'];

if ( $validsession !== true && USE_AJAX ) {
    header('Content-Type: text/xml;charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
    header('Expires: Mon, 16 Jul 2008 04:21:44 GMT'); // HTTP/1.0 Date dans le passé

    echo '<session>';
    echo "<error><![CDATA[$login_msg]]></error>";
    // execution javascript si parsing xml
    echo '<script><![CDATA[alert(\''.$lng['session_lost'].'\'); location.href=\'?\';]]></script>';
    echo '</session>';
    exit;
}

if ( $validsession !== true && IS_IMG ) {
    header("Content-Type: image/png");
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 16 Jul 2008 04:21:44 GMT"); // HTTP/1.0 Date dans le passé

    require_once(CLASS_PATH.'map.class.php');
    $Taille		= 180;
    $image 		= imagecreate($Taille,20);
    $background_color = imagecolorallocate ($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, $Taille, $Taille, $background_color);
    $debug_cl = imagecolorallocate ($image, 254, 254, 254);
    map::map_debug($lng['session_lost']);
    imagepng($image);
    imagedestroy($image);
    exit();
}
// $validsession

if ($validsession === true && $_SESSION['_Perm'] < AXX_VALIDATING) {
    $query = "INSERT INTO SQL_PREFIX_Log (DATE,LOGIN,IP) VALUES(NOW(),'AXX_VALIDATING:{$_SESSION['_login']}','{$_SESSION['_IP']}')";
    $_SESSION['_login'] = '';
    DataEngine::sql($query);
    output::_DoOutput('<a href="'.GetForumLink().'"><p style="color:red">'.$lng['no_axx'].'</p></a>');
}

if ($validsession !==true) {
    require_once(TEMPLATE_PATH.'login.tpl.php');

    $tpl = tpl_login::getinstance();
    $tpl->page_title = $lng['login_page_title'];
    $tpl->DoOutput($login_msg);

}








