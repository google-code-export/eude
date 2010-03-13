var metadata = <><![CDATA[
// ==UserScript==
// @author       Alex10336
// @name         Data Engine
// @namespace    eude
// @version      1.4.2
// @lastmod      $Id$
// @license      GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
// @license      Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
// @homepage     http://app216.free.fr/eu2/tracker
// @description  Script de liaison entre firefox et un serveur Data Engine
// @include      http://*eu2.looki.*/index.php
// @include      http://*eu2.looki.*/galaxy/galaxy_overview.php*
// @include      http://*eu2.looki.*/galaxy/galaxy_info.php*
// @include      http://*eu2.looki.*/planet/planet_info.php*
// @include      http://*eu2.looki.*/wormhole/wormhole_info.php*
// @include      http://*eu2.looki.*/user/settings_overview.php?area=options
// @exclude      http://vs.eu2.looki.*/*
// ==/UserScript==
]]></>;

var c_url = document.location.href;
var c_host = document.location.hostname;
var c_server = c_host.substr(0, c_host.indexOf('.'));
var c_lang = c_host.substr(-3);
c_lang = c_lang.substr(c_lang.indexOf('.')+1);
var c_page = c_url.substr(7+c_host.length);
var c_prefix = c_server+'.'+c_lang;
metadata.search(/\@version\s+(\d+\.\d+\.\d+(\.\d+)?)/);
var mversion=RegExp.$1.replace(/\.+/g, '');
metadata.search(/\$Id\:\ eude\.user\.js\ (\d+)\ \d+\-\d+\-\d+\ .+\$/);
var revision=RegExp.$1;
var version=mversion+'r'+revision;
const debug=true;

var c_game_lang = (unsafeWindow.top.sei_language != 'undefined') ? unsafeWindow.top.sei_language: c_lang;

var i18n = Array();
i18n['fr'] = Array();
i18n['fr']['confheader']     = 'Options spécifique au <u>Data Engine</u>';
i18n['fr']['conflink']       = 'Adresse';
i18n['fr']['confuser']       = 'Nom d\'utilisateur';
i18n['fr']['confpass']       = 'Mot de passe';
i18n['fr']['confspacer']     = 130;
i18n['fr']['confcells']      = 21;
i18n['fr']['coords']         = 'Coordonnées';
i18n['fr']['ress0']          = 'Titane';
i18n['fr']['ress1']          = 'Cuivre';
i18n['fr']['ress2']          = 'Fer';
i18n['fr']['ress3']          = 'Aluminium';
i18n['fr']['ress4']          = 'Mercure';
i18n['fr']['ress5']          = 'Silicium';
i18n['fr']['ress6']          = 'Uranium';
i18n['fr']['ress7']          = 'Krypton';
i18n['fr']['ress8']          = 'Azote';
i18n['fr']['ress9']          = 'Hydrogène';
i18n['fr']['ss_preview']     = 'Aperçu système solaire n°';
i18n['fr']['neutral,planet'] = ' planètes';
i18n['fr']['emp,planet']     = ' joueur(s) de l\'empire';
i18n['fr']['ally,planet']    = ' joueur(s) allié';
i18n['fr']['war,planet']     = ' joueur(s) en guerre';
i18n['fr']['nap,planet']     = ' joueur(s) en pna';
i18n['fr']['wormhole']       = ' vortex';
i18n['fr'][',asteroid']      = ' astéroïde(s)';
i18n['fr'][',wreckage']      = ' champs de débris';
i18n['fr']['neutral,fleet']  = ' flotte(s) neutre';
i18n['fr']['own,fleet']      = ' flotte(s) perso';
i18n['fr']['nap,fleet']      = ' flotte(s) en pna';
i18n['fr']['enemy,fleet']    = ' flotte(s) ennemie(s)';
i18n['fr']['npc,fleet']      = ' flotte(s) pirate';
i18n['fr']['ga,fleet']       = ' flotte(s) schtroumpfs';

