/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 */
var sql_standby=0;
var sql_file;
var sql_cur;
var sql_max;

function sql_run(file, max) {
    $('sqlbatchmsg').update(i18n.Ajax.onCreate);
    sql_file = file;
    sql_cur = 0;
    sql_max = max;
    _sql_run();
}
function _sql_run() {
    clearTimeout(sql_standby);
    new Ajax.Request('./sqlbatch.php',{
        method:'post',
        parameters:{
            file:sql_file,
            username:$('username').value,
            password:$('password').value,
            empire:$('empire').value,
            board:$('board').value
        },
        onSuccess:function(t){
            var xml = '';
            if (Prototype.Browser.Gecko) xml = t.responseXML; else xml = t.responseText.ToXML();
            if (xml==null) {
                $('sqlbatchmsg').update(i18n.Ajax.XML_Error);
                return _sql_error();
            }
            $('sqlbatchmsg').update(DataEngine.GetNode(xml,'msg'));
            haserror = DataEngine.GetNode(xml,'haserror');
            done=DataEngine.GetNode(xml,'done');
            if (done == '1') return sql_done();
            if (haserror == '1') return _sql_error();
            maj_progress();
            sql_standby = setTimeout('_sql_run()', 500);
            return true;
        },
        onFailure:function(t){
            $('sqlbatchmsg').update(i18n.Ajax.onFailure);
        }
    }
    );
}
function sql_done() {
    maj_progress();
    sql_standby = setTimeout('_sql_done()', 500);
}
function _sql_done() {
    clearTimeout(sql_standby);
    $('sqlbatchmsg').update('Finalisation...');
    _final_install();
}
function _sql_error() {
    clearTimeout(sql_standby);
}
function maj_progress() {
    sql_cur++;
    percent = Math.round((sql_cur/sql_max)*10000)/100;
    $('sqlbatchmsgstep').update('Progression: '+percent+'%');    
}

function test_mysql() {
    new Ajax.Request('./install.conf.php',{
        method:'post',
        parameters:{
            act:'testmysqlserver',
            sqlserver:$('sqlserver').value,
            sqluser:$('sqluser').value,
            sqlpass:$('sqlpass').value,
            sqlbase:$('sqlbase').value
        },
        onSuccess:function(t){
            var xml = '';
            if (Prototype.Browser.Gecko) xml = t.responseXML; else xml = t.responseText.ToXML();
            if (xml==null) {
                alert(i18n.Ajax.XML_Error);
                return _sql_error();
            }
            nextstep=DataEngine.GetNode(xml,'nextstep');
            if (nextstep=='1') {
                $('sqlbatchmsg').update('<a href="javascript:run_install();">Lancer l\'installation</a>');
                $('sqlbatchmsgstep').update('');
            } else
                $('sqlbatchmsg').update(DataEngine.GetNode(xml,'msg'));
        },
        onFailure:function(t){
            alert(i18n.Ajax.onFailure);
        }
    }
    );
}

function run_install() {
    new Ajax.Request('./install.conf.php',{
        method:'post',
        parameters:{
            act:'startinstall',
            sqlserver:$('sqlserver').value,
            sqluser:$('sqluser').value,
            sqlpass:$('sqlpass').value,
            sqlbase:$('sqlbase').value,
            sqlprefix:$('sqlprefix').value,
            sqlrooturl:$('sqlrooturl').value
        },
        onSuccess:function(t){
            var xml = '';
            if (Prototype.Browser.Gecko) xml = t.responseXML; else xml = t.responseText.ToXML();
            if (xml==null) {
                alert(i18n.Ajax.XML_Error);
                return _sql_error();
            }
            nextstep=DataEngine.GetNode(xml,'nextstep');
            if (nextstep=='1') {
                $('sqlbatchmsg').update('Initialisation sur le serveur mysql...');
                if ($('sqlmax').value=='0')
                    _sql_done();
                else
                    sql_run('install', $('sqlmax').value);
            } else
                $('sqlbatchmsg').update(DataEngine.GetNode(xml,'msg'));
        },
        onFailure:function(t){
            alert(i18n.Ajax.onFailure);
        }
    }
    );
}

function _final_install() {
    new Ajax.Request('./install.conf.php',{
        method:'post',
        parameters:{
            act:'endinstall'
        },
        onSuccess:function(t){
            var xml = '';
            if (Prototype.Browser.Gecko) xml = t.responseXML; else xml = t.responseText.ToXML();
            if (xml==null) {
                alert(i18n.Ajax.XML_Error);
                return _sql_error();
            }
            nextstep=DataEngine.GetNode(xml,'nextstep');
            if (nextstep=='1')
                location.href='../';
            else
                $('sqlbatchmsg').update(DataEngine.GetNode(xml,'msg'));
        },
        onFailure:function(t){
            alert(i18n.Ajax.onFailure);
        }
    }
    );
}

function DataEngine () {}

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
