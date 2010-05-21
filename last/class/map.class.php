<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/

class parcours {
    protected $allfoundparcours = array();
    protected $allvortex = array();
    protected $mindist, $howmany, $distance, $maxtry, $nbvortex;
    protected $initialised=false;
    protected $map;
    public function _init($IN,$OUT) {

        $this->map = map::getinstance();
        //Chargement d'un liste de tous les vortex actif ou non...
        $sql = 'SELECT ID,POSIN,POSOUT,COORDET,COORDETOUT from SQL_PREFIX_Coordonnee where Type=1';
        if (!$this->map->inactif) $sql .= ' and Inactif=0';

        // @since 1.4.1
        if ($this->map->method == 10) {
            $max_dist = DataEngine::config_key('config', 'Parcours_Nearest')+1;
            $limit = array_merge(
                    $this->GetListeCoorByRay($IN , $max_dist),
                    $this->GetListeCoorByRay($OUT, $max_dist)
            );
            $limit = implode (',',$limit);
            $sql .= " AND (POSIN in ($limit) OR POSOUT in ($limit) )";
        }

        $mysql_result = DataEngine::sql($sql);
        $i = 0;
        while($line=mysql_fetch_array($mysql_result, MYSQL_ASSOC)) {
            $tabvortex[$i]['ID'] = $line['ID'];
            $tabvortex[$i]['IN'] = $line['POSIN'];
            $tabvortex[$i]['OUT'] = $line['POSOUT'];
            $tabvortex[$i]['INDET'] = $line['COORDET'];
            $tabvortex[$i]['OUTDET'] = $line['COORDETOUT'];
            $i++;
            $tabvortex[$i]['ID'] = $line['ID'];
            $tabvortex[$i]['IN'] = $line['POSOUT'];
            $tabvortex[$i]['OUT'] = $line['POSIN'];
            $tabvortex[$i]['INDET'] = $line['COORDETOUT'];
            $tabvortex[$i]['OUTDET'] = $line['COORDET'];
            $i++;
        }
        mysql_free_result($mysql_result);
        $this->allvortex = isset($tabvortex) ? $tabvortex: null;
    }

    public function Do_Parcours($IN,$OUT) {
        // initialisation...
        if (!isset($initialised)) $this->_init($IN,$OUT);
        $this->IN = $IN;
        $this->OUT = $OUT;
        $this->allfoundparcours = array();
        $this->distance = $this->mindist = $this->Calcul_Distance($IN,$OUT);
        if (IN_DEV) $this->howmany  = 0;
        if (DataEngine::config_key('config', 'Parcours_Max_Time')==0)
            $this->maxtry   = (ini_get('max_execution_time')-1);
        else
            $this->maxtry   = DataEngine::config_key('config', 'Parcours_Max_Time');

        $this->nbvortex = min($this->map->method-1,2);
        //        $this->nbvortex = Config::Parcours_Max_Vortex()-1;
        FB::warn($this->nbvortex, 'nbvortex');
        if (!is_array($this->allvortex))
            return array($this->distance, array($IN, $OUT));

        $this->_Parcours($IN,'',0,array(), array(),0);

        $parcours = false;
        foreach ($this->allfoundparcours as $k => $v ) {
            if (!$parcours) {
                $parcours = $v;
                $kv = $k;
                continue;
            }
            if (count($parcours[1])>count($v[1])) {
                $parcours = $v;
                if (IN_DEV) $kv = $k;
            }
        }

        if (IN_DEV) {
            FB::info($this->howmany-1,'Nombre de combinaisons rejeté');
            FB::info($kv,'ID de la combinaison retenue');
            FB::info($this->maxtry, 'Timing max');
            FB::info((microtime(true)-START), 'Timing sec');
            //FB::info($parcours,'Parcours');
        }
        return $parcours;
    }

