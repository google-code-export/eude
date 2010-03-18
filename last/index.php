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
//require_once(INCLUDE_PATH.'update.php');
require_once(CLASS_PATH.'parser.class.php');
require_once(CLASS_PATH.'cartographie.class.php');
require_once(CLASS_PATH.'map.class.php');

if (!DataEngine::CheckPerms('CARTOGRAPHIE')) {
    if (DataEngine::CheckPerms('CARTE'))
        output::Boink(ROOT_URL.'Carte.php');
    else
        output::Boink(ROOT_URL.'Mafiche.php');
}
//else output::Boink(ROOT_URL.'cartographie.php');

$where = "WHERE 1=1 ";
if (DataEngine::CheckPerms('CARTOGRAPHIE_SEARCH')) {
    if (isset($_COOKIE['Recherche'])) {
        foreach ($_COOKIE['Recherche'] as $key => $value) {
            $Rech[$key] = $value;
        }
    }

    if(isset($_GET['ResetSearch']) && $_GET['ResetSearch']!="") {
        if (isset($_COOKIE['Recherche'])) {
            foreach ($_COOKIE['Recherche'] as $key => $value) {
                SetCookie("Recherche[$key]","",time()-1,ROOT_URL);
            }
        }
        // 			unset($_POST); unset($_COOKIE); unset($Rech);
        output::boink(ROOT_URL.basename(__file__));
    }

    if (isset($_POST['RechercheStatut']) || isset($Rech['RechercheStatut'])) {
        if(isset($_POST['RechercheStatut'])) {
            SetCookie("Recherche[RechercheStatut]",$_POST['RechercheStatut'],time()+3600*24,ROOT_URL);
            $Rech['RechercheStatut'] = $_POST['RechercheStatut'];
        }
        if ( ($Rech['RechercheStatut'] != "") && ($Rech['RechercheStatut'] != "-1"))
            $where.= 'AND Inactif='.$Rech['RechercheStatut'].' ';
        else $Rech['RechercheStatut'] = "-1";
    } else $Rech['RechercheStatut'] = "-1";

//if (isset($_POST['RechercheDate']) || isset($Rech['RechercheDate'])) {
//    if(isset($_POST['RechercheDate'])) {
//        SetCookie("Recherche[RechercheDate]",$_POST['RechercheDate'],time()+3600*24,ROOT_URL);
//        $Rech['RechercheDate'] = $_POST['RechercheDate'];
//    }
//    if ($Rech['RechercheDate'] != "")
//        $where.= 'AND Date=\''.$Rech['RechercheDate'].'%\' ';
//}

    if (isset($_POST['RechercheType']) || isset($Rech['RechercheType'])) {
        if(isset($_POST['RechercheType'])) {
            SetCookie("Recherche[RechercheType]",$_POST['RechercheType'],time()+3600*24,ROOT_URL);
            $Rech['RechercheType'] = $_POST['RechercheType'];
        }
        if (($Rech['RechercheType'] != "") && ($Rech['RechercheType'] != "-1"))
            $where.= 'AND TYPE IN ('.$Rech['RechercheType'].')';
        else $Rech['RechercheType'] = "-1";
    } else $Rech['RechercheType'] = "-1";


    if (isset($_POST['RecherchePos']) || isset($Rech['RecherchePos'])) {
        if(isset($_POST['RecherchePos'])) {
            SetCookie("Recherche[RecherchePos]",$_POST['RecherchePos'],time()+3600*24,ROOT_URL);
            $Rech['RecherchePos'] = $_POST['RecherchePos'];
        }
    }
    if (isset($_POST['RechercheRayon']) || isset($Rech['RechercheRayon'])) {
        if(isset($_POST['RechercheRayon'])) {
            SetCookie("Recherche[RechercheRayon]",$_POST['RechercheRayon'],time()+3600*24,ROOT_URL);
            $Rech['RechercheRayon'] = $_POST['RechercheRayon'];
        }
    }
//Traitement de recherche par position et rayon
    if ($Rech['RecherchePos'] != "") {
        if(!is_numeric($Rech['RechercheRayon']) ||($Rech['RechercheRayon']<0) ) $Rech['RechercheRayon']="";
        if($Rech['RechercheRayon']=="") {
            $where.= 'AND (POSIN like \''.$Rech['RecherchePos'].'\' OR POSOUT like \''.$Rech['RecherchePos'].'\') ';
        } else {
            $ListeCoor = implode(",",map::getinstance()->Parcours()->GetListeCoorByRay($Rech['RecherchePos'],$Rech['RechercheRayon']));
            $where.= 'AND (POSIN IN ('.$ListeCoor.') OR POSOUT IN ('.$ListeCoor.'))';
        }
    }



//Traitement recherche planete uniquement si type = planete
    if($Rech['RechercheType']==2) {
        foreach(DataEngine::a_ressources() as $id => $Ress) {
            if(isset($_POST['RechercheRessource'.$id])) {
                SetCookie("Recherche[RechercheRessource".$id."]",$_POST['RechercheRessource'.$id],time()+3600*24,ROOT_URL);
                $Rech['RechercheRessource'.$id] = $_POST['RechercheRessource'.$id];
            }
            if (($Rech['RechercheRessource'.$id] != "") && ($Rech['RechercheType'] != "-1")) {
                $where.= ' AND (SELECT CASE WHEN './*stripacc*/($Ress["Field"]).'="beaucoup" THEN 70 ';
                $where.= 'WHEN './*stripacc*/($Ress["Field"]).'="normal" THEN 40 ';
                $where.= 'WHEN './*stripacc*/($Ress["Field"]).'="peu" THEN 20 ';
                $where.= 'ELSE substring('./*stripacc*/($Ress["Field"]).',1,length('./*stripacc*/($Ress["Field"]).')-1) ';
                $where.= ' END) '.$Rech['RechercheRessource'.$id];
            }
            else $Rech['RechercheRessource'.$id] = "-1";
        }

    } else {
        foreach(DataEngine::a_ressources() as $id => $Ress) {
            $Rech['RechercheRessource'.$id] = "-1";

        }
    }

    if (isset($_POST['RechercheUser']) || isset($Rech['RechercheUser'])) {
        if(isset($_POST['RechercheUser'])) {
            SetCookie("Recherche[RechercheUser]",$_POST['RechercheUser'],time()+3600*24,ROOT_URL);
            $Rech['RechercheUser'] = $_POST['RechercheUser'];
        }
        if ($Rech['RechercheUser'] != "")
            $where.= 'AND USER like \'%'.sqlesc($Rech['RechercheUser']).'%\' ';
    }

    if (isset($_POST['RechercheEmpire']) || isset($Rech['RechercheEmpire'])) {
        if(isset($_POST['RechercheEmpire'])) {
            SetCookie("Recherche[RechercheEmpire]",sqlesc($_POST['RechercheEmpire']),time()+3600*24,ROOT_URL);
            $Rech['RechercheEmpire'] = $_POST['RechercheEmpire'];
        }
        if ($Rech['RechercheEmpire'] != "")
            $where.= 'AND EMPIRE like \'%'.sqlesc($Rech['RechercheEmpire']).'%\' ';
    }

    if (isset($_POST['RechercheInfos']) || isset($Rech['RechercheInfos'])) {
        if(isset($_POST['RechercheInfos'])) {
            SetCookie("Recherche[RechercheInfos]",sqlesc($_POST['RechercheInfos']),time()+3600*24,ROOT_URL);
            $Rech['RechercheInfos'] = $_POST['RechercheInfos'];
        }
        if ($Rech['RechercheInfos'] != "")
            $where.= 'AND INFOS like \'%'.sqlesc($Rech['RechercheInfos']).'%\' ';
    }

    if (isset($_POST['RechercheNote']) || isset($Rech['RechercheNote'])) {
        if(isset($_POST['RechercheNote'])) {
            SetCookie("Recherche[RechercheNote]",sqlesc($_POST['RechercheNote']),time()+3600*24,ROOT_URL);
            $Rech['RechercheNote'] = $_POST['RechercheNote'];
        }
        if ($Rech['RechercheNote'] != "")
            $where.= 'AND NOTE like \'%'.sqlesc($Rech['RechercheNote']).'%\' ';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['RechercheMoi'])) {
        $Rech['RechercheMoi'] = '0';
        SetCookie('Recherche[RechercheMoi]','0',time()+3600*24,ROOT_URL);
    }

    if (isset($_POST['RechercheMoi']) || ( isset($Rech['RechercheMoi']) && $Rech['RechercheMoi']=='1' )) {
        $Rech['RechercheMoi'] = '1';
        SetCookie("Recherche[RechercheMoi]",$Rech['RechercheMoi'],time()+3600*24,ROOT_URL);
        $where.= ' AND UTILISATEUR=\''.strtolower($_SESSION['_login']).'\' ';
    }
} // CXX_CARTOGRAPHIE_SEARCH

