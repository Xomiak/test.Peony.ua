<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
            $this->load->model('Model_gallery','gallery');
	    $this->load->model('Model_comments','comments');
	    $this->load->model('Model_users','users');
	    if($this->model_options->getOption('gallery_active') != 1) err404();
	    $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
	    isLogin();
        }
	
	public function index()
	{
            $options = $this->gallery->getMain();
	    
		$data['images'] 	= false;
		$data['pager']		= '';
		$data['categories']	= false;
		$page_no 		= "";
		
		$data['gallery_images_cols_count'] = $this->model_options->getOption('gallery_images_cols_count');
		if(!$data['gallery_images_cols_count']) $data['gallery_images_cols_count'] = 3;
		
		$data['gallery_categories_cols_count'] = $this->model_options->getOption('gallery_categories_cols_count');
		if(!$data['gallery_categories_cols_count']) $data['gallery_categories_cols_count'] = 3;
		
		$data['parent'] = '';
		
            $categories = $this->gallery->getCategories(1);
			//var_dump($categories);die();
			// ПАГИНАЦИЯ //
			$options['pagination'] = $this->model_options->getOption('gallery_pagination');
			if(!$options['pagination']) $options['pagination'] = 5;
			$options['cols'] = $this->model_options->getOption('gallery_images_cols_count');
			if(!$options['cols']) $options['cols'] = 3;
			
		    $this->load->library('pagination');
		    $per_page = $options['pagination'];
		    $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/gallery/';

		    $config['base_url'] .= '/';
		    //echo $config['base_url'];
		    $config['total_rows'] = count($categories);
		    $config['num_links'] = 10;
		    $config['first_link'] = 'в начало';
		    $config['last_link'] = 'в конец';
		    $config['next_link'] = 'Следующая →';
		    $config['prev_link'] = '← Предыдущая';
		    
		    $config['num_tag_open'] = '<span class="pagerNum">';
		    $config['num_tag_close'] = '</span>';
		    $config['cur_tag_open'] = '<span class="pagerCurNum">';
		    $config['cur_tag_close'] = '</span>';
		    $config['prev_tag_open'] = '<span class="pagerPrev">';
		    $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
		    $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
		    $config['next_tag_close'] = '</span>';
		    $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
		    $config['last_tag_close'] = '</span>';
		    $config['first_tag_open'] = '<span class="pagerFirst">';
		    $config['first_tag_close'] = '</span>&nbsp;&nbsp;';            
		    $config['per_page'] = $per_page;
		    $from = intval($this->uri->segment(2));
					
		    $page_number=$from/$per_page+1;
		    $this->pagination->initialize($config);
		    $data['pager']	= $this->pagination->create_links();
		    //////////////////
		    //$data['images'] = $this->gallery->getImagesByCategory($data['category']['id'],1,$per_page,$from);
		    $data['categories']	= $this->gallery->getCategories(1,$per_page,$from);
	    
	    $gallery_categories_on_main = $this->model_options->getOption('gallery_categories_on_main');
	    if(!$gallery_categories_on_main) $gallery_categories_on_main = 0;
            
	    if($gallery_categories_on_main)
	    {
		// ПОКАЗЫВАЕМ СПИСОК КАТЕГОРИЙ
		//$data['categories'] = $this->gallery->getHomeCategories(1);
		
	    }
	    
	     $gallery_last_on_main = $this->model_options->getOption('gallery_last_on_main');
	    if(!$gallery_last_on_main) $gallery_last_on_main = 0;
	    
	    //if($gallery_last_on_main)
	    //{
		// ПОКАЗЫВАЕМ ПОСЛЕДНИЕ ДОБАВЛЕННЫЕ ФОТО
		/*
		$fotos = $this->gallery->getImages(1);
		
		
		$options['pagination'] = $this->model_options->getOption('gallery_pagination');
		if(!$options['pagination']) $options['pagination'] = 5;
		
		// ПАГИНАЦИЯ //
		$this->load->library('pagination');
		$per_page = $options['pagination'];
		$config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/gallery/';
		$config['total_rows'] = count($fotos);
		$config['num_links'] = 10;
		$config['first_link'] = 'в начало';
		$config['last_link'] = 'в конец';
		$config['next_link'] = 'Следующая →';
		$config['prev_link'] = '← Предыдущая';
		
		$config['num_tag_open'] = '<span class="pagerNum">';
		$config['num_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="active-gal">';
		$config['cur_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span class="pagerPrev">';
		$config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
		$config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
		$config['next_tag_close'] = '</span>';
		$config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
		$config['last_tag_close'] = '</span>';
		$config['first_tag_open'] = '<span class="pagerFirst">';
		$config['first_tag_close'] = '</span>&nbsp;&nbsp;';
		
		$config['per_page'] = $per_page;
		$config['uri_segment']     = 2;
		$from = intval($this->uri->segment(2));
		$page_number=$from/$per_page+1;
		$this->pagination->initialize($config);
		$data['pager']	= $this->pagination->create_links();
		//////////////////
		$page_no = '';
		if($page_number > 1)
		    $page_no = ' (стр. '.$page_number.')';
		$data['images'] = $this->gallery->getImages(1,$per_page,$from);
		
	    //}
	    */
	    $options['title'] = $this->model_options->getOption('gallery_main_title');
	    if(!$options['title']) $options['title'] = "Галерея";
	    $options['keywords'] = $this->model_options->getOption('gallery_main_keywords');
	    if(!$options['keywords']) $options['keywords'] = "Галерея";
	    $options['description'] = $this->model_options->getOption('gallery_main_description');
	    if(!$options['description']) $options['description'] = "Галерея";
	    $options['robots'] = $this->model_options->getOption('gallery_main_robots');
	    if(!$options['robots']) $options['robots'] = "index, follow";
	    $options['seo'] = $this->model_options->getOption('gallery_main_seo');
	    if(!$options['seo']) $options['seo'] = "";
	    $options['h1'] = $this->model_options->getOption('gallery_main_h1');
	    if(!$options['h1']) $options['h1'] = "Галерея";
	    
	    
            $data['title']          = $options['title'].$page_no.$this->model_options->getOption('global_title');
            $data['keywords']       = $options['keywords'].', '.$page_no.$this->model_options->getOption('global_keywords');
            $data['description']    = $options['description'].$page_no.$this->model_options->getOption('global_description');
            $data['robots']         = "index, follow";
            $data['h1']             = $options['h1'];

            $data['seo']            = $options['seo'];
            $data['options']        = $options;
	    $data['page_nomber']	= $page_number;
            
            
	    $this->load->view('gallery/main.tpl.php', $data);
	}
        
        public function category($category_url, $parent_url = '')
	{
	
            $options = $this->gallery->getMain();            
            
            $data['category'] = $this->gallery->getCategoryByUrl($category_url, 1);
		if($_SERVER['REQUEST_URI'] == '/gallery/')
		{
			$categories = $this->gallery->getCategories(1);
			//var_dump($categories);die();
			// ПАГИНАЦИЯ //
			$options['pagination'] = $this->model_options->getOption('gallery_pagination');
			if(!$options['pagination']) $options['pagination'] = 5;
			$options['cols'] = $this->model_options->getOption('gallery_images_cols_count');
			if(!$options['cols']) $options['cols'] = 3;
			
		    $this->load->library('pagination');
		    $per_page = $options['pagination'];
		    $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/gallery/';
		    if($data['parent'] != '')
			$config['base_url'] .= $data['parent']['url'].'/';
		    $config['base_url'] .= $data['category']['url'].'/';
		    //echo $config['base_url'];
		    $config['total_rows'] = count($categories);
		    $config['num_links'] = 10;
		    $config['first_link'] = 'в начало';
		    $config['last_link'] = 'в конец';
		    $config['next_link'] = 'Следующая →';
		    $config['prev_link'] = '← Предыдущая';
		    
		    $config['num_tag_open'] = '<span class="pagerNum">';
		    $config['num_tag_close'] = '</span>';
		    $config['cur_tag_open'] = '<span class="pagerCurNum">';
		    $config['cur_tag_close'] = '</span>';
		    $config['prev_tag_open'] = '<span class="pagerPrev">';
		    $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
		    $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
		    $config['next_tag_close'] = '</span>';
		    $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
		    $config['last_tag_close'] = '</span>';
		    $config['first_tag_open'] = '<span class="pagerFirst">';
		    $config['first_tag_close'] = '</span>&nbsp;&nbsp;';            
		    $config['per_page'] = $per_page;
		    
		    if($parent_url != '')
			$config['uri_segment']     = 4;
		    else
			$config['uri_segment']     = 3;
			
		    if($parent_url != '')
			$from = intval($this->uri->segment(4));
		    else  
			$from = intval($this->uri->segment(3));
			
		    $page_number=$from/$per_page+1;
		    $this->pagination->initialize($config);
		    $data['pager']	= $this->pagination->create_links();
		    //////////////////
		    $data['images'] = $this->gallery->getImagesByCategory($data['category']['id'],1,$per_page,$from);
		    //$data['categories']	= $this->gallery->getCategories(1,$per_page,$from);
		}
	    
            if($parent_url) $data['parent'] = $this->gallery->getCategoryByUrl($parent_url, 1);
            else $data['parent'] = '';
            
            $fotos = $this->gallery->getImagesByCategory($data['category']['id'], 1);
            
            // ПАГИНАЦИЯ //
		$options['pagination'] = $this->model_options->getOption('gallery_pagination');
		if(!$options['pagination']) $options['pagination'] = 5;
		$options['cols'] = $this->model_options->getOption('gallery_images_cols_count');
		if(!$options['cols']) $options['cols'] = 3;
		
            $this->load->library('pagination');
            $per_page = $options['pagination'];
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/gallery/';
            if($data['parent'] != '')
                $config['base_url'] .= $data['parent']['url'].'/';
            $config['base_url'] .= $data['category']['url'].'/';
            //echo $config['base_url'];
            $config['total_rows'] = count($fotos);
            $config['num_links'] = 10;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'Следующая →';
            $config['prev_link'] = '← Предыдущая';
            
            $config['num_tag_open'] = '<span class="pagerNum">';
            $config['num_tag_close'] = '</span>';
            $config['cur_tag_open'] = '<span class="pagerCurNum">';
            $config['cur_tag_close'] = '</span>';
            $config['prev_tag_open'] = '<span class="pagerPrev">';
            $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
            $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
            $config['next_tag_close'] = '</span>';
            $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
            $config['last_tag_close'] = '</span>';
            $config['first_tag_open'] = '<span class="pagerFirst">';
            $config['first_tag_close'] = '</span>&nbsp;&nbsp;';            
            $config['per_page'] = $per_page;
            
            if($parent_url != '')
                $config['uri_segment']     = 4;
            else
                $config['uri_segment']     = 3;
                
            if($parent_url != '')
                $from = intval($this->uri->segment(4));
            else  
                $from = intval($this->uri->segment(3));
                
            $page_number=$from/$per_page+1;
            $this->pagination->initialize($config);
            $data['pager']	= $this->pagination->create_links();
            //////////////////
            $data['images'] = $this->gallery->getImagesByCategory($data['category']['id'],1,$per_page,$from);
	    if(isset($categories)) $data['categories'] = $categories;
	    else $data['categories']	= $this->gallery->getSubCategories($data['category']['id'],1);
            
	    $options['title'] = $this->model_options->getOption('gallery_main_title');
	    if(!$options['title']) $options['title'] = "Галерея";
	    $options['keywords'] = $this->model_options->getOption('gallery_main_keywords');
	    if(!$options['keywords']) $options['keywords'] = "Галерея";
	    $options['description'] = $this->model_options->getOption('gallery_main_description');
	    if(!$options['description']) $options['description'] = "Галерея";
	    
            $page_no = '';
	    if($page_number > 1)
		$page_no = ' (стр. '.$page_number.')';
	    $data['title']          = $data['category']['title'].$page_no.' - '.$options['title'].$this->model_options->getOption('global_title');
            $data['keywords']       = $data['category']['keywords'].', '.$options['keywords'].', '.$page_no.$this->model_options->getOption('global_keywords');
            $data['description']    = $data['category']['description'].$page_no.'. '.$options['description'].$this->model_options->getOption('global_description');
            $data['robots']         = "index, follow";
            $data['h1']             = $data['category']['name'];
            $data['seo']            = $data['category']['seo'];
            $data['options']        = $options;
            
	    $this->load->view('gallery/category.tpl.php', $data);
	}
        
        public function image($image_id, $category_url, $parent_url = '')
        {
		//echo $category_url; echo $parent_url;
            $data['category'] = $this->gallery->getCategoryByUrl($category_url, 1);
            if($parent_url)
	    {
		$data['parent'] = $data['category'];
		$data['category'] = $this->gallery->getCategoryByUrl($parent_url, 1);
	    }
            else $data['parent'] = '';
	    
            //var_dump($data['parent']);die();
            

            $data['image']          = $this->gallery->getImageInCategory($image_id, $data['category']['id'],1);
            if(!$data['image']) err404();
            $data['comments'] = $this->comments->getCommentsToImage($image_id);
            $data['next']       = $this->gallery->getNextImage($data['image']['id'],$data['image']['num'],$data['image']['category_id'],1);
            $data['prev']       = $this->gallery->getPrevImage($data['image']['id'],$data['image']['num'],$data['image']['category_id'],1);
            //$data['spambot']	= $this->spambot->getRandomQuestion();
            
	    // КАПЧА
		$this->load->helper('captcha');
		$vals = array(
		    'img_path' => './captcha/',
		    'font_path' => './system/fonts/texb.ttf',
		    'img_url' => 'http://'.$_SERVER['SERVER_NAME'].'/captcha/'                                            
		    );
		
		$cap = create_captcha($vals);
		
		$capdata = array(
		    'captcha_time' => $cap['time'],
		    'ip_address' => $this->input->ip_address(),
		    'word' => $cap['word']
		    );
		
		$query = $this->db->insert_string('captcha', $capdata);
		$this->db->query($query);
		
		$data['cap']		= $cap;
		//
            
            $this->gallery->countPlus($image_id);
	    
	    $options = $this->gallery->getMain();
            $data['title']          = $data['image']['title'].' - '.$options['title'].$this->model_options->getOption('global_title');
            $data['keywords']       = $data['image']['keywords'].$this->model_options->getOption('global_keywords');
            $data['description']    = $data['image']['description'].$this->model_options->getOption('global_description');
            $data['robots']         = "index, follow";
            $data['h1']             = $data['image']['name'];
            $data['seo']            = $data['image']['seo'];
            $data['options']        = $options;
            
            $this->load->view('gallery/image.tpl.php', $data);            
        }
}