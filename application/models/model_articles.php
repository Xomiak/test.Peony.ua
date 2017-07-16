<?php
class Model_articles extends CI_Model {
    
    function getNewNum()
    {
        $num = $this->db->select_max('num')->get("articles")->result_array();
        if($num[0]['num'] === NULL) return 0;
        else return ($num[0]['num']+1);
    }

    function adminSearch($key, $per_page = -1,$from = -1)
    {
        $this->db->like('name',$key);
        $this->db->or_like('short_content',$key);
        $this->db->or_like('content',$key);
        $this->db->order_by('num','DESC');
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('articles')->result_array();

    }
    
    function getArticles($per_page = -1, $from = -1, $order_by = "DESC", $active = -1)
    {
        if($active != -1) $this->db->where('active',$active);
        $this->db->order_by('num',$order_by);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        return $this->db->get('articles')->result_array();
    }
            
    function getArticleByName($name)
    {
        $this->db->where('name',$name);
        $cat = $this->db->get('articles')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function getRandomArticles($count, $category_id)
    {
        $query = $this->db->query("SELECT * FROM `articles` WHERE active=1 AND category_id=".$category_id." AND id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `articles` ) ORDER BY id LIMIT ".$count.";")->result_array();
        return $query;
    }
    
    function getArticleById($id)
    {
        $this->db->where('id',$id);
        $cat = $this->db->get('articles')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    function getArticleByNum($num)
    {
        $this->db->where('num',$num);
        $cat = $this->db->get('articles')->result_array();
        if(!$cat) return false;
        else return $cat[0];
    }
    
    function searchByName($search, $category_id = -1)
    {
        if($category_id != -1) $this->db->where('category_id',$category_id);
        $this->db->like('name', $search);
        return $this->db->get('articles')->result_array();
    }
    
    function getCountAllArticles($active = -1)
    {
        if($active != -1) $this->db->where('active', $active);
        return $this->db->count_all_results();
    }
    
    function getCountArticlesInCategory($category_id, $active = -1)
    {
        $this->db->where('category_id', $category_id);
        if($active != -1) $this->db->where('active', $active);
        $this->db->from('articles');
        return $this->db->count_all_results();
    }
    
    function getArticlesByCategory($category_id, $per_page = -1, $from = -1, $active = -1, $order_by = "DESC")
    {
        //var_dump("asd");die();
        // �������� ���������, ����� �� ���� ������ ���������� � ���������� ��������
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM articles WHERE";
            if($active != -1) $query .= " active=".$active." AND";
            
            $query .= "(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";
            $query .= " ORDER BY always_first DESC, num ".$order_by;
            if ($per_page != -1 && $from != -1) $query .= ' LIMIT '.$from.','.$per_page;
            
            $this->db->select($query, FALSE);        
            $articles = $this->db->get()->result_array();
        }
        else
        {
            if($active != -1) $this->db->where('active',$active);
            $this->db->where('category_id',$category_id);
            if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
            $this->db->order_by('num','DESC');
            $articles = $this->db->get('articles')->result_array();
        }

        if(!$articles) return false;
        else return $articles;
    }
    
    function getArticlesByParentCategory($category_id, $per_page = -1, $from = -1, $active = -1, $order_by = "DESC")
    {
        $this->db->select('id');
        $this->db->where('parent',$category_id);
        $childs = $this->db->get('categories')->result_array();
        $ccount = count($childs);
        $ch = '';
        for($i = 0; $i < $ccount; $i++)
        {
            $ch[$i] = $childs[$i]['id'];
        }        
        
        if($active != -1) $this->db->where('active',$active);
        $this->db->where_in('category_id',$ch);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num',$order_by);
        $articles = $this->db->get('articles')->result_array();
                
        if(!$articles) return false;
        else return $articles;
    }
    
    // CLIENT //
    
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
        $g = $this->db->get('articles')->result_array();
        
        if(!$g) return false;
        else return $g;
    }    
    
