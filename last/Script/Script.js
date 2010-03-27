/**
 *
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
**/
var Taille=600;
var Mousex=0;
var Mousey=0;
var bullevisible=false;
var submenu=false;
// var enablemenu=true;
var MonTableau = new Array();
var TabData = new Array();

function DataEngine () {}

function move(e) {
    if(bullevisible) {  // Si la bulle est visible, on calcul en temps reel sa position ideale
        if (!Prototype.Browser.IE) { // Si on est pas sous IE
            Mousex = e.pageX;
            spacingx=5;
            Mousey = e.pageY;
            spacingy=10;
        } else {
            spacingx=20;
            spacingy=10;
            if(document.documentElement.clientWidth>0) {
                Mousex = event.x+document.documentElement.scrollLeft;
                Mousey = event.y+document.documentElement.scrollTop;
            } else {
                Mousex = event.x+document.body.scrollLeft;
                Mousey = event.y+document.body.scrollTop;
            }
        }

        cw = ( window.innerWidth ) ? window.innerWidth: document.body.clientWidth;
        if ( cw < ($("curseur").clientWidth+Mousex+spacingx+5))
            Mousex = cw-spacingx-$("curseur").clientWidth-5;
        else
            Mousex = spacingx+Mousex;

        ch = ( window.innerHeight ) ? window.innerHeight: document.body.clientHeight;
        if ( ch < ($("curseur").clientHeight+Mousey+spacingy+5))
            Mousey = ch-spacingy-$("curseur").clientHeight-5;
        else
            Mousey = spacingy+Mousey;

        $("curseur").style.left=Mousex+"px";
        $("curseur").style.top=Mousey+"px";

    } else {
        $("curseur").style.left="-1000px";
        $("curseur").style.top="-1000px";
    }
}


function montre(text) {
    if (submenu) return false;
    if(bullevisible==false) {
        $("curseur").style.visibility="visible"; // Si il est cacher (la verif n'est qu'une securité) on le rend visible.
        $("curseur").innerHTML = text; // Cette fonction est a améliorer, il parait qu'elle n'est pas valide (mais elle marche)
        bullevisible=true;
    }
    return true;
}

function cache() {
    if(bullevisible==true) {
        $("curseur").style.visibility="hidden"; // Si la bulle etais visible on la cache
        bullevisible=false;
    }
}

function montre2(id) { 
    if(submenu==false) {
        $("sm_"+id).style.visibility="visible";
        submenu=true;
    }
}
function cache2(id) {
    $("sm_"+id).style.visibility="hidden";
    submenu=false;
}

function masquer(name,nb) {
    $n(name)[nb].style.visibility="hidden";
    $n(name)[nb].style.position="absolute";
}

function afficher(name,nb) {
    $n(name)[nb].style.visibility="visible";
    $n(name)[nb].style.position="";
}

function affichage_formulaire(Check) {
    switch (Check)
    {
        case '0':
        case '5':
        case '3':
            masquer("COOROUT",0);
            afficher("USER",0);
            afficher("EMPIRE",0);
            afficher("INFOS",0);
            masquer("AddTabRessource",0);
            break;
        case '1':
            afficher("COOROUT",0);
            masquer("USER",0);
            masquer("EMPIRE",0);
            masquer("INFOS",0);
            masquer("AddTabRessource",0);
            break;
        case '2':
        case '4':
            masquer("COOROUT",0);
            masquer("USER",0);
            masquer("EMPIRE",0);
            masquer("INFOS",0);
            afficher("AddTabRessource",0);
            break;
        case '6':
            masquer("COOROUT",0);
            afficher("USER",0);
            afficher("EMPIRE",0);
            masquer("INFOS",0);
            masquer("AddTabRessource",0);
            break;
    }
}

