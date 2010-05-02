<?php
/**
 * @Author: Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 **/


function GetUrl($host, $url, $headers=false) {

//    return 'nothing yet';

    if ( ($fp = fsockopen($host, 80, $errno, $errstr, 10)) === false) return false;

    $in  = "GET $url HTTP/1.1\r\n";
    $in .= "Host: $host\r\n";
    $in .= 'User-Agent: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
    $in .= 'Accept: '.$_SERVER['HTTP_ACCEPT']."\r\n";
    $in .= 'Accept-Language: '.$_SERVER['HTTP_ACCEPT_LANGUAGE']."\r\n";
    $in .= 'Accept-Charset: '.$_SERVER['HTTP_ACCEPT_CHARSET']."\r\n";
    $in .= "Accept-Encoding: chunked\r\n";
    if ($headers) $in .= $headers;
    $in .= "Connection: close\r\n\r\n";

    if (fwrite($fp, $in) === false) return false;

    $h=$b=$d='';
    while (!feof($fp)) {
        $d = fgets($fp, 128);
        if (stripos($h,"\r\n\r\n") === false)
            $h .= $d;
        else
            $b .= $d;
    }
    fclose($fp);
    if ( trim($b) == '') $b = false;
    return $b;
}