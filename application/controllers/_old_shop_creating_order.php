<?php
/**
 * Created by PhpStorm.
 * User: XomiaK
 * Date: 06.05.2017
 * Time: 14:10
 */
// Проверяем, заказ в 1 клик или нет
$oneClickOrder = false;
$one_click_tel = post('one_click_tel');
if(isset($_POST['one_click_tel']) && post('one_click_tel') != false)
    $oneClickOrder = true;

// **

$coupon = false;

if (userdata('coupon') !== false) $coupon = userdata('coupon');
if ($coupon) {

    $coupon = couponCheck($coupon);
    if (!isset($coupon['err'])) {
        set_userdata('coupon', $coupon);
    } else {
        set_userdata($coupon['err']);
        redirect('/my_cart/');
    }
}
//vd($coupon);
//vdd($_POST);
$this->load->helper('geoip_helper');
$country = getUserCountry();
if ($country != 'other') {
    $country = $this->shop->getCountryById($country);
    if (isset($country['name'])) $country = $country['name'];
} else $country = $_POST['other_country'];

if (!$user) {
    //vd("94 no user");
    if(post('email') !== false)
        $user = $this->users->getUserByEmail($_POST['email']);

    if(!$user) {    // Поиск по номеру телефона
        $searchedTel = str_replace('+380','',$one_click_tel);
        $searchedTel = str_replace('+7','',$searchedTel);
        $searchedTel = str_replace('+','',$searchedTel);
        $user = $this->users->getUserByTel($searchedTel);
        if(!$user){     // добавляем нового пользователя по номеру телефона
            $dbins = array(
                'login' => $one_click_tel,
                'tel'   => $one_click_tel,
                'pass'  => md5($one_click_tel),
                'email' => "Не известен",
                'name' => "Не известно",
                'lastname' => "Не известна",
                'tel' => $one_click_tel,
                'country' => $country,
                'city' => "Не известно",
                'adress' => "Не известен",
                'passport' => "",
                'zip' => "",
                'np' => "",
                'type' => 'Посетитель',
                'user_type_id'  => 1,
                'active' => 1,
                'reg_date' => date("Y-m-d"),
                'reg_ip' => $_SERVER['REMOTE_ADDR'],
                'activation' => 1,
                'mailer' => 1
            );
            $this->db->insert('users', $dbins);

            $user = $this->users->getUserByTel($one_click_tel);

            set_userdata('login', $user['email']);
            set_userdata('pass', $user['pass']);
            set_userdata('email', $user['email']);
            set_userdata('type', $user['type']);
            set_userdata('user_type_id', $user['user_type_id']);
        }
    }


    if ($user != false && $oneClickOrder != true) {
        //vd("97 - user found by email");
        $user['email'] = $_POST['email'];
        if (!isset($_POST['pass'])) $_POST['pass'] = $_POST['email'];
        $dbins = array(
            'email' => $_POST['email'],
            'login' => $_POST['email'],
            'name' => $_POST['name'],
            'lastname' => $_POST['lastname'],
            'tel' => $_POST['tel'],
            'country' => $country,
            'city' => $_POST['city'],
            'adress' => $_POST['adress'],
            'passport' => $_POST['passport'],
            'zip' => $_POST['zip'],
            'np' => $_POST['np'],
            'pass' => md5($_POST['pass']),
            'type' => 'client',
            'active' => 1,
            'reg_date' => date("Y-m-d"),
            'reg_ip' => $_SERVER['REMOTE_ADDR'],
            'activation' => 1,
            'mailer' => 1
        );

        if (userdata('adwords') !== false) $dbins['from'] = userdata('adwords');
        $this->db->where('id', $user['id']);
        $this->db->limit(1);
        $this->db->update('users', $dbins);
    } elseif(! $oneClickOrder) {
        //vd("126 - user not found and be added");
        if (!isset($_POST['pass'])) $_POST['pass'] = $_POST['email'];
        $dbins = array(
            'email' => $_POST['email'],
            'login' => $_POST['email'],
            'name' => $_POST['name'],
            'lastname' => $_POST['lastname'],
            'tel' => $_POST['tel'],
            'country' => $country,
            'city' => $_POST['city'],
            'adress' => $_POST['adress'],
            'passport' => $_POST['passport'],
            'zip' => $_POST['zip'],
            'np' => $_POST['np'],
            'pass' => md5($_POST['pass']),
            'type' => 'client',
            'active' => 1,
            'reg_date' => date("Y-m-d"),
            'reg_ip' => $_SERVER['REMOTE_ADDR'],
            'activation' => 1,
            'mailer' => 1
        );
        if (userdata('adwords') !== false) $dbins['from'] = userdata('adwords');

        $this->db->insert('users', $dbins);

        $user = $this->users->getUserByEmail($_POST['email']);
        //var_dump($user);
    }

}

