<?php
class Model_blogs extends CI_Model {
    
    
    // BLOGS
    function getAllBlogs($active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('id','DESC');        
        $blogs = $this->db->get('blogs')->result_array();
        if(!$blogs) return false;
        else return $blogs;
    }
    
    function getBlogById($id, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $blog = $this->db->get('blogs')->result_array();
        if(!$blog) return false;
        else return $blog[0];
    }
    
    function getBlogByUrl($url, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('url', $url);
        $this->db->limit(1);
        $blog = $this->db->get('blogs')->result_array();
        if(!$blog) return false;
        else return $blog[0];
    }
    
    function getBlogByLogin($login, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('login', $login);
        $this->db->limit(1);
        $blog = $this->db->get('blogs')->result_array();
        if(!$blog) return false;
        else return $blog[0];
    }
    
    
    
    //
    
    // BLOGS_CONTENT
    function getAllBlogContent($active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $blogs = $this->db->get('blogs_content')->result_array();
        if(!$blogs) return false;
        else return $blogs;
    }
    function getBlogContentById($id, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $blog = $this->db->get('blogs_content')->result_array();
        if(!$blog) return false;
        else return $blog[0];
    }
    
    function getBlogContentsByBlogId($blog_id, $active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('blog_id', $blog_id);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('id','DESC');
        $blog = $this->db->get('blogs_content')->result_array();
        if(!$blog) return false;
        else return $blog;
    }
    
    function getBlogContentsByLogin($login, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('login', $login);
        $blog = $this->db->get('blogs_content')->result_array();
        if(!$blog) return false;
        else return $blog;
    }
    
    function getLastBlogContentOrderByCountDesc($count)
    {
        $this->db->where('active',1);
        $this->db->where('visible',1);
        $this->db->limit($count);
        $this->db->order_by('count','DESC');
        return $this->db->get('blogs_content')->result_array();
    }
    
    //
    
    // BLOGS_OPTIONS
    function getOption($name)
    {
        $this->db->where('name',$name);
        $this->db->limit(1);
        $option = $this->db->get('blogs_options')->result_array();
        if(!$option) return false;
        else return $option[0]['value'];
    }
    
    function getOptionById($id)
    {
        $this->db->where('id',$id);
        $this->db->limit(1);
        $option = $this->db->get('blogs_options')->result_array();
        if(!$option) return false;
        else return $option[0];
    }
    
    function getAllOptions()
    {
        return $this->db->get('blogs_options')->result_array();
    }
    //
    
    
    // BLOGS_invitation_codes
    
    function getAllInvitationCodes($used = -1)
    {
        if($used != -1) $this->db->where('used',$used);
        $this->db->order_by('id','DESC');
        $codes = $this->db->get('blogs_invitation_codes')->result_array();
        if(!$codes) return false;
        else return $codes;
    }
    
    function getInvitationCode($code, $used = -1)
    {
        $this->db->where('code',$code);
        if($used != -1) $this->db->where('used',$used);
        $this->db->limit(1);
        $ic = $this->db->get('blogs_invitation_codes')->result_array();
        if(!$ic) return false;
        else return $ic[0];
    }
    
    function makeInvitationCodeUsed($code, $used_login)
    {
        $dbins = array(
            'used'          => 1,
            'used_login'    => $used_login
        );
        
        $this->db->where('code',$code);
        $this->db->update('blogs_invitation_codes', $dbins);
    }
    
}
?>