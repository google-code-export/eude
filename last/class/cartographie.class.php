<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 * @since 1.4.1
 *
 **/
DataEngine::conf_cache('EmpireAllys');
DataEngine::conf_cache('EmpireEnnemy');

class cartographie {
    static protected $instance;
    private $nbmsg;
    private $pinfos;
    private $perreurs;
    private $pwarns;

    protected $allys;
    protected $wars;

    /**
     * Ajoute/réactive un vortex dans la base
     * @param string $coordsin
     * @param string $coordsout
     * @param string* $infos
     * @param string* $nom
     * @param string* $note
     * @return boolean
     */
    function add_vortex($coordsin, $coordsout, $infos='', $nom='', $note='') {
        if (($warn=$this->FormatId(trim($coordsin), $INidfixe, $INiddet)) !='') return $this->AddErreur($warn.' du vortex (Entrée)');
        if (($warn=$this->FormatId(trim($coordsout), $OUTidfixe, $OUTiddet)) !='') return $this->AddErreur($warn.' du vortex (Sortie)');

        $nom   = sqlesc(trim($nom));
        $infos = sqlesc(trim($infos));
        $note = sqlesc(trim($note));

        $query = 'SELECT ID,INACTIF FROM SQL_PREFIX_Coordonnee where POSIN=\''.$INidfixe.'\' AND POSOUT=\''.$OUTidfixe.'\' AND COORDET=\''.$INiddet.'\' AND COORDETOUT=\''.$OUTiddet.'\'';
        $mysql_result = DataEngine::sql($query);
        if (mysql_num_rows($mysql_result) > 0) {
            $ligne = mysql_fetch_assoc($mysql_result);
            if($ligne['ID'] > 0) {
                DataEngine::sql('UPDATE SQL_PREFIX_Coordonnee SET `INACTIF`=0,`NOTE`=\''.$note.'\' WHERE TYPE=1 AND (POSIN=\''.$INidfixe.'\' AND POSOUT=\''.$OUTidfixe.'\')');
                if (mysql_affected_rows() > 0) {
                    if ($ligne['INACTIF']=='0')
                        $this->AddInfo('Le vortex '.$coordsin.' vers '.$coordsout.' a été modifié');
                    else
                        $this->AddInfo('Le vortex '.$coordsin.' vers '.$coordsout.' a été réactivée');
                } else
                    $this->AddWarn('Le vortex '.$coordsin.' vers '.$coordsout.' existe déjà');
            }
        } else {
            $query1 = 'INSERT INTO SQL_PREFIX_Coordonnee (TYPE,POSIN,COORDET,POSOUT,COORDETOUT,USER,EMPIRE,INFOS,DATE,NOTE,UTILISATEUR) ';
            $query1 .= 'VALUES (1,\''.$INidfixe.'\',\''.$INiddet.'\',\''.$OUTidfixe.'\',\''.$OUTiddet.'\',\''.$nom.'\',\'\',\''.$infos.'\',NOW(),\'\',\''.$_SESSION['_login'].'\')';
            DataEngine::sql($query1);
            $this->AddInfo('Le vortex '.$coordsin.' <> '.$coordsout.' ajouté...');
        }

        return $this->Messages();
    }

