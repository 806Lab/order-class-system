<?php
class User_Model extends CI_Model{
    const TAB_USER = "user";
    const TAB_ORDER = "order";
    const TAB_CLASSROOM = "classroom";
    const TAB_TIME = "time";
    const TAB_ARRANGEMENT = "arrangement";


    /**
     * 获取基本信息
     * @param $username
     * @return array|string
     */
    function get_info($username){
        $row = $this->db->get_where(self::TAB_USER, array("username" => $username)) ->row();
        if (!isset($row->name)){
            return "nil";
        }
        return array(
            "username" => $username,
            "name" => $row->name,
            "unit_info" => $row->unit_info,
            "last_order_time" => $row->last_order_time
        );
    }

    /**
     * 判断教室是否合法
     * @param $classroom_id
     * @return boolean
     */
    function is_classroom_correct($classroom_id){
        $query = $this->db->get_where(self::TAB_CLASSROOM, array("id" => $classroom_id));
        return $query->num_rows() > 0;
    }

    /**
     * 判断提交时间是否合法
     * @param $date
     * @param $time_id
     * @param $classroom_id
     * @return boolean
     */
    function is_time_avalible($date ,$time_id, $classroom_id){
        $date_id = date("w", strtotime($date));
        $times = $this->get_time_by_date_id_and_classroom_id($date_id, $classroom_id);
        foreach ($times as $time){
            if ($time->id == $time_id){
                return true;
            }
        }
        return false;
    }

    /**
     * 添加一条订单
     * @param $uname
     * @param $date
     * @param $time_id
     * @param $classroom_id
     * @param $reason
     */
    function add_order($uname, $date ,$time_id, $classroom_id, $reason){
        $data = array(
            'id' => '',
            'username' => $uname,
            'date_id' => date("w", strtotime($date)),
            'date' => $date,
            'time_id' => $time_id,
            'classroom_id' => $classroom_id,
            'status' => 0,      //0 为待审核， 1为已通过，2为未通过，3为已使用
            'reason' => $reason,
            "commit_time" =>time(),
            "handle_time" => 0
        );
        $this->db->insert(self::TAB_ORDER, $data);
    }


    /**
     * 根据limit和offset获取订单
     * @param $uname
     * @param $offset
     * @param $limit
     * @return array
     */
    function get_order($uname, $offset, $limit){
        $this->db->order_by("commit_time", "desc");
        $this->db->where(array('username' => $uname));
        $this->db->limit($limit, $offset);
        $query = $this->db->get(self::TAB_ORDER);
        $num_query = $this->db->get(self::TAB_ORDER);
        $result = array();
        foreach ($query->result() as $row){
            $temp_row = $row;
            $classrooms =  $this->get_classroom_by_id($row->classroom_id);
            if (count($classrooms) > 0){
                $temp_row->classroom =$classrooms[0];
            }
            $times = $this->get_time_by_id($row->time_id);
            if (count($times) > 0){
                $temp_row->time = $times[0];
            }
            array_push($result, $temp_row);
        }

        return array(
            "count" => $num_query ->num_rows(),
            "infos" => $result
        );
    }


    /**
     * 根据id获取指定教室
     * @param $id
     * @return mixed
     */
    function get_classroom_by_id($id){
        $query = $this->db->get_where(self::TAB_CLASSROOM, array('id' => $id));
        return $query->result();
    }


    /**
     * 获取时间段
     * @return array
     */
    function get_time(){
        return $this->db->get(self::TAB_TIME)->result();
    }

    /**
     * 根据日期和教室编号获取时间段
     * @param $date_id
     * @param $classroom_id
     * @return array
     */
    function get_time_by_date_id_and_classroom_id($date_id, $classroom_id){
        $this->db->where(array(
            "date_id" => $date_id,
            "classroom_id" => $classroom_id
        ));
        $arrange_query = $this->db->get(self::TAB_ARRANGEMENT);
        $exist_time_arr = array();
        foreach ($arrange_query->result() as $row){
            array_push($exist_time_arr, $row->time_id);
        }

        $query_res = $this->db->get(self::TAB_TIME)->result();
        $result = array();
        foreach($query_res as $row){
            if (!in_array($row->id, $exist_time_arr)){
                array_push($result, $row);
            }
        }
        return $result;
    }


    /**
     * 根据time_id和date_id获取教室
     * @param $date_id
     * @param $time_id
     * @return array
     */
    function get_classroom($date_id, $time_id){
        $this->db->where(array(
            "date_id" => $date_id,
            "time_id" => $time_id
        ));
        //找出所有符合情况的安排
        $arrange_query = $this->db->get(self::TAB_ARRANGEMENT);
        $exist_classroom_arr = array();
        foreach ($arrange_query->result() as $row){
            array_push($exist_classroom_arr, $row->classroom_id);
        }

        //获取所有的教室
        $query_res = $this->db->get(self::TAB_CLASSROOM)->result();
        $result = array();

        //把不在安排中的数据放到result中并返回
        foreach($query_res as $row){
            if (!in_array($row->id, $exist_classroom_arr)){
                array_push($result, $row);
            }
        }
        return $result;
    }

    /**
     * 判断该用户是否已经添加过该时间段了
     * @param $username
     * @param $date
     * @param $time_id
     * @param $classroom_id
     * @return bool
     */
    function is_time_have_added($username, $date, $time_id, $classroom_id){
        $query = $this->db->get_where(self::TAB_ORDER, array(
            "date" => $date,
            "username" => $username,
            "time_id" => $time_id,
            "classroom_id" => $classroom_id,
            "status" => 0
        ));

        $query2 = $this->db->get_where(self::TAB_ORDER, array(
            "date" => $date,
            "username" => $username,
            "time_id" => $time_id,
            "classroom_id" => $classroom_id,
            "status" => 1
        ));

        return ($query->num_rows() + $query2->num_rows()) > 0;
    }

    /**
     * 根据id获取指定时间段
     * @param $id
     * @return mixed
     */
    function get_time_by_id($id){
        $query = $this->db->get_where(self::TAB_TIME, array('id' => $id));
        return $query->result();
    }


    /**
     * 获取未处理或者未使用的教室数目，防止有人恶意提交订单
     * @param $username
     * @return int
     */
    function get_unhandled_or_unused_order_num($username){
        $query1 = $this->db->get_where(self::TAB_ORDER, array("username" => $username, "status" => 0));
        $query2 = $this->db->get_where(self::TAB_ORDER, array("username" => $username, "status" => 1));
        return $query1->num_rows() + $query2->num_rows();
    }

    /**
     * 判断order是否可以取消
     * @param int $id
     * @param string $username
     * @return boolean
     */
    function is_order_valid_cancel($id, $username){
        $row = $this->db->get_where(self::TAB_ORDER, array('id' => $id))->row();

        //如果此订单未被处理，并且是该用户的
        if (($row->status == 0) && ($row->username == $username)){
            return true;
        }
        return false;
    }

    /**
     * 删除一条order
     * @param int $id
     */
    function delete_order($id){
        $this->db->where('id', $id);
        $this->db->delete(self::TAB_ORDER);
    }

    /**
     * 获取从今天起往后的7天的日期
     */
    function get_date(){
        $tody_id = date("w");
        $res= array();
        for ($i = 0; $i < 7; $i++){
            $day_id = ($tody_id + $i) % 7;
            $day = date("Y-m-d",strtotime("+".$i." day"));
            array_push($res, array( "id" => $day_id,"day" => $day ));
        }
        return $res;
    }

}