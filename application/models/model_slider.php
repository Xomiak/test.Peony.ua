<?php
class Model_slider extends CI_Model {
    
    function getNewNum()
    {
        $num = $this->db->select_max('num')->get("slider")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    
    function getSlides()
    {
        $this->db->order_by('num','ASC');
        return $this->db->get('slider')->result_array();
    }
    
    function getSlide($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('slider')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getSlideById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('slider')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getSlideByNum($num)
    {
        $this->db->where('num',$num);
        $cat = $this->db->get('slider')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
}
?>