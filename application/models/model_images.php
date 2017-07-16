<?php
class Model_images extends CI_Model {
        
        function getAllImages($login)
        {
		$user = $this->db->where('login',$login)->get('users')->result_array();
		if(!$user) return false;
		else return $user[0];
        }
	
	function getByArticleId($article_id, $active = -1, $show_in_bottom = -1)
	{
		if($active != -1) $this->db->where('active', $active);
		if($show_in_bottom != -1) $this->db->where('show_in_bottom', $show_in_bottom);
		$this->db->where('article_id', $article_id);
		$this->db->order_by('num', 'ASK');
		return $this->db->get('images')->result_array();
	}
	
	function getByShopId($article_id, $active = -1, $show_in_bottom = -1, $order = 'ASK')
	{
		if($active != -1) $this->db->where('active', $active);
		if($show_in_bottom != -1) $this->db->where('show_in_bottom', $show_in_bottom);
		$this->db->where('shop_id', $article_id);
		$this->db->order_by('num', $order);
		return $this->db->get('images')->result_array();
	}
	
	function getNewNumForShop($shop_id)
	{
		$this->db->where('shop_id', $shop_id);
		$this->db->order_by('num', 'DESC');
		$this->db->limit(1);
		$img = $this->db->get('images')->result_array();
		$num = 0;
		if($img)
		{
			$num = $img[0]['num'] + 1;
		}
		return $num;
	}
	
	function getByAfishaId($article_id, $active = -1, $show_in_bottom = -1)
	{
		if($active != -1) $this->db->where('active', $active);
		if($show_in_bottom != -1) $this->db->where('show_in_bottom', $show_in_bottom);
		$this->db->where('afisha_id', $article_id);
		return $this->db->get('images')->result_array();
	}
	
	function getById($id)
	{
		$this->db->where('id', $id);
		$ret = $this->db->get('images')->result_array();
		if($ret) return $ret[0];
		else return false;
	}
}