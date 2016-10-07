<?php
class Response_Model extends CI_Model{
    
    /**
     * 接口输出 jsonp
     * @param int $code
     * @param string $msg
     * @param mixed $data
     */
    function show($code, $msg, $data=''){
        $result = array(
            "code" =>$code,
            "msg" => $msg,
            "data" => $data
        );
        //$callback = $this->input->get('callback', TRUE);
        //echo $callback.'('.json_encode($result).')';
        echo json_encode($result);
    }
    
    /**
     * 接口输出 json
     * @param int $code
     * @param string $msg
     * @param mixed $data
     */
    function show_json($code, $msg, $data=''){
        $result = array(
            "code" =>$code,
            "msg" => $msg,
            "data" => $data
        );
//        $callback = $this->input->get('callback', TRUE);
//        echo $callback.'('.json_encode($result).')';
        echo json_encode($result);
    }
    
    /**
     * 默认成功的输出接口
     * @param string $msg
     */
    function show_success($msg = 'success'){
        $result = array(
            "code" => 0,
            "msg" => $msg,
            "data" => ''
        );
//        $callback = $this->input->get('callback', TRUE);
//        echo $callback.'('.json_encode($result).')';
        echo json_encode($result);
    }

    /**
     *
     * @param string $msg
     */
    function show_error($msg = "出现错误"){
        $result = array(
            "code" => 1,
            "msg" => $msg,
            "data" => ''
        );
//        $callback = $this->input->get('callback', TRUE);
//        echo $callback.'('.json_encode($result).')';
        echo json_encode($result);
    }

    function show_invalid($msg = "非法请求"){
        $result = array(
            "code" => 1,
            "msg" => $msg,
            "data" => ''
        );
//        $callback = $this->input->get('callback', TRUE);
//        echo $callback.'('.json_encode($result).')';
        echo json_encode($result);
    }
    
    
}