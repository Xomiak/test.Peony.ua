<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Afisha extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    $this->load->model('Model_afisha','afisha');
            $this->load->model('Model_categories','categories');
	    $this->load->model('Model_schedule','schedule');
	    $this->load->helper('date_helper');
	    $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
	    isLogin();
        }
	
	public function index()
	{            
            $this->load->helper('menu_helper');
            $category = $this->categories->getCategoryByUrl('afisha');
            $data['title']          = $category['title'];
            $data['keywords']       = $category['keywords'];
            $data['description']    = $category['description'];
            $data['robots']         = "index, follow";
            $data['h1']             = $category['name'];
            $data['seo']            = $category['seo'];
	    /*
            $data['schedule1']       = $this->schedule->getByMonth(date("Y-m"),"Основная сцена", 1);
            $data['schedule2']       = $this->schedule->getByMonth(date("Y-m"),"Малая «Сцена 38»", 1);
            $data['schedule3']       = $this->schedule->getByMonth(date("Y-m"),"«Зритель на сцене»", 1);
            */
            $data['month']  = date("m");
            $data['year']   = date("Y");
            
            $data['next_month'] = true;
	    $data['days_in_month']	= cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
	    
	    	    
            /*
            $count = 0;
            $count1 = count($data['schedule1']);
            $count2 = count($data['schedule2']);
            if($count2 > $count1) $count = $count2;
            else $count = $count1;
            
            $count3 = count($data['schedule3']);
            if($count3 > $count) $count = $count3;
            
            $data['rows_count'] = $count;
            */
	    $this->load->view('templates/'.$category['template'], $data);

	}
        
        public function next()
	{
            $year   = date("Y");
            $month  = date("m");
            
            $month++;
            if($month == 13)
            {
                $month = 1;
                $year++;
            }
            if(strlen($month) == 1) $month = '0'.$month;
            
            $this->load->helper('menu_helper');
            $category = $this->categories->getCategoryByUrl('afisha');
            $data['title']          = $category['title'].' - '.getMonthName2($month);
            $data['keywords']       = $category['keywords'].', '.getMonthName2($month);
            $data['description']    = $category['description'].' - '.getMonthName2($month);
            $data['robots']         = "index, follow";
            $data['h1']             = $category['name'];
            $data['seo']            = $category['seo'];
            $data['schedule1']       = $this->schedule->getByMonth($year.'-'.$month,"Основная сцена", 1);
            $data['schedule2']       = $this->schedule->getByMonth($year.'-'.$month,"Малая «Сцена 38»", 1);
            $data['schedule3']       = $this->schedule->getByMonth($year.'-'.$month,"«Зритель на сцене»", 1);
            $data['month']  = $month;
            $data['year']   = $year;
            
            $count = 0;
            $count1 = count($data['schedule1']);
            $count2 = count($data['schedule2']);
            if($count2 > $count1) $count = $count2;
            else $count = $count1;
            
            $count3 = count($data['schedule3']);
            if($count3 > $count) $count = $count3;
            
            $data['rows_count'] = $count;
            
            $data['next_month'] = false;
	    
		$data['days_in_month']	= cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            
	    $this->load->view('templates/'.$category['template'], $data);

	}
	
	
	public function repertuar()
	{            
            $this->load->helper('menu_helper');
            $category = $this->categories->getCategoryByUrl('repertuar');
            $data['title']          = $category['title'];
            $data['keywords']       = $category['keywords'];
            $data['description']    = $category['description'];
            $data['robots']         = "index, follow";
            $data['h1']             = $category['name'];
            $data['seo']            = $category['seo'];
	    
	    
	    
            $data['schedule1']       = $this->afisha->getAllByScene("Основная сцена", 1, 0);
            $data['schedule2']       = $this->afisha->getAllByScene("Малая «Сцена 38»", 1, 0);
            $data['schedule3']       = $this->afisha->getAllByScene("«Зритель на сцене»", 1, 0);
	    
	    $count = 0;
            $count1 = count($data['schedule1']);
            $count2 = count($data['schedule2']);
            if($count2 > $count1) $count = $count2;
            else $count = $count1;
            
            $count3 = count($data['schedule3']);
            if($count3 > $count) $count = $count3;
            
            $data['rows_count'] = $count;
	    
	    $data['child']		= $this->afisha->getAllByScene("Основная сцена", 1, 1);            
            $data['child_rows_count'] = count($data['child']);
	    
	    
            
	    $this->load->view('templates/'.$category['template'], $data);

	}
}