if (c_game_lang == 'com') c_game_lang = 'en';
i18n['en'] = Array();
i18n['en']['confheader']     = '<u>Data Engine</u> specifics options';
i18n['en']['conflink']       = 'Address';
i18n['en']['confuser']       = 'User name';
i18n['en']['confpass']       = 'Password';
i18n['en']['confspacer']     = 65;
i18n['en']['confcells']      = 21;
i18n['en']['coords']         = 'Coordinates';
i18n['en']['ress0']          = 'Titanium';
i18n['en']['ress1']          = 'Copper';
i18n['en']['ress2']          = 'Iron';
i18n['en']['ress3']          = 'Aluminium';
i18n['en']['ress4']          = 'Mercury';
i18n['en']['ress5']          = 'Silicon';
i18n['en']['ress6']          = 'Uranium';
i18n['en']['ress7']          = 'Krypton';
i18n['en']['ress8']          = 'Nitrogen';
i18n['en']['ress9']          = 'Hydrogen';
i18n['en']['ss_preview']     = 'Starsystem overview n°';
i18n['en']['neutral,planet'] = ' Planets';
i18n['en']['emp,planet']     = ' joueur(s) de l\'empire';
i18n['en']['ally,planet']    = ' joueur(s) allié';
i18n['en']['war,planet']     = ' joueur(s) en guerre';
i18n['en']['nap,planet']     = ' joueur(s) en pna';
i18n['en']['wormhole']       = ' vortex';
i18n['en'][',asteroid']      = ' astéroïde(s)';
i18n['en'][',wreckage']      = ' champs de débris';
i18n['en']['neutral,fleet']  = ' flotte(s) neutre';
i18n['en']['own,fleet']      = ' flotte(s) perso';
i18n['en']['nap,fleet']      = ' flotte(s) en pna';
i18n['en']['enemy,fleet']    = ' flotte(s) ennemie(s)';
i18n['en']['npc,fleet']      = ' flotte(s) pirate';
i18n['en']['ga,fleet']       = ' flotte(s) schtroumpfs';

i18n['de'] = Array();
i18n['de']['confheader']     = 'Options spécifique au <u>Data Engine</u>';
i18n['de']['conflink']       = 'Adresse';
i18n['de']['confuser']       = 'Nickname';
i18n['de']['confpass']       = 'Passwort';
i18n['de']['confspacer']     = 1;
i18n['de']['confcells']      = 21;
i18n['de']['coords']         = 'Koordinaten';
i18n['de']['ress0']          = 'Titan';
i18n['de']['ress1']          = 'Kupfer';
i18n['de']['ress2']          = 'Eisen';
i18n['de']['ress3']          = 'Aluminium';
i18n['de']['ress4']          = 'Quecksilber';
i18n['de']['ress5']          = 'Silizium';
i18n['de']['ress6']          = 'Uran';
i18n['de']['ress7']          = 'Krypton';
i18n['de']['ress8']          = 'Stickstoff';
i18n['de']['ress9']          = 'Wasserstoff';
i18n['de']['ss_preview']     = 'Aperçu système solaire n°';
i18n['de']['neutral,planet'] = ' planètes';
i18n['de']['emp,planet']     = ' joueur(s) de l\'empire';
i18n['de']['ally,planet']    = ' joueur(s) allié';
i18n['de']['war,planet']     = ' joueur(s) en guerre';
i18n['de']['nap,planet']     = ' joueur(s) en pna';
i18n['de']['wormhole']       = ' vortex';
i18n['de'][',asteroid']      = ' astéroïde(s)';
i18n['de'][',wreckage']      = ' champs de débris';
i18n['de']['neutral,fleet']  = ' flotte(s) neutre';
i18n['de']['own,fleet']      = ' flotte(s) perso';
i18n['de']['nap,fleet']      = ' flotte(s) en pna';
i18n['de']['enemy,fleet']    = ' flotte(s) ennemie(s)';
i18n['de']['npc,fleet']      = ' flotte(s) pirate';
i18n['de']['ga,fleet']       = ' flotte(s) schtroumpfs';

// [PL] translation by jhonny
i18n['pl'] = Array();
i18n['pl']['confheader']     = 'Opcje ustawienia do <u>Data Engine</u>';
i18n['pl']['conflink']       = 'Strona';
i18n['pl']['confuser']       = 'Użytkownik';
i18n['pl']['confpass']       = 'Hasło';
i18n['pl']['confspacer']     = 1;
i18n['pl']['confcells']      = 21;
i18n['pl']['coords']         = 'Współrzędne';
i18n['pl']['ress0']          = 'Tytan';
i18n['pl']['ress1']          = 'Miedź';
i18n['pl']['ress2']          = 'Żelazo';
i18n['pl']['ress3']          = 'Aluminium';
i18n['pl']['ress4']          = 'Rtęć';
i18n['pl']['ress5']          = 'Krzem';
i18n['pl']['ress6']          = 'Uran';
i18n['pl']['ress7']          = 'Krypton';
i18n['pl']['ress8']          = 'Azot';
i18n['pl']['ress9']          = 'Wodór';
i18n['pl']['ss_preview']     = 'Przegląd systemu gwiezdnego n°';
i18n['pl']['neutral,planet'] = ' Planety';
i18n['pl']['emp,planet']     = ' Członkowie imperium';
i18n['pl']['ally,planet']    = ' Członkowie sojuszu';
i18n['pl']['war,planet']     = ' Członkowie Wrogowie';
i18n['pl']['nap,planet']     = ' Członkowie PON';
i18n['pl']['wormhole']       = ' Wormhold';
i18n['pl'][',asteroid']      = ' Asteroidy';
i18n['pl'][',wreckage']      = ' Złom';
i18n['pl']['neutral,fleet']  = ' Neutralne Floty';
i18n['pl']['own,fleet']      = ' Moje Floty';
i18n['pl']['nap,fleet']      = ' Floty PON';
i18n['pl']['enemy,fleet']    = ' Wrogie Floty';
i18n['pl']['npc,fleet']      = ' Pirackie Floty';
i18n['pl']['ga,fleet']       = ' Smerfy Floty';