    protected function _Parcours($ss_pos,$ss_det,$cur_dist,$cur_parcours,$skip,$recur) {

        if ($ss_det=='')
            array_push($cur_parcours, $ss_pos);
        else
            array_push($cur_parcours, $ss_pos.'-'.$ss_det);

        // tester si l'arrivée est possible sans dépassement de distance max...
        if ( (($D1 = $this->Calcul_Distance($ss_pos, $this->OUT))+$cur_dist) <= $this->mindist )
            if (!$this->_Parcours_found($cur_dist+$D1,$cur_parcours)) return false;

        if((microtime(true)-START) >= $this->maxtry) return false;

        if ($recur > $this->nbvortex) return false;

        foreach ($this->allvortex as $v) {
            if (array_search($v['ID'], $skip) !==false) continue;
            $D1 = $this->Calcul_Distance($ss_pos, $v['IN']);
            if ( (($D1+$cur_dist) > $this->mindist)    ) continue;
            if ( $this->map->nointrass && $D1 == 0)      continue;

            $new_dist     = $D1+$cur_dist;
            $new_parcours = $cur_parcours;
            $new_skip     = $skip;
            array_push($new_parcours, $v['IN'].'-'.$v['INDET']);
            array_push($new_skip, $v['ID']);
            $this->_Parcours($v['OUT'], $v['OUTDET'], $new_dist, $new_parcours, $new_skip,$recur+1);
        }
        return true;
    }

    protected function _Parcours_found($dist,$parcours) {
        if (IN_DEV) $this->howmany++;

        if ($dist > $this->mindist) return false;

        array_push($parcours, $this->OUT);
        array_push($this->allfoundparcours, array($dist,$parcours));
        $this->mindist = $dist;
        if (count($this->allfoundparcours)>1)
            foreach ($this->allfoundparcours as $k => $parcours)
                if ($parcours[0] > $dist) unset($this->allfoundparcours[$k]);

        return true;
    }

    /**
     *  @param string SS 1
     *  @param string SS 2
     *  @return distance en pc
     **/
    public function Calcul_Distance ($a,$b) {
        $X = abs( (floor(($a-1)/100)+1) - (floor(($b-1)/100)+1) );
        //        $X = abs( floor(((($a-1)/100)+1) - ((($b-1)/100)+1)) );
        $Y = abs( ((($a-1)%100)+1) - ((($b-1)%100)+1) );
        return round( sqrt(pow($X,2) + pow($Y,2)));
    }
    /**
     *  @param string Vortex (SS+det)
     *  @param boolean search for ?
     *  @return string SS ou 'det'
     **/
    public function get_coords_part($ssdet, $ss=true) {
        $pos = strpos($ssdet,'-');
        if ($ss)
            if ($pos===false)
                return $ssdet;
            else
                return substr($ssdet,0,$pos);
        else
            return substr($ssdet,$pos+1);
    }
    public function GetListeCoorByRay($SS,$max_dist) {
        // 4890 = 90:48
        // 1 = 0001 = 1: 0
        // 10000 = 100: 0
        list($x,$y) = map::ss2xy($SS); // Warn: x/y s'inverse ;)
        $lst = array();

        for ($a = (0-$max_dist); $a <= (0+$max_dist); $a++) {
            for ($b = (0-$max_dist); $b <= (0+$max_dist); $b++) {
                $y1 = ($b+$y);
                $x1 = ($a+$x);
                $out = ($y1*100)+$x1;
                if ($y1 < 0 || $y1 > 99)
                    continue;
                if ($x1 < 1 || $x1 > 100)
                    continue;
                if ($x1 == 100) $x1 = 0;

                $out = ($y1*100)+$x1;
                if ($this->Calcul_Distance($SS, $out) <$max_dist)
                    $lst[] = $out;
            }
        }
        return $lst;
    }
}

//-----------------------------------------------------------------------------------------------

