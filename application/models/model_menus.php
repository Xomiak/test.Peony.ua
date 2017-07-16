<?php
class Model_menus extends CI_Model {
    
    function getNewNum()
    {
        $num = $this->db->select_max('num')->get("menus")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    
    function getNewSectionNum($parent_id)
    {
        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        $ret = $this->db->get('menus')->result_array();
        if(!$ret) return 0;
        else
        {
            $ret = $ret[0]['num'] + 1;
            return $ret;
        }
    }
    
    function getMenus()
    {
        $this->db->order_by('num','ASC');
        return $this->db->get('menus')->result_array();
    }
    
    function getMenusWithParentId($parent_id)
    {
        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('type','ASC');
        $this->db->order_by('subtype','ASC');
        $this->db->order_by('num','ASC');
        
        return $this->db->get('menus')->result_array();
    }
    
    function getMenu($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('menus')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getMenuById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('menus')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getMenuByNum($num)
    {
        $this->db->where('num',$num);
        $cat = $this->db->get('menus')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
}
?>