    /**
     * @param string $coords
     * @param string $planete
     * @param string $nom
     * @param string $empire
     * @param string $note
     * @param integer $type (0,3,5,6)
     * @return boolean
     */
    public function add_player($coords, $planete='', $nom='', $empire='', $note=false, $type=0) {
        $result = $this->Messages();
        $updatetype=true;

        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLAYERS'))
            return $this->AddErreur('Permissions manquante');

        if (is_array($coords)) {
            list($coords, $planete, $nom, $empire) = $coords;
            $updatetype=false;
        }

        if ($empire != '') {
            if (in_array($empire, $this->allys)) {
                $updatetype=true;
                $type = 3;
            }
            if (in_array($empire, $this->wars)) {
                $updatetype=true;
                $type = 5;
            }
        }
//    $types = '0,3,5,6'; // Type pris en charge...
        switch ($type) {
            case 3 : $stype = 'L\'allié'; // planète
                break;
            case 5 : $stype = 'L\'ennemi'; // planète
                break;
            case 6 : $stype = 'La flotte'; // pnj
                break;
            default: $stype = 'Le joueur'; // planète
        }
        $qnom     = sqlesc(trim($nom));
        $qempire  = sqlesc(trim($empire));
        $qplanete = sqlesc(trim($planete));
        $qnote    = sqlesc(trim($note));

        if (($warn=$this->FormatId(trim($coords), $uni, $sys)) =='') {
            if ($nom=='') {
                $query = "DELETE FROM SQL_PREFIX_Coordonnee where Type in (0,5) AND POSIN='{$uni}' AND COORDET='{$sys}'";
                $array = DataEngine::sql($query);
                if ( ($num = mysql_affected_rows()) > 0)
                    $this->AddWarn('Planète(s) devenue inoccupée: '.$coords);
                return $result > $this->Messages();
            }
            $query = 'SELECT ID,TYPE FROM SQL_PREFIX_Coordonnee where POSIN=\''.$uni.'\' AND COORDET=\''.$sys.'\'';
            $array = DataEngine::sql($query);
            $ligne = mysql_fetch_assoc($array);
            if($ligne['ID'] > 0) {
                if (!$updatetype) $type = $ligne['TYPE'];
                if ($note)
                    $query = sprintf('UPDATE SQL_PREFIX_Coordonnee SET `TYPE`=%d,`POSOUT`=\'\',`COORDETOUT`=\'\',`USER`=\'%s\',`EMPIRE`=\'%s\','.
                            '`INFOS`=\'%s\',`UTILISATEUR`=\'%s\',`NOTE`=\'%s\',DATE=NOW() WHERE ID=%s',
                            $type, $qnom, $qempire, $qplanete, sqlesc($_SESSION['_login'], true), $qnote, $ligne['ID'] );
                else
                    $query = sprintf('UPDATE SQL_PREFIX_Coordonnee SET `TYPE`=%d,`POSOUT`=\'\',`COORDETOUT`=\'\',`USER`=\'%s\',`EMPIRE`=\'%s\','.
                            '`INFOS`=\'%s\',`UTILISATEUR`=\'%s\',DATE=NOW() WHERE ID=%s',
                            $type, $qnom, $qempire, $qplanete, sqlesc($_SESSION['_login'], true), $ligne['ID'] );

                DataEngine::sql($query);
                if (mysql_affected_rows() > 0) {
                    if (NO_SESSIONS)
                        $this->AddInfo('MAJ '.$sys.': '.$stype.' '.$nom);//.' [{$ligne['ID']}]');
                    else
                        $this->AddInfo($stype.' '.$nom.' mit à jour au coordonnée : '.$uni.'-'.$sys);//.' [{$ligne['ID']}]');
                } else {
                    if (NO_SESSIONS)
                        $this->AddInfo('Ignoré '.$sys.': '.$stype.' '.$nom);
                    else
                        $this->AddInfo($stype.' '.$nom.' existe déjà au coordonnée : '.$uni.'-'.$sys.' (ignoré)');
                }
            } else {
                $query = sprintf('INSERT INTO SQL_PREFIX_Coordonnee (TYPE,POSIN,POSOUT,COORDET,COORDETOUT,USER,EMPIRE,INFOS,NOTE,DATE,UTILISATEUR)'.
                        ' VALUES (%d,\'%s\',\'\',\'%s\',\'\',\'%s\',\'%s\',\'%s\',\'%s\',now(),\'%s\')',
                        $type, $uni, $sys, $qnom, $qempire, $qplanete, $qnote, sqlesc($_SESSION['_login'],true));
                DataEngine::sql($query);
                if (NO_SESSIONS)
                    $this->AddInfo('Ajout '.$sys.': '.$stype.' '.$nom);
                else
                    $this->AddInfo($stype.' '.$nom.' ajouté au coordonnée : '.$uni.'-'.$sys);
            }
        } else $this->AddErreur($warn);

        return $result > $this->Messages();
    }

