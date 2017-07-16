<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Filter extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_filter','filter');
        }
		
	public function index()
	{
            if(isset($_POST['razmer']))
            {
                $dbins = array(
                    'name'  => $_POST['razmer']
                );
                $this->db->insert('razmer', $dbins);
                redirect($_SERVER['REQUEST_URI']);
            }
            if(isset($_POST['color']))
            {
                $dbins = array(
                    'name'  => $_POST['color']
                );
                $this->db->insert('color', $dbins);
                redirect($_SERVER['REQUEST_URI']);
            }
            if(isset($_POST['sostav']))
            {
                $dbins = array(
                    'name'  => $_POST['sostav']
                );
                $this->db->insert('sostav', $dbins);
                redirect($_SERVER['REQUEST_URI']);
            }
            
            $data['title']  = "Фильтры";
	    $this->db->order_by('name', 'ASC');
            $data['razmer'] = $this->db->get('razmer')->result_array();
	    $this->db->order_by('name', 'ASC');
            $data['color'] = $this->db->get('color')->result_array();
	    $this->db->order_by('name', 'ASC');
            $data['sostav'] = $this->db->get('sostav')->result_array();
            $this->load->view('admin/filter',$data);
	}
        
        
        
        public function del($id)
        {
            if(isset($_GET['table']))
            {
                $this->db->where('id',$id)->limit(1)->delete($_GET['table']);
            }
            redirect("/admin/filter/");
        }
        
	

}