<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("Response_Model", "response_model");
        $this->load->model("User_Model", "user_model");
        $this->load->model("Common_Model", "common_model");
        if(!$this->session->userdata('is_user')){
            $this->response_model->show(2, "你还没有登陆");die();
        }
    }

    function index(){
        $this->get_info();
    }

    /**
     * 获取用户基本信息
     */
    function get_info(){
        $username = $this->session->userdata('username');
        $res = $this->user_model->get_info($username);
        $this->response_model->show(0, 'success', $res);
    }

    /**
     * 获取可选日期
     */
    function get_date(){
        $this->response_model->show(0, 'success', $this->user_model->get_date());
    }

    /**
     * 获取所有时间段
     */
    function get_time(){
        $this->response_model->show(0, "success", $this->user_model->get_time());
    }


    /**
     * 根据time_id和date,获取教室列表
     */
    function get_classroom(){
        $date_id = $this->input->get_post('date_id', TRUE);
        $time_id = $this->input->get_post('time_id', TRUE);
        $this->common_model->check_out_args($date_id, $time_id);
        $this->response_model->show(0, 'success', $this->user_model->get_classroom($date_id, $time_id));
    }



    /**
     * 获取历史借教室的订单
     *
     */
    function get_order(){
        $username = $this->session->userdata('username');
        $offset = $this->input->get_post('offset', TRUE);
        $this->common_model->check_out_args($offset);
        $limit = 30;
        $res = $this->user_model->get_order($username, $offset, $limit);
        $this->response_model->show(0, 'success', $res);
    }

    /**
     * 添加一条订单
     * 请求方式：post
     */
    function add_order(){
        $username = $this->session->userdata('username');
        if ($this->user_model->get_unhandled_or_unused_order_num($username) > 2){
            $this->response_model->show(1, "你处于待审核或者已通过未使用的二维码已经超过两条，请等待管理员审核，或者马上使用二维码。");die();
        }
        $date = $this->input->get_post('date', TRUE);
        $time_id = $this->input->get_post('time_id', TRUE);
        $classroom_id = $this->input->get_post('classroom_id', TRUE);
        $reason = $this->input->get_post('reason', TRUE);
        $this->common_model->check_out_args($date, $time_id, $classroom_id, $reason);

        //判断同一时间段同一用户的请求是否重复添加
        if ($this->user_model->is_time_have_added($username, $date, $time_id,$classroom_id)){
            $this->response_model->show(1, "你已经添加了该时间段的请求，无法重复添加");die();
        }

        //判断是否是合法日期
        if (!$this->common_model->checkDateIsValid($date)){
            $this->response_model->show(2, "日期不合法");die();
        }

        //判断教室是否合法
        if (!$this->user_model-> is_classroom_correct($classroom_id)){
            $this->response_model->show(2, "教室ID不合法");die();
        }

        //判断教室是否合法
        if (!$this->user_model-> is_time_avalible($date, $time_id, $classroom_id)){
            $this->response_model->show(2, "指定时间段不存在");die();
        }

        $this->user_model->add_order($username, $date, $time_id, $classroom_id, $reason);
        $this->response_model->show_success();
    }

    /**
     * 取消未审核的订单
     */
    function cancel_order(){
        $username = $this->session->userdata('username');
        $order_id = $this->input->get_post('order_id', TRUE);
        $this->common_model->check_out_args($order_id);
        //该订单无法取消
        if(! $this->user_model->is_order_valid_cancel($order_id, $username)){
            $this->response_model->show(1, "已经审核的订单无法取消");die();
        }
        $this->user_model->delete_order($order_id);
        $this->response_model->show(0, "删除成功");
    }
}