    public function add_planet_asteroid($coords, $ress_val, $type, $note="") {
        $Ressource=DataEngine::a_Ressources();
        $warn='';

        if ($type==2 && !DataEngine::CheckPerms('CARTOGRAPHIE_PLANETS'))
            return $this->AddErreur('Permissions manquante');
        if ($type==4 && !DataEngine::CheckPerms('CARTOGRAPHIE_ASTEROID'))
            return $this->AddErreur('Permissions manquante');

        if (($warn=$this->FormatId(trim($coords), $uni, $sys)) !="")
            return $this->AddWarn($warn.' pour '.(($type==2)? 'la planète': 'l\'astéroïde') );

        $qnote = mysql_escape_string(trim(htmlspecialchars($note,ENT_QUOTES,'UTF-8')));

        $query = "SELECT ID FROM SQL_PREFIX_Coordonnee where POSIN='$uni' AND COORDET='$sys'";
        $array = DataEngine::sql($query);
        $ligne = mysql_fetch_array($array);
        $do_update = intval($ligne['ID']);

        $sql=$insert_Val=$insert_field= '';

        foreach ($ress_val as $id => $value) {
            if (!is_numeric($id)) continue;
            if(!$this->Ressources_Check_Value($value, $type)) {
                return $this->AddErreur('Format de la valeur de la ressource '.$Ressource[$id]['Nom'].' incorrecte ('.$value.'), autorisé : (p)eu,(n)ormal,(b)eaucoup,xx,xx%');
            }

            $field = mysql_escape_string($Ressource[$id]['Field']);
            $newval = mysql_escape_string($value);
            if ($do_update) {
                if ($sql != '')
                    $sql   .= ', ';
                $sql .= "`$field`='$newval'";
            } else {
                if ($insert_Val  != '')
                    $insert_Val    .= ', ';
                $insert_Val  .= "'$newval'";
                if ($insert_field!= '')
                    $insert_field  .= ', ';
                $insert_field.=  "`$field`";
            }
        }

        $stype = (($type==2)? 'La planète': 'L\'astéroïde');

        if ($do_update) {
            $updated = 0;
            $query = "UPDATE `SQL_PREFIX_Coordonnee` SET `NOTE`='$qnote',DATE=NOW() `INACTIF`=0 WHERE `ID`=$do_update";
            DataEngine::sql($query);
            $updated +=mysql_affected_rows();
            $query = "UPDATE `SQL_PREFIX_Coordonnee_Planetes` SET $sql WHERE `pID`=$do_update";
            DataEngine::sql($query);
            $updated +=mysql_affected_rows();
            if ($updated > 0)
                $this->AddInfo($stype.' mit à jour au coordonnée : '.$uni.'-'.$sys);
            else
                $this->AddWarn($stype.' existe déjà au coordonnée : '.$uni.'-'.$sys.' (ignoré)');
        } else {
            $query    = 'INSERT INTO SQL_PREFIX_Coordonnee (TYPE,POSIN,POSOUT,COORDET,USER,EMPIRE,INFOS,NOTE,DATE,UTILISATEUR) ';
            $query   .= "VALUES ($type,'$uni','','$sys','','','','$qnote',now(),'{$_SESSION['_login']}')";
            DataEngine::sql($query);
            $pID = mysql_insert_id();

            $query2="INSERT INTO `SQL_PREFIX_Coordonnee_Planetes` (`pID`,$insert_field) VALUES($pID,$insert_Val)";
            DataEngine::sql($query2,false) or $warn="($rows) $query<br/>$query2<br/>".print_r($ress_val,true)."<br/>".mysql_error();

            if ($warn!='') {
                DataEngine::sql('DELETE FROM SQL_PREFIX_Coordonnee WHERE ID='.$pID.' LIMIT 1');
                $this->AddWarn($warn);
            }

            $this->AddInfo($stype.' ajouté au coordonnée : '.$uni.'-'.$sys);
        }
        return true;
    }

