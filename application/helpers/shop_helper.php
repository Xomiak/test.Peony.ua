<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


function getLiqpayOrderStatus($order_id, $sendNotificationEmail = false)
{
    $CI = &get_instance();
    $CI->load->library('Liqpay');
    $public_key = getOption('liqpay_public_key');
    $private_key = getOption('liqpay_private_key');
    $order = getOrderById($order_id);
    // Обрабатываем ответ от LiqPay:

    $liqpay = new LiqPay($public_key, $private_key);
    $liqpayData = array(
        'action' => 'status',
        'version' => '3',
        'order_id' => $order['liqpay_order_id']
    );
    $result = $liqpay->api("request", $liqpayData);
//vd($result);
    if (isset($result->status)) {
        $status = $result->status;
        $description = $result->status;
        if ($status == 'success') {
            $np = 0;
            $msg = "LiqPay: ";
            if (strpos($description, 'наложенным платежом') !== false) {
                $np = 1;
                $msg .= "Заказ наложенным платежом №" . $order_id . ' успешно оплачен!';
                updateItem($order_id, 'orders', array('status' => 'npnp_payed'));
            } else {
                updateItem($order_id, 'orders', array('status' => 'payed'));
                $msg .= "Заказ №" . $order_id . ' успешно оплачен!';
            }
            $msg .= '<br/>
Сумма полаты: ' . $result->amount . ' ' . $result->currency;
            if ($sendNotificationEmail) {
                $CI->load->helper('mail_helper');
                $to = getOption('admin_email');
                mail_send($to, "LiqPay: Оплата заказа №" . $order_id, $msg);
            }
        }
        updateItem($order_id, 'orders', array('liqpay_result' => json_encode($result)));
    }
    return $result;
}


// СОЗДАЁМ ФАЙЛ ДЛЯ ИМПОРТА В ТОРГСОФТ
//function createNewOrdersFiles()
//{
//    $CI = &get_instance();
//    //$unix = time() - 1500;
//    $CI->db->where('torgsoft_file', 0);
//    //$CI->db->where('unix <', $unix);
//    $orders = $CI->db->get('orders')->result_array();
//    //vdd($orders);
//    if ($orders) {
//        foreach ($orders as $order) {
//            $writed = writeOrderFile($order);
//            if ($writed)
//                $CI->db->where('id', $order['id'])->limit(1)->update('orders', array('torgsoft_file' => 1));
//        }
//    }
//}

function getPostAdress()
{
    $adress = '';

    $country = post('country');
    if (!$country) {
        $CI = &get_instance();
        loadHelper('geoip');
        $country = getUserCountry();
        if ($country != 'other') {
            $country = getCountryById($country);
            if (isset($country['name'])) $country = $country['name'];
        } else $country = post('other_country');
    }

    if (userdata('type') == 11)
        $adress .= '<b>Заказ от дропшиппера. Адрес доставки:</b><br />' . post('ds_name') . ', ' . $country . ', г. ' . post('city') . '<br />Тел: ' . post('ds_tel');
    else $adress .= $country . ', г. ' . post('city') . '<br />Тел: ' . post('tel');

    if (isset($_POST['adress']) && $_POST['adress'] != '') $adress .= '<br /> Адрес: ' . post('adress');
    if (isset($_POST['passport']) && $_POST['passport'] != '') $adress .= '<br />Паспорт: ' . post('passport');
    if (isset($_POST['zip']) && $_POST['zip'] != '') $adress .= '<br />Индекс: ' . post('zip');
    if (isset($_POST['np']) && $_POST['np'] != '') $adress .= '<br />Новая Почта №' . post('np');

    return $adress;
}


//function getOrderData($order_id)
//{
//    $CI = &get_instance();
//    $result = array();
//    $coupon = false;
//    $mOrders = getModel('shop');
//    $mUsers = getModel('users');
//    $order = $mOrders->getOrderById($order_id);
//    $my_cart = unserialize($order['products']);
//    $user = $mUsers->getUserById($order['user_id']);
//    $country = false;
//    if($order['country_id'] != 0)
//        $country = getItemById($order['country_id'],'countries');
//    elseif($order['country'] != NULL)
//        $country = getCountryByName($order['country']);
//    else{
//        $country = getCountryByName($user['country']);
//    }
//
//    $shop_opt_from = 4;
//    if(isset($country['opt_from']) && $country['opt_from'] > 0)
//        $shop_opt_from = $country['opt_from'];
//
//    $npnp = 0;
//    if($order['npnp'] == 1) $npnp = 1;
//
//    if($order['coupon'] != NULL) $coupon = json_decode($order['coupon'], true);
//    $summa = 0;
//    $summaNotSale = 0;
//    if ($my_cart) {
//        $count = count($my_cart);
//        for ($i = 0; $i < $count; $i++) {
//            $shop = $CI->shop->getProductById($my_cart[$i]['shop_id']);
//
//            $my_cart[$i]['final_price'] = $my_cart[$i]['original_price'] = $shop['price'];
//            if ($shop['discount'] > 0) {
//                $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $shop['discount']);
//                $my_cart[$i]['discount'] = $shop['discount'];
//            } elseif ($coupon) {
//                $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $coupon['discount']);
//                $my_cart[$i]['discount'] = $coupon['discount'];
//            }
//
//            if ($shop) {
//                if (isDiscount($shop)) {
//                    $akciya = 1;
//                }
//
//
//                $razmer = explode('*', $shop['razmer']);
//                $rcount = count($razmer);
//                for ($i2 = 0; $i2 < $rcount; $i2++) {
//                    if (isset($my_cart[$i]['kolvo_' . $razmer[$i2]])) {
//                        //$message_table .= $razmer[$i2].': '.$mc['kolvo_'.$razmer[$i2]].'<br>';
//                        $res = getAkciyaPrice($shop) * $my_cart[$i]['kolvo_' . $razmer[$i2]];
//                        $summa = $summa + $res;
//                        if ($shop['discount'] == 0)
//                            $summaNotSale = $summaNotSale + $res;
//                    }
//                }
//
//
//            }
//            //vd($akciya);
//        }
//    }
//
//    $deliveryPrice = 4;
//    $userType = false;
//    if($user)
//        $userType = getItemById($user['user_type_id'], 'user_types');
//
//    if(!$userType)
//        $userType = getItemById(1, 'user_types');
//
//
//    $nadbavka = $shop_nadbavka = $order['nadbavka'];
//
////    $kolvo = shop_count();
////
////    if ($kolvo < $shop_opt_from) {
////        if (isset($userType['nadbavka']) && $userType['nadbavka'] != -1)
////            $shop_nadbavka = $userType['nadbavka'];
////        else
////            $shop_nadbavka = 0;
////
////        //$shop_nadbavka = $shop_nadbavka * $kolvo;
////        //$summa = $summa + $shop_nadbavka;
////        //$summaNotSale = $summaNotSale + $shop_nadbavka;
////        $nadbavka = $shop_nadbavka;
////    } else $nadbavka = 0;
//
//
//    $discount = 0;
//    if (isset($coupon) && !isset($coupon['err'])) {
//        $discount = $coupon['discount'];
//        if ($coupon['type'] == 0) {
//            if ($coupon['not_sale'] == 1) {
//                // высчитываем скидку без учёта товаров из раздела Sale
//                $res = $summaNotSale / 100 * $discount;
//                // vd($summaNotSale.': '.$res);
//                $summa = $summa - $res;
//            } else {
//                // высчитываем скидку полностью на всю покупку
//                $res = $summa / 100 * $discount;
//                //  vd("not");
//                $summa = $summa - $res;
//            }
//        } elseif ($coupon['type'] == 1) {
//            $summa = $summa - $discount;
//        }
//    }
//
//    $full_summa = $summa;
//
//    if ($nadbavka)
//        $full_summa = $summa + $nadbavka;
//
//
//    // Данные о курсе валют на момент заказа
//    $todayCurrenciesValues = array(
//        'UAH' => getCurrencyValue('UAH'),
//        'USD' => getCurrencyValue('USD'),
//        'RUB' => getCurrencyValue('RUB'),
//    );
//
//    $myCartCount = shop_count($my_cart);
//
//    $deliveryPrice = 0;
//    $deliveryPriceFull = 0;
//   // var_dump($country);
//    if(isset($country['delivery_price'])) $deliveryPrice = $country['delivery_price'];
//    if ($deliveryPrice > 0) {
//    //    vdd($deliveryPrice);
//
//        $deliveryPriceFull = (float)$deliveryPrice * $myCartCount;
//
//        $full_summa = $full_summa + $deliveryPriceFull;
//    }
////vdd($myCartCount);
//    $details = array();
//
//    $result['npnp'] = 1;
//    $result['deliveryPrice'] = $deliveryPrice;
//    $result['deliveryPriceFull'] = $deliveryPriceFull;
//    $result['summa'] = $summa;
//    $result['summaNotSale'] = $summaNotSale;
//    $result['nadbavka'] = $nadbavka;
//    $result['full_summa']   = $full_summa;
//    $result['result_summa']   = $full_summa;
//    $result['pay_summa'] = $full_summa;
//    if($npnp == 1){
//        $npnp_price = getOption('npnp_price');
//        $details['npnp'] = 1;
//        $details['pay_summa'] = $npnp_price;
//        $details['result_summa'] = $full_summa - $npnp_price;
//    }
//    $result['details'] = $details;
//    $result['currencies'] = json_encode($todayCurrenciesValues);
//    $result['deliveryPrice'] = $deliveryPrice;
//    $result['country'] = $country;
//    $result['products_count'] = $myCartCount;
//    $result['shop_opt_from'] = $shop_opt_from;
//    $result['currencySymb'] = getCurrencySymb($order['currency']);
//    $result['currency'] = getCurrencyByCode($order['currency']);
////vdd($result);
//    return $result;
//}

function getMyCountry()
{
    $countryId = false;
    if (post('country') !== false)
        $countryId = post('country');
    elseif (userdata('country') !== false)
        $countryId = userdata('country');

    if ($countryId != false && $countryId != 'other') {
        $mShop = getModel('shop');
        $country = $mShop->getCountryById($countryId);
        if (isset($country['name'])) return $country['name'];
    } elseif ($countryId == 'other' && post('other_country') !== false)
        return post('other_country');

    $CI = &get_instance();
    $CI->load->helper('geoip_helper');
    $country = getUserCountry();

    return $country;
}