var salt = function (string) {
    function RotateLeft(lValue, iShiftBits) {
        return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
    }

    function AddUnsigned(lX,lY) {
        var lX4,lY4,lX8,lY8,lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
        if (lX4 & lY4) {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        if (lX4 | lY4) {
            if (lResult & 0x40000000) {
                return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            } else {
                return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        } else {
            return (lResult ^ lX8 ^ lY8);
        }
    }

    function F(x,y,z) {
        return (x & y) | ((~x) & z);
    }
    function G(x,y,z) {
        return (x & z) | (y & (~z));
    }
    function H(x,y,z) {
        return (x ^ y ^ z);
    }
    function I(x,y,z) {
        return (y ^ (x | (~z)));
    }

    function FF(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function GG(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function HH(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function II(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function ConvertToWordArray(string) {
        var lWordCount;
        var lMessageLength = string.length;
        var lNumberOfWords_temp1=lMessageLength + 8;
        var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
        var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
        var lWordArray=Array(lNumberOfWords-1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while ( lByteCount < lMessageLength ) {
            lWordCount = (lByteCount-(lByteCount % 4))/4;
            lBytePosition = (lByteCount % 4)*8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount)<<lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount-(lByteCount % 4))/4;
        lBytePosition = (lByteCount % 4)*8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
        lWordArray[lNumberOfWords-2] = lMessageLength<<3;
        lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
        return lWordArray;
    };

    function WordToHex(lValue) {
        var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
        for (lCount = 0;lCount<=3;lCount++) {
            lByte = (lValue>>>(lCount*8)) & 255;
            WordToHexValue_temp = "0" + lByte.toString(16);
            WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
        }
        return WordToHexValue;
    };

    function Utf8Encode(string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    };

    var x=Array();
    var k,AA,BB,CC,DD,a,b,c,d;
    var S11=7, S12=12, S13=17, S14=22;
    var S21=5, S22=9 , S23=14, S24=20;
    var S31=4, S32=11, S33=16, S34=23;
    var S41=6, S42=10, S43=15, S44=21;

    string = Utf8Encode(string);

    x = ConvertToWordArray(string);

    a = 0x67452301;
    b = 0xEFCDAB89;
    c = 0x98BADCFE;
    d = 0x10325476;

    for (k=0;k<x.length;k+=16) {
        AA=a;
        BB=b;
        CC=c;
        DD=d;
        a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
        d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
        c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
        b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
        a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
        d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
        c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
        b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
        a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
        d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
        c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
        b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
        a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
        d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
        c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
        b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
        a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
        d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
        c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
        b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
        a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
        d=GG(d,a,b,c,x[k+10],S22,0x2441453);
        c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
        b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
        a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
        d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
        c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
        b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
        a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
        d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
        c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
        b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
        a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
        d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
        c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
        b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
        a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
        d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
        c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
        b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
        a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
        d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
        c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
        b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
        a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
        d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
        c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
        b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
        a=II(a,b,c,d,x[k+0], S41,0xF4292244);
        d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
        c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
        b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
        a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
        d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
        c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
        b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
        a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
        d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
        c=II(c,d,a,b,x[k+6], S43,0xA3014314);
        b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
        a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
        d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
        c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
        b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
        a=AddUnsigned(a,AA);
        b=AddUnsigned(b,BB);
        c=AddUnsigned(c,CC);
        d=AddUnsigned(d,DD);
    }

    var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);

    return temp.toLowerCase();
}

function $() {
    if (arguments.length==1) return document.getElementById(arguments[0]);
    var z=[], i=0, el;
    while(el=document.getElementById(arguments[i++]))
        if (el)
            z.push(el);
    return z;
}

function $x() {
    var x='',          // default values
    node=document,
    type=0,
    fix=true,
    i=0,
    toAr=function(xp){      // XPathResult to array
        var _final=[], next;
        while(next=xp.iterateNext(),next)
            _final.push(next);
        return _final
    },
    cur;
    while (cur=arguments[i++],cur)      // argument handler
        switch(typeof cur) {
            case "string":
                x+=(x=='') ? cur : " | " + cur;
                continue;
            case "number":
                type=cur;
                continue;
            case "object":
                node=cur;
                continue;
            case "boolean":
                fix=cur;
                continue;
        }
    if (fix) {      // array conversion logic
        if (type==6) type=4;
        if (type==7) type=5;
    }
    if (!/^\//.test(x)) x="//"+x;         	 // selection mistake helper
    if (node!=document && !/^\./.test(x)) x="."+x;  // context mistake helper
    var temp=document.evaluate(x,node,null,type,null); //evaluate!
    if (fix)
        switch(type) {                              // automatically return special type
            case 1:
                return temp.numberValue;
            case 2:
                return temp.stringValue;
            case 3:
                return temp.booleanValue;
            case 8:
                return temp.singleNodeValue;
            case 9:
                return temp.singleNodeValue;
        }
    return fix ? toAr(temp) : temp;
}

//------------------------------------------------------------------------------

function options_spacer(width) {
    var cell = document.createElement('td');
    if (!width) width='20';
    cell.innerHTML = '<img width="'+width+'" height="1" src="/img/empty.gif"/>';
    return cell;
}
function options_header(text) {
    var cell = document.createElement('td');
    cell.setAttribute('class', 'font_pink_bold');
    cell.setAttribute('colspan', '4');
    cell.innerHTML = text;
    return cell;
}
function options_cell(text, style) {
    var cell = document.createElement('td');
    cell.setAttribute('class', 'font_white');
    if (style) cell.setAttribute('valign', 'top');
    cell.innerHTML = text;
    return cell;
}

function options_text_s(name, value, width, type) {
    if (!type) type = 'text'; else type = 'password';
    result = "<table cellspacing='0' cellpadding='0'><tr>"+
    "<td><img src='http://static.empireuniverse2.de/default/fr/default/input/input_left_blue.gif'></td>"+
    "<td style='background-image:url(http://static.empireuniverse2.de/default/fr/default/input/input_back_blue.gif);'>"+
    "<input type='"+type+"' class='input' maxlength='' style='width:"+width+
    "px;height:18px;background:transparent;padding:2px 0px 0px 0px; font-size:11px;color:#000000;margin:0px;border:0px' "+
    "' id='"+name+"' value='"+value+"' >    </td>"+
    "<td><img src='http://static.empireuniverse2.de/default/fr/default/input/input_right_blue.gif'></td>"+
    "</tr>    </table>";

    return result;
}

function options_button_save(id) {
    return '<img border="0" id="'+id+
    '" onmousedown="image_swap(\''+id+'\',\'http://static.empireuniverse2.de/default/'+c_game_lang+'/default/button/button_1/save/button_down.gif\')"'+
    ' onmouseover="image_swap(\''+id+'\',\'http://static.empireuniverse2.de/default/'+c_game_lang+'/default/button/button_1/save/button_over.gif\')"'+
    ' onmouseup="image_swap(\''+id+'\',\'http://static.empireuniverse2.de/default/'+c_game_lang+'/default/button/button_1/save/button_default.gif\')"'+
    ' onmouseout="image_swap(\''+id+'\',\'http://static.empireuniverse2.de/default/'+c_game_lang+'/default/button/button_1/save/button_default.gif\')"'+
    ' src="http://static.empireuniverse2.de/default/'+c_game_lang+'/default/button/button_1/save/button_default.gif" class="button"/>';
}

//------------------------------------------------------------------------------
//---------------------------- Partie communication xml ------------------------
//------------------------------------------------------------------------------

var c_onload = function(e) {

    if (e.status=='404' || e.status=='405') {
        GM_setValue(c_prefix+'actived','0');
        AddGameLog('<span class="gamelog_raid">No answer with Data Engine ('+e.status+')</span>');
        return alert('No link etablished with ours Data Engine\nCheck address');
        top.location.reload(true);
    }
    if (e.status=='500') {
        AddGameLog('<span class="gamelog_raid">Ours server has crached ?</span>');
        return alert('Ours server has crached ?!');
    }

    if (e.responseText.indexOf('<eude>')<0) {
        GM_setValue(c_prefix+'actived','0');
        alert("XML error, disabling 'eude'...\n\n\n\nData Engine send:\n"+e.responseText);
        return top.location.reload(true);
    }
    if (!e.responseXML)
        e.responseXML = new DOMParser().parseFromString(e.responseText, "text/xml");
    //    alert('xx'+ e.responseXML.getDocumentElement());

    //    if (debug) alert("Debug...\n"+e.responseXML+e.responseText);

    if (GetNode(e.responseXML, 'logtype'))
        $type = GetNode(e.responseXML, 'logtype');
    else
        $type = ((e.status!='200')? 'raid':'event');

    if (GetNode(e.responseXML, 'log')!='')
        AddGameLog('<span class="gamelog_'+$type+'">'+GetNode(e.responseXML, 'log')+'</span>');

    if (GetNode(e.responseXML, 'alert')!='') alert(GetNode(e.responseXML, 'alert'));
    if (GetNode(e.responseXML, 'script')!='') eval(GetNode(e.responseXML, 'script'));

    if (GetNode(e.responseXML, 'GM_active')!='') {
        var active = GetNode(e.responseXML, 'GM_active');
        GM_setValue(c_prefix+'actived', active);
        if (active=='1') {
            GM_setValue(c_prefix+'galaxy_info',   GetNode(e.responseXML, 'GM_galaxy_info')  =='1'? true:false);
            GM_setValue(c_prefix+'planet_info',   GetNode(e.responseXML, 'GM_planet_info')  =='1'? true:false);
            GM_setValue(c_prefix+'asteroid_info', GetNode(e.responseXML, 'GM_asteroid_info')=='1'? true:false);
            GM_setValue(c_prefix+'pnj_info',      GetNode(e.responseXML, 'GM_pnj_info')     =='1'? true:false);
        }
        top.location.reload(true);
    }
    if (GetNode(e.responseXML, 'content')!='')
        AddToMotd(GetNode(e.responseXML, 'content'));

    return true;
}

var c_onerror = function(e) {
    AddGameLog('<span class="gamelog_raid">Fatal ('+e.status+'): '+e.responseText+'</span>');
}

function get_xml(key, data) {
    var _server = GM_getValue(c_prefix+'serveur','')+
    'xml/eude.php?act='+key;
    if (debug) _server += '&XDEBUG_SESSION_START=netbeans-xdebug';
    var _data='';

    switch(typeof data) {
        case "string":
            _data = '&data='+encodeURIComponent(data);
            break;
        case "number":
            _data = '&data='+data;
            break;
        case "object":
            for (var item in data) {
                _data+='&'+item+'='+encodeURIComponent(data[item]);
            }
            break;
        case "boolean":
            if (data)
                _data = '&data=1';
            else
                _data = '&data=0';
            break;
    }
    _data = 'user='+encodeURIComponent(GM_getValue(c_prefix+'user',''))+
    '&pass='+encodeURIComponent(salt(GM_getValue(c_prefix+'pass','')))+
    '&svr='+encodeURIComponent(c_prefix)+_data;
    
    GM_xmlhttpRequest({
        method: 'POST',
        headers: {
            'Content-Type':'application/x-www-form-urlencoded',
            "User-Agent": navigator.userAgent,
            "Accept": "text/xml",
            "Accept-Encoding":"deflate"
        },
        data: _data,
        url: _server,
        onload: c_onload,
        onerror: c_onerror
    });
}

// -----------------------------------------------------------------------------
// ------------------------------- Routines ------------------------------------
// -----------------------------------------------------------------------------

function AddGameLog(text) {
    var log = null;
    try {
        log = top.document.getElementById('layer_site_content');
    } catch(e) {
        // funny undocumented chromium...
        log = frameElement.parentElement.parentElement.parentElement.parentNode.getElementById('layer_site_content');
    }
    if (log != null) log.innerHTML = text+'<br/>'+log.innerHTML;
}

function AddToMotd(text,sep) {
    var chat_motd = null;
    try {
        chat_motd = top.document.getElementById('chat_motd');
    } catch(e) {
        // funny undocumented chromium...
        chat_motd = frameElement.parentElement.parentElement.parentElement.parentNode.getElementById('chat_motd');
    }
    if (!sep) sep = '<br/>';
    var tmp = text+sep+chat_motd.innerHTML;
    chat_motd.innerHTML = tmp.substr(0,4000)+'...';
}

function GetNode (xml, tag){
    try
    {
        var tagdata = xml.firstChild.getElementsByTagName(tag);
        if (tagdata.length>0)
            return tagdata[0].firstChild.nodeValue;
        else
            return '';
    } catch (e) {
        return 'Erreur XML';
    }
    return '';
}

// -----------------------------------------------------------------------------
// ---------------------------- Fonctions par pages... -------------------------
// -----------------------------------------------------------------------------

function Index() {
    //    AddToMotd('Data Engine: <b>'+c_server+'</b>.<b>'+ c_lang+'</b> activé.');
    AddGameLog('Data Engine: <b>'+c_server+'</b>.<b>'+ c_lang+'</b>.');
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.text = '\x6f\x6c\x64\x53\x65\x74\x54\x69\x6d\x65\x6f\x75\x74'+
    '\x20\x3d\x20\x77\x69\x6e\x64\x6f\x77\x2e\x73\x65\x74\x54\x69\x6d'+
    '\x65\x6f\x75\x74\x3b\x0d\x0a\x77\x69\x6e\x64\x6f\x77\x2e\x73\x65'+
    '\x74\x54\x69\x6d\x65\x6f\x75\x74\x20\x3d\x20\x66\x75\x6e\x63\x74'+
    '\x69\x6f\x6e\x28\x63\x6f\x64\x65\x2c\x20\x69\x6e\x74\x65\x72\x76'+
    '\x61\x6c\x29\x20\x7b\x0d\x0a\x69\x66\x20\x28\x63\x6f\x64\x65\x3d'+
    '\x3d\x27\x63\x68\x61\x74\x4f\x70\x65\x6e\x28\x29\x27\x29\x20\x7b'+
    '\x0d\x0a\x77\x69\x6e\x64\x6f\x77\x2e\x73\x65\x74\x54\x69\x6d\x65'+
    '\x6f\x75\x74\x3d\x6f\x6c\x64\x53\x65\x74\x54\x69\x6d\x65\x6f\x75'+
    '\x74\x3b\x0d\x0a\x72\x65\x74\x75\x72\x6e\x20\x66\x61\x6c\x73\x65'+
    '\x3b\x0d\x0a\x7d\x0d\x0a\x6f\x6c\x64\x53\x65\x74\x54\x69\x6d\x65'+
    '\x6f\x75\x74\x28\x63\x6f\x64\x65\x2c\x20\x69\x6e\x74\x65\x72\x76'+
    '\x61\x6c\x29\x3b\x0d\x0a\x7d';
    $x('/html/body')[0].appendChild(script);

    var a = document.createElement('a');
    a.href='javascript:;';
    a.innerHTML = 'Log Data Engine';
    a.addEventListener('click', function() {
        top.window.document.getElementById('chat_motd').style.display='';
        top.window.document.getElementById('chat').style.display='none';
    }, false);

    if (c_lang=='fr') {
        var lnk = $x('/html/body/div[14]/strong/font/strong/strong/strong/font/strong/strong/font/a[3]');
        if (typeof lnk[0]=='undefined') lnk=$x('/html/body/div[14]/strong/strong/font/strong/strong/font/a[3]');
        if (typeof lnk[0]=='undefined') lnk=$x('/html/body/div[14]/a[3]');
        if (typeof lnk[0]!='undefined') {
            lnk[0].href='http://www.looki.'+c_lang+'/support/';
            lnk[0].target='_blank';
        }
    }
    
    var x =$x('/html/body/div[14]/strong/font/strong/strong/strong/font/strong/strong/font');
    if (typeof x[0]=='undefined' &&
        c_lang == 'de') x=$x('/html/body/div[16]');

    if (typeof x[0]=='undefined') x=$x('/html/body/div[14]/strong/strong/font/strong/strong/font');
    if (typeof x[0]=='undefined') x=$x('/html/body/div[14]');
    if (typeof x[0]!='undefined') {
        x[0].innerHTML = x[0].innerHTML + ' | ';
        x[0].appendChild(a);
    }
}

function Galaxy() {
    var reg=/orb\[\d+\]\='(\w+),[^,]*,[^,]*,[^,]*,[^,]*,(\w+|),[^,]*,[^,]*,([0-9a-h]{32})';/g;
    var m = document.documentElement.innerHTML.match(reg);

    var e=new Array();
    for (i = 0; i < m.length; i++) {
        m[i].search(reg);
        found = RegExp.$2+','+RegExp.$1;
        if (found.substr(1,8)=='wormhole') found='wormhole';
        if (e[found])
            e[found]= e[found]+1;
        else
            e[found]=1;
    }

    var msg = '<br/><b>'+i18n[c_game_lang]['ss_preview']+document.getElementById('target_starsystem_id').value+'</b>';
    if (e['neutral,planet']) msg += '<br/>'+                      e['neutral,planet']+i18n[c_game_lang]['neutral,planet'];
    if (e['emp,planet'])     msg += '<br/><font color=#ffff88>'+  e['emp,planet']    +i18n[c_game_lang]['emp,planet']     +'</font>';
    if (e['ally,planet'])    msg += '<br/><font color=gold>'+     e['ally,planet']   +i18n[c_game_lang]['ally,planet']    +'</font>';
    if (e['war,planet'])     msg += '<br/><font color=red>'+      e['war,planet']    +i18n[c_game_lang]['war,planet']     +'</font>';
    if (e['nap,planet'])     msg += '<br/><font color=#9966FF>'+  e['nap,planet']    +i18n[c_game_lang]['nap,planet']     +'</font>';
    if (e['wormhole'])       msg += '<br/><font color=#AABBFF>'+  e['wormhole']      +i18n[c_game_lang]['wormhole']       +'</font>';
    if (e[',asteroid'])      msg += '<br/><font color=gray>'+     e[',asteroid']     +i18n[c_game_lang][',asteroid']      +'</font>';
    if (e[',wreckage'])      msg += '<br/><font color=#AA55FF>'+  e[',wreckage']     +i18n[c_game_lang][',wreckage']      +'</font>';
    if (e['neutral,fleet'])  msg += '<br/>'+                      e['neutral,fleet'] +i18n[c_game_lang]['neutral,fleet'];
    if (e['own,fleet'])      msg += '<br/><font color=green>'+    e['own,fleet']     +i18n[c_game_lang]['own,fleet']      +'</font>';
    if (e['nap,fleet'])      msg += '<br/><font color=#9966FF>'+  e['nap,fleet']     +i18n[c_game_lang]['nap,fleet']      +'</font>';
    if (e['enemy,fleet'])    msg += '<br/><font color=red>'+      e['enemy,fleet']   +i18n[c_game_lang]['enemy,fleet']    +'</font>';
    if (e['npc,fleet'])      msg += '<br/><font color=gold>'+     e['npc,fleet']     +i18n[c_game_lang]['npc,fleet']      +'</font>';
    if (e['ga,fleet'])       msg += '<br/><font color=lightblue>'+e['ga,fleet']      +i18n[c_game_lang]['ga,fleet']       +'</font>';
    delete(e['neutral,sun']);
    delete(e['neutral,planet']);
    delete(e['own,planet']);
    delete(e['emp,planet']);
    delete(e['nap,planet']);
    delete(e['ally,planet']);
    delete(e['war,planet']);
    delete(e['wormhole']);
    delete(e[',asteroid']);
    delete(e[',wreckage']);
    delete(e['neutral,fleet']);
    delete(e['own,fleet']);
    delete(e['enemy,fleet']);
    delete(e['nap,fleet']);
    delete(e['npc,fleet']);
    delete(e['ga,fleet']);
    for (var item in e) {
        msg += '<br/><font color=red>'+e[item]+' &quot;'+item+'&quot; unknown item !</font>';
        delete(e[item]);
    }
    AddToMotd(msg,'<hr/>');
}

function Galaxy_Info() {
    data = new Array();
    data['ss']   = $x('/html/body/div/div/table/tbody/tr[3]/td[4]')[0].innerHTML;
    data['data'] = document.documentElement.innerHTML;
    //$x('/html/body/div/div[4]/div/table')[0].innerHTML;
    get_xml('galaxy_info', data);
}

function Wormhole() {
    var tables = $x('/html/body/div[2]/div/div/table/tbody/tr/td[3]/table', XPathResult.ORDERED_NODE_SNAPSHOT_TYPE);
    var i=1;
    ///html/body/div[2]/div/div/table/tbody/tr/td[3]/table/tbody/tr[4]/td[4]
    ///html/body/div[2]/div/div/table/tbody/tr/td[3]/table[2]/tbody/tr[3]/td[4]
    var a=new Array();
    tables.forEach(function(paragraph) {  // Loop over every paragraph
        var nodess=paragraph.childNodes[1].childNodes[4].childNodes[7];
        var node=paragraph.childNodes[1].childNodes[6].childNodes[7];
        if (i==1) // départ
            a['IN'] = nodess.innerHTML+':'+node.innerHTML;
        else if (i==2) // arrivée
            a['OUT'] = nodess.innerHTML+':'+node.innerHTML;
        i++;
    });
    get_xml('wormhole', a);
}
function Planet() {
    var html = document.documentElement.innerHTML;

    var a=new Array();
    if (html.match(eval('/'+i18n[c_game_lang]['coords']+'.+\\n.+<td class=\\"font_white\\">(\\d+:\\d+:\\d+:\\d+)<\\/td>/')))
        a['COORIN']= RegExp.$1;
    for (i=0;i<10;i++)
        if (html.match(eval('/'+i18n[c_game_lang]['ress'+i]+'.+\\n.+<td class=\\"font_white\\">(.+)<\\/td>/')))
            a[i]= RegExp.$1;
        else
            return false;

    get_xml('planet', a);
}

function Asteroid() {
    var html = document.documentElement.innerHTML;

    var a=new Array();
    if (html.match(eval('/'+i18n[c_game_lang]['coords']+'.+\\n.+<td class=\\"font_white\\">(\\d+:\\d+:\\d+:\\d+)<\\/td>/')))
        a['COORIN']= RegExp.$1;
    for (i=0;i<10;i++)
        if (html.match(eval('/'+i18n[c_game_lang]['ress'+i]+'.+\\n.+<td class=\\"font_white\\">(.+)<\\/td>/')))
            a[i]= RegExp.$1;

    get_xml('asteroid', a);
}

function Fleet() {

    var a = Array();
    a['owner']     = $x('/html/body/div[2]/div/table/tbody/tr[2]/td[4]')[0].innerHTML;
    a['fleetname'] = $x('/html/body/div[2]/div/table/tbody/tr[3]/td[4]')[0].innerHTML;
    a['coords']    = $x('/html/body/div[2]/div/table/tbody/tr[4]/td[4]')[0].innerHTML;
    if (!a['coords'].match(/\d+\s+-\s+\d+\s+-\s+\d+\s+-\s+\d+/)) // PNJ only ?
        a['coords'] = $x('/html/body/div[2]/div/table/tbody/tr[5]/td[4]')[0].innerHTML;    
    a['coords'] = a['coords'].replace(/\s*/g,'');

    if (a['owner'] =='PNJ' && GM_getValue(c_prefix+'pnj_info',false)) get_xml('pnj', a);

    if (a['owner'] !='PNJ') {
        a['owner'] = a['owner'].replace(/<\/?[^>]+>/gi, '')
    //    get_xml('pnj', a);
    }
//        alert('Fleet called:\nCoords: '+a['coords']+'\nProprio: '+a['owner']+'\nNom: '+a['fleetname']);

}

function Options() {
    var node = document.getElementById('layer_site_content');
    //                form            table
    var area = node.childNodes[1].childNodes[3];
    // area.rows[2]; // = bouton sauver...
    //    area = $x('/html/body/div[2]/div/div[6]/div/form/table[2]'); // alternative ?
    
    area.rows[4].innerHTML='';
    area.rows[4].appendChild(options_header(i18n[c_game_lang]['confheader']+' <small>('+version+')</small>'));

    area.rows[5].innerHTML='';
    area.rows[5].appendChild(options_spacer());
    area.rows[5].appendChild(options_cell(i18n[c_game_lang]['conflink'], true));
    area.rows[5].appendChild(options_spacer());
    area.rows[5].appendChild(options_cell(options_text_s('eude_server',GM_getValue(c_prefix+'serveur','http://app216.free.fr/eu2/test/'),'250')));

    area.rows[6].innerHTML='';
    area.rows[6].appendChild(options_spacer());
    area.rows[6].appendChild(options_cell(i18n[c_game_lang]['confuser'], true));
    area.rows[6].appendChild(options_spacer());
    area.rows[6].appendChild(options_cell(options_text_s('eude_user',GM_getValue(c_prefix+'user','test'),'100')));

    area.rows[7].innerHTML='';
    area.rows[7].appendChild(options_spacer());
    area.rows[7].appendChild(options_cell(i18n[c_game_lang]['confpass'], true));
    area.rows[7].appendChild(options_spacer());
    area.rows[7].appendChild(options_cell(options_text_s('eude_pass',GM_getValue(c_prefix+'pass','test'),'100', true)));

    area.rows[8].innerHTML='';
    area.rows[8].appendChild(options_spacer());
    area.rows[8].appendChild(options_cell(options_button_save('eude_save')));
    area.rows[8].appendChild(options_spacer(i18n[c_game_lang]['confspacer']));
    area.rows[8].appendChild(options_spacer());

    // rewrite delete accounts cells
    id = i18n[c_game_lang]['confcells'];
    var msg = area.rows[id].cells[3].innerHTML;
    area.rows[id].innerHTML='';
    area.rows[id].appendChild(options_spacer());
    var cell = options_cell(msg);
    cell.setAttribute('colspan', '3');
    area.rows[id].appendChild(cell);
    area.deleteRow(9);
    area.deleteRow(9);
    area.deleteRow(9);
    area.deleteRow(9);
    area.deleteRow(9);
    area.deleteRow(9);
    area.deleteRow(9);


    document.getElementById('eude_save').addEventListener('click', function() {
        var server = document.getElementById('eude_server').value;
        var user = document.getElementById('eude_user').value;
        var pass = document.getElementById('eude_pass').value;
        if (server.substr(-1)!='/') server+='/';
        GM_setValue(c_prefix+'serveur',server);
        GM_setValue(c_prefix+'user',user);
        GM_setValue(c_prefix+'pass',pass);

        get_xml('config', '');
        
    }, false);
}

/// Dispacheur
if (debug) AddGameLog('Page: '+c_page);

if (GM_getValue(c_prefix+'actived','0')!='0') {
    if (c_page.indexOf('index.php')>0)                                  Index();
    if (c_page.indexOf('galaxy/galaxy_overview.php?area=galaxy')>0)    Galaxy();
    if (c_page.indexOf('galaxy/galaxy_info.php')>0 &&
        GM_getValue(c_prefix+'galaxy_info',false) )               Galaxy_Info();
    if (c_page.indexOf('wormhole/wormhole_info.php?')>0)             Wormhole();
    if (c_page.indexOf('planet/planet_info.php?')>0) {

        if (c_page.indexOf('asteroid')>0 &&
            GM_getValue(c_prefix+'asteroid_info',false))             Asteroid();
        //if (c_page.indexOf('wreckage')>0)                               cdr();
        if (c_page.indexOf('plantype')<0 &&
            GM_getValue(c_prefix+'planet_info',false) )                Planet();
    }
    if (c_page.indexOf('fleet/fleet_info.php?')>0)                      Fleet();

}

if (c_page.indexOf('user/settings_overview.php?area=options')>0)      Options();
