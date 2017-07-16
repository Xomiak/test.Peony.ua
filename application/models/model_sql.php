<?php
class Model_sql extends CI_Model
{
    function getBy($by, $value, $table){
        return $this->db->where($by,$value)->get($table)->result_array();
    }

    function getById($id, $table){
        $this->db->where('id', $id);
        $this->db->limit(1);
        $result = $this->db->get($table)->result_array();
        if(isset($result[0])) return $result[0];

        return false;
    }
}