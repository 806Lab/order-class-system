<?php
class Admin_Model extends CI_Model{
    const TAB_ORDER = 'order';
    const TAB_USER = "user";
    const TAB_TIME = "time";
    const TAB_DATE = "date";
    const TAB_CLASSROOM = "classroom";
    const TAB_ARRANGEMENT = "arrangement";
    const TAB_GUID = "guid";


    /**
     * 根据用户名获取用户信息
     * @param $username
     * @return mixed
     */
    function get_userinfo_by_username($username){
        return $this->db->get_where(self::TAB_USER, array("username" => $username)) -> row();
    }


    /**
     * 获取时间段信息
     * @param $id
     * @return mixed
     */
    function get_time_by_id($id){
        $query = $this->db->get_where(self::TAB_TIME, array('id' => $id));
        return $query->row();
    }


    /**
     * 获取教室
     * @param $id
     * @return mixed
     */
    function get_classroom_by_id($id){
        $query = $this->db->get_where(self::TAB_CLASSROOM, array('id' => $id));
        return $query->row();
    }


    /**
     * 获取订单
     * @param $is_operated  // 0 未处理，1已经处理
     * @param $offset
     * @param $limit
     * @return array
     */
    function get_order($is_operated, $offset, $limit){
        $status_query = "status";
        if ($is_operated != 0) {
            $status_query .= " !=";
        }
        $this->db->order_by("commit_time", "desc");
        $this->db->where(array($status_query => 0));
        $this->db->limit($limit, $offset);
        $query = $this->db->get(self::TAB_ORDER);
        $res = array();
        foreach ($query->result() as $v){
            $val = $v;
            $val->user = $this->get_userinfo_by_username($v->username);
            $val->time = $this->get_time_by_id($v->time_id) -> time_info;
            $val->classroom = $this->get_classroom_by_id($v->classroom_id)->room_num;
            array_push($res, $val);
        }
        $num_query = $this->db->get_where(self::TAB_ORDER, array($status_query => 0));
        return array(
            "count" => $num_query->num_rows(),
            "infos" => $res
        );
    }


    /**
     * 通过id获取order
     * @param $order_id
     * @return mixed
     */
    function get_order_by_id($order_id){
        $query = $this->db->get_where(self::TAB_ORDER, array("id" => $order_id));
        return $query->row();
    }

