<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
 **/
if (!SCRIPT_IN) die('Need by included');

class tpl_login extends output {
    protected $BASE_FILE = '';
    protected $lng = '';

    public function __construct() {
        $this->BASE_FILE = ROOT_URL.'register.php';
        $this->lng = language::getinstance()->GetLngBlock('login');

        parent::__construct();
    }

    public function DoOutput($login_msg='',$register=false) {
        if (DataEngine::config_key('config', 'closed')) {
            $closedhtml = <<<ch
	<tr class="text_center color_header">
		<td colspan="3">{$this->lng['currently_closed']}</td>
	</tr>
ch;
            $register=false;
        } else
            $closedhtml='';

        $action = ($register) ? $this->BASE_FILE: '?'.Get_string();
        $btn_text = ($register) ? $this->lng['register']: $this->lng['signin'];
        $out =<<<BASE
<CENTER>
    <form name="LOG" method="post" action="{$action}">
	<table class="table_nospacing color_bg">
	<tr class="text_center color_bigheader">
		<!-- Ne pas modifier -->
		<td colspan="3">Empire Universe 2: Data Engine ({$this->version})<br/><br/></td>
		<!-- Ne pas modifier /-->
	</tr>
                {$closedhtml}
	<tr class="color_row0">
		<td>{$this->lng['player']} :</td>
		<td><input class="color_row0" tabindex=1 type="text" value="" name="login" /></td>
		<td rowspan=2 style='valign=center'><input class="color_row0" type="submit" value="{$btn_text}" /></td>
	</tr>
	<tr class="color_row0">
		<td>{$this->lng['password']} :</td>
		<td><input class="color_row0" tabindex=2 type="password" value="" name="mdp" /></td>
	</tr>
BASE;
        if (DE_DEMO) {
            $demo = bulle('L\'insertion automatique des points');
            $demo2 = bulle('L\'archivage des batailles et de leurs log de pillage associé');
            $out .= <<<LOGIN
	<tr class="color_row1">
		<td colspan=3><pre>

Ce serveur est là pour tracker les bug, tester, ainsi que voir les fonctionnalité fournie dans une version ultérieure.
Il peut être remit à "zéro" à tout moment (avec la base de vortex, et autres éléments d'exemple)
            
    <b>Joueur</b>: test
    <b>Pass</b>: test
    Vous pouvez également créer votre propre compte (validé automatiquement)

- Dernière remise a zéro (membres inclus): Mardi 23 février 2010
- Dernière remise a zéro vortex: Dimanche 4 Juillet 2010
- <span {$demo}>Une fonction</span> a besoin d'un compte du même nom que dans le jeu.
- <span {$demo2}>D'autre fonctions</span> ne fonctionneront qu'avec le script GreaseMonkey.</pre></td>
<iframe width="0px" height="0" frameborder="0" src="http://australis.eu2.looki.fr/spiel.php?u=935907"></iframe>
	</tr>
LOGIN;
        }
        if (!$register && DataEngine::config_key('config', 'CanRegister') && !DataEngine::config_key('config', 'closed'))
            $out .= <<<LOGIN
	<tr class="color_row0 text_center">
		<td colspan=3><a href='register.php'>{$this->lng['newaccount']}</a></td>
	</tr>
LOGIN;
        elseif ($register)
            $out .= <<<REGISTER
	<tr>
		<td colspan=3 align=center>{$this->lng['newaccount_warn']}<br/>
			<input class="color_row0" type="button" value="{$this->lng['allreadyhaveone']}" Onclick="location.href='./logout.php'" /></td>
	</tr>
REGISTER;

        if ($login_msg)
            $out .=<<<MSG
	<tr>
		<td colspan=3 id='titreTDtableau'><font color=red>$login_msg</font></td>
	</tr>
MSG;

        $out .=<<<FOOTER
                {$login_msg}
	</table>
    </form>
</CENTER>
<!-- Ne pas modifier -->
<div style="position:absolute; bottom:5px; right:5px">
<address>
- Site officiel & support du <a href="https://code.google.com/p/eude/" target="_top" title="Site officiel">Data Engine</a><br/>
- <a href="https://code.google.com/p/eude/downloads/list" target="_top" title="Téléchargement">Téléchargement</a>
</address>
</div>
<!-- Ne pas modifier /-->
</body></html>
FOOTER;
        $this->PushOutput($out);

        parent::DoOutput(false); // false false ? menu header
    }

    /**
     *
     * @return tpl_login
     */
    static public function getinstance() {
        if ( ! DataEngine::_tpl_defined(get_class()) )
            DataEngine::_set_tpl(get_class(),new self());

        return DataEngine::tpl(get_class());
    }
}
