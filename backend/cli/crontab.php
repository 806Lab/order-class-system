<?php

ini_set('date.timezone','Asia/Shanghai');

require_once './vendor/autoload.php';

$db = new \MysqliDb ('127.0.0.1', 'root', 'root', 'ustb_class_order');

$db->where('type', 0);
$arrangements = $db->get("arrangement");


$now = strtotime(date("Y-m-d",strtotime("+0 day")));

foreach ($arrangements as $arrangement) {
//    var_dump($arrangement);
    if ($arrangement["end_date"] != "") {
        $day_time = strtotime($arrangement["end_date"]);
        if ($day_time < $now) {
            $db->where("id", $arrangement["id"]);
            $db->delete("arrangement");
            echo "deleting id:".$arrangement["id"]." content:".$arrangement["content"]."\n";
        }
    }
}