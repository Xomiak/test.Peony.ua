<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        //isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_pages', 'mp');
    }

    function get_order_products($order_id){
        loadHelper('admin');
        showOrderProducts($order_id);
    }

    function get_product_sizes($id){
        $model = getModel('shop');
        $product = $model->getArticleById($id);
        if(isset($product['razmer']))
            echo $product['razmer'];
    }

    function edit_order($order_id){
        $action = post('action');
        loadHelper('order');
        $my_cart = $size = $shop_id = $model = $order = false;


        //if($action) {                   // если передана команда действия
            $model = getModel('shop');
            $order = $model->getOrderById($order_id);
            $shop_id = post('shop_id');
            $size = post('size');
            $my_cart = json_decode($order['products_json'], true);
            $count = shop_count($my_cart);
            $productsCount = count($my_cart);
            $modelUsers = getModel('users');
            $user = $modelUsers->getUserById($order['user_id']);
            $nadbavka = 0;
            if($user) {
                $userType = getItemById($user['user_type_id'], 'user_types');
                if (!$userType)
                    $userType = getItemById(1, 'user_types');

                $nadbavka = $userType['nadbavka'];
            }
            $optFrom = 4;
            $deliveryPrice = 0;
            $country = false;

            if($order['country_id'] != 1 && $order['country_id'] != 0){             // Прибавляем доставку
                $country = getItemById($order['country_id'], 'countries');

                if(isset($country['opt_from'])) $optFrom = $country['opt_from'];

                if(isset($country['delivery_price']) && $country['delivery_price'] > 0) {
                    if($count > $country['bigopt_from']) $deliveryPrice = $country['bigopt_delivery_price'];
                    else
                        $deliveryPrice = $country['delivery_price'];
                }

            }



        //}

        if(($order) && $action == 'set_status') {                                    // меняем статус
            $value = post('value');
            if($value) {
                $ret = $this->db->where('id', $order['id'])->limit(1)->update('orders', array('status' => $value));
                if($ret)
                    echo 'Статус заказа №'.$order['id'].' успешно изменён на '.$value;
                else alert('Ошибка смены статуса 1!');
            } else
                alert('Ошибка смены статуса 2!');
        }
        elseif(($order) && $action == 'edit_addr') {                                    // редактируем адрес доставки
            $addr_id = post('addr_id');
            if($addr_id) {
                $model = getModel('shop');
                $addr = $model->getAddr($addr_id);
                if($addr){
                    $name = post('name');
                    $tel = post('tel');
                    $country = post('country');
                    $country_id = post('country_id');
                    $city = post('city');
                    $delivery = post('delivery');
                    $adress = post('adress');
                    $np = post('np');
                    $payment = post('payment');
                    $ttn = post('ttn');

                    $dbins = array();
                    if($addr['name'] != $name) $dbins['name'] = $name;
                    if($addr['tel'] != $tel) $dbins['tel'] = $tel;
                    if($addr['country'] != $country) {
                        $dbins['country'] = $country;
                        $country = getCountryByName($country);
                        if($country) $dbins['country_id'] = $country['id'];
                    }
                    if($addr['city'] != $city && $city != '') {
                        $dbins['city'] = $city;
                        $city = getCityByName($city, $country_id);
                        if(!$city && $country_id > 0)
                            $city = addCity($city, $country);
                        if($city) $dbins['city_id'] = $city['id'];
                    }

                    if($addr['adress'] != $adress) $dbins['adress'] = $adress;
                    if($addr['np'] != $np) $dbins['np'] = $np;


                    if(count($dbins) > 0){
                        $result = $this->db->where('id', $addr_id)->limit(1)->update('addr', $dbins);
                        if($result) echo 'Данные о доставке успешно сохранены!<br>';
                        else echo 'Ошибка изменения данных о доставке<br>';
                    } else echo 'Нет изменённых данных о доставке для изменения<br>';

                    /** Проверяем и сохраняем способы оплаты и доставки */
                    $orderArr = array();
                    if($order['payment'] != $payment) $orderArr['payment'] = $payment;
                    if($order['delivery'] != $delivery) $orderArr['delivery'] = $delivery;
                    if($order['ttn'] != $ttn) $orderArr['ttn'] = $ttn;
                    if(count($orderArr) > 0){
                        $result = $this->db->where('id', $order_id)->limit(1)->update('orders', $orderArr);
                        if($result) echo 'Данные о способах оплаты и доставки успешно сохранены!<br>';
                        else echo 'Ошибка изменения данных о способах оплаты и доставки<br>';
                    } else echo 'Нет изменённых данных о способах оплаты и доставки<br>';
                } else echo 'Адрес id '.$addr_id.' не найден!<br>';
            }else echo 'Не передан id адреса<br>';
        }
        elseif(($order) && $action == 'delete'){                                    // удаляем товар из заказа
            if($shop_id && $size){
                $newarr = array();
                $count = shop_count($my_cart);
                echo 'count = '.$count.'<br>';
                for ($i = 0; $i < $productsCount; $i++) {
                    if ($my_cart[$i]['shop_id'] != $shop_id) {
                        echo 'not this product<br>';
                        array_push($newarr, $my_cart[$i]);
                    } else {
                        echo 'this porduct!<br>';
                        //vdd($my_cart[$i]);
                        $productSizes = array();
                        foreach ($my_cart[$i]['sizesCounts'] as $razmer){
                            if(isset($razmer['size']) && $razmer['size'] != $size){
                                $productSizes[] = $razmer;
                            }
                        }
                        unset($my_cart[$i]['kolvo_'.$size]);
                        if(count($productSizes) > 0){
                            $my_cart[$i]['sizesCounts'] = $productSizes;
                            if(($key = array_search($size, $my_cart[$i]['sizes'])) !== false)
                                unset($my_cart[$i]['sizes'][$key]);
                            array_push($newarr, $my_cart[$i]);
                        }
                    }
                }
                $my_cart = $newarr;
                $count = shop_count($my_cart);

                ///////// Вычисляем и сохраняем данные заказа

                $full_summa = $summa = getProductsPrice($my_cart);
                if($deliveryPrice > 0)
                    $full_summa = $full_summa + ($deliveryPrice * $count);

                if(($nadbavka) && $nadbavka > 0){                                   // прибавляем надбавку
                    if($optFrom < $count){
                        $full_summa = $full_summa + $nadbavka;
                    }
                }

                $editedOrder = array(
                    'products'  => serialize($my_cart),
                    'products_json' => json_encode($my_cart),
                    'summa'     => $summa,
                    'full_summa' => $full_summa,
                    'delivery_price' => $deliveryPrice,
                    'products_count'    => $count,
                    'nadbavka'      => $nadbavka
                );
                $this->db->where('id',$order_id)->limit(1)->update('orders', $editedOrder);
                echo 'delete complete';
            }
        }
        elseif(($order) && $action == 'add'){                            // Добавляем товар в заказ
            $newProduct = true;
            $newSize = true;
            for($i = 0; $i < $productsCount; $i++){
                $mc = $my_cart[$i];
                if($mc['id'] == $shop_id){                  // Если этот товар уже есть в заказе
                    $newProduct = false;

                    $sizes = $mc['sizes'];
                    foreach ($sizes as $item){
                        if($size == $item){
                            $newSize = false;
                        }
                    }
                    if(!$newSize){                      // Если такой размер этого товара уже есть в заказе
                        for($is = 0; $is < count($mc['sizesCounts']); $is++){
                            if($mc['sizesCounts'][$is]['size'] == $size){
                                $my_cart[$i]['sizesCounts'][$is]['count'] = $my_cart[$i]['sizesCounts'][$is]['count'] + post('count');
                                $my_cart[$i]['count'] = $my_cart[$i]['count'] + post('count');
                                $my_cart[$i]['kolvo_'.$size] = $my_cart[$i]['sizesCounts'][$is]['count'];
                            }
                        }
                    } else{                     // Если такого размера этого товара нет в заказе
                        $my_cart[$i]['sizes'][] = $size;
                        $my_cart[$i]['sizesCounts'][] = array('size' => $size, 'count' => post('count'));
                        $my_cart[$i]['kolvo_'.$size] = post('count');
                        $my_cart[$i]['count'] = $my_cart[$i]['count'] + post('count');
                    }
                }
            }
            if($newProduct){                            // Если такого товара ещё нет в корзине
                $product = $model->getProductById($shop_id);
                if($product) {
                    $mc = array(
                        'shop_id'       => $shop_id,
                        'id'            => $shop_id,
                        'final_price'   => getNewPrice($product['price'], $product['discount']),
                        'discount'      => $product['discount'],
                        'sizes'         => array($size),
                        'sizesCounts'   => array(array('size' => $size, 'count' => post('count'))),
                        'kolvo_'.$size  => post('count'),
                        'count'         => post('count')
                    );
                    array_push($my_cart, $mc);
                } else echo 'Товар с ID: '.$shop_id.' не найден в базе!!!';
            }

            $count = shop_count($my_cart);

            $full_summa = $summa = getProductsPrice($my_cart);
            if($deliveryPrice > 0)
                $full_summa = $full_summa + ($deliveryPrice * $count);

            if(($nadbavka) && $nadbavka > 0){                                   // прибавляем надбавку
                if($optFrom < $count){
                    $full_summa = $full_summa + $nadbavka;
                }
            }

            $editedOrder = array(
                'products'  => serialize($my_cart),
                'products_json' => json_encode($my_cart),
                'summa'     => $summa,
                'full_summa' => $full_summa,
                'delivery_price' => $deliveryPrice,
                'products_count'    => $count,
                'nadbavka'      => $nadbavka,
            );
            $this->db->where('id',$order_id)->limit(1)->update('orders', $editedOrder);
            echo 'Добавлене прошло успешно!';
        }
        elseif(($order) && $action == 'set_count'){                            // Изменяем кол-во
            $size = post('size');
            //alert("set_count orderID: ".$order['id'].' | shop_id='.$shop_id.' | count='.post('count').' | size='.$size);
            $unsetId = false;
            for($i = 0; $i < $productsCount; $i++) {
                //vd($my_cart[$i]);
                if(! is_array($my_cart[$i]['sizesCounts'])){
                    $unsetId = $i;
                    echo 'Ошибка! Данные в базе сохранились в неправильном формате и будут автоматически очищены!';
                }
                if(is_array($my_cart[$i]['sizesCounts'])) {
                    $mc = $my_cart[$i];
                    if ($mc['shop_id'] == $shop_id) {
                        //alert(vd($my_cart[$i]['sizesCounts']));
                        for ($ic = 0; $ic < count($mc['sizesCounts']); $ic++) {
                            if (isset($mc['sizesCounts'][$ic]['size']) && $mc['sizesCounts'][$ic]['size'] == $size) {
                                //alert("FOUND!!");
                                $razn = post('count') - $mc['sizesCounts'][$ic]['count'];
                                $my_cart[$i]['kolvo_' . $size] = $my_cart[$i]['sizesCounts'][$ic]['count'] = post('count');
                                $my_cart[$i]['count'] = $my_cart[$i]['count'] + $razn;
                            }
                        }
                    }
                }
            }
            if($unsetId){
                $resArr = array();
                for($i = 0; $i < $productsCount; $i++){
                    if(isset($my_cart[$i]['sizesCounts']) && is_array($my_cart[$i]['sizesCounts']) )
                        $resArr[] = $my_cart[$i];
                }
                $my_cart = $resArr;
            }


            $count = shop_count($my_cart);

            $full_summa = $summa = getProductsPrice($my_cart);
            if($deliveryPrice > 0)
                $full_summa = $full_summa + ($deliveryPrice * $count);


            if(($nadbavka) && $nadbavka > 0){                                   // прибавляем надбавку
                if($count < $optFrom){
                    //$full_summa = $full_summa + $nadbavka;
                } else $nadbavka = 0;
            }

            $editedOrder = array(
                'products'  => serialize($my_cart),
                'products_json' => json_encode($my_cart),
                'summa'     => $summa,
                'full_summa' => $full_summa,
                'delivery_price' => $deliveryPrice,
                'products_count'    => $count,
                'nadbavka'      => $nadbavka
            );
            $this->db->where('id',$order_id)->limit(1)->update('orders', $editedOrder);
            echo 'Изменение кол-ва прошло успешно! $nadbavka = '.$nadbavka.' кол-во товаров = '.$count.' opt_from = '.$optFrom;
        }
        return false;
    }

    public function autocomplete($type){
        if(!isset($_REQUEST['term']) && isset($_GET['term']))
            $_REQUEST['term'] = $_GET['term'];
        if(!isset($_REQUEST['term']) && isset($_POST['term']))
            $_REQUEST['term'] = post('term');

        $search = '';
        if(isset($_REQUEST['term']))
            $search = $_REQUEST['term'];

        $model = getModel($type);
        $users = $model->Search($search, 25, 0);
        $list = array();
        if($users){
            foreach ($users as $user){
                $list[] = array(
                    'id' => $user['id'],
                    'label' => $user['email'],
                    'value' => $user['login']
                );
            }
        }

        echo json_encode($list);
        //echo '[{"id":"Grus grus","label":"Common Crane","value":"Common Crane"},{"id":"Tringa totanus","label":"Common Redshank","value":"Common Redshank"},{"id":"Sterna sandvicensis","label":"Sandwich Tern","value":"Sandwich Tern"},{"id":"Caprimulgus europaeus","label":"European Nightjar","value":"European Nightjar"},{"id":"Upupa epops","label":"Eurasian Hoopoe","value":"Eurasian Hoopoe"},{"id":"Jynx torquilla","label":"Eurasian Wryneck","value":"Eurasian Wryneck"},{"id":"Picus viridis","label":"European Green Woodpecker","value":"European Green Woodpecker"},{"id":"Saxicola rubicola","label":"European Stonechat","value":"European Stonechat"},{"id":"Emberiza hortulana","label":"Ortolan Bunting","value":"Ortolan Bunting"},{"id":"Phalacrocorax carbo","label":"Great Cormorant","value":"Great Cormorant"},{"id":"Ficedula hypoleuca","label":"Eurasian Pied Flycatcher","value":"Eurasian Pied Flycatcher"},{"id":"Sitta europaea","label":"Eurasian Nuthatch","value":"Eurasian Nuthatch"}]';
    }

    public function import($action){
        if(isset($_GET['GoodID'])) $_POST['GoodID'] = $_GET['GoodID'];
        $GoodID = post('GoodID');
        if($action == 'dont_show'){
            $dbins = array(
                'GoodID' => $GoodID,
                'dont_show' => 1
            );
            $this->db->insert('import', $dbins);
        }
    }

    public function mailer($action, $id){
        if($action == 'delete'){
            $this->db->where('id',$id)->limit(1)->delete('mailer_cron');
        }
    }
    
    public function specifications($action){
        if(isset($_GET['id'])) $_POST['id'] = $_GET['id'];
        if(isset($_GET['name'])) $_POST['name'] = $_GET['name'];
        if($action == 'set_name'){
            $id = post('id');
            $name = post('name');
            $this->db->where('id',$id)->limit(1)->update('specifications', array('saved_name' => $name));
        }
    }

    public function index()
    {
        echo "ajax";
    }

    public function send_foto()
    {
        echo "asd";
    }

    function mail_send($mailer_id)
    {
        $this->load->model('Model_mailer', 'mailer');

        $mailer = $this->mailer->getById($mailer_id);
        if ($mailer) {
            $emails_list = unserialize($mailer['emails_list']);
            if ($emails_list == NULL) {
                $type = false;
                $mailer_test = getOption('mailer_test');
                if ($mailer_test == 0) $mailer_test = false; else $mailer_test = true;

                if ($mailer_test) $type = 'admin';
                $users = $this->users->getUsersByType(-1, -1, 'ASC', $type, 'id', 1);
                $emails_count = 0;
                $count = count($users);
                for ($i = 0; $i < $count; $i++) {
                    if (valid_email($users[$i]['email'])) {
                        $emails_list[$emails_count] = $users[$i]['email'];
                        $emails_count++;
                    }
                }
            }
            $message = createEmail($mailer['header'], $mailer['name'], $mailer['content'], unserialize($mailer['articles']), $mailer['adding'], $mailer['footer'], $mailer['no_price']);
            $isSended = false;
            $el = array_pop($emails_list);

            if(count($emails_list)  < 1) {
                echo 'end';die();
            }
            $isSended = mail_send($el, $mailer['name'], $message, "html", $mailer_test);
            echo 'Отправка на: ' . $el . ' ';
            if ($isSended) echo 'выполнена успешно!';
            else echo 'ЗАВЕРШИЛАСТЬ НЕУДАЧЕЙ((';

            $dbins['emails_list'] = serialize($emails_list);
            $this->db->where('id', $mailer_id);
            $this->db->limit(1);
            $this->db->update('mailer', $dbins);

        }
        //echo "asd";
    }

    function admin_action()
    {
        if (count($_POST) == 0) {
            echo "post=get" . "\r\n";
            $_POST = $_GET;
        }
        if (isset($_POST['action']) && isset($_POST['obj'])) {
            $action = $_POST['action'];
            $obj = $_POST['obj'];
            $type = false;
            if (isset($_POST['type'])) $type = $_POST['type'];
            $value = false;
            if (isset($_POST['value'])) $value = $_POST['value'];
            if ($action == 'create_torgsoft_file') {
                if (writeOrderFile($obj)) {
                    echo "Файл создан успешно";
                    die();
                } else echo "Ошибка записи файла!";
                die();
            } elseif ($action == 'mailer_stop') {
                $dbins = array('mailer_' . $type => 0);
                $this->db->where('id', $obj)->limit(1)->update('shop', $dbins);
                echo "done";
                die();
            } elseif ($action == 'mailer_add') {
                $dbins = array('mailer_' . $type => 1);
                $this->db->where('id', $obj)->limit(1)->update('shop', $dbins);
                echo "done";
                die();
            } elseif ($type == 'mailer') {
                if (($obj !== false) && ($value !== false)) {
                    $dbins = array(
                        'mailer_' . $action => $value
                    );

                    $this->db->where('id', $obj)->limit(1)->update('shop', $dbins);
                }
                echo "done: " . 'mailer_' . $action . ' = ' . $value;
                die();
            } else {
                echo "error";
            }

        } else echo "error";
        //err404();
    }

    public function getAdminMessage()
    {
        if ((isset($_GET)) && count($_GET) > 0) $_POST = $_GET;

        if (isset($_POST['action'])) {

            $action = $_POST['action'];

            $this->db->where('mailer_' . $action, 1);

            $this->db->from('shop');
            $ret = $this->db->count_all_results();
            echo "Отмечено " . $ret . " позиций типа " . $action;

        }
        //else err404();
    }


    public function users_search()
    {
        alert("asd");

        $this->load->model('Model_users', 'users');
        if (isset($_GET['search'])) ;
        $users = $this->users->search($_GET['search']);

        if ($users) {
            foreach ($users as $key => $value) {
                echo $value['name'] . "\n";
            }
        }

    }

    public function users()
    {
        $model = getModel('users');
        if (isset($_GET['search'])) {
            $this->load->model('Model_users', 'users');
            $keyword = $_GET['term'];
            // заголовки необходимы для браузеров
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Content-Type: text/json; charset=utf-8;');
            // отсылаем данные клиенту

            $patterns = array('/\s+/', '/"+/', '/%+/');
            $replace = array('');
            $keyword = preg_replace($patterns, $replace, $keyword);

            $users = $this->users->Search($keyword);

            $output = '[';
            // результат формируется циклом
            if ($users) {
                $count = count($users);
                for ($i = 0; $i < $count; $i++) {
                    $p = $users[$i];
                    if ($i == 0) {
                        $output .= '{ "id": "' . $p['id'] . '", "label": "' . $p['name'] . ' (' . $p['email'] . ')", "value": "' . $p['email'] . '"}';
                    } else {
                        $output .= ',{ "id": "' . $p['id'] . '", "label": "' . $p['name'] . ' (' . $p['email'] . ')", "value": "' . $p['email'] . '"}';
                    }
                }
            }
            // закрывающий тег json формата
            $output .= ']';
            // возвращаем данные
            echo $output;
        }
        elseif(isset($_GET['edit'])){   /** редактируем клиента */
            $id             = post('user_id');
            $name           = post('name');
            $lastname       = post('lastname');
            $user_type_id   = post('user_type_id');
            $email          = trim(post('email'));
            $tel            = post('tel');
            $country        = trim(post('country'));
            $city           = trim(post('city'));

            if($id){
                $user = $model->getUserById($id);
                if($user){
                    $err = false;
                    $dbins = array();
                    if($user['name'] != $name) $dbins['name'] = $name;
                    if($user['lastname'] != $lastname) $dbins['lastname'] = $lastname;
                    if($user['user_type_id'] != $user_type_id) $dbins['user_type_id'] = $user_type_id;
                    if($user['email'] != $email){
                        $res = $model->getUserByEmail($email);
                        if(! $res) {
                            $dbins['email'] = $dbins['login'] = $email;
                        }
                        else{
                            echo 'Невозможно изменить e-mail, т.к. такой адрес уже зарегистрирован!';
                            $err = true;
                        }
                    }
                    if($user['tel'] != $tel) $dbins['tel'] = $tel;
                    if($user['country'] != $country) {
                        $mShop = getModel('shop');
                        $countryArr = $mShop->getCountryByName($country);
                        if(! $countryArr){
                            /** Добавляем страну */
                            $this->db->insert('countries', array('name' => $country));
                        }

                        $dbins['country'] = $country;
                    }

                    /** Проверяем и добавляем город в таблицу, если такого ещё нет в базе */
                    $mShop = getModel('shop');
                    $cityArr = $mShop->getCityByName($city);
                    if(! $cityArr){
                        /** Добавляем город */
                        $countryArr = $mShop->getCountryByName($country);
                        $result = $this->db->insert('cities', array('name' => $city,'country_id'=>$countryArr['id'],'country'=>$countryArr['name']));
                        if($result)
                            echo 'Добавили город <b>'.$city.'</b> в базу<br>';
                        else echo 'Ошибка добавления города в базу!';
                    } else echo 'Найден город в базе';
                    if($user['city'] != $city){
                        $dbins['city'] = $city;
                    }

                    if(count($dbins) > 0 && !$err) {
                        $result = $this->db->where('id', $id)->limit(1)->update('users', $dbins);
                        if($result) echo 'Данные о клиенте успешно сохранены!';
                    } else echo 'Нет изменённых данных клиента';

                    if($err) echo 'Какая-то ошибка!';

                }  else echo 'Ошибка: не найден клиент с таким id!';
            } else echo 'Ошибка: не передан id клиента!';
        }
    }
}