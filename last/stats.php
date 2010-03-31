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

DataEngine::CheckPermsOrDie('MEMBRES_STATS');

require_once(TEMPLATE_PATH.'stats.tpl.php');

$tpl = tpl_stats::getinstance();
//$tpl->css_file=false;
$tpl->page_title = "EU2: Statistiques";

$invert_sort = array(''=>'ASC','DESC' => 'ASC', 'ASC' => 'DESC');
if (isset($_GET['act']) && $_GET['act'] == 'pts') {

    $tpl->SetheaderPoints();
    $sort_key = array('Points', 'pts_architecte', 'pts_mineur', 'pts_commercant',
        'pts_science', 'pts_amiral', 'pts_guerrier');    
    $sort='Points DESC';
    foreach($sort_key as $v) {
        $newvalue = array('sort' => array($v=>$invert_sort[$_GET['sort'][$v]]));
        $tpl->AddToRow(Get_string($newvalue), $v);
        if ($_GET['sort'][$v]) $sort= $v.' '.$_GET['sort'][$v];
    }

    $tpl->PushRow();
    $sql='SELECT * FROM SQL_PREFIX_Membres ORDER BY '.$sort;
    $mysql_result = DataEngine::sql($sql);

    $cols = array('Points', 'pts_architecte', 'pts_mineur', 'pts_commercant',
        'pts_science', 'pts_amiral', 'pts_guerrier');
    while($line=mysql_fetch_assoc($mysql_result)) {
        $tpl->AddToRow($line['Joueur'], -2);

        foreach ($cols as $key) {
            $tpl->AddToRow($line[$key], $key);
        }
        $tpl->PushRow();
    }

} else {

    $sql='SELECT Joueur FROM SQL_PREFIX_Membres ORDER BY Joueur ASC';
    $mysql_result = DataEngine::sql($sql);

    $tpl->SetRowtpl();
    $tpl->Setheader();
    while($line=mysql_fetch_assoc($mysql_result)) {
        $tpl->AddToRow($line['Joueur'], -2);
        $sql='SELECT Type,count(*) AS Nb FROM SQL_PREFIX_Coordonnee WHERE Utilisateur=\''.$line['Joueur'].'\' AND inactif=0 GROUP BY Type';
        $mysql_result2 = DataEngine::sql($sql);
        $ut=0;
        while($line2=mysql_fetch_assoc($mysql_result2)) {
            $tpl->AddToRow($line2['Nb'], $line2['Type']);
            $ut += $line2['Nb'];
        }
        $tpl->AddToRow($ut, -1);
        $tpl->PushRow();
    }
    $tpl->footer();
}

$tpl->DoOutput();