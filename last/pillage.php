<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

require_once('./init.php');
require_once(INCLUDE_PATH.'Script.php');

DataEngine::CheckPermsOrDie('PERSO_TROOPS_BATTLE');


require_once(TEMPLATE_PATH.'troops.tpl.php');
$tpl = tpl_troops::getinstance();
$lng = language::getinstance()->GetLngBlock('pillage');

if ($_GET['player'] != '')
    $player = gpc_esc($_GET['player']);
else
    $player = $_SESSION['_login'];

$sql = sqlesc($player);
$sql = <<<sql
  SELECT * FROM SQL_PREFIX_troops_attack ta
  LEFT JOIN SQL_PREFIX_troops_pillage tp on (tp.mid=ta.id)
   WHERE players_attack LIKE '%"{$sql}"%' OR players_defender LIKE '%"{$sql}"%'
  ORDER BY `when` DESC LIMIT 0,30

sql;
$result = DataEngine::sql($sql);

$tpl->Setheader();
$tpl->AddToRow($player, 'player');
$tpl->PushRow();

$i = 1;
$id = -1;
$closepillagelog = false;
while ($row = mysql_fetch_assoc($result)) {
    if ($id != $row['ID']) {
        if ($closepillagelog) {
            $tpl->SetlogRow_footer();
            $closepillagelog = false;
        }
        $row['players_attack'] = unserialize($row['players_attack']);
        $row['players_defender'] = unserialize($row['players_defender']);
        $row['players_pertes'] = unserialize($row['players_pertes']);
        if (!is_array($row['players_pertes'])) $row['players_pertes'] = array();

        array_walk($row['players_attack'], 'formatarr');
        array_walk($row['players_defender'], 'formatarr');
        array_walk($row['players_pertes'], 'formatarr');

        $tpl->SetBattleRow();
        $tpl->AddToRow($i%2, 'rowid');
        $tpl->AddToRow($lng['listing_type'][$row['type']], 'type');
        $tpl->AddToRow(strftime($lng['listing_dateformat'], $row['when']), 'date');
        $tpl->AddToRow($row['coords_ss'].'-'.$row['coords_3p'], 'coords');
        $tpl->AddToRow(implode('<br/>', $row['players_attack']), 'attack');
        $tpl->AddToRow(implode('<br/>', $row['players_defender']), 'defend');
        $tpl->AddToRow(implode('<br/>', $row['players_pertes']), 'lost');
        $tpl->PushRow();
        $id = $row['ID'];
        $i++;
    }
    if ($id == $row['mid']) {
        if (!$closepillagelog) {
            $tpl->SetlogRow_header();
            $closepillagelog = true;
        }

        $tpl->SetlogRow();
        $tpl->AddToRow($i%2, 'class');
        $tpl->AddToRow(strftime($lng['listing_dateformat'], $row['date']), 'date');
        $tpl->AddToRow($row['Player'], 'player');
        for ($r=0;$r<10;$r++)
            $tpl->AddToRow(DataEngine::format_number($row['ress'.$r]), 'ress'.$r);
        $tpl->PushRow();
        $i++;
    }
}
if ($closepillagelog)
    $tpl->SetlogRow_footer();
$tpl->DoOutput();

function formatarr(&$value, $key) {
    global $lng;
    $value = sprintf($lng['listing_playerrow'],$key,DataEngine::format_number($value, true));
    return true;
}