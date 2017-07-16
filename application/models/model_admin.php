<?php
class Model_admin extends CI_Model {
        
        function getUser($login)
        {
		$user = $this->db->where('login',$login)->get('users')->result_array();
		if(!$user) return false;
		else return $user[0];
        }

	// GET
	// MaxSort
	function get_MaxSort($table) {
		return $this->db->select_max('sort')->get($table)->result_array();
	}

	// INSERT
	function in_byTab($table,$dbins){
		$this->db->insert($table,$dbins);
	}

	// UPDATE
	function up_byTabId($table,$id,$dbins) {
		if(($table != '')||($id != ''))
			$this->db->where('id',$id)->limit(1)->update($table,$dbins);
	}
 
	function up_sort_array($up_array,$db_tab_name) {
		foreach($up_array as $key=>$up) {
			$dbins = array('sort' => $up);
			$this->db->where('id',$key)->update($db_tab_name,$dbins);
		}
	}

	// DELETE
	function del_byTabId($table,$id) {
		if(($table != '')||($id != ''))
		$this->db->where('id',$id)->limit(1)->delete($table);
	}

	// Форматирование даты
	function date_format($date) {
		$srt = '0000-00-00';
		$d1 = explode('-', $date);
		if((isset($d1[0])) && (isset($d1[1])) && (isset($d1[2])))
			$srt = $d1[0].'-'.$d1[1].'-'.$d1[2];
		$d2 = explode('.', $date);
		if((isset($d2[0])) && (isset($d2[1])) && (isset($d2[2])))
			$srt = $d2[2].'-'.$d2[1].'-'.$d2[0];
		return $srt;
	}

	function date_format_view($date) {
		$d = explode('-', $date);
		switch($d[1]) {
		case '01':
			$month = 'января';
			break;
		case '02':
			$month = 'февраля';
			break;
		case '03':
			$month = 'марта';
			break;
		case '04':
			$month = 'апреля';
			break;
		case '05':
			$month = 'мая';
			break;
		case '06':
			$month = 'июня';
			break;
		case '07':
			$month = 'июля';
			break;
		case '08':
			$month = 'августа';
			break;
		case '09':
			$month = 'сентября';
			break;
		case '10':
			$month = 'октября';
			break;
		case '11':
			$month = 'ноября';
			break;
		case '12':
			$month = 'декабря';
			break;
		}
		$srt = $d[2].'&nbsp;'.$month.'&nbsp;'.$d[0];
		return $srt;
	}

	function session_test() {
		$session = $this->session->userdata('logon');
		if($session == 'Yes!') $sess = true;
		else $sess = false;
		return $sess;
	}

	function getNewsCount()
	{
		$newsCount = 0;
		$this->db->where('admin_new', 1);
		$artists = $this->db->get('artists');		
		$this->db->where('admin_new', 1);
		$content = $this->db->get('content');
		$this->db->where('admin_new', 1);
		$links = $this->db->get('links');
		$this->db->where('admin_new', 1);
		$fotos = $this->db->get('fotos');
		
		$newsCount = $artists->num_rows() + $content->num_rows() + $links->num_rows() + $fotos->num_rows();
		return $newsCount;
	}
	
	function getMain()
	{
		$this->db->limit(1);
		$main = $this->db->get('main')->result_array();
		if(!$main) return false;
		else return $main[0];
	}
	
	function setActive($id,$table)
	{
		$this->db->where('id',$id);
		$arr = $this->db->get($table)->result_array();
		if($arr)
		{
			$active = 0;
			if($arr[0]['active'] == 0) $active = 1;
			$dbins = array('active' => $active);
			$this->db->where('id',$id)->update($table,$dbins);
		}
	}
	
	function setAlwaysFirst($id,$table)
	{
		$this->db->where('id',$id);
		$arr = $this->db->get($table)->result_array();
		if($arr)
		{
			$active = 0;
			if($arr[0]['always_first'] == 0) $active = 1;
			$dbins = array('always_first' => $active);
			$this->db->where('id',$id)->update($table,$dbins);
		}
	}
}
?>