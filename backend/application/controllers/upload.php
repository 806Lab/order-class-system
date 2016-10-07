<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/4/24
 * Time: 下午2:35
 */
class Upload extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model("XLSX_Model", "xlsx_model");
        $this->load->model("Response_Model", "response_model");
    }

//    public function index()
//    {
//        echo json_encode(array(
//            "code" => "2",
//            "msg"=>"非法请求",
//            "data"=>"null"
//        ));
//    }

    /**
     * 显示上传页面
     */
    public function index()
    {
        $this->load->view('upload_form', array('error' => ' ' ));
    }

    public function upload_courses_xlsx()
    {
        $config['upload_path']      = dirname(BASEPATH)."/tmp/";
        $config['allowed_types']    = 'xlsx';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('upload_form', $error);
        } else {
            $filename = $this->upload->data()['file_name'];
            $filepath = dirname(BASEPATH)."/tmp/".$filename;
            //echo $filepath;
            if($this->xlsx_model->insert_courses($filepath)){
                $this->response_model->show_success("数据上传成功");
            }else{
                $this->response_model->show(1, "上传失败");
            }
            unlink(dirname(BASEPATH)."/tmp/".$filename);
        }
    }

}