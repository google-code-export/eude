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
            file:sql_file
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
            sql_standby = setTimeout('_sql_run()', 700);
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
    sql_standby = setTimeout('_sql_done()', 700);
}
function _sql_done() {
    clearTimeout(sql_standby);
    $('sqlbatchmsg').update('Terminé.');
}
function _sql_error() {
    clearTimeout(sql_standby);
}
function maj_progress() {
    sql_cur++;
    percent = Math.round((sql_cur/sql_max)*10000)/100;
    $('sqlbatchmsgstep').update('Progression: '+percent+'%');    
}