class map /*extends parcours*/ {
    public $TabData = array();
    public $vortex,$joueur,$planete,$asteroide,$sc,$taille,$pnj,$ennemis,$allys,$IN,$OUT,$loadfleet,$itineraire,$inactif,$parcours;
    public $nointrass;
    /**
     * @since 1.4.1
     */
    public $method;
    private $empire;
    private $cxx_empires; // -> #86 ~265
    /**
     * @since 1.4.2
     */
    public $lng;
    /**
     * @var parcours
     */
    protected $cparcours;

    static private $instance;

    public function __construct() {
        if ( isset($_SESSION['carte_prefs']) && $_SESSION['carte_prefs'] != '' )
            $this->load_prefs($_SESSION['carte_prefs']);
        else
            $this->load_prefs('1;1;1;1;0;600;1;1;1');

        $this->parcours		= '';

        if(IS_IMG) {
            $this->inactif		= $_SESSION['inactif'];
            $this->IN			= $_SESSION['IN'];
            $this->OUT			= $_SESSION['OUT'];
            $this->loadfleet	= (isset($_SESSION['loadfleet']) ? $_SESSION['loadfleet']:'');
            $this->parcours		= $_SESSION['parcours'];
        } else {
            if (isset($_POST['coorin'])) $this->IN = intval($_POST['coorin']); else $this->IN = 0;
            if (isset($_POST['coorout'])) $this->OUT = intval($_POST['coorout']); else $this->OUT = 0;
            if ($this->IN < 1  || $this->IN > 10000 ) $this->IN  = '';
            if ($this->OUT < 1 || $this->OUT > 10000) $this->OUT = '';
            $this->loadfleet = (isset($_REQUEST['loadfleet']) and intval($_REQUEST['loadfleet']) > 0) ? intval($_REQUEST['loadfleet']): 0;
            $this->method = (isset($_REQUEST['method']) and intval($_REQUEST['method']) > 0) ? intval($_REQUEST['method']): 2;
            $this->inactif		= ( isset($_POST['inactif']) 	&& $_POST['inactif'] > 0 ) 	? true: false;
            $this->nointrass		= ( isset($_POST['nointrass']) 	&& $_POST['nointrass'] > 0 ) 	? true: false;
            $this->update_session();

            FB::warn($this->method,'method');
        }

        // mise en variable, plus rapide que 36 call function
        $this->empire = trim(DataEngine::config_key('config', 'MyEmpire'));
        $this->cxx_empires = DataEngine::CheckPerms('CARTE_SHOWEMPIRE');
        $this->lng = language::getinstance()->GetLngBlock('carte');

        $this->itineraire = ( ($this->IN != '' && $this->OUT != '') && ($this->IN != $this->OUT) );

    }

    public function load_prefs($value) {
        $tmp = explode(';',$value);
        $this->vortex		= $tmp[0];
        $this->joueur		= $tmp[1];
        $this->planete		= $tmp[2];
        $this->asteroide	= $tmp[3];
        $this->sc		= $tmp[4];
        $this->taille		= $tmp[5];
        $this->pnj		= $tmp[6];
        $this->ennemis		= $tmp[7];
        $this->allys		= $tmp[8];

        $this->perms_prefs();
    }

    public function save_prefs() {
        $this->perms_prefs();

        $tmp = implode(';',
                array(	$this->vortex,
                $this->joueur,
                $this->planete,
                $this->asteroide,
                $this->sc,
                $this->taille,
                $this->pnj,
                $this->ennemis,
                $this->allys
                )
        );
        if ($_SESSION['carte_prefs'] != $tmp || $_SESSION['carte_prefs'] == '')
            DataEngine::sql_spool('UPDATE SQL_PREFIX_Membres SET carte_prefs=\''.$tmp.'\' WHERE Joueur=\''.$_SESSION['_login'].'\'');
    }

    private function perms_prefs() {
        if (!DataEngine::CheckPerms('CARTE_JOUEUR')) $this->joueur = 0;

    }

    public function update_session() {
        $_SESSION['IN'] = $this->IN;
        $_SESSION['OUT'] = $this->OUT;
        $_SESSION['parcours'] = $this->parcours;
        $_SESSION['inactif'] = $this->inactif;
    }

    /**
     * @return parcours
     */
    public function Parcours() {
        if (!isset ($this->cparcours)) $this->cparcours = new parcours();

        return $this->cparcours;
    }

    public function Parcours_loadfleet() {

        if ($this->loadfleet==0) return false;
        $mysql_result = DataEngine::sql("SELECT Start,End,Flotte from SQL_PREFIX_itineraire where ID='".$this->loadfleet."' AND Joueur='".$_SESSION["_login"]."'");
        if (mysql_num_rows($mysql_result) > 0) {
            $ligne			= mysql_fetch_array($mysql_result, MYSQL_ASSOC);
            $this->IN		= $ligne['Start'];
            $this->OUT	= $ligne['End'];
            $this->itineraire		= true;
        }
        mysql_free_result($mysql_result);
        return $ligne['Flotte'];
    }

    public function init_map($custom='') {
        // init ici
        global $vortex_a, $CurrSS, $CurrSS_a;
        $this->Parcours();

        // variables
        $vortex_a = array();
        $CurrSS = 0;
        $CurrSS_a = array();

        /// RÉCUPÉRATION DES VORTEX (POSOUT) ///
        if ($this->vortex) {
            $where = ( ($this->inactif) ? '':' AND INACTIF=0 ' );
            $sql = 'SELECT ID,POSIN,POSOUT from SQL_PREFIX_Coordonnee where Type=1'.$where;
            $mysql_result = DataEngine::sql($sql);
            while($line=mysql_fetch_assoc($mysql_result)) {
                $vortex_a[$line['POSOUT']][$line['ID']]['POSIN'] = $line['POSOUT'];
                $vortex_a[$line['POSOUT']][$line['ID']]['POSOUT'] = $line['POSIN'];
                $vortex_a[$line['POSOUT']][$line['ID']]['TYPE'] = 1;
            }
            mysql_free_result($mysql_result);
        }

        /// TRAITEMENT DES CORPS CÉLESTES ///

        /// filtre spécial...
        $if = array();
        if (!$this->ennemis && !$this->allys)   $if[] = 'IF(a.TYPE in (3,5), 0, a.TYPE) as TYPE,';
        if (!$this->ennemis && $this->allys)    $if[] = 'IF(a.TYPE=5, 0, a.TYPE) as TYPE,';
        if ($this->ennemis && !$this->allys)    $if[] = 'IF(a.TYPE=3, 0, a.TYPE) as TYPE,';

        /// filtre in type:
        $in = array();
        if ($this->joueur)			$in[] = 0;
        if ($this->vortex)			$in[] = 1;
        if ($this->planete)			$in[] = 2;
        if ($this->allys || $this->joueur)      $in[] = 3;
        if ($this->asteroide)                   $in[] = 4;
        if ($this->pnj)				$in[] = 6;
        if ($this->ennemis || $this->joueur)	$in[] = 5;
        if (count($in)>0 && count($in)<7)
            $in = 'AND ( `TYPE` IN ('.implode(',',$in).')';
        else
            $in = '';
        /// filtre au cas par cas:
        $cas = array();
        if (!$this->joueur)		$cas[] = '(Type=0 AND b.Joueur=\''.$_SESSION['_login'].'\')';

        // compilation des filtres
        $if = ' '.trim(implode(' ', $if));
        if (count($cas) >0) {
            if ($in != '')
                $where = $in.' OR '.implode(' OR ', $cas).' ) ';
            else
                $where = ' AND '.implode(' OR ', $cas).' ';
        } else {
            if ($in != '')
                $where = $in.' ) ';
        }
        $where = 'WHERE '.$custom. ( ($this->inactif) ? '1=1 ':'INACTIF=0 ' ) . $where;

        $where = $where." ORDER BY POSIN ASC";
        $sql='SELECT a.*,'.$if.' IFNULL(b.Joueur,"") as Joueur,IFNULL(c.Grade,"") as Grade FROM SQL_PREFIX_Coordonnee a left outer join SQL_PREFIX_Membres b on (a.USER=b.Joueur) left outer join SQL_PREFIX_Grade c on (b.Grade=c.GradeId) '.$where;
        $mysql_result = DataEngine::sql($sql);

        return $mysql_result;
    }


    public function add_ss($force_ss=false, $callback=NULL) {
        global $vortex_a, $CurrSS, $CurrSS_a, $Parcours;

        if ($force_ss) {
            if(!is_numeric($force_ss)) trigger_error('$force_ss on map::add_ss('.print_r($force_ss,true).')',E_USER_ERROR);
            $CurrSS = $force_ss;
        }

        // Ajout des vortex présents...
        if (isset($vortex_a[$CurrSS]) && is_array($vortex_a[$CurrSS])) {
            foreach($vortex_a[$CurrSS] as $k => $v) {
                $CurrSS_a[$k] = $v;
                $CurrSS_a[$k]['type'] = 'Vortex';
                $CurrSS_a['Vortex'] = isset($CurrSS_a['Vortex']) ? $CurrSS_a['Vortex']++: 1;
            }
            unset($vortex_a[$CurrSS]); // destruction du vortex...
        }

        if ($this->itineraire && is_array($this->parcours)) { // Mode itinéraire...
            $last = count($this->parcours[1])-1;
            foreach($this->parcours[1] as $k => $v) {
                if ( $CurrSS == $this->cparcours->get_coords_part($v) ) {
                    switch ($k) {
                        case 0: $CurrSS_a['Chemin'] = 1;
                            continue;		// départ
                        case $last: $CurrSS_a['Chemin'] = 3;
                            continue;	// arrivée
                        default: $CurrSS_a['Chemin'] = 2;
                            continue;
                            ;	// départ vortex
                    }
                }
            }
        }

        $callback($CurrSS, $CurrSS_a);

        $CurrSS_a = array();
    }

    /**
     *  @@ ID = aabb
     *  @@  X = bb
     *  @@  Y = aa
     */
    static public function ss2xy($ID) {
        $Y= floor(($ID-1) / 100);
        $X= (($ID-1)      % 100)+1;
        $X= ($X == 0) ? 100: $X;
        return array($X,$Y);
    }

    /**
     * @param		mixte				Debug text
     * @param		boolean			New line before
     * @return	boolean
     */
    static public function map_debug($text, $newline=true) {
        if (!IN_DEV) return false;
        global $image, $debug_cl;
        static $x=1, $y=-15, $map;

        if (!$map) $map = map::getinstance();

        if (DEBUG_PLAIN) return print_r($text);
        if (!IS_IMG) return false;
        if (!isset($image)) return false;
        if (is_array($text)) $text = str_replace("\t\t", '', print_r($text, true));
        if ($newline) {
            $x=1;
            $y+=15;
        }

        $text = utf8_decode($text);
        for ($i=0; $i<strlen($text); $i++) {
            $c = substr($text,$i,1);
            if ( (($x-10) >= ($map->taille-15)) or ($c == "\n") ) {
                $x=1;
                $y+=15;
            }
            if ($c == "\n") continue;
            imagechar($image, 4, $x, $y, $c, $debug_cl);
            $x += 10;
        }
        return true;
    }

    /**
     * @param		array			Corps céleste
     * @return	string		Type du corps céleste (texte)
     */
    public function ss_type($line) {
        switch ($line['TYPE']) {
            case 0:  $type =  'Joueur';
                break;
            case 1:  $type =  'Vortex';
                break;
            case 2:  $type =  'Planète';
                break;
            case 3:  $type =  'alliance';
                break;
            case 4:  $type =  'Astéroïde';
                break;
            case 5:  $type =  'Ennemi';
                break;
            case 6:  $type =  'pnj';
                break;
            default: $type =  'na';
        }

        if( stristr($line['Joueur'], $_SESSION['_login']) !== FALSE ) return 'moi';

        if ($_SESSION['emp'] != '')
            if( stristr($line['EMPIRE'], $_SESSION['emp']) !== FALSE) return 'search';
        if ($_SESSION['jou'] != '')
            if( stristr($line['USER'], $_SESSION['jou']) !== FALSE) return 'search';
        if ($_SESSION['inf'] != '')
            if( stristr($line['INFOS'], $_SESSION['inf']) !== FALSE) return 'search';

        if( $this->empire != '' && $line['EMPIRE'] == $this->empire &&
                $this->cxx_empires) return 'empire';

        return $type;
    }

