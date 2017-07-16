<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Options extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	       $this->load->helper('login_helper');
	       isAdminLogin();
            $this->load->model('Model_admin','ma');
        }
	
	public function index()
	{
        // SORT
        $sort_by = userdata('options_sort_by');
        
        if(isset($_GET['sort_by']))
        {
            set_userdata('options_sort_by', $_GET['sort_by']);
            redirect('admin/options');
        }

        $data['sort_by'] = $sort_by;
       if($this->session->userdata('options_module_name') !== false)
		   $data['options'] = $this->model_options->getOptionsByModule($this->session->userdata('options_module_name'));
       else
		   $data['options'] = $this->model_options->getAllOptions($sort_by);
        $data['title']  = "Опции";
        $data['modules'] = $this->model_options->getAllModules();
        
        $this->load->view('admin/options',$data);
	}
        
        public function add()
        {
            $err = false;
            if(isset($_POST['name']))
            {
                $name = trim($_POST['name']);
                $rus = trim($_POST['rus']);
                $value = $_POST['value'];
                $adding = trim($_POST['adding']);
		$module = "";
		if(isset($_POST['module'])) $module = $_POST['module'];
		$type = "";
		if(isset($_POST['type'])) $type = $_POST['type'];
		$privilege = "";
		if(isset($_POST['privilege'])) $privilege = $_POST['privilege'];
                if($name == '')
                    $err['name'] = "Название не может быть пустым!";
                if($rus == '')
                    $err['rus'] = "Описание не может быть пустым!";
		    
                //if($value == '')
                    //$err['value'] = "Значение не может быть пустым!";
                
                if(!$err)
                {
                    $dbins = array(
			   'name'      	=> $name,
			   'rus'       	=> $rus,
			   'value'     	=> $value,
			   'adding'    	=> $adding,
			   'module'	=> $module,
			   'type'   	=> $type,
			   'privilege'	=> $privilege
                    );
                    $this->db->insert('options',$dbins);
                    redirect('/admin/options/');
                }
            }
	    $data['modules'] = $this->model_options->getAllModules();
            $data['title']  = "Добавление опции";
            $data['err'] = $err;            
            $this->load->view('admin/options_add',$data);
        }
        
        public function edit($id)
        {
            $err = false;
            if(isset($_POST['name']))
            {
                $name = trim($_POST['name']);
                $rus = trim($_POST['rus']);
                $value = $_POST['value'];
                $adding = trim($_POST['adding']);
		$module = "";
		if(isset($_POST['module'])) $module = $_POST['module'];
		$type = "";
		if(isset($_POST['type'])) $type = $_POST['type'];
		$privilege = "";
		if(isset($_POST['privilege'])) $privilege = $_POST['privilege'];
                if($name == '')
                    $err['name'] = "Название не может быть пустым!";
                if($rus == '')
                    $err['rus'] = "Описание не может быть пустым!";
                //if($value == '')
                    //$err['value'] = "Значение не может быть пустым!";
                
                if(!$err)
                {
                    $dbins = array(
                        'name'      	=> $name,
                        'rus'       	=> $rus,
                        'value'     	=> $value,
                        'adding'    	=> $adding,
			'module'	=> $module,
			'type'		=> $type,
			'privilege'	=> $privilege
			
                    );
                    $this->db->where('id', $id)->update('options', $dbins);
                    redirect('/admin/options/');
                }
            }
	    $data['type'] = $this->session->userdata('type');
            $data['option'] = $this->model_options->getOptionById($id);
	    $data['modules'] = $this->model_options->getAllModules();
            $data['title']  = "Редактирование опции";
            $data['err'] = $err;            
            $this->load->view('admin/options_edit',$data);
        }

        public function del($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('options');
            redirect("/admin/options/");
        }
	
	public function set_module($module = false)
	{
	 if(isset($_POST['module']))
	 {
		  $module = $_POST['module'];
	 }
	 if($module)
	 {
		  if($module == 'all')
		  {
			   $this->session->unset_userdata('options_module_name');
		  }
		  else
		  {
			   $mod = $this->model_options->getModule($module);
			   if($mod)
			   {
				    $this->session->set_userdata('options_module_name', $module);
			   }
		  }
	 }
	 redirect("/admin/options/");
	}
}