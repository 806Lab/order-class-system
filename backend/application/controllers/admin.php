<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->Model("Response_Model", "response_model");
        $this->load->Model("Admin_Model", "admin_model");
        $this->load->Model("User_Model", "user_model");
        $this->load->Model("Common_Model", "common_model");
        $this->load->Model("Usermanage_Model", "usermanage_model");
        if(!$this->session->userdata('is_admin')){
            $this->response_model->show(2, "你还没有登陆");die();
        }
    }

    /**
     * 获取七天内的日期和星期
     */
    function get_date(){
        $this->response_model->show(0, "success", $this->user_model->get_date());
    }

    /**
     * 获取教室请求
     */
    function get_order(){
        $is_operated = $this->input->get_post("is_operated", TRUE);
        $offset = $this->input->get_post("offset", TRUE);
        $limit = 50;
        $this->common_model->check_out_args($is_operated, $offset);
        $this->response_model->show(0, "success", $this->admin_model->get_order($is_operated, $offset, $limit));
    }

    /**
     * 处理教室请求
     */
    function handle_order(){
        $order_id = $this->input->get_post("order_id", TRUE);
        $status = $this->input->get_post("status", TRUE); //1
        $feedback = $this->input->get_post("feedback", TRUE); //可以为空
        $this->common_model->check_out_args($order_id, $status);
        if ( !(($status == 1) || ($status == 2))){  //1为同意,2为拒绝
            $this->response_model->show(2, "请求非法");die();
        }
        if (!$this->admin_model->is_order_exist($order_id)){
            $this->response_model->show(2, "非法请求，订单不存在");die();
        }
        if (!$this->admin_model->is_order_unhandled($order_id)){
            $this->response_model->show(1, "已经处理的订单无法处理");die();
        }

        // 处理请求
        if ($status == 1){ // if tatus =1, add order to arrangement
            // handle the order when adding the arrangement
            $order = $this->admin_model->get_order_by_id($order_id);//获取order
            $date_id = $order->date_id;
            $classroom_id = $order->classroom_id;
            $time_id = $order->time_id;
            $user = $this->admin_model->get_userinfo_by_username($order->username);//获取申请人用户信息

            //判断安排是否存在
            if($this->admin_model->is_arrangement_exist($date_id, $classroom_id, $time_id)) {
                $this->response_model->show(1, "已经添加了安排，请删除后再添加");die();
            }

            $theUserName =$user ->name;
            $theUserUnitInfo = $user->unit_info;
            $content = $theUserName. "（".$theUserUnitInfo."）（".$order->reason."）";

            $this->admin_model->add_arrangement($date_id, $classroom_id, $time_id, $content, 0);
        }
        $this->admin_model->update_order($order_id, $status, $feedback);
        $this->response_model->show_success();
    }

    /**
     * 获取安排表
     */
    function get_arrangement(){
        $date_id = $this->input->get_post("date_id", TRUE);
        $this->common_model->check_out_args($date_id);
        $this->response_model->show(0, "success", $this->admin_model->get_arrangement($date_id));
    }

    /**
     * 添加一个安排
     */
    function add_arrangement(){
        $classroom_id = $this->input->get_post("classroom_id", TRUE);
        $time_id = $this->input->get_post("time_id", TRUE);
        $date_id = $this->input->get_post("date_id", TRUE);
        $content = $this->input->get_post("content", TRUE);
        $type = $this->input->get_post("type", TRUE);
        $this->common_model->check_out_args($classroom_id, $time_id, $date_id, $content, $type);
        if ( $this->admin_model->add_arrangement($date_id, $classroom_id, $time_id, $content, $type))   {
            $this->response_model->show_success();
        }else{
            $this->response_model->show(1, "已经添加了安排，不能重复添加");
        }
    }

    /**
     * 删除一条安排
     */
    function delete_arrangement(){
        $classroom_id = $this->input->get_post("classroom_id", TRUE);
        $time_id = $this->input->get_post("time_id", TRUE);
        $date_id = $this->input->get_post("date_id", TRUE);
        $this->common_model->check_out_args($classroom_id, $time_id, $date_id);
        if ($this->admin_model->delete_arrangement($date_id, $classroom_id, $time_id)){
            $this->response_model->show_success();
        }else{
            $this->response_model->show(1, "还没有添加安排，不能删除");
        }
    }

    /**
     * 更新一条安排
     */
    function update_arrangement(){
        $classroom_id = $this->input->get_post("classroom_id", TRUE);
        $time_id = $this->input->get_post("time_id", TRUE);
        $date_id = $this->input->get_post("date_id", TRUE);
        $content = $this->input->get_post("content", TRUE);
        $type = $this->input->get_post("type", TRUE);
        $this->common_model->check_out_args($classroom_id, $time_id, $date_id, $content, $type);
        if($this->admin_model->update_arrangement($date_id, $classroom_id, $time_id, $content, $type)){
            $this->response_model->show_success();
        }else{
            $this->response_model->show(1, "还没有添加安排，不能更新");
        }
    }


    /**
     * 获取用户列表
     */
    function search_users() {
        $offset = $this->input->get_post("offset", TRUE);
        $search_type = $this->input->get_post("search_type", TRUE);
        $search_content = $this->input->get_post("search_content", TRUE);
        $this->common_model->check_out_args($offset, $search_content, $search_type);

        $username = null;
        $name = null;
        $unit_info = null;
        if ($search_type == '1') {          //按照学号搜索
            $username = $search_content;
        } elseif ($search_type == '2') {    //按照姓名搜索
            $name = $search_content;
        } elseif ($search_type == '3') {    //按照单位来搜索
            $unit_info = $search_content;
        }                                   // seach_type = 0 为默认查找
        $limit = 10;
        $data = [
            "users" => $this->usermanage_model->search_users($username, $name, $unit_info, $offset, $limit),
            "count" => $this->usermanage_model->search_users_count($username, $name, $unit_info)
        ];
        $this->response_model->show(0, "", $data);
    }

    /**
     * 添加新用户
     */
    function add_user(){
        $username = $this->input->get_post("username", TRUE);
        $name = $this->input->get_post("name", TRUE);
        $unit_info = $this->input->get_post("unit_info", TRUE);
        $mobile_number = $this->input->get_post("mobile_number", TRUE);
        $user_type = $this->input->get_post("user_type", TRUE);
        $this->common_model->check_out_args($username, $name, $unit_info, $mobile_number, $user_type);

        if ($this->usermanage_model->is_user_exist($username)){
            $this->response_model->show_error("用户已存在,无法重复添加");die();
        }
        $this->usermanage_model->add_user($username, $name, $unit_info, $mobile_number, $user_type);
        $this->response_model->show_success("添加成功");
    }

    /**
     * 删除用户
     */
    function delete_user(){
        $username = $this->input->get_post("username", TRUE);
        $this->common_model->check_out_args($username);

        if (!$this->usermanage_model->is_user_exist($username)){
            $this->response_model->show_error("用户不存在,无法删除");die();
        }
        $this->usermanage_model->delete_user($username);
        $this->response_model->show_success("删除成功");
    }

    /**
     * 更新用户
     */
    function update_user(){
        $username = $this->input->get_post("username", TRUE);
        $name = $this->input->get_post("name", TRUE);
        $unit_info = $this->input->get_post("unit_info", TRUE);
        $mobile_number = $this->input->get_post("mobile_number", TRUE);
        $user_type = $this->input->get_post("user_type", TRUE);
        $this->common_model->check_out_args($username, $name, $unit_info, $mobile_number, $user_type);

        if (!$this->usermanage_model->is_user_exist($username)){
            $this->response_model->show_error("用户不存在,无法更新");die();
        }
        $this->usermanage_model->update_user($username, $name, $unit_info, $mobile_number, $user_type);
        $this->response_model->show_success("更新成功");
    }
}