$erreur = "";

// GESTION DES TRI
$tri="ORDER BY INACTIF ASC";
$triorder="";

foreach ($_GET as $key => $value) {
    if ($key == 'TriEmp') {
        if($_GET['TriEmp']==1) $tri .= ",EMPIRE";
        else $triorder .= ",EMPIRE DESC";
    }
    if ($key == 'TriJoueur') {
        if($_GET['TriJoueur']==1) $tri.=",USER";
        else $triorder .= ",USER DESC";
    }
    if ($key == 'TriCoor') {
        if($_GET['TriCoor']==1) $tri .= ",POSIN";
        else $triorder .= ",POSIN DESC";
    }
    if ($key == 'TriType') {
        if($_GET['TriType']==1) $tri .= ",TYPE";
        else $triorder .= ", TYPE DESC";
    }
    if ($key == 'TriDate') {
        if($_GET['TriDate']==1) $tri .= ",DATE";
        else $triorder .= ", DATE DESC";
    }
    if ($key == 'TriInfos') {
        if($_GET['TriInfos']==1) $tri .= ",INFOS";
        else $triorder .= ", INFOS DESC";
    }
    if ($key == 'TriNote') {
        if($_GET['TriNote']==1) $tri .= ",NOTE";
        else $triorder .= ", NOTE DESC";
    }
    if ($key == 'TriCoorOut') {
        if($_GET['TriCoorOut']==1) $tri .= ",POSOUT";
        else $triorder .= ", POSOUT DESC";
    }

}
$triorder.=", ID DESC";
$tri = $tri.$triorder;

