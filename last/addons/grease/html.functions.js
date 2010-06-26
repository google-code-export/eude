
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


function AddGameLog(text) {
    var log = null;
    try {
        log = unsafeWindow.top.document.getElementById('gamelog');
    } catch(e) {
        GM_log('AddGameLog Err:'+text);
    }
    if (log != null) log.innerHTML = text+'<br/>'+log.innerHTML;
}

function AddToMotd(text,sep) {
    var chat_motd = null;
    try {
        chat_motd = unsafeWindow.top.document.getElementById('chat_motd');
    } catch(e) {
        GM_log('AddToMotd Err:'+text);
    }
    if (!sep) sep = '<br/>';
    var tmp = text+sep+chat_motd.innerHTML;
    chat_motd.innerHTML = tmp.substr(0,4000);
}