if ($user) {
    //vd("158 - user founded. create order");
    $akciya = 0;

    $dropship_id = 0;
    $addr_id = 0;
    $addr = false;

    $adress = '';

    if (userdata('type') == 11)
        $adress .= '<b>Заказ от дропшиппера. Адрес доставки:</b><br />' . post('ds_name') . ', ' . $country . ', г. ' . post('city') . '<br />Тел: ' . post('ds_tel');
    else $adress .= $country . ', г. ' . post('city') . '<br />Тел: ' . post('tel');

    if(isset($_POST['adress']) && $_POST['adress'] != '') $adress .= '<br /> Адрес: '.post('adress');
    if (isset($_POST['passport']) && $_POST['passport'] != '') $adress .= '<br />Паспорт: ' . post('passport');
    if (isset($_POST['zip']) && $_POST['zip'] != '') $adress .= '<br />Индекс: ' . post('zip');
    if (isset($_POST['np']) && $_POST['np'] != '') $adress .= '<br />Новая Почта №' . post('np');

    $user_type = $this->users->getUserTypeById($user['user_type_id']);



    if (userdata('type') == 11) { // Если дропшиппер
        $dropship_id = $user['id'];
        if (post('addr_id') == 0) {   // Добавляем адрес в базу
            $country = $this->shop->getCountryById(post('country'));
            if (isset($country['name'])) $country = $country['name'];
            else $country = NULL;
            $dbins = array(
                'user_id' => $user['id'],
                'login' => $user['login'],
                'name' => post('ds_name'),
                'tel' => post('ds_tel'),
                'country_id' => post('country'),
                'country' => $country,
                'city' => post('city'),
                'adress' => $adress,
                'np' => post('np'),
                'passport' => post('passport'),
                'zip' => post('zip')
            );
            $this->db->insert('addr', $dbins);

            $this->db->where('user_id', $user['id']);
            $this->db->where('login', $user['login']);
            $this->db->where('name', post('ds_name'));
            $this->db->where('tel', post('ds_tel'));
            $this->db->limit(1);
            $this->db->order_by('id', 'DESC');
            $addr = $this->db->get('addr')->result_array();
            if (isset($addr[0])) {
                $addr = $addr[0];
                $addr_id = $addr['id'];
            }
        } else {
            $addr = $this->users->getAddressById(post('addr_id'));
            if ($addr) $addr_id = $addr['id'];
        }
    }

    // ОБНОВЛЯЕМ ДАННЫЕ О КЛИЕНТЕ:
    $needUpdate = false;
    if(isset($_POST['email']) && $user['email'] != post('email')) {
        $user['email'] = post('email');
        $needUpdate = true;
    }
    if(isset($_POST['name']) && $user['name'] != post('name')) {
        $user['name'] = post('name');
        $needUpdate = true;
    }
    if(isset($_POST['lastname']) && $user['lastname'] != post('lastname')) {
        $user['lastname'] = post('lastname');
        $needUpdate = true;
    }
    if(isset($_POST['tel']) && $user['tel'] != post('tel')) {
        $user['tel'] = post('tel');
        $needUpdate = true;
    }
    if(isset($_POST['country']) && $user['country'] != post('country')) {
        $country = getItemById(post('country'), 'countries');
        if(isset($country['name']))
            $user['country'] = $country['name'];
        $needUpdate = true;
    }
    if($user['country'] == NULL) {
        loadHelper('geoip');
        $user['country'] = getUserCountry();
    }
    if(isset($_POST['city']) && $user['city'] != post('city')) {
        $user['city'] = post('city');
        $needUpdate = true;
    }
    if(isset($_POST['adress']) && $user['adress'] != post('adress')) {
        $user['adress'] = post('adress');
        $needUpdate = true;
    }

    if(isset($_POST['passport']) && $user['passport'] != post('passport')) {
        $user['passport'] = post('passport');
        $needUpdate = true;
    }

    if(isset($_POST['zip']) && $user['zip'] != post('zip')) {
        $user['zip'] = post('zip');
        $needUpdate = true;
    }

    if(isset($_POST['np']) && $user['np'] != post('np')) {
        $user['np'] = post('np');
        $needUpdate = true;
    }
//
//                if(isset($_POST['email']) && $user['email'] != post('email')) {
//                    $user['email'] = post('email');
//                    $needUpdate = true;
//                }

    if($needUpdate)
        $this->db->where('id', $user['id'])->limit(1)->update('users', $user);
    // **

    $summa = 0;
    $summaNotSale = 0;

    for ($i = 0; $i < $count; $i++) {
        $shop = $this->shop->getProductById($my_cart[$i]['shop_id']);

        $my_cart[$i]['final_price'] = $my_cart[$i]['original_price'] = $shop['price'];
        if ($shop['discount'] > 0) {
            $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $shop['discount']);
            $my_cart[$i]['discount'] = $shop['discount'];
        } elseif ($coupon) {
            $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $coupon['discount']);
            $my_cart[$i]['discount'] = $coupon['discount'];
        }

        if ($shop) {
            if (isDiscount($shop)) {
                $akciya = 1;
            }


            $razmer = explode('*', $shop['razmer']);
            $rcount = count($razmer);
            for ($i2 = 0; $i2 < $rcount; $i2++) {
                if (isset($my_cart[$i]['kolvo_' . $razmer[$i2]])) {
                    //$message_table .= $razmer[$i2].': '.$mc['kolvo_'.$razmer[$i2]].'<br>';
                    $res = getAkciyaPrice($shop) * $my_cart[$i]['kolvo_' . $razmer[$i2]];
                    $summa = $summa + $res;
                    if ($shop['discount'] == 0)
                        $summaNotSale = $summaNotSale + $res;
                }
            }


        }
        //vd($akciya);
    }


