<?php
class Model_vk extends CI_Model {

    function getByUrl($url)
    {
        $this->db->where('url', $url);
        $this->db->limit(1);
        $tkdz = $this->db->get('tkdz')->result_array();
        if(isset($tkdz[0])) return $tkdz[0];

        return false;
    }

    function isCategoryExists($category_id, $login, $group_id)
    {
        $this->db->where('category_id', $category_id);
        $this->db->where('login', $login);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $this->db->from('vkmarket_category_to_vk');
        $res = $this->db->count_all_results();
        if($res > 0) return true;

        return false;
    }

    function getMarketCategory($category_id, $vk_user_id, $group_id, $login = false){
        if(!$login) $login = userdata('login');
        $this->db->where('category_id', $category_id);
        $this->db->where('user_id', $vk_user_id);
        $this->db->where('login', $login);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);

        $res = $this->db->get('vkmarket_category_to_vk')->result_array();
        //vd($res);
        if($res) return $res[0]['vk_album_id'];

        return false;
    }

    function isProductExists($shop_id, $vk_user_id, $group_id, $login = false)
    {
        if(!$login) $login = userdata('login');
        $this->db->where('shop_id', $shop_id);
        $this->db->where('user_id', $vk_user_id);
        $this->db->where('group_id', $group_id);
        $this->db->where('login', $login);
        $this->db->limit(1);
        //$this->db->from('vkmarket_product_to_vk');

        $res = $this->db->get('vkmarket_product_to_vk')->result_array();
        //echo $this->db->last_query();
        //vd($res);

        if(isset($res[0]['vk_product_id'])) return $res[0];

        return false;
    }

    function getVkProductId($shop_id, $group_id = '58777985', $user_id = false){
        $this->db->where('shop_id', $shop_id);
        if($user_id)
            $this->db->where('user_id', $user_id);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vkmarket_product_to_vk')->result_array();
        if($ret) return $ret[0]['vk_product_id'];

        return false;
    }

    function getVkAlbumIdByCategoryId($category_id, $group_id = '58777985', $user_id = false){
        $this->db->where('category_id', $category_id);
        if($user_id)
            $this->db->where('user_id', $user_id);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vkmarket_category_to_vk')->result_array();
        if($ret) return $ret[0]['vk_album_id'];

        return false;
    }

    function getAlbumByName($name, $user_id = false, $group_id = false)
    {
        if(!$user_id) $user_id = userdata('vk_user_id');
        if(!$group_id) $group_id = userdata('group_id');
        $this->db->where('name', $name);
        $this->db->where('user_id', $user_id);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vkmarket_category_to_vk')->result_array();
//vd($ret);
        //      echo $this->db->last_query();
        if(isset($ret[0])) return $ret[0]['vk_album_id'];

        return false;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////

    function getAlbumId($category_id, $user_login, $group_id)
    {
        $this->db->where('category_id', $category_id);
        $this->db->where('user_login', $user_login);
        $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vkmarket_category_to_vk')->result_array();
//vd($ret);
  //      echo $this->db->last_query();
        if(isset($ret[0])) return $ret[0]['vk_album_id'];

        return false;
    }



    function  getUserAlbumId($category_id, $vk_user_id = false, $group_id = false, $login = false){
        if(!$vk_user_id) $vk_user_id = userdata('vk_user_id');
        if(!$login) $login = userdata('login');
        $this->db->where('category_id', $category_id);
        $this->db->where('user_id', $vk_user_id);
        if($group_id) $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vk_albums')->result_array();
        if(isset($ret[0])) return $ret[0]['vk_album_id'];
        return false;
    }

    function  getUserAlbum($category_id, $vk_user_id = false, $group_id = false, $login = false){
        if(!$vk_user_id) $vk_user_id = userdata('vk_user_id');
        if(!$login) $login = userdata('login');
        $this->db->where('category_id', $category_id);
        $this->db->where('user_id', $vk_user_id);
        if($group_id) $this->db->where('group_id', $group_id);
        $this->db->limit(1);
        $ret = $this->db->get('vk_albums')->result_array();
        if(isset($ret[0])) return $ret[0];
        return false;
    }

    function getImageByAlbumUserIdProductId($album_id, $user_id, $product_id, $group_id = false, $login = false){
        if(!$login) $login = userdata('login');
        if($group_id) $this->db->where('group_id', $group_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);
        $this->db->where('album_id', $album_id);
        $this->db->limit(1);
        $ret = $this->db->get('vk_albums_images')->result_array();
        if(isset($ret[0])) return $ret[0];
        return false;
    }

    function getExportLogByRandomId($random_id){
        $this->db->where('random_id', $random_id);
        $this->db->limit(1);
        $ret = $this->db->get('export_logs')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getVkMarketProducts($login, $shop_id){
        $this->db->where('login', $login);
        $this->db->where('shop_id', $shop_id);
        return $this->db->get('vkmarket_product_to_vk')->result_array();
    }

    function getVkAlbumProducts($login, $shop_id){
        $this->db->where('login', $login);
        $this->db->where('product_id', $shop_id);
        return $this->db->get('vk_albums_images')->result_array();
    }
}