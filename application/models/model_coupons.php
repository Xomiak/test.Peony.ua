<?php
class Model_coupons extends CI_Model {

    function add($code)
    {

    }

    function getAll($per_page = false, $from = false)
    {
        if($per_page !== false && $from !== false)
            $this->db->limit($per_page, $from);
        $this->db->order_by('id','DESC');
        return $this->db->get('coupons')->result_array();
    }

    function getAllCount()
    {
        return $this->db->count_all('coupons');
    }

    function getById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('coupons')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }

    function getByCode($code, $active = -1)
    {
        $this->db->where('code',$code);
        if($active != -1) $this->db->where('active',$active);
        $cat = $this->db->get('coupons')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }








    // COUPONS_USING
    
    function getUsing($coupon_id, $user_login)
    {
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('user_login', $user_login);
        $this->db->limit(1);
        $using = $this->db->get('coupons_using')->result_array();
        return $using;
    }
}
?>