$carto = cartographie::getinstance();

/** ### Gestion des modifications "automatique" ### **/
if (isset($_POST['Type'])) {
    $info = "";
    $parsed = false;

    // SS brut
    if ($_POST["phpparser"] == 1) {
        $carto->add_solar_ss(gpc_esc($_POST["importation"]));
        $parsed = true;
    } // SS brut

    // check if all needed fields...
    if ($_POST["phpparser"] != 1) {
        if ($_POST["Type"] != 1 and $_POST["COORIN"] == "") $erreur = 'Les coordonnés d\'entrée doivent-être renseigné';
        if ($_POST["Type"] != 1 and $_POST["COOROUT"] != "") $erreur = 'Les coordonnés de sortie ne sont à renseigner que pour les Vortex';
        if ($_POST["Type"] == 1 and $_POST["COOROUT"] == "") $erreur = 'Il faut impérativement renseigner Les coordonnés de sortie pour les Vortex';
        if ($_POST["Type"] == 0 and $_POST["USER"] == "") $erreur = 'Merci de renseigner le nom du joueur';
    }

    // Joueur,Ennemis,PNJ...Partie traitement html...
    if (!$parsed and $erreur=='' and in_array($_POST['Type'], array(0,3,5,6))) {
//        list($info, $erreur) = add_player($_POST['COORIN'], $_POST['INFOS'], $_POST['USER'],$_POST['EMPIRE'], $_POST['NOTE'], $_POST['Type']);
        $_POST['COORIN']= sqlesc($_POST['COORIN']);
        $_POST['INFOS']= sqlesc($_POST['INFOS']);
        $_POST['USER']= sqlesc($_POST['USER']);
        $_POST['EMPIRE']= sqlesc($_POST['EMPIRE']);
        $_POST['NOTE']= sqlesc($_POST['NOTE']);
        $_POST['Type']= sqlesc($_POST['Type']);

        $carto->add_player($_POST['COORIN'], $_POST['INFOS'], $_POST['USER'],$_POST['EMPIRE'], $_POST['NOTE'], $_POST['Type']);
        $parsed = true;
    }

    // Vortex...
    if (!$parsed and $erreur=="" and $_POST["Type"] == 1) {
//        list($info, $erreur) = add_vortex($_POST["COORIN"],$_POST["COOROUT"], $_POST["INFOS"], $_POST["USER"], $_POST["NOTE"]);
        $carto->add_vortex($_POST['COORIN'],$_POST['COOROUT'], $_POST['INFOS'], $_POST['USER'], $_POST['NOTE']);
        $parsed = true;
    }

    // Planète, Astéroides...
    if (!$parsed and $erreur=="" and in_array($_POST["Type"], array(2,4))) {
        foreach(DataEngine::a_Ressources() as $id => $dummy) $Ress[$id] = gpc_esc($_POST["RESSOURCE".$id]);

        //list($info, $erreur) =
        $carto->add_planet_asteroid(gpc_esc($_POST["COORIN"]), $Ress, gpc_esc($_POST["Type"]), gpc_esc($_POST["NOTE"]));
        $parsed = true;
    } // Planète/Astéroïde

} // Fin gestion des modifications "automatique"


/** ### Gestion des modifications "manuellement" ### **/
if (isset($_POST['NewStatut'])) {
    $query = 'UPDATE SQL_PREFIX_Coordonnee SET INACTIF="'.$_POST["NewStatut"].'" WHERE ID='.$_POST["ID"];
    DataEngine::sql($query);
}

