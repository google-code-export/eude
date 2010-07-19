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
require_once(CLASS_PATH.'parser.class.php');

DataEngine::CheckPermsOrDie('PERSO');

$lng = language::getinstance()->GetLngBlock('personal');

if(isset($_POST['importation'])) {
//    $data = file_get_contents('./test/data/mafiche_ff.txt');
    $data = gpc_esc($_POST['importation']);
    $parser = parser::getinstance();

    // simple détection...
    if (mb_stripos($parser->GetValueByLabel($data, $lng['ident']), $_SESSION['_login'],0, 'utf8') !== false) {
        $matrix = explode("\n",trim($data));
        $info = array();
        $info['GameGrade'] = trim($matrix[0]);
        $info['Race'] = $parser->GetValueByLabel($data, $lng['Race'] );
        $info['Titre'] = $parser->GetValueByLabel($data, $lng['Titre'] );
        $info['Commerce'] = $parser->GetValueByLabel($data, $lng['Commerce'] );
        $info['Recherche'] = $parser->GetValueByLabel($data, $lng['Recherche'] );
        $info['Combat'] = $parser->GetValueByLabel($data, $lng['Combat'] );
        $info['Construction'] = $parser->GetValueByLabel($data, $lng['Construction'] );
        $info['Economie'] = $parser->GetValueByLabel($data, $lng['Economie'] );
        $info['Navigation'] = $parser->GetValueByLabel($data, $lng['Navigation'] );
        $info['POINTS'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['POINTS'] ));
        $info['pts_architecte'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['pts_architecte'] ));
        $info['pts_mineur'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['pts_mineur'] ));
        $info['pts_science'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['pts_science'] ));
        $info['pts_commercant'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['pts_commercant'] ));
        $info['pts_amiral'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['pts_amiral'] ));
        $info['pts_guerrier'] = DataEngine::strip_number($parser->GetValueByLabel($data, $lng['pts_guerrier'] ));


        foreach ($info as $k => $v) $info[$k] = sqlesc($v);

        $query = <<<q
            UPDATE `SQL_PREFIX_Membres` SET `POINTS`=%d,
        `Economie`=%d, `Commerce`=%d, `Recherche`=%d, `Combat`=%d,
        `Construction`='%s', `Navigation`=%d, `Race`='%s',
        `Titre`='%s', `GameGrade`='%s', `pts_architecte`=%d, `pts_mineur`=%d,
        `pts_science`=%d, `pts_commercant`=%d, `pts_amiral`=%d,
        `pts_guerrier`=%d WHERE `Joueur`='%s'
q;
        DataEngine::sql(sprintf($query, $info['POINTS'],
            $info['Economie'], $info['Commerce'], $info['Recherche'], $info['Combat'],
            $info['Construction'], $info['Navigation'], $info['Race'],
            $info['Titre'], $info['GameGrade'], $info['pts_architecte'], $info['pts_mineur'],
            $info['pts_science'], $info['pts_commercant'], $info['pts_amiral'],
            $info['pts_guerrier'], $_SESSION['_login']
            )
        );
    }

}
if(isset($_POST['JOUEUR'])) {

    foreach ($_POST as $k => $v) $_POST[$k] = gpc_esc($v);

    $_POST['Commerce'] = intval($_POST['Commerce']);
    $_POST['Recherche'] = intval($_POST['Recherche']);
    $_POST['Combat'] = intval($_POST['Combat']);
    $_POST['Construction'] = intval($_POST['Construction']);
    $_POST['Economie'] = intval($_POST['Economie']);
    $_POST['Navigation'] = intval($_POST['Navigation']);

    $_POST['Points'] = DataEngine::strip_number($_POST['Points']);
    $_POST['pts_architecte'] = DataEngine::strip_number($_POST['pts_architecte']);
    $_POST['pts_mineur'] = DataEngine::strip_number($_POST['pts_mineur']);
    $_POST['pts_science'] = DataEngine::strip_number($_POST['pts_science']);
    $_POST['pts_commercant'] = DataEngine::strip_number($_POST['pts_commercant']);
    $_POST['pts_amiral'] = DataEngine::strip_number($_POST['pts_amiral']);
    $_POST['pts_guerrier'] = DataEngine::strip_number($_POST['pts_guerrier']);

    foreach ($_POST as $k => $v) $_POST[$k] = sqlesc($v);

    $query = <<<q
        UPDATE `SQL_PREFIX_Membres` SET `POINTS`=%d, `ship`='%s',
        `Economie`=%d, `Commerce`=%d, `Recherche`=%d, `Combat`=%d,
        `Construction`='%s', `Navigation`=%d,
        `pts_architecte`=%d, `pts_mineur`=%d,
        `pts_science`=%d, `pts_commercant`=%d, `pts_amiral`=%d,
        `pts_guerrier`=%d WHERE `Joueur`='%s'
q;
    DataEngine::sql(sprintf($query, $_POST['Points'], $_POST['ship'],
        $_POST['Economie'], $_POST['Commerce'], $_POST['Recherche'], $_POST['Combat'],
        $_POST['Construction'], $_POST['Navigation'],
        $_POST['pts_architecte'], $_POST['pts_mineur'],
        $_POST['pts_science'], $_POST['pts_commercant'], $_POST['pts_amiral'],
        $_POST['pts_guerrier'], $_SESSION['_login']
        )
    );

}