//                if(isDebug())
//                    vd($summaNotSale);

    $nadbavka = 0;
    $kolvo = shop_count();
    if ($kolvo > 0) {
        if ($kolvo < $shop_opt_from) {
            if (isset($user_type['nadbavka']) && $user_type['nadbavka'] != -1)
                $shop_nadbavka = $user_type['nadbavka'];
            else
                $shop_nadbavka = getOption('shop_nadbavka');

            //$shop_nadbavka = $shop_nadbavka * $kolvo;
            //$summa = $summa + $shop_nadbavka;
            //$summaNotSale = $summaNotSale + $shop_nadbavka;
            $nadbavka = $shop_nadbavka;
        }


        $unix = time();

        $details = array();
        $details['city'] = post('city');
        $details['country'] = $country['name'];
        $details['country_id'] = $country['id'];
        $details['adress'] = post('adress');
        $details['tel'] = post('tel');
        $details['ds_tel'] = post('ds_tel');
        $details['zip'] = post('zip');
        $details['passport'] = post('passport');
        $details['np'] = post('np');


        $discount = 0;
        if (isset($coupon) && !isset($coupon['err'])) {
            $discount = $coupon['discount'];
            if ($coupon['type'] == 0) {
                if ($coupon['not_sale'] == 1) {
                    // высчитываем скидку без учёта товаров из раздела Sale
                    $res = $summaNotSale / 100 * $discount;
                    // vd($summaNotSale.': '.$res);
                    $summa = $summa - $res;
                } else {
                    // высчитываем скидку полностью на всю покупку
                    $res = $summa / 100 * $discount;
                    //  vd("not");
                    $summa = $summa - $res;
                }
            } elseif ($coupon['type'] == 1) {
                $summa = $summa - $discount;
            }
        }

//                    if ($nadbavka)
//                        $summa = $summa + $nadbavka;

//                    if(isDebug()){
//                        vd($summa);
//                        die();
//                    }

        $full_summa = $summa + $nadbavka ;



        $currencies = array(
            'UAH' => getCurrencyValue('UAH'),
            'USD' => getCurrencyValue('USD'),
            'RUB' => getCurrencyValue('RUB'),
        );

        $delivery_to_russia_price = false;
        $country_id = post('country');
        if(!$country_id)
            $country_id = userdata('country');

        $countryDelivery = getCountryDeliveryPrice($country_id);
        if($countryDelivery) $full_summa = $full_summa + $countryDelivery;

        $deliveryPrice = $countryDelivery;

        $details = json_encode($details);

        $currencies = json_encode($currencies);

        $npnp = 0;
        if (isset($_POST['npnp']) && $_POST['npnp'] == true) $npnp = 1;

        $adding = post('adding');
        if($oneClickOrder)
            $adding = "Перезвоните мне, пожалуйста для уточнения моих данных!";

        $dbins = array(
            'user_id' => $user['id'],
            'date' => date("Y-m-d"),
            'time' => date("H:i"),
            'unix' => $unix,
            'products' => serialize($my_cart),
            'adress' => $adress,
            'payment' => post('payment'),
            'delivery' => post('delivery'),
            'delivery_price' => $deliveryPrice,
            'summa' => $summa,
            'full_summa' => $full_summa,
            'adding' => $adding,
            'currency' => $currency,
            'akciya' => $akciya,
            'details' => $details,
            'nadbavka' => $nadbavka,
            'currencies' => $currencies,
            'dropship_id' => $dropship_id,
            'addr_id' => $addr_id,
            'npnp' => $npnp
        );

        if($oneClickOrder) {
            $dbins['one_click'] = 1;
            $dbins['status'] = 'one_click';
        }
//                    if(isDebug())
//                        vdd($dbins);

        if ($npnp == 1)
            $dbins['status'] = 'npnp_not_payed';

        if (isDebug()) {
            // vdd($dbins);
        }

        if (userdata('adwords') !== false)
            $dbins['from'] = userdata('adwords');

        if ($coupon) {
            $dbins['code'] = $coupon['code'];
            $dbins['coupon'] = json_encode($coupon);
        }


        $this->db->insert('orders', $dbins);

        $this->db->where('unix', $unix);
        $this->db->where('user_id', $user['id']);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $order = $this->db->get('orders')->result_array();
        if ($order) {

            // Проверяем кол-во заказов пользователем и, при необходимости, меняем тип клиента
            $ocount = $this->shop->getUserOrdersCount($user['id']);
            if ($ocount == 0 && $user['user_type_id'] != 11 && $user['user_type_id'] != 10 && $user['user_type_id'] != 12 && $user['user_type_id'] != 6 && $user['user_type_id'] != 4) {
                //echo ' Посетитель<hr>';
                $this->db->where('id', $user['id'])->limit(1)->update('users', array('user_type_id' => 1, 'user_type' => 'Посетитель'));
            } elseif ($ocount > 0 && $ocount < 4 && $user['user_type_id'] != 11 && $user['user_type_id'] != 10 && $user['user_type_id'] != 12 && $user['user_type_id'] != 6 && $user['user_type_id'] != 4) {
                //echo ' Покупатель<hr>';
                $this->db->where('id', $user['id'])->limit(1)->update('users', array('user_type_id' => 2, 'user_type' => 'Покупатель'));
            } elseif ($ocount >= 4 && $user['user_type_id'] != 11 && $user['user_type_id'] != 10 && $user['user_type_id'] != 12 && $user['user_type_id'] != 6 && $user['user_type_id'] != 4) {
                //echo ' Постоянный<hr>';
                $this->db->where('id', $user['id'])->limit(1)->update('users', array('user_type_id' => 3, 'user_type' => 'Постоянный'));
            }
            //////////////////////////////////////////////////////////////////////////////////

            $order = $order[0];

            if (!isDebug())
                writeOrderFile($order);

            set_userdata('action_order', serialize($order));


            // Обработка купона
            if ($coupon) {
                $coupon['used_date'] = date("Y-m-d H:i");
                $coupon['used_by'] = $user['login'];
                $coupon['order_id'] = $order['id'];
                $this->db->where('id', $coupon['id'])->limit(1)->update('coupons', $coupon);

                if ($coupon['multi'] != 1) {
                    $dbins = array(
                        'coupon_id' => $coupon['id'],
                        'user_login' => $user['login'],
                        'date' => date("Y-m-d H:i"),
                        'order_id' => $order['id']
                    );
                    $this->db->insert('coupons_using', $dbins);
                }
            }


            $this->load->helper('mail_helper');

            // Отправка клиенту
            if(!$oneClickOrder) {
                $clientMessage = createOrderEmail($order['id']);
                $msg = createEmail('/upload/email/42ed846af09614f5cb4bf3ffc7cdeb6e.jpg', "Ваш заказ №: " . $order['id'] . ' оформлен успешно!', $clientMessage, false, false,false,0,false,false);
                mail_send($user['email'], "Ваш заказ №: " . $order['id'] . ' оформлен успешно!', $msg);
            }
            //$message = str_replace($shop_sended, '', $message);

            $to = $this->model_options->getOption('admin_email');
            //$to = 'xomiak@rap.org.ua';

            $message = "";
            if(!$oneClickOrder)
                $message = createOrderEmail($order['id'], true);
            else {
                $message = getMyCartTable($order);
                $message .= "Заказ в 1 клик!<br />Пожалуйста, перезвоните мне для уточнения моих контактных данных!<BR><strong>".$one_click_tel ."</strong>";
            }
            $from = "";
            if (userdata('adwords') !== false)
                $from .= '<br /><b>Клиент пришёл с рекламы: ' . userdata('adwords') . '</b><br />';
            elseif ($user['from'] != NULL && $user['from'] != '')
                $from .= '<br /><b>Клиент пришёл с рекламы: ' . $user['from'] . '</b><br />';

            $message .= $from;
            //$msg = createEmail('/upload/email/42ed846af09614f5cb4bf3ffc7cdeb6e.jpg', "Поступил новый заказ № " . $order['id'], $message);
            $subject = "Поступил новый заказ № " . $order['id'];
            if (userdata('type') == 11) $subject .= ' от дропшиппера';
            if($oneClickOrder) $subject = "Поступил заказ в 1 клик №".$order['id'];
            mail_send($to, $subject, $message);


            // SMS УВЕДОМЛЕНИЕ
            if ($user['tel'] != '') {
                if (getOption('sms_send') == 1) {

                    $sms_newOrderTemplate = getOption('sms_newOrderTemplate');

                    $orderPrice = $order['full_summa'] * getCurrencyValue($order['currency']);
                    $orderPrice .= ' ' . $order['currency'];


                    $sms_newOrderTemplate = str_replace('[order_id]', $order['id'], $sms_newOrderTemplate);
                    $sms_newOrderTemplate = str_replace('[order_sum]', $orderPrice, $sms_newOrderTemplate);


                    $this->load->helper('sms_helper');

                    $credits = sms_getCredits();
                    if ($credits < 10) mail_send($to, 'На SMS рассыле осталось ' . $credits . ' кредитов!', 'На SMS рассыле осталось ' . $credits . ' кредитов!<br/>Необходимо пополнить счёт в кабинете на https://turbosms.ua/');
                    if ($credits > 0 && isset($order['id'])) {
                        //vdd($order);
                        $result = sms_send($user['tel'], $sms_newOrderTemplate);
                        $dbins['order_sms_sended'] = 1;
                        $dbins['order_sms_result'] = json_encode($result);
                        $this->db->where('id', $order['id'])->limit(1)->update('orders', $dbins);
                    }
                }
            }

            //////////////////
//					$message = $this->model_options->getOption('template_mail_order');
//					$message = str_replace('[name]', $user['name'], $message);
//					$message = str_replace('[order]', $message_table, $message);

            set_userdata('complete_order', $order['id']);


            if ($user['user_type'] == 'посетитель') {
                $dbins = array('user_type' => 'Покупатель');
                $this->db->where('id', $user['id']);
                $this->db->limit(1);
                $this->db->update('users', $dbins);
            }

            $completed = true;
            $this->session->unset_userdata('my_cart');

            unset_userdata('country');
//                    if ($_POST['payment'] == 'Приват24') {
//                        redirect('/payment/privat24/' . $order['id'] . '/');
//                    } elseif ($_POST['payment'] == 'Кредитная карта') {
//                        redirect('/payment/liqpay/' . $order['id'] . '/');
//                    } elseif ($_POST['payment'] == 'walletone') {
//                        redirect('/payment/walletone/' . $order['id'] . '/');
//                    } elseif ($_POST['payment'] == 'interkassa') {
//                        redirect('/payment/interkassa/' . $order['id'] . '/');
//                    }
//                    redirect('/my_cart/sended/');

            if(!$oneClickOrder) {
                if ($_POST['payment'] == 'interkassa') {
                    set_userdata('payment', 'interkassa');
                    set_userdata('order_id', $order['id']);
                } elseif ($_POST['payment'] == 'Интеркасса') {
                    set_userdata('payment', 'Интеркасса');
                    set_userdata('order_id', $order['id']);
                } elseif ($_POST['payment'] == 'liqpay') {
                    set_userdata('payment', 'liqpay');
                    set_userdata('order_id', $order['id']);
                } else {
                    set_userdata('order_completed', 'true');
                }
            }
        } else $data['err'] = 'Произошла ошибка! Видимо, прошло долго времени с момента добавления товаров в корзину';
    } else {
        $data['err'] = 'Ошибка оформления заказа! Попробуйте ещё раз.';
    }
}