<?php
class Model_afisha extends CI_Model {
      
    function getArticles($per_page = -1, $from = -1, $order_by = "DESC", $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('id',$order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('afisha')->result_array();
    }
            
    function getArticleByName($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('afisha')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getArticleById($id, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('id',$id);
        $this->db->limit(1);
        $cat = $this->db->get('afisha')->result_array();
        
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function searchByName($search)
    {
        $this->db->like('name', $search);
        return $this->db->get('afisha')->result_array();
    }
    
    function getArticleByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        if($active != -1) $this->db->where('active',$active);
        $this->db->limit(1);
        $cat = $this->db->get('afisha')->result_array();
        
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getSceneById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('afisha')->result_array();
        if($ret) return $ret[0]['scene'];
        else return false;
    }
    
    
    function getAllByScene($scene, $active, $child = -1)
    {
        $this->db->where('scene',$scene);
        if($active != -1) $this->db->where('active', $active);
        if($child != -1) $this->db->where('child', $child);
        $this->db->order_by('name', 'ASC');
        $cat = $this->db->get('afisha')->result_array();
        return $cat;
    } 
   
}
?>