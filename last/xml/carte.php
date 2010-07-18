<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
define('USE_AJAX',true);

require_once('../init.php');
require_once(INCLUDE_PATH.'Script.php');
require_once(CLASS_PATH.'map.class.php');

if (!DataEngine::CheckPerms('CARTE_SEARCH')) {
    $msg = language::getinstance()->GetLngBlock('dateengine');
    $msg = $msg['nopermsanddie'];
    $out=<<<o
<carte>
    <script>
    Carte.DetailsShow(false);
    alert('{$msg}');
    </script>
</carte>
o;
    output::_DoOutput($out);
}

// Partie recherche
$_SESSION['emp']	= ( ($_POST['s']!='')	&& $_POST['type'] == 'emp') ? stripslashes($_POST['s']): '';
$_SESSION['jou']	= ( ($_POST['s']!='')	&& $_POST['type'] == 'jou') ? stripslashes($_POST['s']): '';
$_SESSION['inf']	= ( ($_POST['s']!='')	&& $_POST['type'] == 'inf') ? stripslashes($_POST['s']): '';

$search = '';
if ($_SESSION['emp'] != "") $search = "j.`EMPIRE`='".sqlesc($_SESSION['emp'])."' ";
if ($_SESSION['jou'] != "") $search = "j.`USER`='".sqlesc($_SESSION['jou'])."' ";
if ($_SESSION['inf'] != "") $search = "j.`INFOS`='".sqlesc($_SESSION['inf'])."' ";

if ( isset($_POST['ss']) && $_POST['ss'] != "") {
    if ($search != '') $search = "($search OR ";
    $search .= "POSIN in (".sqlesc($_POST['ss']).")) ";
}

//if ($search != '') $search .= "AND ";

// Init donnée avec sql spécifique...
$map=map::getinstance();
$mysql_result=$map->init_map($search);

$CurrSS = 0; $CurrSS_a = array();
$tabdata = $currentsearch = '';
while ($line=mysql_fetch_assoc($mysql_result)) {
    if ($CurrSS == 0) $CurrSS = $line['POSIN'];

    if ($line['POSIN'] != $CurrSS) {
        $map->add_ss(false,'xml_add');
        $CurrSS   = $line['POSIN'];
    }

    $ID   = $line['ID'];
    $ss   = $line['POSIN'];

    $CurrSS_a[$ID] = $line;
    $CurrSS_a[$ID]['type'] = $map->ss_type($line);
    $CurrSS_a[$CurrSS_a[$ID]['type']]++;
}
mysql_free_result($mysql_result);
$map->add_ss(false,'xml_add');


DataEngine::sql_do_spool();

if (is_array($currentsearch)) $currentsearch = implode(',',$currentsearch);
$out=<<<o
<carte>
    <currentsearch><![CDATA[$currentsearch]]></currentsearch>
    <tabdata><![CDATA[$tabdata]]></tabdata>
</carte>
o;
output::_DoOutput($out);

function xml_add($ss, $data) {
    global $output,$tabdata, $currentsearch, $map;

    if ($data['search']>0) $currentsearch[] = $ss;//"// $ss n'est pas dans la recherche \r\n";
    $tabdata .= "Carte.updatedata($ss,\"".DataEngine::xml_fix51($map->ss_info($ss,$data))."\");\r\n";
}