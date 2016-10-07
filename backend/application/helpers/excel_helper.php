<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/4/29
 * Time: 下午3:34
 */
function excelTime($date, $time = false) {
    $date=$date>25568?$date+1:25569;
    /*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/
    $ofs=(70 * 365 + 17+2) * 86400;
    $date = date("Y-m-d",($date * 86400) - $ofs).($time ? " 00:00:00" : '');
    return $date;
}

function gbk_to_utf8($str){
    return mb_convert_encoding($str, "UTF-8", "GBK");
}