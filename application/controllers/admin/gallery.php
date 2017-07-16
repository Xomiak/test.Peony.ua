<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_pages','mp');
	    $this->load->model('Model_gallery','gallery');
        }
	
	           
	public function index()
	{
		  $data['title']  = "Галерея";
		  //var_dump($this->session->userdata('category_id'));die();
		  if($this->session->userdata('gallery_category_id') != null)
		  {
			$a = $this->gallery->getImagesByCategory($this->session->userdata('gallery_category_id'));			
		  }
		  else
		  {
			$a = $this->gallery->getImages();
		  }
			
		  
		  // ПАГИНАЦИЯ //
		      $this->load->library('pagination');
		      $per_page = 35;
		      $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/gallery/';
		      $config['total_rows'] = count($a);
		      $config['num_links'] = 4;
		      $config['first_link'] = 'в начало';
		      $config['last_link'] = 'в конец';
		      $config['next_link'] = 'далее';
		      $config['prev_link'] = 'назад';
		      
		      $config['per_page'] = $per_page;
		      $config['uri_segment']     = 3;
		      $from = intval($this->uri->segment(3));
		      $page_number=$from/$per_page+1;
		      $this->pagination->initialize($config);
		      $data['pager']	= $this->pagination->create_links();
		      //////////
		      
		      if($this->session->userdata('gallery_category_id') != null)
		      {
			   //var_dump($this->session->userdata('category_id'));
			      $data['images'] = $this->gallery->getImagesByCategory($this->session->userdata('gallery_category_id'),-1,$per_page,$from);
		      }
		      else
		      {
			      $data['images'] = $this->gallery->getImages(-1,$per_page,$from);
		      }
		      
		  // 	var_dump($data['images']);
		  //$data['category_id'] = $this->session->userdata('gallery_category_id');
		  $data['categories'] = $this->gallery->getCategories();
		  $this->load->view('admin/gallery',$data);
	}
	
	 public function set_category()
	 {
		   if(isset($_POST['category_id']) && $_POST['category_id'] == 'all') $this->session->unset_userdata('gallery_category_id');
		   else if(isset($_POST['category_id'])) $this->session->set_userdata('gallery_category_id',$_POST['category_id']);
		   //var_dump($this->session->userdata('gallery_category_id'));die();
		   redirect("/admin/gallery/");
	 }
	        
        public function categories()
	{
            $data['title']  = "Разделы галереи";
            $data['categories'] = $this->gallery->getHomeCategories();
            $this->load->view('admin/gallery_categories',$data);
	}
	
	function upload_zip()
	{
		  $config['upload_path'] 	= 'upload/temp';
		  $config['allowed_types'] 	= 'zip'; 
		  $config['overwrite']  	= true;
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
	
	function upload_category_foto($folder = false){ // Функция загрузки и обработки фото
	 
		  $config['upload_path'] 	= 'upload/gallery/categories';
		  if($folder)			$config['upload_path'] .= "/".$folder;
		  $config['allowed_types'] 	= 'jpg|png|gif|jpe';
		  $config['max_size']		= '0';
		  $config['max_width']  	= '0';
		  $config['max_height']  	= '0';
		  $config['encrypt_name']	= true;
		  $config['overwrite']  	= false;
		  //$config['remove_spaces']	= true;
  
		  $this->load->library('upload', $config);
		    
		  if ( ! $this->upload->do_upload())
		  {
			  echo $this->upload->display_errors();
			  die();
		  }
		  else
		  {
			   $ret = $this->upload->data();
			   
			   $gallery_mini_foto_width = $this->model_options->getOption("gallery_mini_foto_width");
			   if(!$gallery_mini_foto_width) $gallery_mini_foto_width = 200;
			   
			   $gallery_mini_foto_height = $this->model_options->getOption("gallery_mini_foto_height");
			   if(!$gallery_mini_foto_height) $gallery_mini_foto_height = 200;
			   
			   $config['image_library'] 	= 'GD2';
			   $config['create_thumb'] 	= FALSE;
			   $config['maintain_ratio'] 	= TRUE;
			   $config['width'] 			= $gallery_mini_foto_width;
			   $config['height'] 			= $gallery_mini_foto_height;
			   $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
			   $config['new_image']		= $ret["file_path"].$ret['file_name'];
			   $config['thumb_marker']	= '';
			   $this->image_lib->initialize($config);
			   $this->image_lib->resize();

			   
			   
			   
			  return $ret;
		  }
	 }
	 
	 function upload_foto($article_name = '', $folder = ''){// Функция загрузки и обработки фото
		  
	 // Выбираем тип названия файла
	 $gallery_file_names = $this->model_options->getOption('gallery_file_names');
	 if(!$gallery_file_names) $gallery_file_names = 'encrypt';
	 
	 $encrypt_name = false;
	 if($gallery_file_names == 'encrypt') $encrypt_name = true;

		  if($folder)
			   $folder = '/categories/'.$folder;
		  
		  $config['upload_path'] 	= 'upload/gallery'.$folder;
		  $config['allowed_types'] 	= 'jpg|png|gif|jpe';
		  $config['max_size']		= '0';
		  $config['max_width']  	= '0';
		  $config['max_height']  	= '0';
		  $config['encrypt_name']	= $encrypt_name;
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
			   
			   // Настраиваем название файла
			   $filename = $ret['file_name'];
			   
			   if($gallery_file_names == 'translit')
			   {
				    $this->load->helper('translit_helper');
				    $fnametrans = translitRuToEn($article_name);
				    $filename = $fnametrans.$ret['file_ext'];
				    
				    $count = 1;
				    while(file_exists($ret['file_path'].$filename))
				    {
					     $fnametrans .= $count;
					     $count++;
					     $filename = $fnametrans.$ret['file_ext'];
				    }
			   }
			   
			   $gallery_max_foto_width = $this->model_options->getOption('gallery_max_foto_width');
			   if(!$gallery_max_foto_width) $gallery_max_foto_width = 600;
			   $gallery_max_foto_height = $this->model_options->getOption('gallery_max_foto_height');
			   if(!$gallery_max_foto_height) $gallery_max_foto_height = 600;
			   
			   if($ret['image_width'] < $gallery_max_foto_width) $gallery_max_foto_width = $ret['image_width'];
			   if($ret['image_height'] < $gallery_max_foto_height) $gallery_max_foto_height = $ret['image_height'];
			   
			   $gallery_mini_foto_width = $this->model_options->getOption('gallery_mini_foto_width');
			   if(!$gallery_mini_foto_width) $gallery_mini_foto_width = 600;
			   $gallery_mini_foto_height = $this->model_options->getOption('gallery_mini_foto_height');
			   if(!$gallery_mini_foto_height) $gallery_mini_foto_height = 600;
			   
			   if($ret['image_width'] < $gallery_mini_foto_width) $gallery_mini_foto_width = $ret['image_width'];
			   if($ret['image_height'] < $gallery_mini_foto_height) $gallery_mini_foto_height = $ret['image_height'];
			   
			   $gallery_normal_foto_width = $this->model_options->getOption('gallery_normal_foto_width');
			   if(!$gallery_normal_foto_width) $gallery_normal_foto_width = 600;
			   $gallery_normal_foto_height = $this->model_options->getOption('gallery_normal_foto_height');
			   if(!$gallery_normal_foto_height) $gallery_normal_foto_height = 600;
			   
			   if($ret['image_width'] < $gallery_normal_foto_width) $gallery_normal_foto_width = $ret['image_width'];
			   if($ret['image_height'] < $gallery_normal_foto_height) $gallery_normal_foto_height = $ret['image_height'];
			   
			   
			   if(
			      ($ret['image_width'] > $gallery_max_foto_width && $ret['image_height'] < $gallery_max_foto_height) ||
			      ($ret['image_width'] < $gallery_max_foto_width && $ret['image_height'] > $gallery_max_foto_height) ||
			      ($ret['image_width'] > $gallery_max_foto_width && $ret['image_height'] > $gallery_max_foto_height)
			      )
			   {
				    $config['image_library'] 	= 'GD2';
				    $config['create_thumb'] 	= FALSE;
				    $config['maintain_ratio'] 	= TRUE;
				    $config['width'] 			= $gallery_max_foto_width;
				    $config['height'] 			= $gallery_max_foto_height;
				    $config['source_image'] 		= $ret["file_path"].$ret['file_name'];
				    $config['new_image']		= $ret["file_path"].$ret['file_name'];
				    $config['thumb_marker']	= '';
				    $this->image_lib->initialize($config);
				    $this->image_lib->resize();
				    
			   }
			   
			   if($filename != $ret['file_name'])
			   {
				    $config['image_library'] 	= 'GD2';
				    $config['create_thumb'] 	= FALSE;
				    $config['maintain_ratio'] 	= TRUE;				    
				    $config['source_image'] 		= $ret["file_path"].$ret['file_name'];
				    $config['new_image']		= $filename;
				    $config['thumb_marker']	= '';
				    $this->image_lib->initialize($config);
				    $this->image_lib->resize();
				    
				    unlink($ret["file_path"].$ret['file_name']);
				    $ret['file_name'] = $filename;
			   }
			   
			   // ВОДЯНОЙ ЗНАК
			   $gallery_watermark = $this->model_options->getOption('gallery_watermark');
			   
			   if($gallery_watermark)
			   {				    
				    $config['source_image'] = $ret["file_path"].$ret['file_name'];
				    $config['create_thumb'] = FALSE;
				    $config['wm_type'] = 'overlay';
				    $config['wm_opacity']	= 20;
				    $config['wm_overlay_path'] = 'img/logo.png';
				    $config['wm_hor_alignment'] = 'right';
				    $this->image_lib->initialize($config);
				    $this->image_lib->watermark();
			   }
			   //////////////

			   
			   $config['image_library'] 	= 'GD2';
			   $config['create_thumb'] 	= FALSE;
			   $config['maintain_ratio'] 	= TRUE;
			   $config['width'] 			= $gallery_mini_foto_width;
			   $config['height'] 			= $gallery_mini_foto_height;
			   $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
			   $config['new_image']		= $ret["file_path"].'/mini/'.$filename;
			   $config['thumb_marker']	= '';
			   $this->image_lib->initialize($config);
			   $this->image_lib->resize();
			   
			   $config['image_library'] 	= 'GD2';
			   $config['create_thumb'] 	= FALSE;
			   $config['maintain_ratio'] 	= TRUE;
			   $config['width'] 			= $gallery_normal_foto_width;
			   $config['height'] 			= $gallery_normal_foto_height;
			   $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
			   $config['new_image']		= $ret["file_path"].'/normal/'.$filename;
			   $config['thumb_marker']	= '';
			   $this->image_lib->initialize($config);
			   $this->image_lib->resize();

			  return $ret;
		  }
	 }
	
	public function categories_add()
	{
		  if(isset($_POST['name']) && $_POST['name'] != '')
		  {
			   if(!$this->gallery->getCategoryByName($_POST['name']))
			   {
				    $url = $_POST['url'];
				    if($_POST['url'] == '')
				    {
					$this->load->helper('translit_helper');
					$url = translitRuToEn($_POST['name']);                        
				    }
				    if(!$this->gallery->getCategoryByUrl($url))
				    {
					     // Создаём папку для раздела
					     $folder = $_POST['folder'];
					     if($folder == '')
					     {
						      $folder = $url;
					     }
					     if(!file_exists("/upload/gallery/categories/".$folder."/"))
						      @mkdir($_SERVER['DOCUMENT_ROOT']."/upload/gallery/categories/".$folder."/");
					     if(!file_exists("/upload/gallery/categories/".$folder."/mini/"))
						      @mkdir($_SERVER['DOCUMENT_ROOT']."/upload/gallery/categories/".$folder."/mini/");
					     if(!file_exists("/upload/gallery/categories/".$folder."/normal/"))
						      @mkdir($_SERVER['DOCUMENT_ROOT']."/upload/gallery/categories/".$folder."/normal/");

							       
					     //echo $folder;die();
					     ////////////////////////////
					     
					     $image = '';
//var_dump($folder);die();
					     if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
						      if ($_FILES['userfile']['name'] != '') {				    
							     $imagearr = $this->upload_category_foto($folder);
							     if($image != '') unlink($_SERVER['DOCUMENT_ROOT'].$image);
							     $image = '/upload/gallery/categories/';
							     if($folder != '')
							     {							       
									$image .= $folder."/";
							     }
							     $image .= $imagearr['file_name'];
						      }
					     }
					     $active = 0;
					     if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
					     
					     if($_POST['title'] == '') $_POST['title'] = $_POST['name'];
					     if($_POST['keywords'] == '') $_POST['keywords'] = $_POST['name'];
					     if($_POST['description'] == '') $_POST['description'] = $_POST['name'];
					     
					     $dbins = array(
					     'name'           => $_POST['name'],
					     'url'            => $url,
					     'num'            => $_POST['num'],
					     'active'         => $active,
					     'title'          => $_POST['title'],
					     'keywords'       => $_POST['keywords'],
					     'description'    => $_POST['description'],
					     'image'		=> $image,
					     'parent_id'	=> $_POST['parent_id'],
					     'seo'            => $_POST['seo'],
					     'date'		=> date("Y-m-d"),
					     'time'		=> date("H:i"),
					     'folder'		=> $folder
					     );
					     $this->db->insert('gallery_categories',$dbins);
					     redirect("/admin/gallery/categories/");
				    }
				    else $err = "Раздел с таким url уже есть!";
			   }
			   else $err = "Раздел с таким названием уже есть!";
		  }
		  $err = '';
		  $data['title']  	= "Добавление раздела галереи";
		  $data['num'] 		= $this->gallery->getNewCategoryNum();
		  $data['err']		= $err;
		  $data['categories']	= $this->gallery->getHomeCategories();
		  //$data['categories'] = $this->gallery->getCategories();
		  $this->load->view('admin/gallery_categories_add',$data);		  
	}
        
                
        public function categoriesEdit($id)
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
		
		$image = '';
		  if(isset($_POST['image'])) $image = $_POST['image'];
		  if(isset($_POST['image_del']) && $_POST['image_del'] == true)
		  {
			 unlink($_SERVER['DOCUMENT_ROOT'].$image);
			 $image = '';
			 
		  }
		  if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
			   if ($_FILES['userfile']['name'] != '') {
				    
				  $imagearr = $this->upload_category_foto($_POST['folder']);
				  if($image != '') {
				    if(file_exists($_SERVER['DOCUMENT_ROOT'].$image))
					     @unlink($_SERVER['DOCUMENT_ROOT'].$image);
				  }
				  //var_dump($imagearr);die();
				  $folder = '';
				  if($_POST['folder'] != '') $folder = $_POST['folder'].'/';
				  $image = '/upload/gallery/categories/'.$folder.$imagearr['file_name'];
			   }
		    }
		  
		  if($_POST['title'] == '') $_POST['title'] = $_POST['name'];
		  if($_POST['keywords'] == '') $_POST['keywords'] = $_POST['name'];
		  if($_POST['description'] == '') $_POST['description'] = $_POST['name'];
		  
                $dbins = array(
                                   'name'           => $_POST['name'],
				    'url'            => $url,
				    'num'            => $_POST['num'],
				    'active'         => $active,
				    'title'          => $_POST['title'],
				    'keywords'       => $_POST['keywords'],
				    'description'    => $_POST['description'],
				    'image'		=> $image,
				    'parent_id'		=> $_POST['parent_id'],
				    'seo'            => $_POST['seo'],
				    'date'		=> date("Y-m-d"),
				    'time'		=> date("H:i"),
				    'folder'		=> $_POST['folder']
                               );
                $this->db->where('id',$id);
                $this->db->limit(1);
                $this->db->update('gallery_categories',$dbins);
                redirect("/admin/gallery/categories/");
            }
            $data['category'] = $this->gallery->getCategoryById($id);
            $data['title']  = "Редактирование категории галереи";
            $data['err'] = $err;
            $data['num'] = $this->gallery->getNewCategoryNum();
            $data['categories'] = $this->gallery->getHomeCategories();
            $this->load->view('admin/gallery_categories_edit',$data);
        }
        
        public function categoriesUp($id)
        {
            $cat = $this->gallery->getCategoryById($id);
            if(($cat) && $cat['num'] > 0)
            {
                $num = $cat['num']-1;
                $oldcat = $this->gallery->getCategoryByNum($num);
                $dbins = array('num' => $num);
                $this->db->where('id',$id)->limit(1)->update('gallery_categories',$dbins);
                if($oldcat)
                {
                    $dbins = array('num' => ($num+1));
                    $this->db->where('id',$oldcat['id'])->limit(1)->update('gallery_categories',$dbins);
                }
            }
            redirect('/admin/gallery/categories/');
        }
        public function categoriesDown($id)
        {
            $cat = $this->gallery->getCategoryById($id);
            if(($cat) && $cat['num'] < ($this->gallery->getNewCategoryNum()-1))
            {
                $num = $cat['num']+1;
                $oldcat = $this->gallery->getCategoryByNum($num);
                $dbins = array('num' => $num);
                $this->db->where('id',$id)->limit(1)->update('gallery_categories',$dbins);
                if($oldcat)
                {
                    $dbins = array('num' => ($num-1));
                    $this->db->where('id',$oldcat['id'])->limit(1)->update('gallery_categories',$dbins);
                }
            }
            redirect('/admin/gallery/categories/');
        }
	
	function delete($arg){
		  $d=opendir($arg);
		  while($f=readdir($d)){
		    if($f!="."&&$f!=".."){
		      if(is_dir($arg."/".$f))
			delete($arg."/".$f);
		      else 
			unlink($arg."/".$f);
		    }
		  }
		  closedir($d); 
		  rmdir($arg);
	 }
        
        public function categoriesDel($id)
        {
		  $category = $this->gallery->getCategoryById($id);
		  
		  $this->delete($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/categories/'.$category['folder']);
		  
            $this->db->where('id',$id)->limit(1)->delete('gallery_categories');
            redirect("/admin/gallery/categories/");
        }
	
	public function categoriesActive($id)
	{
		  $this->ma->setActive($id,'gallery_categories');
		  redirect('/admin/gallery/categories/');
	}
	
	public function addFoto()
	{
		  if(isset($_POST['name']) && $_POST['name'] != '')
		  {
			   $image = '';

			   if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
				    if ($_FILES['userfile']['name'] != '') {
					     $category = $this->gallery->getCategoryById($_POST['category_id']);
					     $categoryFolder = '';
					     if($category) $categoryFolder = $category['folder'];
					   $imagearr = $this->upload_foto($_POST['name'], $categoryFolder);
					   if($image != '') {
					     if(file_exists($_SERVER['DOCUMENT_ROOT'].$image))
						      unlink($_SERVER['DOCUMENT_ROOT'].$image);
					   }
					   $image = $imagearr['file_name'];
				    }
			   }
			   $active = 0;
			   if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
			   $showintop = 0;
			   if(isset($_POST['showintop']) && $_POST['showintop'] == true) $showintop = 1;
			   
			   if($_POST['title'] == '') 		$_POST['title'] 	= $_POST['name'];
			   if($_POST['keywords'] == '') 	$_POST['keywords'] 	= $_POST['name'];
			   if($_POST['description'] == '') 	$_POST['description'] 	= $_POST['name'];
			   $dbins = array(
			   'name'           => $_POST['name'],
			   'num'            => $_POST['num'],
			   'active'         => $active,
			   'title'          => $_POST['title'],
			   'keywords'       => $_POST['keywords'],
			   'description'    => $_POST['description'],
			   'image'  	    => $image,
			   'seo'            => $_POST['seo'],
			   'category_id'    => $_POST['category_id'],
			   'date'		=> date("Y-m-d"),
			   'time'		=> date("H:i"),
			   'showintop'		=> $showintop,
			   'login'		=> $this->session->userdata('login')
			   );
			   $this->db->insert('gallery_images',$dbins);
			   $this->session->set_userdata('gallery_category_id', $_POST['category_id']);
			   redirect("/admin/gallery/");
				    
			   
		  }
		  $err = '';
		  $data['title']  	= "Добавление фото в галерею";
		  $data['categories']	= $this->gallery->getHomeCategories();
		  $data['num'] 		= $this->gallery->getNewImageNum();
		  $data['err']		= $err;
		  //$data['categories'] = $this->gallery->getCategories();
		  $this->load->view('admin/gallery_foto_add',$data);
	}
	
	public function editFoto($id)
	{
		  if(isset($_POST['name']) && $_POST['name'] != '')
		  {
			   $image = '';
			   
			   if(isset($_POST['oldfoto'])) $image = $_POST['oldfoto'];
			   if(isset($_POST['del']))
			   {
				    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/'.$image))
					     unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/'.$image);
				    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/mini/'.$image))
					     unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/mini/'.$image);
				    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/normal/'.$image))
					     unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/normal/'.$image);
				    $_POST['oldfoto'] = '';
			   }
			   if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
				    if ($_FILES['userfile']['name'] != '') {				    
					   $imagearr = $this->upload_foto();
					   if($image != '') {
					     if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/'.$image))
						      unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/'.$image);
					     if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/mini/'.$image))
						      unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/mini/'.$image);
					     if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/normal/'.$image))
						      unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/normal/'.$image);
					   }
					   $image = $imagearr['file_name'];
				    }
			   }
			   $active = 0;
			   if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
			   $showintop = 0;
			   if(isset($_POST['showintop']) && $_POST['showintop'] == true) $showintop = 1;
			   if($_POST['title'] == '') 		$_POST['title'] 	= $_POST['name'];
			   if($_POST['keywords'] == '') 	$_POST['keywords'] 	= $_POST['name'];
			   if($_POST['description'] == '') 	$_POST['description'] 	= $_POST['name'];
			   $dbins = array(
			   'name'           => $_POST['name'],
			   'num'            => $_POST['num'],
			   'active'         => $active,
			   'title'          => $_POST['title'],
			   'keywords'       => $_POST['keywords'],
			   'description'    => $_POST['description'],
			   'image'  	    => $image,
			   'seo'            => $_POST['seo'],
			   'category_id'    => $_POST['category_id'],
			   'date'		=> date("Y-m-d"),
			   'time'		=> date("H:i"),
			   'showintop'		=> $showintop
			   );
			   $this->db->where('id', $id);
			   $this->db->update('gallery_images',$dbins);
			   redirect("/admin/gallery/");
				    
			   
		  }
		  $err = '';
		  $data['title']  	= "Добавление раздела галереи";
		  $data['foto']		= $this->gallery->getFoto($id);
		  $data['categories']	= $this->gallery->getHomeCategories();
		  $data['num'] 		= $this->gallery->getNewImageNum();
		  $data['err']		= $err;
		  //$data['categories'] = $this->gallery->getCategories();
		  $this->load->view('admin/gallery_foto_edit',$data);
	}
	
	public function delFoto($id)
	{
		  $foto = $this->gallery->getFoto($id);
		  $image = $foto['image'];
		  if($image != '') {
			   if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/'.$image))
				    @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/'.$image);
			   if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/mini/'.$image))
				    @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/mini/'.$image);
			   if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/normal/'.$image))
				    @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/gallery/normal/'.$image);
		  }
		  $this->db->where('id',$id);
		  $this->db->delete('gallery_images');
		  
		  redirect('/admin/gallery/');
	}
	
	public function options()
	{
		  $data['title']  	= "Настройки галереи";
		  $data['main']		= $this->gallery->getMain();		  
		  //$data['categories'] = $this->gallery->getCategories();
		  $this->load->view('admin/gallery_options',$data);
	}
	
	public function optionsEdit()
	{
		  if(isset($_POST['title']))
		  {
			   $dbins = array(
			   'title'           => $_POST['title'],
			   'keywords'            => $_POST['keywords'],
			   'description'         => $_POST['description'],
			   'h1'          => $_POST['h1'],
			   'robots'       => $_POST['robots'],
			   'cols'    => $_POST['cols'],
			   'pagination'  	    => $_POST['pagination'],
			   'seo'            => $_POST['seo']
			   );
			   $this->db->where('id', 1);
			   $this->db->update('gallery',$dbins);
			   redirect("/admin/gallery/options/");
		  }
		  $data['title']  	= "Редактирование настроек галереи";
		  $data['main']		= $this->gallery->getMain();		  
		  //$data['categories'] = $this->gallery->getCategories();
		  $this->load->view('admin/gallery_options_edit',$data);
	}
	
	function encrypt($chars_min=10, $chars_max=20, $use_upper_case=false, $include_numbers=true, $include_special_chars=false)
        {
            $length = rand($chars_min, $chars_max);
            $selection = 'aeuoyibcdfghjklmnpqrstvwxzQWERTYUIOPASDFGHJKLZXCVBNM';
            if($include_numbers) {
                $selection .= "1234567890";
            }
            if($include_special_chars) {
                $selection .= "!@\"#$%&[]{}?|";
            }
                                    
            $password = "";
            for($i=0; $i<$length; $i++) {
                $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];            
                $password .=  $current_letter;
            }                
            
            return $password;
        }
	
	public function zipImport()
	{
		  if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл			   
			   if ($_FILES['userfile']['name'] != '') {				    
				    $file = $this->upload_zip();
				    $zip = new ZipArchive();
				    $filename = "./upload/temp/".$file['file_name'];
				    $zip->open($filename);
				    $zip->extractTo('./upload/temp/');
				    $zip->close();
				    @unlink($filename);
				    $files = scandir("./upload/temp/");
				    $count = count($files);
				    if($count > 2)
				    {					     
					     $folder = './upload/gallery/';
					     $category = $this->gallery->getCategoryById($_POST['category_id']);
					     $article_name = $_POST['name'];
					     $file_path = "./upload/temp/";
					     if($category)
					     {
						      if($category['folder'] != '') $folder = './upload/gallery/categories/'.$category['folder'].'/';
					     }
					     //var_dump($files);die();
					     
					     $j = 1; // Подписка номера к названию фотки
					     for($i = 2; $i < $count; $i++)
					     {						      
						      // Настраиваем название файла
		  
						      $gallery_file_names = $this->model_options->getOption('gallery_file_names');
						      if(!$gallery_file_names) $gallery_file_names = 'translit';
						      						      						      
						      if($gallery_file_names == 'translit')
						      {
							       $this->load->helper('translit_helper');
							       $fnametrans = translitRuToEn($article_name);
							       $new_filename = $fnametrans.'.'.end(explode(".", $files[$i]));
						      }
						      elseif($gallery_file_names == 'encrypt')
						      {
							       $fnametrans = $this->encrypt();
							       $new_filename = $fnametrans.'.'.end(explode(".", $files[$i]));
						      }
						      else
						      {
							       $arr = explode(".", $files[$i]);
							       $carr = count($arr);
							       $fnametrans = '';
							       for($i = 0; $i < ($carr-1); $i++)
							       {
									$fnametrans .= $arr[$i];
									if(($i+1) < ($carr-1)) $fnametrans .= '.';
							       }
							       $new_filename = $fnametrans.'.'.$arr[$carr-1];
						      }
						      
						      $fcount = 1;
						      $resname = $fnametrans;
						      while(file_exists($folder.$new_filename))
						      {
							       $fcount++;
							       $resname = $fnametrans.'-'.$fcount;									
							       $new_filename = $resname.'.'.end(explode(".", $files[$i]));
						      }						      					      
						      
						      $imgsize = getimagesize($file_path.$files[$i]);
						      //var_dump($imgsize);die();
						      $image_width 		= 0;
						      $image_height 		= 0;
						      if($imgsize)
						      {
							       $image_width 	= $imgsize[0];
							       $image_height 	= $imgsize[1];
						      }
						      
						      
						      $gallery_max_foto_width = $this->model_options->getOption('gallery_max_foto_width');
						      if(!$gallery_max_foto_width) $gallery_max_foto_width = 600;
						      $gallery_max_foto_height = $this->model_options->getOption('gallery_max_foto_height');
						      if(!$gallery_max_foto_height) $gallery_max_foto_height = 600;
						      
						      if($image_width < $gallery_max_foto_width) $gallery_max_foto_width = $image_width;
						      if($image_height < $gallery_max_foto_height) $gallery_max_foto_height = $image_height;
						      
						      $gallery_mini_foto_width = $this->model_options->getOption('gallery_mini_foto_width');
						      if(!$gallery_mini_foto_width) $gallery_mini_foto_width = 600;
						      $gallery_mini_foto_height = $this->model_options->getOption('gallery_mini_foto_height');
						      if(!$gallery_mini_foto_height) $gallery_mini_foto_height = 600;
						      
						      if($image_width < $gallery_mini_foto_width) $gallery_mini_foto_width = $image_width;
						      if($image_height < $gallery_mini_foto_height) $gallery_mini_foto_height = $image_height;
						      
						      $gallery_normal_foto_width = $this->model_options->getOption('gallery_normal_foto_width');
						      if(!$gallery_normal_foto_width) $gallery_normal_foto_width = 600;
						      $gallery_normal_foto_height = $this->model_options->getOption('gallery_normal_foto_height');
						      if(!$gallery_normal_foto_height) $gallery_normal_foto_height = 600;
						      
						      if($image_width < $gallery_normal_foto_width) $gallery_normal_foto_width = $image_width;
						      if($image_height < $gallery_normal_foto_height) $gallery_normal_foto_height = $image_height;
						      
						      
						      if(
							 ($image_width > $gallery_max_foto_width && $image_height < $gallery_max_foto_height) ||
							 ($image_width < $gallery_max_foto_width && $image_height > $gallery_max_foto_height) ||
							 ($image_width > $gallery_max_foto_width && $image_height > $gallery_max_foto_height)
							 )
						      {
							       //var_dump("asd");die();
							       $config['image_library'] 	= 'GD2';
							       $config['create_thumb'] 	= FALSE;
							       $config['maintain_ratio'] 	= TRUE;
							       $config['width'] 			= $gallery_max_foto_width;
							       $config['height'] 			= $gallery_max_foto_height;
							       $config['source_image'] 			= $file_path.$files[$i];
							       $config['new_image']			= $folder.$new_filename;
							       $config['thumb_marker']	= '';
							       $this->image_lib->initialize($config);
							       $this->image_lib->resize();
							       unlink($file_path.$filename);
							       $filename = $new_filename;
							       
						      }
						      else{
							       //var_dump("qwe");die();
							       $config['image_library'] 	= 'GD2';
							       $config['create_thumb'] 	= FALSE;
							       $config['maintain_ratio'] 	= TRUE;				    
							       $config['source_image'] 		= $file_path.$files[$i];
							       $config['new_image']		= $folder.$new_filename;
							       $config['thumb_marker']	= '';
							       $this->image_lib->initialize($config);
							       $this->image_lib->resize();
							       
							       unlink($file_path.$files[$i]);
							       $filename = $new_filename;
						      }
						      
						      // ВОДЯНОЙ ЗНАК
						      $gallery_watermark = $this->model_options->getOption('gallery_watermark');
						      
						      if($gallery_watermark)
						      {				    
							       $config['source_image'] = $folder.$new_filename;
							       $config['create_thumb'] = FALSE;
							       $config['wm_type'] = 'overlay';
							       $config['wm_opacity']	= 20;
							       $config['wm_overlay_path'] = 'img/logo.png';
							       $config['wm_hor_alignment'] = 'right';
							       $this->image_lib->initialize($config);
							       $this->image_lib->watermark();
						      }
						      //////////////
						      
						      $config['image_library'] 		= 'GD2';
						      $config['create_thumb'] 		= FALSE;
						      $config['maintain_ratio'] 	= TRUE;
						      $config['width'] 			= $gallery_mini_foto_width;
						      $config['height'] 	 	= $gallery_mini_foto_height;
						      $config['source_image'] 		= $folder.$new_filename;
						      $config['new_image']		= $folder.'mini/'.$new_filename;
						      $config['thumb_marker']		= '';
						      $this->image_lib->initialize($config);
						      $this->image_lib->resize();
						      
						      $config['image_library'] 		= 'GD2';
						      $config['create_thumb'] 		= FALSE;
						      $config['maintain_ratio'] 	= TRUE;
						      $config['width'] 			= $gallery_normal_foto_width;
						      $config['height'] 		  = $gallery_normal_foto_height;
						      $config['source_image'] 		= $folder.$new_filename;
						      $config['new_image']		= $folder.'normal/'.$new_filename;
						      $config['thumb_marker']		= '';
						      $this->image_lib->initialize($config);
						      $this->image_lib->resize();
						      //die();
						      
						      /// ДОБАВЛЯЕМ ЗАПИСЬ О НОВОЙ ФОТКЕ В БД
						      $name = $_POST['name'].' - '.$j;
						      $dbins = array(
							       'name'		=> $name,
							       'image'		=> $new_filename,
							       'category_id'	=> $_POST['category_id'],
							       'title'		=> $name,
							       'keywords'	=> $name,
							       'description'	=> $name,
							       'num'		=> $this->gallery->getNewImageNum(),
							       'robots'		=> "index, follow",
							       'active'		=> 1,
							       'count'		=> 0,
							       'date'		=> date("Y-m-d"),
							       'time'		=> date("H:i"),
							       'showintop'	=> 1
						      );
						      $this->db->insert('gallery_images', $dbins);
						      
						      $j++;
					     }
					     //die();
					     
					     
					     
				    }
			   }
			   redirect("/admin/gallery/");
		  }
		  $data['err']		= "";
		  $data['categories']	= $this->gallery->getHomeCategories();
		  $data['title']  	= "Импорт из ZIP архива";
		  $this->load->view('admin/gallery_zip_import',$data);
	}
}