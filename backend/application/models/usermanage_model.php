<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 10/6/16
 * Time: 1:08 PM
 */

class Usermanage_Model extends CI_Model{

    const TAB_USER = "user";
    
    function get_user_list($offset, $limit)
    {
        $this->db->limit($limit, $offset);
        $query = $this->db->get(self::TAB_USER);
        return $query->result();
    }
    
    function get_user_count()
    {
        $query = $this->db->get(self::TAB_USER);
        return $query->num_rows();
    }

    function add_user()
    {

    }

    function delete_user()
    {

    }

    function update_user()
    {

    }

}