<?php
class Model_mailer extends CI_Model {
    
    function getOption($name)
    {
        $this->db->where('name', $name);
        $this->db->limit(1);
        $opt = $this->db->get('mailer_options')->result_array();
        if(!$opt) return false;
        else return $opt[0]['value'];
    }
    
    function setOption($name, $value)
    {
        $old = $this->getOption($name);
        
        if(!$old) $this->addOption($name, $value);
        else $this->db->where('name',$name)->limit(1)->update('mailer_options',array('value' => $value));
        
    }
    
    function addOption($name, $value)
    {
        return $this->db->insert('mailer_options',array('name' => $name, 'value' => $value));
    }
    
    function getAllOptions()
    {
        return $this->db->get('mailer_options')->result_array();
    }
    
    function getOptionById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $opt = $this->db->get('mailer_options')->result_array();
        if(!$opt) return false;
        else return $opt[0];
    }
    
    function getAll()
    {
        $this->db->order_by('id','DESC');
        return $this->db->get('mailer')->result_array();
    }
    
    function getById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('mailer')->result_array();
        if(!$ret) return false;
        else return $ret[0];
    }
    
    function getLastMailer()
    {
        $this->db->limit(1);
        $this->db->order_by('id','DESC');
        $last = $this->db->get('mailer')->result_array();
        if(!$last) return false;
        else return $last[0];
    }

    function getQueueMailCount($complete = 0){
        $this->db->where('complete', $complete);
        $this->db->from('mailer_cron');
        return $this->db->count_all_results();
    }

    function getSendedMailCount($date = false){
        $this->db->where('complete', 1);
        if($date)
            $this->db->like('date', $date);
        $this->db->from('mailer_cron');
        return $this->db->count_all_results();
    }

    function getMailerQueue($per_page = -1, $from = -1, $complete = 0)
    {
        $this->db->where('complete',$complete);
        $this->db->order_by('id','ASC');
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('mailer_cron')->result_array();
    }

    function getQueueById($id){
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('mailer_cron')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getAllSmsMailers(){
        return $this->db->get('sms_mailers')->result_array();
    }

    function getSmsMailerById($id){
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('sms_mailers')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }
    
}
?>