<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banners extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
			$this->load->helper('admin_helper');
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_banners','mban');
        }
	
	function upload_banner(){
	  
		  //////
		  // Функция загрузки и обработки фото
		  $config['upload_path'] 	= 'upload/slider';
		  $config['allowed_types'] 	= 'jpg|png|gif|jpe';
		  $config['max_size']		= '0';
		  $config['max_width']  	= '0';
		  $config['max_height']  	= '0';
		  $config['encrypt_name']	= true;
		  $config['overwrite']  	= false;
  
		  $this->load->library('upload', $config);
		    
		  if ( ! $this->upload->do_upload())
		  {
			  echo $this->upload->display_errors();
			  die();
		  }
		  else
		  {
			   $ret = $this->upload->data();			  		    
			   return $ret;
		  }
	 }
           
	public function index()
	{
            $data['title']  = "Баннеры";

	    $data['banners'] = $this->mban->getBanners();
            $this->load->view('admin/banners',$data);
	}
	
        public function add()
        {
            $err = '';
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
		
		$image = '';
		    if(isset($_POST['image'])) $image = $_POST['image'];
		    if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки 
			   if ($_FILES['userfile']['name'] != '') {
				  $imagearr = $this->upload_banner();
				  $image = '/upload/slider/'.$imagearr['file_name'];
			   }
		    }
		    
		    if($image)
			   $_POST['content'] = '<img src="http://'.$_SERVER['SERVER_NAME'].$image.'" alt="'.$_POST['name'].'" title="'.$_POST['name'].'" />';

                $dbins = array(
                               'name'           => $_POST['name'],
                               'content'        => $_POST['content'],
			       'url'		=> $_POST['url'],
			       'position'	=> $_POST['position'],
			       'count'		=> $_POST['count'],
			       'image'		=> $image,
                               'active'         => $active,
					'num'		=> $_POST['num']
                               );
                $this->db->insert('banners',$dbins);
                redirect("/admin/banners/");
            }
            
            $data['title']  = "Добавление баннера";
                        
            $this->load->view('admin/banners_add',$data);
        }
        
        public function edit($id)
        {
            $err = '';
	    $banner = $this->mban->getBannerById($id);
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
		
		$is_image_set = false;
		$image = $banner['image'];
		    if(isset($_POST['image'])) $image = $_POST['image'];
		    //var_dump("1");die();
		    if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки
			   //var_dump("YES!!");die();
			   if ($_FILES['userfile']['name'] != '') {
				  $imagearr = $this->upload_banner();				  
				  if($imagearr['file_name'] !== false && $imagearr['file_name'] != '')
				  {
				    @unlink($_SERVER['DOCUMENT_ROOT'].$image);
				    $image = '/upload/slider/'.$imagearr['file_name'];
				    $is_image_set = true;
				  }
			   }
		    }
		    
		    if($is_image_set)
			   $banner['content'] = '<img src="http://'.$_SERVER['SERVER_NAME'].$image.'" alt="'.$_POST['name'].'" title="'.$_POST['name'].'" />';
			   
                $dbins = array(
                                'name'           => $_POST['name'],
                                'content'        => $banner['content'],
								'url'	         => $_POST['url'],
								'position'	 => $_POST['position'],
			        			'count'		 => $_POST['count'],
			        			'format'	=> $_POST['format'],
								'image'		=> $image,
                                'active'         => $active,
								'num'		=> $_POST['num']
                               );
                $this->db->where('id',$id);
                $this->db->limit(1);
                $this->db->update('banners',$dbins);
                redirect("/admin/banners/");
            }
            $data['banner'] = $banner;
            $data['title']  = "Редактирование баннера";
            
            $this->load->view('admin/banners_edit',$data);
        }
        
        public function del($id)
        {
		  $banner = $this->mban->getBannerById($id);
		  if($banner['image'] != '')
			   @unlink($_SERVER['DOCUMENT_ROOT'].$banner['image']);

		  $this->db->where('id',$id)->limit(1)->delete('banners');
		  redirect("/admin/banners/");
        }
	
	public function active($id)
	{
		  $this->ma->setActive($id,'banners');
		  redirect('/admin/banners/');
	}
}