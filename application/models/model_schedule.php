<?php
class Model_schedule extends CI_Model {
      
    function getArticles($per_page = -1, $from = -1, $order_by = "DESC", $active = -1, $only_future = false)
    {
        if($active != -1) $this->db->where('active',$active);
        if($only_future) $this->db->where('date_unix >',time()); // показать только будущие
        
        $this->db->order_by('date_unix',$order_by);
        
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('schedule')->result_array();
    }
            
    function getArticleByName($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('schedule')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getByMonth($date, $scene = -1, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        if($scene != -1) $this->db->where('scene',$scene);
        $this->db->like('date', $date);
        $this->db->order_by('date_unix', 'ASC');
        $ret = $this->db->get('schedule')->result_array();        
        return $ret;
    }
    
    function getByDate($date, $scene = -1, $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        if($scene != -1) $this->db->where('scene',$scene);
        $this->db->where('date', $date);
        $this->db->order_by('date_unix', 'ASC');
        $ret = $this->db->get('schedule')->result_array();        
        return $ret;
    }
    
    function getArticleById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('schedule')->result_array();
        
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getScheduleByAfishaId($afisha_id, $limit = -1, $active = -1, $only_future = false, $order_by = 'DESC')
    {
        $this->db->where('afisha_id', $afisha_id);
        if($active != -1) $this->db->where('active', $active);
        
        if($only_future) $this->db->where('date_unix >',time()); // показать только будущие
        
        $this->db->order_by('date_unix',$order_by);
        
        if($limit != -1) $this->db->limit($limit);
        
        $schedule = $this->db->get('schedule')->result_array();
        
        return $schedule;
    }   
}
?>