//
//function createNewOrder($userInfo = false, $orderDetails = false, $config = false){
//
//    $CI = &get_instance();
//
//    if(!$orderDetails)
//        $orderDetails = getMyCartData();
//   // vdd($orderDetails);
//
//    $adding = post('adding');
//    $oneClickOrder = 0;
//    if(isset($orderDetails['one_click_order'])) {
//        $adding .= "<p><b>Заказ в 1 клик!<br/>Перезвоните мне, пожалуйста для уточнения моих данных!</b></p>";
//        $oneClickOrder = 1;
//    }
//
//
//    $user = getOrCreateUser($userInfo);
//
//    if(!isset($orderDetails['products'])) {
//        $orderDetails = getMyCartData();
//        if($oneClickOrder == 1) $orderDetails['one_click_order'] = 1;
//    }
//
//    $one_click_tel = NULL;
//    if(isset($orderDetails['one_click_tel']))
//        $one_click_tel = $orderDetails['one_click_tel'];
//    elseif(post('one_click_tel') !== false)
//        $one_click_tel = post('one_click_tel');
//
//
//
//    $adress = false;
//    if($oneClickOrder != 1) {
//        if (isset($orderDetails['adress'])) $adress = $orderDetails['adress'];
//        else $adress = getPostAdress();
//    }
//    //vdd("asd");
//    $country = 'Не определена';
//    $smodel = getModel('shop');
//    if(userdata('country') !== false)
//        $country = userdata('country');
//    elseif($user['country'] != NULL)
//        $country = $user['country'];
//    else
//        $country = getMyCountry();
//
//    $countryId = 0;
//    $countryName = 'Не определена';
//    if($country != false && $country != 'Не определена') {
//        $rCountry = $smodel->getCountryByName($country);
//        if (!$rCountry)
//            $rCountry = $smodel->getCountryById($country);
//        if ($rCountry)
//            $country = $rCountry;
//    }
//
//
//    if(isset($country['id'])) $countryId = $country['id'];
//    if(isset($country['name'])) $countryName = $country['name'];
////    $shop_opt_from = 0;
////    if(isset($country['opt_from'])) $shop_opt_from = $country['opt_from'];
////    else $shop_opt_from = getOption('shop_opt_from');
//
//    $deliveryPrice = 0;
//    //vdd($orderDetails);
//    if(isset($orderDetails['deliveryPrice'])) $deliveryPrice = $orderDetails['deliveryPrice'];
//    elseif(isset($country['delivery_price'])) $deliveryPrice = $country['delivery_price'];
//
//    $npnp = 0;
//    if(isset($orderDetails['npnp']) && $oneClickOrder != 1) $npnp = $orderDetails['npnp'];
//
//    $akciya = 0;
//    if(isset($orderDetails['coupon']) && $orderDetails['coupon'] !== false)
//        $akciya = 1;
//
//    $dropship_id = 0;
//    $addr_id = 0;
//    if (userdata('type') == 11) { // Если дропшиппер
//        $dropship_id = $user['id'];
//        $addr_id = post('addr_id');
//        if(! $addr_id || $addr_id == 0){
//            $dbins = array(
//                'user_id' => $user['id'],
//                'login' => $user['login'],
//                'name' => post('ds_name'),
//                'tel' => post('ds_tel'),
//                'country_id' => post('country'),
//                'country' => $country['name'],
//                'city' => post('city'),
//                'adress' => $adress,
//                'np' => post('np'),
//                'passport' => post('passport'),
//                'zip' => post('zip')
//            );
//            $CI->db->insert('addr', $dbins);
//
//            $CI->db->where('user_id', $user['id']);
//            $CI->db->where('login', $user['login']);
//            $CI->db->where('name', post('ds_name'));
//            $CI->db->where('tel', post('ds_tel'));
//            $CI->db->limit(1);
//            $CI->db->order_by('id', 'DESC');
//            $addr = $CI->db->get('addr')->result_array();
//            if (isset($addr[0])) {
//                $addr = $addr[0];
//                $addr_id = $addr['id'];
//            }
//        } elseif($addr_id != 0){
//            $addr = $CI->users->getAddressById(post('addr_id'));
//            if ($addr) $addr_id = $addr['id'];
//        }
//    }
//    //$my_cart = userdata('my_cart');
//
//    if($orderDetails['products']) {
//        $status = 'new';
//        if($npnp == 1)
//            $status = 'npnp_not_payed';
//
//        $unix = time();
//        $currency = 'uah';
//        if(isset($orderDetails['currency']))
//            $currency = $orderDetails['currency'];
//
//        $payment = "Не указан";
//        $delivery = "Не указан";
//
//
//        if($oneClickOrder) {
//            $adress = '<b>Заказ в 1 клик</b>Тел.: ' . $one_click_tel.'<br />IP клиента: '.$_SERVER['REMOTE_ADDR'];
//            if(userdata('country') !== false) $adress .= '<br />Возможная страна: '.$country['name'];
//        } else {
//            if(isset($_POST['payment'])) $payment = post('payment');
//            if(isset($_POST['delivery'])) $delivery = post('delivery');
//        }
//
//        $nadbavka = $shop_nadbavka = $orderDetails['nadbavka'];
//        //vdd($nadbavka);
//
//        if(!isset($orderDetails['currencies'])) {
//            $orderDetails['currencies'] = array(
//                'UAH' => getCurrencyValue('UAH'),
//                'USD' => getCurrencyValue('USD'),
//                'RUB' => getCurrencyValue('RUB'),
//            );
//        }
//
//        if($deliveryPrice > 0) $deliveryPrice = $deliveryPrice * $orderDetails['products_count'];
//        $dbins = array(
//            'user_id' => $user['id'],
//            'date' => date("Y-m-d"),
//            'time' => date("H:i"),
//            'unix' => $unix,
//            'country' => $countryName,
//            'country_id' => $countryId,
//            'products_json' => json_encode($orderDetails['products']),
//            'products' => serialize($orderDetails['products']),
//            'products_count' => $orderDetails['products_count'],
//            'adress' => $adress,
//            'payment' => $payment,
//            'delivery' => $delivery,
//            'delivery_price' => $deliveryPrice,
//            'summa' => $orderDetails['summa'],
//            'full_summa' => $orderDetails['full_summa'],
//            'adding' => $adding,
//            'currency' => $currency,
//            'akciya' => $akciya,
//            'details' => json_encode($orderDetails['details']),
//            'nadbavka' => $nadbavka,
//            'currencies' => json_encode($orderDetails['currencies']),
//            'dropship_id' => $dropship_id,
//            'addr_id' => $addr_id,
//            'npnp' => $npnp,
//            'status' => $status,
//            'one_click' => $oneClickOrder
//        );
//
//        //return $dbins;
//
////vdd($dbins);
////        if(isDebug()){
////            vdd($dbins);
////        }
//        $adding = $CI->db->insert('orders', $dbins);
//
//
//        $CI->db->where('user_id', $user['id']);
//        $CI->db->where('unix', $unix);
//        $order = $CI->db->get('orders')->result_array();
//        if(isset($order[0])) {
//            $order = $order[0];
//            echo 'Заказ №'.$order['id'].' успешно сформирован!';
//        }
//
//
//        $currencySymb = getCurrencySymb($order['currency']);
//
//        // Отправка клиенту
//        $sendClientNotification = true;
//        if(isset($config['sendClientNotification']) && $config['sendClientNotification'] == false)
//            $sendClientNotification = false;
//        if(isset($user['email']) && $sendClientNotification) {
//            $clientMessage = '';
//            if(!$oneClickOrder)
//                $clientMessage = createOrderEmail($order['id']);
//            else{
//                $myCartTable = getMyCartTable($order);
//
//                $clientMessage = $myCartTable;
//                if($order['nadbavka'])
//                    $clientMessage .= '<p><b>Надбавочная стоимость:</b> '.get_price($order['nadbavka']).' '.$currencySymb.'</p>';
//                $clientMessage .= '<p><b>Цена всех товаров:</b> '.get_price($order['full_summa']).' '.$currencySymb.'</p>';
//                $clientMessage .= '<br/><br/>В ближайшее время с Вами свяжется наш менеджер для уточнения всех деталей заказа!';
//            }
//            $msg = createEmail('/upload/email/42ed846af09614f5cb4bf3ffc7cdeb6e.jpg', "Ваш заказ №: " . $order['id'] . ' оформлен успешно!', $clientMessage,false,false,false,0,false,false);
//            $result = mail_send($user['email'], "Ваш заказ №: " . $order['id'] . ' оформлен успешно!', $msg);
//            //if($result) echo '<br/>Письмо клиенту отправлено!';
//        }
//        //$message = str_replace($shop_sended, '', $message);
//
//        $to = getOption('admin_email');
//        //$to = 'xomiak@rap.org.ua';
////vdd("a");
//        $message = "";
//        if(!$oneClickOrder)
//            $message = createOrderEmail($order['id'], true);
//        else {
//            $myCartTable = getMyCartTable($order, true);
//            $message = $myCartTable;
//            $message .= '<p><b>Сумма заказа:</b> '.get_price($order['full_summa']).' '.$currencySymb.' ('.$order['full_summa'].'$)</p>';
//            $message .= "<p>Заказ в 1 клик!<br />
//Телефон: ".$one_click_tel."<br />
//Предположительная страна: ".$country['name']."<br/>
//<a href='http://peony.ua/admin/orders/edit/".$order['id']."/".adminLoginGetAdding()."'>Перейти к заказу</a></p>";
//        }
//        $from = "";
//        if (userdata('adwords') !== false)
//            $from .= '<br /><b>Клиент пришёл с рекламы: ' . userdata('adwords') . '</b><br />';
//        elseif ($user['from'] != NULL && $user['from'] != '')
//            $from .= '<br /><b>Клиент пришёл с рекламы: ' . $user['from'] . '</b><br />';
//
//        $message .= $from;
//        //$msg = createEmail('/upload/email/42ed846af09614f5cb4bf3ffc7cdeb6e.jpg', "Поступил новый заказ № " . $order['id'], $message);
//        $subject = "Поступил новый заказ № " . $order['id'];
//        if (userdata('type') == 11) $subject .= ' от дропшиппера';
//        if($oneClickOrder) $subject = "Поступил заказ в 1 клик №".$order['id'];
//        mail_send($to, $subject, $message);
//
//        unset_userdata('my_cart');
//
//        return $order;
//    }
//
//    return false;
//}

