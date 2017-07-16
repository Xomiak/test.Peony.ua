<?php
class Model_forum extends CI_Model {
    
    // SECTIONS
    function getAllSections($active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num', 'ASC');
        return $this->db->get('forum_sections')->result_array();
    }
    
    function getParentSections($parent_id, $active = -1, $per_page = -1, $from = -1)
    {
        $this->db->where('parent_id', $parent_id);
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num', 'ASC');
        return $this->db->get('forum_sections')->result_array();
    }
    
    function getSectionById($id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('forum_sections')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getSectionByUrl($url, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('url', $url);
        $this->db->limit(1);
        $ret = $this->db->get('forum_sections')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getSectionByUrlAndParentId($url, $parent_id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('url', $url);
        $this->db->where('parent_id', $parent_id);
        $this->db->limit(1);
        $ret = $this->db->get('forum_sections')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getSubSections($id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('parent_id', $id);
        $this->db->order_by('num', 'ASC');
        return $this->db->get('forum_sections')->result_array();
    }
    
    function getNewSectionNum($parent_id)
    {
        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        $ret = $this->db->get('forum_sections')->result_array();
        if(!$ret) return 0;
        else
        {
            $ret = $ret[0]['num'] + 1;
            return $ret;
        }
    }
    
    function getPrevSectionByNumAndParentId($num, $parent_id)
    {
        $this->db->where('parent_id',$parent_id);
        $this->db->where('num <',$num);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        $ret = $this->db->get('forum_sections')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getNextSectionByNumAndParentId($num, $parent_id)
    {
        $this->db->where('parent_id',$parent_id);
        $this->db->where('num >',$num);
        $this->db->order_by('num', 'ASC');
        $this->db->limit(1);
        $ret = $this->db->get('forum_sections')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getSectionsByParentId($parent_id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('num', 'ASC');
        return $this->db->get('forum_sections')->result_array();
    }
    // -- //
    
    // TOPICS
    function getAllTopics($active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('lastmsgdatetime', 'DESC');
        return $this->db->get('forum_topics')->result_array();
    }
    
    function getTopicById($id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('forum_topics')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getTopicByUrl($url, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('url', $url);
        $this->db->limit(1);
        $ret = $this->db->get('forum_topics')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getTopicsBySectionId($section_id, $active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('section_id',$section_id);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('lastmsgdatetime', 'DESC');
        return $this->db->get('forum_topics')->result_array();
    }
    
    function getTopicByUrlAndSectionId($url, $section_id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('url', $url);
        $this->db->where('section_id', $section_id);
        $this->db->limit(1);
        $ret = $this->db->get('forum_topics')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getTopicsNamesLike($like, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->like('name', $like);
        return $this->db->get('forum_topics')->result_array();
    }
    
    // -- //
    
    // MESSAGES
    function getAllMessages($active = -1, $per_page = -1, $from = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('forum_messages')->result_array();
    }
    
    function getReserveMessages($active = -1, $per_page = -1, $from = -1)
    {
        $this->db->where('reserve', 1);
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('forum_messages')->result_array();
    }
    
    function getMessagesContentLike($like, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->like('message', $like);
        return $this->db->get('forum_messages')->result_array();
    }
    
    function getMessagesFromLogin($login, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('login', $login);
        return $this->db->get('forum_messages')->result_array();
    }
    
    function getMessageById($id, $active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('forum_messages')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getMessagesByTopicId($topic_id, $active = -1, $per_page = -1, $from = -1)
    {
        $this->db->where('topic_id', $topic_id);
        if($active != -1) $this->db->where('active', $active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('forum_messages')->result_array();
    }
    
    function getTopicMessage($topic_id, $active = -1)
    {
        $this->db->where('topic_id', $topic_id);
        if($active != -1) $this->db->where('active', $active);
        $this->db->where('topic_message', 1);
        $this->db->limit(1);
        $ret = $this->db->get('forum_messages')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    // -- //
    
    // OPTIONS
    function getOption($name)
    {
        $this->db->where('name',$name);
        $this->db->limit(1);
        $ret = $this->db->get('forum_options')->result_array();
        if(!$ret) return false;
        else return $ret[0]['value'];
    }
    
    function getOptionById($id)
    {
        $this->db->where('id',$id);
        $this->db->limit(1);
        $option = $this->db->get('forum_options')->result_array();
        if(!$option) return false;
        else return $option[0];
    }
    
    function getAllOptions()
    {
        return $this->db->get('forum_options')->result_array();
    }
    // -- //
}
?>