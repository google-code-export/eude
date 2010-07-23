<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
**/
if (!SCRIPT_IN) die('Need by included');

class tpl_header {
    /**
     *
     * @return string html header
     */
	static public function Get_Header() {
		$obj = DataEngine::tpl('');

		if ($obj->page_title=="")
			$title = "EU2: Data Engine v{$obj->version}";
		else
			$title = $obj->page_title;

		if ($obj->css_file!="") {
			$css = <<<EOF
		<link rel="stylesheet" type="text/css" href="%LNG_URL%{$obj->css_file}?{$obj->version}" media="screen" />
EOF;
		} else {
			$css = '';
		}

if (DE_DEMO)
$stats = <<<st
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-12854322-1");
pageTracker._trackPageview();
} catch(err) {}</script>
st;
$doctype= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
$doctype= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
$doctype= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
$doctype='<html xmlns="http://www.w3.org/1999/html" lang="'.LNG_CODE.'" xml:lang="'.LNG_CODE.'">';
		return<<<EOF
{$doctype}
<head>
<title>{$title}</title>
{$css}
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="%BTN_URL%eude.png">
</head>
<body>
{$stats}
<script type="text/javascript" src="%INCLUDE_URL%prototype.js?1.6.1"></script>
<script type="text/javascript" src="%LNG_URL%eude.local.js?{$obj->version}"></script>
<script type="text/javascript" src="%INCLUDE_URL%Script.js?{$obj->version}"></script>
<div id="curseur" class="infobulle"></div>
<div id="debug" style="z-index:8; position:fixed; visibility:visible; background-color: #C0C0C0;white-space:nowrap; top:50px; left:5px"></div>
%NEW_MESSAGE_ENTRY%
EOF;

	}
        static function messager(&$data, &$msg) {
            $html = (is_array($msg)) ? implode('<br/>',$msg): $msg;
            $html = <<<h
<div id="newmessage" Onclick="$('newmessage').style.visibility='hidden';">
    <div class="newmessage">
      <table class="newmessage">
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td width="50px">&nbsp;</td>
            <td class="color_row0">{$html}</td>
            <td width="50px">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
     </table>
   </div>
   <div class="messagerbg"></div>
</div>
<script type="text/javascript">
setTimeout("$('newmessage').style.visibility='hidden';",5000);
</script>
h;
            if ($msg)
                $data = str_replace('%NEW_MESSAGE_ENTRY%',$html, $data);
            else
                $data = str_replace('%NEW_MESSAGE_ENTRY%','', $data);
            $msg='';
        }
}

