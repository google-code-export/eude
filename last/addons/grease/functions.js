
function Index() {
    AddGameLog('<span class="gamelog_event">'+i18n[c_game_lang]['eudeready']+'</span>');
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

    var aserver = document.createElement('a');
    aserver.href=GM_getValue(c_prefix+'serveur','');
    aserver.target='_blank';
    aserver.innerHTML = 'Data Engine';

    x = $x('//*[@id="linkline"]');
    block = x[x.length-1];
    block.innerHTML = block.innerHTML + ' | ';
    block.appendChild(aserver);
    var chatton = unsafeWindow.top.window.document.getElementById('chat_motd');
    chatton.style.height = 500;

    if (debug) {
        chatton.removeAttribute('OnClick');
        var js_OnClick = document.createAttribute('Ondblclick');
        js_OnClick.value = "chatOpen();";
        chatton.setAttributeNode(js_OnClick);
        var adebug = document.createElement('a');
        adebug.href='javascript:;';
        adebug.innerHTML = 'Reset';
        js_OnClick = document.createAttribute('OnClick');
        js_OnClick.value = "top.window.document.getElementById('chat_motd').style.display='';top.window.document.getElementById('chat').style.display='none';top.window.document.getElementById('chat_motd').innerHTML='';";
        adebug.setAttributeNode(js_OnClick);
        block.innerHTML = block.innerHTML + ', ';
        block.appendChild(adebug);
    } else {
        var alog = document.createElement('a');
        alog.href='javascript:;';
        alog.innerHTML = 'Log';
        var js_OnClick = document.createAttribute('OnClick');
        js_OnClick.value = "top.window.document.getElementById('chat_motd').style.display='';top.window.document.getElementById('chat').style.display='none';";
        alog.setAttributeNode(js_OnClick);
        block.innerHTML = block.innerHTML + ', ';
        block.appendChild(alog);
    }

    if (debug) return AddGameLog('<span class="gamelog_raid">Debug mode, script update disabled</span>');
    if (mversion=='svn') return AddGameLog('<span class="gamelog_raid">Dev release, no update check</span>');

    GM_xmlhttpRequest({
        method: 'GET',
        headers: {
            "User-Agent": navigator.userAgent,
            "Accept": "text/xml",
            "Accept-Encoding":"deflate"
        },
        url: 'http://eude.googlecode.com/svn/tag/GreaseMonkey/lastrelease.xml',
        onload: function(e){
            if (e.status!='200') {
                AddGameLog('<span class="gamelog_raid">Official server has dirty answer (omg)</span>');
                return;
            }

            if (!e.responseXML)
                e.responseXML = new DOMParser().parseFromString(e.responseText, "text/xml");
            rversion = GetNode(e.responseXML, 'rversion');
            eudeversion = GetNode(e.responseXML, 'eudeversion');
            majurl = GetNode(e.responseXML, 'url');
            majlog = GetNode(e.responseXML, 'log');
            if (revision<rversion) {
                AddToMotd('<b>Log:</b><br/>'+majlog, '<hr/>');
                if (mversion==eudeversion)
                    AddToMotd('<a href="'+majurl+'" class="gamelog_raid">=> MAJ Greasemonkey</a>');
                AddToMotd('<hr/>Mise à jour disponible de '+mversion+'r'+revision+' vers '+eudeversion+'r'+rversion);

                if (mversion==eudeversion)
                    AddGameLog('<a href="'+majurl+'" class="gamelog_raid">=> MAJ Greasemonkey</a>');
                else
                    AddGameLog('<span class="gamelog_raid">Mise à jour manuelle a faire !</span>');
            }
        },
        onerror: c_onerror
    });
}

function Galaxy() {
    var reg=/orb\[\d+\]\='(\w+),[^,]*,[^,]*,[^,]*,[^,]*,(\w+|),[^,]*,[^,]*,([0-9a-h]{32})[^,]*?,?[^;]+';/g;
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
    a = new Array();
    row = id= 1;
    while (typeof $x('/html/body/div/div[4]/div/table/tbody/tr['+id+']/td[3]')[0] != 'undefined') {
        a[row-1] = new Array();
        a[row-1][1] = $x('/html/body/div/div[4]/div/table/tbody/tr['+id+']/td[3]')[0].innerHTML;
        a[row-1][2] = $x('/html/body/div/div[4]/div/table/tbody/tr['+id+']/td[5]')[0].innerHTML;
        a[row-1][3] = $x('/html/body/div/div[4]/div/table/tbody/tr['+id+']/td[7]')[0].innerHTML;
        row++;
        id = (row*2)-1;
    }
    data = new Array();
    data['ss']   = $x('/html/body/div/div/table/tbody/tr[3]/td[4]')[0].innerHTML;
    data['data'] = serialize(a);
    get_xml('galaxy_info', data);
}