    function getPodGlavnoe($category_id)
    {
        // �������� ���������, ����� �� ���� ������ ���������� � ���������� ��������
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM articles WHERE";
            $query .= " active=1 AND podglavnoe=1 AND";
            
            $query .= "(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";
            $query .= " ORDER BY num DESC";
            $query .= ' LIMIT 1';
            
            $this->db->select($query, FALSE);        
            $articles = $this->db->get()->result_array();
        }
        else{
            $this->db->where('active','1');
            $this->db->where('category_id',$category_id);
            $articles = $this->db->get('articles')->result_array();
        }
        if(!$articles) return false;
        else return $articles[0];
    }
    
    function getArticlesByDate($date)
    {
        $this->db->where('date',$date);
        $this->db->where('active','1');
        $this->db->order_by('num','DESC');
        $art = $this->db->get('articles')->result_array();
        if(!$art) return false;
        else return $art;
    }
    
    function getLastArticleFromCategory($category_id, $limit = 1)
    {
        $this->db->where('category_id',$category_id);
        $this->db->order_by('num','DESC');
        $this->db->limit($limit);
        $art = $this->db->get('articles')->result_array();
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
        $art = $this->db->get('articles')->result_array();
        if(!$art) return false;
        else return $art;
    }
    
    function getLastArticlesAuthor($count)
    {   
        $this->db->where('author', '1');
        $this->db->order_by('num','DESC');
        $this->db->limit($count);
        $art = $this->db->get('articles')->result_array();
        if(!$art) return false;
        else return $art;
    }
    
    function getLastArticlesFromCategory($count, $category_id)
    {
        // �������� ���������, ����� �� ���� ������ ���������� � ���������� ��������
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $query = "* FROM articles WHERE";
            $query .= " active=1 AND";
            
            $query .= "(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")";
            $query .= " ORDER BY num DESC";
            $query .= ' LIMIT '.$count;
            
            $this->db->select($query, FALSE);        
            $articles = $this->db->get()->result_array();
        }
        
        else
        {
            $this->db->where('active','1');
            $this->db->where('category_id',$category_id);
            $this->db->limit($count);
            $articles = $this->db->get('articles')->result_array();
        }
        if(!$articles) return false;
        else return $articles;
    }
    
    function getArticlesByDateAndCategoryId($date, $category_id)
    {
        // �������� ���������, ����� �� ���� ������ ���������� � ���������� ��������
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $this->db->select("* FROM articles WHERE date='".$date."' AND active=1 AND(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\")", FALSE);
            $art = $this->db->get()->result_array();
        }
        else
        {
            $this->db->where('date',$date);
            $this->db->where('category_id',$category_id);
            $this->db->where('active','1');
            $this->db->order_by('num','DESC');
            $art = $this->db->get('articles')->result_array();
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
        // �������� ���������, ����� �� ���� ������ ���������� � ���������� ��������
        $article_in_many_categories = 0;
        $this->db->where('name','article_in_many_categories');
        $aimc = $this->db->get('options')->result_array();
        
        if($aimc) $article_in_many_categories = $aimc[0]['value'];
        
        if($article_in_many_categories == 1)
        {
            $this->db->select("* FROM articles WHERE url='".$url."' AND active=1 AND(category_id=".$category_id." or category_id like \"%*".$category_id."\" or category_id like \"%*".$category_id."*%\" or category_id like \"".$category_id."*%\") LIMIT 1", FALSE);
            $art = $this->db->get()->result_array();
        }
        else
        {            
            $this->db->where('category_id',$category_id);
            $this->db->where('url',$url);
            $this->db->where('active','1');
            $this->db->order_by('num','DESC');
            $art = $this->db->get('articles')->result_array();
        }
        
        
        if(!$art) return false;
        else return $art[0];
    }
    
    function getArticleByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        $this->db->where('active',1);
        $this->db->limit(1);
        $article = $this->db->get('articles')->result_array();
        if(!$article) return false;
        else return $article[0];
    }
    function getArticlesByUrl($url, $active = -1)
    {
        $this->db->where('url',$url);
        $this->db->where('active',1);
        $article = $this->db->get('articles')->result_array();
        if(!$article) return false;
        else return $article;
    }
    
    function Search($key)
    {
        //$this->db->select("* FROM articles, shop WHERE articles.name LIKE '%".$key."%'");
        
        $this->db->like('name',$key);
        $this->db->or_like('content',$key);
        $articles = $this->db->get('articles')->result_array();
        if(!$articles) return false;
        else return $articles;
    }
    
    function Archive($date)
    {
        $this->db->where('active',1);
        $this->db->where('date',$date);
        $articles = $this->db->get('articles')->result_array();
        if(!$articles) return false;
        else return $articles;
    }
    
    function countPlus($id)
    {
        $this->db->where('id',$id);
        $articles = $this->db->get('articles')->result_array();
        if($articles)
        {
            $articles = $articles[0];
            $count = $articles['count'] + 1;
            $dbins = array('count' => $count);
            $this->db->where('id',$id)->update('articles',$dbins);
        }
    }
    
    function getUserArticles($login, $active = -1, $per_page = -1, $from = -1)
    {
        $this->db->where('login', $login);
        if($active != -1) $this->db->where('active',$active);
        if ($per_page != -1 && $from != -1) $this->db->limit($per_page, $from);
        $this->db->order_by('num', 'DESC');
        $articles = $this->db->get('articles')->result_array();
        if(!$articles) return false;
        else return $articles;
    }
    
    function getUserArticlesCount($login)
    {
        $this->db->where('login', $login);
        $this->db->where('active',1);
        $articles = $this->db->get('articles')->result_array();
        if(!$articles) return 0;
        else return count($articles);
    }
    
    function getLastImportant($active = -1, $category_id = -1, $count = 1)
    {
        $this->db->where('important', 1);
        if($active != -1) $this->db->where('active',$active);
        if($category_id != -1) $this->db->where('category_id',$category_id);        
        $this->db->limit($count);
        $this->db->order_by('num', 'DESC');
        $li = $this->db->get('articles')->result_array();
        if($count != 1) return $li;
        else
        {
            if(!$li) return false;
            else return $li[0];
        }
    }
    
    
    //////////////////////////////////////
    
    function getBottom($bottom, $limit)
    {
        $this->db->where('bottom'.$bottom, 1);
        $this->db->where('active', 1);
        $this->db->limit($limit);
        $this->db->order_by('num', 'DESC');
        return $this->db->get('articles')->result_array();
    }
}
?>