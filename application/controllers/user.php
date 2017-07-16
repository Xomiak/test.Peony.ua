<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller
{

    private $current_lang;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_shop', 'shop');


        $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
        isLogin();
    }

    function upload_avatar()
    {
        $config['upload_path'] = 'upload/avatars';
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('avatar')) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();

            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 100;
            $config['height'] = 100;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            //$arr = explode('.', $ret['file_name'])

            return $ret;
        }
    }

    function upload_foto()
    {        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/users/fotos';
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            echo $this->upload->display_errors();
            die();
        } else {
            $width = $this->model_options->getOption('upload_image_max_width');
            $height = $this->model_options->getOption('upload_image_max_height');

            $ret = $this->upload->data();

            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['create_thumb'] = FALSE;
            $config['wm_type'] = 'overlay';
            $config['wm_overlay_path'] = 'img/watermark.png';
            $config['wm_hor_alignment'] = 'right';
            $this->image_lib->initialize($config);
            $this->image_lib->watermark();

            if (($ret['image_width'] != '') && $ret['image_width'] < $width)
                $width = $ret['image_width'];
            if (($ret['image_height'] != '') && $ret['image_height'] < $height)
                $height = $ret['image_height'];

            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            $ret = $this->upload->data();

            return $ret;
        }
    }

    private function getMyCart()
    {
        $user = $this->users->getUserByLogin($this->session->userdata('login'));
        $my_orders = $this->shop->getOrdersByUserId($user['id'], -1, -1, 0);
        $my_cart = array();
        if ($my_orders) {
            for ($i = 0; $i < count($my_orders); $i++) {
                $prod = unserialize($my_orders[$i]['products']);

                for ($j = 0; $j < count($prod); $j++) {
                    $my_cart[] = $prod[$j];
                    $my_cart[count($my_cart) - 1]['status'] = $my_orders[$i]['status'];
                    $my_cart[count($my_cart) - 1]['order_id'] = $my_orders[$i]['id'];
                }

            }
        }

        if ($this->session->userdata('my_cart') !== false)
            $my_cart_now = $this->session->userdata('my_cart');

        if (isset($my_cart_now) && $my_cart_now && !empty($my_cart_now)) {
            $my_cart = array_merge($my_cart, $my_cart_now);
        }
        $my_cart = array_reverse($my_cart);
        return $my_cart;
    }

    public function index()
    {
        $data['title'] = "" . $this->model_options->getOption('global_title');
        $data['keywords'] = "" . $this->model_options->getOption('global_keywords');
        $data['description'] = "" . $this->model_options->getOption('global_description');
        $data['robots'] = "noindex, nofollow";
        $data['h1'] = "";
        $data['seo'] = "";
        $this->load->view('users/mypage.tpl.php', $data);
    }

    function no_auth()
    {
        $data['title'] = "Вы не авторизированы!" . $this->model_options->getOption('global_title');
        $data['keywords'] = "Вы не авторизированы!" . $this->model_options->getOption('global_keywords');
        $data['description'] = "Вы не авторизированы!" . $this->model_options->getOption('global_description');
        $data['robots'] = "noindex, nofollow";
        $data['h1'] = "";
        $data['seo'] = "";
        $this->load->view('users/no_auth.tpl.php', $data);
    }

    public function mypage()
    {
        if ($this->session->userdata('login') != false) {
            $user = $this->users->getUserByLogin($this->session->userdata('login'));
            if ($user) {
                $data['user'] = $user;

                if (isset($_POST['sort'])) {
                    if ($_POST['sort'] == 'all')
                        unset_userdata('sort_orders');
                    else
                        set_userdata('sort_orders', $_POST['sort']);

                    redirect($_SERVER['REQUEST_URI']);
                }

                $sort = userdata('sort_orders');

                if (!$sort) $sort = -1;
                $data['sort'] = $sort;

                $data['title'] = "Личный кабинет" . $this->model_options->getOption('global_title');
                $data['keywords'] = $this->lang->line('mypage_h1') . $data['user']['name'] . $this->model_options->getOption('global_keywords');
                $data['description'] = $this->lang->line('mypage_h1') . $data['user']['name'] . $this->model_options->getOption('global_description');
                $data['robots'] = "noindex, nofollow";
                $data['h1'] = "";
                $data['seo'] = "";
                $data['orders'] = $this->shop->getOrdersByUserId($user['id'], $sort);
                $data['my_cart_and_orders'] = $this->getMyCart();
                $this->load->view('users/mypage.tpl.php', $data);
            } else
                $this->no_auth();
        } else
            $this->no_auth();
    }

    public function showUserPage($id)
    {
        err404();
        $data['user'] = $this->users->getUserById($id);
        if ($data['user']) {
            $this->load->model('Model_articles', 'articles');
            $this->load->model('Model_categories', 'categories');
            $this->load->model('Model_blogs', 'blogs');

            $login = $data['user']['login'];

            $articles = $this->articles->getUserArticles($login, 1);
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = 5;

            $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/users/' . $data['user']['id'] . '/';
            $config['prefix'] = '!';
            //$config['use_page_numbers']	= TRUE;
            $config['total_rows'] = count($articles);
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
            $config['uri_segment'] = 3;
            $from = intval(str_replace('!', '', $this->uri->segment(3)));
            //echo $from;die();
            $page_number = $from / $per_page + 1;
            $this->pagination->initialize($config);
            $data['pager'] = $this->pagination->create_links();
            //////////

            $page_no = '';
            if ($page_number > 1) {
                $page_no = ' (стр. №' . $page_number . ')';
            }

            $data['blog'] = $this->blogs->getBlogByLogin($data['user']['login'], 1);
            $data['articles'] = $this->articles->getUserArticles($login, 1, $per_page, $from);
            $data['articlesCount'] = $this->articles->getUserArticlesCount($data['user']['login']);
            $data['title'] = $this->lang->line('mypage_h1') . $data['user']['name'] . $page_no . $this->model_options->getOption('global_title');
            $data['keywords'] = $this->lang->line('mypage_h1') . $data['user']['name'] . $page_no . $this->model_options->getOption('global_keywords');
            $data['description'] = $this->lang->line('mypage_h1') . $data['user']['name'] . $page_no . $this->model_options->getOption('global_description');
            $data['robots'] = "index, follow";
            $data['h1'] = $this->lang->line('mypage_h1') . $data['user']['name'];
            $data['seo'] = "";
            $this->load->view('users/mypage.tpl.php', $data);
        } else
            err404();
    }

    public function edit_mypage()
    {
        if ($this->session->userdata('login') != false) {
            $data['user'] = $this->users->getUserByLogin($this->session->userdata('login'));
            if ($data['user']) {
                if (isset($_POST['save']) && $_POST['save'] == 'ok') {

                    $bd_date = $this->input->post('bd_date');
                    $bd_date_unix = 0;
                    if ($bd_date != '') {
                        $dbarray = explode('-', $bd_date);
                        vd($dbarray);
                        if (is_array($dbarray)) {
                            $bd_date_unix = mktime(0, 0, 0, $dbarray[1], $dbarray[2], $dbarray[0]);
                        }
                    }
                    //if(isDebug()) vdd($_POST);
                    $dbins = array(
                        'name' => $this->input->post('name'),
                        'lastname' => $this->input->post('lastname'),
                        'city' => $this->input->post('city'),
                        'country' => $this->input->post('country'),
                        'mailer' => (isset($_POST['mailer']) && !empty($_POST['mailer'])) ? 1 : 0,
                        'adress' => $_POST['adress'],
                        'tel' => (isset($_POST['tel']) && !empty($_POST['tel'])) ? $_POST['tel'] : '',
                        'bd_date' => $bd_date,
                        'bd_date_unix' => $bd_date_unix
                    );
                    /*if (isset($_POST['pass']) && !empty($_POST['pass']) && $_POST['pass'] != '') {
                        $dbins['pass'] = md5($_POST['pass']);
                    }*/
                    $this->db->where('id', $data['user']['id']);
                    $this->db->limit(1);
                    $this->db->update('users', $dbins);
                    /*set_userdata('login', $_POST['email']);
                    if (isset($_POST['pass']) && !empty($_POST['pass']) && $_POST['pass'] != '')
                        set_userdata('pass', md5($_POST['pass']));*/
                    redirect("/user/mypage/");
                } else {
                    $data['title'] = $this->lang->line('mypage_edit_h1') . $data['user']['login'] . $this->model_options->getOption('global_title');
                    $data['keywords'] = $this->lang->line('mypage_edit_h1') . $data['user']['login'] . $this->model_options->getOption('global_keywords');
                    $data['description'] = $this->lang->line('mypage_edit_h1') . $data['user']['login'] . $this->model_options->getOption('global_description');
                    $data['robots'] = "noindex, nofollow";
                    $data['h1'] = $this->lang->line('mypage_edit_h1') . $data['user']['login'];
                    $data['seo'] = "";
                    $data['my_cart_and_orders'] = $this->getMyCart();
                    $data['sort'] = (isset($_POST['sort'])) ? $_POST['sort'] : '';
                    $this->load->view('users/mypage_edit.tpl.php', $data);
                }
            }
        } else {
            $data['title'] = "Вы не авторизированы!" . $this->model_options->getOption('global_title');
            $data['keywords'] = "Вы не авторизированы!" . $this->model_options->getOption('global_keywords');
            $data['description'] = "Вы не авторизированы!" . $this->model_options->getOption('global_description');
            $data['robots'] = "noindex, nofollow";
            $data['h1'] = "";
            $data['seo'] = "";
            $this->load->view('users/no_auth.tpl.php', $data);
        }

    }

    public function rating($id)
    {
        err404();
        if ($this->session->userdata('login') !== false) {


            $user = $this->users->getUserById($id);
            $ip = $_SERVER['REMOTE_ADDR'];
            $time = time();
            $user_login = $this->session->userdata('login');
            $rating = $this->users->getRating($user['login'], $ip, $user_login);

            $rating_period = $this->options->getOption('rating_period');
            $all_ok = false;
            if ($rating_period != 0) {
                if (!$rating)
                    $all_ok = true;
                elseif (isset($rating['time'])) {
                    if (($time - $rating['time']) > $rating_period)
                        $all_ok = true;
                }
            } else
                $all_ok = true;

            if ($all_ok) {
                $rat = $user['rating'];
                $rat++;
                $dbins = array(
                    'rating' => $rat
                );
                $this->db->where('id', $user['id']);
                $this->db->update('users', $dbins);

                $dbins = array(
                    'login' => $user['login'],
                    'user_login' => $user_login,
                    'ip' => $ip,
                    'time' => $time
                );
                $this->db->insert('rating', $dbins);

                //var_dump($this->session->userdata('last_url'));die();
                //if($this->session->userdata('last_url') !== false)
                //redirect($this->session->userdata('last_url'));
                //else
                redirect("/user/" . $user['id'] . "/");
            } else {
                $data['title'] = "Вы уже проголосовали за данного участника!";
                $data['keywords'] = "Вы уже проголосовали за данного участника!";
                $data['description'] = "Вы уже проголосовали за данного участника!";
                $data['robots'] = "noindex, nofollow";
                $data['h1'] = "Вы уже проголосовали за данного участника!";
                $data['content'] = 'Вы уже проголосовали за данного участника!<br />
								<a href="/user/' . $user['id'] . '/">Назад</a>';
                $data['breadcrumbs'] = "Голосование";
                $data['seo'] = "";
                $this->load->view('msg.tpl.php', $data);
            }
        } else {
            $data['title'] = "Для голосования Вам необходимо зарегистрироваться!";
            $data['keywords'] = "Для голосования Вам необходимо зарегистрироваться!";
            $data['description'] = "Для голосования Вам необходимо зарегистрироваться!";
            $data['robots'] = "noindex, nofollow";
            $data['h1'] = "Для голосования Вам необходимо зарегистрироваться!";
            $data['content'] = 'Для голосования Вам необходимо <a rel="nofollow" href="/register/">зарегистрироваться</a>!<br />
							<a href="/users/">Назад</a>';
            $data['breadcrumbs'] = "Голосование";
            $data['seo'] = "";
            $this->load->view('msg.tpl.php', $data);
        }
    }

    public function users()
    {
        err404();
        $this->load->model('Model_options', 'options');

        $data['users'] = $this->users->getMemberUsers();
        $data['title'] = "Список зарегистрированных участников конкурса";
        $data['keywords'] = "Список зарегистрированных участников конкурса";
        $data['description'] = "Список зарегистрированных участников конкурса";
        $data['robots'] = "index, follow";
        $data['h1'] = "Список зарегистрированных участников конкурса";
        $data['seo'] = "";
        $this->load->view('users/users.tpl.php', $data);
    }

    public function order_cancel($id)
    {
        $err404 = false;
        $order = $this->shop->getOrderById($id);
        if(!$order) $err404 = true;

        if (userdata('login') === false) $err404 = true;
        $user = $this->users->getUserById($order['user_id']);
        if(!$user) $err404 = true;
        if(userdata('hash') !== false && userdata('hash') == md5($user['pass']))
            $err404 = false;

        if($err404) err404();

        $dbins = array('status' => 'canceled');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->update('orders', $dbins);

        $to = getOption('admin_email');
        $this->load->helper('mail_helper');
        $user = $this->users->getUserByLogin(userdata('login'));
        $message = 'Клиент ' . $user['name'] . ' (' . $user['email'] . ') отказался от заказа №' . $id . '<br />
        <a href="//' . $_SERVER['SERVER_NAME'] . '/admin/orders/edit/' . $id . '/">Просмотреть заказ</a>';
        mail_send($to, 'Отмена заказа №' . $id, $message);

        redirect('/user/mypage/');
    }

    public function order_done($id)
    {
        if (userdata('login') === false) err404();
        $dbins = array('status' => 'done');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->update('orders', $dbins);
        redirect('/user/mypage/');
    }

    public function order_details($id)
    {
        $err = false;
        $order = $this->shop->getOrderById($id);
        if ($order) {
            if (!$order) err404();

            $user = $this->users->getUserById($order['user_id']);
            if (!$user) err404();

            if (isset($_GET['hash']) && $_GET['hash'] == md5($user['pass'])) {
                userdata('hash',$_GET['hash']);
                $err = false;
            } elseif (userdata('login') != false) {
                $err = false;
            } else $err = true;

            if (!$err) {
                $data['order'] = $order;
                $data['user'] = $user;
                $data['title'] = "Заказ №" . $order['id'] . $this->model_options->getOption('global_title');
                $data['keywords'] = '';
                $data['description'] = '';
                $data['robots'] = "noindex, nofollow";
                $data['h1'] = "";
                $data['seo'] = "";
                //$data['orders'] = $this->shop->getOrdersByUserId($user['id']);
                //$data['my_cart_and_orders'] = $this->getMyCart();
                $data['sort'] = (isset($_POST['sort'])) ? $_POST['sort'] : '';
                $this->load->view('users/my_order.tpl.php', $data);
            } else err404();
        }
    }

    public function setMailer($user_id, $secret)
    {
        $mailer = 1;
        $content = "";
        $user = $this->users->getUserById($user_id);
        if (!$user) err404();

        if ($user['pass'] != $secret) err404();

        if (isset($_POST['set_mailer'])) {
            if (post('mailer') == true)
                $mailer = 1;
            else $mailer = 0;


            $dbins = array('mailer' => $mailer);
            $this->db->where('id', $user_id)->limit(1)->update('users', $dbins);
            $content .= '<div class="msg">Изменения успешно сохранены!</div>';
            $user = $this->users->getUserById($user_id);
        }

        $content .= '<form method="post"><input type="hidden" name="set_mailer" value="set_mailer"/><input type="checkbox" name="mailer"';
        if ($user['mailer'] == 1) $content .= ' checked';
        $content .= ' /> Получать новости об наших акциях и новинках<br /><input type="submit" value="Сохранить" /></form>';

        $data['title'] = "Подписка на новости";
        $data['keywords'] = "Подписка на новости";
        $data['description'] = "Подписка на новости";
        $data['robots'] = "noindex, nofollow";
        $data['h1'] = "Подписка на новости";
        $data['msg'] = $content;
        $data['breadcrumbs'] = "Подписка на новости";
        $data['seo'] = "";
        $this->load->view('msg.tpl.php', $data);


    }

    public function dropship_client_adress($id = false)
    {


        $user = $this->users->getUserByLogin($this->session->userdata('login'));

        $client = false;
        if ($id) $client = $this->users->getAddressById($id);


        // Удаление адреса
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            if ($client) {
                if (($client['user_id'] != $user['id']) || ($client['login'] != userdata('login'))) {
                    set_userdata('msg', 'У Вас нет прав доступа к данному клиенту!');
                    redirect('/user/mypage/');
                }
            }

            $this->db->where('id', $id)->limit(1)->delete('addr');
            set_userdata("Адрес клиента " . $client['tel'] . ' (' . $client['name'] . ') успешно удалён!');
            redirect('/user/mypage/');
        }

        // Добавление или сохранение адреса
        if (isset($_POST['action'])) {

            $action = post('action');

            $country_id = $this->shop->getCountryByName(post('country'));
            if (!$country_id) $country_id = 0;
            else $country_id = $country_id['id'];

            $dbins = array(
                'name' => post('name'),
                'tel' => post('tel'),
                'country' => post('country'),
                'country_id' => $country_id,
                'city' => post('city'),
                'np' => post('np'),
                'adress' => post('adress'),
                'login' => userdata('login'),
                'user_id' => $user['id']
            );
            $msg = '';
            if ($action == 'add') {
                $this->db->insert('addr', $dbins);
                $msg = "Клиент успешно добавлен!";
            } elseif ($action == 'edit') {
                $this->db->where('id', $id)->limit(1)->update('addr', $dbins);
                $msg = "Клиент успешно сохранён!";
            }
            set_userdata('msg', $msg);

            redirect('/user/mypage/');
        }

        $action = 'add';
        $title = "Добавление нового клиента";
        if ($id !== false) {
            $action = 'edit';
            $title = 'Редактирование клиента: ' . $client['tel'] . ' (' . $client['name'] . ')';
        }
        if (isset($_GET['action'])) $action = $_GET['action'];

        if ($client) {
            if (($client['user_id'] != $user['id']) || ($client['login'] != userdata('login'))) {
                set_userdata('msg', 'У Вас нет прав доступа к данному клиенту!');
                redirect('/user/mypage/');
            }
        }

        $data['client'] = $client;
        $data['action'] = $action;

        $data['countries'] = $this->shop->getCountries();
        $country = 'Украина';
        if ($client) {
            $country = $this->shop->getCountryById($client['country']);
            if ($country) $country = $country['name'];
        }
        $data['country'] = $country;

        $data['user'] = $user;
        $data['title'] = $title . $this->model_options->getOption('global_title');
        $data['keywords'] = $title . $this->model_options->getOption('global_keywords');
        $data['description'] = $title . $this->model_options->getOption('global_description');
        $data['robots'] = "noindex, nofollow";
        $data['h1'] = $title;
        $data['seo'] = "";

        $this->load->view('users/dropship_client_adress.tpl.php', $data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */