<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 10/6/16
 * Time: 1:08 PM
 */

class Usermanage_Model extends CI_Model{

    const TAB_USER = "user";
    const TAB_ORDER = "order";

    function search_users($username, $name, $unit_info, $offset, $limit)
    {
        if($username != null){
            $this->db->like('username', $username);
        }
        if($name != null){
            $this->db->like('name', $name);
        }
        if($unit_info != null){
            $this->db->like('unit_info', $unit_info);
        }
        $this->db->limit($limit, $offset);
        $query = $this->db->get(self::TAB_USER);
        return $query->result();

    }

    function search_users_count($username, $name, $unit_info)
    {
        if($username != null){
            $this->db->like('username', $username);
        }
        if($name != null){
            $this->db->like('name', $name);
        }
        if($unit_info != null){
            $this->db->like('unit_info', $unit_info);
        }
        $query = $this->db->get(self::TAB_USER);
        return $query->num_rows();
    }

    function add_user($username, $name, $unit_info, $mobile_number, $user_type)
    {
        $data = array(
            'username' => $username,
            'name' => $name,
            'unit_info' => $unit_info,
            'mobile_number' => $mobile_number,
            'user_type' => $user_type,
            'last_order_time' => 0,
        );
        $this->db->insert(self::TAB_USER, $data);
    }
    
    function is_user_exist($username)
    {
        return $this->db->get_where(self::TAB_USER, ['username'=>$username])->num_rows() != 0;
    }

    function delete_user($username)
    {
        $this->delete_orders_by_username($username);
        $this->db->where(array("username" => $username));
        $this->db->delete(self::TAB_USER);
    }

    function update_user($username, $name, $unit_info, $mobile_number, $user_type)
    {
        $this->db->where(array("username" => $username));
        $data = array(
            'name' => $name,
            'unit_info' => $unit_info,
            'mobile_number' => $mobile_number,
            'user_type' => $user_type,
            'last_order_time' => 0,
        );
        $this->db->update(self::TAB_USER, $data);
    }


    private function delete_orders_by_username($username)
    {
        $this->db->where(array("username" => $username));
        $this->db->delete(self::TAB_ORDER);
    }

}