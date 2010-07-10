var metadata = <><![CDATA[
// ==UserScript==
// @author       Alex10336
// @name         Data Engine
// @namespace    http://eude.googlecode.com/
// @version      svn
// @lastmod      $Id$
// @license      GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
// @license      Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
// @description  Script de liaison entre firefox et un serveur Data Engine
// @include      http://*eu2.looki.tld/index.php
// @include      http://*eu2.looki.tld/galaxy/galaxy_overview.php*
// @include      http://*eu2.looki.tld/galaxy/galaxy_info.php*
// @include      http://*eu2.looki.tld/planet/planet_info.php*
// @include      http://*eu2.looki.tld/fleet/fleet_info.php*
// @include      http://*eu2.looki.tld/fleet/fleet_edit.php*
// @include      http://*eu2.looki.tld/fleet/fleet_troop.php*
// @include      http://*eu2.looki.tld/fleet/commander_info.php?commander_id=*
// @include      http://*eu2.looki.tld/wormhole/wormhole_info.php*
// @include      http://*eu2.looki.tld/building/control/control_overview.php?area=planet
// @include      http://*eu2.looki.tld/user/settings_overview.php?area=options
// @include      http://*eu2.looki.tld/battle/battle_ground_report_info.php?area=ground_battle*
// @include      http://*eu2.looki.tld/gamelog/gamelog_view.php?gamelog_id*
// @include      http://*.eu2.looki.tld/empire/empire_info.php?area=member&empire_id=*
// @include      http://*.eu2.looki.tld/empire/empire_info.php?empire_id=*
// @include      http://*.eu2.looki.tld/empire/empire_info.php?area=info&empire_id=*
// @exclude      http://vs.eu2.looki.tld/*
// ==/UserScript==
]]></>.toString();

var c_url = document.location.href;
var c_host = document.location.hostname;
var c_server = c_host.substr(0, c_host.indexOf('.'));
var c_lang = c_host.substr(-3);
c_lang = c_lang.substr(c_lang.indexOf('.')+1);
var c_page = c_url.substr(7+c_host.length);
var c_prefix = c_server+'.'+c_lang;
if (c_prefix == 'eu2.fr') c_prefix = 'australis.fr';
metadata.search(/\@version\s+(.*)/);
var mversion=RegExp.$1.replace(/\.*/g, '');
metadata.search(/Id\:\ eude\.user\.js\ (\d+)\ \d+\-\d+\-\d+\ .+\$/);
var revision=RegExp.$1;
var version=mversion+'r'+revision;
const debug=true;
const UseTamper = function_exists('TM_log');

if (UseTamper) {
    TM_log('Version '+version);
    TM_log('Page Check '+c_page);
}

try {
var c_game_lang = (typeof unsafeWindow.top.window.fv['lang'] != 'undefined') ? unsafeWindow.top.window.fv['lang']: c_lang;
} catch(e) {c_game_lang = c_lang;}

if (UseTamper) TM_log('Check Point, should no work after yet !');