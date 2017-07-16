<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
	    //$this->load->helper('login_helper');
            $this->load->model('Model_blogs','blogs');
            $this->load->model('Model_users','users');
	    $this->load->model('Model_options','options');
	    $this->load->model('Model_comments','comments');
	    
	    $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
            isLogin();
        }
	
	public function index()
	{
            $data['title']          = $this->blogs->getOption('main_title').$this->model_options->getOption('global_title');
            $data['keywords']       = $this->blogs->getOption('main_keywords').$this->model_options->getOption('global_keywords');
            $data['description']    = $this->blogs->getOption('main_description').$this->model_options->getOption('global_description');
            $data['robots']         = $this->blogs->getOption('main_robots');
            $data['h1']             = $this->blogs->getOption('main_h1');
            $data['seo']            = $this->blogs->getOption('main_seo');
	    $data['content']	    = $this->blogs->getLastBlogContentOrderByCountDesc($this->blogs->getOption('main_blog_content_count'));
	    
	    //$data['glavnoe']	    = $this->art->getGlavnoe();	    
	    $this->load->view('blog/main.tpl.php', $data);
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
        
        public function createBlog()
        {
            $data['title']          = "Создание блога".$this->model_options->getOption('global_title');
            $data['keywords']       = "Создание блога".$this->model_options->getOption('global_keywords');
            $data['description']    = "Создание блога".$this->model_options->getOption('global_description');
            $data['robots']         = "noindex, nofollow";
            $data['h1']             = "Создание блога";
            $data['seo']            = "";            
            
            if($this->session->userdata('login') !== false && $this->session->userdata('type') !== false)
            {
                $err = false;
                $blog = $this->blogs->getBlogByLogin($this->session->userdata('login'));
                if(!$blog)
                {
                    if(isset($_POST['name']))
                    {
                        $_POST['name'] = trim($_POST['name']);
                        if($_POST['name'] == '')
                            $err['name'] = 'Название блога не может быть пустым!';
                        if(trim(strip_tags($_POST['content'])) == '')
                            $err['content'] = 'Контент не может быть пустым!';
                        
                        $zvanie = 'новичёк';
                        if($_POST['p_code'] != '')
                        {
                            $code = $this->blogs->getInvitationCode($_POST['p_code'],0);
                            if(!$code)
                                $err['p_code'] = 'Вы не верно указали код приглашения!<br />Вы можете зарегистрироваться без пригласительного кода.';
                            else
                            {
                                $zvanie = "ветеран";
                                $this->blogs->makeInvitationCodeUsed($_POST['p_code'], $this->session->userdata('login'));
                            }
                        }
                            
                        if(!$err)
                        {
                            $user = $this->users->getUserByLogin($this->session->userdata('login'));
                            
                            $image = '';
                            if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
                                if ($_FILES['userfile']['name'] != '') {				    
                                    $imagearr = $this->upload_foto();
                                    if($image != '') @unlink($_SERVER['DOCUMENT_ROOT'].$image);
                                    $image = $imagearr['file_name'];
                                }
                            }
                    
                            $dbins = array(
                                'login'             => $this->session->userdata('login'),
                                'name'              => $_POST['name'],
                                'url'               => $user['id'],
                                'short_content'     => $_POST['short_content'],
                                'content'           => $_POST['content'],
                                'image'             => $image,
                                'title'             => $_POST['name'],
                                'keywords'          => $_POST['name'],
                                'description'       => $_POST['name'],
                                'zvanie'            => $zvanie                                
                            );
                            
                            $this->db->insert('blogs',$dbins);
                            redirect("/blog/user/".$user['id']."/");
                        }
                    }
                    $data['err'] = $err;
                    $this->load->view('blog/createBlog.tpl.php', $data);
                }
                else
                {
                    $data['breadcrumbs'] = '
                <span typeof="v:Breadcrumb">
                    <a property="v:title" rel="v:url" href="http://'.$_SERVER['SERVER_NAME'].'/blog/">Блог</a>
                </span>
                &nbsp;»&nbsp;
                Создание блога
                ';
                $data['content'] = 'У Вас уже есть свой блог!<br />Вы не можете создать больше одного блога!';
                $this->load->view('msg.tpl.php', $data);
                }
            }
            else
            {
                $data['breadcrumbs'] = '
                <span typeof="v:Breadcrumb">
                    <a property="v:title" rel="v:url" href="http://'.$_SERVER['SERVER_NAME'].'/blog/">Блог</a>
                </span>
                &nbsp;»&nbsp;
                Создание блога
                ';
                $data['content'] = 'Для создания блога, Вам необходимо для начала <a rel="nofollow" href="/register/">зарегистрироваться</a> на нашем сайте!<br />
                                    Если Вы уже зарегистрированы у нас, то Вам необходимо пройти авторизацию:
                                    <form method="POST" action="/login/">
                                        <table>
                                            <tr>
                                                <td>Логин:</td>
                                                <td><input type="text" name="login" /></td>
                                            </tr>
                                            <tr>
                                                <td>Пароль:</td>
                                                <td><input type="password" name="pass" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Войти" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    ';
                $this->load->view('msg.tpl.php', $data);
            }
        }
        
        public function showBlog($url)
        {
            $blog = $this->blogs->getBlogByUrl($url,1);
            if(!$blog) err404();
            $blog_content = $this->blogs->getBlogContentsByBlogId($blog['id'], 1);
            
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = $this->blogs->getOption('blog_contents_pagination');
            if(!$per_page) $per_page = 20;
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/blog/user/'.$url.'/';
            $config['prefix']	= '!';
            //$config['use_page_numbers']	= TRUE;
            $config['total_rows'] = count($blog_content);
            $config['num_links'] = 10;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'Следующая →';
            $config['prev_link'] = '← Предыдущая';
            
            $config['num_tag_open'] = '<span class="pagerNum">';
            $config['num_tag_close'] = '</span>';
            $config['cur_tag_open'] = '<span class="pagerCurNum">';
            $config['cur_tag_close'] = '</span>';
            $config['prev_tag_open'] = '<span class="pagerPrev">';
            $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
            $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
            $config['next_tag_close'] = '</span>';
            $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
            $config['last_tag_close'] = '</span>';
            $config['first_tag_open'] = '<span class="pagerFirst">';
            $config['first_tag_close'] = '</span>&nbsp;&nbsp;';
            
            $config['per_page'] = $per_page;
            $config['uri_segment']     = 4;
            $from = intval(str_replace('!','',$this->uri->segment(4)));
            //echo $from;die();
            $page_number=$from/$per_page+1;
            $this->pagination->initialize($config);
            $data['pager']	= $this->pagination->create_links();
            
            $data['blog_content'] = $this->blogs->getBlogContentsByBlogId($blog['id'], 1, $per_page, $from);
            
            $data['blog']   = $blog;
            $data['user']   = $this->users->getUserByLogin($blog['login']);
            
            $title = $this->blogs->getOption('adding_title');
            if(!$title) $title = '';
            $keywords = $this->blogs->getOption('adding_keywords');
            if(!$keywords) $keywords = '';
            $description = $this->blogs->getOption('adding_description');
            if(!$description) $description = '';
            
            $data['title']          = $blog['name'].$title.$this->model_options->getOption('global_title');
            $data['keywords']       = $blog['name'].$keywords.$this->model_options->getOption('global_keywords');
            $data['description']    = $blog['name'].$description.$this->model_options->getOption('global_description');
            $data['robots']         = $blog['robots'];
            $data['h1']             = $blog['name'];
            $data['seo']            = $blog['seo'];
            $this->load->view('blog/blog.tpl.php', $data);
        }
	
	public function showContent($user_id,$content_id)
	{
		$content = $this->blogs->getBlogContentById($content_id,1);
		if(!$content) err404();
		$user = $this->users->getUserById($user_id);
		if(!$user) err404();
		$blog = $this->blogs->getBlogById($content['blog_id'],1);
		if(!$blog) err404();
		
		$title = $this->blogs->getOption('adding_title');
		if(!$title) $title = '';
		$keywords = $this->blogs->getOption('adding_keywords');
		if(!$keywords) $keywords = '';
		$description = $this->blogs->getOption('adding_description');
		if(!$description) $description = '';
		
		// КАПЧА
		$this->load->helper('captcha');
		$vals = array(
		    'img_path' => './captcha/',
		    'font_path' => './system/fonts/texb.ttf',
		    'img_url' => 'http://'.$_SERVER['SERVER_NAME'].'/captcha/'                                            
		    );
		
		$cap = create_captcha($vals);
		
		$dat = array(
		    'captcha_time' => $cap['time'],
		    'ip_address' => $this->input->ip_address(),
		    'word' => $cap['word']
		    );
		
		$query = $this->db->insert_string('captcha', $dat);
		$this->db->query($query);
		
		$data['cap']		= $cap;
		//
		
		$data['content']	= $content;
		$data['user']		= $user;
		$data['blog']		= $blog;
		$data['comments']	= $this->comments->getCommentsToBlogContent($content_id);
		$data['title']          = $content['title'].$title.$this->model_options->getOption('global_title');
		$data['keywords']       = $content['keywords'].$keywords.$this->model_options->getOption('global_keywords');
		$data['description']    = $content['description'].$description.$this->model_options->getOption('global_description');
		$data['robots']         = $content['robots'];
		$data['h1']             = $content['name'];
		$data['seo']            = $content['seo'];
		$this->load->view('blog/blogContent.tpl.php', $data);
	}
        
        public function add_blog_content($blog_id)
        {            
            $data['title']          = "Добавление записи в блог".$this->model_options->getOption('global_title');
            $data['keywords']       = "Добавление записи в блог".$this->model_options->getOption('global_keywords');
            $data['description']    = "Добавление записи в блог".$this->model_options->getOption('global_description');
            $data['robots']         = "noindex, nofollow";
            $data['h1']             = "Добавление записи в блог";
            $data['seo']            = "";            
            
            if($this->session->userdata('login') !== false && $this->session->userdata('type') !== false)
            {
                $err = false;
                $blog = $this->blogs->getBlogByLogin($this->session->userdata('login'));
                if($blog['id'] == $blog_id)
                {
                    if(isset($_POST['name']))
                    {
                        $_POST['name'] = trim($_POST['name']);
                        if($_POST['name'] == '')
                            $err['name'] = 'Название записи блога не может быть пустым!';
                        if(trim(strip_tags($_POST['content'])) == '')
                            $err['content'] = 'Контент не может быть пустым!';                        
                        
                            
                        if(!$err)
                        {
                            $user = $this->users->getUserByLogin($this->session->userdata('login'));
                            
                            $image = '';
                            if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
                                if ($_FILES['userfile']['name'] != '') {				    
                                    $imagearr = $this->upload_foto();
                                    if($image != '') @unlink($_SERVER['DOCUMENT_ROOT'].$image);
                                    $image = $imagearr['file_name'];
                                }
                            }
                            
                            $visible = 1;
                            if(isset($_POST['visible']) && $_POST['visible'] == true) $visible = 0;
                    
                            $dbins = array(
                                'login'             => $this->session->userdata('login'),
                                'name'              => $_POST['name'],
                                'short_content'     => $_POST['short_content'],
                                'content'           => $_POST['content'],
                                'image'             => $image,
                                'title'             => $_POST['name'],
                                'keywords'          => $_POST['name'],
                                'description'       => $_POST['name'],
                                'date'              => date("Y-m-d"),
                                'time'              => date("H:i"),
                                'visible'           => $visible,
                                'blog_id'           => $blog['id']
                            );
                            
                            $this->db->insert('blogs_content',$dbins);
                            redirect("/blog/user/".$blog['url']."/");
                        }
                    }
                    $data['err'] = $err;
                    $data['blog'] = $blog;
                    $this->load->view('blog/add_blog_content.tpl.php', $data);
                }
                else
                {
                    $data['breadcrumbs'] = '
                <span typeof="v:Breadcrumb">
                    <a property="v:title" rel="v:url" href="http://'.$_SERVER['SERVER_NAME'].'/blog/">Блог</a>
                </span>
                &nbsp;»&nbsp;
                Добавление записи в блог
                ';
                $data['content'] = 'Вы не можете добавить запись в чужой блог!';
                $this->load->view('msg.tpl.php', $data);
                }
            }
            else
            {
                $data['breadcrumbs'] = '
                <span typeof="v:Breadcrumb">
                    <a property="v:title" rel="v:url" href="http://'.$_SERVER['SERVER_NAME'].'/blog/">Блог</a>
                </span>
                &nbsp;»&nbsp;
                Создание блога
                ';
                $data['content'] = 'Для создания блога, Вам необходимо для начала <a rel="nofollow" href="/register/">зарегистрироваться</a> на нашем сайте!<br />
                                    Если Вы уже зарегистрированы у нас, то Вам необходимо пройти авторизацию:
                                    <form method="POST" action="/login/">
                                        <table>
                                            <tr>
                                                <td>Логин:</td>
                                                <td><input type="text" name="login" /></td>
                                            </tr>
                                            <tr>
                                                <td>Пароль:</td>
                                                <td><input type="password" name="pass" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Войти" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    ';
                $this->load->view('msg.tpl.php', $data);
            }
        }
        
        public function edit_blog_content($id)
        {
            $data['title']          = "Редактирование записи в блоге".$this->model_options->getOption('global_title');
            $data['keywords']       = "Редактирование записи в блоге".$this->model_options->getOption('global_keywords');
            $data['description']    = "Редактирование записи в блоге".$this->model_options->getOption('global_description');
            $data['robots']         = "noindex, nofollow";
            $data['h1']             = "Редактирование записи в блоге";
            $data['seo']            = "";            
            
            if($this->session->userdata('login') !== false && $this->session->userdata('type') !== false)
            {
                $err = false;
                $blog = $this->blogs->getBlogByLogin($this->session->userdata('login'));
                $blog_content = $this->blogs->getBlogContentById($id, 1);
		//var_dump($blog);
		//echo '<br><BR><BR><BR>';
		//var_dump($blog_content);
		//die();
		
                if($blog)
                {
                    $data['blog']           = $blog;
                    //$data['blog_content']   = $blog_content;
                    
                    if(isset($_POST['name']))
                    {
                        $_POST['name'] = trim($_POST['name']);
                        if($_POST['name'] == '')
                            $err['name'] = 'Название записи блога не может быть пустым!';
                        if(trim(strip_tags($_POST['content'])) == '')
                            $err['content'] = 'Контент не может быть пустым!';                        
                        
                            
                        if(!$err)
                        {
                            $user = $this->users->getUserByLogin($this->session->userdata('login'));
                            
                            $image = '';
                            if(isset($_POST['old_image'])) $image = $_POST['old_image'];
                            if(isset($_POST['del_image']))
                            {
                                @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/blogs/'.$image);
                                @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/blogs/mini/'.$image);
                                @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/blogs/normal/'.$image);
                                $image = '';
                            }
                            if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки			   
                                if ($_FILES['userfile']['name'] != '') {				    
                                    $imagearr = $this->upload_foto();
                                    if($image != '')
                                    {
                                        @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/blogs/'.$image);
                                        @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/blogs/mini/'.$image);
                                        @unlink($_SERVER['DOCUMENT_ROOT'].'/upload/blogs/normal/'.$image);
                                    }
                                    $image = $imagearr['file_name'];
                                }
                            }
                            
                            $visible = 1;
                            if(isset($_POST['visible']) && $_POST['visible'] == true) $visible = 0;
                    
                            $dbins = array(
                                'name'              => $_POST['name'],
                                'short_content'     => $_POST['short_content'],
                                'content'           => $_POST['content'],
                                'image'             => $image,                                
                                'visible'           => $visible
                            );
                            $this->db->where('id', $id);
                            $this->db->update('blogs',$dbins);
                            redirect("/blog/user/".$blog['url']."/".$blog_content['id'].'/');
                        }
                    }
                    $data['err'] = $err;

                    $this->load->view('blog/edit_blog_content.tpl.php', $data);
                    
                }
                else err404();
            }
            else
            {
                $data['breadcrumbs'] = '
                <span typeof="v:Breadcrumb">
                    <a property="v:title" rel="v:url" href="http://'.$_SERVER['SERVER_NAME'].'/blog/">Блог</a>
                </span>
                &nbsp;»&nbsp;
                Создание блога
                ';
                $data['content'] = 'Для создания блога, Вам необходимо для начала <a rel="nofollow" href="/register/">зарегистрироваться</a> на нашем сайте!<br />
                                    Если Вы уже зарегистрированы у нас, то Вам необходимо пройти авторизацию:
                                    <form method="POST" action="/login/">
                                        <table>
                                            <tr>
                                                <td>Логин:</td>
                                                <td><input type="text" name="login" /></td>
                                            </tr>
                                            <tr>
                                                <td>Пароль:</td>
                                                <td><input type="password" name="pass" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Войти" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    ';
                $this->load->view('msg.tpl.php', $data);
            }
        }
        
        public function del_blog_content($id)
        {
            $redirect = '/';
            $this->db->where('id',$id);
            $this->db->limit(1);
            $bc = $this->db->get('blogs_content')->result_array();
            if($bc)
            {
                $this->db->where('id', $bc[0]['blog_id']);
                $this->db->limit(1);
                $blog = $this->db->get('blogs')->result_array();
                if($blog)
                {
                    $redirect = "/blog/user/".$blog[0]['url'].'/';
                }
            }
            $this->db->where('id',$id)->limit(1)->delete('blogs_content');
            
	    redirect($redirect);
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */