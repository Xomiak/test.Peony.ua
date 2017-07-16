<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forum extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->model('Model_users','users');
        $this->load->model('Model_forum','forum');
        if($this->forum->getOption('forum_active') != 1) err404();
        $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
        isLogin();
    }
    
    public function index()
    {
        if($this->session->userdata('login') !== false)
            $data['user']       = $this->users->getUserByLogin($this->session->userdata('login'));
        else $data['user']      = false;
        $data['title']          = $this->forum->getOption('forum_main_title').$this->model_options->getOption('global_title');;
        $data['keywords']       = $this->forum->getOption('forum_main_keywords').$this->model_options->getOption('global_keywords');
        $data['description']    = $this->forum->getOption('forum_main_description').$this->model_options->getOption('global_description');
        $data['robots']         = $this->forum->getOption('forum_main_robots');
        $data['h1']             = $this->forum->getOption('forum_main_h1');
        $data['forum_name']     = $this->forum->getOption('forum_name');
        $data['seo']            = $this->forum->getOption('forum_main_seo');
        $data['slogan']         = $this->forum->getOption('forum_slogan');
        $data['sections']       = $this->forum->getParentSections(0, 1);
        $this->load->view('forum/main.tpl.php', $data);
    }
}