    /**
     * @param		array			Système solaire complet
     * @return	integer		ID du tableau de couleur
     */
    public function ss_colors($ss) {

        if (isset($ss['alliance'])) xdebug_break();

        $joueur		= ( isset( $ss['Joueur'] ))		? intval($ss['Joueur'])		: 0;
        $vortex		= ( isset( $ss['Vortex'] ))		? intval($ss['Vortex'])		: 0;
        $planete	= ( isset( $ss['Planète'] ))	? intval($ss['Planète'])	: 0;
        $asteroide	= ( isset( $ss['Astéroïde'] ))	? intval($ss['Astéroïde'])	: 0;
        $ennemi		= ( isset( $ss['Ennemi'] ))		? intval($ss['Ennemi'])		: 0;
        $pnj		= ( isset( $ss['pnj'] ))		? intval($ss['pnj'])		: 0;

        $empire		= ( isset( $ss['empire'] ))		? intval($ss['empire'])		: 0;
        $alliance	= ( isset( $ss['alliance'] ))		? intval($ss['alliance'])	: 0;
        $search		= ( isset( $ss['search'] ))		? intval($ss['search'])		: 0;
        $moi		= ( isset( $ss['moi'] ))		? intval($ss['moi'])		: 0;

        $chemin		= ( isset( $ss['Chemin'] ))		? intval($ss['Chemin'])		: 0;
        $total		= 0;
        if($joueur>0) $total++;
        if($vortex>0) $total++;
        if($planete>0) $total++;
        if($asteroide>0) $total++;

        $result = -1;

        if ($joueur>0||$planete>0)  $result = 3;
        if ($asteroide>0)           $result = 5;
        if ($vortex>0)              $result = 4;

        if ($moi>0 && $total>1)     $result = 6;
        if ($empire>0 && $total>1)  $result = 7;
        if ($pnj>0)                 $result = 9;

        if ($alliance>0)            $result = 11;
        if ($ennemi>0)              $result = 8;
        if ($empire>0)              $result = 1;
        if ($search>0)              $result = 10;
        if ($moi>0)                 $result = 2;

        if ($this->itineraire) {
            $result = 1;
            if ($moi>0)             $result = 2;
            if ($chemin==1)         $result = 20;
            if ($chemin==3)         $result = 21;
            if ($chemin==2)         $result = 22;
        }

        return $result;
    }

