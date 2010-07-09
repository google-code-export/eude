
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
        alert("XML error, disabling 'eude'...\n\n\n\nData Engine send:\n"+e.responseText);
        if (debug) return false;
        GM_setValue(c_prefix+'actived','0');
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
            GM_setValue(c_prefix+'troops_battle', GetNode(e.responseXML, 'GM_troops_battle')=='1'? true:false);
        }
        if (c_page!='/index.php') top.location.reload(true);
    }
    if (GetNode(e.responseXML, 'content')!='')
        AddToMotd(GetNode(e.responseXML, 'content'));

    return true;
}

var c_onerror = function(e) {
    AddGameLog('<span class="gamelog_raid">Fatal ('+e.status+'): Use in firefox only</span>');
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
    '&pass='+encodeURIComponent(md5(GM_getValue(c_prefix+'pass','')))+
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