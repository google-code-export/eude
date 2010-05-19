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

require_once(TEMPLATE_PATH.'sample.tpl.php');

$tpl = tpl_sample::getinstance();

ob_start();

?><br/><br/><br/>
<table>
    <tr class="base_row1">
        <td>ID</td>
        <td>type</td>
        <td>nb_assault</td>
        <td>players_attack</td>
        <td>players_defender</td>
        <td>players_pertes</td>
        <td>when</td>
        <td>coords</td>
        <td>pid</td>
        <td>mid</td>
        <td>date</td>
        <td>ress0</td>
        <td>ress1</td>
        <td>ress2</td>
        <td>ress3</td>
        <td>ress4</td>
        <td>ress5</td>
        <td>ress6</td>
        <td>ress7</td>
        <td>ress8</td>
        <td>ress9</td>
    </tr>
<?php
$login = sqlesc($_SESSION['_login']);
$sql = <<<sql
  SELECT * FROM SQL_PREFIX_troops_attack ta
  LEFT JOIN SQL_PREFIX_troops_pillage tp on (tp.mid=ta.id)
   WHERE players_attack LIKE '%"$login"%' OR players_defender LIKE '%"$login"%'
  ORDER BY ID DESC

sql;
$result = DataEngine::sql($sql);

while ($row = mysql_fetch_assoc($result)) { ?>

    <tr class="base_row1">
        <td><?php echo $row['ID'] ?></td>
        <td><?php echo $row['type'] ?></td>
        <td><?php echo $row['nb_assault'] ?></td>
        <td><?php echo $row['players_attack'] ?></td>
        <td><?php echo $row['players_defender'] ?></td>
        <td><?php echo $row['players_pertes'] ?></td>
        <td><?php echo date('d.m.Y H:i:s', $row['when']); ?></td>
        <td><?php echo $row['coords_ss'].'-'.$row['coords_3p'] ?></td>
        <td><?php echo $row['pid'] ?></td>
        <td><?php echo $row['mid'] ?></td>
        <td><?php echo date('d.m.Y H:i:s', $row['date']) ?></td>
        <td><?php echo $row['ress0'] ?></td>
        <td><?php echo $row['ress1'] ?></td>
        <td><?php echo $row['ress2'] ?></td>
        <td><?php echo $row['ress3'] ?></td>
        <td><?php echo $row['ress4'] ?></td>
        <td><?php echo $row['ress5'] ?></td>
        <td><?php echo $row['ress6'] ?></td>
        <td><?php echo $row['ress7'] ?></td>
        <td><?php echo $row['ress8'] ?></td>
        <td><?php echo $row['ress9'] ?></td>
    </tr>
    <?php
}
?></table><?php

$tpl->PushOutput(ob_get_clean());
$tpl->DoOutput();