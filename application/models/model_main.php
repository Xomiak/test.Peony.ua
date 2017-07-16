<?php
class Model_main extends CI_Model {
    function getMain()
	{
            $this->db->limit(1);
            $main = $this->db->get('main')->result_array();
            if(!$main) return false;
            else return $main[0];
	}
}