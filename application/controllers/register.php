<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
            $this->load->model('Model_users','users');	    
	    $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
	    isLogin();
        }
	
	function upload_foto(){								// Функция загрузки и обработки фото
		  $config['upload_path'] 	= 'upload/avatars';
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
			   $config['width'] 			= 100;
			   $config['height'] 			= 100;
			   $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
			   $config['new_image']		= $ret["file_path"].$ret['file_name'];
			   $config['thumb_marker']	= '';
			   $this->image_lib->initialize($config);
			   $this->image_lib->resize();
			   //$arr = explode('.', $ret['file_name'])
			   
			  return $ret;
		  }
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
	
	public function index()
	{		
		$err = false;
		$reg_ok = false;
		if(isset($_POST['email']))
		{
			//if($_POST['login'] == '')
				$_POST['login'] = $_POST['email'];
			if($_POST['email'] == '')
				$err['email'] = 'e-mail не может быть пустым!';
			if($this->users->getUserByLogin($_POST['login']))
				$err['login'] = 'Выбранный Вами логин уже занят!';
			if($this->users->getUserByEmail($_POST['email']))
				$err['email'] = 'Выбранный Вами e-mail уже зарегистрирован!';
			if($_POST['pass'] == '')
				$err['pass'] = "Пароль не может быть пустым!";
			if($_POST['pass'] != $_POST['pass2'])
				$err['pass'] = "Введённые Вами пароли не совпадают!";
			
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
			
			if(!$err)
			{
				$active = 0;
				//if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
				
				$image = '';
				if(isset($_POST['image'])) $image = $_POST['image'];
				if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки 
				       if ($_FILES['userfile']['name'] != '') {
					      $imagearr = $this->upload_foto();
					      $image = '/upload/avatars/'.$imagearr['file_name'];
				       }
				}
				
				$site = '';
				
				    
				$activation_code = '';
				$email_confirm = $this->model_options->getOption('email_confirm');
				$activation = 1;
				if($email_confirm == 1)
				{
					$activation_code = $this->getActiveCode();
					$activation = 0;
				}
				
				
				$user = $this->users->getUserByLogin($_POST['login']);
				$email = $this->users->getUserByEmail($_POST['email']);
				if(!$user && !$email)
				{
					$dbins = array(
						'login'         	=> $_POST['login'],
						'pass'          	=> md5($_POST['pass']),
						'type'          	=> 'user',
						'name'          	=> $_POST['name'],
						'email'         	=> $_POST['email'],
						'city'          	=> $_POST['city'],
						'tel'				=> $_POST['tel'],
						'active'        	=> 1,
						'sex'           	=> 'no',
						'avatar'        	=> $image,
						'reg_date'		=> date("Y-m-d H:i"),
						'reg_ip'		=> $_SERVER['REMOTE_ADDR'],
						'activation'		=> $activation,
						'activation_code'	=> $activation_code
					);
					$this->db->insert('users',$dbins);
					$reg_ok = true;
					$user = $this->users->getUserByLogin($_POST['login']);
					$message = '';
					if($email_confirm == 1)
					{						
						$message = '

Для активации Вашей учётной записи Вам необходимо перейти по следующей ссылке:
	http://'.$_SERVER['SERVER_NAME'].'/register/activation/'.$user['id'].'/'.$activation_code.'/

Для авторизации на сайте '.$_SERVER['SERVER_NAME'].' используйте свои данные:
Ваши данные для входа:
Логин: '.$_POST['login'].'
Пароль: '.$_POST['pass'].'


Благодарим Вас за проявленный интерес к нашему сайту!
Администрация сайта '.$_SERVER['SERVER_NAME'].'!
';
						
						$this->session->set_userdata('no_auth','true');
						$this->session->set_userdata('msg','Для активации Вашей учётной записи Вам необходимо перейти по ссылке, отправленной Вам на e-mail');
						
					}
					else
					{
						$message = '
						Вы успешно зарегистрировались на сайте '.$_SERVER['SERVER_NAME'].'!<br />
						Ваши данные для входа:<br />
						Логин: '.$_POST['login'].'<br />
						Пароль: '.$_POST['pass'].'<br />
						<br />
						
						Благодарим Вас за проявленный интерес к нашему сайту!<br />
						<i>Администрация сайта <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>!</i>
						';
						$this->session->set_userdata('msg','Вы успешно зарегистрировались!');
					}
					
					$this->load->helper('mail_helper');
					mail_send($_POST['email'],'Регистрация на Peony',$message,"text");
					
					redirect('/');
					$data['email_confirm'] = $email_confirm;
					
					$data['title']          = "Регистрация".$this->model_options->getOption('global_title');
					$data['keywords']       = "Регистрация".$this->model_options->getOption('global_keywords');
					$data['description']    = "Регистрация".$this->model_options->getOption('global_description');
					$data['robots']         = "noindex, follow";
					$data['h1']             = "Регистрация";
					$data['seo']            = "";    
					$this->load->view('register/step2.tpl.php', $data);
				}
				else
				{
					$msg = 'Пользователь с таким';
					if(($user) && ($email)) $msg .= 'и логином и e-mail';
					elseif($user) $msg .= ' логином';
					elseif($email) $msg .= 'e-mail';
					$msg .= ' уже есть в базе!<br /><br /><a mce_href="javascript:history.go(-1)" href="javascript:history.go(-1)">Назад</a>';
						
					$data['title']          = "Регистрация";
					$data['keywords']       = "Регистрация";
					$data['description']    = "Регистрация";
					$data['robots']         = "noindex, nofollow";
					$data['h1']             = "Ошибка регистрации";
					$data['content']	= $msg;
					$data['breadcrumbs']	= "Ошибка регистрации";
					$data['seo']            = "";    
					$this->load->view('msg.tpl.php', $data);
				}
			}
			else
			{
				$reg_ok = false;
				$err['err'] = "Ошибка добавления пользователя в базу! Обратитеь к администрации сайта.";
			}
				
		}
		
		
		if(!$reg_ok)
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
			
			$data['title']          = "Регистрация".$this->model_options->getOption('global_title');
			$data['keywords']       = "Регистрация".$this->model_options->getOption('global_keywords');
			$data['description']    = "Регистрация".$this->model_options->getOption('global_description');
			$data['robots']         = "noindex, follow";
			$data['h1']             = "Регистрация";
			$data['seo']            = "";    
			$this->load->view('register/register.tpl.php', $data);
		}
	}
	
	public function activation($user_id, $activation_code)
	{
		$user = $this->users->getUserById($user_id);
		$msg = '';
		$title = '';

		if($user)
		{

			//vd($user['activation_code']);
			//vd($activation_code);
			if($user['activation_code'] == '' && $user['activation'] == 1)
			{
				$data['msg'] = "Ваша учётная запись уже активирована!";
				$title = "Активация учётной записи";
			}
			else if(strtolower($user['activation_code']) == strtolower($activation_code))
			{
				$dbins = array(
					'activation_code'	=> '',
					'activation'		=> 1,
					'active'		=> 1
				);
				$this->db->where('id',$user['id'])->limit(1)->update('users',$dbins);
				//set_userdata('msg','Вы успешно активировали свой аккаунт!');
				$back = userdata('last_url');
				if(isset($_GET['back'])) $back = urldecode($_GET['back']);
				if(!$back) $back = '/';
				set_userdata('login',$user['login']);
				set_userdata('pass',$user['pass']);
				set_userdata('type',$user['type']);
				redirect($back);
				die();
				/*
				$this->load->helper('mail_helper');
				$message = '
Вы успешно активировали свой аккаунт на сайте '.$_SERVER['SERVER_NAME'].'!
Ваш Логин: '.$user['login'].'

Благодарим Вас за проявленный интерес к нашему сайту!
Администрация сайта '.$_SERVER['SERVER_NAME'].'!
';
				mail_send($user['email'],'Активация аккаунта на '.$_SERVER['SERVER_NAME'].' прошла успешно!',$message,"text");
				

				$title = "Активация выполнена успешно!";

				$this->session->set_userdata('login',$user['login']);
				$this->session->set_userdata('pass',$user['pass']);
				$this->session->set_userdata('type',$user['type']);
				*/
			}
			else
			{
				$data['msg'] = "Не верный код активации!<br />
					Вы можете <a rel=\"nofollow\" href=\"/register/send-activation-code/".$user['id']."/\">запросить код активации</a>.";
				$title = "Ошибка активации";
			}
		}
		else
		{
			$msg = "Пользователь не найден в нашей базе!<br />
				Вам необходимо повторить процедуру регистрации.<br />
				<a rel=\"nofollow\" href=\"/register/\">Перейти к регистрации</a>";
			$title = "Ошибка активации";
		}
				
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
	
	public function send_activation_code($user_id)
	{
		$msg = '';
		$title = '';
		$user = $this->users->getUserById($user_id);
		if($user)
		{
			$title = "Повторный запрос кода активации аккаунта";
			$this->load->helper('mail_helper');
			$message = '
Вы запросили повторный код активации на сайте '.$_SERVER['SERVER_NAME'].'!
Ваш Логин: '.$user['login'].'	

Для активации Вашей учётной записи Вам необходимо перейти по следующей ссылке:

	http://'.$_SERVER['SERVER_NAME'].'/register/activation/'.$user['id'].'/'.$user['activation_code'].'/

Благодарим Вас за проявленный интерес к нашему сайту!
Администрация сайта '.$_SERVER['SERVER_NAME'].'!
			';
			mail_send($user['email'],$title,$message,"text");
			$msg = 'Код активации был успешно отправлен на указанный Вами при регистрации e-mail адрес!';
		}
		else
		{
			$msg = "Такого пользователя в базе нет. Возможно он был удалён в связи с тем, что активация не была произведена вовремя.";
			$title = "Повторный запрос кода активации аккаунта";
		}
		$this->session->set_userdata('msg', $msg);
		redirect('/');
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
	/*
	public function forgot() // ЗАБЫЛИ ПАРОЛЬ
	{
		$err = false;
		
		if(isset($_POST['email']) && isset($_POST['captcha']))
		{
			if($_POST['email'] == '')
				$err['email'] = 'e-mail не может быть пустым!';
				
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
			
			$user = $this->users->getUserByEmail($_POST['email']);
			if(!$user)
				$err['err'] = "Пользователь с таким e-mail адресом не зарегистрирован!";
			
			if(!$err)
			{
				$forgot = $this->getActiveCode();
				
				$dbins = array(
					'forgot'	=> $forgot
				);
				
				$this->db->where('id', $user['id']);
				$this->db->update('users', $dbins);
				
				$message = 'Добрый день!<br />
Вы запросили восстановление логина и пароля на сайте <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>.<br /><br />
Ваш логин на сайте: <strong>'.$user['login'].'</strong><br />
Чтобы задать новый пароль, перейдите по ссылке:<br />
<a href="http://'.$_SERVER['SERVER_NAME'].'/register/set_password/'.$user['id'].'/'.$forgot.'/">http://'.$_SERVER['SERVER_NAME'].'/register/set_password/'.$user['id'].'/'.$forgot.'/</a><br /><br />
Желаем Вам не забывать пароли)) Администрация сайта <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>';
				
				$this->load->helper('mail_helper');
				mail_send($user['email'],'Восстановление логина и пароля на '.$_SERVER['SERVER_NAME'],$message,"text");
				
				$title = "Восстановление пароля";
				$msg = "В течении 10 минут на Ваш e-mail адрес будет доставлено письмо с дальнейшими инструкциями.";
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
		$title = "Восстановление пароля";
		$data['err']		= $err;
		$data['title']          = $title.$this->model_options->getOption('global_title');
		$data['keywords']       = $title.$this->model_options->getOption('global_keywords');
		$data['description']    = $title.$this->model_options->getOption('global_description');
		$data['robots']         = "noindex, nofollow";
		$data['h1']             = $title;		
		$data['breadcrumbs']	= $title;
		$data['seo']            = "";    
		$this->load->view('register/forgot.tpl.php', $data);
	}
	*/
	
	public function forgot() // ЗАБЫЛИ ПАРОЛЬ
	{
		$err = false;
		
		if(isset($_POST['email']))
		{
			if($_POST['email'] == '')
			{
				set_userdata('msg','e-mail не может быть пустым!');
			}
			else
			{
				$user = $this->users->getUserByEmail($_POST['email']);
				if(!$user)
					set_userdata('msg',"Пользователь с таким e-mail адресом не зарегистрирован!");
				else
				{				
					$forgot = $this->getActiveCode();
					
					$dbins = array(
						'forgot'	=> $forgot
					);
					
					$this->db->where('id', $user['id']);
					$this->db->update('users', $dbins);
					
					$message = 'Добрый день!
Вы запросили восстановление логина и пароля на сайте '.$_SERVER['SERVER_NAME'].'
Ваш логин на сайте: '.$user['login'].'
Чтобы задать новый пароль, перейдите по ссылке:
http://'.$_SERVER['SERVER_NAME'].'/register/set_password/'.$user['id'].'/'.$forgot.'/
Желаем Вам не забывать пароли))
Администрация сайта '.$_SERVER['SERVER_NAME'];
					
					$this->load->helper('mail_helper');
					mail_send($user['email'],'Восстановление логина и пароля на '.$_SERVER['SERVER_NAME'],$message,"text");
					
					
					set_userdata('msg',"В течении 10 минут на Ваш e-mail адрес будет доставлено письмо с дальнейшими инструкциями.");
					
					
				}
			}
		}
		else set_userdata('msg',"Вы не ввели e-mail для восстановления пароля!");
		redirect('/');
	}
	
	/*
	public function set_password($id, $forgot)
	{
		$user = $this->users->getUserById($id);
		if($user)
		{
			if(($user['forgot'] != '') && $forgot == $user['forgot'])
			{
				$err = false;
				if(isset($_POST['pass']) && isset($_POST['pass2']))
				{
					if($_POST['pass'] == '' || $_POST['pass'] == ' ')
						$err['err'] = "Пароль не может быть пустым!";
					if($_POST['pass'] != $_POST['pass2'])
						$err['err'] = "Введённые Вами пароли не совпадают!";
					
					if(!$err)
					{
						$dbins = array(
							'pass'		=> md5($_POST['pass']),
							'forgot'	=> ''
						);
						
						$this->db->where('id',$user['id']);
						$this->db->update('users', $dbins);
						
						
						
						$message = '
						Ваш пароль на сайте <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a> был успешно изменён.<br /><br />
						Ваш логин: <strong>'.$user['login'].'</strong><br />
						Ваш новый пароль: <strong>'.$_POST['pass'].'</strong><br /><br />						
						Благодарим Вас за проявленный интерес к нашему ресурсу!<br />
						Администрация сайта <a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>';
						
						unset($_POST);
						
						$this->load->helper('mail_helper');
						mail_send($user['email'],'Пароль успешно изменён!',$message,"text");
						
						$title = "Восстановление пароля";
						$msg = 'Ваш пароль был успешно изменён! Теперь Вы можете зайти на сайт под своим логином и новым паролем!<br /><br />
						<a href="/">На главную</a>';
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
				
				$title = "Восстановление пароля";
				$data['err']		= $err;
				$data['title']          = $title.$this->model_options->getOption('global_title');
				$data['keywords']       = $title.$this->model_options->getOption('global_keywords');
				$data['description']    = $title.$this->model_options->getOption('global_description');
				$data['robots']         = "noindex, nofollow";
				$data['h1']             = $title;		
				$data['breadcrumbs']	= $title;
				$data['seo']            = "";    
				$this->load->view('register/set_password.tpl.php', $data);
			}
			else
			{
				$title = "Восстановление пароля";
				$msg = 'При попытке восстановить пароль произошла непредвиденная ошибка! Возможно полученная Вами ссылка устарела.<br />
				Попробуйте повторить попытку восстановления пароля с <a rel="nofollow" href="/register/forgot/">самого начала</a>.<br />
				Если Вы неоднократно пробовали восстановить пароль и постоянно видите это сообщение, просим Вас связаться с администрацией сайта!<br /><br />
				<a href="/">На главную</a>';
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
		else err404();
	}
	*/
	
	public function set_password($id, $forgot)
	{
		$user = $this->users->getUserById($id);
		if($user)
		{
			if(($user['forgot'] != '') && $forgot == $user['forgot'])
			{
				$err = false;
				if(isset($_POST['pass']) && isset($_POST['pass2']))
				{
					if($_POST['pass'] == '' || $_POST['pass'] == ' ')
					{
						set_userdata('msg',"Пароль не может быть пустым!");
						set_userdata('forgot',"true");
						redirect('/?forgot='.$forgot.'&id='.$id);
					}
					elseif($_POST['pass'] != $_POST['pass2'])
					{
						set_userdata('msg',"Введённые Вами пароли не совпадают!");
						set_userdata('forgot',"true");
						redirect('/?forgot='.$forgot.'&id='.$id);
					}
					
					else
					{
						$dbins = array(
							'pass'		=> md5($_POST['pass']),
							'forgot'	=> ''
						);
						
						$this->db->where('id',$user['id']);
						$this->db->update('users', $dbins);
						
						
						
						$message = '
Ваш пароль на сайте '.$_SERVER['SERVER_NAME'].' был успешно изменён.
Ваш логин: '.$user['login'].'
Ваш новый пароль: '.$_POST['pass'].'					
Благодарим Вас за проявленный интерес к нашему ресурсу!
Администрация сайта '.$_SERVER['SERVER_NAME'];
						
						unset($_POST);
						
						$this->load->helper('mail_helper');
						mail_send($user['email'],'Пароль успешно изменён!',$message,"text");
						
						$title = "Восстановление пароля";
						set_userdata('msg','Ваш пароль был успешно изменён! Теперь Вы можете зайти на сайт под своим логином и новым паролем!');
						
						redirect('/');
					}
				}
				else
				{
					set_userdata("forgot",'true');
					redirect('/?forgot='.$forgot.'&id='.$id);
				}
			}
			else
			{
				vd('При попытке восстановить пароль произошла непредвиденная ошибка! Возможно полученная Вами ссылка устарела.<br />Попробуйте повторить попытку восстановления пароля с <a rel="nofollow" href="?forgot">самого начала</a>.');
				set_userdata('msg', 'При попытке восстановить пароль произошла непредвиденная ошибка! Возможно полученная Вами ссылка устарела.<br />Попробуйте повторить попытку восстановления пароля с <a rel="nofollow" href="?forgot">самого начала</a>.');
			}
			
		//	redirect('/');
		}
		else err404();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */