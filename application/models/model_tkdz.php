<?php
class Model_tkdz extends CI_Model {

    function getByUrl($url)
    {
        $this->db->where('url', $url);
        $this->db->limit(1);
        $tkdz = $this->db->get('tkdz')->result_array();
        if(isset($tkdz[0])) return $tkdz[0];

        return false;
    }
}