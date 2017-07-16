<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blogs extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->helper('admin_helper');
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_blogs','blogs');
            $this->load->model('Model_users','users');
        }
	
	function CreateThumb($sizex, $sizey, $image, $folder)
	 {
	     require_once('./application/thumbs/ThumbLib.inc.php');
	     $filethumb = false;
 
	     if ($sizex > 0 && $sizey > 0 && !empty($image) && file_exists('.'.$image) && !empty($folder)){
		 if (!is_dir('./upload/blogs/'.$folder))
		     mkdir('./upload/blogs/'.$folder);
		
		 $ex = end(explode('.', $image));
		 $filename = end(explode('/', $image));
		 $filethumb = '/upload/blogs/'.$folder.'/'.$filename;
		    
		 if (!file_exists('.'.$filethumb)){
		     $thumb = PhpThumbFactory::create('.'.$image);
		     $thumb->resize($sizex, $sizey);
		     $thumb->save('.'.$filethumb, $ex);
		 }
	     }
	    //var_dump($filethumb); die();
	     return $filethumb;
	 }
	
	function upload_foto()
        {								// Функция загрузки и обработки фото
            $config['upload_path'] 	= 'upload/blogs';
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
                
                $config['source_image'] = $ret["file_path"].$ret['file_name'];
                $config['create_thumb'] = FALSE;
                $config['wm_type'] = 'overlay';
                $config['wm_opacity']	= 20;
                $config['wm_overlay_path'] = 'img/logo.png';
                $config['wm_hor_alignment'] = 'right';
                $this->image_lib->initialize($config);
                $this->image_lib->watermark();
                
                $this->CreateThumb(200,200,'/upload/blogs/'.$ret['file_name'],'mini');
                $this->CreateThumb(600,600,'/upload/blogs/'.$ret['file_name'],'normal');
                
                /*
                $config['image_library'] 	= 'GD2';
                $config['create_thumb'] 	= TRUE;
                $config['maintain_ratio'] 	= TRUE;
                $config['width'] 			= 200;
                $config['height'] 			= 200;
                $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
                $config['new_image']		= $ret["file_path"].'/mini/'.$ret['file_name'];
                $config['thumb_marker']	= '';
                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                
                $config['image_library'] 	= 'GD2';
                $config['create_thumb'] 	= TRUE;
                $config['maintain_ratio'] 	= TRUE;
                $config['width'] 			= 600;
                $config['height'] 			= 600;
                $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
                $config['new_image']		= $ret["file_path"].'/normal/'.$ret['file_name'];
                $config['thumb_marker']	= '';
                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                */
                //$arr = explode('.', $ret['file_name'])
                
                return $ret;
            }
	}
           
	public function index()
	{
            $blogs = $this->blogs->getAllBlogs();
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = 35;
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/blogs/';
            $config['total_rows'] = count($blogs);
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
            $data['title']  = "Блоги";
            $data['blogs'] = $this->blogs->getAllBlogs(-1, $per_page, $from);
            $this->load->view('admin/blogs',$data);
	}
        
        public function edit($id)
        {
            $err = '';
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
                $url = $_POST['url'];
                if($_POST['url'] == '')
                {
                    //$this->load->helper('translit_helper');
                    //$url = translitRuToEn($_POST['name']);
                    $url = $id;
                }
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
		
		$image = '';
		  if(isset($_POST['image'])) $image = $_POST['image'];
		  if(isset($_POST['image_del']) && $_POST['image_del'] == true)
		  {
			 @unlink($_SERVER['DOCUMENT_ROOT'].$image);
			 $image = '';
			 
		  }
		  if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
			   if ($_FILES['userfile']['name'] != '') {				    
				  $imagearr = $this->upload_foto();
				  if($image != '') @unlink($_SERVER['DOCUMENT_ROOT'].$image);
				  $image = $imagearr['file_name'];
			   }
		    }
		  
                $dbins = array(
                                   'name'           => $_POST['name'],
                                   'url'            => $url,
                                   'short_content'  => $_POST['short_content'],
				   'content'        => $_POST['content'],
                                   'rating'         => $_POST['rating'],
                                   'active'         => $active,
                                   'title'          => $_POST['title'],
                                   'keywords'       => $_POST['keywords'],
                                   'description'    => $_POST['description'],
                                   'robots'         => $_POST['robots'],
                                   'seo'            => $_POST['seo'],
				   'image'	    => $image,
                                   'zvanie'         => $_POST['zvanie']
                               );
                $this->db->where('id',$id);
                $this->db->limit(1);
                $this->db->update('blogs',$dbins);
                redirect("/admin/blogs/");
            }
            $data['blog'] = $this->blogs->getBlogById($id);
            $data['title']  = "Редактирование блога";
            $data['err'] = $err;            
            $this->load->view('admin/blogs_edit',$data);
        }
        
        
        
        public function del($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('blogs');
            redirect("/admin/blogs/");
        }
	
	public function active($id)
	{
		  $this->ma->setActive($id,'blogs');
		  redirect('/admin/blogs/');
	}
        
        
        /////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////
        
        public function blog($id)
        {
            $blog = $this->blogs->getBlogById($id);
            $blogs = $this->blogs->getBlogContentsByBlogId($id);
            $user = $this->users->getUserByLogin($blog['login']);
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = 35;
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/blogs/blog/'.$id.'/';
            $config['total_rows'] = count($blogs);
            $config['num_links'] = 4;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'далее';
            $config['prev_link'] = 'назад';
            
            $config['per_page'] = $per_page;
            $config['uri_segment']     = 5;
            $from = intval($this->uri->segment(5));
            $page_number=$from/$per_page+1;
            $this->pagination->initialize($config);
            $data['pager']	= $this->pagination->create_links();
            //////////
            $data['title']  = "Блог пользователя ".$blog['login'];
            $data['blogs_content'] = $this->blogs->getBlogContentsByBlogId($id, -1, $per_page, $from);
            $data['blog'] = $blog;
            $data['user'] = $user;
            $this->load->view('admin/blogs_blog',$data);
        }
        
        public function blog_content_edit($id)
        {
            $err = '';
	    $data['blog'] = $this->blogs->getBlogContentById($id);
	    $blog = $this->blogs->getBlogById($data['blog']['blog_id']);
	    $data['user_blog'] = $blog;
	    
            if(isset($_POST['name']) && $_POST['name'] != '')
            {
		  /*
                $url = $_POST['url'];
                if($_POST['url'] == '')
                {
                    //$this->load->helper('translit_helper');
                    //$url = translitRuToEn($_POST['name']);
                    $url = $id;
                }
		  */
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
		
		$image = '';
		  if(isset($_POST['image'])) $image = $_POST['image'];
		  if(isset($_POST['image_del']) && $_POST['image_del'] == true)
		  {
			 @unlink($_SERVER['DOCUMENT_ROOT'].$image);
			 $image = '';
			 
		  }
		  if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
			   if ($_FILES['userfile']['name'] != '') {				    
				  $imagearr = $this->upload_foto();
				  if($image != '') @unlink($_SERVER['DOCUMENT_ROOT'].$image);
				  $image = $imagearr['file_name'];
			   }
		    }
		  
                $dbins = array(
                                   'name'           => $_POST['name'],
                                   'short_content'  => $_POST['short_content'],
				   'content'        => $_POST['content'],
                                   'rating'         => $_POST['rating'],
                                   'active'         => $active,
                                   'title'          => $_POST['title'],
                                   'keywords'       => $_POST['keywords'],
                                   'description'    => $_POST['description'],
                                   'robots'         => $_POST['robots'],
                                   'seo'            => $_POST['seo'],
				   'image'	    => $image
                               );
                $this->db->where('id',$id);
                $this->db->limit(1);
                $this->db->update('blogs_content',$dbins);
                redirect("/admin/blogs/blog/".$blog['id']."/");
            }
            
            $data['title']  = "Редактирование контента блога";
            $data['err'] = $err;            
            $this->load->view('admin/blogs_content_edit',$data);
        }
	
	public function invitation_codes()
	{
		  $data['codes'] = $this->blogs->getAllInvitationCodes();
		  $data['title']  = "Пригласительные коды для блога";
		  
		  $this->load->view('admin/blogs_invitation_codes',$data);
	}
	
	public function invitation_code_del($id)
	{
		  $this->db->where('id',$id)->limit(1)->delete('blogs_invitation_codes');
		  redirect("/admin/blogs/invitation_codes/");
	}
	
	 public function invitation_code_add()
	 {
		  $code = $this->getActiveCode();
		  $dbins = array(
			   'code'		=> $code,
			   'admin_login'	=> $this->session->userdata('login'),
			   'date'		=> date("Y-m-d"),
			   'time'		=> date("H:i"),
			   'used'		=> 0
		  );
		  $this->db->insert('blogs_invitation_codes',$dbins);
		  redirect("/admin/blogs/invitation_codes/");
	 }
	 
	 function getActiveCode($chars_min=10, $chars_max=20, $use_upper_case=false, $include_numbers=true, $include_special_chars=false)
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
	
	public function blogOptions()
	{
            $data['title']  = "Опции блога";
            $data['options'] = $this->blogs->getAllOptions();
            $this->load->view('admin/blogs_options',$data);
	}
	
	public function blogOptionsAdd()
        {
            $err = false;
            if(isset($_POST['name']))
            {
                $name = trim($_POST['name']);
                $rus = trim($_POST['rus']);
                $value = trim($_POST['value']);
                $adding = trim($_POST['adding']);
                if($name == '')
                    $err['name'] = "Название не может быть пустым!";
                if($rus == '')
                    $err['rus'] = "Описание не может быть пустым!";                
                
                if(!$err)
                {
                    $dbins = array(
                        'name'      => $name,
                        'rus'       => $rus,
                        'value'     => $value,
                        'adding'    => $adding
                    );
                    $this->db->insert('blogs_options',$dbins);
                    redirect('/admin/blogs/options/');
                }
            }
            $data['title']  = "Добавление опции";
            $data['err'] = $err;            
            $this->load->view('admin/blogs_options_add',$data);
        }
	
	public function blogOptionsEdit($id)
        {
            $err = false;
            if(isset($_POST['name']))
            {
                $name = trim($_POST['name']);
                $rus = trim($_POST['rus']);
                $value = trim($_POST['value']);
                $adding = trim($_POST['adding']);
                if($name == '')
                    $err['name'] = "Название не может быть пустым!";
                if($rus == '')
                    $err['rus'] = "Описание не может быть пустым!";
                                
                if(!$err)
                {
                    $dbins = array(
                        'name'      => $name,
                        'rus'       => $rus,
                        'value'     => $value,
                        'adding'    => $adding
                    );
                    $this->db->where('id', $id)->update('blogs_options', $dbins);
                    redirect('/admin/blogs/options/');
                }
            }
            $data['option'] = $this->blogs->getOptionById($id);	    
            $data['title']  = "Редактирование опции блога";
            $data['err'] = $err;            
            $this->load->view('admin/blogs_options_edit',$data);
        }
	
	public function blogOptionsDel($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('blogs_options');
            redirect("/admin/blogs/options/");
        }
}