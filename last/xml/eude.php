<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * @since 1.4.2
 *
 * */
define('NO_SESSIONS', true); // mode login/pass a chaque requêtes...
define('USE_AJAX', true); // mode xml
//define('CHECK_LOGIN',false);

require_once('../init.php');
require_once(INCLUDE_PATH . 'Script.php');
require_once(CLASS_PATH . 'parser.class.php');
require_once(CLASS_PATH . 'cartographie.class.php');

$lng = language::getinstance()->GetLngBlock('eude');

if (!DataEngine::CheckPerms('CARTOGRAPHIE_GREASE')) {
    header('HTTP/1.1 403 Forbidden');
    output::_DoOutput('<eude><alert>' . $lng['err_403'] . '</alert><GM_active>0</GM_active></eude>');
}
$serveur = DataEngine::config_key('config', 'eude_srv');
if ($serveur != '' && $serveur != $_POST['svr']) {
    header('HTTP/1.1 403 Forbidden');
    output::_DoOutput("<eude><alert>{$lng['err_wrongserver']}</alert><GM_active>0</GM_active></eude>");
}

$xml = array();
$carto = cartographie::getinstance();


switch ($_GET['act']) {
    case 'init': //-------------------------------------------------------------
        $xml['GM_galaxy_info'] = DataEngine::CheckPerms('CARTOGRAPHIE_PLAYERS') ? '1' : '0';
        $xml['GM_planet_info'] = DataEngine::CheckPerms('CARTOGRAPHIE_PLANETS') ? '1' : '0';
        $xml['GM_asteroid_info'] = DataEngine::CheckPerms('CARTOGRAPHIE_ASTEROID') ? '1' : '0';
        $xml['GM_pnj_info'] = DataEngine::CheckPerms('CARTOGRAPHIE_PNJ') ? '1' : '0';

        $xml['GM_troops_battle'] = DataEngine::CheckPerms('PERSO_TROOPS_BATTLE') ? '1' : '0';
        $xml['GM_empire_maj'] = DataEngine::CheckPerms('EMPIRE_GREASE') ? '1' : '0';

        DataEngine::sql_spool('INSERT INTO `SQL_PREFIX_Log` (`DATE`,`LOGIN`,`IP`) VALUES(NOW(),\'gm:' . sqlesc($_SESSION['_login']) . '\',\'' . $_SESSION['_IP'] . '\')');
        DataEngine::sql_spool('UPDATE `SQL_PREFIX_Membres` SET `Date`=now() WHERE `Joueur`=\'' . sqlesc($_SESSION['_login']) . '\'');
    case 'config': //-----------------------------------------------------------
        $msg = $xml['log'] = $lng['config_helloworld'];
        $xml['logtype'] = 'none';
        $xml['GM_active'] = '1';
        break;

    case 'mafiche': //----------------------------------------------------------
        $query = <<<q
            UPDATE `SQL_PREFIX_Membres` SET `POINTS`='%d',
        `Economie`='%d', `Commerce`='%d', `Recherche`='%d', `Combat`='%d',
        `Construction`='%s', `Navigation`='%d', `Race`='%s',
        `Titre`='%s', `GameGrade`='%s', `pts_architecte`='%d', `pts_mineur`='%d',
        `pts_science`='%d', `pts_commercant`='%d', `pts_amiral`='%d',
        `pts_guerrier`='%d', `Date`=now() WHERE `Joueur`='%s'
q;
        DataEngine::sql(sprintf($query, DataEngine::strip_number($_POST['POINTS']),
                                DataEngine::strip_number($_POST['Economie']), DataEngine::strip_number($_POST['Commerce']),
                                DataEngine::strip_number($_POST['Recherche']), DataEngine::strip_number($_POST['Combat']),
                                DataEngine::strip_number($_POST['Construction']), DataEngine::strip_number($_POST['Navigation']),
                                sqlesc(trim($_POST['Race']), false), sqlesc($_POST['Titre'], false),
                                sqlesc($_POST['GameGrade'], false), DataEngine::strip_number($_POST['pts_architecte']),
                                DataEngine::strip_number($_POST['pts_mineur']), DataEngine::strip_number($_POST['pts_science']),
                                DataEngine::strip_number($_POST['pts_commercant']), DataEngine::strip_number($_POST['pts_amiral']),
                                DataEngine::strip_number($_POST['pts_guerrier']), $_SESSION['_login']
                        )
        );
        $xml['log'] = $lng['mafiche_maj'];
        break;

    case 'ownuniverse': //------------------------------------------------------

        if (!DataEngine::CheckPerms('PERSO_OWNUNIVERSE')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        require_once(CLASS_PATH . 'ownuniverse.class.php');
        $data = unserialize(gpc_esc($_POST['data']));
        list($info, $warn) = ownuniverse::getinstance()->add_ownuniverse($data);
        $xml['log'] = $info;
        break;

    case 'troop_battle': //-----------------------------------------------------

        if (!DataEngine::CheckPerms('PERSO_TROOPS_BATTLE')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        require_once(CLASS_PATH . 'map.class.php');
        require_once(CLASS_PATH . 'ownuniverse.class.php');
        require_once(CLASS_PATH . 'troops.class.php');
        $date = gpc_esc($_POST['date']);
        preg_match('/(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2})/', $date, $adate);
        $idate = mktime($adate[4], $adate[5], 0, $adate[2], $adate[1], $adate[3]);
        $coords = gpc_esc($_POST['coords']);
        $left = gpc_esc($_POST['left']);
        $right = gpc_esc($_POST['right']);
        $nb_assault = intval($_POST['nb_assault']);
        $pertes = gpc_esc($_POST['pertes']);
        $xml['log'] = troops::getinstance()->AddBattle($idate, $coords, $left, $right, $nb_assault, $pertes);
        break;

    case 'troop_log': //--------------------------------------------------------

        if (!DataEngine::CheckPerms('PERSO_TROOPS_BATTLE')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        require_once(CLASS_PATH . 'map.class.php');
        require_once(CLASS_PATH . 'ownuniverse.class.php');
        require_once(CLASS_PATH . 'troops.class.php');
        $mode = gpc_esc($_POST['mode']);
        $date = gpc_esc($_POST['date']);
        preg_match('/(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2})/', $date, $adate);
        $idate = mktime($adate[4], $adate[5], 0, $adate[2], $adate[1], $adate[3]);
        $smsg = gpc_esc($_POST['msg']);
        $xml['log'] = troops::getinstance()->AddPillage_log($mode, $idate, $smsg);
        break;

    case 'troop_howmany': //--------------------------------------------------------

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLAYERS')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        $lastcoords = gpc_esc($_POST['lastcoords']);
        $carto->Edit_Entry($lastcoords,
                array('TROOP' => DataEngine::strip_number($_POST['EnnemyTroops'])),
                $lng['players_troopnb']);
        $xml['log'] = sprintf($lng['players_troopnb2'], $lastcoords);
        break;

    case 'wormhole': //---------------------------------------------------------
        $carto->add_vortex($_POST['IN'], $_POST['OUT']);
        $xml['log'] = sprintf($lng['wormhole'], $_POST['IN'], $_POST['OUT']);
        break;

    case 'galaxy_info': //------------------------------------------------------

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLAYERS')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        $galaxy_info = unserialize(gpc_esc($_POST['data']));
        $cur_ss = $_POST['ss'];

        $nbplanets = 0;
        $SS_A = $del_planet = $curss_info = array();
        for ($i = 0, $max = count($galaxy_info); $i < $max; $i++) {
            $nbplanets++;
            // $galaxy_info[$i][1] = coords xxxx-xx-xx-xx
            // $galaxy_info[$i][2] = nom planète
            // $galaxy_info[$i][3] = Nom joueur (et empire) brut
            if (trim($galaxy_info[$i][3]) != '') {
                preg_match_all('#<b>(.+)</b><br>(.*)#', $galaxy_info[$i][3], $player, PREG_SET_ORDER);
                $joueur = $player[0][1];
                $empire = html_entity_decode(utf8_decode(trim($player[0][2])), ENT_QUOTES, 'utf-8');
            } else {
                $joueur = '';
                $empire = '';
            }

            if (trim($galaxy_info[$i][3]) == '' && $carto->FormatId($galaxy_info[$i][1], $dummy, $sys, '')) // Planète inoccupée
                $del_planet[] = $sys;
            else
                $SS_A[] = array($galaxy_info[$i][1], $galaxy_info[$i][2], $joueur, $empire);
        }

        // repiquage cartographie->add_solar_ss
        if (count($del_planet) > 0) {
            $del_planet = '' . implode("','", $del_planet) . '';
            $query = <<<sql
UPDATE `SQL_PREFIX_Coordonnee` c, `SQL_PREFIX_Coordonnee_Joueurs` j, `SQL_PREFIX_Coordonnee_Planetes` p
SET `Type`=2, `USER`='', `EMPIRE`='', `INFOS`='', `batiments`=NULL, `troop`=NULL
WHERE `Type` in (0,3,5) AND `POSIN`=$cur_ss AND `COORDET` in ('$del_planet')
sql;

            $array = DataEngine::sql($query);
            if (($num = mysql_affected_rows()) > 0)
                $carto->AddInfo(sprintf($lng['class_solar_msg1'], $num, $cur_ss));
        }

        foreach ($SS_A as $v)
            $carto->add_player($v);
        // fin du repiquage cartographie->add_solar_ss

        $xml['log'] = sprintf($lng['solar_msg3'], $_POST['ss'], $max);
        break;


    case 'planet': // ----------------------------------------------------------

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLANETS')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        foreach (DataEngine::a_Ressources() as $id => $dummy)
            $Ress[$id] = gpc_esc($_POST[$id]);

        $ok = $carto->add_planet(gpc_esc($_POST['COORIN']), $Ress) ? $lng['planet_msg1'] : $lng['planet_msg2'];
        $xml['log'] = sprintf($ok, $_POST['COORIN']);
        break;
    case 'asteroid': // --------------------------------------------------------

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_ASTEROID')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        foreach (DataEngine::a_Ressources() as $id => $dummy)
            $Ress[$id] = gpc_esc($_POST[$id]);

        $ok = $carto->add_asteroid(gpc_esc($_POST['COORIN']), $Ress) ? $lng['asteroid_msg1'] : $lng['asteroid_msg2'];
        $xml['log'] = sprintf($ok, $_POST['COORIN']);
        break;

    case 'pnj': // -------------------------------------------------------------

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PNJ')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        $_POST['fleetname'] = gpc_esc($_POST['fleetname']);
        $ok = $carto->add_PNJ($_POST['coords'], gpc_esc($_POST['owner']), $_POST['fleetname']) ? $lng['asteroid_msg1'] : $lng['asteroid_msg2'];
        $xml['log'] = sprintf($ok, $_POST['fleetname']);
        break;

    case 'player': // --------------------------------------------------------

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLAYERS')) {
            $carto->AddErreur($lng['err_noaxx']);
            break;
        }
        $water = (($_POST['WATER'] != '') && (is_numeric($_POST['WATER']))) ?
                DataEngine::strip_number($_POST['WATER']) : '';
        $batiments = (($_POST['BUILDINGS'] != "") && (is_numeric($_POST['BUILDINGS']))) ?
                DataEngine::strip_number($_POST['BUILDINGS']) : '';
        if (!$carto->FormatId(trim($_POST['COORIN']), $uni, $sys, '')) {
            $xml['log'] = sprintf($lng['player_err_coords'], $_POST['COORIN']);
            $carto->AddWarn($xml['log']);
        } else {
            $carto->Edit_Entry($_POST['COORIN'],
                    array('water' => $water,
                        'batiments' => $batiments),
                    $lng['player_edit_msg']);
            $xml['log'] = sprintf($lng['player_edit_log'], $sys);
        }
        break;

    case 'empire': // --------------------------------------------------------
        if (!DataEngine::CheckPerms('EMPIRE_GREASE')) {
            $carto->AddErreur('Permissions manquante');
            break;
        }
        $empire_name = gpc_esc(html_entity_decode($_POST['empire']));
        $membres = unserialize(gpc_esc($_POST['data']));
        $listemembres = '"' . implode('","', $membres) . '"';
        $query = 'UPDATE `SQL_PREFIX_Coordonnee_Joueurs` SET
			`EMPIRE` = \'' . sqlesc($empire_name) . '\'
			WHERE `USER` in (' . $listemembres . ')';

        $ok = DataEngine::sql($query) ? ' a été mis à jour' : ' n\'a pas été mis à jour';
        $query2 = 'UPDATE `SQL_PREFIX_Coordonnee_Joueurs` SET
			`EMPIRE` = "" 
			WHERE `USER` not in (' . $listemembres . ')
			AND `EMPIRE` LIKE "' . sqlesc($empire_name) . '"';

        if ($ok) {
            $ok = DataEngine::sql($query2) ? ' a été mis à jour' : ' n\'a pas été mis à jour';
        }
        $carto->AddInfo('L\'empire ' . $empire_name . $ok);
        $xml['log'] = 'L\'empire ' . $empire_name . $ok;

        break;

    default:
        $xml['log'] = $lng['err_unknown'];
        $xml['logtype'] = 'raid';
}

$msg .= $carto->Infos();
$warn = $carto->Warns();
$err = $carto->Erreurs();

$msg = ($msg != '' && $warn != '') ? $msg . '<br/>' . $warn : ($warn != '') ? $warn : $msg;
$msg = ($msg != '' && $err != '') ? $msg . '<br/>' . $err : ($err != '') ? $err : $msg;

if ($msg)
    $xml['content'] = '<![CDATA[' . DataEngine::xml_fix51($msg) . ']]>';
if ($xml['log'])
    $xml['log'] = '<![CDATA[' . DataEngine::xml_fix51($xml['log']) . ']]>';
$out = "<eude>\n";
foreach ($xml as $key => $v)
    if ($v != '')
        $out .= '<' . $key . '>' . $v . '</' . $key . ">\n";
$out .= '</eude>';

output::_DoOutput($out);