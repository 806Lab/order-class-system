<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verify extends CI_Controller {
    const TAB_ORDER = "order";
    const TAB_TIME = "time";
    const TAB_CLASSROOM = "classroom";
    const TAB_GUID = "guid";
    const TAB_USER = "user";
    
    function __construct(){
        parent::__construct();
        $this->load->model("Response_Model", "response_model");
        $this->load->model("Common_Model", "common_model");
    }
    
    /**
     * 获取二维码信息
     */
    function get_qrcode_info(){
        $guid = $this->input->get_post("guid");
        if ($guid === FALSE) die();
        $guid_query = $this->db->get_where(self::TAB_GUID, array("guid" => $guid));
        if (!($guid_query->num_rows() >0)){//如果不存在二维码
            $this->response_model->show_json(2, "二维码非法");die();
        }
        $order_id = $guid_query->row()->order_id;
        $this->db->where(array('id' => $order_id));
        $query = $this->db->get(self::TAB_ORDER);
        if (!($query->num_rows() > 0)){
            $this->response_model->show_json(1, "借教室请求不存在");die();
        }
        $order = $query->row();
        if (($order->status == 2) || ($order->status == 0)){
            $this->response_model->show_json(2, "此订单是非法请求");die();
        }
        if ($order->status != 1){
            $this->response_model->show_json(1, "此二维码已使用，无法重复使用");die();
        }
        $order->room = $this->db->get_where(self::TAB_CLASSROOM,array('id'=>$order->classroom_id))->row();
        $order->time = $this->db->get_where(self::TAB_TIME,array('id'=>$order->time_id))->row();
        $order->user = $this->db->get_where(self::TAB_USER, array('username' => $order->username))->row();
        $this->response_model->show_json(0, 'success', $order);
    }
    
    
    /**
     * 请求验证二维码的地址
     */
    function nxnjznjxnsaxiahiueiqiojw(){
        $guid = $this->input->get_post("guid");
        if ($guid === FALSE) die();
        //$this->Common_Model->check_out_args($guid);
        $guid_query = $this->db->get_where(self::TAB_GUID, array("guid" => $guid));
        if (!($guid_query->num_rows() >0)){//如果不存在二维码
            $this->response_model->show_json(2, "二维码非法");die();
        }
        $order_id = $guid_query->row()->order_id;
        $this->db->where(array('id' => $order_id));
        $query = $this->db->get(self::TAB_ORDER);
        if (!($query->num_rows() > 0)){
            $this->response_model->show_json(1, "借教室请求不存在");die();
        }
        $order = $query->row();
        if ($order->status == 2){
            $this->response_model->show_json(1, "此订单已过期，无法重复使用");die();
        }
        
        if ($order->status != 1){
            $this->response_model->show_json(2, "此订单是非法请求");die();
        }
        $this->db->where(array('id' => $order_id));
        $this->db->update(self::TAB_ORDER, array('status' => 3));
        $this->response_model->show_json(0, '验证成功，借教室请求已使用', $order);
    }
    
}