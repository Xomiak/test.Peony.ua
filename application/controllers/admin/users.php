<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('login_helper');
		isAdminLogin();
		$this->load->model('Model_admin','ma');
		$this->load->model('Model_users','users');
		$this->load->model('Model_shop','shop');
		$this->load->model('Model_categories','categories');
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

	public function index()
	{

//		 if(isset($_GET['format']))
//		 {
//		 	$users = $this->users->getUsers();
//		 	$count = count($users);
//		 	for($i = 0; $i < $count; $i++)
//		 	{
//		 		$user = $users[$i];
//
//
//				$dbins['name'] = trim($user['name']);
//				$dbins['lastname'] = trim($user['lastname']);
//				$this->db->where('id', $user['id']);
//				$this->db->limit(1);
//				$this->db->update('users', $dbins);
//
//		 	}
//		 	var_dump("All done!");
//		 }

		// Обновляем типы клиентов
		if(isset($_GET['set_types'])){
			$users = $this->users->getUsers();
			foreach ($users as $user){
				// Проверяем кол-во заказов пользователем и, при необходимости, меняем тип клиента
				$ocount = $this->shop->getUserOrdersCount($user['id']);
				if($ocount == 0 && $user['user_type_id'] != 11 && $user['user_type_id'] != 10 && $user['user_type_id'] != 12 && $user['user_type_id'] != 6 && $user['user_type_id'] != 4){
					echo $ocount.'. Посетитель<hr>';
					$this->db->where('id', $user['id'])->limit(1)->update('users', array('user_type_id' => 1, 'user_type' => 'Посетитель'));
				} elseif ($ocount > 0 && $ocount < 4 && $user['user_type_id'] != 11 && $user['user_type_id'] != 10 && $user['user_type_id'] != 12 && $user['user_type_id'] != 6 && $user['user_type_id'] != 4){
					echo $ocount.'. Покупатель<hr>';
					$this->db->where('id', $user['id'])->limit(1)->update('users', array('user_type_id' => 2, 'user_type' => 'Покупатель'));
				} elseif ($ocount >= 4 && $user['user_type_id'] != 11 && $user['user_type_id'] != 10 && $user['user_type_id'] != 12 && $user['user_type_id'] != 6 && $user['user_type_id'] != 4){
					echo $ocount.'. Постоянный<hr>';
					$this->db->where('id', $user['id'])->limit(1)->update('users', array('user_type_id' => 3, 'user_type' => 'Постоянный'));
				}
				//////////////////////////////////////////////////////////////////////////////////
			}
		}

		$data['title']  = "Пользователи";

		$user_type = userdata('user_type');



		$sort_by = userdata('users_sort');
		if(!$sort_by) $sort_by = 'id';

		$order_by = userdata('users_order');
		if(!$order_by) $order_by = 'DESC';



		if(isset($_GET['sort']))
		{
			set_userdata('users_sort', $_GET['sort']);
			if($order_by == 'DESC') set_userdata('users_order', 'ASC');
			else set_userdata('users_order', 'DESC');

			$sort_by = $_GET['sort'];
		}

		if(isset($_POST['user_type']) && $user_type != $_POST['user_type'])
		{
			$user_type = $_POST['user_type'];
			if($user_type == 'Все') $user_type = false;
			set_userdata('user_type', $user_type);
		}
		$data['filter'] = $user_type;

		if(isset($_POST['search']))
		{
			$data['users'] = $this->users->search($_POST['search']);

			$data['pager'] = '';
		}
		else
		{
			$a = $this->users->getUsersCount2($order_by,$user_type,$sort_by);
			$data['usersCount'] = $a;
			// ПАГИНАЦИЯ //
			$this->load->library('pagination');
			$per_page = 35;
			$config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/users/';
			$config['total_rows'] = $a;
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

			if($page_number > 1) $this->session->set_userdata('articlesFrom', $from);
			else $this->session->unset_userdata('articlesFrom');
			//////////

			$data['userTypes'] = $this->users->getUserTypes(1);

			$data['users'] = $this->users->getUsers($from, $per_page,$order_by,$user_type,$sort_by);
		}
		$this->load->view('admin/users',$data);
	}

	public function add()
	{
		$err = '';
		if(isset($_POST['login']) && $_POST['login'] != '')
		{
			if((!$this->users->getUserByLogin($_POST['login'])) && (!$this->users->getUserByEmail($_POST['email'])))
			{
				if($_POST['pass'] == '')
				{
					$err = "Пароль не может быть пустым!";
				}
				else if($_POST['pass'] != $_POST['pass2'])
				{
					$err = "Введённые Вами пароли не совпадают!";
				}

				if($err == '')
				{
					$active = 0;
					if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;

					$image = '';
					if(isset($_POST['image'])) $image = $_POST['image'];
					if (isset($_FILES['userfile'])) {					// проверка, выбран ли файл картинки
						if ($_FILES['userfile']['name'] != '') {
							$imagearr = $this->upload_foto();
							$image = '/upload/avatars/'.$imagearr['file_name'];
						}
					}

					$mailer = 0;
					if(isset($_POST['mailer']) && $_POST['mailer'] == true) $mailer = 1;

					$site = '';
					if($_POST['site'] != '' && $_POST['site'] != 'http://')
						$site = trim($_POST['site']);

					$dbins = array(
						'login'         => $_POST['login'],
						'pass'          => md5($_POST['pass']),
						'type'          => $_POST['type'],
						'name'          => $_POST['name'],
						'email'         => $_POST['email'],
						'sex'           => $_POST['sex'],
						'city'          => $_POST['city'],
						'active'        => $active,
						'age'           => $_POST['age'],
						'site'          => $site,
						'avatar'        => $image,
						'reg_date'	=> date("Y-m-d H:i"),
						'reg_ip'	=> $_SERVER['REMOTE_ADDR'],
						'mailer'	=> $mailer
					);
					$this->db->insert('users',$dbins);
					redirect("/admin/users/");
				}
			}
			else $err = 'Пользователь с таким логином и/или email адресом уже зарегистрирован!';
		}
		$data['err']    = $err;
		$data['userTypes'] = $this->users->getUserTypes(1);
		$data['title']  = "Добавление пользователя";
		$data['users'] = $this->users->getUsers();
		$this->load->view('admin/users_add',$data);
	}

	public function edit($id)
	{
		$err = '';
		if(isset($_POST['name']))
		{
			$user = $this->users->getUserById($id);

//		  if($_POST['email'] == '')
//			   $err = "e-mail не может быть пустым!";

			if($err == '')
			{
				$active = 0;
				if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;

				$mailer = 0;
				if(isset($_POST['mailer']) && $_POST['mailer'] == true) $mailer = 1;

				$type = 'client';
				if($_POST['user_type'] == 'Администратор') $type = 'admin';

				$user_type = $this->users->getUserTypeById($_POST['user_type_id']);

				$dbins = array(
					'user_type'          => $user_type['name'],
					'user_type_id'          => $_POST['user_type_id'],
					'name'          => $_POST['name'],
					'lastname'			=> $_POST['lastname'],
					'email'         => $_POST['email'],
					'sex'           => $_POST['sex'],
					'city'          => $_POST['city'],
					'country'          => $_POST['country'],
					'adress'			=> $_POST['adress'],
					'active'        => $active,
					'tel'           => $_POST['tel'],
					'mailer'		=> $mailer,
					'type'		=> $_POST['type']
				);
				$this->db->where('id',$id);
				$this->db->limit(1);
				$this->db->update('users',$dbins);
				redirect("/admin/users/");
			}
		}

		$data['orders'] = $this->model_shop->getOrdersByUserId($id);

		$data['err']    = $err;
		$data['userTypes'] = $this->users->getUserTypes(1);
		$data['title']  = "Данные клиента";
		$data['user'] = $this->users->getUserById($id);
		$this->load->view('admin/users_edit',$data);
	}

	function users_types()
	{
		if(isset($_POST['new_type']))
		{
			$bd_mailing = 0;
			$discount = 0;
			if(isset($_POST['bd_mailing']) && $_POST['bd_mailing'] == true) $bd_mailing = 1;
			if(isset($_POST['discount'])) $discount = $_POST['discount'];
			$dbins = array(
				'name' 			=> trim($_POST['new_type']),
				'bd_mailing' 	=> $bd_mailing,
				'congratulation'=> $_POST['congratulation'],
				'discount'		=> $discount,
				'nadbavka'=> $_POST['nadbavka']
			);
			$this->db->insert('user_types', $dbins);
		}
		elseif(isset($_POST['save_type']))
		{
			$bd_mailing = 0;
			$discount = 0;
			if(isset($_POST['bd_mailing']) && $_POST['bd_mailing'] == true) $bd_mailing = 1;
			if(isset($_POST['discount'])) $discount = $_POST['discount'];
			$dbins = array(
				'name' 			=> trim($_POST['type']),
				'bd_mailing' 	=> $bd_mailing,
				'congratulation'=> $_POST['congratulation'],
				'discount'		=> $discount,
				'nadbavka'=> $_POST['nadbavka']
			);
			$this->db->where('id', $_POST['id']);
			$this->db->limit(1);
			$this->db->update('user_types', $dbins);
		}

		$data['userTypes'] = $this->users->getUserTypes();
		$data['title']  = "Типы клиентов";
		$this->load->view('admin/users_types',$data);
	}

	public function type_del($id)
	{
		$ut = $this->users->getUserTypeById($id);
		$this->db->where('id',$id)->limit(1)->delete('user_types');
		redirect("/admin/users/types/");
	}

	public function del($id)
	{
		$this->db->where('id',$id)->limit(1)->delete('users');
		redirect("/admin/users/");
	}

	public function type_active($id)
	{
		$this->ma->setActive($id,'user_types');
		redirect('/admin/users/types/');
	}

	public function active($id)
	{
		$this->ma->setActive($id,'users');
		redirect('/admin/users/');
	}

	public function export()
	{

		header("Content-Description: File Transfer\r\n");header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . 'export_'.date("Y-m-d_H-i").'.csv');
		$fp = fopen('php://output', 'w');
		header("Content-Type: text/csv; charset=CP1251\r\n");

		$dbins = array(
			iconv('UTF-8','CP1251','Имя'),
			iconv('UTF-8','CP1251','e-mail'),
			iconv('UTF-8','CP1251','Город')
		);

		//headers
		fputcsv($fp, $dbins, ';', '"');

		$articles = $this->users->getUsers();
		//var_dump($articles);die();
		$count = count($articles);
		for($i = 0; $i < $count; $i++)
		{
			$p = $articles[$i];

			if($p['type'] != 'admin')
			{

				$row = array(
					iconv('UTF-8','CP1251',$p['name']),
					iconv('UTF-8','CP1251',$p['email']),
					iconv('UTF-8','CP1251',$p['city'])
				);

				fputcsv($fp, $row, ';', '"');
			}
		}

		fclose($fp);
	}



}