function getOrCreateUser($data = false)
{
    $CI = &get_instance();

    $undefinedUser = false;

    $user = false;
    $CI->load->model('Model_users', 'users');

    if (isset($data['one_click_tel']) && $data['one_click_tel'] != '') {
        $email = $data['email'];
        if($email){
            $user = $CI->users->getUserByEmail($data['email'], true);
        }
        $tel = $data['tel'];

        if ($tel) {
            $user = $CI->users->getUserByTel($tel, true);
            if (!$user) {
                $advSearch = false;
                if (mb_substr($tel, 0, 4) == '+380') {
                    $tel = mb_substr($tel, 4);
                    $advSearch = true;
                } elseif (mb_substr($tel, 0, 3) == '380') {
                    $tel = mb_substr($tel, 3);
                    $advSearch = true;
                } elseif (mb_substr($tel, 0, 2) == '+7') {
                    $tel = mb_substr($tel, 2);
                    $advSearch = true;
                }

                if ($advSearch) {
                    $user = $CI->users->getUserByTel($tel, true);
                    echo "advSearch".'<br/>';;
                }
            } else echo 'Найден по телефону: '.$tel.'<br/>';;
        } else echo 'Не найден по тел'.'<br/>';
        if ($user) return $user;
    }
    if ($user) return $user;

    if ($data == false && userdata('login') !== false) {
        $user = $CI->users->getUserByLogin(userdata('login'));
        echo "Найден клиент с логином ".userdata('login').'<br/>';
    }
//var_dump($user);die();
    if (isset($data['email']) && !$user) {
        $user = $CI->users->getUserByEmail($data['email'], true);
        if ($user) {
            echo "Найден пользователь по мылу: ".$data['email'].'<br/>';
            return $user;
        }
    }
    if (isset($data['tel']) && !$user) {
        $tel = $data['tel'];
        $user = $CI->users->getUserByTel($tel, true);
        if ($user){
            echo "Найден клиент по моб: ".$tel.'<br/>';
            return $user;
        }
    }
    if (!$user) {
        if (isset($data['login'])) {
            $user = $CI->users->getUserByLogin($data['login'], true);
            if ($user) return $user;
        } else if (userdata('login') !== false) {
            $data['login'] = userdata('login');
            $user = $CI->users->getUserByLogin(userdata('login'), true);
            if ($user) return $user;
        }
    }


    // ЕСЛИ ПОЛЬЗОВАТЕЛЯ НЕТ, ТО СОЗДАЁМ ЕГО
    if (!$user) {
        if (!isset($data['country'])) {
            $CI->load->helper('geoip_helper');
            $country = getUserCountry();
            if ($country != 'other') {
                $CI->load->model('Model_shop', 'shop');
                $country = $CI->shop->getCountryById($country);
                if (isset($country['name'])) $data['country'] = $country['name'];
            } else $data['country'] = post('other_country');
        }
        if (!isset($data['login'])) {
            if (isset($data['email'])) $data['login'] = $data['email'];
            elseif (isset($data['tel'])) $data['login'] = $data['tel'];
            else $data['login'] = time();
        }
        if (!isset($data['name'])) $data['name'] = "n/a";
        if (!isset($data['lastname'])) $data['lastname'] = "n/a";
        if (!isset($data['email'])) $data['email'] = "";
        if (!isset($data['city'])) $data['city'] = "";
        if (!isset($data['adress'])) $data['adress'] = "";
        if (!isset($data['passport'])) $data['passport'] = "";
        if (!isset($data['zip'])) $data['zip'] = "";
        if (!isset($data['np'])) $data['np'] = "";
        if (!isset($data['type'])) $data['type'] = "Посетитель";
        if (!isset($data['user_type_id'])) $data['user_type_id'] = 1;
        if (!isset($data['active'])) $data['active'] = 1;
        if (!isset($data['reg_date'])) $data['reg_date'] = date("Y-m-d");
        if (!isset($data['reg_ip'])) $data['reg_ip'] = $_SERVER['REMOTE_ADDR'];
        if (!isset($data['activation'])) $data['activation'] = 1;
        if (!isset($data['mailer'])) $data['mailer'] = 1;
        if (!isset($data['register_from']) && isset($data['one_click_tel'])) $data['register_from'] = 'buy_one_click';
        elseif($_SERVER['REQUEST_URI'] == '/my_cart/') $data['register_from'] = 'my_cart';

        $data['pass'] = md5('login');
    }


    $CI->db->insert('users', $data);
    $CI->load->model('Model_users', 'users');
    echo "Создан новый клиент: ".$data['login'].'<br/>';
//    if (isDebug())
//        echo 'Create New User: ' . vdd($data);

    $user = $CI->users->getUserByLogin($data['login']);
    $user['new'] = true;
    if($data['name'] == $data['lastname'] && $data['name'] == 'n/a')
        $user['undefinedUser'] = true;

    return $user;
}

//function getMyCartTable($order, $toAdmin = false, $coupon = false){
//
//    $CI = &get_instance();
//    $currency = getCurrencyByCode($order['currency']);
//
//    $userHash = '';
//    if(!$toAdmin) {
//        $user = false;
//        if (userdata('login') !== false)
//            $user = getUserIdBylogin(userdata('login'), true);
//
//
//        if ($user != false) {
//            $userHash = '?from=email&auto_auth=true&user_id=' . $user['id'] . '&hash=' . md5($user['pass']);
//        }
//    }
//
//    $currencySymb = '$';
//    if(isset($currency['symb']))
//        $currencySymb = $currency['symb'];
//
//    $message_table = '<table class="products" border="1"';
//
//    if(!$toAdmin) $message_table .= ' style="width: 100%; border-top:1px solid #c2c2c2;"';
//    $message_table .= '>
//					<th>Товар</th>
//					<th>Цвет</th>
//					<th>Размер:Кол-во</th>
//					<th>Цена за шт.</th>
//					<th>Общая стоимость</th>';
//
//    $order['inCurrency'] = getPriceInCurrency($order['summa'],0,$order['currency']);
//
//    $user = getItemById($order['user_id'],'users');
//    $userType = false;
//    if($user)
//        $userType = getItemById($user['user_type_id'], 'user_types');
//
//    if(!$userType)
//        $userType = getItemById(1, 'user_types');
//
//
//    $nadbavka = $shop_nadbavka = $order['nadbavka'];
//
//    $opt_from = getOption('shop_opt_from');
//    if(userdata('country') === false){
//        loadHelper('geoip');
//        set_userdata('country', getUserCountry());
//    }
//    if(userdata('country') !== false){
//        $country = getCountryById(userdata('country'));
//       // vdd($country);
//        if(!$country) $country = getCountryByName(userdata('country'));
//        if(!$country) {
//            loadHelper('geoip');
//            $country = getCountryByName(getUserCountry());
//        }
//        if(isset($country['opt_from'])) $opt_from = $country['opt_from'];
//    }
//
//
//
//    $my_cart = unserialize($order['products']);
//    $pcount = count($my_cart);
//    $full_price = 0;
//    $kolvo = 0;
//    for ($j = 0; $j < $pcount; $j++) {
//        $mc = $my_cart[$j];
//        $product = $CI->shop->getProductById($mc['shop_id']);
//        $cat = $CI->model_categories->getCategoryById($product['category_id']);
//        $razmer = explode('*', $product['razmer']);
//        $rcount = count($razmer);
//        $parent = false;
//        $price = getAkciyaPrice($product);
//        $productSale = false;
//        if ($product['discount'] > 0)
//            $productSale = true;
//        $akciya = isActionTime();
//
//
//        $price_one = round($price, 2);
//
//        //if($mc['kolvo'] > 1)
//        //$price = $price * $mc['kolvo'];
//        //var_dump($price);die();
//        if ($product['parent_category_id'] != 0) $parent = $CI->model_categories->getCategoryById($product['parent_category_id']);
//        $message_table .= '<tr><td>
//						    <a href="http://' . $_SERVER['SERVER_NAME'] . '/';
//        if ($parent) $message_table .= $parent['url'] . '/';
//        $message_table .= $cat['url'] . '/' . $product['url'] . '/'.$userHash.'" target="_blank">' . $product['name'] . '</a> (' . $product['articul'] . ')
//						</td>
//						<td align="center">' . $product['color'] . '</td>
//						<td align="center">';
//
//        $pres = 0;
//        for ($i2 = 0; $i2 < $rcount; $i2++) {
//            if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != '0') {
//                $message_table .= $razmer[$i2] . ': ' . $mc['kolvo_' . $razmer[$i2]] . '<br>';
//                $res = $price * $mc['kolvo_' . $razmer[$i2]];
//                $pres += $res;
//                $kolvo += $mc['kolvo_' . $razmer[$i2]];
//            }
//        }
//        $full_price += $pres;
//        if (isset($full_price_discount) && $full_price_discount > 0) $full_price = $full_price_discount + $pres;
//
//        $price = round($pres, 2);
//
//
//        $message_table .= '</td>
//						<td align="center">' . get_price($price_one) . ' ' . $currencySymb;
//        if($toAdmin) $message_table .= ' (' . $price_one . '$)';
//        if ($coupon && $productSale == false) $message_table .= ' (<b>Акция!</b>)';
//        elseif ($productSale) $message_table .= ' (<b>Sale</b>)';
//        $message_table .= '</td>
//						<td align="center">' . get_price($price) . ' ' . $currencySymb;
//        if($toAdmin) $message_table .= '(' . $price . '$)';
//        $message_table .= '</td>
//</tr>';
//    }
//
//    $message_table .= '</table>';
//
//    if($order['nadbavka'] > 0 && ($toAdmin)) {
//        $message_table .= '<p><b>Розничная наценка: </b>' . get_price($order['nadbavka']). ' ' . $currencySymb;
//        $message_table .= ' (' . $order['nadbavka'] . '$)';
//        $message_table .= '</p>';
//    }
//    if($order['delivery_price'] > 0) {
//        $message_table .= '<p><b>Стоимость доставки в страну получателя: </b>' . get_price($order['delivery_price']);
//        if ($toAdmin) $message_table .= ' (' . $order['delivery_price'] . '$)';
//        $message_table .= '</p>';
//    }
//
//    return $message_table;
//}