if (isset($_POST['pwd']) && !($_SESSION['_login'] == 'test' && DE_DEMO)) {
    $query = 'UPDATE `SQL_PREFIX_Users` SET `Password`=md5(\''.sqlesc($_POST['pwd'],false).'\') WHERE `Login`=\''.$_SESSION['_login'].'\'';
    DataEngine::sql($query);
}

$mysql_result = DataEngine::sql('Select m.`Joueur`, m.`Points`, m.`Economie`, m.`Commerce`, m.`Recherche`, m.`Combat`, m.`Construction`, m.`Navigation`, m.`Race`, m.`ship`, m.`Titre`, m.`GameGrade`, m.`pts_architecte`, m.`pts_mineur`, m.`pts_science`, m.`pts_commercant`, m.`pts_amiral`, m.`pts_guerrier`, g.`Grade` from `SQL_PREFIX_Membres` m, `SQL_PREFIX_Grade` g WHERE m.`Joueur`=\''.$_SESSION['_login'].'\' AND (m.`Grade`=g.`GradeId`)');
$ligne = mysql_fetch_assoc($mysql_result);

require_once(TEMPLATE_PATH.'mafiche.tpl.php');

$tpl = tpl_mafiche::getinstance();
$tpl->page_title = $lng['page_title'];

$tpl->header($ligne);
$tpl->add_row($lng['POINTS'], 'Points',$ligne['Points']);
$tpl->add_row($lng['pts_architecte'], 'pts_architecte',$ligne['pts_architecte']);
$tpl->add_row($lng['pts_mineur'], 'pts_mineur',$ligne['pts_mineur']);
$tpl->add_row($lng['pts_science'], 'pts_science',$ligne['pts_science']);
$tpl->add_row($lng['pts_commercant'], 'pts_commercant',$ligne['pts_commercant']);
$tpl->add_row($lng['pts_amiral'], 'pts_amiral',$ligne['pts_amiral']);
$tpl->add_row($lng['pts_guerrier'], 'pts_guerrier',$ligne['pts_guerrier']);


$tpl->add_row($lng['Commerce'], 'Commerce',$ligne['Commerce']);
$tpl->add_row($lng['Recherche'], 'Recherche',$ligne['Recherche']);
$tpl->add_row($lng['Combat'], 'Combat',$ligne['Combat']);
$tpl->add_row($lng['Construction'], 'Construction',$ligne['Construction']);
$tpl->add_row($lng['Economie'], 'Economie',$ligne['Economie']);
$tpl->add_row($lng['Navigation'], 'Navigation',$ligne['Navigation']);

$tpl->add_row($lng['Race'],'',$ligne['Race']);
//$tpl->add_row_select($lng['Race'], '', $tabrace, $ligne['Race']);
$tpl->add_row_select($lng['lastship'], 'ship', DataEngine::a_shiplist(), $ligne['ship']);

$tpl->add_row($lng['Titre'],'',$ligne['Titre']);
$tpl->add_row($lng['grade_ingame'],'',$ligne['GameGrade']);
$tpl->add_row($lng['grade_eude'],'',$ligne['Grade']);

$tpl->footer();
$tpl->doOutput();

