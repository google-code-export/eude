<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');


class tpl_menu {
/// déclaration de base...
    static private $instance;

    protected $out='';
    protected $left=0;

    public function __construct() {
    }

    /**
     * Définit le menu par défaut, pour ajouter des entrée voir la partie addons.
     * @return array menu
     */
    static function DefaultMenu() {

// 'menu_unique_id' => array('file/http','btn-img','btn_width','eval(some_php_for_axx)', $array_for_sub_menu_item),
// $array_for_sub_menu_item = array('file/http','btn-img','eval(some_php_for_axx)')
        return array(
                'carto' => array('%ROOT_URL%index.php','%BTN_URL%cartographie.png',160,'DataEngine::CheckPerms("CARTOGRAPHIE")||DataEngine::CheckPerms("CARTE")', array(
                                array('%ROOT_URL%cartographie.php','%BTN_URL%tableau.png','DataEngine::CheckPerms("CARTOGRAPHIE")'),
                                array('%ROOT_URL%Carte.php','%BTN_URL%carte.png','DataEngine::CheckPerms("CARTE")'),
                        ),
                ),
                'perso' => array('%ROOT_URL%Mafiche.php','%BTN_URL%mafiche.png',160,'DataEngine::CheckPerms("PERSO")', array(
                                array('%ROOT_URL%Recherche.php','%BTN_URL%recherche.png','DataEngine::CheckPerms("PERSO_RESEARCH")'),
                                array('%ROOT_URL%ownuniverse.php','%BTN_URL%ownuniverse.png','DataEngine::CheckPerms("PERSO_OWNUNIVERSE")'),
                                array('%ROOT_URL%pillage.php','%BTN_URL%pillage.png','DataEngine::CheckPerms("PERSO_TROOPS_BATTLE")'),
                        ),
                ),
                'addon' => array('', '%BTN_URL%addon.png',160, 'addons::getinstance()->IncludeAddonMenu()', array() ),
                'admin' => array('%ROOT_URL%Membres.php','%BTN_URL%membres.png',160,'DataEngine::CheckPerms("MEMBRES_HIERARCHIE")', array(
                                array('%ROOT_URL%Membres.php','%BTN_URL%hierarchie.png','DataEngine::CheckPerms("MEMBRES_HIERARCHIE")'),
                                array('%ROOT_URL%editmembres.php','%BTN_URL%editmembres.png','DataEngine::CheckPerms("MEMBRES_EDIT")'),
                                array('%ROOT_URL%stats.php','%BTN_URL%stats.png','DataEngine::CheckPerms("MEMBRES_STATS")'),
                                array('%ROOT_URL%EAdmin.php','%BTN_URL%eadmin.png','DataEngine::CheckPerms("MEMBRES_ADMIN")'),
                        ),
                ),
                'forum' => array(DataEngine::config_key('config', 'ForumLink'),'%BTN_URL%forum.png',160,'DataEngine::config_key(\'config\', \'ForumLink\') != ""', null),
                'logout' => array('%ROOT_URL%logout.php','%BTN_URL%logout.png',160,'DataEngine::CheckPerms(AXX_GUEST)', null),
        );
    }

    static public function Gen_Menu($menu) {
        return self::getinstance()->_Gen_Menu($menu);
    }

    protected function _Gen_Menu($menu) {
        $this->left=5;
        $this->out = <<<HEADER
<div id="menu" style="z-index:4; font-size:10px; width:100%; height:40px; position:absolute; top:0px; margin-left:10px; margin-right:auto;">&nbsp;</div>

HEADER;
        foreach($menu as $menu_id => $main_menu) {
            if (!eval("return {$main_menu[3]};")) continue; // pas d'autorisation sur ce menu.
            $submenu = false;

            if (is_array($main_menu[4])) {
                $sub_items=array();
                foreach($main_menu[4] as $sub_menu) {
                    if (!eval("return {$sub_menu[2]};")) continue; // pas d'autorisation sur ce sous_menu.
                    $sub_items[] = $this->sub_menu_item($sub_menu[0],$sub_menu[1]);
                }
                if (count($sub_items)>0) {
                    $submenu = true;
                    $this->sub_menu($menu_id, $main_menu[2], $sub_items);
                }
            }
            $this->main_menu($menu_id, $main_menu[0], $main_menu[1], $main_menu[2], $submenu);
        }
        return $this->out.'<br/><br/><br/>';
    }

    protected function main_menu($id, $url, $img, $width, $submenu=true) {
        $sm = ($submenu) ? ' OnMouseOver="montre2(\''.$id.'\');" OnMouseOut="cache2(\''.$id.'\');"': '';
        $link = ''; $link2 = '';
        if ($url) {
            $link = (stristr($url,"http") === false) ? "<a href='$url'>": "<a href='$url' target='_blank'>";
            $link2 = '</a>';
        }
        $l = ($this->left).'px'; $w=($width).'px';

        $this->out .= <<<EOF
<div id="mm_$id" style="z-index:7; left:$l; width:$w; top:5px; position:absolute;"$sm>
    <center>$link<img src="$img" />{$link2}</center>
</div>
EOF;
        $this->left += $width+5;
    }
    
    protected function sub_menu($id, $width, $content) {
        $content = implode("\n", $content);
        $left = ($this->left-5).'px'; $width=($width+10).'px';
        $this->out .= <<<EOF
<div id="sm_$id"  onmouseover="montre2('$id');" onmouseout="cache2('$id');" style="z-index:10; font-size:10px; top:35px; left:{$left}; width:$width; background-color: black; visibility:hidden; position:absolute; text-align:center">$content</div>

EOF;
    }

    protected function sub_menu_item($url, $img) {
        $link = ''; $link2 = '';
        if ($url) {
            $link = (stristr($url,"http") === false) ? "<a href='$url'>":
                    "<a href='$url' target='_blank'>";
            $link2 = '</a>';
        }
        return "<center>{$link}<img src={$img} />{$link2}</center>";
    }

    /**
     * @return tpl_menu
     */
    static public function getinstance() {
        if ( ! self::$instance )
            return new self();
        else
            return self::$instance;
    }

}