//function getOrderDetailsData($order, $toAdmin = false){
//    $result = '';
//
//    $orderDetails = getOrderData($order['id']);
//    $coupon = false;
//    $user = getItemById($order['user_id'],'users');
//    $full_price_discount = 0;
//    if($order['coupon'] != NULL) {
//        $coupon = json_decode($order['coupon'], true);
//    }
//    if ($coupon) {
//        $result .= '<b>Был использован скидочный купон</b>: ' . $coupon['code'];
//        $full_price = $orderDetails['summa'];
//        $full_price_discount = $orderDetails['summa'];
//        if (isset($coupon) && !isset($coupon['err'])) {
//            $discount = $coupon['discount'];
//            $result .= ' (делает скидку ' . $discount;
//            if ($coupon['type'] == 0) {
//                $res = 0;
//                if ($coupon['not_sale'] == 1) {
//                    $res = $orderDetails['summaNotSale'] / 100 * $discount;
//                } else {
//                    $res = $orderDetails['full_summa'] / 100 * $discount;
//                }
//                $full_price_discount = $full_price - $res;
//                $full_price = $full_price_discount;
//                $result .= '%';
//                if ($coupon['not_sale'] == 1)
//                    $result .= ' на все товары, кроме раздела Sale';
//            } elseif ($coupon['type'] == 1) {
//                $full_price_discount = $full_price - $discount;
//                $full_price = $full_price_discount;
//                $result .= ' USD';
//            }
//            $result .= ')<br />';
//            if ($coupon['info'] != '') $result .= 'Дополнительная информация о купоне: <i>' . $coupon['info'] . '</i><br />';
//        }
//        unset_userdata('coupon');
//    }
//    $nadbavka = $shop_nadbavka = $orderDetails['nadbavka'];
//
//    $shop_opt_from = $orderDetails['shop_opt_from'];
//
//
//    $delivery_price_msg = '';
////    $delivery_price = 0;
////    if (isset($orderDetails['deliveryPriceFull']) && $orderDetails['deliveryPriceFull'] > 0) {
////
////        $result .= '<b>Стоимость доставки в страну '.$orderDetails['country']['name'].': ' . get_price($order['delivery_price']) . ' ' . $orderDetails['currencySymb'] . '</b>';
////        if($toAdmin) $result .= '(' . $order['delivery_price'] . '$)';
////        $result .= '<br />';
////    }
//
//    //var_dump($message_table);die();
//
//    //	var_dump($order['summa']);die();
//
//   // $message_table .= '<br />';
//
//
//    if ($user['user_type_id'] == 11 && $toAdmin)
//        $result .= '<b>Заказ от дропшиппера</b><br />';
//
//    if(!$toAdmin) {
//        $shop_sended = getOption('shop_sended');
//        $result .= $shop_sended;
//
//        // Генерируем ссылку для перехода к заказу и авто авторизации
//        $userHash = '?from=email';
//        if($user != false && ! $toAdmin)
//            $userHash = '&auto_auth=true&user_id='.$user['id'].'&hash='.md5($user['pass']);
//
//        $result .= 'Следить за статусом своего заказа, а также, оплатить онлайн, Вы можете на странице Вашего заказа: <a href="//peony.ua/user/order-details/1411/'.$userHash.'">перейти к заказу</a><br />';
//    }
//    $result .= 'Фамилия, Имя: ' . $user['lastname'] . ', ' . $user['name'] . '<br />
//					e-mail: ' . $user['email'] . '<br />';
//    if ($coupon) {
//        if (isset($full_price_discount) && $full_price_discount > 0) $full_price = $full_price_discount;
//        $result .= '<b>Стоимость товаров со скидкой: ' . get_price($full_price_discount) . ' ' . $orderDetails['currencySymb'];
//        if($toAdmin) $result .= ' (' . $full_price_discount . '$)';
//        $result .= '</b><br />';
//    } else {
//        $result .= 'Стоимость товаров: ' . get_price($orderDetails['summa']) . ' ' . $orderDetails['currencySymb'];
//        if($toAdmin)$result .= ' (' . $orderDetails['summa'] . '$)';
//        $result .= '<br />';
//    }
//
//    $kolvo = $orderDetails['products_count'];
//
////    if ($kolvo < $shop_opt_from) {
////        //$shop_nadbavka = $orderDetails['nadbavka'];
////        $result .= '<b">Розничная наценка</b>: ' . get_price($nadbavka) . ' ' . getCurrencySymb($order['currency']);
////        if($toAdmin) $result .=  ' (' . $nadbavka . '$)';
////        $result .= '</b><br />';
////    }
//    //vdd($orderDetails);
//
//   // $message_table .= $nadbavka;
//   // $message_table .= $delivery_price_msg;
//    $result .= 'Общее количество товаров: ' . $kolvo . ' <br />';
//
//    //vd($orderDetails);
//
//    $result .= '<h2><b>Всего к оплате</b>: ' . get_price($orderDetails['full_summa']) . ' ' . $orderDetails['currencySymb'];
//    if($toAdmin) $result .= ' (' . $orderDetails['full_summa'] . '$)';
//    $result .= '</h2>';
//
//    //if ($order['delivery'] != 'Новая Почта')
//    $result .= 'Доставка: ' . $order['delivery'] . '<br />';
//
//    if ($order['npnp']) {
//        $npnp_price = getOption('npnp_price');
//        $result .= 'Предоплата наложенного платежа: ' . get_price($npnp_price) . ' ' . $orderDetails['currencySymb'] . '<br />';
//        $result .= 'Остаток наложенного платежа: ' . get_price($full_price - $npnp_price) . ' ' . $orderDetails['currencySymb'] . '<br />';
//    }
//
//    $result .= 'Адрес: ' . $order['adress'] . '<br />
//					Оплата: ' . $order['payment'] . '<br />
//
//					Валюта: ' . $orderDetails['currency']['name'] . '
//					<br />
//					Дополительная информация:<br />
//					' . $order['adding'];
//
//    if($toAdmin){
//        $result .= '<br/><br><b><a href="http://peony.ua/admin/orders/edit/'.$order['id'].'/'.adminLoginGetAdding().'">Перейти к заказу</a></b>';
//    }
//
//    return $result;
//    //$message = $message . $message_table;
//
//
//    //echo $message;die();
//
////    // Обработка купона
////    if ($coupon) {
////        $coupon['used_date'] = date("Y-m-d H:i");
////        $coupon['used_by'] = $user['login'];
////        $coupon['order_id'] = $order['id'];
////        $CI->db->where('id', $coupon['id'])->limit(1)->update('coupons', $coupon);
////
////        if ($coupon['multi'] != 1) {
////            $dbins = array(
////                'coupon_id' => $coupon['id'],
////                'user_login' => $user['login'],
////                'date' => date("Y-m-d H:i"),
////                'order_id' => $order['id']
////            );
////            $CI->db->insert('coupons_using', $dbins);
////        }
////    }
//}

function createOrderEmail($order_id, $toAdmin = false)
{
    $html = '';
    $order = getItemById($order_id, 'orders');
    $user = getItemById($order['user_id'], 'users');
    $table = getMyCartTable($order, $toAdmin);
    $details = getOrderDetailsData($order, $toAdmin);
//    $result = array(
//        'table' => $table,
//        'details' => $details
//    );
    $html .= '<b>Номер заказа:</b> ' . $order_id . '<br/>Дата: ' . $order['date'] . '<br/>Товары: ';
    $html .= $table . $details;

    return $html;
}

function getNewPrice($old, $discount)
{
    $new = $old - ($old / 100 * $discount);
    return $new;
}

function getPriceInCurrency($old, $discount, $currency = false)
{
    $CI = &get_instance();
    $new = $old - ($old / 100 * $discount);

    if (!$currency)
        $currency = userdata('currency');
    if (!$currency) $currency = getMainCurrency();
    $usd_to = getCurrencyValue($currency);
    if (!$usd_to) $usd_to = 1;
    $price = $new * $usd_to;

    return round($price, 2);
}

function getMainCurrency()
{
    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    return $CI->shop->getMainCurrency();
}

function getCurrencyValue($code)
{
    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    $code = strtoupper($code);
    return $CI->shop->getCurrencyValue($code);
}


function getCurrencySymb($code)
{
    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    $code = strtoupper($code);
    $currency = $CI->shop->getCurrencyByCode($code);
    if ($currency['symb']) return $currency['symb'];

    return false;
}

function getCurrencyByCode($code)
{
    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    return $CI->shop->getCurrencyByCode($code);
}

function getCurrency()
{
    $currency = userdata('currency');
    if (isset($_POST['currency']) && $_POST['currency'] != $currency) {
        set_userdata('currency', post('currency'));
        $currency = post('currency');
    }
    if (!$currency) $currency = 'uah';

    return $currency;
}

function getOrderById($id)
{
    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    return $CI->shop->getOrderById($id);
}

function isActionTime()
{
    $is_action_now = getOption('is_action_now');
    if ($is_action_now) {
        $timer_start_date = getOption('timer_start_date');
        $timer_end_date = getOption('timer_end_date');

        $sArr = explode('-', $timer_start_date);
        $eArr = explode('-', $timer_end_date);
        if (is_array($sArr) && is_array($eArr)) {
            $sUnix = mktime(0, 0, 0, $sArr[1], $sArr[2], $sArr[0]);
            $eUnix = mktime(0, 0, 0, $eArr[1], $eArr[2], $eArr[0]);
            $now = time();
            if ($now > $sUnix && $now < $eUnix)
                return true;
        }
    }
    return false;
}

function isActionNow()
{
    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    $count = $CI->shop->getActionsCount();
    if ($count > 0) return true;

    return false;
}

function isDiscount($product, $date = false)
{
    if ($product['sale'] == 1 && $product['discount'] > 0) return true;
    $time = time();
    if ($date && is_array($date)) {
        $darr = explode('-', $date);
        $time = mktime(0, 0, 0, $darr[1], $darr[2], $darr[0]);
    }
    $show = false;
    if ($product['discount'] > 0) {
        if ($product['akciya_start_unix'] != 0 && $product['akciya_end_unix'] != 0) {
            //$show = true;
            if ($time > $product['akciya_start_unix'] && $time < $product['akciya_end_unix']) {
                $show = true;
            } else $show = false;
        }
    }

    // Проверка на акцию в разделе 
    // if(!$show && $product['sale'] == 0)
    // {
    //     $cats = explode('*', $product['category_id']);
    //     if($cats)
    //     {
    //         if(!is_array($cats))
    //             $cats[0] = $cats;

    //         $count = count($cats);
    //         for($i = 0; $i < $count; $i++)
    //         {
    //             $cat = $cats[$i];
    //         }
    //     }
    // }

    return $show;
}

