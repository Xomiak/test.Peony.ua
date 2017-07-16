<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Shop extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->driver('cache');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_coupons', 'coupons');

        preloader();
        isLogin();
    }

    public function complete_my_cart($returned = false)
    {
//        if(isDebug()){
//            createNewOrder(array('id'=> 1));
//        }
        //if(!isset($_POST['currency'])) $_POST['currency'] = userdata('currency');
        $completed = false;
        $payStep = false;
        $my_cart = array();
        $shop_opt_from = getOption('shop_opt_from');
        if(userdata('country') !== false && userdata('country') != 1)
            $shop_opt_from = getOption('shop_opt_from_other');
        if (userdata('opt_from') !== false) $shop_opt_from = userdata('opt_from');
        $currency = getCurrency();
        $user = false;
        $user_type = $this->users->getUserTypeById(1);
        if ($this->session->userdata('my_cart') !== false) $my_cart = $this->session->userdata('my_cart');
        $count = count($my_cart);

        if (isset($_POST['resumm'])) {
            $coupon = false;
            // Получаеам  данные о купонах
            $coupon = post('coupon');

            if ($coupon) {
                //vdd(post('coupon'));
                $coupon = $this->coupons->getByCode($coupon);
                if ($coupon) {
                    $coupon = couponCheck($coupon);
                    if (!isset($coupon['err'])) {
                        set_userdata('coupon', $coupon);
                    } else {
                        set_userdata('msg', $coupon['err']);
                    }
                    //vdd($coupon);
                } else {
                    //vdd("zxc");
                    unset_userdata('coupon');
                    set_userdata('msg', 'Введён не действительный код купона!');
                }
            }
            // ** //

            //$my_cart['coupon'] = $coupon;
            for ($i = 0; $i < $count; $i++) {
                $mc = $my_cart[$i];
                $shop = $this->shop->getArticleById($mc['shop_id']);
                $razmer = explode('*', $shop['razmer']);
                $rcount = count($razmer);
                for ($i2 = 0; $i2 < $rcount; $i2++) {
                    if (isset($_POST['kolvo_' . $razmer[$i2] . '_' . $mc['shop_id']])) {
                        $my_cart[$i]['kolvo_' . $razmer[$i2]] = $_POST['kolvo_' . $razmer[$i2] . '_' . $mc['shop_id']];
                    }
                }
            }

            $this->session->set_userdata('my_cart', $my_cart);
            //redirect($_SERVER['REQUEST_URI']);
        }
        //var_dump($my_cart);
        if ($this->session->userdata('login') !== false) {
            $user = $this->users->getUserByLogin($this->session->userdata('login'));
        }
        // ОФОРМЛЕНИЕ
        if (isset($_POST['action']) && $_POST['action'] == 'order') {

            loadHelper('order');
            //if(isDebug()){
            //  echo 'starting order...';
            $order = createNewOrder();
            if (isset($order['id'])) {    // Перенаправляем на страницу оплаты...

                if ($order['payment'] == 'liqpay')
                    redirect('/payment/liqpay/' . $order['id'] . '/');
                elseif ($order['payment'] == 'interkassa')
                    redirect('/payment/interkassa/' . $order['id'] . '/');
                else
                    redirect('/payment/to_cart/' . $order['id'] . '/');

            } else {
                redirect('/order/error/');
            }
        } else {

            if (isset($_GET['del_shop_id'])) {
                $del = $this->shop->getArticleById($_GET['shop_id']);
                $cat = $this->model_categories->getCategoryById($del['category_id']);
                set_userdata('action_del', $_GET['shop_id']);
                set_userdata('action_del_name', $del['name']);
                set_userdata('action_del_category', $cat['name']);

                $newarr = array();
                for ($i = 0; $i < $count; $i++) {
                    if ($my_cart[$i]['shop_id'] != $_GET['del_shop_id']) {
                        array_push($newarr, $my_cart[$i]);
                    } elseif (isset($_GET['razmer'])) {
                        unset($my_cart[$i]['kolvo_' . $_GET['razmer']]);
                        array_push($newarr, $my_cart[$i]);
                    }
                }
                $my_cart = $newarr;
                $this->session->set_userdata('my_cart', $my_cart);
                redirect('/my_cart/');
            }
        }

        if ($returned)
            return $my_cart;
        else {
            $data['user'] = $user;
            $data['user_type'] = $this->users->getUserTypeById($user['user_type_id']);
            $data['my_cart'] = $my_cart;
            $data['shop_opt_from'] = $shop_opt_from;

            $this->load->view('shop/my_cart_complete.tpl.php', $data);
        }

    }

    public function my_cart()
    {
//        if(isset($_GET['new_table'])){
//            createOrderEmail($_GET['new_table'], true);
//            die();
//        }
        if (isset($_GET['coupon_cancel'])) {
            unset_userdata('coupon');
            redirect('/my_cart/');
        }

        $my_cart = $this->complete_my_cart(true);
        $user = false;
        $addrArr = false;
        $user_type = false;
        if ($this->session->userdata('login') !== false) {
            $user = $this->users->getUserByLogin($this->session->userdata('login'));
            $user_type = $this->users->getUserTypeById($user['user_type_id']);
            if($user)
                $addrArr = $this->shop->getAddrByLogin($user['login']);
        }
//        if(isDebug()){
//            vd($user_type);
//        }

        if (userdata('type') == 11) {
//            vd("DROP");
            $data['dpopship'] = true;
            $data['addresses'] = $this->users->getAddressesByUser(userdata('login'));
        }
        $data['shop_opt_from'] = getOption('shop_opt_from');
        //var_dump($user_type);
        if(!$user_type)
            $user_type = $this->users->getUserTypeById(1);
        if (userdata('opt_from') !== false) $data['shop_opt_from'] = userdata('opt_from');
        $data['user'] = $user;
        $data['user_type'] = $user_type;
        $data['my_cart'] = $my_cart;

        if (userdata('payment') == 'Интеркасса') {                                                           // interkassa
            $data['order'] = $this->shop->getOrderById(userdata('order_id'));
            $data['user'] = $this->users->getUserByLogin(userdata('login'));
            unset_userdata('order_id');
            unset_userdata('payment');
            $data['title'] = 'Оплата через Интеркассу' . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('shop/interkassa.tpl.php', $data);
        } elseif (userdata('payment') == 'liqpay') {                                                         // LiqPay
            $order = $this->shop->getOrderById(userdata('order_id'));
            $data['order'] = $order;
            $data['user'] = $this->users->getUserByLogin(userdata('login'));

            unset_userdata('order_id');
            unset_userdata('payment');
            unset_userdata('npnp');
            redirect('/payment/liqpay/' . $order['id'] . '/');
            $data['title'] = 'Оплата через LiqPay' . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('shop/liqpay.tpl.php', $data);
        } /*
        elseif (userdata('payment') == 'Перевод на карту Приват Банка') {                                                         // Перевод на карту Приват Банка
            $data['order'] = $this->shop->getOrderById(userdata('order_id'));
            $data['user'] = $this->users->getUserByLogin(userdata('login'));
            unset_userdata('order_id');
            unset_userdata('payment');
            $data['name'] = 'Перевод на карту Приват Банка';
            $data['msg'] = getOption('payment_privat_card');
            $data['title'] = $data['name'] . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('shop/msg.tpl.php', $data);
        } elseif (userdata('payment') == 'Наложенный платёж') {                                                         // Наложенный платёж
            $data['order'] = $this->shop->getOrderById(userdata('order_id'));
            $data['user'] = $this->users->getUserByLogin(userdata('login'));
            unset_userdata('order_id');
            unset_userdata('payment');
            $data['name'] = 'Наложенный платёж';
            $data['msg'] = getOption('payment_nalogenniy');
            $data['title'] = $data['name'] . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('shop/msg.tpl.php', $data);
        }
        */
        elseif (userdata('order_completed') == true) {
            unset_userdata('order_completed');
            if (userdata('payment') == 'Перевод на карту Приват Банка') {
                if (userdata('payment') == 'Перевод на карту Приват Банка') {                                                         // Перевод на карту Приват Банка
                    $data['order'] = $this->shop->getOrderById(userdata('order_id'));
                    $data['user'] = $this->users->getUserByLogin(userdata('login'));
                    unset_userdata('order_id');
                    unset_userdata('payment');
                    $data['name'] = 'Перевод на карту Приват Банка';
                    $data['msg'] = getOption('payment_privat_card');
                    $data['title'] = $data['name'] . $this->model_options->getOption('global_title');
                } elseif (userdata('payment') == 'Наложенный платёж') {                                                                       // Наложенный платёж
                    $data['order'] = $this->shop->getOrderById(userdata('order_id'));
                    $data['user'] = $this->users->getUserByLogin(userdata('login'));
                    unset_userdata('order_id');
                    unset_userdata('payment');
                    $data['name'] = 'Наложенный платёж';
                    $data['msg'] = getOption('payment_nalogenniy');
                    $data['title'] = $data['name'] . $this->model_options->getOption('global_title');
                }
            } else {
                $data['order'] = $this->shop->getOrderById(userdata('order_id'));
                $data['user'] = $this->users->getUserByLogin(userdata('login'));
                unset_userdata('order_id');
                unset_userdata('payment');
                $data['name'] = 'Заказ оформлен успешно!';
                if($data['order']['one_click'] == 1)
                    $msg = getOption('shop_sended_one_click');
                else
                    $msg = getOption('shop_sended');
                $data['msg'] = $msg;
                $data['title'] = 'Заказ успешно оформлен' . $this->model_options->getOption('global_title');
            }
            $this->session->unset_userdata('my_cart');

            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('shop/msg.tpl.php', $data);
        } else {
            $data['addrArr'] = $addrArr;
            $data['title'] = 'Моя корзина' . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $data['countries'] = $this->shop->getCountries();
            $data['deliveries'] = $this->shop->getDeliveries();
            if(isset($_GET['block_only']) && $_GET['block_only'] == 'form_new_addr')
                $this->load->view('shop/new_addr.php', $data);
            else {
               //if (count($addrArr) > 0)
//                $this->load->view('shop/new_my_cart.tpl.php', $data);
//                else
                    $this->load->view('shop/my_cart.tpl.php', $data);
            }
        }
    }

    public function paymentToCart($order_id){
        $order = $this->shop->getOrderById($order_id);
        if($order) {
            $data['h1'] = $data['description'] = $data['keywords'] = $data['name'] = 'Заказ оформлен успешно!';
            $data['robots'] = 'noindex, nofollow';
            $msg = getOption('shop_pay_to_cart');
            $data['msg'] = $msg;
            $data['title'] = 'Заказ успешно оформлен' . $this->model_options->getOption('global_title');
            $this->load->view('msg.tpl.php', $data);
        } else err404();
    }
    public function paymentOther($order_id){
        $order = $this->shop->getOrderById($order_id);
        if($order) {
            $data['h1'] = $data['description'] = $data['keywords'] = $data['name'] = 'Заказ оформлен успешно!';
            $data['robots'] = 'noindex, nofollow';
            if ($data['order']['one_click'] == 1)
                $msg = getOption('shop_sended_one_click');
            else
                $msg = getOption('shop_sended');
            $data['msg'] = $msg;
            $data['title'] = 'Заказ успешно оформлен' . $this->model_options->getOption('global_title');
            $this->load->view('msg.tpl.php', $data);
        } else err404();
    }

    public function orderError(){
        $name = 'Ошибка заказа!';
        $msg = 'Заказ не найден! Возможно, он уже был оформлен ранее. Проверьте Ваши заказы в <a href="/user/mypage/">личном кабинете</a>, либо свяжитесь с нашим менеджером!';

        $data['h1'] = $data['description'] = $data['keywords'] = $data['name'] = $name;
        $data['robots'] = 'noindex, nofollow';

        $data['msg'] = $msg;
        $data['title'] = $name . $this->model_options->getOption('global_title');
        $this->load->view('msg.tpl.php', $data);
    }

    public function order()
    {
        $user = false;
        if ($this->session->userdata('login') !== false) {
            $user = $this->users->getUserByLogin($this->session->userdata('login'));
        }

        ///var_dump($_POST);die();

        if (isset($_POST['name'])) {
            if ($user) {

            } else {

            }
        }

        $my_cart = array();
        if ($this->session->userdata('my_cart') !== false) $my_cart = $this->session->userdata('my_cart');

        $data['my_cart'] = $my_cart;
        $data['title'] = 'Оформление заказа' . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/order.tpl.php', $data);
    }

    function set_brand($id)
    {
        $this->session->set_userdata('f_brand_id', $id);
        $back = $this->session->userdata('last_url');
        redirect($back);
    }

    function unset_brand()
    {
        $this->session->unset_userdata('f_brand_id');
        $back = $this->session->userdata('last_url');
        redirect($back);
    }

    function set_filter()
    {
        if (isset($_POST['type'])) {
            if ($_POST['type'] == 'max_price') {
                $_POST['max_price'] = str_replace(' UAH', '', $_POST['max_price']);
                $this->session->set_userdata('f_price_max', $_POST['max_price']);
                $back = $this->session->userdata('last_url');
                redirect($back);
            }
        }
    }

    function add_to_cart()
    {
        if (isset($_POST['shop_id'])) {
            //var_dump($_POST);die();
            if (!isset($_POST['kolvo'])) $_POST['kolvo'] = 1;
            $my_cart = array();
            if ($this->session->userdata('my_cart') !== false) $my_cart = $this->session->userdata('my_cart');

            $shop = $this->shop->getArticleById($_POST['shop_id']);
            //$razmer = json_decode($shop['warehouse']);
            $razmer = explode('*', $shop['razmer']);
            $is_new = true;
            $count = count($my_cart);
            for ($i = 0; $i < $count; $i++) {
                if ($my_cart[$i]['shop_id'] == $_POST['shop_id']) {
                    $rcount = count($razmer);

                    if (isset($my_cart[$i]['kolvo_' . $_POST['razmer']])) {
                        $my_cart[$i]['kolvo_' . $_POST['razmer']] += $_POST['kolvo'];
                    } else {
                        $my_cart[$i]['kolvo_' . $_POST['razmer']] = $_POST['kolvo'];
                    }


                    $kolvo = 0;
                    for ($i2 = 0; $i2 < $rcount; $i2++) {
                        //vd($razmer[$i2]);
                        if (isset($_POST['kolvo_' . $razmer[$i2]])) {
                            //$my_cart[$i]['kolvo_'.$razmer[$i2]] = $_POST['kolvo_'.$razmer[$i2]];
                            $kolvo = $kolvo + $_POST['kolvo_' . $razmer[$i2]];
                        }
                    }

                    $my_cart[$i]['kolvo'] = $kolvo;
                    $is_new = false;
                }
            }

            if ($is_new) {
                $new = array(
                    'shop_id' => $_POST['shop_id']
                );

                $razmer = json_decode($shop['warehouse']);
                $rcount = count($razmer);

                $kolvo = 0;

                $new['kolvo_' . $_POST['razmer']] = $_POST['kolvo'];
                $new['kolvo'] = $_POST['kolvo'];

                array_push($my_cart, $new);
            }

            $cat = $this->model_categories->getCategoryById($shop['category_id']);
            set_userdata('action_add_to_cart', $_POST['shop_id']);
            set_userdata('action_add_to_cart_name', $shop['name']);
            set_userdata('action_add_to_cart_price', $shop['price']);
            set_userdata('action_add_to_cart_category', $cat['name']);
            set_userdata('action_add_to_cart_razmer', $_POST['razmer']);
            set_userdata('action_add_to_cart_kolvo', $_POST['kolvo']);

            $this->session->set_userdata('my_cart', $my_cart);

            $my_cart = $this->session->userdata('my_cart');
        }

        redirect($_POST['back']);
    }

    public function sended($order_id = false)
    {
        //vd(userdata('adwords'));
        $this->session->unset_userdata('my_cart');

        if($order_id){
            $model = getModel('shop');
            $order = $model->getOrderById($order_id);
            if($order) {
                $data['order'] = $order;
                $data['h1'] = 'Заказ №'.$order_id.' оформлен успешно!';
                if($order['one_click'] == 1) $data['msg'] = 'В ближайшее время с Вами свяжется наш менеджер для уточнения деталей заказа';
                else $data['msg'] = getOption('shop_sended');
            } else {
                $data['h1'] = 'Заказ не найден...(';
                $data['msg'] = 'Что-то пошло не так... Свяжитесь с нашим оператором для выяснения проблемы...';
            }
        }

        $data['title'] = 'Заказ успешно оформлен' . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/sended.tpl.php', $data);
    }

    function privat_payed($order_id)
    {
        if (isset($_POST['payment']) && isset($_POST['signature'])) {
            $privat24_merchant_pass = $this->model_options->getOption('privat24_merchant_pass');

            $payment = $_POST['payment'];
            $signature = $_POST['signature'];

            $checkSignature = sha1(md5($payment . $privat24_merchant_pass));

            if ($signature == $checkSignature) {

                // Ответ от настоящего сервера
                // Далее парсим $payment
                parse_str($payment, $data);

                if ($data['state'] == 'ok' || $data['state'] == 'test') {
                    $dbins = array(
                        'status' => 'payed',
                        'pay_answer' => serialize($data)
                    );
                    $this->db->where('id', $data['order']);
                    $this->db->limit(1);
                    $this->db->update('orders', $dbins);

                    $this->session->unset_userdata('my_cart');

                    //////////////////////////////vd($order_id);
                    $data['order'] = $this->shop->getOrderById($order_id);
                    $data['title'] = 'Оплата прошла успешно' . $this->model_options->getOption('global_title');
                    $data['keywords'] = "";
                    $data['description'] = "";
                    $data['robots'] = 'noindex, nofollow';
                    $this->load->view('shop/payed.tpl.php', $data);
                } else {
                    vd($order_id);
                    $data['order'] = $this->shop->getOrderById($order_id);
                    $data['title'] = 'Оплата не прошла ((' . $this->model_options->getOption('global_title');
                    $data['keywords'] = "";
                    $data['description'] = "";
                    $data['robots'] = 'noindex, nofollow';
                    $this->load->view('shop/not_payed.tpl.php', $data);
                }


            } else {

                // Фальшивый ответ
                echo "Уважаемые хакеры, идите лесом. С уважением, администрация.";
            }
        } else {
            //vd($order_id);
            $order = $this->shop->getOrderById($order_id);
            $data['order'] = $order;
            if ($order['status'] == 'payed') {
                $data['title'] = 'Оплата прошла успешно' . $this->model_options->getOption('global_title');
                $data['keywords'] = "";
                $data['description'] = "";
                $data['robots'] = 'noindex, nofollow';
                $this->load->view('shop/payed.tpl.php', $data);
            } else {
                $data['title'] = 'Оплата не прошла ((' . $this->model_options->getOption('global_title');
                $data['keywords'] = "";
                $data['description'] = "";
                $data['robots'] = 'noindex, nofollow';
                $this->load->view('shop/not_payed.tpl.php', $data);
            }
        }
    }

    function privat($order_id)
    {
        //$this->session->unset_userdata('my_cart');
        $data['order'] = $this->shop->getOrderById($order_id);
        $data['title'] = 'Оплата через Приват24' . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/privat.tpl.php', $data);
    }

    function walletone($order_id)
    {
        //$this->session->unset_userdata('my_cart');
        $data['order'] = $this->shop->getOrderById($order_id);
        $data['user'] = $this->users->getUserByLogin(userdata('login'));
        $data['title'] = 'Оплата через WalletOne' . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/walletone.tpl.php', $data);
    }

    function walletone_payed($order_id)
    {
        //$this->session->unset_userdata('my_cart');
        $data['order'] = $this->shop->getOrderById($order_id);
        $data['user'] = $this->users->getUserByLogin(userdata('login'));
        $data['title'] = 'Оплата через WalletOne' . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/walletone_payed.tpl.php', $data);
    }

    function interkassa($order_id)
    {
        //$this->session->unset_userdata('my_cart');
        $data['order'] = $this->shop->getOrderById($order_id);
        $data['user'] = $this->users->getUserByLogin(userdata('login'));
        $data['title'] = 'Оплата через Интеркассу' . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/interkassa.tpl.php', $data);
    }

    function interkassa_payed()
    {
        $otherStatus = false;
        $order = false;
        $status = 'new';
        $log = '__________________________________________________________________________'
            . "\r\n"
            . date("Y-m-d H:i")
            . ': Произведена оплата через Интеркассу'
            . "\r\n";
        // Сохраняем все полученные значения $_POST в файл логов
        ob_start();
        var_dump($_POST);
        $output = ob_get_clean();
        $log .= 'Вернувшиеся данные: '
            . "\r\n"
            . $output
            . "\r\n"
            . "\r\n";
//        vd($_POST['ik_pm_no']);
        if (isset($_POST['ik_pm_no']))
            $order = $this->shop->getOrderById($_POST['ik_pm_no']);

        elseif (isset($_GET['prder_id']))
            $order = $this->shop->getOrderById($_GET['prder_id']);

        //$this->session->unset_userdata('my_cart');
        if ($order) {
            $status = 'new';
            $ik_inv_st = post('ik_inv_st');
            if ($ik_inv_st == 'waitAccept') $status = 'waitAccept';
            if ($ik_inv_st == 'process') $status = 'process';
            if ($ik_inv_st == 'success') $status = 'payed';
            if ($ik_inv_st == 'canceled') $status = 'canceled';
            if ($ik_inv_st == ' fail') $status = ' fail';

            $log .= "Статус оплаты: " . $status . "\r\n";
            if ($order['status'] != $status) {
                setStatus($order['id'], $status);
                $otherStatus = true;
            }


            $data['order'] = $order;
            $data['user'] = $this->users->getUserByLogin(userdata('login'));

        } else $log .= "Интеркасса не передала данные!";
        $log .= "\r\n";

        if ($otherStatus == true && $status == 'payed') {     // Сообщаем об успешной оплате заказа
            $user = $this->users->getUserById($order['user_id']);
            $to = getOption('admin_email');
            $message = "Заказ №" . $order['id'] . ' успешно оплачен через систему Интеркасса!<br />
            <a href="http://' . $_SERVER['SERVER_NAME'] . '/admin/orders/edit/' . $order['id'] . '/">Перейти к заказу в админке</a>';
            mail_send($to, "Заказ №" . $order['id'] . " оплачен!", $message);              // отправляем админу

            $message = "Ваш заказ №" . $order['id'] . ' успешно оплачен через систему Интеркасса!<br />
            Всю информацию о данном заказе Вы можете посмотреть тут: <a href="http://' . $_SERVER['SERVER_NAME'] . '/user/order-details/' . $order['id'] . '/?hash=' . $user['pass'] . '">Заказ №' . $order['id'] . '</a><br />';

            $message .= 'Также, отслеживать все свои заказы Вы можете в своём <a href="http://' . $_SERVER['SERVER_NAME'] . '/user/mypage/">личном кабинете</a><br /><br />
            Благодарим Вас за сотрудничество и просим, получения заказа, оставить свои отзывы о заказанных Вами товарах. Авторов лучших отзывов ожидают приятные сюрпризы от производителя <a href="http://' . $_SERVER['SERVER_NAME'] . '/">PEONY</a>!';

            mail_send($user['email'], "Заказ №" . $order['id'] . " оплачен!", $message);              // отправляем клиенту
        }
        file_put_contents('./application/logs/interkassa_' . date("Y-m-d") . '.txt', $log, FILE_APPEND);
        $data['status'] = $status;
        $data['payed'] = true;
        $data['title'] = 'Оплата заказа №' . $order['id'] . $this->model_options->getOption('global_title');
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/interkassa.tpl.php', $data);
    }


    function liqpay_payed($order_id){
        $result = getLiqpayOrderStatus($order_id);
        //vdd($result);
        $order = $this->shop->getOrderById($order_id);
        $data['order'] = $order;
        $data['result'] = $result;

        $status = $result->status;

        if($status == 'success') {
            // Уведомляем админа об успешной оплате
            if($order['liqpay_admin_email'] == 0){
                $message = 'Оплата заказа №'.$order['id'].' прошла успешно!<br />';
                if($order['npnp'] == 0)
                    $message .= 'Клиент полностью оплатил заказ!';
                else $message .= 'Клиент оплатил предоплату наложенного платежа';
                $this->load->helper('mail_helper');
                mail_send(getOption('admin_email'),'Оплата через LiqPay заказа №'.$order['id'], $message);
                updateItem($order_id,'orders',array('liqpay_admin_email' => 1));
            }

            // устанавливаем верный статус заказа
            if($order['npnp'] == 0)
                $status = 'payed';
            else $status = 'npnp_payed';
        }



        // Обновляем статус платежа
        if(isset($result->status) && $status != $order['status'])
            setStatus($order['id'], $status);

        $order['status'] = $result->status;
        //vd($result);


        if ($order['status'] == 'payed' || $order['status'] == 'npnp_payed' || $order['status'] == 'success') {
            $data['msg'] = 'Оплата прошла успешно!<br />
    Ожидайте, с Вами свяжутся.';
            $data['h1'] = 'Оплата прошла успешно!';
            $data['title'] = 'Оплата прошла успешно №'.$order_id . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';

            $this->load->view('msg.tpl.php', $data);
        } elseif ($order['status'] == 'wait_accept' || $order['status'] == 'wait_secure') {
            $data['msg'] = 'Платёж в обработке<br />
    Ожидайте, с Вами свяжутся.';
            $data['h1'] = 'Платёж в обработке';
            $data['title'] = 'Платёж по заказу №'.$order_id . ' в обработке' . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('msg.tpl.php', $data);
        } elseif ($order['status'] == 'error' || $order['status'] == 'failure') {
            //vd($order['status']);
            $data['msg'] = 'Платёж в обработке<br />
    Ожидайте, с Вами свяжутся.';
            $data['msg'] = '<p>Платёж не произведён</p>';
            if(isset($result->err_code)) $data['msg'] .= '<p><b>Код ошибки: </b>'.$result->err_code.'</p>';
            if(isset($result->err_code)) $data['msg'] .= '<p><b>Описание ошибки: </b>'.$result->err_description.'</p>';
            $data['msg'] .= '<p>Попробуйте <a href="/payment/liqpay/'.$order_id.'/">Повторить платёж</a></p>';
            $data['msg'] .= '<p>Перейти к <a href="/user/order-details/'.$order_id.'/">деталям заказа</a></p>';
            $data['h1'] = 'Ошибка платежа';
            $data['title'] = 'Ошибка платежа заказа '.$order_id . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('msg.tpl.php', $data);
        } else {
            $data['msg'] = '<p>Платёж находится в статусе: '.getStatus($order['status']).'</p>';

            $data['msg'] .= '<p>Если у Вас возникли какие-либо вопросы, обратитесь к нашему консультанту</p>';
            $data['h1'] = 'Платёж находится в статусе: '.getStatus($order['status']);
            $data['title'] = 'Ошибка платежа заказа '.$order_id . $this->model_options->getOption('global_title');
            $data['keywords'] = "";
            $data['description'] = "";
            $data['robots'] = 'noindex, nofollow';
            $this->load->view('msg.tpl.php', $data);
        }
    }


    function liqpay($order_id = false)
    {
        $order = getOrderById($order_id);
        // Обрабатываем ответ от LiqPay:
        if (isset($_POST['data'])) {
            redirect('/payed/liqpay/'.$order_id.'/');
        }


        //vd($order);


        //vdd($public_key);
        $this->load->library('Liqpay');
        $public_key = getOption('liqpay_public_key');
        $private_key = getOption('liqpay_private_key');
        $lp = new Liqpay($public_key, $private_key);

        $npnp_price = getOption('npnp_price');
        $usd_full_price = $full_price = $order['full_summa'];

        $currency = mb_strtoupper($order['currency']);
        if (!$currency) $currency == 'USD';
        $currencyValue = getCurrencyValue($currency);

        if ($currencyValue != 0) {
            $full_price = $full_price * $currencyValue;
            $npnp_price = $npnp_price * $currencyValue;
        }

        $data['currency'] = $currency;

        $description = "Оплата заказа №" . $order_id . ' в магазине PEONY.ua';

        if ($order['npnp'] == 1 && !isset($_GET['pay_all_price'])) {
            $description = "Предоплата заказа наложенным платежом №" . $order_id . ' в магазине PEONY.ua';

            $full_price = round($npnp_price, 2);
        }

        //$paymentMethodValue = $full_price / 100 * 3;
        $paymentMethodValue = 0;
        $data['liqpay_value'] = round($paymentMethodValue, 2);
        $full_price = round($full_price + $paymentMethodValue, 2);


//vdd($full_price);


        $backUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $liqpay_order_id = $order_id . date("Hi");
        //echo 'LiqPay order id: ' . $liqpay_order_id;
        $params = array(
            'version' => 3,
            'currency' => $currency,
            'amount' => $full_price,
            'description' => $description,
            'action' => 'pay',
            'order_id' => $liqpay_order_id,
            'sandbox' => 0,                // 1 - тестовый платёж
            'server_url' => $backUrl,
            'result_url' => $backUrl,
            'customer' => userdata('login')
        );
        updateItem($order['id'], 'orders', array('liqpay_order_id' => $liqpay_order_id));
        $form = $lp->cnb_form($params);
        $data['liqpay_order_id'] = $liqpay_order_id;
        $data['form'] = $form;
        $data['full_price'] = $full_price;
        $data['usd_full_price'] = $usd_full_price;
        $data['currency_value'] =  $currencyValue;
        //$this->session->unset_userdata('my_cart');
        //vd($order_id);
        $data['order'] = $order;
        $data['title'] = $description;
        $data['keywords'] = "";
        $data['description'] = "";
        $data['robots'] = 'noindex, nofollow';
        $this->load->view('shop/liqpay.tpl.php', $data);
    }

    function getOneClickForm(){
        loadHelper('one_click');
        echo get_buy_one_click_form(false);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */