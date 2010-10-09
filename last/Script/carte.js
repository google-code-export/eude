
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
**/
var Navigateur = {
    LoadFleet: function()
    {
        fleet = $('fleet').value;
        if (fleet > 0)
            document.forms['calculer'].submit();
        else
            alert(i18n.Map.NoneSelected);
    },
    SaveFleet: function()
    {
        vin = $n('coorin')[0].value;
        vout = $n('coorout')[0].value;
        dd = $('fleet');
        if (dd.selectedIndex > 0)
            fleetname = dd[dd.selectedIndex].text;
        else
            fleetname = i18n.Map.NewFleet;

        if (vout == "" || vin =="") return alert(i18n.Map.IncompleteForm);
        if ( (val = prompt(i18n.Map.Save.evaluate({start:vin,end:vout}),fleetname)) )
            location.href = "?savefleet="+val+"&in="+vin+"&out="+vout;
        return true;
    },
    DelFleet: function()
    {
        dd = $('fleet');
        fleetname = dd[dd.selectedIndex].text;
        fleet = dd.value;
        if (fleet == 0)	return alert(i18n.Map.NoneSelected);
        if (fleet >0 && confirm(i18n.Map.Delete.evaluate({name:fleetname}))) location.href = "?delfleet="+fleet;
        return true;
    },
    invertcoords: function()
    {
        vin = $n('coorin')[0].value;
        vout = $n('coorout')[0].value;
        this.SetStart(vout);
        this.SetEnd(vin);
    },
    SetStart: function(coord)
    {
        $n('coorin')[0].value = coord;
    },
    SetEnd: function(coord)
    {
        $n('coorout')[0].value = coord;
    },
    InitSearch: function(value, type)
    {
        $('searchempire')['type'].selectedIndex=type;
        $('searchempire')['search'].value=value;
        this.DoSearch();
    },
    DoSearch: function()
    {
        var input = $('searchempire')['search'].value;
        var type  = $('searchempire')['type'].value;
        //                     return false;
        //			if ($('searchempire')['type'][0].checked)
        //				emp = Form.Element.getValue(input);
        //			else
        //				jou = Form.Element.getValue(input);
        $('carteunivers').src="./Images/loading.gif";

        new Ajax.Request('xml/carte.php',{
            method:'post',
            parameters:{
                type:type,
                s:input,
                'ss':Carte.GetLastSearch()
            },
            onCreate:function(){
                $('ajaxstatus').update(i18n.Ajax.onCreate);
            },
            onSuccess:function(t){
                var xml = '';
                $('ajaxstatus').update(i18n.Ajax.onSuccess);
                if (Prototype.Browser.Gecko) xml = t.responseXML; else xml = t.responseText.ToXML();
                if (xml==null) {
                    alert(i18n.Ajax.XML_Error);
                    Carte.SetLastSearch("");
                    $('carteunivers').src="./img.php?"+Math.random();
                    return false;
                }
                Carte.SetLastSearch(DataEngine.GetNode(xml,'currentsearch'));
                eval(DataEngine.GetNode(xml,'tabdata'));
                tmp=DataEngine.GetNode(xml,'script');
                if (tmp!='') eval(tmp);
                $('carteunivers').src="./img.php?"+Math.random();
                return true;
            },
            onFailure:function(t){
                $('carteunivers').src="./img.php?"+Math.random();
                alert(i18n.Ajax.onFailure);
            }
        });
        return false;
    }
}