    /**
     * 判断order是否存在
     * @param int $order_id
     * @return boolean
     */
    function is_order_exist($order_id){
        $this->db->where(array("id" => $order_id));
        if ($this->db->get(self::TAB_ORDER)->num_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * 判断order是否未处理
     * @param int $order_id
     * @return boolean
     */
    function is_order_unhandled($order_id){
        $this->db->where(array("id" => $order_id));
        return $this->db->get(self::TAB_ORDER)->row()->status == 0;
    }

    /**
     * 更新order的status和feedback
     * @param int $order_id
     * @param int $status
     * @param string $feedback
     */
    function update_order($order_id, $status, $feedback){
        if ($feedback === FALSE){
            $feedback = '';
        }
        $result_url = '';
        if ($status == 1){
            $result_url = base_url()."qrcode/".$this->generate_qrcode($order_id);
        }
        $data = array(
            'status' => $status,
            'feedback' => $feedback,
            'handle_time' => time(),
            'result_url' => $result_url
        );
        $this->db->where(array("id" => $order_id));
        $this->db->update(self::TAB_ORDER, $data);
    }

    /**
     * 生成验证二维码
     * @param int $order_id
     * @return string
     */
    function generate_qrcode($order_id){
        $this->load->helper("qrcode");
        $this->load->helper("guid");
        $guid = create_guid();
        $guid_pngname = create_guid().".png";
        $filepath = dirname(dirname(__DIR__))."/qrcode/".$guid_pngname;
        QRcode::png($guid, $filepath, QR_ECLEVEL_L, $size = 12);
        $data = array(
            'guid' =>$guid,
            'order_id' => $order_id
        );
        $this->db->insert(self::TAB_GUID, $data);
        return $guid_pngname;
    }

    /**
     * 获取安排
     * @param int $date_id
     * @return array
     */
    function  get_arrangement($date_id){
        $rooms = $this->db->get(self::TAB_CLASSROOM)->result();
        $times = $this->db->get(self::TAB_TIME)->result();
        $res = array();
        foreach ($rooms as $room){
            $temp_room = $room;
            $temp_room->room_id = $room->id;
            $temp_room->time_infos = array();
            foreach ($times as $time){
                $query = $this->db->get_where(self::TAB_ARRANGEMENT, array(
                    "date_id" => $date_id,
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
            array_push($res, $temp_room);
        }
        return $res;
    }

    /**
     * 判断安排是否存在
     * @param int $date_id
     * @param int $classroom_id
     * @param int $time_id
     * @return boolean
     */
    function is_arrangement_exist($date_id, $classroom_id, $time_id){
        $this->db->where(array(
            "date_id" => $date_id,
            "classroom_id" => $classroom_id,
            "time_id" =>$time_id
        ));
        $query = $this->db->get(self::TAB_ARRANGEMENT);
        if ($query->num_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * 添加一个安排
     * @param int $date_id
     * @param int $classroom_id
     * @param int $time_id
     * @param string $content
     * @param int $type
     * @return boolean
     */
    function  add_arrangement($date_id, $classroom_id, $time_id, $content, $type){
        if ($this->is_arrangement_exist($date_id, $classroom_id, $time_id)){
            return false;
        }
        $data = array(
            'id' =>'',
            'date_id' => $date_id ,
            'classroom_id' =>$classroom_id,
            'time_id' => $time_id,
            'content' => $content,
            'type' => $type,
            'end_date' => $this->get_date_by_id($date_id)
        );
        $this->db->insert(self::TAB_ARRANGEMENT, $data);
        return true;
    }

    /**
     * 删除一个安排
     * @param int $date_id
     * @param int $classroom_id
     * @param int $time_id
     * @return boolean
     */
    function  delete_arrangement($date_id, $classroom_id, $time_id){
        if (!$this->is_arrangement_exist($date_id, $classroom_id, $time_id)){
            return false;
        }
        $this->db->where(array(
            "date_id" => $date_id,
            "classroom_id" => $classroom_id,
            "time_id" =>$time_id
        ));
        $this->db->delete(self::TAB_ARRANGEMENT);
        return true;
    }

    /**
     * 更新安排
     * @param int $date_id
     * @param int $classroom_id
     * @param int $time_id
     * @param string $content
     * @param int $type
     * @return boolean
     */
    function  update_arrangement($date_id, $classroom_id, $time_id, $content, $type){
        if (!$this->is_arrangement_exist($date_id, $classroom_id, $time_id)){
            return false;
        }
        $this->db->where(array(
            "date_id" => $date_id,
            "classroom_id" => $classroom_id,
            "time_id" =>$time_id
        ));
        $data = array(
            'content' => $content,
            'type' => $type
        );
        $this->db->update(self::TAB_ARRANGEMENT, $data);
        return true;
    }


    function clean_outdated_orders() {
        $times = [
            " 0:00",
            " 8:00",
            " 9:55",
            " 13:30",
            " 15:20",
            " 17:10",
            " 19:30",
            " 21:30"
        ];

        $data = $this->get_order(0, 0, 1000); //获取未处理的订单
        $orders = $data["infos"];

        $now = strtotime("now");

        $count = 0;

        foreach ($orders as $order) {
            $t = strtotime($order->date.$times[$order->time_id]);
            if ($t < $now) {
                $this->db->where(array("id"=>$order->id));
                $this->db->delete(self::TAB_ORDER);
                $count++;
            }
        }
        return $count;
    }

    function get_date_by_id($id){
        $tody_id = date("w");
        for ($i = 0; $i < 7; $i++){
            $day_id = ($tody_id + $i) % 7;
            $day = date("Y-m-d",strtotime("+".$i." day"));
            if ($id == $day_id) {
                return $day;
            }
        }
        return "";
    }
}