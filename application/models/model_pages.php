<?php
class Model_pages extends CI_Model {
    
    function getNewNum()
    {
        $num = $this->db->select_max('num')->get("pages")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    
    function getPages()
    {
        $this->db->order_by('num','ASC');
        return $this->db->get('pages')->result_array();
    }
    
    function getPage($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('pages')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getPageById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('pages')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getPageByNum($num)
    {
        $this->db->where('num',$num);
        $cat = $this->db->get('pages')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getPageByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        if($active != -1) $this->db->where('active',$active);
        $cat = $this->db->get('pages')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
}
?>