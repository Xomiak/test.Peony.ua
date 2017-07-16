<?php
class Model_gallery extends CI_Model {
    
    function getCategories($active = -1)
    {
        $this->db->order_by('num','ASC');
        $cats = $this->db->get('gallery_categories')->result_array();
        if(!$cats) return false;
        else return $cats;
    }
    
    function getHomeCategories($active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('parent_id',0);
        $this->db->order_by('num','ASC');
        $cats = $this->db->get('gallery_categories')->result_array();
        if(!$cats) return false;
        else return $cats;
    }
    
    function getSubCategories($category_id, $active = -1)
    {
        $this->db->where('parent_id',$category_id);
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('num','ASC');
        $cats = $this->db->get('gallery_categories')->result_array();
        if(!$cats) return false;
        else return $cats;
    }
    
    function getNewCategoryNum()
    {
        $num = $this->db->select_max('num')->get("gallery_categories")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    function getNewImageNum()
    {
        $num = $this->db->select_max('num')->get("gallery_images")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    
    function getCategoryByName($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('gallery_categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getCategoryByUrl($url)
    {
        $this->db->where('url',$url);
        $cat = $this->db->get('gallery_categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getCategoryById($id, $active = -1)
    {
        $this->db->where('id',$id);
        if($active != -1) $this->db->where('active', $active);
        $cat = $this->db->get('gallery_categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getCategoryByNum($num)
    {
        $this->db->where('num',$num);
        $cat = $this->db->get('gallery_categories')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getImages($active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num', 'DESC');
        $images = $this->db->get('gallery_images')->result_array();
        return $images;
    }
    
    function getImagesByCategory($category_id, $active = -1, $per_page = -1, $from = -1)
    {
        $this->db->where('category_id', $category_id);
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num', 'DESC');
        $images = $this->db->get('gallery_images')->result_array();
        return $images;
    }
    
    function getFoto($id, $active = -1)
    {
        $this->db->where('id',$id);
        if($active != -1) $this->db->where('active', $active);
        $this->db->limit(1);
        $foto = $this->db->get('gallery_images')->result_array();
        if(!$foto) return false;
        else return $foto[0];
    }
    
    
    
    ////////////////// CLIENT
    
    function getMain()
    {
        $this->db->limit(1);
        $main = $this->db->get('gallery')->result_array();
        if(!$main) return false;
        else return $main[0];
    }
    
    function getImageInCategory($id, $category_id, $active = -1)
    {        
        $this->db->where('id',$id);
        $this->db->where('category_id', $category_id);
        if($active != -1) $this->db->where('active', $active);
        $this->db->limit(1);
        $img = $this->db->get('gallery_images')->result_array();
        if(!$img) return false;
        else return $img[0];
    }
    
    function countPlus($id)
    {
        $this->db->where('id',$id);
        $img = $this->db->get('gallery_images')->result_array();
        if($img)
        {
            $count = $img[0]['count'] + 1;
            
            $dbins = array(
                'count' => $count
            );
            $this->db->where('id',$id)->update('gallery_images', $dbins);
        }
    }
    
    function getNextImage($image_id, $num, $category_id, $active = -1)
    {
        $this->db->where('num >',$num);
        $this->db->where('category_id', $category_id);
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('num', 'ASC');
        $this->db->limit(1);
        $img = $this->db->get('gallery_images')->result_array();
        if(!$img) return false;
        else return $img[0];
    }
    
    function getPrevImage($image_id, $num, $category_id, $active = -1)
    {
        $this->db->where('num <',$num);
        $this->db->where('category_id', $category_id);
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        $img = $this->db->get('gallery_images')->result_array();
        if(!$img) return false;
        else return $img[0];
    }
}
?>