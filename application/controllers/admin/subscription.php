<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Subscription extends CI_Controller {
	public function __construct()
        {
            parent::__construct();

	    //$this->load->model('Model_users','users');
	    
	    isLogin();
        }
	
	public function index()
	{
            if(isset($_POST['subscription']))
            {
                $subs = $this->db->get('subscription')->result_array();
                
                if($subs)
                {
                    $count = count($subs);
                    for($i = 0; $i < $count; $i++)
                    {
                        $email = $subs[$i]['email'];
                        
                        $this->load->helper('mail_helper');
                        $message = $_POST['subscription'];
                        mail_send($email,'Новостная рассылка', $message);
                    }
                    
                    $this->session->set_userdata('subscription_sended', true);
                }
                
                
            }
            
	    $data['title']  = "Рассылка";
            $data['subscriptions']  = $this->db->count_all_results('subscription');
            $this->load->view('admin/subscription',$data);
	}
}