function Wormhole() {
    var tables = $x('/html/body/div[2]/div/div/table/tbody/tr/td[3]/table', XPathResult.ORDERED_NODE_SNAPSHOT_TYPE);
    var i=1;
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

    if (html.match(eval('/'+i18n[c_game_lang]['water']+'.+<td class=\\"font_white\\">(\\d+)%<\\/td>/'))) {
        a['WATER'] = trim(RegExp.$1);
        if (debug) GM_log(i18n[c_game_lang]['water']+':'+a['WATER']);
        a['COORIN']= $x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr[4]/td[4]')[0].innerHTML;
        if (debug) GM_log(i18n[c_game_lang]['coords']+':'+a['COORIN']);
        a['BUILDINGS']=trim($x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr[6]/td[4]')[0].innerHTML);
        if (debug) GM_log(i18n[c_game_lang]['building']+':'+a['BUILDINGS']);
        get_xml('player', a);
    } else {
		if ($x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr[3]/td[2]')[0].innerHTML != i18n[c_game_lang]['coords']) {
			a['COORIN']= $x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr[4]/td[4]')[0].innerHTML;
		} else {
			a['COORIN']= $x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr[3]/td[4]')[0].innerHTML;
		}
        if (debug) GM_log(i18n[c_game_lang]['coords']+':'+a['COORIN']);
        row=4;
        while (typeof $x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr['+row+']/td[2]')[0] != 'undefined') {
            ress = $x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr['+row+']/td[2]')[0].innerHTML;
            for (i=0;i<10;i++)
                if (ress.indexOf(i18n[c_game_lang]['ress'+i])>0) {
                    a[i]= $x('/html/body/div/table/tbody/tr/td[3]/table/tbody/tr['+row+']/td[4]')[0].innerHTML;
                    break;
                }
            row++;
        }
        get_xml('planet', a);
    }

}

function Asteroid() {
    var html = document.documentElement.innerHTML;

    var a=new Array();
    if (html.match(/<td class="font_white">(\d+:\d+:\d+:\d+)<\/td>/))
        a['COORIN']= RegExp.$1;

    row = 4;
    while (typeof $x('/html/body/div[2]/table/tbody/tr/td[3]/table/tbody/tr['+row+']/td[2]')[0] != 'undefined') {
        ress = $x('/html/body/div[2]/table/tbody/tr/td[3]/table/tbody/tr['+row+']/td[2]')[0].innerHTML;
        for (i=0;i<10;i++)
            if (ress.indexOf(i18n[c_game_lang]['ress'+i])>0) {
                a[i]= $x('/html/body/div[2]/table/tbody/tr/td[3]/table/tbody/tr['+row+']/td[4]')[0].innerHTML;
                break;
            }
        row++;
    }

    get_xml('asteroid', a);
}

function Fleet() {

    var a = Array();
    var npc = false;
    a['owner']     = $x('/html/body/div/div/table/tbody/tr[2]/td[4]')[0].innerHTML;
    a['fleetname'] = $x('/html/body/div/div/table/tbody/tr[3]/td[4]')[0].innerHTML;
    a['coords']    = $x('/html/body/div/div/table/tbody/tr[4]/td[4]')[0].innerHTML;
    if (!a['coords'].match(/\d+\s+-\s+\d+\s+-\s+\d+\s+-\s+\d+/)) { // PNJ only ?
        npc = true;
        a['coords'] = $x('/html/body/div/div/table/tbody/tr[5]/td[4]')[0].innerHTML;
    }
    a['coords'] = a['coords'].replace(/\s*/g,'');
    if (npc && GM_getValue(c_prefix+'pnj_info',false)) get_xml('pnj', a);

    if (!npc) {
        a['owner'] = a['owner'].replace(/<\/?[^>]+>/gi, '')

    //    get_xml('userfleet', a);
    }
}


function MaFiche() {
    var a = Array();

    prefixpts = '/html/body/div[2]/div/div/div/center';
    prefixright = '/html/body/div[2]/div/div/div[2]';
    id_td = 4;

    player = $x(prefixright+'/table/tbody/tr[2]/td[4]')[0].innerHTML;

    if (player.toLowerCase() != GM_getValue(c_prefix+'user','').toLowerCase()) return;

    a['Titre'] = $x(prefixright+'/table/tbody/tr[3]/td[4]')[0].innerHTML;
    a['Race'] = $x(prefixright+'/table/tbody/tr[4]/td[4]')[0].innerHTML;

    a['Commerce'] = $x(prefixright+'/table[2]/tbody/tr[2]/td[3]')[0].innerHTML;
    a['Recherche'] = $x(prefixright+'/table[2]/tbody/tr[4]/td[3]')[0].innerHTML;
    a['Combat'] = $x(prefixright+'/table[2]/tbody/tr[6]/td[3]')[0].innerHTML;
    a['Construction'] = $x(prefixright+'/table[2]/tbody/tr[8]/td[3]')[0].innerHTML;
    a['Economie'] = $x(prefixright+'/table[2]/tbody/tr[10]/td[3]')[0].innerHTML;
    a['Navigation'] = $x(prefixright+'/table[2]/tbody/tr[12]/td[3]')[0].innerHTML;

    a['GameGrade'] = $x(prefixpts)[0].innerHTML;
    i = a['GameGrade'].indexOf('>')+1;
    j = a['GameGrade'].indexOf('<', i);
    a['GameGrade'] = a['GameGrade'].substr(i, j-i);

    a['POINTS'] = $x(prefixpts+'/table/tbody/tr[4]/td['+id_td+']/b')[0].innerHTML;
    a['pts_architecte'] = $x(prefixpts+'/table/tbody/tr[6]/td['+id_td+']')[0].innerHTML;
    a['pts_mineur'] = $x(prefixpts+'/table/tbody/tr[7]/td['+id_td+']')[0].innerHTML;
    a['pts_science'] = $x(prefixpts+'/table/tbody/tr[8]/td['+id_td+']')[0].innerHTML;
    a['pts_commercant'] = $x(prefixpts+'/table/tbody/tr[9]/td['+id_td+']')[0].innerHTML;
    a['pts_amiral'] = $x(prefixpts+'/table/tbody/tr[10]/td['+id_td+']')[0].innerHTML;
    a['pts_guerrier'] = $x(prefixpts+'/table/tbody/tr[11]/td['+id_td+']')[0].innerHTML;


    //    tmp = a['Commerce']+'-'+a['Recherche']+'-'+a['Combat']+'-'+a['Construction']+'-'+a['Economie']+'-'+a['Navigation'];
    //    tmp = a['POINTS']+'-'+a['pts_architecte']+'-'+a['pts_mineur']+'-'+a['pts_science']+'-'+a['pts_commercant']+'-'+a['pts_amiral']+'-'+a['pts_guerrier'];
    //    tmp = i+'--'+a['GameGrade']+'-'+a['Race']+'-'+a['Titre'];
    //        AddToMotd(serialize(a));
    get_xml('mafiche', a);
}

function ownuniverse () {
    var Planet = Array();
    var i = 3;
    var j = 0;
    var p = 0;
    var k = '';

    while (trim($x('/html/body/div[2]/div/div[3]/div/table/tbody/tr/td['+i+']')[0].innerHTML) != '')
    {
        Planet[p] = Array();
        Planet[p]['Coord'] = trim($x('/html/body/div[2]/div/div[3]/div/table/tbody/tr/td['+i+']')[0].innerHTML);
        i += 2;
        p++;
    }
    //    AddToMotd(p+' Planets', '<hr/>');

    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['Name'] = trim($x('/html/body/div[2]/div/div[2]/table/tbody/tr/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['control'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[3]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['communication'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[5]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['university'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[7]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['technology'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[9]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['gouv'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[11]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['defense'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[13]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['shipyard'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[15]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['spacedock'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[17]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['bunker'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[19]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['tradepost'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[21]/td['+i+']')[0].innerHTML);
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j]['ressource'] = trim($x('/html/body/div[2]/div/div[3]/div/div/table/tbody/tr[23]/td['+i+']')[0].innerHTML);

    k='current_';// Stock sur planète
    div='2';
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Titane'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[3]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Cuivre'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[5]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Fer'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[7]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Aluminium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[9]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Mercure'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[11]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Silicium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[13]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Uranium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[15]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Krypton'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[17]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Azote'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[19]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Hydrogene'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[21]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));

    k='';// Production par heure
    div='3';
    for (i=3,j=0; j<p; i+=2,j++)  ///html/body/div[2]/div/div[3]/div/div[3]/table/tbody/tr[3]/td[3]
        Planet[j][k+'Titane'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[3]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Cuivre'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[5]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Fer'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[7]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Aluminium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[9]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Mercure'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[11]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Silicium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[13]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Uranium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[15]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Krypton'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[17]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Azote'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[19]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Hydrogene'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[21]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));

    k='bunker_';// Ressources dans le bunker
    div='4';
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Titane'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[3]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Cuivre'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[5]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Fer'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[7]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Aluminium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[9]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Mercure'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[11]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Silicium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[13]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Uranium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[15]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Krypton'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[17]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Azote'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[19]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Hydrogene'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[21]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));

    k='sell_';// Ventes par jours
    div='5';
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Titane'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[3]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Cuivre'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[5]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Fer'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[7]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Aluminium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[9]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Mercure'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[11]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Silicium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[13]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Uranium'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[15]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Krypton'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[17]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Azote'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[19]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));
    for (i=3,j=0; j<p; i+=2,j++)
        Planet[j][k+'Hydrogene'] = trim($x('/html/body/div[2]/div/div[3]/div/div['+div+']/table/tbody/tr[21]/td['+i+']')[0].innerHTML.replace(/\.*/g, ''));

    //    var key = k+'Hydrogene';
    //    data = key+': ';
    //    for (j=0; j<p;j++) data += Planet[j][key]+'¤ ';
    //    data +=' @'+p;
    //    AddToMotd(data);
    get_xml('ownuniverse', serialize(Planet));
}