function upload_foto($name = 'userfile', $type = 'reviews')
{
    $CI = &get_instance();
    // Проверка наличия папки текущей даты. Если нет, то создать
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $type . '/' . date("Y-m-d") . '/')) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $type . '/' . date("Y-m-d") . '/', 0777);
    }

    //////
    // Функция загрузки и обработки фото
    $config['upload_path'] = 'upload/' . $type . '/' . date("Y-m-d");
    $config['allowed_types'] = 'jpg|png|gif|jpe';
    $config['max_size'] = '0';
    $config['max_width'] = '0';
    $config['max_height'] = '0';
    $config['encrypt_name'] = true;
    $config['overwrite'] = false;

    $CI->load->library('upload', $config);

    if (!$CI->upload->do_upload($name)) {
        echo $CI->upload->display_errors();
        die();
    } else {
        $ret = $CI->upload->data();

        $width = getOption('article_foto_max_width');
        $height = getOption('article_foto_max_height');
        if (!$width) $width = 200;
        if (!$height) $height = 200;

        if (($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
        if (($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];


        $config['image_library'] = 'GD2';
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = $width;
        $config['height'] = $height;
        $config['source_image'] = $ret["file_path"] . $ret['file_name'];
        $config['new_image'] = $ret["file_path"] . $ret['file_name'];
        $config['thumb_marker'] = '';
        $CI->image_lib->initialize($config);
        $CI->image_lib->resize();

        //copy($ret['full_path'],str_replace('/articles/','/original/',$ret['full_path']));

        // Проверяем нужен ли водяной знак на картинках в статьях
        $articles_watermark = getOption('articles_watermark');
        /*if ($articles_watermark === false)*/
        $articles_watermark = 1;
        if ($articles_watermark) {
            // Получаем файл водяного знака
            $watermark_file = getOption('watermark_file');
            if ($watermark_file === false) $watermark_file = 'img/logo.png';
            //
            // Получаем вертикальную позицию водяного знака
            $watermark_vertical_alignment = getOption('watermark_vertical_alignment');
            if ($watermark_vertical_alignment === false) $watermark_vertical_alignment = 'bottom';
            // Получаем горизонтальную водяного знака
            $watermark_horizontal_alignment = getOption('watermark_horizontal_alignment');
            if ($watermark_horizontal_alignment === false) $watermark_horizontal_alignment = 'center';
            //
            // Получаем прозрачность водяного знака
            $watermark_opacity = getOption('watermark_opacity');
            if ($watermark_opacity === false) $watermark_opacity = '20';
            //

            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['create_thumb'] = FALSE;
            $config['wm_type'] = 'overlay';
            $config['wm_opacity'] = $watermark_opacity;
            $config['wm_overlay_path'] = $watermark_file;
            $config['wm_hor_alignment'] = $watermark_horizontal_alignment;
            $config['wm_vrt_alignment'] = $watermark_vertical_alignment;
            $CI->image_lib->initialize($config);
            $CI->image_lib->watermark();
        }


        return $ret;
    }
}

function addToCart()
{
    $CI = &get_instance();
    if (isset($_POST['add_review'])) {
        if ($CI->input->post('comment') !== false && userdata('login') !== false) {
            $CI->load->model('Model_users', 'users');
            $user = $CI->users->getUserByLogin(userdata('login'));
            if ($user) {
                $rate = $CI->input->post('rate');
                $shop_id = 0;
                $article_id = 0;
                if ($CI->input->post('shop_id') != false) $shop_id = $CI->input->post('shop_id');
                if ($CI->input->post('article_id') != false) $article_id = $CI->input->post('article_id');

                $image = '';
                if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки
                    if ($_FILES['userfile']['name'] != '') {
                        $imagearr = upload_foto('userfile', 'reviews');
                        $image = '/upload/reviews/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                    }
                }

                if ($rate < 1) $rate = 5;
                $dbins = array(
                    'comment' => $CI->input->post('comment'),
                    'rate' => $rate,
                    'login' => userdata('login'),
                    'name' => $user['name'],
                    'shop_id' => $shop_id,
                    'article_id' => $article_id,
                    'user_id' => $user['id'],
                    'ip' => getRealIp(),
                    'date' => date("Y-m-d"),
                    'time' => date("H:i"),
                    'active' => 0,
                    'images' => $image
                );

                $CI->db->insert('comments', $dbins);
                //vdd($dbins);
                set_userdata('msg', "Большое спасибо за Ваш отзыв! Он появится на сайте после проверки.");
            }
        } else echo 'Ошибка пользователя';
    } elseif
    (isset($_POST['shop_id'])) {
        $CI = &get_instance();
        //var_dump($_POST);die();
        if (!isset($_POST['kolvo'])) $_POST['kolvo'] = 1;
        $my_cart = array();
        if ($CI->session->userdata('my_cart') !== false) $my_cart = $CI->session->userdata('my_cart');

        $CI->load->model('Model_shop', 'shop');
        $shop = $CI->shop->getArticleById($_POST['shop_id']);
        //$razmer = json_decode($shop['warehouse']);
        $razmer = explode('*', $shop['razmer']);
        $is_new = true;
        $count = count($my_cart);
        for ($i = 0; $i < $count; $i++) {
            if ($my_cart[$i]['shop_id'] == $_POST['shop_id']) {
                $rcount = count($razmer);
                $kolvo = 0;
                //var_dump($kolvo);
                /*
                for($i2 = 0; $i2 < $rcount; $i2++)
                {

                    if(isset($_POST['chk_kolvo_'.$razmer[$i2]]))
                    {
                        $my_cart[$i]['kolvo_'.$razmer[$i2]] = $_POST['kolvo_'.$razmer[$i2]];
                        $kolvo = $kolvo + $_POST['kolvo_'.$razmer[$i2]];
                    }
                }
                */
                if (isset($my_cart[$i]['kolvo_' . $_POST['razmer']])) {
                    $my_cart[$i]['kolvo_' . $_POST['razmer']] += $_POST['kolvo'];
                } else {
                    $my_cart[$i]['kolvo_' . $_POST['razmer']] = $_POST['kolvo'];
                }
                //var_dump($kolvo);

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
            /*
            for($i2 = 0; $i2 < $rcount; $i2++)
            {
                if(isset($_POST['chk_kolvo_'.$razmer[$i2]]))
                {
                    $new['kolvo_'.$razmer[$i2]] = $_POST['kolvo_'.$razmer[$i2]];
                    $kolvo = $kolvo + $_POST['kolvo_'.$razmer[$i2]];

                }
            }
            */
            $new['kolvo_' . $_POST['razmer']] = $_POST['kolvo'];
            $new['kolvo'] = $_POST['kolvo'];

            array_push($my_cart, $new);
        }

        $cat = $CI->model_categories->getCategoryById($shop['category_id']);
        set_userdata('action_add_to_cart', $_POST['shop_id']);
        set_userdata('action_add_to_cart_name', $shop['name']);
        set_userdata('action_add_to_cart_price', $shop['price']);
        set_userdata('action_add_to_cart_category', $cat['name']);
        set_userdata('action_add_to_cart_razmer', $_POST['razmer']);
        set_userdata('action_add_to_cart_kolvo', $_POST['kolvo']);

        $CI->session->set_userdata('my_cart', $my_cart);

        $my_cart = $CI->session->userdata('my_cart');

    }
}

function showProductInCategory($art, $i)
{
    $CI = &get_instance();

    $currency = userdata('currency');
    if (!$currency) $currency = 'uah';

    //debug($art['discountNow']);
    // ЕСТЬ ЛИ АКЦИЯ
    $art['discountNow'] = isDiscount($art);

//debug($art['discountNow']);
    $img2 = false;
    $images = $CI->model_images->getByShopId($art['id'], 1, -1, 'DESC');
    if (isset($images[0])) $img2 = $images[0]['image'];

    $darr = explode('-', $art['date']);
    $cat = $CI->model_categories->getCategoryById($art['category_id']);
    ?>


    <div class="shop-block-cat-<?= $i ?>">
        <?php
        $show = false;
        $art['price'] = round($art['price'], 2);
        if ($art['image'] != '') {
            ?>
            <div class="shop-img">

                <div class="details_shop<?php if ($art['discountNow']) echo " discount"; ?>">
                    <p>
                        <span сдass="price-usd"<?php if ($currency != 'usd') echo ' style="display: none"'; ?>><?= $art['price'] ?>
                            $</span>

                        <span class="price-uah"<?php if ($currency != 'uah') echo ' style="display: none"'; ?>><?php
                            $currensy_grn = getCurrencyValue('UAH');
                            echo round($art['price'] * $currensy_grn, 2);
                            ?> грн
                        </span>
                        <span class="price-rub"<?php if ($currency != 'rub') echo ' style="display: none"'; ?>><?php
                            $currensy_rub = getCurrencyValue('RUB');
                            echo round($art['price'] * $currensy_rub, 2);
                            ?> руб
                        </span>
                    </p>
                </div>
                <a href="/<?= $cat['url'] ?>/<?= $art['url'] ?>/">
                    <div class="temphol">

                        <?php
                        if ($img2) {
                            ?>
                            <img class='tovar' alt="<?= $art['name'] ?> - 2" title="<?= $art['name'] ?>"
                                 src="<?= CreateThumb(210, 310, $img2, 'shop_category') ?>" width="210px" height="310px"
                                 border="0"/>
                            <?php
                        } else {
                            ?>
                            <img class="tovar" alt="<?= $art['name'] ?>" title="<?= $art['name'] ?>"
                                 src="<?= CreateThumb(210, 310, $art['image'], 'shop_category') ?>" width="210px"
                                 height="310px" border="0"/>
                            <?php
                        }
                        ?>
                        <img class="front" alt="<?= $art['name'] ?>" title="<?= $art['name'] ?>"
                             src="<?= CreateThumb(210, 310, $art['image'], 'shop_category') ?>" width="210px"
                             height="310px" border="0"/>
                        <?php
                        if ($art['discountNow']) {
                            ?>
                            <img class="discount-img" src="/img/sale/<?= $art['discount'] ?>.png"/>
                            <?php
                        }
                        ?>
                    </div>

                    <?php
                    //if($art['akciya'] == 1)
                    if ($show) {
                        ?>
                        <img class="akciya" src="/img/action.png" alt="Акция" title="Акция"/>
                        <?php
                    }
                    ?>
                </a>

            </div>
            <div class="name-and-art">
                <div class="naa-n"><?= $art['name'] ?></div>
                <div class="naa-a">Арт.:<?= $art['articul'] ?></div>
            </div>
            <?php
        }
        ?>
        <div class="news-cnt-wrap">


        </div>
        <form class="catalog_desc" method="post" action="/add_to_cart/">
            <input type="hidden" name="shop_id" value="<?= $art['id'] ?>"/>
            <input type="hidden" name="back" value="<?= $_SERVER['REQUEST_URI'] ?>"/>

            <div class="sizeandnumber">
                <div class="size">
                    <select name="razmer" required>
                        <option value="">Размер</option>
                        <?php
                        $razmer = explode('*', $art['razmer']);
                        if (is_array($razmer)) {
                            $countj = count($razmer);
                            for ($ij = 0; $ij < $countj; $ij++) {
                                echo '<option value="' . $razmer[$ij] . '">' . $razmer[$ij] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="number">
                    <span>Кол-во (шт.):</span>&nbsp; &nbsp;<input required type="text" name="kolvo" value="1"/> <span
                            class="minus"></span><span class="plus"></span>
                </div>
                <div class="clear"></div>
            </div>
            <div class="sendtocart">
                <a id="shop-<?= $art['id'] ?>">
                    <div class="incart"><input type="submit" value="В корзину"/></div>
                </a>
            </div>
        </form>

    </div>
    <?php
}

function showMiniProduct($art, $i, $count)
{
    $CI = &get_instance();
    $img2 = false;

    $currency = userdata('currency');
    if (!$currency) $currency = 'uah';

    $art['price'] = round($art['price'], 2);
    $images = $CI->model_images->getByShopId($art['id'], 1, -1, 'DESC');
    if (isset($images[0])) $img2 = $images[0]['image'];

    $cat = $CI->model_categories->getCategoryById($art['category_id']);
    ?>
    <div class="novinki<?php if (($i + 1) == $count) echo ' last'; ?>">

        <a href="#">
            <div class="details1">
                <center style="padding-top: 3px;">
                    <?php
                    $currensy_grn = getCurrencyValue('UAH');
                    $currensy_rub = getCurrencyValue('RUB');
                    ?>
                    <span style="font-size: 14px;">
                        <span сlass="curval price-usd"<?php if ($currency != 'usd') echo ' style="display: none"'; ?>><?= $art['price'] ?>
                            $</span>
                        <span сlass="curval price-uah"<?php if ($currency != 'uah') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_grn, 2) ?>
                            грн</span>
                        <span сlass="curval price-rub"<?php if ($currency != 'rub') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_rub, 2) ?>
                            р</span>

                    </span>
                </center>
            </div>
        </a>
        <a href="/<?= $cat['url'] ?>/<?= $art['url'] ?>/">
            <div class="temphol">
                <?php
                if ($img2) {
                    ?>
                    <img class='tovar' alt="<?= $art['name'] ?> - 2" title="<?= $art['name'] ?>"
                         src="<?= CreateThumb(174, 275, $img2, 'shop') ?>" width="174px" height="275px" border="0"/>
                    <?php
                } else {
                    ?>
                    <img class="tovar" alt="<?= $art['name'] ?>" title="<?= $art['name'] ?>"
                         src="<?= CreateThumb(174, 275, $art['image'], 'shop') ?>" width="174px" height="275px"
                         border="0"/>
                    <?php
                }
                ?>
                <img class="front" alt="<?= $art['name'] ?>" title="<?= $art['name'] ?>"
                     src="<?= CreateThumb(174, 275, $art['image'], 'shop_category') ?>" width="174px" height="275px"
                     border="0"/>

            </div>
        </a>
    </div>
    <?php
}

function showProduct($art)
{
    $CI = &get_instance();

    $currency = userdata('currency');
    if (!$currency) $currency = 'uah';

    $art['price'] = round($art['price'], 2);
    $img2 = false;
    $images = $CI->model_images->getByShopId($art['id'], 1, -1, 'DESC');
    if (isset($images[0])) $img2 = $images[0]['image'];
    else $img2 = $art['image'];

    $cat = $CI->model_categories->getCategoryById($art['category_id']);
    ?>
    <div class="product-itm">
        <a target="_blank" href="/<?= $cat['url'] ?>/<?= $art['url'] ?>/">
            <img alt="<?= $art['name'] ?> - 2" title="<?= $art['name'] ?>"
                 src="<?= CreateThumb(172, 262, $img2, 'products') ?>"/>
            <img class="front" src="<?= CreateThumb(172, 262, $art['image'], 'products') ?>" alt="<?= $art['name'] ?>"
                 title="<?= $art['name'] ?>">
        </a>
        <div class="description-itm">
            <span class="itm-title">
                <a href="/<?= $cat['url'] ?>/<?= $art['url'] ?>/">
                    <span><?= $art['name'] ?></span><span><?= $art['articul'] ?></span>
                </a>
            </span>

            <p class="itm-price">
                <?php
                $currensy_grn = getCurrencyValue('UAH');
                $currensy_rub = getCurrencyValue('RUB');
                ?>
                <span style="font-size: 14px;">
                    <span сlass="curval price-usd"<?php if ($currency != 'usd') echo ' style="display: none"'; ?>><?= $art['price'] ?>
                        $</span>
                    <span сlass="curval price-uah"<?php if ($currency != 'uah') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_grn, 2) ?>
                        грн</span>
                    <span сlass="curval price-rub"<?php if ($currency != 'rub') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_rub, 2) ?>
                        р</span>
                </span>
            </p>
        </div>
    </div>
    <?php
}

function getProductHtml($art, $cat = false, $fastOrderJs = false, $fast_order = true)
{
    //vd($fast_order);

    $currency = userdata('currency');
    if (!$currency) $currency = 'uah';

    if ($cat['url'] == 'all') $cat = false;
    $art['price'] = round($art['price'], 2);
    $html = "";
    $CI = &get_instance();
    $art['discountNow'] = $discount = isDiscount($art);
    if ($art['discountNow'] == true && $art['image_no_logo'] != '') $art['image'] = $art['image_no_logo'];
    $price_class_adding = '';
    if ($discount) $price_class_adding = ' old-price';
    $img2 = false;
    if ($art['discountNow'] == true) {
        $images = $CI->model_images->getByShopId($art['id'], 1, 0, 'DESC');
        if (!isset($images[0]))
            $images = $CI->model_images->getByShopId($art['id'], 1, 1, 'DESC');
    } else {
        $images = $CI->model_images->getByShopId($art['id'], 1, 1, 'DESC');
    }
    if (isset($images[0])) $img2 = $images[0]['image'];
    else $img2 = $art['image'];

    if (!$cat)
        $cat = $CI->model_categories->getCategoryById($art['category_id']);
    $html = '
    <div class = "product-itm">';

    if ($fast_order)
        $html .= '<span class="fast-order-a" data-target=".modal-fast-order" data-toggle="modal" shop_id="' . $art['id'] . '">Быстрый заказ</span>';

    $html .= '<a target="_blank" href="/' . $cat['url'] . '/' . $art['url'] . '/">
            <img alt="' . $art['name'] . ' - 2" title="' . $art['name'] . '" src="' . CreateThumb(172, 262, $img2, 'products') . '" />
            <img class="front" src = "' . CreateThumb(172, 262, $art['image'], 'products') . '" alt="' . $art['name'] . '" title="' . $art['name'] . '">
        ';

    if ($discount) {
        $html .= '<img class="discount-img" src="/img/sale/' . $art['discount'] . '.png" alt="Скидка ' . $art['discount'] . '%" />';
    }

    $html .= '</a>
        <div class = "description-itm">
            <span class = "itm-title">
                <a href="/' . $cat['url'] . '/' . $art['url'] . '/">
                    <span>' . $art['name'] . '</span><span>' . $art['articul'] . '</span>
                </a>
            </span>

            <p class = "itm-price' . $price_class_adding . '"';
    if ($price_class_adding == 'old-price') $html .= ' title="На данный товар действует скидка - ' . $art['discount'] . ' %!"';
    $html .= '>';
    $currency = userdata('currency');
    $currencySymb = '$';
    if ($currency == 'uah') $currencySymb = ' грн';
    elseif ($currency == 'rub') $currencySymb = ' р';
    if (!$currency) $currency = 'uah';
    $currensy_grn = getCurrencyValue('UAH');
    $currensy_rub = getCurrencyValue('RUB');
    ob_start();
    ?>
    <span class="product_price" product_id="<?= $art['id'] ?>" usd="<?= $art['price'] ?> $"
          uah="<?= getPriceInCurrency($art['price'], 0, 'uah') ?> грн"
          rub="<?= getPriceInCurrency($art['price'], 0, 'rub') ?> р">
        <?= getPriceInCurrency($art['price'], 0, $currency) ?> <?= $currencySymb ?>
    </span>
    <!--span сlass="curval price-usd"<?php if ($currency != 'usd') echo ' style="display: none"'; ?>><?= $art['price'] ?>$</span>
    <span сlass="curval price-uah"<?php if ($currency != 'uah') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_grn, 2) ?> грн</span>
    <span сlass="curval price-rub"<?php if ($currency != 'rub') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_rub, 2) ?> р</span-->
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    $html .= $output;

    $html .= '
            </p>
        </div>
    </div>
    ';

    if ($fastOrderJs) {
        $html .= '<script src = "/js/jquery.min.js"></script>
<script>
	jQuery(document).ready(function(){

		jQuery(\'.fast-order-a\').click(
			function(){
				//alert(\'start load 2\');
				jQuery("#fast-order-content").html("");
				var shop_id = jQuery(this).attr("shop_id");

				jQuery.ajax({
					/* адрес файла-обработчика запроса */
					url: \'/ajax/get_fast_order/\',
					/* метод отправки данных */
					method: \'POST\',
					/* данные, которые мы передаем в файл-обработчик */
					data: {
						"shop_id": shop_id,
						"request_uri": "<?=$_SERVER[\'REQUEST_URI\']?>"
					},

				}).done(function (data) {
					//alert(\'end load 2\');
					jQuery("#fast-order-content").html(data);
				});
			}

		);
	});


</script>
';
    }
    return $html;
}

