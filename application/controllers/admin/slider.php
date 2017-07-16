<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Slider extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_slider','slider');
        }
	
	function upload_foto(){								// Функция загрузки и обработки фото
		  $config['upload_path'] 	= 'upload/fotos';
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
            $data['title']  = "Статические страницы";
            $data['slider'] = $this->slider->getslider();
            $this->load->view('admin/slider',$data);
	}
        
        public function add()
        {
            $err = '';
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
                if(!$this->slider->getslide($_POST['name']))
                {
                    
                        $this->load->helper('translit_helper');
                        $url = translitRuToEn($_POST['name']);                        

                    $active = 0;
                    if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
		    
		    $social_buttons = 0;
                    if(isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) $social_buttons = 1;
		    
		    $image = '';
		    if(isset($_POST['image'])) $image = $_POST['image'];
		    if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки 
			   if ($_FILES['userfile']['name'] != '') {
				  $imagearr = $this->upload_foto();
				  $image = '/upload/fotos/'.$imagearr['file_name'];
			   }
		    }
		    $tesliderlate = '';
		    if(isset($_POST['tesliderlate'])) $tesliderlate = $_POST['tesliderlate'];
		    
		    $_POST['title'] = $_POST['name'];
		    $_POST['keywords'] = $_POST['name'];
		    $_POST['description'] = $_POST['name'];
		    
                    $dbins = array(
                                   'name'           => $_POST['name'],
                                   'url'            => $url,
                                   'num'            => $_POST['num'],
				   'content'        => $_POST['content'],                                   
                                   'active'         => $active,
                                   'title'          => $_POST['title'],
                                   'keywords'       => $_POST['keywords'],
                                   'description'    => $_POST['description'],
                                   'seo'            => '',
				   'tesliderlate'		=> $tesliderlate,
				   'image'	    => $image,
				   'social_buttons' => $social_buttons
                                   );
                    $this->db->insert('slider',$dbins);
                    redirect("/admin/slider/");
                }
                else $err = 'Такая страница уже существует!';
            }
            $data['title']  = "Добавление страницы";
            $data['err'] = $err;
            $data['num'] = $this->slider->getNewNum();
            $data['slider'] = $this->slider->getslider();
            $this->load->view('admin/slider_add',$data);
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
		
		$social_buttons = 0;
                  if(isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) $social_buttons = 1;
		  
		  $tesliderlate = '';
		  if(isset($_POST['tesliderlate'])) $tesliderlate = $_POST['tesliderlate'];
		  	  
		
		$image = '';
		  if(isset($_POST['image'])) $image = $_POST['image'];
		  if(isset($_POST['image_del']) && $_POST['image_del'] == true)
		  {
			 unlink($_SERVER['DOCUMENT_ROOT'].$image);
			 $image = '';
			 
		  }
		  if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
			   if ($_FILES['userfile']['name'] != '') {				    
				  $imagearr = $this->upload_foto();
				  if($image != '') unlink($_SERVER['DOCUMENT_ROOT'].$image);
				  $image = '/upload/fotos/'.$imagearr['file_name'];
			   }
		    }
		    
		    if($_POST['title'] == '') $_POST['title'] = $_POST['name'];
		    if($_POST['keywords'] == '') $_POST['keywords'] = $_POST['keywords'];
		    if($_POST['description'] == '') $_POST['description'] = $_POST['description'];
		  
                $dbins = array(
                                   'name'           => $_POST['name'],
                                   'url'            => $url,
                                   'num'            => $_POST['num'],
				   'content'        => $_POST['content'],                                   
                                   'active'         => $active,
                                   'title'          => $_POST['title'],
                                   'keywords'       => $_POST['keywords'],
                                   'description'    => $_POST['description'],
                                   'seo'            => $_POST['seo'],
				   'tesliderlate'		=> $tesliderlate,
				   'image'	    => $image,
				   'social_buttons' => $social_buttons
                               );
                $this->db->where('id',$id);
                $this->db->limit(1);
                $this->db->update('slider',$dbins);
		  if(isset($_POST['save_and_stay']))
			   redirect($_SERVER['REQUEST_URI']);
		  else
			   redirect("/admin/slider/");
            }
            $data['slide'] = $this->slider->getslideById($id);
            $data['title']  = "Редактирование страницы";
            $data['err'] = $err;
            $data['num'] = $this->slider->getNewNum();
            $data['slider'] = $this->slider->getslider();
            $this->load->view('admin/slider_edit',$data);
        }
        
        public function up($id)
        {
            $cat = $this->slider->getslideById($id);
            if(($cat) && $cat['num'] > 0)
            {
                $num = $cat['num']-1;
                $oldcat = $this->slider->getslideByNum($num);
                $dbins = array('num' => $num);
                $this->db->where('id',$id)->limit(1)->update('slider',$dbins);
                if($oldcat)
                {
                    $dbins = array('num' => ($num+1));
                    $this->db->where('id',$oldcat['id'])->limit(1)->update('slider',$dbins);
                }
            }
            redirect('/admin/slider/');
        }
        public function down($id)
        {
            $cat = $this->slider->getslideById($id);
            if(($cat) && $cat['num'] < ($this->slider->getNewNum()-1))
            {
                $num = $cat['num']+1;
                $oldcat = $this->slider->getslideByNum($num);
                $dbins = array('num' => $num);
                $this->db->where('id',$id)->limit(1)->update('slider',$dbins);
                if($oldcat)
                {
                    $dbins = array('num' => ($num-1));
                    $this->db->where('id',$oldcat['id'])->limit(1)->update('slider',$dbins);
                }
            }
            redirect('/admin/slider/');
        }
        
        public function del($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('slider');
            redirect("/admin/slider/");
        }
	
	public function active($id)
	{
		  $this->ma->setActive($id,'slider');
		  redirect('/admin/slider/');
	}
}