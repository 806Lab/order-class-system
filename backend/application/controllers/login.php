<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("Login_Model", "login_model");
        $this->load->model("Common_Model", "common_model");
        $this->load->model("Response_Model", "response_model");
    }
    
    /**
     * 登陆接口
     */
    function index(){
        $username = $this->input->get_post('uname', TRUE);
        $password = $this->input->get_post('passwd', TRUE);
        $this->common_model->check_out_args($username, $password);
        $code = 0;
        $msg = "登陆成功";
        $type = 1; // 1 for 管理员  0 for user
        
        if(!$this->login_model->is_admin_password_correct($username, $password)){
            if (!$this->login_model->is_user_password_correct($username, $password)){ //即不是admin也不是user
                $msg = "用户名或密码错误";
                $code = 1;
            }else {
                $type = 0;
                $session['is_user'] = TRUE;
                $session['is_admin'] = FALSE;
                $session['username'] = $username;
                $this->session->set_userdata($session);
            }
        }else {
            $session['is_user'] = FALSE;
            $session['is_admin'] = TRUE;
            $session['username'] = $username;
            $this->session->set_userdata($session);
        }
        
        $data = array(
            "type" => $type
        );
        $this->response_model->show_json($code, $msg, $data);
    }
}