function CCarte(tc,nbc, ss_info){
    var obj=this;
    var TailleCase=tc;
    var NbCase=nbc;
    var CX=CY=CO=IN=0;
    var lastsearch='';
    var ss_info_custom= new Array();
    var postop  = $('divcarteunivers').style.top;
    var posleft = $('divcarteunivers').style.left;
    postop = parseInt(postop.substring(0,postop.indexOf("px")));
    posleft = parseInt(posleft.substring(0,posleft.indexOf("px")));
    $('cibleurV').style.top=postop+1;
    $('cibleurH').style.left=posleft+1;

    this.cartemove = function(x,y) {
        if ( (x<TailleCase+1 || y<TailleCase+1) || (x>=(NbCase+1)*TailleCase || y>=(NbCase+1)*TailleCase) ) {
            if($('cibleurV').style.visibility != 'hidden') {
                $('cibleurV').style.visibility=$('cibleurH').style.visibility='hidden';
                cache();
            }
            CO=-1;
            IN=0;
        } else {
            if (Prototype.Browser.IE)
            {
                x = x - 2;
                y = y - 2;
            }
            CX = Math.floor(x/TailleCase);
            CY = Math.floor(y/TailleCase);
            CO = ''+(((CY-1)*100)+CX);
            $('cibleurV').style.visibility=$('cibleurH').style.visibility='visible';
            $('cibleurV').style.left=x-TailleCase+1;
            $('cibleurH').style.top=y+postop-TailleCase+1;
            $('Coord').innerHTML=CO;
            cache();
            if(typeof(ss_info_custom[CO]) != 'undefined') {
                IN=1;
                montre(this.build_bubulle(CO, ss_info_custom[CO]));
            } else if(typeof(ss_info[CO]) != 'undefined') {
                IN=1;
                montre(this.build_bubulle(CO, ss_info[CO]));
            } else {
                IN=0;
                montre(CO);
            }
        }
    }

    this.carteclick = function(e) {
        if (!e) e = window.event;
        if (e) {
            dest = e.button==2; // 0=left,1=middle,2=right
            popup = e.shiftKey || e.ctrlKey;
        }

        $('AjaxCarteDetails').style.visibility='hidden';
        if (popup) {
            new Ajax.Request('xml/cartedetail.php',{
                method:'get',
                parameters:{
                    'ID':CO
                },
                onCreate:function(){
                    Carte.DetailsShow(true);
                    $('AjaxCarteDetails').update(i18n.Ajax.onCreate);
                },
                onSuccess:function(t){
                    var xml = '';
                    $('ajaxstatus').update(i18n.Ajax.onSuccess);
                    if (Prototype.Browser.Gecko) xml = t.responseXML; else xml = t.responseText.ToXML();
                    if (xml==null) {
                        alert(i18n.Ajax.XML_Error);
                        Carte.DetailsShow(false);
                        return false;
                    }
                    tmp=DataEngine.GetNode(xml,'script');
                    if (tmp!='') eval(tmp);
                    $('AjaxCarteDetails').update(DataEngine.GetNode(xml,'content'));
                    return true;
                },
                onFailure:function(t){
                    alert(i18n.Ajax.onFailure);
                }
            });
        } else if (dest) {
            $n('loadfleet')[0].selectedIndex = 0;
            $n('coorout')[0].value=CO;
        } else {
            $n('loadfleet')[0].selectedIndex = 0;
            $n('coorin')[0].value=CO;
        }
        return false;
    }

    this.Get_SS = function (number) {
        if (typeof ss_info_custom[number] != 'undefined')
            return ss_info_custom[number];
        if (typeof ss_info[number] == 'undefined')
            return false;
        else
            return ss_info[number];
    }
    this.Set_SS = function (number, data) {
        ss_info_custom[number] = data;
    }
    this.Remove_SS = function (number) {
        delete ss_info_custom[number];
    }
    this.build_bubulle = function (ss, data) {
        if (typeof data == 'undefined') return false;
        var bubulle = Array();
        var ss2xy = function (ss) {
            y = Math.floor((ss-1)/100);
            x = ((ss-1)%100)+1;
            x = (x == 0) ? 100: x;
            return {
                ss:ss,
                x:x,
                y:y
            };
        }
        bubulle.push(new Template('#{x}:#{y} (#{ss})').evaluate(ss2xy(ss)));
        if (data.ownplanet) bubulle.push(i18n.Map.ownplanet.evaluate({
            planetname: data.ownplanet
        }));
        if (data.parcours == 1) bubulle.push(i18n.Map.parcours_start);
        if (data.parcours == 2) bubulle.push(i18n.Map.parcours_wormhole);
        if (data.parcours == 3) bubulle.push(i18n.Map.parcours_end);
        if (data.planets) bubulle.push(i18n.Map.planet_header.evaluate({
            num: data.planets
        }));
        if (data.asteroids) bubulle.push(i18n.Map.asteroid_header.evaluate({
            num: data.asteroids
        }));
        if (data.wormholes) {
            bubulle.push(i18n.Map.wormhole_header.evaluate({
                num: data.wormholes.length
            }));
            data.wormholes.each(function(value,ind){
                bubulle.push('=> '+value)
            });
        }
        if (data.empire) {
            bubulle.push(i18n.Map.empire_header.evaluate({
                num: data.empire.length
            }));
            data.empire.each(function(value,ind){
                bubulle.push(value)
            });
        }
        if (data.alliance) {
            bubulle.push(i18n.Map.alliance_header.evaluate({
                num: data.alliance.length
            }));
            data.alliance.each(function(value,ind){
                bubulle.push(value)
            });
        }
        if (data.players) {
            bubulle.push(i18n.Map.player_header.evaluate({
                num: data.players.length
            }));
            data.players.each(function(value,ind){
                bubulle.push(value)
            });
        }
        if (data.ennemys) {
            bubulle.push(i18n.Map.ennemy_header.evaluate({
                num: data.ennemys.length
            }));
            data.ennemys.each(function(value,ind){
                bubulle.push(value)
            });
        }
        if (data.reaperfleet) {
            bubulle.push(i18n.Map.pnj_header.evaluate({
                num: data.reaperfleet.length
            }));
            data.reaperfleet.each(function(value,ind){
                bubulle.push(value)
            });
        }
        if (data.searchresult) {
            bubulle.push(i18n.Map.search_header.evaluate({
                num: data.searchresult.length
            }));
            data.searchresult.each(function(value,ind){
                bubulle.push(value)
            });
        }
        return bubulle.join('<br/>');
    }
    this.DetailsShow = function(visible){
        if (visible)
            $('AjaxCarteDetails').style.visibility='visible';
        else
            $('AjaxCarteDetails').style.visibility='hidden';
        return false;
    }

    this.ImgOnLoad = function() {
        if ($('carteunivers').src.indexOf('loading')==0) $('ajaxstatus').update('ImgOnLoad');
    }
    this.GetLastSearch = function () {
        return lastsearch;
    }
    this.SetLastSearch = function (value) {
        lastsearch = value;
    }

    this.mousemapmove = function(e) {
        if (submenu) return false;
        if (!Prototype.Browser.IE) {
            Mousex = e.pageX - posleft - 1;
            Mousey = e.pageY - postop - 1;
        // 			ButtonM = e.button;
        }	else {
            Mousex = e.x - posleft + document.documentElement.scrollLeft-1;
            Mousey = e.y - postop  + document.documentElement.scrollTop-1;
        // 			ButtonM = e.button;
        }
        obj.cartemove(Mousex+obj.gettc(),Mousey+obj.gettc());
        return true;
    }

    this.init = function() {
        $('divcarteunivers').observe('contextmenu', Event.stop);
        $('cibleurV').observe('contextmenu', Event.stop);
        $('cibleurH').observe('contextmenu', Event.stop);
        $('divcarteunivers').observe('mousedown', this.carteclick);
        $('cibleurV').observe('mousedown', this.carteclick);
        $('cibleurH').observe('mousedown', this.carteclick);
        $('carteunivers').observe('load', this.ImgOnLoad);

        document.observe('mousemove', this.mousemapmove);
    }
    this.gettc = function() {
        return TailleCase;
    }
}