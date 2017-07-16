<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Add extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
            $this->load->model('Model_articles','articles');
	    $this->load->model('Model_categories','categories');
            $this->load->model('Model_options','options');
            $this->load->model('Model_users','users');
	    $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
	    isLogin();
        }
        
        function upload_foto()
        {								// Функция загрузки и обработки фото
            
            // Проверка наличия папки текущей даты. Если нет, то создать
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/articles/'.date("Y-m-d").'/'))
            {
                mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/articles/'.date("Y-m-d").'/', 0777);
            }
            //////
            
            $config['upload_path'] 	= 'upload/articles/'.date("Y-m-d");
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
		    
			$width = $this->options->getOption('article_foto_max_width');			   
			$height = $this->options->getOption('article_foto_max_height');
			if(!$width) $width = 200;
			if(!$height) $height = 200;
			
			if(($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
			if(($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];
                    
                    $config['source_image'] = $ret["file_path"].$ret['file_name'];
                    $config['create_thumb'] = FALSE;
                    $config['wm_type'] = 'overlay';
                    $config['wm_opacity']	= 20;
                    $config['wm_overlay_path'] = 'img/logo.png';
                    $config['wm_hor_alignment'] = 'right';
                    $this->image_lib->initialize($config);
                    $this->image_lib->watermark();
                    
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
                     
                    return $ret;
            }
	 }
       
        public function addArticle()
        {            
            $msg = '';
            $title = 'Публикация новой статьи';
            $err = false;
            $add_ok = false;
            if($this->session->userdata('login') != null && $this->session->userdata('pass') != null && $this->session->userdata('type') != null)
            {
                $login = $this->session->userdata('login');
                $user = $this->users->getUserByLogin($login);
                $type = $user['type'];
                if($user['articles_ban'] != 1)
                {
                    if(isset($_POST['name']) && isset($_POST['content']))
                    {
                        $name = trim($_POST['name']);
                        $url = '';
                        if(isset($_POST['url'])) $url = $_POST['url'];
                        if($url == '')
                        {
                            $this->load->helper('translit_helper');
                            $url = translitRuToEn($name);                        
                        }
                        $category_id = '';
                        $cat_ids = $_POST['category_id'];
                        $ccount = count($cat_ids);
                        for($i = 0; $i < $ccount; $i++)
                        {
                               $category_id .= $cat_ids[$i];
                               if(($i+1) < $ccount) $category_id .= '*';
                        }
                        $h1 = '';
                        if(isset($_POST['h1'])) $h1 = $_POST['h1'];
                        if($h1 == '') $h1 = $name;
                        $num = $this->articles->getNewNum();
                        if(isset($_POST['num'])) $num = $_POST['num'];
                        
                        $time = date("H:i");
                        if(isset($_POST['time'])) $time = $_POST['time'];
                        $date = date("Y-m-d");
                        if(isset($_POST['date'])) $date = $_POST['date'];
                        
                        $image = '';
                        if(isset($_POST['image'])) $image = $_POST['image'];
                        if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки 
                               if ($_FILES['userfile']['name'] != '') {
                                      $imagearr = $this->upload_foto();
                                      $image = '/upload/articles/'.date("Y-m-d").'/'.$imagearr['file_name'];
                               }
                        }
                        
                        $youtube = '';
                        if(isset($_POST['youtube'])) $youtube = $_POST['youtube'];
                        
                        $short_content = '';
                        if(isset($_POST['short_content'])) $short_content = $_POST['short_content'];
                        
                        $content = $_POST['content'];
                        $content = str_replace('href="http://', 'rel="nofollow" href="http://', $content);
                        $content = str_replace('href=\'http://', 'rel="nofollow" href=\'http://', $content);
                        $content = str_replace('href=http://', 'rel="nofollow" href=http://', $content);
                        
                        $source = '';
                        if(isset($_POST['source'])) $source = $_POST['source'];
                        
                        $title = $name;
                        if((isset($_POST['title'])) && $_POST['title'] != '') $title = $_POST['title'];
                        
                        $keywords = $name;
                        if((isset($_POST['keywords'])) && $_POST['keywords'] != '') $keywords = $_POST['keywords'];
                        
                        $description = $name;
                        if((isset($_POST['description'])) && $_POST['description'] != '') $description = $_POST['description'];
                        
                        $robots = "index, follow";
                        if(isset($_POST['robots'])) $robots = $_POST['robots'];
                        
                        $count = 0;
                        if(isset($_POST['count'])) $count = $_POST['count'];
                        
                        $seo = '';
                        if(isset($_POST['seo'])) $seo = $_POST['seo'];
                        
                        $active = 0;
                        if($type == 'author' || $type == 'admin' || $type == 'superadmin' || $type == 'moder')
			{
				if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
			}
			$glavnoe = 1;
                        if($type == 'author' || $type == 'admin' || $type == 'superadmin' || $type == 'moder')
			{
				if(isset($_POST['glavnoe']) && $_POST['glavnoe'] == true) $glavnoe = 1;
			}
                        
                        $moderated = 0;
                        if($type == 'author' || $type == 'admin' || $type == 'superadmin' || $type == 'moder') $moderated = 1;
                        
                        $podglavnoe = 0;
                        $theme = 0;
                        $important = 0;
                        $author = 0;
                        $showintop = 0;
                        
                        // Проверяем на ошибки
                        // КАПЧА
                        $expiration = time()-7200; // Two hour limit
                        $this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
                        
                        // Then see if a captcha exists:
                        $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
                        $binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
                        $query = $this->db->query($sql, $binds);
                        $row = $query->row();
                        
                        if ($row->count == 0)
                            $err['captcha'] = "Вы не верно ввели цифры!";			
                        /////////////////////////////////
                        if($name == '') $err['name'] = "Название не может быть пустым!";
                        if($category_id == '') $err['category_id'] = "Вы не выбрали раздел!";
                        if(strip_tags($content) == '') $err['content']  = "Контент не может быть пустым!";
                        //
                        
                        if(!$err)
                        {
                            $dbins = array(
                                'name'           	=> $name,
                                'url'            	=> $url,
                                'category_id'    	=> $category_id,
                                'short_content'  	=> $short_content,
                                'content'  		=> $content,
                                'h1'  		=> $h1,
                                'image'		=> $image,
                                'num'            	=> $num,
                                'time'		=> $time,
                                'date'		=> $date,
                                'active'         	=> $active,
                                'title'          	=> $title,
                                'youtube'		=> $youtube,
                                'source'          	=> $source,
                                'glavnoe'          	=> $glavnoe,
                                'podglavnoe'	=> $podglavnoe,
                                'theme'          	=> $theme,
                                'important'         => $important,
                                'author'          	=> $author,
                                'showintop'		=> $showintop,
                                'keywords'       	=> $keywords,
                                'description'    	=> $description,
                                'robots'    	=> $robots,
                                'count'		=> $count,
                                'seo'            	=> $seo,
                                'moderated'         => $moderated,
                                'login'             => $login
                            );
                            $this->db->insert('articles',$dbins);
                            
                            $new_article_inform_email = $this->options->getOption('new_article_inform_email');
                            if($new_article_inform_email)
                            {
				if($type != 'admin')
				{
					$category = $this->categories->getCategoryById($category_id);
					if(!$category) $category = '';
					else $category = $category[0]['name'];
					$this->load->helper('mail_helper');
					$message = '
					На сайт <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a> добавлена новая статья!<br />
					Название: <strong>'.$name.'</strong><br />
					url: <strong>'.$url.'</strong><br />
					Раздел: <strong>'.$category.'</strong><br />
					<br />
					<i>С уважением, Автобот сайта <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>!</i>
					';
					mail_send($new_article_inform_email,'Новая статья на '.$_SERVER['SERVER_NAME'],$message);
				}
                            }
                            $add_ok = true;
                            
                            $title = "Статья успешно добавлена!";
                            $msg = "Ваша статья успешно добавлена на сайт!";
                            if($type == 'user') $msg .= '<br />После проверки модератором Ваша статья появится на сайте!';
                            $msg .= '<br />Благодарим Вас за проявленный интерес к нашему сайту!';
                            $data['title']          = $title.$this->model_options->getOption('global_title');
                            $data['keywords']       = $title.$this->model_options->getOption('global_keywords');
                            $data['description']    = $title.$this->model_options->getOption('global_description');
                            $data['robots']         = "noindex, nofollow";
                            $data['h1']             = $title;
                            $data['content']	= $msg;
                            $data['breadcrumbs']	= $title;
                            $data['seo']            = "";    
                            $this->load->view('msg.tpl.php', $data);
                        }
                    }
                    
                    if(!$add_ok)
                    {
                        $this->load->helper('captcha');
                        $vals = array(
                            'img_path' => './captcha/',
                            'font_path' => './system/fonts/texb.ttf',
                            'img_url' => 'http://'.$_SERVER['SERVER_NAME'].'/captcha/'                                            
                            );
                        
                        $cap = create_captcha($vals);
                        
                        $data = array(
                            'captcha_time' => $cap['time'],
                            'ip_address' => $this->input->ip_address(),
                            'word' => $cap['word']
                            );
                        
                        $query = $this->db->insert_string('captcha', $data);
                        $this->db->query($query);
                        
                        $data['cap']		= $cap;
                        $data['err']		= $err;
                        $data['categories']     = $this->categories->getCategories(1);
                        $data['num']            = $this->articles->getNewNum();
                        
                        $data['title']          = "Добавление статьи".$this->model_options->getOption('global_title');
                        $data['keywords']       = "Добавление статьи".$this->model_options->getOption('global_keywords');
                        $data['description']    = "Добавление статьи".$this->model_options->getOption('global_description');
                        $data['robots']         = "noindex, nofollow";
                        $data['h1']             = "Добавление статьи";
                        $data['seo']            = "";    
                        $this->load->view('add/article.tpl.php', $data);
                    }
                }
                else
                {
                    $msg = 'Вы не можете публиковать новые статьи, так как Вы забанены! Обратитесь к администрации.';
                    $data['title']          = $title.$this->model_options->getOption('global_title');
                    $data['keywords']       = $title.$this->model_options->getOption('global_keywords');
                    $data['description']    = $title.$this->model_options->getOption('global_description');
                    $data['robots']         = "noindex, nofollow";
                    $data['h1']             = $title;
                    $data['content']	= $msg;
                    $data['breadcrumbs']	= $title;
                    $data['seo']            = "";    
                    $this->load->view('msg.tpl.php', $data);
                }
            }
            else
            {
                $msg = 'Для добавления статьи, Вам необходимо <a rel="nofollow" href="/register/">зарегистрироваться</a> или авторизироваться!';
                $data['title']          = $title.$this->model_options->getOption('global_title');
		$data['keywords']       = $title.$this->model_options->getOption('global_keywords');
		$data['description']    = $title.$this->model_options->getOption('global_description');
		$data['robots']         = "noindex, follow";
		$data['h1']             = $title;
		$data['content']	= $msg;
		$data['breadcrumbs']	= $title;
		$data['seo']            = "";    
		$this->load->view('msg.tpl.php', $data);
            }
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */