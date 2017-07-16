<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rss extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
	    //$this->load->helper('login_helper');
            $this->load->model('Model_articles','articles');
	    $this->load->model('Model_categories','categories');
	    $this->load->model('Model_afisha','afisha');
	    $this->load->model('Model_afisha','afisha');
	    //$this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
        }
        
        public function cut_str($str, $pos) {
            $str = preg_replace('/&#?[a-z0-9]{2,8};/i', '', strip_tags(htmlspecialchars_decode($str)));
            $str = preg_replace('/&/i', '', $str);
            if (!empty($str)){
                    $temppos = 0;
                    
                    for ($i = 0; $i < $pos; $i++){
                            
                            $temppos2 = strpos($str, '.', $temppos);
                            if ($temppos <= $temppos2){
                                    $temppos = strpos($str, '.', $temppos) + 1;
                            }
                            else/*if (strlen($str) < $temppos)*/
                                    return $str;
                    
                    }	
                    return substr($str, 0, $temppos);
                    
            }
            else 
                    return '';
        }
	
	public function afisha()
	{
		$this->db->where('date_unix >',time()); // показать только будущие
		$this->db->order_by('date_unix',"ASC");
		$schedule = $this->db->get('schedule')->result_array();

		
		$rss_channal_title = $this->model_options->getOption('rss_afisha_title');
		if(!$rss_channal_title) $rss_channal_title = "Афиша";
		$rss_channal_description = $this->model_options->getOption('rss_afisha_description');
		if(!$rss_channal_description) $rss_channal_description = "Скоро в театре";
		
		$data['rss_channal_title']	= $rss_channal_title;
		$data['rss_channal_description'] = $rss_channal_description;
		$data['schedule'] 		= $schedule;
		$this->load->view('rss/afisha.php', $data);
	}
	
	public function index()
	{
		$rss_news_count = $this->model_options->getOption('rss_news_count');
		if(!$rss_news_count) $rss_news_count = 10;
		
            $articles = $this->articles->getLastArticles($rss_news_count);
            
	    $rss_channal_title = $this->model_options->getOption('rss_channal_title');
	    if(!$rss_channal_title) $rss_channal_title = "Íîâîñòè";
	    $rss_channal_description = $this->model_options->getOption('rss_channal_description');
	    if(!$rss_channal_description) $rss_channal_description = "Ëåíòà íîâîñòåé";
	    
	    $data['rss_channal_title']	= $rss_channal_title;
	    $data['rss_channal_description'] = $rss_channal_description;
            $data['articles'] 		= $articles;
	    $this->load->view('rss/rss.php', $data);
	}
	
	public function ukrnetrss()
	{
		$rss_news_count = $this->model_options->getOption('rss_news_count');
		if(!$rss_news_count) $rss_news_count = 10;
		
            $articles = $this->articles->getLastArticlesAuthor($rss_news_count);
            
	    $rss_channal_title = $this->model_options->getOption('rss_channal_title');
	    if(!$rss_channal_title) $rss_channal_title = "Íîâîñòè";
	    $rss_channal_description = $this->model_options->getOption('rss_channal_description');
	    if(!$rss_channal_description) $rss_channal_description = "Ëåíòà íîâîñòåé";
	    
	    $data['rss_channal_title']	= $rss_channal_title;
	    $data['rss_channal_description'] = $rss_channal_description;
            $data['articles'] 		= $articles;
	    $this->load->view('rss/ukrnetrss.php', $data);
	}
        
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */