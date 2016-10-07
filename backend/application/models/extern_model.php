<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/3/10
 * Time: 上午10:00
 */

class Extern_Model extends CI_Model{
    const TAB_TIME = "time";
    const TAB_DATE = "date";
    const TAB_CLASSROOM = "classroom";
    const TAB_ARRANGEMENT = "arrangement";


    /**
     * 获取安排传给接口
     * @return mixed
     */
    function  get_arrangement(){
        $tody_id = date("w");
        $rooms = $this->db->get(self::TAB_CLASSROOM)->result();
        $times = $this->db->get(self::TAB_TIME)->result();
        $res["times"] = $times;
        $res["infos"] = array();
        $res["date"] = date("Y-m-d");
        foreach ($rooms as $room){
            $temp_room = $room;
            $temp_room->room_id = $room->id;
            $temp_room->time_infos = array();
            foreach ($times as $time){
                $query = $this->db->get_where(self::TAB_ARRANGEMENT, array(
                    "date_id" => $tody_id,
                    "time_id" => $time->id,
                    "classroom_id" => $room->id
                ));

                if ($query->num_rows() > 0){
                    array_push($temp_room->time_infos, array(
                        "time" => $time,
                        "content" => $query->row()->content,
                        "type" => $query->row()->type
                    ));
                }
            }
            array_push($res["infos"], $temp_room);
        }
        return $res;
    }

}