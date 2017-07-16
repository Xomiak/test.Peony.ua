<?php
class Model_spambot extends CI_Model {
        
        function getRandomQuestion()
        {
		$q = $this->db->get('spambot')->result_array();
                $this->load->helper('array');
		if(!$q) return false;
		else return random_element($q);
        }
        
        function getAnswer($question)
        {
            $this->db->where('question',$question);
            $this->db->limit(1);
            $q = $this->db->get('spambot')->result_array();
            if(!$q) return false;
            else return $q[0]['answer'];
        }
}