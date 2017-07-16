<?php
class Model_export extends CI_Model {

    function getNext(){
        $this->db->where('status','new');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $ret = $this->db->get('export_cron')->result_array();
        if($ret) return $ret[0];

        return false;
    }

}
?>