function troop_battle() {

    var inf = Array();
    inf['date'] = $x('/html/body/div[2]/div/div/table[2]/tbody/tr/td/table/tbody/tr[2]/td[4]')[0].innerHTML;
    inf['coords'] = $x('/html/body/div[2]/div/div/table[2]/tbody/tr/td/table/tbody/tr[3]/td[4]')[0].innerHTML;



    reg= /shiplist\[(\d+)]\['caption'\] = '([^']+)'/g;
    m = document.documentElement.innerHTML.match(reg);
    var IdToPlayer=new Array();
    for (i = 0; i < m.length; i++) {
        m[i].search(reg);
        IdToPlayer[RegExp.$1] = RegExp.$2;
    }

    reg = /Array\(\'dmg\',(\d+),(\d+),(\d+)\);/g;
    m = document.documentElement.innerHTML.match(reg);
    if (m == null) {
        inf['nb_assault'] = 0;
        inf['pertes'] = new Array();
    } else {
        inf['nb_assault'] = m.length;
        var pertes=new Array();
        for (i = 0; i < m.length; i++) {
            m[i].search(reg);
            //            if (i==0) iddefenseur=RegExp.$2;
            if (typeof pertes[IdToPlayer[RegExp.$2]] == 'undefined')
                pertes[IdToPlayer[RegExp.$2]] = parseInt(RegExp.$3);
            else
                pertes[IdToPlayer[RegExp.$2]] += parseInt(RegExp.$3);
        }
    }
    inf['pertes'] = serialize(pertes);
    //    reg= eval('/shiplist\\['+iddefenseur+"\\]\\['color'\\] = '(.*)'/");
    //    m = document.documentElement.innerHTML.match(reg);
    //    AddToMotd('Def color: '+RegExp.$1);

    reg= /shiplist\[(\d+)\]\['color'\] = 'green'/g;
    m = document.documentElement.innerHTML.match(reg);
    var arr=new Array();
    for (i = 0; i < m.length; i++) {
        m[i].search(reg);
        id = RegExp.$1;
        reg2= eval("/shiplist\\["+id+"\\]\\['caption'\\] = '([^']+)'/");
        document.documentElement.innerHTML.match(reg2);
        Player = RegExp.$1;
        reg2= eval("/shiplist\\["+id+"\\]\\['health_max'\\] = '([^']+)'/");
        document.documentElement.innerHTML.match(reg2);
        troops = parseInt(RegExp.$1);

        arr[Player] = troops;
    }
    inf['left'] = serialize(arr);

    reg= /shiplist\[(\d+)\]\['color'\] = 'red'/g;
    m = document.documentElement.innerHTML.match(reg);
    arr=new Array();
    for (i = 0; i < m.length; i++) {
        m[i].search(reg);
        id = RegExp.$1;
        reg2= eval("/shiplist\\["+id+"\\]\\['caption'\\] = '([^']+)'/");
        document.documentElement.innerHTML.match(reg2);
        Player = RegExp.$1;
        reg2= eval("/shiplist\\["+id+"\\]\\['health_max'\\] = '([^']+)'/");
        document.documentElement.innerHTML.match(reg2);
        troops = parseInt(RegExp.$1);

        arr[Player] = troops;
    }
    inf['right'] = serialize(arr);

    //    AddToMotd(inf['coords']);
    get_xml('troop_battle', inf);
}

function troop_log (mode) {

    var inf = Array();
    inf['date'] = $x('/html/body/div[2]/div/div/table/tbody/tr[4]/td[4]')[0].innerHTML;
    inf['msg'] = $x('/html/body/div[2]/div/div/table[2]/tbody/tr[2]/td')[0].innerHTML.replace(/<[^<]*>/g, '\n');
    inf['mode'] = mode;
    get_xml('troop_log', inf);
}

function gamelog_spooler () {
    ident = $x('/html/body/div[2]/div/div/table/tbody/tr[2]/td[4]')[0].innerHTML;

    if (ident.indexOf(i18n[c_game_lang]['troop_log_att']) == 0) troop_log('attacker');
    if (ident.indexOf(i18n[c_game_lang]['troop_log_def']) == 0) troop_log('defender');
}

function Options() {
    var area = $x('/html/body/div[2]/div/div/div/form/table[2]')[0];

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