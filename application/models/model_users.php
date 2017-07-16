<?php
class Model_users extends CI_Model {

    /*function getArticles($per_page = -1, $from = -1, $order_by = "DESC", $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('num',$order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('articles')->result_array();
    }*/

    function getUsers($per_page = -1, $from = -1, $order_by = 'DESC', $type = false, $sort_by = 'id', $mailer = -1)
    {
		if($type) $this->db->where('user_type', $type);
        if ($mailer != -1) $this->db->where('mailer', $mailer);
		
        $this->db->order_by($sort_by, $order_by);
		if ($per_page != -1 && $from != -1) $this->db->limit($from, $per_page);
        $users = $this->db->get('users')->result_array();

       // echo $this->db->last_query();
        return $users;

    }

    function getMailerUsers($per_page = -1, $from = -1, $order_by = 'DESC', $type = false, $sort_by = 'id'){
        if($type) $this->db->where('user_type', $type);
        $this->db->where('mailer', 1);
        $this->db->where('email <>', '');

        $this->db->order_by($sort_by, $order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($from, $per_page);
        $users = $this->db->get('users')->result_array();

        // echo $this->db->last_query();
        return $users;
    }

    function getNoMailerUsers($per_page = -1, $from = -1, $order_by = 'DESC', $type = false, $sort_by = 'id'){
        if($type) $this->db->where('user_type', $type);
        $this->db->where('mailer', 0);

        $this->db->order_by($sort_by, $order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($from, $per_page);
        $users = $this->db->get('users')->result_array();

        // echo $this->db->last_query();
        return $users;
    }

    function getUsersCount2( $order_by = 'DESC', $type = false, $sort_by = 'id')
    {
        if($type) $this->db->where('user_type', $type);
        
        $this->db->order_by($sort_by, $order_by);
        $this->db->from('users');
        return $this->db->count_all_results();

    }

    function getUsersByType($per_page = -1, $from = -1, $order_by = 'DESC', $type = false, $sort_by = 'id', $mailer = -1)
    {
        if($type) $this->db->where('type', $type);
        if($mailer != -1) $this->db->where('mailer', $mailer);
        
        $this->db->order_by($sort_by, $order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($from, $per_page);
        $users = $this->db->get('users')->result_array();

        //echo $this->db->last_query();
        return $users;

    }

    function getUsersByTypeId($user_type_id){
        $this->db->where('user_type_id', $user_type_id);
        return $this->db->get('users')->result_array();
    }
	
	function getUsersCount($type = false)
	{
		if($type) $this->db->where('type', $type);
		$this->db->from('users');
		return $this->db->count_all_results();
	}

    function getSmsUsers($per_page = -1, $from = -1, $order_by = 'DESC', $type = false, $sort_by = 'id'){
        if($type) $this->db->where('user_type', $type);
        //$this->db->where('tel <>', NULL);
        $this->db->where('tel <>', '');
        $this->db->where('email <>', '');

        $this->db->order_by($sort_by, $order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($from, $per_page);
        $users = $this->db->get('users')->result_array();

        // echo $this->db->last_query();
        return $users;
    }
    
    function getUserById($id)
    {
        $this->db->where('id',$id);
        $this->db->limit(1);
        $user = $this->db->get('users')->result_array();
        if(!$user) return false;
        else return $user[0];
    }
    
    function getUserByLogin($login, $noAdmin = false)
    {
        $this->db->where('login',$login);
        if($noAdmin) $this->db->where('type <>', 'admin');
        $this->db->limit(1);
        $user = $this->db->get('users')->result_array();
        if(!$user) return false;
        else return $user[0];
    }
    
    function getUserByEmail($email, $noAdmin = false)
    {
        $this->db->where('email',$email);
        if($noAdmin) $this->db->where('type <>', 'admin');
        $this->db->limit(1);
        $user = $this->db->get('users')->result_array();
        if(!$user) return false;
        else return $user[0];
    }
    function getUserByTel($tel, $noAdmin = false)
    {
        if($noAdmin) $this->db->where('type <>', 'admin');
        $this->db->like('tel',$tel);
        $this->db->limit(1);
        $user = $this->db->get('users')->result_array();
        if(!$user) return false;
        else return $user[0];
    }

    function getUser($email)
    {
        $this->db->where('email',$email);
        $this->db->limit(1);
        $user = $this->db->get('users')->result_array();
        if(!$user) return false;
        else return $user[0];
    }
    
    function setLastDateAndIp($login)
    {
        $this->db->where('login',$login);
        $dbins = array(
            'last_login_date'       => date("Y-m-d H:i"),
            'last_login_ip'         => $_SERVER['REMOTE_ADDR']
           );
        $this->db->limit(1);
	$this->db->update('users',$dbins);
    }

// function Search($key,$per_page = -1,$from = -1)
//     {
//         $this->db->like('name',$key);
//         $this->db->or_like('url',$key);
//         $this->db->or_like('type',$key);
//         $this->db->or_like('adding',$key);
        
//         if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
//         $shop = $this->db->get('projects')->result_array();
//         if(!$shop) return false;
//         else return $shop;
//     }
    function Search($key,$per_page = -1,$from = -1)
    {
        $this->db->where('active', 1);
        $this->db->like('name',$key);
        $this->db->or_like('lastname',$key);
        $this->db->or_like('email',$key);
        $this->db->or_like('tel',$key);
        $this->db->or_like('city',$key);
        $this->db->or_like('country',$key);
        $this->db->or_like('adress',$key);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $shop = $this->db->get('users')->result_array();
        if(!$shop) return false;
        else return $shop;
    }

    function getUserTypes($active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        return $this->db->get('user_types')->result_array();
    }

    function getUserTypeById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $ret = $this->db->get('user_types')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getUserTypeByName($name)
    {
        $this->db->where('name', $name);
        $this->db->limit(1);
        $ret = $this->db->get('user_types')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getUsersByBdDate($date)
    {
        $this->db->where('active', 1);
        $this->db->like('bd_date', $date,'before');
        $this->db->or_like('bd_date', $date,'before');

        return $this->db->get('users')->result_array();
    }
    function getUsersByBdDateNow()
    {
        $date = date("-m-d");
        $this->db->where('active', 1);
        $this->db->like('bd_date', $date,'before');
        $this->db->or_like('bd_date', $date,'before');

        return $this->db->get('users')->result_array();
    }

    function getUsersForMailer()
    {
        $this->db->where('email <>', '');
        $this->db->where('mailer', 1);
        $this->db->where('active', 1);
        return $this->db->get('users')->result_array();
    }

    function getCurrentUser()
    {
        $user = false;
        if(userdata('login') !== false) $user = $this->getUserByLogin(userdata('login'));
        return $user;
    }

    function getByVkId($vk_id){
        $this->db->where('vk_id', $vk_id);
        $this->db->limit(1);
        $ret = $this->db->get('users')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function searchByProfile($profile){
        $this->db->like('profile', $profile);
        $this->db->limit(1);
        $ret = $this->db->get('users')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getAddressesByUser($login){
        $this->db->where('login', $login);
        return $this->db->get('addr')->result_array();
    }

    function getAddressById($id){
        $this->db->where('id',$id);
        $ret = $this->db->get('addr')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }
}
?>