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
            Mousey = e.pageY;
//            scrollx= document.body.scrollLeft;
//            scrolly= document.body.scrollTop;
            spacingx=5;
            spacingy=15;
        } else {
            spacingx=20;
            spacingy=10;
            Mousex = event.x;
            Mousey = event.y;
//            if(document.documentElement.clientWidth>0) {
//                scrollx = document.documentElement.scrollLeft;
//                scrolly = document.documentElement.scrollTop;
//            } else {
//                scrollx = document.body.scrollLeft;
//                scrolly = document.body.scrollTop;
//            }
        }

        cw = ( window.innerWidth ) ? window.innerWidth: document.body.clientWidth;
        if ( cw < ($("curseur").clientWidth+Mousex+spacingx+15))
            Posx = Mousex-(spacingx*2)-$("curseur").clientWidth;
        else
            Posx = spacingx+Mousex;

        ch = ( window.innerHeight ) ? window.innerHeight: document.body.clientHeight;
        if ( ch < ($("curseur").clientHeight+Mousey+spacingy+15))
            Posy = Mousey-spacingy-$("curseur").clientHeight;
        else
            Posy = spacingy+Mousey;

        $("curseur").style.left = Posx+"px";
        $("curseur").style.top  = Posy+"px";
//        $("debug").innerHTML  = 'w:'+$("curseur").clientWidth+'<br/>h:'+$("curseur").clientHeight+'<br/>';
//        $("debug").innerHTML += 'Mousex:'+Mousex+'<br/>Mousey:'+Mousey+'<br/>';
//        $("debug").innerHTML += 'scrollx:'+scrollx+'<br/>scrolly:'+scrolly+'<br/>';
//        $("debug").innerHTML += 'cw:'+cw+' < '+($("curseur").clientWidth+Mousex+spacingx+5)+'<br/>';
//        $("debug").innerHTML += 'ch:'+ch+' < '+($("curseur").clientHeight+Mousey+spacingy+5)+'<br/>';
//        $("debug").innerHTML += 'Posx:'+Posx+'<br/>Posy:'+Posy+'<br/>';

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

function GestionFormulaire() {
    affichage_formulaire($n('Type')[0].value);
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
            masquer("EMPIRE",0);
            afficher("INFOS",0);
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
        if(mystring.interpreter_getvalue(i18n.User) != "") {
            if(mystring.interpreter_getvalue(i18n.Player) != "") {
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

    $n("Type")[0].value=1;
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
    $n("EMPIRE")[0].value='';
    $n("INFOS")[0].value=nom;
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

function CheckOn(name) {
    $(name).checked=true;
}

function $n (name) {
    return document.getElementsByName(name);
}

Event.observe(document, 'mousemove', move);

function validateform(form) {
    i=0;
    submit=true;
    users = '';
    while (eval("form.Suppr"+i)) {
        if (eval("form.Suppr"+i+".checked == true")) {
            users += '\n- '+eval("form.ModifJoueur"+i+".value");
        }
        i++;
    }
    if (users!='') submit = confirm(i18n.DeleteUsers.evaluate({list:users}));
    return submit;
}