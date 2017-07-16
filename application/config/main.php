<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
            $this->load->model('Model_articles','art');
	    $this->load->model('Model_categories','cat');
	    $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
        }
	
	public function index()
	{
            $this->load->model('Model_main','main');
            $this->load->helper('menu_helper');
            $tkdzst = $this->main->getMain();
            $data['title']          = $tkdzst['title'];
            $data['keywords']       = $tkdzst['keywords'];
            $data['description']    = $tkdzst['description'];
            $data['robots']         = "index, follow";
            $data['h1']             = $tkdzst['h1'];
            $data['seo']            = $tkdzst['seo'];
	    $data['glavnoe']	    = $this->art->getGlavnoe();	    
	    $this->load->view('main', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */