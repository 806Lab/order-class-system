<?php
class Login_Model extends CI_Model{
    
    const TAB_USER = 'user';
    const TAB_ADMIN = "admin";
    
    /**
     * 判断用户是否存在
     * @param string $username
     * @return boolean
     */
    function  is_user_exsit($username){
        $query = $this->db->get(self::TAB_USER);
        foreach ($query->result() as $row){
            if($row->username == $username){
                return true;
            }
        }
        return false;
    }
    
    /**
     * 判断用户的密码是否正确
     * @param string $username
     * @param string $password
     * @return boolean
     */
    function is_user_password_correct($username, $password){
        if (!$this->is_user_exsit($username)){
            return false;
        }
        $query = $this->db->get(self::TAB_USER);
        foreach ($query->result() as $row){
            if($row->username == $username){ //用户名和密码一样
                return $row->username == $password;
            }
        }
        return false;
    }
    
    /**
     * 判断管理员是否存在
     * @param string $username
     * @return boolean
     */
    function is_admin_exist($username){
        $query = $this->db->get(self::TAB_ADMIN);
        foreach ($query->result() as $row){
            if($row->username == $username){
                return true;
            }
        }
        return false;
    }
     
    /**
     * 判断管理员密码是否正确
     * @param string $username
     * @param string $password
     * @return boolean
     */
    function is_admin_password_correct($username, $password){
        if (!$this->is_admin_exist($username)){
            return false;
        }
        $query = $this->db->get(self::TAB_ADMIN);
        foreach ($query->result() as $row){
            if($row->username == $username){
                return $row->password == $password;
            }
        }
        return false;
    }
}