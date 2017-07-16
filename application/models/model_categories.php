<?php
class Model_categories extends CI_Model {
    
    function getNewNum()
    {
        $num = $this->db->select_max('num')->get("categories")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    
    function getCategories($active = -1, $type = -1)
    {
        $this->db->where('parent',0);
        if($active != -1) $this->db->where('active',$active);
        if($type != -1) $this->db->where('type',$type);
        $this->db->order_by('num','ASC');
        $ret = $this->db->get('categories')->result_array();
        if(!$ret) return false;
        else return $ret;
    }
    
    function getSubCategories($parent_id, $active = -1)
    {
        $this->db->where('parent',$parent_id);
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('num','ASC');
        $ret = $this->db->get('categories')->result_array();
        if(!$ret) return false;
        else return $ret;
    }
    
    function getAllCategories($active = -1)
    {
        $this->db->order_by('num','ASC');
        if($active != -1) $this->db->where('active',$active);
        $ret = $this->db->get('categories')->result_array();
        if(!$ret) return false;
        else return $ret;
    }
    
    function getCategory($name, $active = -1)
    {
        $this->db->where('name',$name);
        if($active != -1) $this->db->where('active',$active);
        $this->db->limit(1);
        $cat = $this->db->get('categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getCategoryById($id, $active = -1)
    {
        $this->db->where('id',$id);
        if($active != -1) $this->db->where('active',$active);
        $this->db->limit(1);
        $cat = $this->db->get('categories')->result_array();        
        if(!$cat) return false;
        else return $cat[0];
    }
    function getParentById($id)
    {
        $this->db->where('id',$id);
        $this->db->limit(1);
        $cat = $this->db->get('categories')->result_array();
        var_dump($cat);
        if(!$cat) return false;
        else{            
            if($cat[0]['parent'] == 0) return false;
            else{
                $this->db->where('id',$cat[0]['parent']);
                $this->db->limit(1);
                $parent = $this->db->get('categories')->result_array();
                if(!$parent) return false;
                else return $parent[0];
            }
        }
    }
    function getCategoryByNum($num, $active = -1)
    {
        $this->db->where('num',$num);
        if($active != -1) $this->db->where('active',$active);
        $this->db->limit(1);
        $cat = $this->db->get('categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getCategoryByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        if($active != -1) $this->db->where('active',$active);
        $this->db->limit(1);
        $cat = $this->db->get('categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }

    function getVkCategoryId($category_id, $group_id)
    {
        $this->db->where('category_id', $category_id);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vkmarket_category_to_vk')->result_array();
        if(isset($ret[0]['vk_album_id'])) return $ret[0]['vk_album_id'];

        return false;
    }
}
?>