function getFastOrderHtml($art, $cat = false, $needAjax = false)
{
    $currency = userdata('currency');
    if (!$currency) $currency = 'uah';
    $html = "";
    $CI = &get_instance();
    if (isDiscount($art)) {
        $art['discountNow'] = true;
    }
    // Округляем цену
    $art['price'] = round($art['price'], 2);
    // Подгружаем курс валют
    $currensy_grn = getCurrencyValue('UAH');
    $currensy_rub = getCurrencyValue('RUB');

    if (!$cat) $cat = $CI->model_categories->getCategoryById($art['category_id']);
    //if($art['rating'] == 0) $art['rating'] = 5;
    if ($art['rating'] > 0) {
        $CI->load->helpers('modules_helper');
        $html .= getRatingHtnl($art, 'fast-rating');
    }
    $html .= '
    <section class="container cart">
            <img class="fast-order-image" src="' . $art['image'] . '" />

        <div class = "fast-order-desc itm-desc">

            <h2>' . $cat['name'] . ' <span>' . $art['name'] . '</span> (' . $art['color'] . ')' . isAdminEdit($art['id']) . '</h2>
            <p class = "all-price';
    if (isset($art['discountNow']))
        $html .= ' old-price';
    $html .= '">';
    $currensy_grn = getCurrencyValue('UAH');
    $currensy_rub = getCurrencyValue('RUB');
    ob_start();
    ?>
    <span сlass="curval price-usd"<?php if ($currency != 'usd') echo ' style="display: none"'; ?>><?= $art['price'] ?>
        $</span>
    <span сlass="curval price-uah"<?php if ($currency != 'uah') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_grn, 2) ?>
        грн</span>
    <span сlass="curval price-rub"<?php if ($currency != 'rub') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_rub, 2) ?>
        р</span>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    $html .= $output;
    $html .= '</p>
