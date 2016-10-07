<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extern extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("Extern_Model", "extern_model");
        $this->load->model("Response_Model", "response_model");
    }

    function get_arrangement(){
        $this->response_model->show(0, "success", $this->extern_model->get_arrangement());
    }
}