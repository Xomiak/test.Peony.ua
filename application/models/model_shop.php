<?php
class Model_shop extends CI_Model {
     
    function getOrdersByUserId($user_id, $status = -1, $per_page = -1, $from = -1) {
        $this->db->where('user_id', $user_id);
        if ($status != -1)
            $this->db->where('status', $status);
        $this->db->order_by('id', 'DESC');
        if ($per_page != -1 && $from != -1)
            $this->db->limit($per_page, $from);
        $orders = $this->db->get('orders')->result_array();
        //vd($orders);
        if ($orders)
            return $orders;
        else
            return false;
    }
    
    function getNewNum()
    {
        $num = $this->db->select_max('num')->get("shop")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }
    
    function getArticles($per_page = -1, $from = -1, $order_by = "DESC", $active = -1, $only_with_images = false, $sort_by = 'num', $filters = false)
    {
        if($active != -1) $this->db->where('active',$active);
        if($only_with_images) $this->db->where('image <>','');
        if(is_array($filters)){
            foreach ($filters as $key => $value) {
                if($key != 'articul')
                    $this->db->where($key, $value);
            }
        }

        if(isset($filters['articul']))
            $this->db->like('articul', $filters['articul']);

        $this->db->order_by($sort_by, $order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('shop')->result_array();
    }

    function getArticlesForExport($per_page = -1, $from = -1, $sort_by = "category_id", $order_by = "DESC", $only_with_images = true, $warehouseOnly = true)
    {
        $this->db->where('active',1);
        if($only_with_images) $this->db->where('image <>','');
        if($warehouseOnly) $this->db->where('warehouse_sum >',0);
        $this->db->order_by($sort_by,$order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('shop')->result_array();
    }

    function getArticlesSortedByCategory($per_page = -1, $from = -1, $order_by = 'ASC', $warehouseOnly = false){
        $this->db->where('active',1);
        if($warehouseOnly) $this->db->where('warehouse_sum >',0);
        $this->db->order_by('category_id',$order_by);
        $this->db->order_by('id',$order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('shop')->result_array();
    }
            
    function getArticleByName($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('shop')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getRandomArticles($count, $category_id)
    {
        $query = $this->db->query("SELECT * FROM `shop` WHERE active=1 AND category_id=".$category_id." AND id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `shop` ) ORDER BY id LIMIT ".$count.";")->result_array();
        return $query;
    }
    
    function getArticleById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('shop')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getArticleByNum($num)
    {
        $this->db->where('num',$num);
        $cat = $this->db->get('shop')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getCountArticlesInBrand($brand_id, $active = -1)
    {
        $max_price = $this->session->userdata('f_price_max');
        $this->db->like('brand_id', $brand_id);
        if($active != -1) $this->db->where('active', $active);
        if($max_price) $this->db->where('price <=', $max_price);

        $this->db->from('shop');
        return $this->db->count_all_results();
        
    }

    
    
    function getArticlesByBrand($brand_id, $per_page = -1, $from = -1, $active = -1, $order_by = "DESC", $sort_by = 'num')
    {
        $max_price = $this->session->userdata('f_price_max');
        
        if($active != -1) $this->db->where('active',$active);
        $this->db->where('brand_id',$brand_id);
        if($max_price) $this->db->where('price <=', $max_price);
        
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by($sort_by, $order_by);
        $shop = $this->db->get('shop')->result_array();

        if(!$shop) return false;
        else return $shop;
    }

    function getCountAll($filters = false){
        if(is_array($filters)){
            foreach ($filters as $key => $value) {
                if($key != 'articul')
                    $this->db->where($key, $value);
            }
        }

        $this->db->from('shop');
        return $this->db->count_all_results();
    }

    function getActionsCount()
    {
        $time = time();
        $this->db->where('akciya_start_unix <', $time);
        $this->db->where('akciya_end_unix >', $time);
        $this->db->where('active', 1);

        $this->db->from('shop');
        return $this->db->count_all_results();
    }

    function getActions($per_page = -1, $from = -1)
    {
        $time = time();
        $this->db->where('akciya_start_unix <', $time);
        $this->db->where('akciya_end_unix >', $time);
        $this->db->where('active', 1);

        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);

        $shop = $this->db->get('shop')->result_array();
        return $shop;
    }

    
    function searchByName($search, $category_id = -1, $active = 1, $limit = -1, $order_by = 'DESC', $sort_by = 'count', $notConnectedOnly = false)
    {
        $this->db->where('active', $active);
        if($category_id != -1) $this->db->where('category_id',$category_id);
        $this->db->where('name', $search);
        if($notConnectedOnly) $this->db->where('base_ids',NULL);
        $this->db->order_by($sort_by, $order_by);
        if($limit != -1) $this->db->limit($limit);
        return $this->db->get('shop')->result_array();
    }


    function searchByNameArticulColor($name = false, $articul = false, $color = false, $active = 1)
    {
        if($active != -1)
            $this->db->where('active', $active);
        if($articul) $this->db->where('articul',$articul);
        if($name) $this->db->like('name',$name);
        if($color) $this->db->like('color',$color);
        $this->db->limit(1);
        $ret = $this->db->get('shop')->result_array();
        if($ret)
        {
            return $ret[0];
        }

        return false;
    }

    function searchByNameArticulColorArray($name = false, $articul = false, $color = false, $notConnectedOnly = false)
    {

        if($name) $this->db->where('name',$name);
        if($notConnectedOnly) $this->db->where('base_ids',NULL);
        if($color) $this->db->where('color',$color);
        if($articul) $this->db->like('articul',$articul);
        $this->db->limit(1);
        $ret = $this->db->get('shop')->result_array();

        return $ret;
    }

    function searchByBaseId($base_id){
        $this->db->like('base_ids','"'.$base_id.'"');
        $this->db->limit(1);
        $ret = $this->db->get('shop')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function searchByBaseIdCount($base_id){
        $this->db->like('base_ids','"'.$base_id.'"');
        $this->db->from('shop');
        return $this->db->count_all_results();

        return $ret;
    }
    
    function getCountAllArticles($active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        return $this->db->count_all_results();
    }
    
    function getCountArticlesInCategoryByColor($category_id, $color, $active = -1)
    {
        $brand_id = $this->session->userdata('f_brand_id');
        $max_price = $this->session->userdata('f_price_max');
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM shop WHERE";            
            if($active != -1) $query .= " active=".$active." AND";
            
            if($color) $query .= " color = '".$color."' AND";
            
            $query .= " (category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";

            //$query .= " ORDER BY ".$sort_by." ".$order_by;
            //if ($per_page != -1 && $from != -1) $query .= ' LIMIT '.$from.','.$per_page;
            
            $this->db->select($query, FALSE);        
            $shop = $this->db->get()->result_array();
            return count($shop);
        }
        else
        {
            $this->db->like('category_id', $category_id);
            if($active != -1) $this->db->where('active', $active);
            
            if($color) $this->db->like('color', $color);
            
            $this->db->from('shop');
            $ret = $this->db->count_all_results();
            //var_dump($ret);
            return $ret;
        }
    }
    
    function getCountArticlesInCategory($category_id, $active = -1, $razmer = false, $color = false, $inWarehouseOnly = true, $filters = false)
    {
        $brand_id = $this->session->userdata('f_brand_id');
        $max_price = $this->session->userdata('f_price_max');
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM shop WHERE";            
            if($active != -1) $query .= " active=".$active." AND";

            if(is_array($filters)){
                foreach ($filters as $key => $value) {
                    if($key != 'articul')
                        $query .= " " . $key . "=" . $value . " AND";
                }
            }

            $query .= " ended = 0 AND";
            if($inWarehouseOnly)
                $query .= " warehouse_sum > 0 AND";
            
            if($razmer) $query .= " razmer LIKE '%".$razmer."%' AND";
            if($color) $query .= " color = '".$color."' AND";
            
            $query .= " (category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";

            if(isset($filters['articul']))
                $query .= " articul LIKE" . $filters['articul'] . " AND";
            //$query .= " ORDER BY ".$sort_by." ".$order_by;
            //if ($per_page != -1 && $from != -1) $query .= ' LIMIT '.$from.','.$per_page;
            
            $this->db->select($query, FALSE);        
            $shop = $this->db->get()->result_array();
            return count($shop);
        }
        else
        {
            $this->db->like('category_id', $category_id);
            if($active != -1) $this->db->where('active', $active);
            
            if($razmer) $this->db->where('razmer', $razmer);
            if($color) $this->db->like('color', $color);
            
            $this->db->from('shop');
            $ret = $this->db->count_all_results();
            //var_dump($ret);
            return $ret;
        }
    }
    
        
    function getArticlesByCategory($category_id, $per_page = -1, $from = -1, $active = -1, $order_by = "DESC", $sort_by = 'num', $razmer = false, $color = false, $glavnoe = -1, $inWarehouseOnly = true, $filters = false)
    {
        $brand_id = $this->session->userdata('f_brand_id');
        $max_price = $this->session->userdata('f_price_max');
        //var_dump("asd");die();
        // Получаем настройки, будет ли одна статья находиться в нескольких разделах
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM shop WHERE";            
            if($active != -1) $query .= " active=".$active." AND";
            if(is_array($filters)){
                foreach ($filters as $key => $value) {
                    if($key != 'articul')
                        $query .= " " . $key . "=" . $value . " AND";
                }
            }
            $query .= " ended = 0 AND";

            if($inWarehouseOnly)
                $query .= " warehouse_sum > 0 AND";

            if($razmer) {
                $razmer = '*'.$razmer.'*';
                $query .= " razmer_filter LIKE '%".$razmer."%' AND";
            }

            if($color) $query .= " color = '".$color."' AND";

            if($glavnoe != -1 && $glavnoe != '') $query .= " glavnoe=".$glavnoe." AND";
            
            $query .= " (category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";

            if(isset($filters['articul']))
                $query .= " articul LIKE" . $filters['articul'] . " AND";

            $query .= " ORDER BY ".$sort_by." ".$order_by;
            if ($per_page != -1 && $from != -1) $query .= ' LIMIT '.$from.','.$per_page;
            
            $this->db->select($query, FALSE);
            $shop = $this->db->get()->result_array();
        }
        else
        {
            if($active != -1) $this->db->where('active',$active);
            $this->db->where('category_id',$category_id);
            if($brand_id) $this->db->where('brand_id', $brand_id);
            if($max_price) $this->db->where('price <=', $max_price);

            if($razmer) {
                $razmer = '*'.$razmer.'*';
                $this->db->like('razmer_filter', $razmer);
            }
            if($color) $this->db->like('color', $color);
            
            if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
            $this->db->order_by($sort_by, $order_by);
            $shop = $this->db->get('shop')->result_array();
            //var_dump(count($shop));
        }

        if(!$shop) return false;
        else return $shop;
    }
    
    function getCountArticlesByParentCategory($parent_category_id, $active, $razmer = false, $color = false)
    {
        $this->db->where('parent_category_id', $parent_category_id);
        if($active != -1) $this->db->where('active', $active);
        
        if($razmer) {
            $razmer = '*'.$razmer.'*';
            $this->db->like('razmer_filter', $razmer);
        }
        if($color) $this->db->like('color', $color);
        
        $this->db->from('shop');
        return $this->db->count_all_results();
    }
    
    function getArticlesByParentCategory($parent_category_id, $per_page = -1, $from = -1, $active = -1, $order_by = "DESC", $sort_by = 'num', $razmer = false, $color = false)
    {
        $this->db->where('parent_category_id', $parent_category_id);
        $this->db->where('ended', 0);
        if($active != -1) $this->db->where('active', $active);

        if($razmer) {
            $razmer = '*'.$razmer.'*';
            $this->db->like('razmer_filter', $razmer);            
        }
        if($color) $this->db->like('color', $color);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by($sort_by, $order_by);
        $shop = $this->db->get('shop')->result_array();
                
        if(!$shop) return false;
        else return $shop;
    }
    
    // CLIENT //

    function getColorByName($name){
        $this->db->where('name', $name);
        $this->db->limit(1);
        $ret = $this->db->get('color')->result_array();
        if(isset($ret[0])) return $ret[0];
        else{
            $dbins = array('name' => $name);
            $this->db->insert('color',$dbins);
            return $this->getColorByName($name);
        }
        return false;
    }
    
    function getGlavnoe($count = -1, $per_page = -1, $from = -1, $active = -1, $category_id = -1)
    {
        $this->db->where('glavnoe','1');
        if($active != -1) $this->db->where('active',$active);
        if($category_id != -1) $this->db->where('category_id',$category_id);
        
        $this->db->order_by('num','DESC');
        if($count != -1)
        {
            $this->db->limit($count);
        }
        else
            if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $g = $this->db->get('shop')->result_array();
        
        if(!$g) return false;
        else return $g;
    }    
    
    function getPodGlavnoe($category_id)
    {
        // Получаем настройки, будет ли одна статья находиться в нескольких разделах
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM shop WHERE";
            $query .= " active=1 AND podglavnoe=1 AND";
            
            $query .= "(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";
            $query .= " ORDER BY num DESC";
            $query .= ' LIMIT 1';
            
            $this->db->select($query, FALSE);        
            $shop = $this->db->get()->result_array();
        }
        else{
            $this->db->where('active','1');
            $this->db->where('category_id',$category_id);
            $shop = $this->db->get('shop')->result_array();
        }
        if(!$shop) return false;
        else return $shop[0];
    }
    
    function getArticlesByDate($date)
    {
        $this->db->where('date',$date);
        $this->db->where('active','1');
        $this->db->order_by('num','DESC');
        $art = $this->db->get('shop')->result_array();
        if(!$art) return false;
        else return $art;
    }

    function getArticlesWithDiscount()
    {
        $this->db->where('discount >',0);
        $this->db->where('active','1');
        $this->db->order_by('num','DESC');
        $art = $this->db->get('shop')->result_array();
        if(!$art) return false;
        else return $art;
    }
    
    function getProductById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('shop')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getLastArticleFromCategory($category_id, $limit = 1)
    {
        $this->db->where('category_id',$category_id);
        $this->db->order_by('num','DESC');
        $this->db->limit($limit);
        $art = $this->db->get('shop')->result_array();
        if(!$art) return false;
        elseif($limit == 1) return $art[0];
        else return $art;
    }
    
    function getLastArticles($count)
    {
        $this->db->where('parent','7');
        $cats = $this->db->get('categories')->result_array();
        
        $this->db->where('active','1');
        
        if($cats)
        {
            $count = count($cats);
            for($i = 0; $i < $count; $i++)
            {
                $this->db->where('category_id <> ',$cats[$i]['id']);
            }
        }
        
        $this->db->order_by('num','DESC');
        $this->db->limit($count);
        $art = $this->db->get('shop')->result_array();
        if(!$art) return false;
        else return $art;
    }
    
    function getLastArticlesAuthor($count)
    {   
        $this->db->where('author', '1');
        $this->db->order_by('num','DESC');
        $this->db->limit($count);
        $art = $this->db->get('shop')->result_array();
        if(!$art) return false;
        else return $art;
    }
    
    function getLastArticlesFromCategory($count, $category_id)
    {
        // Получаем настройки, будет ли одна статья находиться в нескольких разделах
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM shop WHERE";
            $query .= " active=1 AND";
            
            $query .= "(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";
            $query .= " ORDER BY num DESC";
            $query .= ' LIMIT '.$count;
            
            $this->db->select($query, FALSE);        
            $shop = $this->db->get()->result_array();
        }
        
        else
        {
            $this->db->where('active','1');
            $this->db->where('category_id',$category_id);
            $this->db->limit($count);
            $shop = $this->db->get('shop')->result_array();
        }
        if(!$shop) return false;
        else return $shop;
    }
    
    function getArticlesByDateAndCategoryId($date, $category_id)
    {
        // Получаем настройки, будет ли одна статья находиться в нескольких разделах
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $this->db->select("* FROM shop WHERE date='".$date."' AND active=1 AND(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")", FALSE);
            $art = $this->db->get()->result_array();
        }
        else
        {
            $this->db->where('date',$date);
            $this->db->where('category_id',$category_id);
            $this->db->where('active','1');
            $this->db->order_by('num','DESC');
            $art = $this->db->get('shop')->result_array();
        }
        
        if(!$art) return false;
        else return $art;
    }
    
    function getArticleByUrlAndCategoryId($url, $category_id)
    {        
        $this->db->where('id',$category_id);
        $this->db->where('active', 1);
        $this->db->limit(1);
        $cat = $this->db->get('categories')->result_array();
        if(!$cat) return false;
        $cat = $cat[0];
        if($cat['parent'] != 0)
        {
            $this->db->where('id',$cat['parent']);
            $this->db->where('active', 1);
            $this->db->limit(1);
            $cat = $this->db->get('categories')->result_array();
            if(!$cat) return false;
            //$cat = $cat[0];
        }
        // Получаем настройки, будет ли одна статья находиться в нескольких разделах
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $this->db->select("* FROM shop WHERE url='".$url."' AND active=1 AND(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\") LIMIT 1", FALSE);
            $art = $this->db->get()->result_array();
        }
        else
        {            
            $this->db->where('category_id',$category_id);
            $this->db->where('url',$url);
            $this->db->where('active','1');
            $this->db->order_by('num','DESC');
            $art = $this->db->get('shop')->result_array();
        }
        
        
        if(!$art) return false;
        else return $art[0];
    }
    
    function getArticleByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        $this->db->where('active',1);
        $this->db->limit(1);
        $article = $this->db->get('shop')->result_array();
        if(!$article) return false;
        else return $article[0];
    }
    function getArticlesByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        $this->db->where('active',1);
        $article = $this->db->get('shop')->result_array();
        if(!$article) return false;
        else return $article;
    }

