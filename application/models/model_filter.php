<?php
class Model_filter extends CI_Model {
    
    function getByType($type)
    {
        $this->db->where('type',$type);
        $option = $this->db->get('filter')->result_array();        
        return $option;
    }
    
    function getById($id)
    {
        $this->db->where('id',$id);
        $this->db->limit(1);
        $option = $this->db->get('filter')->result_array();
        if(!$option) return false;
        else return $option[0];
    }
    
    function getAll()
    {
        $this->db->order_by('type', 'ASC');
        return $this->db->get('filter')->result_array();
    }
    
}
?>