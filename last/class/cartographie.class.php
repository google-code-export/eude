<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
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
    private $lng;
    private $lngmain;

    protected $allys;
    protected $wars;

    /**
     * Ajoute/réactive un vortex dans la base
     * @param string $coordsin
     * @param string $coordsout
     * @return boolean
     */
    function add_vortex($coordsin, $coordsout) {
        if (!$this->FormatId(trim($coordsin) , $INidfixe , $INiddet ,'entrée vortex')) return false;
        if (!$this->FormatId(trim($coordsout), $OUTidfixe, $OUTiddet,'sortie vortex')) return false;

        $query = 'SELECT `ID` FROM `SQL_PREFIX_Coordonnee` where `POSIN`='.$INidfixe.' AND `POSOUT`=\''.$OUTidfixe.'\' AND `COORDET`=\''.$INiddet.'\' AND `COORDETOUT`=\''.$OUTiddet.'\'';
        $mysql_result = DataEngine::sql($query);
        if (mysql_num_rows($mysql_result) > 0) {
            return $this->AddWarn(sprintf($this->lng['class_vortex_msg2'],$coordsin,$coordsout));
        } else {
            $query1 = 'INSERT INTO `SQL_PREFIX_Coordonnee` (`TYPE`,`POSIN`,`COORDET`,`POSOUT`,`COORDETOUT`,`DATE`,`UTILISATEUR`) ';
            $query1 .= 'VALUES (1,\''.$INidfixe.'\',\''.$INiddet.'\',\''.$OUTidfixe.'\',\''.$OUTiddet.'\',now(),\''.$_SESSION['_login'].'\')';
            DataEngine::sql($query1);
            return $this->AddInfo(sprintf($this->lng['class_vortex_msg3'],$coordsin,$coordsout));
        }
    }

    /**
     * Ajoute/modifie une planète colonisable
     * @param string $coords
     * @param array $ress_val
     * @return boolean
     */
    public function add_planet($coords, $ress_val) {
        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLANETS'))
            return $this->AddErreur('Permissions manquante');

        $Ressource=DataEngine::a_Ressources();
        $warn='';

        if (!$this->FormatId(trim($coords), $uni, $sys, 'planet')) return false;

        $query = 'SELECT `ID` FROM `SQL_PREFIX_Coordonnee` where `POSIN`='.$uni.' AND `COORDET`=\''.$sys.'\'';
        $array = DataEngine::sql($query);
        $ligne = mysql_fetch_array($array);
        $do_update = intval($ligne['ID']);

        $sql=$insert_Val=$insert_field= '';

        foreach ($ress_val as $id => $value) {
            if (!is_numeric($id)) continue;
            if(!$this->Ressources_Check_Value($value, true)) {
                return $this->AddErreur(sprintf($this->lng['class_err_ress'],$Ressource[$id]['Nom'],$value));
            }

            $field = mysql_escape_string($Ressource[$id]['Field']);
            $newval = mysql_escape_string($value);
            if ($do_update) {
                if ($sql != '')
                    $sql   .= ', ';
                $sql .= '`'.$field.'`= \''.$newval.'\'';
                if ($sql2  != '')
                    $sql2    .= ', ';
                $sql2  .= '\''.$newval.'\'';
                if ($sql3	!= '')
                    $sql3  .= ', ';
                $sql3.=  '`'.$field.'`';
            } else {
                if ($insert_Val  != '')
                    $insert_Val    .= ', ';
                $insert_Val  .= '\''.$newval.'\'';
                if ($insert_field!= '')
                    $insert_field  .= ', ';
                $insert_field.=  '`'.$field.'`';
            }
        }

        if ($do_update) {
            $updated = 0;
            $query = 'UPDATE `SQL_PREFIX_Coordonnee` SET `DATE`=NOW(), `UTILISATEUR`=\''.$_SESSION['_login'].'\' WHERE `ID`='.$do_update;
            DataEngine::sql($query);
            $query = 'SELECT COUNT(`pID`) AS NOMBRE FROM `SQL_PREFIX_Coordonnee_Planetes` where `pID`='.$do_update;
            $mysql_result = DataEngine::sql($query);
			$ligne=mysql_fetch_assoc($mysql_result);
            if ($ligne['NOMBRE'] == 0) {
                $query2='INSERT INTO `SQL_PREFIX_Coordonnee_Planetes` (`pID`,'.$sql3.') VALUES('.$do_update.','.$sql2.')';
                DataEngine::sql($query2,false);
				return $this->AddInfo(sprintf($this->lng['class_planet_msg3'],$uni,$sys));
            } else {
                $query = DataEngine::sql('SELECT `Titane`, `Cuivre`, `Fer`, `Aluminium`, `Mercure`, `Silicium`, `Uranium`, `Krypton`, `Azote`, `Hydrogene` FROM `SQL_PREFIX_Coordonnee_Planetes` where `pID`='.$do_update);
                $ligne=mysql_fetch_assoc($query);
                $array = array($ligne['Titane'],$ligne['Cuivre'],$ligne['Fer'],$ligne['Aluminium'],$ligne['Mercure'],$ligne['Silicium'],$ligne['Uranium'],$ligne['Krypton'],$ligne['Azote'],$ligne['Hydrogene']);
                $array = str_replace('%', '', $array);
                if (is_numeric($array[0]) && is_numeric($array[1]) && is_numeric($array[2]) && is_numeric($array[3]) && is_numeric($array[4]) && is_numeric($array[5])
                        && is_numeric($array[6]) && is_numeric($array[7]) && is_numeric($array[8]) && is_numeric($array[9])) {
                    return $this->AddInfo(sprintf($this->lng['class_planet_msg1'],$uni,$sys));
                } else {
                    $query = 'UPDATE `SQL_PREFIX_Coordonnee_Planetes` SET '.$sql.' WHERE `pID`='.$do_update;
                    DataEngine::sql($query);
                    return $this->AddInfo(sprintf($this->lng['class_planet_msg2'],$uni,$sys));
                }
            }
        } else {
            $query    = 'INSERT INTO SQL_PREFIX_Coordonnee (`TYPE`,`POSIN`,`COORDET`,`NOTE`,`DATE`,`UTILISATEUR`) ';
            $query   .= 'VALUES (2,\''.$uni.'\',\''.$sys.'\',\''.$qnote.'\',now(),\''.$_SESSION['_login'].'\')';
            DataEngine::sql($query);
            $pID = mysql_insert_id();

            $query2='INSERT INTO `SQL_PREFIX_Coordonnee_Planetes` (`pID`,'.$insert_field.') VALUES(\''.$pID.'\','.$insert_Val.')';
            DataEngine::sql($query2,false);
			return $this->AddInfo(sprintf($this->lng['class_planet_msg3'],$uni,$sys));

            $query2='INSERT INTO `SQL_PREFIX_Coordonnee_Joueurs` (`jID`) VALUES('.$pID.')';
            DataEngine::sql($query2,false);

            if ($warn!='') {
                DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee` WHERE `ID`=\''.$pID.'\' LIMIT 1');
                return $this->AddErreur($warn);
            }

            return $this->AddInfo(sprintf($this->lng['class_planet_msg3'],$uni,$sys));
        }
    }

    /**
     * Ajoute/modifie un astéroïde
     * @param string $coords
     * @param array $ress_val
     * @return boolean
     */
    public function add_asteroid($coords, $ress_val) {
        if (!DataEngine::CheckPerms('CARTOGRAPHIE_ASTEROID'))
            return $this->AddErreur($this->lng['class_err_noaxx']);

        $Ressource=DataEngine::a_Ressources();
        $warn='';

        if (!$this->FormatId(trim($coords), $uni, $sys, 'asteroid')) return false;

        $query = 'SELECT `ID` FROM `SQL_PREFIX_Coordonnee` where `POSIN`='.$uni.' AND `COORDET`=\''.$sys.'\'';
        $array = DataEngine::sql($query);
        $ligne = mysql_fetch_array($array);
        $do_update = intval($ligne['ID']);

        $sql=$insert_Val=$insert_field= '';

        foreach ($ress_val as $id => $value) {
            if (!is_numeric($id)) continue;
            if(!$this->Ressources_Check_Value($value, false)) {
                return $this->AddErreur(sprintf($this->lng['class_err_ress'],$Ressource[$id]['Nom'],$value));
            }

            $field = mysql_escape_string($Ressource[$id]['Field']);
            $newval = mysql_escape_string($value);
            if ($do_update) {
                if ($sql != '')
                    $sql   .= ', ';
                $sql .= '`'.$field.'`= \''.$newval.'\'';
            } else {
                if ($insert_Val  != '')
                    $insert_Val    .= ', ';
                $insert_Val  .= '\''.$newval.'\'';
                if ($insert_field!= '')
                    $insert_field  .= ', ';
                $insert_field.=  '`'.$field.'`';
            }
        }

        if ($do_update) {
            $updated = 0;
            $query = 'UPDATE `SQL_PREFIX_Coordonnee` SET `DATE`=NOW(), `UTILISATEUR`=\''.$_SESSION['_login'].'\' WHERE `ID`='.$do_update;
            DataEngine::sql($query);
            $query = 'UPDATE `SQL_PREFIX_Coordonnee_Planetes` SET '.$sql.' WHERE `pID`='.$do_update;
            DataEngine::sql($query);
            return $this->AddInfo(sprintf($this->lng['class_asteroid_msg1'],$uni,$sys));

        } else {
            $query    = 'INSERT INTO `SQL_PREFIX_Coordonnee` (`TYPE`,`POSIN`,`COORDET`,`NOTE`,`DATE`,`UTILISATEUR`) ';
            $query   .= 'VALUES (4,'.$uni.',\''.$sys.'\',\''.$qnote.'\',now(),\''.$_SESSION['_login'].'\')';
            DataEngine::sql($query);
            $pID = mysql_insert_id();

            $query2='INSERT INTO `SQL_PREFIX_Coordonnee_Planetes` (`pID`,'.$insert_field.') VALUES('.$pID.','.$insert_Val.')';
            DataEngine::sql($query2,false);
			return $this->AddInfo(sprintf($this->lng['class_asteroid_msg2'],$uni,$sys));

            if ($warn!='') {
                DataEngine::sql('DELETE FROM `SQL_PREFIX_Coordonnee` WHERE `ID`='.$pID.' LIMIT 1');
                return $this->AddErreur($warn);
            }

            return $this->AddInfo(sprintf($this->lng['class_asteroid_msg2'],$uni,$sys));
        }
    }

    public function add_player($coords, $planete='', $nom='', $empire='') {
        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PLAYERS'))
            return $this->AddErreur($this->lng['class_err_noaxx']);

        $updatetype=true;

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

        $stype    = $this->lng['class_player_type'.$type];
        $qnom     = sqlesc(trim($nom));
        $qempire  = sqlesc(trim($empire));
        $qplanete = sqlesc(trim($planete));

        if (!$this->FormatId(trim($coords), $uni, $sys,'')) return false;

        if ($nom=='') {
            $query = 'UPDATE `SQL_PREFIX_Coordonnee`, `SQL_PREFIX_Coordonnee_Joueurs` SET `Type`=2, `USER`=\'\', `EMPIRE`=\'\', `INFOS`=\'\', `batiments`=NULL, `troop`=NULL where `Type` in (0,3,5) AND `POSIN`='.$uni.' AND `COORDET`=\''.$sys.'\'';
            $array = DataEngine::sql($query);
            if (mysql_affected_rows() > 0)
                return $this->AddWarn(sprintf($this->lng['class_player_msg1'],$coords));
        }
        $query = 'SELECT `ID`, `TYPE` FROM `SQL_PREFIX_Coordonnee` where `POSIN`='.$uni.' AND `COORDET`=\''.$sys.'\'';
        $array = DataEngine::sql($query);
        $ligne = mysql_fetch_assoc($array);
        if($ligne['ID'] > 0) {
            if (!$updatetype) $type = $ligne['TYPE'];
            if (!$updatetype && $ligne['TYPE'] == 2) $type = 0;
            
            $query = sprintf('UPDATE `SQL_PREFIX_Coordonnee`, `SQL_PREFIX_Coordonnee_Joueurs` SET `TYPE`=%d, `POSOUT`=\'\', `COORDETOUT`=\'\', `USER`=\'%s\', `EMPIRE`=\'%s\','.
                    '`INFOS`=\'%s\', `UTILISATEUR`=\'%s\', `DATE`=NOW() WHERE `ID`=%s AND `jID`=%6$s',
                    $type, $qnom, $qempire, $qplanete, sqlesc($_SESSION['_login']), $ligne['ID'] );
            DataEngine::sql($query);

            if (mysql_affected_rows() > 0)
                return $this->AddInfo(sprintf($this->lng['class_player_msg2'],$stype,$nom,$uni,$sys));
            else
                return $this->AddInfo(sprintf($this->lng['class_player_msg3'],$stype,$nom,$uni,$sys));
        } else {
            $query = sprintf('INSERT INTO `SQL_PREFIX_Coordonnee` (`TYPE`,`POSIN`,`COORDET`,`DATE`,`UTILISATEUR`)'.
                    ' VALUES (%d,\'%s\',\'%s\',now(),\'%s\')',
                    $type, $uni, $sys, sqlesc($_SESSION['_login']));
            DataEngine::sql($query);
            $id = mysql_insert_id();

            $query = sprintf('INSERT INTO `SQL_PREFIX_Coordonnee_Joueurs` (`jID`,`USER`,`EMPIRE`,`INFOS`)'.
                    ' VALUES (%d,\'%s\',\'%s\',\'%s\')',
                    $id, $qnom, $qempire, $qplanete);
            DataEngine::sql($query);

            return $this->AddInfo(sprintf($this->lng['class_player_msg4'],$stype,$nom,$uni,$sys));
        }
    }


    public function add_PNJ($coords, $nom='', $fleet='') {
        if (!DataEngine::CheckPerms('CARTOGRAPHIE_PNJ'))
            return $this->AddErreur($this->lng['class_err_noaxx']);

        $qnom     = sqlesc(trim($nom));
        $qfleet  = sqlesc(trim($fleet));

        if (!$this->FormatId(trim($coords), $uni, $sys,'NPC')) return false;

        $query = 'SELECT `ID`, `TYPE` FROM `SQL_PREFIX_Coordonnee` where `POSIN`=\''.$uni.'\' AND `COORDET`=\''.$sys.'\'';
        $array = DataEngine::sql($query);
        $ligne = mysql_fetch_assoc($array);
        if($ligne['ID'] > 0) {
            $query = sprintf('UPDATE `SQL_PREFIX_Coordonnee`, `SQL_PREFIX_Coordonnee_Joueurs` SET `TYPE`=6, `USER`=\'%s\', `INFOS`=\'%s\','.
                    '`UTILISATEUR`=\'%s\' WHERE `ID`=%s',
                    $qnom, $qfleet, sqlesc($_SESSION['_login']), $ligne['ID'] );

            DataEngine::sql($query);
            if (mysql_affected_rows() > 0)
                return $this->AddInfo(sprintf($this->lng['class_npc_msg1'],$nom,$uni,$sys));
            else
                return $this->AddInfo(sprintf($this->lng['class_npc_msg2'],$nom,$uni,$sys));
        } else {
            $query = sprintf('INSERT INTO `SQL_PREFIX_Coordonnee` (`TYPE`,`POSIN`,`COORDET`,`DATE`,`UTILISATEUR`)'.
                    ' VALUES (6,%d,\'%s\',now(),\'%s\')',
                    $uni, $sys, sqlesc($_SESSION['_login']));
            DataEngine::sql($query);
            $id = mysql_insert_id();
            $query = sprintf('INSERT INTO `SQL_PREFIX_Coordonnee_Joueurs` (`jID`,`USER`,`INFOS`)'.
                    ' VALUES (%d,\'%s\',\'%s\')',
                    $id, $qnom, $qfleet);
            DataEngine::sql($query);
            return $this->AddInfo(sprintf($this->lng['class_npc_msg3'],$nom,$uni,$sys));
        }
    }

    /**
     *  @param string	Données contenant tout (+/- brut)...
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
            if (!$cur_ss) if (!$this->FormatId($coords,$cur_ss,$dummy,'')) $cur_ss = false;
            if ( trim($lignes[$i+1]) != '') { // Empire ?
                list($empire) = explode($sep, trim($lignes[$i+1]));
                $empire = trim($empire);
                if (!$this->FormatId($empire,$dummy,$dummy,'')) { // n'est pas une coords
                    $SS_A[$i] = array($coords, $planete, $nom, $empire); // Joueur avec empire
                    $i++;
                } elseif ($nom != '')
                    $SS_A[$i] = array($coords, $planete, $nom, ''); // Joueur sans empire
            } elseif ($nom != '') {
                $SS_A[$i] = array($coords, $planete, $nom, ''); // Joueur sans empire
            }
            if ($nom == '' && $this->FormatId($coords, $dummy, $sys,'')) // Planète inoccupée
                $del_planet[] = $sys;
        }

        if (count($del_planet)>0) {
            $del_planet = ''.implode("','",$del_planet).'';
            $query = <<<sql
UPDATE `SQL_PREFIX_Coordonnee` c, `SQL_PREFIX_Coordonnee_Joueurs` j, `SQL_PREFIX_Coordonnee_Planetes` p
SET `Type`=2, `USER`='', `EMPIRE`='', `INFOS`='', `batiments`=NULL, `troop`=NULL
WHERE `Type` in (0,3,5) AND `POSIN`=$cur_ss AND `COORDET` in ('$del_planet')
sql;

            $array = DataEngine::sql($query);
            if ( ($num = mysql_affected_rows()) > 0)
                $this->AddInfo(sprintf($this->lng['class_solar_msg1'],$num,$cur_ss));
        }

        foreach($SS_A as $v)
            $this->add_player($v);
// //-- Partie changement d'empire obsolète?...
//        $query = 'SELECT `USER`, `EMPIRE` FROM `SQL_PREFIX_Coordonnee` where `POSIN`=\''.$cur_ss.'\' AND `TYPE` in (0,3,5)';
//        $sql_result = DataEngine::sql($query);
//        while ($row = mysql_fetch_assoc($sql_result))
//        // par nom de joueur
//            $curss_info[$row['USER']] = $row['EMPIRE'];
//
//        foreach($SS_A as $v) {
//            $result = $this->add_player($v);
//            if ($result) { // uniquement si changement, vide autrement.
//                list($dummy, $dummy, $nom, $empire) = $v;
//                if (isset($curss_info[$nom])) {
//                    if ($curss_info[$nom] != $empire) {
//                        $qnom    = sqlesc($nom);
//                        $qempire = sqlesc($empire);
//                        $query = 'UPDATE `SQL_PREFIX_Coordonnee` SET `EMPIRE`=\''.$qempire.'\', `UTILISATEUR`=\''.$_SESSION['_login'].'\', `DATE`=now() WHERE `USER`=\''.$qnom.'\'';
//                        DataEngine::sql($query);
//                        $this->AddInfo(sprintf($this->lng['class_solar_msg2'],$nom));
//                        unset($curss_info[$nom]);
//                    }
//                }
//            }
//        }
    }

    /**
     * @param integer/string $ident entry key (by id or posin)
     * @param array $data key = sql field, value = value!
     * @param string [Optional] Message d'infomation personalisable
     * @param mixed [Optional] paramètre<b>s</b> a inclure dans le formatage
     * @return boolean
     */
    public function Edit_Entry($ident, $data) {
        $where = array();

        if ($this->FormatId($ident,$sys,$det,'')) {
            $where[] = '`POSIN`=\''.$sys.'\'';
            $where[] = '`COORDET`=\''.$det.'\'';
        } else
            $where[] = '`ID`=\''.$ident.'\'';

        $where = implode(' AND ',$where);

        $query = 'SELECT `TYPE`, `POSIN`, `COORDET`, `USER` FROM `SQL_PREFIX_Coordonnee` c, `SQL_PREFIX_Coordonnee_Joueurs` j WHERE c.`id`=j.`jID` AND '.$where;
        $sql_result = DataEngine::sql($query);
        if (mysql_num_rows($sql_result)==0)
            return $this->AddErreur (sprintf($this->lng['class_delete_nofound'],$ident));

        $item = mysql_fetch_assoc($sql_result);

        $value = array();
        foreach ($data as $k => $v) {
            if (preg_match('/[^a-zA-Z_]+/', $k) > 0)
                return $this->AddErreur('fatal: $key syntax invalid');
            if ($k == 'TROOP' && $v == -1)
                $value[] = sprintf('`%s`= NULL', $k, sqlesc($v));
            else
                $value[] = sprintf('`%s`=\'%s\'', $k, sqlesc($v));
        }
        if ($data['TROOP']>0) $value[] = '`troop_date`=now()';
        if ($data['TROOP']<=0) $value[] = '`troop_date`=0';

        $value = implode(',',$value);
        $query = sprintf('UPDATE `SQL_PREFIX_Coordonnee`, `SQL_PREFIX_Coordonnee_Joueurs`, `SQL_PREFIX_Coordonnee_Planetes` SET %s,`UTILISATEUR`=\'%s\',`DATE`=now() WHERE id=pID AND id=jID AND %s',
                $value, $_SESSION['_login'], $where);
        $sql_result = DataEngine::sql($query);

        $msg = $this->lng['class_edit_defmsg'];
        if (func_num_args()>=3) {
            $amsg = func_get_args();
            $msg  = $amsg[2] != '' ? $amsg[2]: $msg;
            array_shift($amsg);
            array_shift($amsg);
            array_shift($amsg);
            array_unshift($amsg, $item['POSIN'].'-'.$item['COORDET']);
            array_unshift($amsg, $item['USER']);
            array_unshift($amsg, $this->lngmain['types']['string'][$item['TYPE']]);
        } else {
            $amsg = array($this->lngmain['types']['string'][$item['TYPE']],
                    $item['USER'],
                    $item['POSIN'].'-'.$item['COORDET']);
        }
        return $this->AddInfo(vsprintf($msg, $amsg));
    }

    public function Delete_Entry($ident,$type) {

        $where = '`ID`='.$ident;
        $where2 = '`jID`='.$ident;
        $where3 = '`pID`='.$ident;

        $query = 'DELETE FROM `SQL_PREFIX_Coordonnee` WHERE '.$where;
        $sql_result = DataEngine::sql($query);
        if (mysql_affected_rows()==0) return $this->AddErreur(sprintf($this->lng['class_delete_nofound'],$ident));

        $query = 'DELETE FROM `SQL_PREFIX_Coordonnee_Joueurs` WHERE '.$where2;
        $sql_result = DataEngine::sql($query);

        $query = 'DELETE FROM `SQL_PREFIX_Coordonnee_Planetes` WHERE '.$where3;
        $sql_result = DataEngine::sql($query);

        $this->AddInfo(sprintf($this->lng['class_delete_msg'], $this->lngmain['types']['string'][$type],$ident));
        return true;
    }

// --- Routines... -------------------------------------------------------------
    public function Boink($url) {
        if ($this->Infos()!='')   output::Messager($this->Infos());
        if ($this->Warns()!='')   output::Messager($this->Warns());
        if ($this->Erreurs()!='') output::Messager($this->Erreurs());

        if ($url!='') output::Boink($url);
    }
    /**
     *
     * @param string $id ssss-xx-yy-zz / ssss:xx:yy:zz
     * @param string &$idsys sss
     * @param string &$iddet xx-yy-zz
     * @param string $part complément affiché en cas d'erreur
     * @return boolean
     */
    public function FormatId($id,&$idsys,&$iddet,$part) {
        $tmppos = str_replace(':','-',$id);
        $tmppos = explode('-',$tmppos);

        if (count($tmppos) != 4) {
            if ($part=='')
                return false;
            else
                return $this->AddErreur(sprintf($this->lng['class_err_coords'],$part));
        }
        if ((!is_numeric($tmppos[0]) || !is_numeric($tmppos[1]) || !is_numeric($tmppos[2]) || !is_numeric($tmppos[3]))) {
            if ($part=='')
                return false;
            else
                return $this->AddErreur(sprintf($this->lng['class_err_coords'],$part));
        }

        $idsys = $tmppos[0];
        $iddet = intval($tmppos[1]).'-'.intval($tmppos[2]).'-'.intval($tmppos[3]);

        return true;
    }
    private function Ressources_Check_Value(&$value, $checkpercent) {
        if(is_numeric($value) && $value > 0) return true;
        switch (mb_strtolower($value, 'utf8')) {
            case $this->lng['ress10%']:
            case $this->lng['ress20%']:
            case $this->lng['ress40%']:
            case $this->lng['ress50%']:
            case $this->lng['ress70%']:
            case $this->lng['ress80%']:
            case $this->lng['ress90%']:
                return true;
        }
        if ($checkpercent) {
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
            return implode('<br/>', $this->pinfos);
//            return '<font color="green">'.implode('<br/>', $this->pinfos).'</font>';
        else
            return '';
    }
    public function Warns() {
        if (count($this->pwarns)>0)
            return '<font color="darkorange">'.implode('<br/>', $this->pwarns).'</font>';
        else
            return '';
    }
    public function reset() {
        $this->nbmsg    = 0;
        $this->perreurs = array();
        $this->pinfos   = array();
        $this->pwarns   = array();
    }
    public function __construct() {
        $this->reset();
        $this->lng = language::getinstance()->GetLngBlock('cartographie');
        $this->lngmain = language::getinstance()->GetLngBlock('dataengine');

        $this->allys = DataEngine::config('EmpireAllys');
        $this->wars  = DataEngine::config('EmpireEnnemy');
        if (!is_array($this->allys) && $this->allys !='') $this->allys = array($this->allys);
        if (!is_array($this->wars)  && $this->wars  !='') $this->wars = array($this->wars);
        if (!is_array($this->allys) && $this->allys =='') $this->allys = array();
        if (!is_array($this->wars)  && $this->wars  =='') $this->wars = array();
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