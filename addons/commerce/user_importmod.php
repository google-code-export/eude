<?php
// Partie standard d'EU2de
  require_once('../../init.php');
  require_once(INCLUDE_PATH.'Script.php');
  require_once(TEMPLATE_PATH.'sample.tpl.php');
  $tpl = tpl_sample::getinstance();

// Déclaration variables
  $Joueur = $_SESSION['_login'];
  require_once('cnh_fonctions.php');
  Init_Addon();
  
// DEBUT CODE LIBRE
if (!DataEngine::CheckPerms('ZZZ_COMMERCE_IMPORT'))
    output::Boink('./index.php');

// FIN CODE LIBRE  
?>

<HTML>
<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<br /><br /><?php cnhTB(0) ?><hr><br />
<!-- DEBUT CODE LIBRE -->
<br />
<center><font color='#dddddd'>
<?php
  $bcontinue = true;
  $chainecar = 'abcdefghijklmnopqrstuvwxyz';

  if($_POST["ReplaceAll"]=='on') {
    echo('Effacement de toutes les anciennes données du joueur '.$Joueur.'...<br />');
    $mysql_result = DataEngine::sql('DELETE FROM SQL_PREFIX_Modules_Users WHERE Login=\''.$Joueur.'\'') or die(mysql_error());
  }

  if(isset($_POST['submit'])) {
    if($_POST['submit']=='Interpréter' && !empty($_POST['RawData'])) {
      $t = str_replace("\n", ' ', $_POST['RawData']);
      $t = str_replace("\t", ' ', $_POST['RawData']);

      $importmod = 0;
      $tmp = strpos($t, "Options");
      if($tmp !== false) {
        $tmp = strpos($t, "Module", $tmp + 1);
        if($tmp !== false) {
          if(strpos($t, "Coûts", $tmp + 1) !== false)
            $importmod = 1;
        }
      }

      $tmp = strpos($t, "Module");
	  $tmpname = strpos($t, "Module");
      if($tmp !== false && $importmod == 0) {
        $tmp = strpos($t, "Grosseur", $tmp + 1);
        if($tmp !== false) {
          if(strpos($t, "Avantages", $tmp + 1) !== false)
            $importmod = 2;
		  elseif(strpos($t, "Camouflage", $tmpname + 1) !== false)
		    $importmod = 2;
        }
      }
      
/* #####################################################################################
   #####################################################################################

   IMPORTATION LISTE CONSTRUCTION JOUEUR (v2)

   #####################################################################################
   ##################################################################################### */
      if($importmod == 1)
      {
        echo("Modules fabriqués par le Joueur<br />\n");
        echo("Importation en cours...<br />");
        $bcontinue = false;
        $totalmod = 0;
        $addedmod = 0;
        $tDesc = '';
        
        $firefox = (strpos($t, "[") !== false);
        
        $t = mb_substr($t, strpos($t, "Montant") + 1);
        $t = trim(mb_substr($t, strpos($t, "Options") + 8));
//        $t = str_replace(chr(13).chr(10), chr(32), $t);
        
        $gf = 0;
        while(strlen($t) > 5 && $gf < 100) {
          if($firefox) {
            $t = mb_substr(strstr($t, "["), 1);
            $i = strpos($t, "]");
            $tDesc = trim(mb_substr($t, 0, $i - 1));
            $t = trim(mb_substr($t, $i + 1));
          }
          
          $i = 1000;
          if(strpos($t, '*') !== false)
             $i = strpos($t, '*');
          if(strpos($t, '+') !== false) {
            if(strpos($t, '+') < $i)
              $i = strpos($t, '+');
          }
            
          $tName = trim(mb_substr($t, 0, $i - 1));
    
          $mysql_result = DataEngine::sql('
          SELECT ID, Description, SQL_PREFIX_Modules_Own.Modifier
          FROM SQL_PREFIX_Modules_Template LEFT JOIN (SELECT Login, Module_ID, Modifier FROM SQL_PREFIX_Modules_Users WHERE Login='.$Joueur.') AS SQL_PREFIX_Modules_Own ON SQL_PREFIX_Modules_Template.ID = SQL_PREFIX_Modules_Own.Module_ID  
          WHERE Nom = \''.$tName.'\' OR Abreviation = \''.$tName.'\'') or die(mysql_error());
          
          if($datas = mysql_fetch_array($mysql_result)) {
            $totalmod++;
            echo('<br />Importation: <b>'.$tName.'</b>');
            
            if(empty($datas['ID'])) {
              echo(" -> Concordance non-trouvée (vérifiez le nom et l'abbréviation dans le Template du module).");
            }
            else
            {
              if(empty($datas['Description']) && !empty($tDesc)){
                DataEngine::sql('UPDATE SQL_PREFIX_Modules_Template SET Description=\''.$tDesc.'\' WHERE ID=\''.$datas['ID'].'\'') or die(mysql_error());
              }
              
              if(is_null($datas['Modifier'])){          
                DataEngine::sql('INSERT INTO SQL_PREFIX_Modules_Users(Login, Module_ID, Modifier) VALUES (\''.$Joueur.'\', \''.$datas['ID'].'\', 0)') or die(mysql_error());
                $addedmod++;
              }
              else
                echo(" (déjà existant).");
            }
          }
          else
            echo("<br />Aucune concordance avec <b>".$tName."</b>, vérifiez le nom et l'abbréviation dans le Template du module.");
          
          $i = strpos($t, ":");
          $t = trim(mb_substr($t, $i + 1));
          $i = strpos($t, ":");
          if($i <= 3)
            $t = trim(mb_substr($t, $i + 1));
          if(strlen($t) <= 2)
            $t = '';
          else
            $t = trim(mb_substr($t, 2));
            
          $gf++;        
        }
        
        echo('<br /><br />'.$totalmod.' module(s) trouvé(s), '.$addedmod.' ajouté(s).');

        echo("<br /><br /><hr width='50%' /><p align=center>[&nbsp;<a href='user_importmod.php'>Autre importation...</a>&nbsp;]</p>");
      }
/* #####################################################################################
   #####################################################################################

   IMPORTATION TEMPLATE MODULE

   #####################################################################################
   ##################################################################################### */
      elseif($importmod == 2 && !DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_INSERT'))
        echo("<br>Seul les Spécialistes peuvent importer des template de modules...<br>");
      elseif($importmod == 2 && DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_INSERT'))
      {
        echo("Template de Module<br />\n");
        echo("Importation en cours...<br />");
        $bcontinue = false;
        $alertjava = '';
        
        $i = strpos($t, "Nom");
        $j = strpos($t, "\n", $i);
        $datas["Nom"] = trim(mb_substr($t, $i + 3, $j - $i - 2));
        $t = mb_substr($t, $j + 1);
        
        $i = strpos($t, "Grosseur");
        $j = strpos($t, "\n", $i);
        $datas["PAGVG"] = intval(trim(mb_substr($t, $i + 8, $j - $i - 2)));
        $datas["PAChasseur"] = intval($datas['PAGVG'] / 2 + 0.5);
        $t = mb_substr($t, $j + 1);

        $i = strpos($t, "construction");
        $j = strpos($t, "\n", $i);
        $datas["Temps"] = trim(mb_substr($t, $i + 12, $j - $i - 12));
        $t = mb_substr($t, $j + 1);
		if(strlen($datas['Temps']) == 5) {
		$datas['Temps'] = '00:'.$datas['Temps'];
		}
		
        // Avantages
		if(strpos($t, "Avantages") !== false) {
        	$i = strpos($t, "\n", strpos($t, "Avantages") + 1);
            $j = strpos($t, "\n", $i + 1);
            $tmp = trim(mb_substr($t, $i, $j - $i));
            $t = mb_substr($t, $j + 1);
            $j = 0;
            while($tmp != 'Ressources' && $j < 10)
            {
              $i = strpos($tmp, " ");
              $value = trim(mb_substr($tmp, 0, $i));           
              $field = trim(mb_substr($tmp, $i + 1));
              
              switch($field) {
                case 'Consommation': // Propulsion 
                  $datas['Categorie'] = 0; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
                  $field = 'PropConsommation';
                  $value = trim(mb_substr($value, 1));
                  break; 
	            case 'Impulsions': // Propulsion
	              $datas['Categorie'] = 0; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $field = 'PropImpulsion';
	              $value = DelPoint(trim(mb_substr($value, 0, strpos($value, ","))));
	              break; 
	            case 'Warp': // Propulsion 
	              $datas['Categorie'] = 0; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $field = 'PropWarp';
	              $value = DelPoint(trim(mb_substr($value, 0, strpos($value, ","))));
	              break;
	            case 'Attaque (Laser)': // Armement "Laser"
	              $datas['Categorie'] = 1; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $alertjava = "Attention, il sera nécessaire de rêgler la précision de l'arme manuellement!";
	              $datas['ArmType'] = 0;
	              $field = 'ArmDegat';
	              $value = trim(mb_substr($value, 1));
	              break; 
	            case 'Attaque (projectile)': // Armement "Projectile"
	              $datas['Categorie'] = 1; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $alertjava = "Attention, il sera nécessaire de rêgler la précision de l'arme manuellement!";
	              $datas['ArmType'] = 1;
	              $field = 'ArmDegat';
	              $value = trim(mb_substr($value, 1));
	              break; 
	            case 'Attaque (Ions)': // Armement "Ion"
	              $datas['Categorie'] = 1; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $alertjava = "Attention, il sera nécessaire de rêgler la précision de l'arme manuellement!";
	              $datas['ArmType'] = 2;
	              $field = 'ArmDegat';
	              $value = trim(mb_substr($value, 1));
	              break; 
	            case 'Blindage': // Blindage
	              $datas['Categorie'] = 2; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $datas['ProtType'] = 0;
	              $field = 'ProtGVG';
	              $value = trim(mb_substr($value, 1));
	              $datas['ProtChasseur'] = intval($value / 4 + 0.5);
	              break; 
	            case 'Bouclier': // Bouclier
	              $datas['Categorie'] = 2; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $datas['ProtType'] = 1;
	              $field = 'ProtGVG';
	              $value = DelPoint(trim(mb_substr($value, 1)));
	              $datas['ProtChasseur'] = intval($value / 5 + 0.5);
	              break;
	            case 'Carburant': // Carburant
	              $datas['Categorie'] = 3; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $datas['PAChasseur'] = $datas['PAGVG'];
	              $datas['EquipType'] = 5;
	              $field = 'EquipNiv';
	              $value = DelPoint(trim(mb_substr($value, 1)));
	              break; 
	            case 'Radar': // Scan
	              $datas['Categorie'] = 3; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $datas['PAChasseur'] = $datas['PAGVG'];
	              $datas['EquipType'] = 3;
	              $field = 'EquipNiv';
	              $value = DelPoint(trim(mb_substr($value, 1)));
	              break; 
	            case 'Chargement': // Minage 2, Cargo 0, Récupérateur 4
	              $datas['Categorie'] = 3; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $alertjava = "Attention, l'importation ne peut faire la différence entre un Cargo, un Mineur ou un ramasseur de débris!\nVous devrez vous-même effectuer les réglages!";
	              $datas['PAChasseur'] = $datas['PAGVG'];
	              $datas['EquipType'] = 0;
	              $field = 'EquipNiv';
	              $value = DelPoint(trim(mb_substr($value, 1)));
	              break; 
	            case 'Inenterie': // Troupes
	            case 'Infenterie':
	            case 'Infanterie':
	              $datas['Categorie'] = 3; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $datas['PAChasseur'] = $datas['PAGVG'];
	              $datas['EquipType'] = 1;
	              $field = 'EquipNiv';
	              $value = DelPoint(trim(mb_substr($value, 1)));
	              break; 
	            case 'Coloniser':
	              $datas['Categorie'] = 3; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
	              $datas['PAChasseur'] = $datas['PAGVG'];
	              $datas['EquipType'] = 6;
	              $field = 'EquipNiv';
	              $value = trim(mb_substr($value, 1));
	              break; 
	          }

	          if($field != '') $datas[$field] = $value;
	        
	          $i = strpos($t, "\n");
	          $tmp = trim(mb_substr($t, 0, $i));
	          $t = mb_substr($t, $i + 1);
	          $j++;
	        }
		}
		// MAJ looki camouflage
		if (preg_match('/camouflage\s+(\d+)/i', $datas["Nom"], $matches)) {
			$datas['Categorie'] = 3; //0 Propulsion, 1 Armement, 2 Protection, 3 Equipement, 4 Chassis
			$datas['PAChasseur'] = $datas['PAGVG'];
			$datas['EquipType'] = 7; // Camouflage
			$datas['EquipNiv']= $matches[1];
		}

        // Ressources
        $i = strpos($t, "\n");
        $tmp = trim(mb_substr($t, 0, $i));
        $t = mb_substr($t, $i + 1);
        $j = 0;
        while($tmp != 'Description' && $j < 10)
        {
          $i = strpos($tmp, " ");
          $field = trim(mb_substr($tmp, 0, $i));           
          $value = intval(DelPoint(trim(mb_substr($tmp, $i + 1))));

          if($field == 'Hydrogène') $field = "Hydrogene";

          if($field != '') $datas[$field] = $value;
        
          $i = strpos($t, "\n");
          $tmp = trim(mb_substr($t, 0, $i));
          $t = mb_substr($t, $i + 1);
          $j++;
        }

        // Description
        $datas['Description'] = addslashes (trim($t));

        // Divers
        $datas['Taille'] = $datas['PAGVG'] * 1000;
        


	$query = 'SELECT ID FROM SQL_PREFIX_Modules_Template WHERE Nom LIKE \''.$datas['Nom'].'\'';
        $mysql_result = DataEngine::sql($query);
        if (mysql_num_rows($mysql_result) > 0) {
            $ligne = mysql_fetch_assoc($mysql_result);
            if($ligne['ID'] > 0) {
			DataEngine::sql('UPDATE SQL_PREFIX_Modules_Template SET Nom=\''.$datas['Nom'].'\', Description=\''.$datas['Description'].'\', Categorie=\''.$datas['Categorie'].'\', Taille=\''.$datas['Taille'].'\', PAChasseur=\''.$datas['PAChasseur'].'\', PAGVG=\''.$datas['PAGVG'].'\', Temps=\''.$datas['Temps'].'\', Titane=\''.$datas['Titane'].'\', Cuivre=\''.$datas['Cuivre'].'\', Fer=\''.$datas['Fer'].'\', Aluminium=\''.$datas['Aluminium'].'\', Mercure=\''.$datas['Mercure'].'\', Silicium=\''.$datas['Silicium'].'\', Uranium=\''.$datas['Uranium'].'\', Krypton=\''.$datas['Krypton'].'\', Azote=\''.$datas['Azote'].'\', Hydrogene=\''.$datas['Hydrogene'].'\', PropImpulsion=\''.$datas['PropImpulsion'].'\', PropWarp=\''.$datas['PropWarp'].'\', PropConsommation=\''.$datas['PropConsommation'].'\', ArmType=\''.$datas['ArmType'].'\', ArmDegat=\''.$datas['ArmDegat'].'\', ProtType=\''.$datas['ProtType'].'\', ProtChasseur=\''.$datas['ProtChasseur'].'\', ProtGVG=\''.$datas['ProtGVG'].'\', EquipType=\''.$datas['EquipType'].'\', EquipNiv=\''.$datas['EquipNiv'].'\' WHERE ID=\''.$ligne['ID'].'\'');
			echo("<br /><center><b>Templates de module mis à jour.</b></center><br /><br />");
			echo("<br /><br /><hr width='50%' /><p align=center>&nbsp;<a href='user_importmod.php'>Autre importation...</a>&nbsp;</p>");
			}
			}
			else {
			
            $query    = 'INSERT INTO SQL_PREFIX_Modules_Template (`Nom`, `Description`, `Categorie`, `Taille`, `PAChasseur`, `PAGVG`, `Temps`, `Titane`, `Cuivre`, `Fer`, `Aluminium`, `Mercure`, `Silicium`, `Uranium`, `Krypton`, `Azote`, `Hydrogene`, `PropImpulsion`, `PropWarp`, `PropConsommation`, `ArmType`, `ArmDegat`, `ArmManiabilite`, `ProtType`, `ProtChasseur`, `ProtGVG`, `EquipType`, `EquipNiv`) ';
            $query   .= 'VALUES (\''.$datas['Nom'].'\', \''.$datas['Description'].'\', \''.$datas['Categorie'].'\', \''.$datas['Taille'].'\', \''.$datas['PAChasseur'].'\', \''.$datas['PAGVG'].'\', \''.$datas['Temps'].'\', \''.$datas['Titane'].'\', \''.$datas['Cuivre'].'\', \''.$datas['Fer'].'\', \''.$datas['Aluminium'].'\', \''.$datas['Mercure'].'\', \''.$datas['Silicium'].'\', \''.$datas['Uranium'].'\', \''.$datas['Krypton'].'\', \''.$datas['Azote'].'\', \''.$datas['Hydrogene'].'\', \''.$datas['PropImpulsion'].'\', \''.$datas['PropWarp'].'\', \''.$datas['PropConsommation'].'\', \''.$datas['ArmType'].'\', \''.$datas['ArmDegat'].'\', \''.$datas['ArmManiabilite'].'\', \''.$datas['ProtType'].'\', \''.$datas['ProtChasseur'].'\', \''.$datas['ProtGVG'].'\', \''.$datas['EquipType'].'\', \''.$datas['EquipNiv'].'\')';
            $datas_id = DataEngine::sql($query);
			echo("<br /><center><b>Templates de module ajouté.</b></center><br /><br />");
			echo("<br /><br /><hr width='50%' /><p align=center>&nbsp;<a href='user_importmod.php'>Autre importation...</a>&nbsp;</p>");
          }
		}
/* #####################################################################################
   #####################################################################################

   Données non reconnues

   #####################################################################################
   ##################################################################################### */
      else {
        echo("<br /><center><b>Données non reconnues.</b></center><br /><br />");
		}
    }
  }

  if($bcontinue) {
?>
<form name="form1" method="post" action="">
<table border="1" align="center" cellpadding="3" cellspacing="0" width=80%>
  <tr class="color_row0">
    <td>Pour importer des données, il vous suffit de sélectionner une page du jeu (CTRL-A), de la copier entièrement (CTRL-C) puis de la coller dans le champs à droite (CTRL-V).<br />
    <br />
    Vous pouvez importer:
    <ul>
    <li><b>Liste des modules</b> que vous pouvez fabriquer (<i>Bâtiment -> Chantier Naval -> Modules</i>).</li>
<?php if(DataEngine::CheckPerms('ZZZ_COMMERCE_TPL_INSERT')) { ?>
    <li><b>Template de module</b> (<i>Bâtiment -> Chantier Naval -> Modules -> Click sur Module concerné</i>).</li>
<?php } ?>
    </ul></td>
    <td>
      Données à interpréter: <textarea class="color_row0" name="RawData" cols="45" rows="5" wrap="virtual" id="RawData"></textarea><br />
      <input class="color_row0" type="checkbox" name="ReplaceAll" id="ReplaceAll" /> Avant cette importation, remise à zéro de la liste des modules que vous pouvez fabriquer.
    </td>
  </tr>
</table>
<br />
<center><input class="color_row0" type="submit" name="submit" id="submit" value="Interpréter"></center>
</form>
<br />
<?php } ?>

</font></center>
<!-- FIN CODE LIBRE -->
</BODY></HTML>


<?php

	require_once(TEMPLATE_PATH.'sample.tpl.php');
$tpl = tpl_sample::getinstance();
$tpl->page_title = "EU2: Commerce";
$tpl->PushOutput(ob_get_clean());
$tpl->doOutput();