';

    if (isset($art['discountNow'])) {
        $art['price'] = getNewPrice($art['price'], $art['discount']);
        $html .= '
                <p class = "all-price new-price">';

        ob_start();
        ?>
        <span сlass="curval price-usd"<?php if ($currency != 'usd') echo ' style="display: none"'; ?>><?= $art['price'] ?>
            $</span>
        <span сlass="curval price-uah"<?php if ($currency != 'uah') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_grn, 2) ?>
            грн</span>
        <span сlass="curval price-rub"<?php if ($currency != 'rub') echo ' style="display: none"'; ?>><?= round($art['price'] * $currensy_rub, 2) ?>
            р</span>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        $html .= $output;

        $html .= '</p>';

    }
    ?>
    <!--		<span class = "itm-code">--><?//= $article['articul']
    ?><!--</span>-->


    <?php
    $razmer = explode('*', $art['razmer']);
    $warehouse = json_decode($art['warehouse'], true);

    $razm = "";
    if (is_array($razmer)) {
        sort($razmer);
        foreach ($razmer as $r) {
            $available = 'true';
            if (($warehouse != NULL) && isset($warehouse[$r])) $available = $warehouse[$r];
            else $available = -1;

            $classes = 'harki-param';
            if (!$available) $classes .= ' not-available';
            else $classes .= ' available';

            $razm .= '<span class="' . $classes . '"';
            if ((!$available) && $art['sale'] == 1) $razm .= ' style="display:none;"';
            elseif ($available == -1) $razm .= ' style="display:none;"';

            $razm .= '>' . $r . ';</span>&nbsp;';
        }
    }
    $html .= '
            <div class="clr"></div>
            <div class="clr"></div>
            <p style="margin-top: -10px">
                <strong>Ткань</strong>: ' . $art['tkan'] . '<br />';

    if ($art['sostav']) {
        $html .= '<strong>Состав</strong>: ';
        $sostav = str_replace("\n", "<br>", $art['sostav']);
        $html .= $sostav;
        $html .= '<br />';
    }
    $html .= '<strong>Размерный ряд</strong>: ' . $razm . '
            </p>


            <form id="formx" method = "post" action="javascript:void(null);" class = "cart-form">
                <input type = "hidden" name = "shop_id" value = "' . $art['id'] . '"/>
                <input type = "hidden" name = "back" value = "' . $_SERVER['REQUEST_URI'] . '"/>

                <span>Размер</span>

                <select id="razmer" name = "razmer">
                    <option></option>
';
    if (is_array($razmer)) {

        $count = count($razmer);

        for ($i = 0; $i < $count; $i++) {
            $r = $razmer[$i];

            // Остаток по каждому размеру
            $available = 'true';
            if (isset($warehouse[$r])) $available = $warehouse[$r];
            else $available = -1;

            // Нет на складе

            $class = '';
            if (($art['warehouse'] != NULL) && $available == 0) $class = 'class="not-available"';
            elseif ($available == -1) $class = 'class="availible-unknoun"';

            $html .= '<option ' . $class . ' available="' . $available . '" value = "' . $razmer[$i] . '"';
            if ((!$available) && $art['sale'] == 1) $html .= 'style="display:none;"';
            elseif (!$available) $html .= ' disabled ';
            $html .= '>' . $razmer[$i] . '</option>';

        }
    }
    $html .= '
                </select>
                <div id="razmer-err" class="form-error" style="display: none">Вы не указали размер!</div>
                <div id="kolvo-err" class="form-error" style="display: none">Вы не указали количество!</div>

                <div class="to-normal-cart">
                    <a href="' . getFullUrl($art) . '">Перейти на страницу товара >></a>
                </div>