    /**
     * @param		integer		ID Système solaire
     * @param		array			Système solaire complet
     * @return	string		Texte du SS utilisé dans les infos bulle
     */
    public function ss_info($ss, $data) {
        list($PosInX, $PosInY) = self::ss2xy($ss);
//        $PosInY			= floor(($ss-1) / 100);
//        $PosInX			= (($ss-1)      % 100)+1;
        $info			= $PosInX.':'.$PosInY.' ('.$ss.')';
        $empire			= '';
        $alliance		= '';
        $chemin			= '';
        $joueur			= '';
        $search			= '';
        $ennemi			= '';
        $pnj                    = '';
        $vortex			= '';
        $planète		= '';
        $asteroide		= '';
        $moi			= '';

        // NOTA: #93 Do no use quotes in html !
        foreach ($data as $k => $v) { /// $k = ID mysql/nb type @@ $v = array...
            if (!is_numeric($k)) continue;
            if (isset($v['EMPIRE']))
                $v['EMPIRE'] = htmlspecialchars(addcslashes($v['EMPIRE'], '"'));

            switch ($v['type']) {
                case 'moi':
                    $moi .= '<br/><font size=2>'.sprintf($this->lng['map_ownplanet'], $v['INFOS']).'</font>';
                    break;
                case 'empire':
                    if ($empire=='') {
                        $empire = '<br/>'.sprintf($this->lng['map_empire_header'], $data['empire'], $this->empire);
                    }

                    if ($v['Joueur'] != '')
                        $empire .= '<br/>'.sprintf($this->lng['map_row_player1'], $v['Joueur'], $v['Grade']);
                    else
                        $empire .= '<br/>'.sprintf($this->lng['map_row_player2'], $v['USER']);
                    break;
                case 'alliance':
                    if ($alliance=='') {
                        $alliance = '<br/>'.sprintf($this->lng['map_alliance_header'], $data['alliance']);
                    }

                    if ($v['Joueur'] != '')
                        $alliance .= '<br/>'.sprintf($this->lng['map_row_player1'], $v['Joueur'], $v['Grade']);
                    else
                        $alliance .= '<br/>'.sprintf($this->lng['map_row_player3'], $v['USER'], $v['EMPIRE']);
                    break;
                case 'search':
                    if ($search=='') $search = '<br/>'.sprintf($this->lng['map_search_header'], $data['search']);
                    $search .= '<br/>'.sprintf($this->lng['map_row_player3'], $v['USER'], $v['TYPE'] != 6 ? $v['EMPIRE']: $v['INFOS']);
                    break;

                case 'Joueur':
                    if ($joueur=='') $joueur = '<br/>'.sprintf($this->lng['map_player_header'], $data['Joueur']);
                    if ($v['EMPIRE'] != '')
                        $joueur .= '<br/>'.sprintf($this->lng['map_row_player3'], $v['USER'], $v['EMPIRE']);
                    else
                        $joueur .= '<br/>'.$v['USER'];
                    break;
                case 'Ennemi':
                    if ($ennemi=='') $ennemi = '<br/>'.sprintf($this->lng['map_ennemy_header'], $data['Ennemi']);
                    if ($v['EMPIRE'] != '')
                        $ennemi .= '<br/>'.sprintf($this->lng['map_row_player4'], $v['USER'], $v['EMPIRE']);
                    else
                        $ennemi .= '<br/><font color=red>'.$v['USER'].'</font>';
                    break;
                case 'pnj':
                    if ($pnj=='') $pnj = '<br/>'.sprintf($this->lng['map_pnj_header'], $data['pnj']);
                    $pnj .= '<br/>'.$v['INFOS'];
                    break;

                case 'Vortex':
                    if ($vortex=='') $vortex = '<br/>'.sprintf($this->lng['map_wormhole_header'], $data['Vortex']);
                    $vortex .= '<br/>=> '.$v['POSOUT'];
                    break;

                case 'Planète':
                    if ($planète=='')	$planète = '<br/>'.sprintf($this->lng['map_planet_header'], $data['Planète']);
                    break;
                case 'Astéroïde':
                    if ($asteroide=='') $asteroide = '<br/>'.sprintf($this->lng['map_asteroid_header'], $data['Astéroïde']);
                    break;
            }
        }

        if ( isset($data['Chemin']) ) {
            switch ($data['Chemin']) {
                case 1:		$chemin = $this->lng['map_parcours_start'];
                    break;
                case 3:		$chemin = $this->lng['map_parcours_end'];
                    break;
                default:	$chemin = $this->lng['map_parcours_wormhole'];
                    break;
            }
            $chemin = '<br/>'.sprintf($this->lng['map_parcours'], $chemin);
        }

        return $info.$moi.$chemin.$search.$empire.$alliance.$vortex.$asteroide.$planète
                .$ennemi.$pnj.$joueur;
    }

    /**
     * @return map
     */
    static public function getinstance() {
        if ( ! self::$instance )
            self::$instance = new self();

        return self::$instance;
    }

}
