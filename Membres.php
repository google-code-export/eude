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

DataEngine::CheckPermsOrDie('MEMBRES_HIERARCHIE');

if(DataEngine::checkPerms('MEMBRES_NEW')) {
    if(isset($_POST['Joueur'])) {
        DataEngine::NewUser(sqlesc($_POST['Joueur']), sqlesc(md5($_POST['Password'])), AXX_MEMBER,
            sqlesc($_POST['Points']), intval($_POST['Grade']));
    }

    //Modification niveaux de grade
    IF(isset($_POST['GradeId'])) {
        if($_POST['GradeId']==-1)
            DataEngine::sql("INSERT INTO SQL_PREFIX_Grade(Grade,Niveau,Rattachement) VALUES('".$_POST['GradeNom']."','".$_POST['GradeNiv']."','".$_POST['GradePere']."')");
        else
            DataEngine::sql("UPDATE SQL_PREFIX_Grade SET Niveau=".$_POST['GradeNiv'].", Rattachement=".$_POST['GradePere'].", Grade='".$_POST['GradeNom']."' WHERE GradeId='".$_POST['GradeId']."'");
    }

    if(isset($_POST['GradeSuppr'])) {
        if($_POST['GradeSuppr']=='1') {
            DataEngine::sql("UPDATE SQL_PREFIX_Grade SET Rattachement=0 WHERE Rattachement='".$_POST['GradeId']."'");
            DataEngine::sql("UPDATE SQL_PREFIX_Membres SET Grade=9 WHERE Grade='".$_POST['GradeId']."'");
            DataEngine::sql("DELETE FROM SQL_PREFIX_Grade Where GradeId='".$_POST['GradeId']."'");
        }
    }
} // Edit Perms

$Grades = array();
$mysql_result = DataEngine::sql('SELECT * from SQL_PREFIX_Grade ORDER BY Rattachement,Niveau');
while ($ligne=mysql_fetch_assoc($mysql_result))
    $Grades[] = $ligne;

require_once(TEMPLATE_PATH.'membre.tpl.php');
$tpl = tpl_membre::getinstance();
$tpl->page_title = 'EU2: Membres';

$levels = array();
foreach ($Grades as $id => $Grade) {
    if ($Grade['Rattachement'] == 0)
        $levels[0][$id] = $Grade['GradeId'];
}

$lid=0;
do {
    $level = $levels[$lid];
    foreach ($Grades as $id => $Grade) {
        if (array_search($Grade['Rattachement'], $level) !== false) {
            $levels[$lid+1][$id] = $Grade['GradeId'];
        }
    }
    $lid++;
} while ($lid<count($levels));

$players = array();
$mysql_result = DataEngine::sql('SELECT * from SQL_PREFIX_Membres ORDER BY Grade,Joueur DESC');
while ($ligne=mysql_fetch_assoc($mysql_result))
    $players[$ligne['Grade']][] = $ligne;

$tpl->header();

foreach ($levels as $levelkey => $level) {

    $tpl->level_header();

    foreach($level as $GradeKey => $GradeId)
        $tpl->level_grade($Grades[$GradeKey]['Grade']);

    $tpl->level_grade_sep();

    foreach($level as $GradeKey => $GradeId) {

        if (!isset ($players[$GradeId])) {
            $tpl->level_players_empty();
            continue;
        }

        $tpl->level_players_header();

        foreach ($players[$GradeId] as $player)
            $tpl->level_player_row($player);

        $tpl->level_players_footer();
    }

    $tpl->level_footer();
}

$tpl->level_vs_grade();

// Séparer les grades de la création de joueur ?
if(DataEngine::CheckPerms('MEMBRES_NEW')) {

    $tpl->Grade_Header()->Grade_AddPlayer($Grades)->Grade_Sep();

    $tpl->Grade_Modif_Header();
    foreach ($Grades as $v)
        $tpl->Grade_Modif($Grades, $v);

    $tpl->Grade_New($Grades)->Grade_modif_Footer()->Grade_Footer();

} // Perm

$tpl->DoOutput();