';


    if ($art['ended'] == 1) {
        $html .= '
            <div class="ended">
                Товар закончился и больше не выпускается
            </div>';
    } else {

        if ($art['warehouse_sum'] > 0) {
            $html .= ' <div class = "cart-count">
                    <span>Кол-во</span>
                    <input pattern="^[ 0-9]+$" id="kolvo" type = "text" placeholder = "Кол-во" value = "1" name = "kolvo" onkeyup="this.value=this.value.replace(/[^0-9]+/g,\'\'); isright(this);">
                    <button onclick = "yaCounter26267973.reachGoal(\'kupit\'); ga(\'send\', \'event\', \'kupit\', \'click\'); fast_order_to_cart();">В корзину<span class = "icon-cart"></button>
                    <input type="hidden" id="one_click_now" value="0" />
                    <form id="fast-order">
                        <div class="one-click-div">
                            <button id="button_goto_one_click" onclick="fast_order_to_cart(true)">Купить  в  1  клик</button>            
                        </div>
                    </form>
        
                    <script>
                    function setBuyOneClick(){
                        $("#one_click_now").val(1);
                        return false;
                    }
                    </script>
        
                </div>';
        } else {
            $html .= '
                <div class="not_in_warehouse">
                    Товар закончился
                    <!--a id="uvedomit" onclick="uvedomit()">Уведомить о поступлении</a-->
                </div>';
        }
    }

    $html .= '
    <script type="text/javascript" language="javascript">
        function uvedomit()
        {
        //jQuery(\'#modal_not_available\').modal(\'show\');
        //closeModal();
        }
     	function fast_order_to_cart(oneClick = false) {
     	    if(j("#razmer").val() == ""){
     	        j("#razmer-err").show();
     	         setTimeout(hideErrors, 4000);
     	    } else if(j("#kolvo").val() == "" || j("#kolvo").val() == "0")
     	    {
     	        j("#kolvo-err").show();
     	        setTimeout(hideErrors, 4000);
     	    } else if(isMaxKolvo()){
                 var available = j( "#razmer option:selected").attr("available");
                j("#kolvo").val(available);
                jQuery(\'#modal_max_available\').modal(\'show\');                 
     	    }else
            {
            //alert(j("#razmer").val());
            //alert(j("#kolvo").val());
                var msg   = j(\'#formx\').serialize();
                j.ajax({
                  type: \'POST\',
                  url: \'/ajax/to_cart/\',
                  data: msg,
                  success: function(data) {
                      if(oneClick == false){
                        j(\'#fast-order-content\').html("Товар успешно добавлен в корзину!");
                        j("#my_cart_count").html(data);
                        setTimeout(closeModal, 1500);
                    } else {
                          j(\'#fast-order-content\').html("Загружаем форму быстрого заказа..."); 
                          j.ajax({
                            url: \'/ajax/fast_order/\',
                            method: \'post\',
                            async: false,
                            data: {
                                \'type\': "my_cart"
                            },
                    
                        }).done(function (data) {
                            j("#fast-order-content").html(data);
                        });
                                                 
                    }
                    //alert(data);
                  },
                  error:  function(xhr, str){
                    alert(\'Возникла ошибка: \' + xhr.responseCode);
                  }
                });
            }
        }
        
        function hideErrors(){
			j(\'#razmer-err\').hide();
			j(\'#kolvo-err\').hide();
		}
        function closeModal(){            
			j(\'#fast-order\').modal(\'hide\');
			j(\'.modal-backdrop\').hide();
		}

    </script>

            </form>

        </div>
    </section>';

    $html .= '<script>
		var maxKolvo = ' . $art['warehouse_sum'] . '

    var j = jQuery.noConflict();
    j( "#razmer" )
    .change(function () {

    var available = j( "#razmer option:selected").attr("available");
    if(available == 0) {
        j("#kolvo").val("0");
        jQuery(\'#modal_not_available\').modal(\'show\');
    }
    if(available > 0) {
        var kolvo = parseInt(j("#kolvo").val());
        if(kolvo > available)
        {
                j("#kolvo").val(available);
        }
    }
    })
    .change();

    j( "#kolvo" )
    .change(function () {
    //alert(available + "=" + j("#kolvo").val());
    if(isMaxKolvo()){
            j("#kolvo").val(available);
            jQuery(\'#modal_max_available\').modal(\'show\');
    }
    })
    .change();
            
     function isMaxKolvo(){
         var available = j( "#razmer option:selected").attr("available");
         var kolvo = parseInt(j("#kolvo").val());
         if(kolvo > available) return true;
         return false;
     }
    </script>';

    $user = getCurrentUser();
    if ($user)
//vd($user);
        $message = "";
    $email = '';
    if ($user) {
        $email = $user['email'];

        $message = '<section class="available-me-form"><p>К сожалению, на данный момент этого размера нет в наличии.</p>
<p>Сообщить, когда появится:</p>
<input class="itm-desc" type="email" required name="email" placeholder="e-mail" id="say_me_available_email" value="' . $email . '" /><button class="say-me" id="say_me_available">OK</button>
</section>';
    } else {
        $message = '<section class="available-me-form"><p>К сожалению, на данный момент этого размера нет в наличии.</p>
<p>Сообщить, когда появится:</p>
            <p style="font-size: 12px;">Выберите любую, удобную для Вас, соц. сеть, либо почтовую службу:</p>
			<script src="//ulogin.ru/js/ulogin.js"></script>
            <div id="uLogin08292de1" data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=google,yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=//' . $_SERVER['SERVER_NAME'] . '/login/soc/"></div>';
    }

    $html .= '


<div class="modal fade bs-example-modal-sm" id="modal_not_available" tabindex="-1" role="dialog"
aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm registration-modal">
    <div class="modal-content">
    <button class="close" type="button" onclick="onAjaxSuccess()">&times;</button>
<div class="after-autorization">
    ' . $message . '
    </div>
    </div>
    </div>
    </div>
    <script>
    j(\'#say_me_available\').on(\'click\', function () {
        var email = j(\'#say_me_available_email\').val();
        if(j(\'#say_me_available_email\').val() != "")
        {
            j.post(
                "/ajax/say_me_available/",
                {
                    shop_id: ' . $art['id'] . ',
                    razmer: j("#razmer").val(),
                    email: j("#say_me_available_email").val(),
                    url: "http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '"
                },
                onAjaxSuccess
            );
        }else alert(\'Укажите правильный e-mail!\');
    });

function onAjaxSuccess(data) {
    j("#modal_not_available").modal(\'hide\');
}

function modal_not_available(data) {
    j("#modal_not_available").modal(\'hide\');
}
function modal_max_available(data) {
    j("#modal_max_available").modal(\'hide\');
}
    </script>';


    $message = 'Это максимально допустимое количество данного размера';
    $html .= getModalDialog('modal_max_available', $message, true);

    return $html;

}

function addOrder($order)
{
    $CI = &get_instance();
    $CI->db->insert('orders', $order);
}

function editOrderStatus($order_id, $status)
{
    $CI = &get_instance();
    $dbins = array('status' => $status);
    $CI->db->where('id', $order_id)->limit(1)->update('orders', $dbins);
}

function getArticleHtml($art, $cat = false)
{
    $CI = &get_instance();
    if ($art['short_content'] == '')
        $art['short_content'] = string_limit_words($art['content'], 20);

    if (!$cat)
        $cat = $CI->model_categories->getCategoryById($art['category_id']);
    $url = '/' . $cat['url'] . '/' . $art['url'] . '/';
    $html = '<article class = "itm-news">';
    if ($art['image'] != '') {
        $html .= '<a class="new-image-url" href = "' . $url . '">';
        $html .= '<img alt="' . $art['name'] . '" title="' . $art['name'] . '" src="' . CreateThumb(270, 180, $art['image'], 'news') . '">';
        $html .= "</a>";
    }
    $html .= '<a href="' . $url . '" class="new-h2-url"><h2>' . $art['name'] . '</h2></a>';
    $html .= '<p>' . strip_tags($art['short_content']) . '</p>';
    $html .= '<a href="' . $url . '" class="podrobnee">Подробнее</a></article>';
    return $html;
}

function getMyCartCount()
{
    $CI = &get_instance();

    $my_cart = array();
    if ($CI->session->userdata('my_cart') !== false) $my_cart = $CI->session->userdata('my_cart');

    echo count($my_cart);
}

function isAkciya($article, $date = false)
{
    $unix = time();
    $show = isDiscount($article, $date);
//    if($date && is_array($date))
//    {
//        $darr = explode('-', $date);
//        $unix = mktime(0,0,0,$darr[1],$darr[2],$darr[0]);
//    }
//    $show = false;
//    if($article['old_price'] != '' && $article['old_price'] != 0)
//    {
//        //debug("1");
//        if($article['akciya_start_unix'] != 0 && $article['akciya_end_unix'] != 0)
//        {
//            //$show = true;
//            if($unix > $article['akciya_start_unix'] && $unix < $article['akciya_end_unix'])
//            {
//                //debug("2");
//                $show = true;
//            }
//            else $show = false;
//        }
//        elseif($article['akciya_start_unix'] != 0 && $article['akciya_end_unix'] == 0)
//        {
//            //debug("3");
//            if($unix > $article['akciya_start_unix'])
//            {
//                //debug("4");
//                $show = true;
//            }
//        }
//        elseif($article['akciya_start_unix'] == 0 && $article['akciya_end_unix'] != 0)
//        {
//            //debug("5");
//            if($unix < $article['akciya_end_unix'])
//            {
//                //debug("6");
//                $show = true;
//            }
//        }
//        else
//        {
//            //debug("7");
//            $show = true;
//        }
//    }
    return $show;
}

function getAkciyaPrice($article, $date = false)
{
    $ret = 0;
    if (isDiscount($article))
        $ret = getNewPrice($article['price'], $article['discount']);
    else
        $ret = $article['price'];
    return round($ret, 2);
}

function set_currency()
{
    if (isset($_GET['currency'])) {
        $CI = &get_instance();
        $CI->session->set_userdata('currency', $_GET['currency']);

        $back = $_SERVER['REQUEST_URI'];
        $pos = strpos($back, '?');
        if ($pos)
            $back = substr($back, 0, $pos);

        redirect($back);
    }
}

function get_price($price, $currency = false)
{
    $CI = &get_instance();
    if(!$currency) {
        $currency = $main_currency = getMainCurrency();
        if (isset($_POST['currency'])) $currency = $_POST['currency'];
        else $currency = $CI->session->userdata('currency');
    }
    if ($currency) {
        if ($currency != $main_currency) {
            if ($currency == 'ГРН' || $currency == 'uah')
                $usd_to_uah = getCurrencyValue('UAH');
            elseif ($currency == 'РУБ' || $currency == 'rub')
                $usd_to_uah = getCurrencyValue('RUB');
            elseif ($currency == 'USD' || $currency == 'usd')
                $usd_to_uah = getCurrencyValue('USD');

            $price = $price * $usd_to_uah;
        }
    }
    $price = round($price, 2);

    return $price;
}

function getCurrencyTypeValue($currency)
{
    $cur = getCurrencyByCode($currency);
    if (isset($cur['value']))
        return $cur['value'];

    return false;
}

function getStatus($status)
{
    $status = str_replace('new', 'Новый', $status);
    $status = str_replace('processing', 'В обработке', $status);
    $status = str_replace('one_click', 'Заказ в 1 клик', $status);
    $status = str_replace('npnp_not_payed', 'Ожидается предоплата', $status);
    $status = str_replace('not_payed', 'Оплата не прошла', $status);
    $status = str_replace('npnp_payed', 'Предоплата получена', $status);
    $status = str_replace('payed', 'Оплачен', $status);
    $status = str_replace('wait_accept', 'Ожидает подтверждения оплаты', $status);
    $status = str_replace('reversed', 'Деньги возвращены', $status);
    $status = str_replace('process', 'Платёж обрабатывается', $status);
    $status = str_replace('sended', 'Отправлен', $status);
    $status = str_replace('done', 'Готово', $status);
    $status = str_replace('canceled', 'Отменён', $status);
    $status = str_replace('fail', 'Ошибка платежа', $status);


    return $status;
}

function isNeedPay($order)
{
    $goPay = false;

    $status = $order['status'];

    if ($status != 'payed' && $status != 'npnp_payed' && $status != 'sended' && $status != 'canceled' && $status != 'done') return true;
    return false;
}

function setStatus($order_id, $status)
{
    $CI = &get_instance();
    $CI->db->where('id', $order_id)->limit(1)->update('orders', array('status' => $status));
}

function shop_count($products = false)
{
    $CI = &get_instance();
    $all = 0;
    $my_cart = array();

    if ((!$products) && $CI->session->userdata('my_cart') !== false) $my_cart = $CI->session->userdata('my_cart');
    else $my_cart = $products;

    $count = count($my_cart);

    for ($i = 0; $i < $count; $i++) {
        $mc = $my_cart[$i];
        $shop = $CI->model_shop->getArticleById($mc['shop_id']);
        $razmer = explode('*', $shop['razmer']);
        $rcount = count($razmer);
        for ($i2 = 0; $i2 < $rcount; $i2++) {
            if (isset($mc['kolvo_' . $razmer[$i2]])) {

                $all += $mc['kolvo_' . $razmer[$i2]];
            }
        }
    }

    return $all;
}

function shop_sizes_count($product)
{
    $CI = &get_instance();
    $all = 0;
    //vd("asd");
    $razmer = explode('|', getOption('sizes'));
    $rcount = count($razmer);
    $product['kolvo'] = 0;
    //vd($product['kolvo_48']);
    for ($i2 = 0; $i2 < $rcount; $i2++) {
        if (isset($product['kolvo_' . $razmer[$i2]])) {
//            vd($product['kolvo_' . $razmer[$i2]]);
            //echo $razmer[$i2] . ': ' . $product['kolvo_' . $razmer[$i2]] . '<br />';
            $product['kolvo'] += $product['kolvo_' . $razmer[$i2]];
        }
    }
    return $product['kolvo'];
}

// Создание скидочного купона
function createCoupon($params = false)
{
    $CI = &get_instance();
    $CI->load->model('Model_coupons', 'coupons');
    $dbins = array();

    if (isset($params['discount'])) $dbins['discount'] = $params['discount'];
    if (isset($params['start_date'])) $dbins['start_date'] = $params['start_date'];
    if (isset($params['end_date'])) $dbins['end_date'] = $params['end_date'];
    if (isset($params['user_login'])) $dbins['user_login'] = $params['user_login'];
    if (isset($params['info'])) $dbins['info'] = $params['info'];
    if (isset($params['multi'])) $dbins['multi'] = $params['multi'];
    if (isset($params['gived_by'])) $dbins['gived_by'] = $params['gived_by'];

    $dbins['created_date'] = date("Y-m-d");
    $dbins['active'] = 1;

    $code = getRandCode(6, 10, false);

    $dbins['code'] = $code;  // генерируем случайный код
    $old = $CI->coupons->getByCode($dbins['code']);
    while ($old != false) { // проверяем, нет ли в базе такого кода. если есть, генерируем новый.
        $dbins['code'] = getRandCode(6, 10, false);
        $old = $CI->coupons->getByCode($dbins['code']);
    }

    $CI->db->insert('coupons', $dbins);

    return $dbins['code'];
}

function getCoupon($coupon)
{
    $CI = &get_instance();
    $CI->load->model('Model_coupons', 'coupons');
    return $CI->coupons->getByCode($coupon);
}

function couponCheck($coupon)
{
    $info = "";
    $err = '';
    $user = false;
    if ($coupon['start_date'] != false && $coupon['end_date'] != false) {
        $unix_now = time();
        $arr = explode('-', $coupon['start_date']);
        if (is_array($arr)) {
            $unix_start_date = mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
            if ($unix_now < $unix_start_date) $err .= 'Купон не действителен! Причина: ещё рано!<br />';
        }

        $arr = explode('-', $coupon['end_date']);
        if (is_array($arr)) {
            $unix_end_date = mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
            if ($unix_now > $unix_end_date) $err .= 'Купон не действителен! Причина: Купон просрочен!<br />';
        }
        $info .= "Срок действия купона: " . $coupon['start_date'] . " - " . $coupon['end_date'] . '<br />';
    }

    if ($coupon['user_login'] != "") $info .= 'Купон персональный<br/>';

    if (($coupon['user_login'] != false) && ($coupon['user_login'] != userdata('login'))) $err .= 'Купон не действителен! Причина: купон персональный<br />';
    if ($coupon['used_date'] != false && $coupon['multi'] == 0) $err .= 'Купон не действителен! Причина: Купон уже был использован<br />';
    if ($coupon['active'] != 1) $err .= 'Купон не действителен! Причина: Купон не активен<br />';

    $CI = &get_instance();
    $CI->db->where('coupon_id', $coupon['id']);
    $CI->db->where('user_login', userdata('login'));
    $CI->db->limit(1);
    $using = $CI->db->get('coupons_using')->result_array();

    //vdd($using);
    if ($using) $err .= 'Купон уже был использован Вами ранее!<br />';
    if ($err != '') $coupon['err'] = $err;
    if ($coupon['info'] == NULL) {
        $dbins['info'] = $info;
    }
    // vd($coupon);
    return $coupon;
}

function showAllColors($name, $id = false)
{
    $CI = &get_instance();
    $CI->db->where('active', 1);
    if ($id) $CI->db->where('id <>', $id);
    $CI->db->where('name', $name);
    $colors = $CI->db->get('shop')->result_array();

    if ($colors) {
        ?>
        <span class="itm-info-title">Другие цвета</span>
        <div class="shop-colors">
            <?php
            $count = count($colors);
            for ($i = 0; $i < $count; $i++) {
                $col = $colors[$i];
                $cat = $CI->model_categories->getCategoryById($col['category_id']);
                // 55x80
                ?>
                <div class="shop-color<?php if ($col['id'] == $id) echo ' shop-active'; ?>">
                    <a href="/<?= $cat['url'] ?>/<?= $col['url'] ?>/"><img
                                src="<?= CreateThumb(55, 80, $col['image'], 'shop_55x80') ?>"
                                alt="<?= $col['name'] ?> (<?= $col['color'] ?>)"
                                title="<?= $col['name'] ?> (<?= $col['color'] ?>)"/></a>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
}

function getNewOrdersCount()
{
    $CI = &get_instance();
    $CI->db->where('viewed', 0);
    $CI->db->from('orders');
    return $CI->db->count_all_results();
}

