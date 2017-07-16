<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_menus','mp');
        }
           
	public function index()
	{
            $data['title']  = "Меню";
            $data['menus'] = $this->mp->getMenusWithParentId(0);
            $this->load->view('admin/menus',$data);
	}
        
        public function add()
        {
            $err = '';
	    
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
		  $num = $this->mp->getNewSectionNum($_POST['parent_id']);
                    $url = $_POST['url'];
                    if($_POST['url'] == '')
                    {
                        $this->load->helper('translit_helper');
                        $url = translitRuToEn($_POST['name']);                        
                    }
                    
                    $active = 0;
                    if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
                    $dbins = array(
                                   'name'           => $_POST['name'],
                                   'url'            => $url,
                                   'num'            => $num,
				   'parent_id'      => $_POST['parent_id'],                                   
                                   'active'         => $active,
                                   'type'           => $_POST['type'],
				   'subtype'		=> $_POST['subtype'],
				   'params'		=> $_POST['params']
                                   );
                    $this->db->insert('menus',$dbins);
                    redirect("/admin/menus/");

            }
            $data['title']  = "Добавление пункта меню";
            $data['err'] = $err;
            $data['num'] = $this->mp->getNewNum();
            $data['menus'] = $this->mp->getMenusWithParentId(0);
            $this->load->view('admin/menus_add',$data);
        }
        
        public function edit($id)
        {
            $err = '';
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
                $url = $_POST['url'];
                if($_POST['url'] == '')
                {
                    $this->load->helper('translit_helper');
                    $url = translitRuToEn($_POST['name']);                    
                }
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
                $dbins = array(
                                   'name'           => $_POST['name'],
                                   'url'            => $url,
                                   'num'            => $_POST['num'],
				   'parent_id'      => $_POST['parent_id'],                                   
                                   'active'         => $active,
                                   'type'           => $_POST['type'],
				   'subtype'		=> $_POST['subtype'],
				   'params'		=> $_POST['params']
                               );
                $this->db->where('id',$id);
                $this->db->limit(1);
                $this->db->update('menus',$dbins);
                redirect("/admin/menus/");
            }
            $data['menu'] = $this->mp->getMenuById($id);
            $data['title']  = "Ред. п. меню";
            $data['err'] = $err;
            $data['num'] = $this->mp->getNewNum();
            $data['menus'] = $this->mp->getMenus();
            $this->load->view('admin/menus_edit',$data);
        }
        
        public function up($id)
        {
            $cat = $this->mp->getMenuById($id);
            if(($cat) && $cat['num'] > 0)
            {
                $num = $cat['num']-1;
                $oldcat = $this->mp->getMenuByNum($num);
                $dbins = array('num' => $num);
                $this->db->where('id',$id)->limit(1)->update('menus',$dbins);
                if($oldcat)
                {
                    $dbins = array('num' => ($num+1));
                    $this->db->where('id',$oldcat['id'])->limit(1)->update('menus',$dbins);
                }
            }
            redirect('/admin/menus/');
        }
        public function down($id)
        {
            $cat = $this->mp->getMenuById($id);
            if(($cat) && $cat['num'] < ($this->mp->getNewNum()-1))
            {
                $num = $cat['num']+1;
                $oldcat = $this->mp->getMenuByNum($num);
                $dbins = array('num' => $num);
                $this->db->where('id',$id)->limit(1)->update('menus',$dbins);
                if($oldcat)
                {
                    $dbins = array('num' => ($num-1));
                    $this->db->where('id',$oldcat['id'])->limit(1)->update('menus',$dbins);
                }
            }
            redirect('/admin/menus/');
        }
        
        public function del($id)
        {
		  $this->db->where('id',$id)->limit(1)->delete('menus');
		  redirect("/admin/menus/");
        }
	
	public function del_all_post()
	{
	  echo $_POST['type'];
	  	if(isset($_POST['type']) && $_POST['type'] != '')
		{
			   $this->db->where('type',$_POST['type'])->delete('menus');
			   
		}
		redirect("/admin/menus/");
	}
	
	public function active($id)
	{
		  $this->ma->setActive($id,'menus');
		  redirect('/admin/menus/');
	}
}