    /**
     *  @param string				Données contenant tout (+/- brut)...
     *  @return array				($info, $warn)
     */
    public function add_solar_ss($importation) {
        $cur_ss = false;
        $del_planet = $curss_info = array();
        if (DataEngine::$browser->getBrowser() == Browser::BROWSER_IE)
            $sep = '  ';
        else
            $sep = "\t\t";
        $info=$warn='';
        $SS_A = array();
        $lignes = explode("\n", $importation);
        for($i=0,$size=count($lignes);$i<$size;$i++) {
            $ligne = trim($lignes[$i]);
            if ($ligne=='') continue;
            list($coords, $planete, $nom) = explode($sep, $ligne);
            $coords = trim($coords);
            $planete = trim($planete);
            $nom = trim($nom);
            if ( (strlen($coords)<6) ) continue;
            if (!$cur_ss) if ($this->FormatId($coords,$cur_ss,$dummy) != '') $cur_ss = false;
            if ( trim($lignes[$i+1]) != '') { // Empire ?
                list($empire) = explode($sep, trim($lignes[$i+1]));
                $empire = trim($empire);
                if ($this->FormatId($empire,$dummy,$dummy) != '') { // n'est pas une coords
                    $SS_A[$i] = array($coords, $planete, $nom, $empire); // Joueur avec empire
                    $i++;
                } elseif ($nom != '')
                    $SS_A[$i] = array($coords, $planete, $nom, ""); // Joueur sans empire
            } elseif ($nom != '') {
                $SS_A[$i] = array($coords, $planete, $nom, ''); // Joueur sans empire
            }
            if ($nom == '' && $this->FormatId($coords, $dummy, $sys) == '') // Planète inoccupée
                $del_planet[] = $sys;
        }

        if (count($del_planet)>0) {
            $del_planet = "'".implode("','",$del_planet)."'";
            $query = "DELETE FROM SQL_PREFIX_Coordonnee where Type in (0,5) AND POSIN='{$cur_ss}' AND COORDET in ({$del_planet})";
            $array = DataEngine::sql($query);
            if ( ($num = mysql_affected_rows()) > 0)
                $this->AddInfo($num.' planète(s) devenue inoccupée dans le système '.$cur_ss);
        }

        $query = "SELECT USER,EMPIRE FROM SQL_PREFIX_Coordonnee where POSIN='{$cur_ss}' AND TYPE=0";
        $sql_result = DataEngine::sql($query);
        while ($row = mysql_fetch_assoc($sql_result)) {
            // par nom de joueur
            $curss_info[$row['USER']] = $row['EMPIRE'];
        }

        foreach($SS_A as $v) {
            $result = $this->add_player($v);
            if ($result) { // uniquement si changement, vide autrement.
                list($dummy, $dummy, $nom, $empire) = $v;
                $nom    = gpc_esc($nom);
                $empire = gpc_esc($empire);
                if (isset($curss_info[$nom])) {
                    if ($curss_info[$nom] != $empire) {
                        $qnom    = sqlesc($nom, true);
                        $qempire = sqlesc($empire, true);
                        $query = "UPDATE SQL_PREFIX_Coordonnee SET `EMPIRE`='{$qempire}',`UTILISATEUR`='{$_SESSION['_login']}' WHERE USER='{$qnom}'";
                        DataEngine::sql($query);
                        $this->AddInfo('Changement d\'empire du joueur: \''.$nom.'\' ['.mysql_affected_rows().']');
                        unset($curss_info[$nom]);
                    }
                }
            }
        }
        return $result;
    }

    //    Routines...
    public function Messages() {
        return $this->nbmsg;
    }
    public function AddErreur($value) {
        if ($value=='') return true;
        $this->nbmsg++;
        array_push($this->perreurs, $value);
        return false;
    }
    public function AddInfo($value) {
        if ($value=='') return false;
        $this->nbmsg++;
        array_push($this->pinfos, $value);
        return true;
    }
    public function AddWarn($value) {
        if ($value=='') return true;
        $this->nbmsg++;
        array_push($this->pwarns, $value);
        return false;
    }

    public function Erreurs() {
        if (count($this->perreurs)>0)
            return '<font color="red">'.implode('<br/>', $this->perreurs).'</font>';
        else
            return '';
    }
    public function Infos() {
        if (count($this->pinfos)>0)
            return '<font color="green">'.implode('<br/>', $this->pinfos).'</font>';
        else
            return '';
    }
    public function Warns() {
        if (count($this->pwarns)>0)
            return '<font color="darkorange">'.implode('<br/>', $this->pwarns).'</font>';
        else
            return '';
    }
    public function FormatId($id,&$idsys,&$iddet) {
        $tmppos = str_replace(':','-',$id);
        $tmppos = explode('-',$tmppos);

        if (count($tmppos) != 4)
            return 'Erreur, le format de coordonnée doit-être xxxx-xx-xx-xx ou xxxx:xx:xx:xx';
        if ((!is_numeric($tmppos[0]) || !is_numeric($tmppos[1]) || !is_numeric($tmppos[2]) || !is_numeric($tmppos[3])))
            return 'Erreur, le format de coordonnée doit-être numérique au format xxxx-xx-xx-xx ou xxxx:xx:xx:xx';

        if($erreur=='') {
            $idsys = $tmppos[0];
            $iddet = intval($tmppos[1]).'-'.intval($tmppos[2]).'-'.intval($tmppos[3]);
        }

        return($erreur);
    }

    private function Ressources_Check_Value(&$value, $type) {
        if(is_numeric($value) && $value > 0) return true;
        $value=strtolower($value);
        if( ($value=='b') || ($value=='bcp') || ($value=='beaucoup')) $value= 'beaucoup';
        if( ($value=='p') || ($value=='peu') || ($value=='pe') ) $value= 'peu';
        if( ($value=='n') || ($value=='nor') || ($value=='normal') ) $value= 'normal';
        switch ($value) {
            case 'très peu':
            case 'peu':
            case 'normal':
            case 'moyennement':
            case 'beaucoup':
            case 'considérablement':
            case 'énormément':
                return true;
        }
        if ($type == 2) {
            if(!is_numeric($value) && is_numeric(substr($value,0,strlen($value)-1))) $value=substr($value,0,strlen($value)-1);
            if(is_numeric($value) && $value >=100) $value = 100;
            if(is_numeric($value) && $value <0) $value=0;
            if(is_numeric($value)) {
                $value=$value.'%';
                return true;
            }
        }
        if($value=='') return true;
        return false;
    }

    public function reset() {
        $this->nbmsg    = 0;
        $this->perreurs = array();
        $this->pinfos   = array();
        $this->pwarns   = array();
    }
    public function __construct() {
        $this->reset();

        $this->allys = DataEngine::config('EmpireAllys');
        $this->wars  = DataEngine::config('EmpireEnnemy');
        if (!is_array($this->allys) && $this->allys !='') $this->allys = array($this->allys);
        if (!is_array($this->wars)  && $this->wars  !='') $this->wars = array($this->wars);
        if (is_array($this->allys)) $this->allys = parser::getinstance()->cleaning_array($this->allys);
        if (is_array($this->wars )) $this->wars  = parser::getinstance()->cleaning_array($this->wars);
    }
    /**
     * @return cartographie
     */
    static public function getinstance() {
        if ( ! self::$instance )
            self::$instance = new self();

        return self::$instance;
    }

}
?>