//    function Search($key,$per_page = -1,$from = -1)
//    {
//        $this->db->having('active', 1);
//        $this->db->like('name',$key);
//        $this->db->or_like('articul',$key);
//        $this->db->or_like('content',$key);
//        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
//        $this->db->order_by('count', 'DESC');
//        $shop = $this->db->get('shop')->result_array();
//        if(!$shop) return false;
//        else return $shop;
//    }

    function Search($key,$per_page = -1,$from = -1, $active = 1)
    {
        if($active != -1)
            $this->db->where('active',$active);
        $this->db->where('name',$key);
        $this->db->or_where('articul',$key);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $shop1 = $this->db->get('shop')->result_array();

        if($active != -1)
            $this->db->where('active',$active);
        $this->db->like('name',$key,'after');
        $this->db->or_like('articul',$key,'after');
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $shop2 = $this->db->get('shop')->result_array();

        if($active != -1)
            $this->db->where('active',$active);
        $this->db->like('name',$key,'both');
        $this->db->or_like('articul',$key,'both');
        $this->db->or_like('id',$key,'both');
        $this->db->or_like('content',$key,'both');
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('id',"DESC");
        $shop3 = $this->db->get('shop')->result_array();

        $shop = array_merge($shop1,$shop2,$shop3);

        

        $shop = arrayDelCopies($shop);

        if($per_page != -1 && $from != -1) {
            $shop = arrayGetPart($shop, $per_page, $from);
        }

        if(!$shop) return false;
        else return $shop;
    }

    function adminSearch($key, $per_page = -1,$from = -1)
    {
        $this->db->where('name',$key);
        $this->db->or_where('articul',$key);
        $shop1 = $this->db->get('shop')->result_array();

        //if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        //$this->db->order_by('count', 'DESC');

        $this->db->like('name',$key,'after');
        $this->db->or_like('articul',$key,'after');
        $shop2 = $this->db->get('shop')->result_array();

        $this->db->like('name',$key,'both');
        $this->db->or_like('articul',$key,'both');
        $shop3 = $this->db->get('shop')->result_array();

        $shop = array_merge($shop1,$shop2,$shop3);
        //vd($shop);

        $shop = arrayDelCopies($shop);

        if($per_page != -1 && $from != -1) {
            $shop = arrayGetPart($shop, $per_page, $from);
        }

        if(!$shop) return false;
        else return $shop;
    }

    function adminSearchCount($key)
    {
        $this->db->select('id');
        $this->db->where('name',$key);
        $this->db->or_where('articul',$key);
        $shop1 = $this->db->get('shop')->result_array();

        //if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        //$this->db->order_by('count', 'DESC');

        $this->db->select('id');
        $this->db->like('name',$key,'after');
        $this->db->or_like('articul',$key,'after');
        $shop2 = $this->db->get('shop')->result_array();

        $this->db->select('id');
        $this->db->like('name',$key,'both');
        $this->db->or_like('articul',$key,'both');
        $shop3 = $this->db->get('shop')->result_array();

        $shop = array_merge($shop1,$shop2,$shop3);

        $shop = arrayDelCopies($shop);

        return count($shop);
    }
    
    function Archive($date)
    {
        $this->db->where('active',1);
        $this->db->where('date',$date);
        $shop = $this->db->get('shop')->result_array();
        if(!$shop) return false;
        else return $shop;
    }
    
    function countPlus($id)
    {
        $this->db->where('id',$id);
        $shop = $this->db->get('shop')->result_array();
        if($shop)
        {
            $shop = $shop[0];
            $count = $shop['count'] + 1;
            $dbins = array('count' => $count);
            $this->db->where('id',$id)->update('shop',$dbins);
        }
    }
    
    function getUserArticles($login, $active = -1, $per_page = -1, $from = -1)
    {
        $this->db->where('login', $login);
        if($active != -1) $this->db->where('active',$active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num', 'DESC');
        $shop = $this->db->get('shop')->result_array();
        if(!$shop) return false;
        else return $shop;
    }
    
    function getUserArticlesCount($login)
    {
        $this->db->where('login', $login);
        $this->db->where('active',1);
        $shop = $this->db->get('shop')->result_array();
        if(!$shop) return 0;
        else return count($shop);
    }
    
    function getLastImportant($active = -1, $category_id = -1, $count = 1)
    {
        $this->db->where('important', 1);
        if($active != -1) $this->db->where('active',$active);
        if($category_id != -1) $this->db->where('category_id',$category_id);        
        $this->db->limit($count);
        $this->db->order_by('num', 'DESC');
        $li = $this->db->get('shop')->result_array();
        if($count != 1) return $li;
        else
        {
            if(!$li) return false;
            else return $li[0];
        }
    }


    


    function getAllSizes()
    {
        $this->db->where('showed', 1);
        $this->db->order_by('name', 'ASK');
        return $this->db->get('razmer')->result_array();
    }
    
    //////////////////////////////////////
    
    function getBottom($bottom, $limit)
    {
        $this->db->where('bottom'.$bottom, 1);
        $this->db->where('active', 1);
        $this->db->limit($limit);
        $this->db->order_by('num', 'DESC');
        return $this->db->get('shop')->result_array();
    }
    
    ///////
    // ORDERS - ОБРАБОТКА ЗАКАЗОВ
    function getOrders($per_page = -1, $from = -1, $status = -1)
    {
        if($status != -1) $this->db->where('status',$status);
        $this->db->order_by('unix','DESC');
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('orders')->result_array();
    }

    function getOrdersCount($per_page = -1, $from = -1, $status = -1)
    {
        if($status != -1) $this->db->where('status',$status);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->from('orders');
        return $this->db->count_all_results();
    }

    function getUserOrdersCount($user_id, $status = -1)
    {
        $this->db->where('user_id',$user_id);
        if($status != -1) $this->db->where('status',$status);
        $this->db->order_by('id','DESC');
        $this->db->from('orders');
        return $this->db->count_all_results();
    }
    
    function getOrderById($id)
    {
        //vd($id);
        if(isset($id['id'])) $id = $id['id'];
        $this->db->where('id', $id);
        $this->db->limit(1);
        $order = $this->db->get('orders')->result_array();
        if($order) return $order[0];
        else return false;
    }
    
    function getToMail()
    {
        $this->db->where('tomail', 1);
        return $this->db->get('shop')->result_array();
    }
    
    /////////////


// COUNTRIES, CITIES AND ADDRESSESS

    function getCountries()
    {
        $this->db->where('active', 1);
        return $this->db->get('countries')->result_array();
    }
    function getCountryById($id)
    {
        $this->db->where('id', $id);
        $ret = $this->db->get('countries')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getCountryByName($name)
    {
        $this->db->where('name', $name);
        $ret = $this->db->get('countries')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getCities()
    {
        $this->db->where('active', 1);
        return $this->db->get('cities')->result_array();
    }
    function getCityById($id)
    {
        $this->db->where('id', $id);
        $ret = $this->db->get('cities')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getCityByName($name, $country_id = false)
    {
        $this->db->where('name', $name);
        if($country_id) $this->db->where('country_id', $country_id);
        $ret = $this->db->get('cities')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }


    function getAllAddrByUserId($user_id){
        $this->db->where('user_id', $user_id);
        $result = $this->db->get('addr')->result_array();
        return $result;
    }

    function getAddrByLogin($login){
        $this->db->where('login', $login);
        $this->db->order_by('default','DESC');
        $this->db->order_by('id','DESC');
        return $this->db->get('addr')->result_array();
    }

    function getDefaultAddrByLogin($login){
        $this->db->where('login', $login);
        $this->db->where('default', 1);
        $this->db->limit(1);
        $result = $this->db->get('addr')->result_array();
        if(isset($result[0])) return $result[0];

        return false;
    }

    function getDefaultAddrByUserId($user_id){
        $this->db->where('user_id', $user_id);
        $this->db->where('default', 1);
        $this->db->limit(1);
        $result = $this->db->get('addr')->result_array();
        if(isset($result[0])) return $result[0];

        return false;
    }

    function getAddr($id){
        $addr = $this->db->where('id', $id)->limit(1)->get('addr')->result_array();
        if(isset($addr[0])) return $addr[0];

        return false;
    }
// **COUNTRIES, CITIES AND ADDRESSESS

    function getDeliveries($active = -1)
    {
        if($active != -1)
            $this->db->where('active', $active);
        return $this->db->get('delivery')->result_array();
    }
    function getDeliveryById($id)
    {
        $this->db->where('id', $id);
        $ret = $this->db->get('delivery')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }
    function getDeliveryByName($name)
    {
        $this->db->where('name', $name);
        $ret = $this->db->get('delivery')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }
    function getDeliveriesByCountryId($country_id)
    {
        $this->db->where('country_id', $country_id);
        return $this->db->get('delivery')->result_array();
    }

    function getSMA()
    {
        $this->db->where('sended', 0);
        return $this->db->get('say_me_available')->result_array();
    }
    function setSMA($dbins)
    {
        $this->db->where('id', $dbins['id']);
        $this->db->limit(1);
        return $this->db->update('say_me_available', $dbins);
    }


    function getCountOrdersByDate($date)
    {
        $this->db->where('date', $date);
        $this->db->from('orders');
        return $this->db->count_all_results();
    }

    function getOrdersByDate($date)
    {
        $this->db->where('date', $date);
        return $this->db->get('orders')->result_array();
    }

    ///// ВАЛЮТЫ: ...........

    function getCurrencies($active = -1){
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('main','DESC');

        return $this->db->get('currencies')->result_array();
    }

    function getCurrency($code){
        $this->db->where('code',$code);
        $this->db->limit(1);
        $ret = $this->db->get('currencies')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getCurrencyByCode($code){
        $code = mb_strtoupper($code);
        $this->db->where('code',$code);
        $this->db->limit(1);
        $ret = $this->db->get('currencies')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getCurrencyById($id){
        $this->db->where('id',$id);
        $this->db->limit(1);
        $ret = $this->db->get('currencies')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getMainCurrency(){
        $this->db->where('main',1);
        $this->db->limit(1);
        $ret = $this->db->get('currencies')->result_array();
        if(isset($ret[0])) return $ret[0]['code'];

        return false;
    }

    function getCurrencyValue($code){
        $cur = $this->getCurrency($code);
        if(isset($cur['value'])) return $cur['value'];

        return false;
    }


    // SPECIFICATIONS

    function getSpecifications($per_page = -1, $from = -1){
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('id','DESC');
        $shop = $this->db->get('specifications')->result_array();
        return $shop;
    }

    function getSpecificationsCount(){
        $this->db->from('specifications');
        return $this->db->count_all_results();
    }

    function getSpecificationById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $order = $this->db->get('specifications')->result_array();
        if($order) return $order[0];
        else return false;
    }

    // MAILER //
    // old functions:
    function getForMailer($type)
    {
        $this->db->where('mailer_'.$type, 1);
        $this->db->where('active', 1);
        $this->db->order_by('discount','ASC');
        return $this->db->get('shop')->result_array();
    }

    function getMailerCount($type)
    {
        $this->db->where('mailer_'.$type, 1);
        $this->db->where('active', 1);

        $this->db->from('shop');
        return $this->db->count_all_results();

    }
    /////////////
    
    function getMailerCountByName($type, $name){
        $this->db->where('name', $name);
        $this->db->where('mailer_'.$type, 1);
        $this->db->from('shop');
        return $this->db->count_all_results();
    }

    function getMailerCountByArticul($type, $articul){
        $this->db->where('active', 1);
        $this->db->where('articul', $articul);
        $this->db->where('mailer_'.$type, 1);
        $this->db->from('shop');
        return $this->db->count_all_results();
    }

    function getShopIdByArticulWithMaxWarehouse($articul){
        $this->db->where('active', 1);
        $this->db->where('articul', $articul);
        $this->db->order_by('warehouse_sum', 'DESC');
        $ret = $this->db->get('shop')->result_array();
        if(isset($ret[0]['id'])) return $ret[0]['id'];

        return false;
    }

    function getMaileerShop($type){
        $this->db->where('active', 1);
        $this->db->where('mailer_'.$type, 1);
        $this->db->order_by('num','DESC');
        return $this->db->get('shop')->result_array();
    }

    function getInImportByGoodID($GoodId){
        $this->db->where('GoodID', $GoodId);
        $this->db->limit(1);
        $ret = $this->db->get('import')->result_array();
        if(isset($ret[0])) return $ret[0];

        return false;
    }

    function getOrderByPromId($prom_id){
        $this->db->where('prom_id', $prom_id);
        $this->db->limit(1);
        $order = $this->db->get('orders')->result_array();
        if(isset($order[0])) return $order[0];

        return false;
    }



    /** ADDR */


    function getStatuses($active = -1){
        if($active != -1)
            $this->db->where('active', $active);
        return $this->db->get('orders_statuses')->result_array();
    }

    function getStatusBy($by, $value, $getCell = 'id', $limit = 1){
        $this->db->where($by, $value);
        $this->db->limit($limit);
        $result = $this->db->get('orders_statuses')->result_array();
        if($result)
            return $result[0][$getCell];

        return false;
    }
}
?>