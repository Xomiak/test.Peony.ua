<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Orders extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_categories', 'categories');
    }


    public function index()
    {
        $msg = '';
        if(isset($_GET['get_from_prom'])){
            $this->load->helper('prom_helper');
            $result = prom_getOrders();
            if($result) {
                $msg = 'Найдены новые заказы!';
            }
            loadHelper('order');
            createNewOrdersFiles();
        }

        if (isset($_POST['set_status']) && isset($_POST['list'])) {
            $list = $_POST['list'];
            $new_status = $_POST['status'];
            if (is_array($list)) {
                foreach ($list as $item) {
                    if (post('mail_to_client') == true) {
                        $order = $this->shop->getOrderById($item);
                        $user = $this->users->getUserById($order['user_id']);
                        $status_text = getStatus(post('status'));
                        $order_url = 'http://' . $_SERVER['SERVER_NAME'] . '/user/order-details/' . $order['id'] . '/?hash=' . $user['pass'];
                        $message = $user['name'] . ', спешим Вам сообщить, что статус Вашего заказа №' . $order['id'] . ' изменился на "' . $status_text . '"!<br />';
                        if ($user['network'] != NULL) {
                            $message .= 'Более детальную информацию о Вашем заказе Вы можете получить в личном кабинете: <a href="' . $order_url . '">' . $order_url . '</a>';
                        }
                        if (post('status') == 'sended')
                            $message .= '<br />Также, просим Вас оставить отзыв и оценить заказанные Вами товары!<br />';
                        $message .= '<br /><br />Также, предлагаем ознакомиться с последними нашими новинками.';
                        $subject = "Заказ №" . $order['id'] . ' изменил статус на "' . $status_text . '"';

                        $articles = $this->shop->getLastArticles(8);

                        $this->load->library('Emails');
                        $emails = new Emails();
                        $emails->setSubject($subject);
                        $emails->setArticles($articles);
                        $emails->setContent($message);
                        $emails->send($user['email']);
                    }
                    $this->db->where('id', $item)->limit(1)->update('orders', array('status' => post('status')));
                }
            }
            //vdd($list);

        }


        $status = -1;

        if (isset($_POST['status'])) {
            $status = $_POST['status'];
            set_userdata('order_status', $status);
        } else {
            if (userdata('order_status') !== null) $status = userdata('order_status');
        }

        $count = $this->shop->getOrdersCount(-1, -1, $status);
        // ПАГИНАЦИЯ //
        $this->load->library('pagination');
        $per_page = 35;
        $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/orders/';
        $config['total_rows'] = $count;
        $config['num_links'] = 4;
        $config['first_link'] = 'в начало';
        $config['last_link'] = 'в конец';
        $config['next_link'] = 'далее';
        $config['prev_link'] = 'назад';

        $config['per_page'] = $per_page;
        $config['uri_segment'] = 3;
        $from = intval($this->uri->segment(3));
        $page_number = $from / $per_page + 1;
        $this->pagination->initialize($config);
        $data['pager'] = $this->pagination->create_links();

        if ($page_number > 1) $this->session->set_userdata('ordersFrom', $from);
        else $this->session->unset_userdata('ordersFrom');
        //////////
        $data['msg'] = $msg;
        $data['pages'] = $this->shop->getOrders($per_page, $from, $status);
        $data['status'] = $status;
        $data['title'] = "Заказы";
        $this->load->view('admin/orders/orders.tpl.php', $data);
    }


    public function edit($id)
    {
        $err = false;
        //var_dump($_POST);die();
        $order = $this->shop->getOrderById($id);
        $user = $this->users->getUserById($order['user_id']);

        if(isset($_POST)){
            loadHelper('admin');
            $dbins = array(
                'adress' => $_POST['adress'],
                'status' => $_POST['status'],
                'status_adding' => post('status_adding'),
                'ttn' => $_POST['ttn'],
                'country'   => post('country'),
                'city'   => post('city'),
                'np'   => post('np'),
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('orders', $dbins);

            if($user['tel'] == '') {
                setValueInUser($user['id'], 'tel', post('tel'));
                vdd('set tel');
            }
            if($user['country'] == '') {
                setValueInUser($user['id'], 'country', post('country'));
                vdd('set country');
            }
            if($user['city'] == '') {
                setValueInUser($user['id'], 'city', post('city'));
                vdd('set city');
            }


            $ttn = trim(post('ttn'));

            // Отправка ТТН SMS сообщением
            if (isset($_POST['send_sms']) && $_POST['send_sms'] == true && isset($_POST['tel']) && $_POST['tel'] != '') {

                $this->load->helper('sms_helper');

                if ($ttn != '') {
                    $sms_ttnTemplate = getOption('sms_ttnTemplate');
                    $sms_ttnTemplate = str_replace('[order_id]', $order['id'], $sms_ttnTemplate);
                    $sms_ttnTemplate = str_replace('[ttn]', $ttn, $sms_ttnTemplate);
                    $result = sms_send($_POST['tel'], $sms_ttnTemplate);
                    $dbins = array(
                        'ttn_sms_sended' => 1,
                        'ttn_sms_result' => json_encode($result)
                    );
                    $this->db->where('id', $order['id'])->limit(1)->update('orders', $dbins);
                } else {
                    $err['sendSmsTtn'] = 'Вы не указали номер накладной!';
                }

            }

            // Отправка суммы оплаты SMS сообщением
            if (isset($_POST['send_sum_sms']) && $_POST['send_sum_sms'] == true && isset($_POST['tel']) && $_POST['tel'] != '' && isset($_POST['sms_mesage']) && $_POST['sms_mesage'] != '') {
                $this->load->helper('sms_helper');
                $result = sms_send($_POST['tel'], post('sms_mesage'));
                $dbins = array(
                    'sum_sms_sended' => 1,
                    'sum_sms_result' => json_encode($result)
                );
                $this->db->where('id', $order['id'])->limit(1)->update('orders', $dbins);
            }

            // Отправляем уведомление о смене статуса
            if ($dbins['status'] != $order['status']) {
                $status_text = getStatus(post('status'));
                $order_url = 'http://' . $_SERVER['SERVER_NAME'] . '/user/order-details/' . $order['id'] . '/';
                $message = $user['name'] . ', спешим Вам сообщить, что статус Вашего заказа №' . $order['id'] . ' изменился на "' . $status_text . '"!<br />';
                if ($ttn != '') {
                    $message .= 'ТТН: ' . $ttn . '<br />';
                    if ($user['network'] != NULL) {
                        $message .= 'Более детальную информацию о Вашем заказе Вы можете получить в личном кабинете: <a href="' . $order_url . '">' . $order_url . '</a>';
                    }
                    if ($dbins['status'] == 'sended')
                        $message .= '<br />Также, просим Вас оставить отзыв и оценить заказанные Вами товары!<br />';
                    $message .= '<br /><br />Также, предлагаем ознакомиться с последними нашими новинками.';
                    $subject = "Заказ №" . $order['id'] . ' изменил статус на "' . $status_text . '"';

                    $articles = $this->shop->getLastArticles(8);

                    $this->load->library('Emails');
                    $emails = new Emails();
                    $emails->setSubject($subject);
                    $emails->setArticles($articles);
                    $emails->setContent($message);
                    $emails->send($user['email']);

                }
            }
            if (isset($_POST['save_and_stay']))
                redirect($_SERVER['REQUEST_URI']);
            else
                redirect("/admin/orders/");
        }
   

        if ($order['viewed'] == 0) {
            $dbins = array(
                'viewed' => 1,
                'status' => 'processing'
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('orders', $dbins);
        }
        $data['order'] = $order;
        $data['user'] = $user;

        $data['title'] = "Редактирование заказа";
        $data['err'] = $err;
        $this->load->view('admin/orders_edit', $data);
    }

    public function edit2($id)
    {
        $err = false;
        //var_dump($_POST);die();
        $order = $this->shop->getOrderById($id);
        $user = $this->users->getUserById($order['user_id']);

        // Проверяем, если файл торгсофта не был создан по причине того, что небыло данных клиента
        // и если данные клиента уже заполнены, то меняем статус и создаём файл для торгсофта
        if($order['torgsoft_file'] == -1 && $user['name'] != 'n/a' && $user['lastname'] != 'n/a'){
            $writed = writeOrderFile($order);
            if ($writed)
                $this->db->where('id', $order['id'])->limit(1)->update('orders', array('torgsoft_file' => 1));
        }

        if (isset($_POST['status'])) {
            loadHelper('admin');

            $dropship_id = 0;
            if(post('addr_id') != 0 && isset($_POST['is_dropship']))
                $dropship_id = post('addr_id');

            $dbins = array(
                'adress' => $_POST['adress'],
                'status' => $_POST['status'],
                'status_adding' => post('status_adding'),
                'ttn' => $_POST['ttn'],
                'country'   => post('country'),
                'city'   => post('city'),
                'np'   => post('np'),
                'dropship_id' => $dropship_id
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('orders', $dbins);



            if($user['tel'] == '')
                setValueInUser($user['id'], 'tel', post('tel'));
            if($user['country'] == '')
                setValueInUser($user['id'], 'country', post('country'));
            if($user['city'] == '')
                setValueInUser($user['id'], 'city', post('city'));



            $ttn = trim(post('ttn'));

            // Отправка ТТН SMS сообщением
            if (isset($_POST['send_sms']) && $_POST['send_sms'] == true && isset($_POST['tel']) && $_POST['tel'] != '') {

                $this->load->helper('sms_helper');

                if ($ttn != '') {
                    $sms_ttnTemplate = getOption('sms_ttnTemplate');
                    $sms_ttnTemplate = str_replace('[order_id]', $order['id'], $sms_ttnTemplate);
                    $sms_ttnTemplate = str_replace('[ttn]', $ttn, $sms_ttnTemplate);
                    $result = sms_send($_POST['tel'], $sms_ttnTemplate);
                    $dbins = array(
                        'ttn_sms_sended' => 1,
                        'ttn_sms_result' => json_encode($result)
                    );
                    $this->db->where('id', $order['id'])->limit(1)->update('orders', $dbins);
                } else {
                    $err['sendSmsTtn'] = 'Вы не указали номер накладной!';
                }

            }

            // Отправка суммы оплаты SMS сообщением
            if (isset($_POST['send_sum_sms']) && $_POST['send_sum_sms'] == true && isset($_POST['tel']) && $_POST['tel'] != '' && isset($_POST['sms_mesage']) && $_POST['sms_mesage'] != '') {
                $this->load->helper('sms_helper');
                $result = sms_send($_POST['tel'], post('sms_mesage'));
                $dbins = array(
                    'sum_sms_sended' => 1,
                    'sum_sms_result' => json_encode($result)
                );
                $this->db->where('id', $order['id'])->limit(1)->update('orders', $dbins);
            }

            // Отправляем уведомление о смене статуса
            if ($dbins['status'] != $order['status']) {
                $status_text = getStatus(post('status'));
                $order_url = 'http://' . $_SERVER['SERVER_NAME'] . '/user/order-details/' . $order['id'] . '/';
                $message = $user['name'] . ', спешим Вам сообщить, что статус Вашего заказа №' . $order['id'] . ' изменился на "' . $status_text . '"!<br />';
                if ($ttn != '') {
                    $message .= 'ТТН: ' . $ttn . '<br />';
                    if ($user['network'] != NULL) {
                        $message .= 'Более детальную информацию о Вашем заказе Вы можете получить в личном кабинете: <a href="' . $order_url . '">' . $order_url . '</a>';
                    }
                    if ($dbins['status'] == 'sended')
                        $message .= '<br />Также, просим Вас оставить отзыв и оценить заказанные Вами товары!<br />';
                    $message .= '<br /><br />Также, предлагаем ознакомиться с последними нашими новинками.';
                    $subject = "Заказ №" . $order['id'] . ' изменил статус на "' . $status_text . '"';

                    $articles = $this->shop->getLastArticles(8);

                    $this->load->library('Emails');
                    $emails = new Emails();
                    $emails->setSubject($subject);
                    $emails->setArticles($articles);
                    $emails->setContent($message);
                    $emails->send($user['email']);

                }
            }
            if (isset($_POST['save_and_stay']))
                redirect($_SERVER['REQUEST_URI']);
            else
                redirect("/admin/orders/");
        }


        if ($order['viewed'] == 0) {
            $dbins = array(
                'viewed' => 1
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('orders', $dbins);
        }
        $data['order'] = $order;
        $data['user'] = $user;

        $data['title'] = "Редактирование заказа";
        $data['err'] = $err;
        $this->load->view('admin/orders/orders_edit', $data);
    }

    public function popup($id){
        $err = false;
        //var_dump($_POST);die();
        $order = $this->shop->getOrderById($id);
        $user = $this->users->getUserById($order['user_id']);
        $addr = false;
        if($order['addr_id'] != 0)
            $addr = $this->users->getAddressById($order['addr_id']);
        $data['order'] = $order;
        $data['user'] = $user;
        $data['addr'] = $addr;

        $data['title'] = "POPUP Редактирования заказа";
        $data['err'] = $err;
        $data['orders_edit_block'] = $this->orders_edit_block($id);
        $data['user_details'] = $this->user_details($user);
        $data['delivery_adress'] = $this->delivery_adress($order, $user);
        //echo $data['orders_edit_block'];die();
        $this->load->view('admin/orders/popup.tpl.php', $data);
    }

    private function orders_edit_block($id){
        $err = false;
        //var_dump($_POST);die();
        $order = $this->shop->getOrderById($id);
        $user = $this->users->getUserById($order['user_id']);
        $data['order'] = $order;
        $data['user'] = $user;

        $data['title'] = "POPUP Редактирования заказа";
        $data['err'] = $err;
        return $this->load->view('admin/orders/orders_edit_block.php', $data, true);
    }

    private function user_details($user){
        $data['user'] = $user;
        return $this->load->view('admin/orders/user_details.php', $data, true);
    }

    private function delivery_adress($order, $user){
        $data['user'] = $user;
        $data['order'] = $order;
        $addr = false;
        if($order['addr_id'] != 0)
            $addr = $this->users->getAddressById($order['addr_id']);
        $data['addr'] = $addr;
        return $this->load->view('admin/orders/delivery_adress.php', $data, true);
    }

    public function up($id)
    {
        $cat = $this->mp->getPageById($id);
        if (($cat) && $cat['num'] > 0) {
            $num = $cat['num'] - 1;
            $oldcat = $this->mp->getPageByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('pages', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num + 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('pages', $dbins);
            }
        }
        redirect('/admin/pages/');
    }

    public function down($id)
    {
        $cat = $this->mp->getPageById($id);
        if (($cat) && $cat['num'] < ($this->mp->getNewNum() - 1)) {
            $num = $cat['num'] + 1;
            $oldcat = $this->mp->getPageByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('pages', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num - 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('pages', $dbins);
            }
        }
        redirect('/admin/pages/');
    }

    public function del($id)
    {
        $this->db->where('id', $id)->limit(1)->delete('orders');
        redirect("/admin/orders/");
    }

    public function active($id)
    {
        $this->ma->setActive($id, 'pages');
        redirect('/admin/pages/');
    }

    function upload_foto()
    {                                // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/fotos';
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
            $ret = $this->upload->data();
            return $ret;
        }
    }

    function upload_file($page_id)
    {                                // Функция загрузки и обработки фото

        if (!file_exists('upload/files/' . $page_id))
            mkdir('upload/files/' . $page_id, 0777);
        $config['upload_path'] = 'upload/files/' . $page_id;
        $config['allowed_types'] = 'jpg|png|gif|jpe|zip|rar|doc|docx|xls|xlsx';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = false;
        $config['overwrite'] = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();
            return $ret;
        }
    }

}