function interpreter(text,insert) {
    var mystring = new String();
    mystring=text;
    var parsed=false;
    var needmore=false;
	

    //Recherche "nous avons les informations de la planète identifiée"  = information planète
    if(mystring.indexOf(i18n.PlayerPlanet) > 0)
    {
        if(mystring.interpreter_getvalue(i18n.Player) != "") {
            if(mystring.interpreter_getvalue(i18n.User) != "") {
                interpreter_joueur(mystring);
                parsed = true;
            } else {
                alert(i18n.PlayerIncomplete);
                needmore = true;
            }
        } else {
            interpreter_planete(mystring);
            parsed = true;
        }
    }
	
    if(mystring.indexOf(i18n.WormholeStart) > 0) {
        interpreter_vortex(mystring);
        parsed = true;
    }

    if(mystring.indexOf(i18n.Sun_page) > 0) {
        interpreter_system(mystring);
        parsed = true;
    }
    if(mystring.indexOf(i18n.Asteroid) > 0) {
        interpreter_asteroide(mystring);
        parsed = true;
    }
    if(mystring.indexOf(i18n.NPC1) > 0 && mystring.indexOf(i18n.NPC2) > 0) {
        interpreter_fleet_pnj(mystring);
        parsed = true;
    }

    if (!parsed && !needmore) alert(i18n.UnknownData);
    if (parsed && !needmore && insert) document.forms['data'].submit();
}

function interpreter_system(mystring) {
    pos = mystring.indexOf(i18n.Owner);
    $n('importation')[0].value = mystring.substr(pos+12);
    $n('phpparser')[0].value = 1;
    document.forms['data'].submit();
}

function interpreter_vortex(mystring) {
    pos1 = mystring.indexOf(i18n.WormholeStart);
    pos2 = mystring.indexOf(i18n.WormholeEnd);
    depart = mystring.substr(pos1,pos2);
    arrive = mystring.substr(pos2);
	
    cooD = depart.interpreter_getvalue(i18n.WormholeSS);
    detD = depart.interpreter_getvalue(i18n.Coords);
	
    cooA = arrive.interpreter_getvalue(i18n.WormholeSS);
    detA = arrive.interpreter_getvalue(i18n.Coords);
		
    $n("Type1")[0].selected=true;
    $n("COORIN")[0].value=cooD+":"+detD;
    $n("COOROUT")[0].value=cooA+":"+detA;
    affichage_formulaire(1);
}


function interpreter_joueur(mystring) {
    jou = mystring.interpreter_getvalue(i18n.User);
    coo = mystring.interpreter_getvalue(i18n.Coords);
    emp = mystring.interpreter_getvalue(i18n.Empire);
    nom = mystring.interpreter_getvalue(i18n.UserName);
    $n("Type")[0].value=0;
    $n("COORIN")[0].value=coo;
    $n("COOROUT")[0].value="";
    $n("USER")[0].value=jou;
    $n("EMPIRE")[0].value=emp;
    $n("INFOS")[0].value=nom;
}

function interpreter_fleet_pnj(mystring) {
    jou = mystring.interpreter_getvalue(i18n.Owner);
    nom = mystring.interpreter_getvalue(i18n.FleetName);
    coo = mystring.interpreter_getvalue(i18n.Coords);

    $n("Type")[0].value=6;
    $n("USER")[0].value=jou;
    $n("COORIN")[0].value=coo.replace(/(\ )/g, "");
    $n("COOROUT")[0].value='';
    $n("EMPIRE")[0].value=nom;
    $n("INFOS")[0].value='';
}

function interpreter_planete(mystring) {
    coo = mystring.interpreter_getvalue(i18n.Coords);
    tit	= mystring.interpreter_getvalue(i18n.Ressources0);
    cui	= mystring.interpreter_getvalue(i18n.Ressources1);
    fer	= mystring.interpreter_getvalue(i18n.Ressources2);
    alu	= mystring.interpreter_getvalue(i18n.Ressources3);
    mer	= mystring.interpreter_getvalue(i18n.Ressources4);
    sil	= mystring.interpreter_getvalue(i18n.Ressources5);
    ura	= mystring.interpreter_getvalue(i18n.Ressources6);
    kry	= mystring.interpreter_getvalue(i18n.Ressources7);
    azo	= mystring.interpreter_getvalue(i18n.Ressources8);
    hyd	= mystring.interpreter_getvalue(i18n.Ressources9);
	
    $n("Type")[0].value=2;
    $n("COORIN")[0].value=coo;
    $n("RESSOURCE0")[0].value=tit;
    $n("RESSOURCE1")[0].value=cui;
    $n("RESSOURCE2")[0].value=fer;
    $n("RESSOURCE3")[0].value=alu;
    $n("RESSOURCE4")[0].value=mer;
    $n("RESSOURCE5")[0].value=sil;
    $n("RESSOURCE6")[0].value=ura;
    $n("RESSOURCE7")[0].value=kry;
    $n("RESSOURCE8")[0].value=azo;
    $n("RESSOURCE9")[0].value=hyd;
    GestionFormulaire("Type");
}

function interpreter_asteroide(mystring) {
    coo = mystring.interpreter_getvalue(i18n.Coords);
    tit	= mystring.interpreter_getvalue(i18n.Ressources0);
    cui	= mystring.interpreter_getvalue(i18n.Ressources1);
    fer	= mystring.interpreter_getvalue(i18n.Ressources2);
    alu	= mystring.interpreter_getvalue(i18n.Ressources3);
    mer	= mystring.interpreter_getvalue(i18n.Ressources4);
    sil	= mystring.interpreter_getvalue(i18n.Ressources5);
    ura	= mystring.interpreter_getvalue(i18n.Ressources6);
    kry	= mystring.interpreter_getvalue(i18n.Ressources7);
    azo	= mystring.interpreter_getvalue(i18n.Ressources8);
    hyd	= mystring.interpreter_getvalue(i18n.Ressources9);

    $n("Type")[0].value=4;
    $n("COORIN")[0].value=coo;
    $n("RESSOURCE0")[0].value=tit;
    $n("RESSOURCE1")[0].value=cui;
    $n("RESSOURCE2")[0].value=fer;
    $n("RESSOURCE3")[0].value=alu;
    $n("RESSOURCE4")[0].value=mer;
    $n("RESSOURCE5")[0].value=sil;
    $n("RESSOURCE6")[0].value=ura;
    $n("RESSOURCE7")[0].value=kry;
    $n("RESSOURCE8")[0].value=azo;
    $n("RESSOURCE9")[0].value=hyd;
    GestionFormulaire("Type");
}

String.prototype.interpreter_getvalue = function(valeur) {
    if (Prototype.Browser.IE)
        sep = "  ";
    else
        sep = "\t";

    pos = this.indexOf(valeur);
    if(pos<0) return "";
    pos3 = this.indexOf("\n",pos);
    chaine = this.substr(pos,pos3-pos);
    pos2 = chaine.lastIndexOf(sep);
    return chaine.substr(pos2).replace(sep,"").strip();
}

String.prototype.ToXML = function() {
    if (Prototype.Browser.IE) {
        try //Internet Explorer (stupid browser)
        {
            var XML=new ActiveXObject("Microsoft.XMLDOM");
            XML.async="false";
            XML.loadXML(this);
            return XML;
        }
        catch(e)
        {
            alert('XML error:'+e.message);
            return false;
        }
    } else if (DOMParser) {
        try
        {
            var parser = new DOMParser();
            return parser.parseFromString(this,"text/xml");
        }
        catch(e)
        {
            alert('XML error:'+e.message);
            return false;
        }
    } else return false;
}

DataEngine.GetNode = function (xml, tag)
{
    try
    {
        var tagdata = xml.firstChild.getElementsByTagName(tag);
        if (tagdata.length>0)
            return tagdata[0].firstChild.nodeValue;
        else
            return "";
    } catch (e) {
        return 'Erreur XML';
    }
    return "";
}

function RechercheOnType()
{
    if(	$n("RechercheType")[0].value==2 ||
        $n("RechercheType")[0].value==4    ) {
        $n("RechercheTabRessource")[0].style.visibility="visible";
        $n("RechercheTabRessource")[0].style.position="";
    } else {
        $n("RechercheTabRessource")[0].style.visibility="hidden";
        $n("RechercheTabRessource")[0].style.position="absolute";
    }
}

/**
 * @example http://www.somacon.com/p143.php
 * @deprecated plus utilisé depuis 1.4.0-rc1
 */
function getCheckedValue(radioObj) {
    if(!radioObj)
        return "";
    var radioLength = radioObj.length;
    if(radioLength == undefined)
        if(radioObj.checked)
            return radioObj.value;
        else
            return "";
    for(var i = 0; i < radioLength; i++) {
        if(radioObj[i].checked) {
            return radioObj[i].value;
        }
    }
    return "";
}

function $n (name) {
    return document.getElementsByName(name);
}

Event.observe(document, 'mousemove', move);

function validateform(form) {
    i=0;
    submit=true;
    while (eval("form.Suppr"+i)) {
        if (eval("form.Suppr"+i+".checked == true")) {
            submit = confirm(i18n.DeleteUsers);
            break;
        }
        i++;
    }
    return submit;
}

var sprintfWrapper = {

	init : function () {

		if (typeof arguments == "undefined") { return null; }
		if (arguments.length < 1) { return null; }
		if (typeof arguments[0] != "string") { return null; }
		if (typeof RegExp == "undefined") { return null; }

		var string = arguments[0];
		var exp = new RegExp(/(%([%]|(\-)?(\+|\x20)?(0)?(\d+)?(\.(\d)?)?([bcdfosxX])))/g);
		var matches = new Array();
		var strings = new Array();
		var convCount = 0;
		var stringPosStart = 0;
		var stringPosEnd = 0;
		var matchPosEnd = 0;
		var newString = '';
		var match = null;

		while (match = exp.exec(string)) {
			if (match[9]) { convCount += 1; }

			stringPosStart = matchPosEnd;
			stringPosEnd = exp.lastIndex - match[0].length;
			strings[strings.length] = string.substring(stringPosStart, stringPosEnd);

			matchPosEnd = exp.lastIndex;
			matches[matches.length] = {
				match: match[0],
				left: match[3] ? true : false,
				sign: match[4] || '',
				pad: match[5] || ' ',
				min: match[6] || 0,
				precision: match[8],
				code: match[9] || '%',
				negative: parseInt(arguments[convCount]) < 0 ? true : false,
				argument: String(arguments[convCount])
			};
		}
		strings[strings.length] = string.substring(matchPosEnd);

		if (matches.length == 0) { return string; }
		if ((arguments.length - 1) < convCount) { return null; }

		var code = null;
		var match = null;
		var i = null;

		for (i=0; i<matches.length; i++) {

			if (matches[i].code == '%') { substitution = '%' }
			else if (matches[i].code == 'b') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(2));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'c') {
				matches[i].argument = String(String.fromCharCode(parseInt(Math.abs(parseInt(matches[i].argument)))));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'd') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'f') {
				matches[i].argument = String(Math.abs(parseFloat(matches[i].argument)).toFixed(matches[i].precision ? matches[i].precision : 6));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'o') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(8));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 's') {
				matches[i].argument = matches[i].argument.substring(0, matches[i].precision ? matches[i].precision : matches[i].argument.length)
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'x') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'X') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]).toUpperCase();
			}
			else {
				substitution = matches[i].match;
			}

			newString += strings[i];
			newString += substitution;

		}
		newString += strings[i];

		return newString;

	},

	convert : function(match, nosign){
		if (nosign) {
			match.sign = '';
		} else {
			match.sign = match.negative ? '-' : match.sign;
		}
		var l = match.min - match.argument.length + 1 - match.sign.length;
		var pad = new Array(l < 0 ? 0 : l).join(match.pad);
		if (!match.left) {
			if (match.pad == "0" || nosign) {
				return match.sign + pad + match.argument;
			} else {
				return pad + match.sign + match.argument;
			}
		} else {
			if (match.pad == "0" || nosign) {
				return match.sign + match.argument + pad.replace(/0/g, ' ');
			} else {
				return match.sign + match.argument + pad;
			}
		}
	}
}

sprintf = sprintfWrapper.init;