if (isset($_POST['modifTYPE'])) {
    if($_POST["modifDELETE"]==1) {
        if ($_POST["modifTYPE"] == 2 or $_POST["modifTYPE"] == 4) {
            $query="DELETE FROM SQL_PREFIX_Coordonnee_Planetes WHERE pID={$_POST["ID"]}";
            DataEngine::sql($query);
        }
        $query='DELETE FROM SQL_PREFIX_Coordonnee WHERE ID='.$_POST["ID"];
        DataEngine::sql($query);
    } else {
        switch ($_POST["modifTYPE"]) {
            case 0:
            case 3:
            case 5:
            case 6:
//                list($info, $erreur) = add_player($_POST["modifCOORIN"], $_POST["modifINFOS"], $_POST["modifUSER"],$_POST["modifEMPIRE"],$_POST['NOTE'],$_POST["modifTYPE"]);
                $erreur = $carto->add_player($_POST["modifCOORIN"], $_POST["modifINFOS"], $_POST["modifUSER"],$_POST["modifEMPIRE"],$_POST['NOTE'],$_POST["modifTYPE"]);
                if (!$erreur) {
                    $query="DELETE FROM SQL_PREFIX_Coordonnee_Planetes WHERE pID={$_POST["ID"]}";
                    DataEngine::sql($query);
                }
                break;
            case 2:
            case 4:
            //foreach(DataEngine::a_ressources() as $id => $dummy)
                $Ress = array();
                for ($i=0;$i<10;$i++)    $Ress[$i] = $_POST[$_POST['ID'].'ModifRessource'.$i];
                $carto->add_planet_asteroid($_POST["modifCOORIN"], $Ress, $_POST["modifTYPE"]);
                break;
            case 1:
                $carto->add_vortex($_POST["modifCOORIN"],$_POST["modifCOOROUT"], $_POST["modifINFOS"], $_POST["modifUSER"], $_POST["NOTE"]);
                break;
            default: $erreur = "Type de modification non reconnu... [".__line__."]";
        }
    } // end if modif type...
}



// GESTION DES PAGES
if (isset($_GET['Page']))
    $PageCurr = $_GET['Page'];
else
    $PageCurr = 0;

$Maxline = 20;
// 		$erreurselect ="Yes !"; // Désactive le tableau
$limit = " LIMIT ".($PageCurr*$Maxline).",".$Maxline;


/// ### ### ### ### ### ### ### ### ###
/// ### ### ### Partie traitement html...
/// ### ### ### ### ### ### ### ### ###

include_once(TEMPLATE_PATH.'index.tpl.php');

$tpl = tpl_index::getinstance();

$erreur .= $carto->Warns();
$info   .= $carto->Infos();

// Partie insertion
$tpl->insert_part1();
$tpl->SelectOptions($cctype,$_POST["Type"],'Type');
$tpl->insert_part2(DataEngine::a_ressources());
$tpl->RessTextRow(DataEngine::a_ressources(), 'RESSOURCE');
$tpl->insert_part3($erreur, $info);

// Partie de recherche
if (DataEngine::CheckPerms('CARTOGRAPHIE_SEARCH')) {
    $tpl->search_part1($Rech['RechercheStatut']);
    $tpl->SelectOptions($cctype,$Rech['RechercheType']);
    $tpl->search_part2($Rech);
    $tpl->RessImgRow(DataEngine::a_ressources());
    $tpl->search_part3(DataEngine::a_ressources(),$Rech);
}

// 		$query = "SELECT count(*) as Nb from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.POSIN=b.COOR and a.COORDET=b.COORDET) $where";
$query = "SELECT count(*) as Nb from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.ID=b.pID) $where";
$mysql_result = DataEngine::sql($query,false) or $erreurselect='Ooops';
if($erreurselect!="") {
    $erreur="Impossible d'effectuer la requête, vous devez changer de recherche<br>";
    $erreurselect = true;
} else {
    $ligne=mysql_fetch_array($mysql_result);
    $NbLigne = $ligne["Nb"];
    $MaxPage = ceil($NbLigne / $Maxline)-1;
    if($PageCurr > $MaxPage)
        $PageCurr = $MaxPage;
    else if ($PageCurr < 0)
        $PageCurr = 0;
}

if(!$erreurselect) {
// 			$sql="SELECT * from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.POSIN=b.COOR and a.COORDET=b.COORDET) ".$where." ".$tri.$limit;
    $sql="SELECT * from SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Coordonnee_Planetes b on (a.ID=b.pID) ".$where." ".$tri.$limit;

    $mysql_result = DataEngine::sql($sql);
}		

// Partie de résultats
$tpl->result_part1($PageCurr, $MaxPage, $myget);

if($_SESSION['_Perm']>=AXX_MEMBER || strtolower($ligne["UTILISATEUR"])==strtolower($_SESSION['_login'])) $disabled="";
else $disabled=" disabled ";

while (!$erreurselect and $ligne=mysql_fetch_array($mysql_result, MYSQL_ASSOC))
    $tpl->search_row($ligne,$disabled,$cctype,$myget);

$tpl->result_pagination($PageCurr, $MaxPage,$myget);
$tpl->DoOutput();


