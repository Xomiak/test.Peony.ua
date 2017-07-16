<?php
class Model_comments extends CI_Model {
   
    function getAllComments()
    {
        return $this->db->get('comments')->result_array();
    }

    function getNewCommentsCount()
    {
        $this->db->where('active', 0);
        $this->db->from('comments');
        return $this->db->count_all_results();
    }
    
    function getComments($per_page = -1, $from = -1, $order_by = 'id', $sort_by="ASC", $active = -1)
    {
        if($active != -1)
        $this->db->where('active',$active);
        $this->db->order_by($order_by,$sort_by);
        $this->db->order_by('id','DESC');
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('comments')->result_array();
    }
    
    function getCommentsToArticle($article_id, $order_by = -1)
    {
        $this->db->where('name','articles_comments_order_by');
        $articles_comments_order_by = $this->db->get('options')->result_array();
        if($articles_comments_order_by) $articles_comments_order_by = $articles_comments_order_by[0]['value'];
        if($order_by == -1) $order_by = "ASC";
        if(($articles_comments_order_by) && $articles_comments_order_by == 1) $order_by = "DESC";
        
        $this->db->where('article_id',$article_id);
        $this->db->where('active',1);
        $this->db->order_by('id',$order_by);
        $comments = $this->db->get('comments')->result_array();
        if(!$comments) return false;
        else return $comments;
    }

    function getCommentsToShop($shop_id, $active = -1, $order_by = 'DESC')
    {
        $this->db->where('shop_id',$shop_id);
        if($active != -1) $this->db->where('active',$active);
            
        $this->db->order_by('id',$order_by);
        $comments = $this->db->get('comments')->result_array();
        if(!$comments) return false;
        else return $comments;
    }


    function getCommentsToImage($id)
    {
        $this->db->where('image_id',$id);
        $this->db->where('active',1);
        $comments = $this->db->get('comments')->result_array();
        if(!$comments) return false;
        else return $comments;
    }

    function getToPageId($id)
    {
        $this->db->where('page_id',$id);
        $this->db->where('active',1);
        $comments = $this->db->get('comments')->result_array();
        if(!$comments) return false;
        else return $comments;
    }
    
    function addComment($dbins)
    {
        $this->db->insert('comments',$dbins);
    }
    
    function getCommentById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $comm = $this->db->get('comments')->result_array();
        if(!$comm) return false;
        else return $comm[0];
    }
    
}
?>