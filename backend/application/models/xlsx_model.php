<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/4/24
 * Time: 下午4:10
 */



class XLSX_Model extends CI_Model{

    const TAB_PRE_ARRANGEMENT = "pre_arrangement";
    const TAB_USER = "user";
    const TAB_CLASSROOM = "classroom";


    function __construct()
    {
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        $this->load->helper("excel");

    }

    /**
     * 根据教室获取classroom的id
     * @param $classroom
     * @return int
     */
    function get_classroom_id($classroom){

        $query = $this->db->get_where(self::TAB_CLASSROOM, array("room_num" =>
            substr($classroom, strlen($classroom)-3, strlen($classroom)-1)));

        if($query->num_rows() == 0){
            return -1;
        }
        return $query->row()->id;
    }

    /**
     * 把课程xlsx的内容插入到pre_arrangement表里
     * @param $filepath
     * @return bool
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    function insert_courses($filepath){

        if (!file_exists($filepath)) {
            return false;
        }

        $reader = IOFactory::createReader('Excel2007');
        $PHPExcel = $reader->load($filepath); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数

        /** 循环读取每个单元格的数据 */
        for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
            $date_id = '';
            $classroom = '';
            $time_id = '';
            $content = '';
            $start_date = '';
            $end_date = '';

            for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                $value = $sheet->getCell($column.$row)->getValue();
                switch($column) {
                    case 'C':
                        $content = $value;
                        break;
                    case 'H':
                        $content .= "\n" . $value;
                        break;
                    case 'E':
                        $content .= "\n" . $value;
                        break;
                    case 'K':
                        $date_id = $value;
                        break;
                    case 'L':
                        $start_date = $value;
                        break;
                    case 'M':
                        $end_date = $value;
                        break;
                    case 'O':
                        $classroom = $value;
                        break;
                    case 'N':
                        $time_id = $value;
                        break; //节次
                }
            }

            $classroom_id = $this->get_classroom_id(gbk_to_utf8($classroom));
            if($classroom_id == -1){
                continue;
            }
            $data = array(
                'date_id' => $date_id ,
                'classroom_id' =>$classroom_id,
                'time_id' => $time_id,
                'content' => $content,
                'type' => 1,
                'start_date' => excelTime($start_date),
                'end_date' => excelTime($end_date)
            );
            $this->db->insert(self::TAB_PRE_ARRANGEMENT, $data);
        }

        return true;

    }

}