<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Images extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
			$this->load->helper('admin_helper');
            //$this->load->model('Model_admin','ma');	    
            $this->load->model('Model_options','options');
        }
	
	function upload_foto($width, $height){								// Функция загрузки и обработки фото
		  $config['upload_path'] 	= 'upload/images';
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
			   
			   $config['image_library'] 	= 'GD2';
			   $config['create_thumb'] 	= TRUE;
			   $config['maintain_ratio'] 	= TRUE;			   
			   $config['width'] 			= $width;
			   $config['height'] 			= $height;
			   $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
			   $config['new_image']		= $ret["file_path"].$ret['file_name'];
			   $config['thumb_marker']	= '';
			   $this->image_lib->initialize($config);
			   $this->image_lib->resize();
			   
			   if($this->model_options->getOption('articles_watermark') == 1)
			   {			   
				    $config['source_image'] = $ret["file_path"].$ret['file_name'];
				    $config['create_thumb'] = FALSE;
				    $config['wm_type'] = 'overlay';
				    $config['wm_overlay_path'] = $this->model_options->getOption('watermark_file');
				    if($config['wm_overlay_path'])
				    {
					     $config['wm_hor_alignment'] = 'right';
					     $this->image_lib->initialize($config);
					     $this->image_lib->watermark();
				    }
			   }
			   if($ret['image_width'] < $width) $width = $ret['image_width'];
			   if($ret['image_height'] < $height) $height = $ret['image_height'];
			   
			   
		  
		  	   $ret = $this->upload->data();
			   
			  return $ret;
		  }
	}
        
        public function index()
	{
		  $image = '';
		  if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки 
			   if ($_FILES['userfile']['name'] != '') {
				  $imagearr = $this->upload_foto($_POST['width'], $_POST['height']);				  
				  $image = '/upload/images/'.$imagearr['file_name'];
			   }
		  }
		  
		  $upload_image_max_width = $this->options->getOption('upload_image_max_width');
		  if(!$upload_image_max_width) $upload_image_max_width = 0;
		  $upload_image_max_height = $this->options->getOption('upload_image_max_height');
		  if(!$upload_image_max_height) $upload_image_max_height = 0;
		  
		  $data['upload_image_max_width'] = $upload_image_max_width;
		  $data['upload_image_max_height'] = $upload_image_max_height;
		  
		  $data['title']  = "Загрузка фото";
		  $data['image'] = $image;
		  //$data['categories'] = $this->mcats->getCategories();
		  $this->load->view('admin/images', $data);
	}
}