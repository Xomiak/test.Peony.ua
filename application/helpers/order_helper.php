<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: XomiaK
 * Date: 26.04.2017
 * Time: 16:23
 */
// СОЗДАЁМ ФАЙЛ ДЛЯ ИМПОРТА В ТОРГСОФТ
function createNewOrdersFiles()
{
    $CI = &get_instance();
    //$unix = time() - 1500;
    $CI->db->where('torgsoft_file', 0);
    //$CI->db->where('unix <', $unix);
    $orders = $CI->db->get('orders')->result_array();
    //vdd($orders);
    if ($orders) {
        foreach ($orders as $order) {
            $writed = writeOrderFile($order);
            if ($writed)
                $CI->db->where('id', $order['id'])->limit(1)->update('orders', array('torgsoft_file' => 1));
        }
    }
}

function getMyCartData()
{
    $CI = &get_instance();
    $result = array();
    $coupon = false;

    $my_cart = userdata('my_cart');
    $login = userdata('login');
    $user = false;

    if ($login) {
        $mUsers = getModel('users');
        $user = $mUsers->getUserByLogin($login);
    }
    if (post('country') !== false)
        set_userdata('country', post('country'));
    $country = userdata('country');
    $myCountry = false;
    if ($country) $myCountry = getCountryByName($country);
    if (!$myCountry) $myCountry = getCountryById($country);

    if ($myCountry) $country = $myCountry;

    if (!$country)
        $country = getMyCountry();
    $countryId = $countryName = false;
    if (isset($country['id'])) $countryId = $country['id'];
    if (isset($country['name'])) $countryName = $country['name'];

    $shop_opt_from = 0;
    if (isset($country['opt_from'])) $shop_opt_from = $country['opt_from'];
    //vdd($country);
    //$shop_opt_from = getOption('shop_opt_from');
    if (!$country) $country = getItemById(1, 'countries');

    if (isset($country['opt_from']) && $country['opt_from'] > 0)
        $shop_opt_from = $country['opt_from'];

    $coupon = userdata('coupon');
    if ($coupon)
        $coupon = couponCheck($coupon);

    $summa = 0;
    $full_summa = 0;
    $summaNotSale = 0;
    $products = array();
    if ($my_cart) {
        $count = count($my_cart);
        for ($i = 0; $i < $count; $i++) {
            $shop = $CI->shop->getProductById($my_cart[$i]['shop_id']);

            $my_cart[$i]['final_price'] = $my_cart[$i]['original_price'] = $shop['price'];
            if ($shop['discount'] > 0) {
                $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $shop['discount']);
                $my_cart[$i]['discount'] = $shop['discount'];
            } elseif ($coupon) {
                $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $coupon['discount']);
                $my_cart[$i]['discount'] = $coupon['discount'];
            }

            if ($shop) {
                $product = array(
                    'id' => $shop['id'],
                    'final_price' => $my_cart[$i]['final_price'],
                    'discount' => $shop['discount']
                );

                if (isDiscount($shop)) {
                    $akciya = 1;
                }

                $product['sizes'] = array();
                $product['sizesCounts'] = array();

                $productCount = 0;

                $razmer = explode('*', $shop['razmer']);
                $rcount = count($razmer);
                for ($i2 = 0; $i2 < $rcount; $i2++) {
                    if (isset($my_cart[$i]['kolvo_' . $razmer[$i2]]) && $my_cart[$i]['kolvo_' . $razmer[$i2]] > 0) {
                        array_push($product['sizes'], $razmer[$i2]);
                        $sizesCounts = array(
                            'size' => $razmer[$i2],
                            'count' => $my_cart[$i]['kolvo_' . $razmer[$i2]]
                        );
                        $product['kolvo_' . $razmer[$i2]] = $my_cart[$i]['kolvo_' . $razmer[$i2]];
                        array_push($product['sizesCounts'], $sizesCounts);
                        $productCount += $my_cart[$i]['kolvo_' . $razmer[$i2]];

                        //$message_table .= $razmer[$i2].': '.$mc['kolvo_'.$razmer[$i2]].'<br>';
                        $res = getAkciyaPrice($shop) * $my_cart[$i]['kolvo_' . $razmer[$i2]];
                        $summa = $summa + $res;
                        if ($shop['discount'] == 0)
                            $summaNotSale = $summaNotSale + $res;
                    }
                }
                $product['count'] = $productCount;
                $product['shop_id'] = $shop['id'];

                array_push($products, $product);
            }
            //vd($akciya);
        }
    } else return false;

    $deliveryPrice = 4;
    $userType = false;
    if ($user)
        $userType = getItemById($user['user_type_id'], 'user_types');

    if (!$userType)
        $userType = getItemById(1, 'user_types');


    $nadbavka = $shop_nadbavka = $userType['nadbavka'];

    $kolvo = shop_count();
//vdd($shop_opt_from);
    if ($kolvo < $shop_opt_from) {
        if (isset($userType['nadbavka']) && $userType['nadbavka'] != -1)
            $shop_nadbavka = $userType['nadbavka'];
        else
            $shop_nadbavka = 0;

        //$shop_nadbavka = $shop_nadbavka * $kolvo;
        //$summa = $summa + $shop_nadbavka;
        //$summaNotSale = $summaNotSale + $shop_nadbavka;
        if (isset($country['nadbavka']) && $country['nadbavka'] > 0)
            $shop_nadbavka += $country['nadbavka'];
        $nadbavka = $shop_nadbavka;
    } else $nadbavka = $shop_nadbavka = 0;


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

    if ($nadbavka)
        $full_summa = $summa + $nadbavka;
    else
        $full_summa = $summa;

    //$full_summa = $summa;

    // Данные о курсе валют на момент заказа
    $todayCurrenciesValues = array(
        'UAH' => getCurrencyValue('UAH'),
        'USD' => getCurrencyValue('USD'),
        'RUB' => getCurrencyValue('RUB'),
    );

    $delivery_to_russia_price = false;
    $deliveryPrice = 0;
    $deliveryPriceFull = 0;
    //vd($country);
    if (isset($country['delivery_price'])) {
        $deliveryPrice = $country['delivery_price'];
        if($country['bigopt_from'] < $count)
            $deliveryPrice = $country['bigopt_deliver_price'];
    }
    if ($deliveryPrice > 0) {
        $myCartCount = shop_count();
        $deliveryPriceFull = (float)$deliveryPrice * $myCartCount;
        ///vd($full_summa);
        $full_summa = $full_summa + $deliveryPriceFull;
        // vdd($full_summa);
    }

//    if(!$country){
//        $country = getMyCountry();
//        $myCountry = getCountryByName($country);
//        if(!$myCountry) $myCountry = getCountryById($country);
//
//        if($myCountry) $country = $myCountry;
//    }
    $currency = userdata('currency');
    if (!$currency) $currency = 'uah';
//vdd($deliveryPriceFull);
    $details = array();
//    if(! isset($country['name']) && ! isset($country['id']))
//        $country = getMyCountry();
    // vd($country);
    $npnp = 0;
    if (isset($_POST['npnp']) && $_POST['npnp'] == true)
        $npnp = 1;
    elseif (userdata('npnp') !== false)
        $npnp = 1;
    $details['city'] = post('city');
    $details['country'] = $countryName;
    $details['country_id'] = $countryId;
    $details['adress'] = post('adress');
    $details['tel'] = post('tel');
    $details['ds_tel'] = post('ds_tel');
    $details['zip'] = post('zip');
    $details['passport'] = post('passport');
    $details['np'] = post('np');
//    $details['npnp'] = 0;
//    if(isset($_POST['npnp']) && $_POST['npnp'] == true)
//        $details['npnp'] = 1;

    if (!$details['np'])
        $details['np'] = '';

    if ($npnp == 1) {
        $npnp_price = getOption('npnp_price');
        $result['npnp'] = $details['npnp'] = 1;
        $result['npnp_price'] = $details['npnp_price'] = $npnp_price;
        $result['pay_summa'] = $details['pay_summa'] = $npnp_price;
        $result['result_summa'] = $details['result_summa'] = $full_summa - $npnp_price;
    }

    $result['products'] = $products;
    $result['products_count'] = $kolvo;
    $result['deliveryPrice'] = $deliveryPrice;
    $result['deliveryPriceFull'] = $deliveryPriceFull;
    $result['summa'] = $summa;
    $result['summaNotSale'] = $summaNotSale;
    $result['nadbavka'] = $nadbavka;
    $result['full_summa'] = $full_summa;
    $result['currencies'] = $todayCurrenciesValues;
    $result['deliveryPrice'] = $deliveryPrice;
    $result['country'] = $country;
    $result['shop_opt_from'] = $shop_opt_from;
    $result['coupon'] = $coupon;
    $result['currencySymb'] = getCurrencySymb($currency);
    $result['currency'] = $currency;
    //vd($result['currency']);
    $result['details'] = $details;
    $result['np'] = $details['np'];

    return $result;
}

/** СТАТУСЫ ЗАКАЗОВ */
function getStatuses($active = -1){
    $model = getModel('shop');
    return $model->getStatuses($active);
}

function getStatusName($status, $ceil = 'name'){
    $model = getModel('shop');
    return $model->getStatusBy('status', $status, $ceil);
}
function getStatusValueByName($name, $ceil = 'status'){
    $model = getModel('shop');
    return $model->getStatusBy('name', $name, $ceil);
}

function getProductsPrice($my_cart, $coupon = false){
    $summa = 0;
    $summaNotSale = 0;

    if($coupon != false && !is_array($coupon)){
        $model = getModel('coupon');
        $coupon = $model->getByCode($coupon);
    }

    $model = getModel('shop');

    if ($my_cart) {
        $count = count($my_cart);
        for ($i = 0; $i < $count; $i++) {
            $shop = $model->getProductById($my_cart[$i]['shop_id']);

            $my_cart[$i]['final_price'] = $my_cart[$i]['original_price'] = $shop['price'];
            if ($shop['discount'] > 0) {
                $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $shop['discount']);
                $my_cart[$i]['discount'] = $shop['discount'];
            } elseif ($coupon) {
                $my_cart[$i]['final_price'] = getNewPrice($shop['price'], $coupon['discount']);
                $my_cart[$i]['discount'] = $coupon['discount'];
            }

            if ($shop) {
                $products = array();

                $product = array(
                    'id' => $shop['id'],
                    'final_price' => $my_cart[$i]['final_price'],
                    'discount' => $shop['discount']
                );


                $product['sizes'] = array();
                $product['sizesCounts'] = array();

                $productCount = 0;

                $razmer = explode('*', $shop['razmer']);
                $rcount = count($razmer);
                for ($i2 = 0; $i2 < $rcount; $i2++) {
                    if (isset($my_cart[$i]['kolvo_' . $razmer[$i2]]) && $my_cart[$i]['kolvo_' . $razmer[$i2]] > 0) {
                        array_push($product['sizes'], $razmer[$i2]);
                        $sizesCounts = array(
                            'size' => $razmer[$i2],
                            'count' => $my_cart[$i]['kolvo_' . $razmer[$i2]]
                        );
                        $product['kolvo_' . $razmer[$i2]] = $my_cart[$i]['kolvo_' . $razmer[$i2]];
                        array_push($product['sizesCounts'], $sizesCounts);
                        $productCount += $my_cart[$i]['kolvo_' . $razmer[$i2]];

                        //$message_table .= $razmer[$i2].': '.$mc['kolvo_'.$razmer[$i2]].'<br>';
                        $res = getAkciyaPrice($shop) * $my_cart[$i]['kolvo_' . $razmer[$i2]];
                        $summa = $summa + $res;
                        if ($shop['discount'] == 0)
                            $summaNotSale = $summaNotSale + $res;
                    }
                }
                $product['count'] = $productCount;
                $product['shop_id'] = $shop['id'];

                if(is_array($product))
                    array_push($products, $product);
            }
            //vd($akciya);
        }
    }
    return $summa;
}

function clearNulledProductsFromOrder($my_cart)
{
    $result = array();
    if (isDebug()) vd($my_cart);
    foreach ($my_cart as $mc) {
        //var_dump($mc['kolvo']);
        if ($mc['kolvo'] == 0)
            $result[] = $mc;
    }
    return $result;
}

function getOrderData($order_id)
{
    $CI = &get_instance();
    $result = array();
    $coupon = false;
    $mOrders = getModel('shop');
    $mUsers = getModel('users');
    $order = $mOrders->getOrderById($order_id);
    $my_cart = unserialize($order['products']);
    $user = $mUsers->getUserById($order['user_id']);
    $country = false;
    if ($order['country_id'] != 0)
        $country = getItemById($order['country_id'], 'countries');
    elseif ($order['country'] != NULL)
        $country = getCountryByName($order['country']);
    else {
        $country = getCountryByName($user['country']);
    }

    $shop_opt_from = 4;
    if (isset($country['opt_from']) && $country['opt_from'] > 0)
        $shop_opt_from = $country['opt_from'];

    $npnp = 0;
    if ($order['npnp'] == 1) $npnp = 1;

    if ($order['coupon'] != NULL) $coupon = json_decode($order['coupon'], true);
    $summa = 0;
    $summaNotSale = 0;
    if ($my_cart) {
        $count = count($my_cart);
        for ($i = 0; $i < $count; $i++) {
            $shop = $CI->shop->getProductById($my_cart[$i]['shop_id']);

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
    }

    $deliveryPrice = 4;
    $userType = false;
    if ($user)
        $userType = getItemById($user['user_type_id'], 'user_types');

    if (!$userType)
        $userType = getItemById(1, 'user_types');


    $nadbavka = $shop_nadbavka = $order['nadbavka'];

//    $kolvo = shop_count();
//
//    if ($kolvo < $shop_opt_from) {
//        if (isset($userType['nadbavka']) && $userType['nadbavka'] != -1)
//            $shop_nadbavka = $userType['nadbavka'];
//        else
//            $shop_nadbavka = 0;
//
//        //$shop_nadbavka = $shop_nadbavka * $kolvo;
//        //$summa = $summa + $shop_nadbavka;
//        //$summaNotSale = $summaNotSale + $shop_nadbavka;
//        $nadbavka = $shop_nadbavka;
//    } else $nadbavka = 0;


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

    $full_summa = $summa;

    if ($nadbavka)
        $full_summa = $summa + $nadbavka;


    // Данные о курсе валют на момент заказа
    $todayCurrenciesValues = array(
        'UAH' => getCurrencyValue('UAH'),
        'USD' => getCurrencyValue('USD'),
        'RUB' => getCurrencyValue('RUB'),
    );

    $myCartCount = shop_count($my_cart);

    $deliveryPrice = 0;
    $deliveryPriceFull = 0;
    // var_dump($country);
    if (isset($country['delivery_price'])) $deliveryPrice = $country['delivery_price'];
    if ($deliveryPrice > 0) {
        //    vdd($deliveryPrice);

        $deliveryPriceFull = (float)$deliveryPrice * $myCartCount;

        $full_summa = $full_summa + $deliveryPriceFull;
    }
//vdd($myCartCount);
    $details = array();

    $result['npnp'] = 1;
    $result['deliveryPrice'] = $deliveryPrice;
    $result['deliveryPriceFull'] = $deliveryPriceFull;
    $result['summa'] = $summa;
    $result['summaNotSale'] = $summaNotSale;
    $result['nadbavka'] = $nadbavka;
    $result['full_summa'] = $full_summa;
    $result['result_summa'] = $full_summa;
    $result['pay_summa'] = $full_summa;
    if ($npnp == 1) {
        $npnp_price = getOption('npnp_price');
        $details['npnp'] = 1;
        $details['pay_summa'] = $npnp_price;
        $details['result_summa'] = $full_summa - $npnp_price;
    }
    $result['details'] = $details;
    $result['currencies'] = json_encode($todayCurrenciesValues);
    $result['deliveryPrice'] = $deliveryPrice;
    $result['country'] = $country;
    $result['products_count'] = $myCartCount;
    $result['shop_opt_from'] = $shop_opt_from;
    $result['currencySymb'] = getCurrencySymb($order['currency']);
    $result['currency'] = getCurrencyByCode($order['currency']);
//vdd($result);
    return $result;
}

function createNewOrder($userInfo = false, $orderDetails = false)
{
    $CI = &get_instance();

    $tel = false;
    $email = false;
    if (isset($userInfo['tel'])) $tel = $userInfo['tel'];
    if (isset($userInfo['email'])) $email = $userInfo['email'];

    if (!isset($userInfo['name']) && post('name') !== false) $userInfo['name'] = post('name');
    if (!isset($userInfo['lastname']) && post('lastname') !== false) $userInfo['lastname'] = post('lastname');
    if (!isset($userInfo['email']) && post('email') !== false) $userInfo['email'] = post('email');
    if (!isset($userInfo['tel']) && post('tel') !== false) $userInfo['tel'] = post('tel');
    if (!isset($userInfo['country']) && post('country') !== false) $userInfo['country'] = post('country');
    if (!isset($userInfo['city']) && post('city') !== false) $userInfo['city'] = post('city');
    //   if(!isset($userInfo['adding']) && post('adding') !== false) $userInfo['adding'] = post('adding');
    //   if(!isset($userInfo['payment']) && post('payment') !== false) $userInfo['payment'] = post('payment');
    //  if(!isset($userInfo['currency']) && post('currency') !== false) $userInfo['currency'] = post('currency');
    //  if(!isset($userInfo['delivery']) && post('delivery') !== false) $userInfo['delivery'] = post('delivery');
    if (!isset($userInfo['adress']) && post('adress') !== false) $userInfo['adress'] = post('adress');
    if (!isset($userInfo['np']) && post('np') !== false) $userInfo['np'] = post('np');
    //if(!isset($userInfo['currency']) && post('tel') !== false) $userInfo['tel'] = post('tel');

    $countryValue = false;
    if (is_numeric($userInfo['country']))
        $countryValue = getCountryById($userInfo['country']);
    if (isset($countryValue['name'])) $userInfo['country'] = $countryValue['name'];

    $adding = post('adding');
    $oneClickOrder = 0;
    if (isset($orderDetails['one_click_order'])) {
        $adding .= "<p><b>Заказ в 1 клик!<br/>Перезвоните мне, пожалуйста для уточнения моих данных!</b></p>";
        $oneClickOrder = 1;
        $userInfo = array(
            'tel'   => $tel,
            'email' => $email,
            'one_click_order'   => 1
        );
    }


    $user = getOrCreateUser($userInfo);
//    if(isDebug())
//        echo 'User founded: '.vdd($user);


    if (!$tel) $tel = $user['tel'];
    if (!$email) $email = $user['email'];

    if (!isset($orderDetails['products'])) {
        $orderDetails = getMyCartData();
        if ($oneClickOrder == 1) $orderDetails['one_click_order'] = 1;
    }

    $one_click_tel = NULL;
    if (isset($orderDetails['one_click_tel']))
        $one_click_tel = $orderDetails['one_click_tel'];
    elseif (post('one_click_tel') !== false)
        $one_click_tel = $tel;


    $adress = false;
    if ($oneClickOrder != 1) {
        if (isset($orderDetails['adress'])) $adress = $orderDetails['adress'];
        else $adress = getPostAdress();
    }
    //vdd("asd");
    $country = getMyCountry();

    $countryId = $countryName = false;
    if (isset($country['id'])) $countryId = $country['id'];
    if (isset($country['name'])) $countryName = $country['id'];
//    $shop_opt_from = 0;
//    if(isset($country['opt_from'])) $shop_opt_from = $country['opt_from'];
//    else $shop_opt_from = getOption('shop_opt_from');

    $deliveryPrice = 0;
    if (isset($orderDetails['deliveryPrice'])) $deliveryPrice = $orderDetails['deliveryPrice'];
    elseif (isset($country['delivery_price'])) $deliveryPrice = $country['delivery_price'];

    $npnp = 0;
    if (isset($orderDetails['npnp']) && $oneClickOrder != 1) $npnp = $orderDetails['npnp'];

    $akciya = 0;
    if (isset($orderDetails['coupon']) && $orderDetails['coupon'] !== false)
        $akciya = 1;

    $dropship_id = 0;
    $addr_id = 0;
    if (userdata('type') == 11) { // Если дропшиппер
        $dropship_id = $user['id'];
        $addr_id = post('addr_id');
        if (!$addr_id || $addr_id == 0) {
            $dbins = array(
                'user_id' => $user['id'],
                'login' => $user['login'],
                'name' => post('ds_name'),
                'tel' => post('ds_tel'),
                'country_id' => post('country'),
                'country' => $country['name'],
                'city' => post('city'),
                'adress' => $adress,
                'np' => post('np'),
                'passport' => post('passport'),
                'zip' => post('zip')
            );
            $CI->db->insert('addr', $dbins);

            $CI->db->where('user_id', $user['id']);
            $CI->db->where('login', $user['login']);
            $CI->db->where('name', post('ds_name'));
            $CI->db->where('tel', post('ds_tel'));
            $CI->db->limit(1);
            $CI->db->order_by('id', 'DESC');
            $addr = $CI->db->get('addr')->result_array();
            if (isset($addr[0])) {
                $addr = $addr[0];
                $addr_id = $addr['id'];
            }
        } elseif ($addr_id != 0) {
            $addr = $CI->users->getAddressById(post('addr_id'));
            if ($addr) $addr_id = $addr['id'];
        }
    }
    //$my_cart = userdata('my_cart');

    if (isset($orderDetails['products']) && $orderDetails['products']) {
        $status = 'new';
        if ($npnp == 1)
            $status = 'npnp_not_payed';

        $unix = time();
        $currency = '';
        //vdd($orderDetails['currency']);
        if (post('currency') !== false)
            $currency = post('currency');
        elseif (isset($orderDetails['currency']))
            $currency = $orderDetails['currency'];
        elseif (userdata('currency') !== false)
            $country = userdata('currency');

        $payment = "Не указан";
        $delivery = "Не указан";
        $country = 'Не определена';
        $smodel = getModel('shop');
        if (post('country') !== false)
            $country = post('country');
        elseif (userdata('country') !== false)
            $country = userdata('country');
        elseif ($user['country'] != NULL)
            $country = $user['country'];
        else
            $country = getMyCountry();

        $rCountry = $smodel->getCountryByName($country);
        if (!$rCountry)
            $rCountry = $smodel->getCountryById($country);
        if ($rCountry)
            $country = $rCountry;

        $countryName = '';
        if (isset($country['name'])) $countryName = $country['name'];
        elseif ($country) $countryName = $country;
        $countryId = 0;
        if (isset($country['id'])) $countryId = $country['id'];

        if ($oneClickOrder) {
            if($one_click_tel != '') $user['tel'] = $one_click_tel;
            $deliveryInfo = '<b>Заказ в 1 клик</b>&nbsp;<br/>';
            if (isset($user['new']) && $user['new'] == true) $deliveryInfo .= '<b>новый клиент</b><br/>';
            if($user['tel'] != '') $deliveryInfo .= 'Тел.: '.$user['tel'].'<br/>';
            if($user['email'] != '') $deliveryInfo .= 'E-mail: '.$user['email'].'<br/>';
            $deliveryInfo .= '<br />IP клиента: ' . $_SERVER['REMOTE_ADDR'];
            if ($countryName) $deliveryInfo .= '<br />Возможная страна: ' . $countryName;
        } else {
            if (isset($_POST['payment'])) $payment = post('payment');
            if (isset($_POST['delivery'])) $delivery = post('delivery');
        }

        $nadbavka = $shop_nadbavka = $orderDetails['nadbavka'];
        //vdd($nadbavka);

        $torgsoft_file = 0;
        if(isset($user['undefinedUser']) && $user['undefinedUser'] == true)
            $torgsoft_file = -1;

        if ($deliveryPrice > 0) $deliveryPrice = $deliveryPrice * $orderDetails['products_count'];


        $dbins = array(
            'user_id' => $user['id'],
            'date' => date("Y-m-d"),
            'time' => date("H:i"),
            'unix' => $unix,
            'products_json' => json_encode($orderDetails['products']),
            'products' => serialize($orderDetails['products']),
            'products_count' => $orderDetails['products_count'],
            'adress' => $adress,
            'country' => $countryName,
            'country_id' => $countryId,
            'city'  => post('city'),
            'payment' => $payment,
            'delivery' => $delivery,
            'delivery_price' => $deliveryPrice,
            'delivery_info' => $deliveryInfo,
            'summa' => $orderDetails['summa'],
            'full_summa' => $orderDetails['full_summa'],
            'adding' => $adding,
            'currency' => $currency,
            'akciya' => $akciya,
            'details' => json_encode($orderDetails['details']),
            'nadbavka' => $nadbavka,
            'currencies' => json_encode($orderDetails['currencies']),
            'dropship_id' => $dropship_id,
            'addr_id' => $addr_id,
            'npnp' => $npnp,
            'status' => $status,
            'one_click' => $oneClickOrder,
            'torgsoft_file' => $torgsoft_file
        );

        if(post('np') != false)
            $dbins['delivery_np'] = post('np');

        //return $dbins;

//vdd($dbins);
        $adding = $CI->db->insert('orders', $dbins);


        $CI->db->where('user_id', $user['id']);
        $CI->db->where('unix', $unix);
        $order = $CI->db->get('orders')->result_array();
        if (isset($order[0])) {
            $order = $order[0];
            echo 'Заказ №' . $order['id'] . ' успешно сформирован!';
        }


        $currencySymb = getCurrencySymb($order['currency']);

        // Отправка клиенту
        if (isset($user['email'])) {
            $clientMessage = '';
            if (!$oneClickOrder)
                $clientMessage = createOrderEmail($order['id']);
            else {
                $myCartTable = getMyCartTable($order);

                $clientMessage = $myCartTable;
                if ($order['nadbavka'])
                    $clientMessage .= '<p><b>Надбавочная стоимость:</b> ' . get_price($order['nadbavka']) . ' ' . $currencySymb . '</p>';
                $clientMessage .= '<p><b>Цена всех товаров:</b> ' . get_price($order['full_summa']) . ' ' . $currencySymb . '</p>';
                $clientMessage .= '<br/><br/>В ближайшее время с Вами свяжется наш менеджер для уточнения всех деталей заказа!';
            }
            $msg = createEmail('/upload/email/42ed846af09614f5cb4bf3ffc7cdeb6e.jpg', "Ваш заказ №: " . $order['id'] . ' оформлен успешно!', $clientMessage, false, false, false, 0, false, false);
            $result = mail_send($user['email'], "Ваш заказ №: " . $order['id'] . ' оформлен успешно!', $msg);
            //if($result) echo '<br/>Письмо клиенту отправлено!';
        }

        $to = getOption('admin_email');

        $message = "";
        if (!$oneClickOrder)
            $message = createOrderEmail($order['id'], true);
        else {
            $myCartTable = getMyCartTable($order, true);
            $message = $myCartTable;
            $message .= '<p><b>Сумма заказа:</b> ' . get_price($order['full_summa']) . ' ' . $currencySymb . ' (' . $order['full_summa'] . '$)</p>';
            if (isset($user['new']) && $user['new'] == true) $message .= '<p><b>Новый клиент</b></p>';
            else $message .= '<p>Клиент: <b><a href="http://' . $_SERVER['SERVER_NAME'] . '/admin/users/edit/' . $user['id'] . '/' . adminLoginGetAdding() . '">' . $user['name'] . ' ' . $user['lastname'] . '</a></b></p>';
            $message .= "<p>Заказ в 1 клик!<br />
E-mail: <a href='mailto:" . $user['email'] . "'>" . $user['email'] . "</a><br />
Телефон: <a href='tel:" . $tel . "'>" . $tel . "</a><br />";
            if ($countryName) $message .= 'Предположительная страна: ' . $countryName . '<br/>';
            $message .= "<a href='http://peony.ua/admin/orders/edit/" . $order['id'] . "/" . adminLoginGetAdding() . "'>Перейти к заказу</a></p>";
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
        if ($oneClickOrder) $subject = "Поступил заказ в 1 клик №" . $order['id'];
        mail_send($to, $subject, $message);

        unset_userdata('my_cart');

        return $order;
    } else echo "Товары в корзине не найдены! Возможно Вы не сразу решились купить и корзина самоочистилась... Попробуйте ещё раз!";

    return false;
}


function getMyCartTable($order, $toAdmin = false, $coupon = false)
{

    $CI = &get_instance();
    $currency = getCurrencyByCode($order['currency']);

    $userHash = '';
    if (!$toAdmin) {
        $user = false;
        if (userdata('login') !== false)
            $user = getUserIdBylogin(userdata('login'), true);


        if ($user != false) {
            $userHash = '?from=email&auto_auth=true&user_id=' . $user['id'] . '&hash=' . md5($user['pass']);
        }
    }

    $currencySymb = '$';
    if (isset($currency['symb']))
        $currencySymb = $currency['symb'];

    $message_table = '<table class="products" border="1"';

    if (!$toAdmin) $message_table .= ' style="width: 100%; border-top:1px solid #c2c2c2;"';
    $message_table .= '>
					<th>Товар</th>
					<th>Цвет</th>
					<th>Размер:Кол-во</th>
					<th>Цена за шт.</th>
					<th>Общая стоимость</th>';

    $order['inCurrency'] = getPriceInCurrency($order['summa'], 0, $order['currency']);

    $user = getItemById($order['user_id'], 'users');
    $userType = false;
    if ($user)
        $userType = getItemById($user['user_type_id'], 'user_types');

    if (!$userType)
        $userType = getItemById(1, 'user_types');


    $nadbavka = $shop_nadbavka = $order['nadbavka'];

    $opt_from = getOption('shop_opt_from');
    if (userdata('country') === false) {
        loadHelper('geoip');
        set_userdata('country', getUserCountry());
    }
    if (userdata('country') !== false) {
        $country = getCountryById(userdata('country'));
        // vdd($country);
        if (!$country) $country = getCountryByName(userdata('country'));
        if (!$country) {
            loadHelper('geoip');
            $country = getCountryByName(getUserCountry());
        }
        if (isset($country['opt_from'])) $opt_from = $country['opt_from'];
    }


    $my_cart = unserialize($order['products']);
    $pcount = count($my_cart);
    $full_price = 0;
    $kolvo = 0;
    for ($j = 0; $j < $pcount; $j++) {
        $mc = $my_cart[$j];
        $product = $CI->shop->getProductById($mc['shop_id']);
        $cat = $CI->model_categories->getCategoryById($product['category_id']);
        $razmer = explode('*', $product['razmer']);
        $rcount = count($razmer);
        $parent = false;
        $price = getAkciyaPrice($product);
        $productSale = false;
        if ($product['discount'] > 0)
            $productSale = true;
        $akciya = isActionTime();


        $price_one = round($price, 2);

        //if($mc['kolvo'] > 1)
        //$price = $price * $mc['kolvo'];
        //var_dump($price);die();
        if ($product['parent_category_id'] != 0) $parent = $CI->model_categories->getCategoryById($product['parent_category_id']);
        $message_table .= '<tr><td>
						    <a href="http://' . $_SERVER['SERVER_NAME'] . '/';
        if ($parent) $message_table .= $parent['url'] . '/';
        $message_table .= $cat['url'] . '/' . $product['url'] . '/' . $userHash . '" target="_blank">' . $product['name'] . '</a> (' . $product['articul'] . ')
						</td>
						<td align="center">' . $product['color'] . '</td>
						<td align="center">';

        $pres = 0;
        for ($i2 = 0; $i2 < $rcount; $i2++) {
            if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != '0') {
                $message_table .= $razmer[$i2] . ': ' . $mc['kolvo_' . $razmer[$i2]] . '<br>';
                $res = $price * $mc['kolvo_' . $razmer[$i2]];
                $pres += $res;
                $kolvo += $mc['kolvo_' . $razmer[$i2]];
            }
        }
        $full_price += $pres;
        if (isset($full_price_discount) && $full_price_discount > 0) $full_price = $full_price_discount + $pres;

        $price = round($pres, 2);


        $message_table .= '</td>
						<td align="center">' . get_price($price_one) . ' ' . $currencySymb;
        if ($toAdmin) $message_table .= ' (' . $price_one . '$)';
        if ($coupon && $productSale == false) $message_table .= ' (<b>Акция!</b>)';
        elseif ($productSale) $message_table .= ' (<b>Sale</b>)';
        $message_table .= '</td>
						<td align="center">' . get_price($price) . ' ' . $currencySymb;
        if ($toAdmin) $message_table .= '(' . $price . '$)';
        $message_table .= '</td>
</tr>';
    }

    $message_table .= '</table>';

    if ($order['nadbavka'] > 0 && ($toAdmin)) {
        $message_table .= '<p><b>Розничная наценка: </b>' . get_price($order['nadbavka']) . ' ' . $currencySymb;
        $message_table .= ' (' . $order['nadbavka'] . '$)';
        $message_table .= '</p>';
    }
    if ($order['delivery_price'] > 0) {
        $message_table .= '<p><b>Стоимость доставки в страну получателя: </b>' . get_price($order['delivery_price']);
        if ($toAdmin) $message_table .= ' (' . $order['delivery_price'] . '$)';
        $message_table .= '</p>';
    }

    return $message_table;
}

function getOrderDetailsData($order, $toAdmin = false)
{
    $result = '';

    $orderDetails = getOrderData($order['id']);
    $coupon = false;
    $user = getItemById($order['user_id'], 'users');
    $full_price_discount = 0;
    if ($order['coupon'] != NULL) {
        $coupon = json_decode($order['coupon'], true);
    }
    if ($coupon) {
        $result .= '<b>Был использован скидочный купон</b>: ' . $coupon['code'];
        $full_price = $orderDetails['summa'];
        $full_price_discount = $orderDetails['summa'];
        if (isset($coupon) && !isset($coupon['err'])) {
            $discount = $coupon['discount'];
            $result .= ' (делает скидку ' . $discount;
            if ($coupon['type'] == 0) {
                $res = 0;
                if ($coupon['not_sale'] == 1) {
                    $res = $orderDetails['summaNotSale'] / 100 * $discount;
                } else {
                    $res = $orderDetails['full_summa'] / 100 * $discount;
                }
                $full_price_discount = $full_price - $res;
                $full_price = $full_price_discount;
                $result .= '%';
                if ($coupon['not_sale'] == 1)
                    $result .= ' на все товары, кроме раздела Sale';
            } elseif ($coupon['type'] == 1) {
                $full_price_discount = $full_price - $discount;
                $full_price = $full_price_discount;
                $result .= ' USD';
            }
            $result .= ')<br />';
            if ($coupon['info'] != '') $result .= 'Дополнительная информация о купоне: <i>' . $coupon['info'] . '</i><br />';
        }
        unset_userdata('coupon');
    }
    $nadbavka = $shop_nadbavka = $orderDetails['nadbavka'];

    $shop_opt_from = $orderDetails['shop_opt_from'];


    $delivery_price_msg = '';
//    $delivery_price = 0;
//    if (isset($orderDetails['deliveryPriceFull']) && $orderDetails['deliveryPriceFull'] > 0) {
//
//        $result .= '<b>Стоимость доставки в страну '.$orderDetails['country']['name'].': ' . get_price($order['delivery_price']) . ' ' . $orderDetails['currencySymb'] . '</b>';
//        if($toAdmin) $result .= '(' . $order['delivery_price'] . '$)';
//        $result .= '<br />';
//    }

    //var_dump($message_table);die();

    //	var_dump($order['summa']);die();

    // $message_table .= '<br />';


    if ($user['user_type_id'] == 11 && $toAdmin)
        $result .= '<b>Заказ от дропшиппера</b><br />';

    if (!$toAdmin) {
        $shop_sended = getOption('shop_sended');
        $result .= $shop_sended;

        // Генерируем ссылку для перехода к заказу и авто авторизации
        $userHash = '?from=email';
        if ($user != false && !$toAdmin)
            $userHash .= '&auto_auth=true&user_id=' . $user['id'] . '&hash=' . md5($user['pass']);

        $result .= 'Следить за статусом своего заказа, а также, оплатить онлайн, Вы можете на странице Вашего заказа: <a href="//peony.ua/user/order-details/' . $order['id'] . '/' . $userHash . '">перейти к заказу</a><br />';
    }
    $result .= 'Фамилия, Имя: ' . $user['lastname'] . ', ' . $user['name'] . '<br />
					e-mail: ' . $user['email'] . '<br />';
    if ($coupon) {
        if (isset($full_price_discount) && $full_price_discount > 0) $full_price = $full_price_discount;
        $result .= '<b>Стоимость товаров со скидкой: ' . get_price($full_price_discount) . ' ' . $orderDetails['currencySymb'];
        if ($toAdmin) $result .= ' (' . $full_price_discount . '$)';
        $result .= '</b><br />';
    } else {
        $result .= 'Стоимость товаров: ' . get_price($orderDetails['summa']) . ' ' . $orderDetails['currencySymb'];
        if ($toAdmin) $result .= ' (' . $orderDetails['summa'] . '$)';
        $result .= '<br />';
    }

    $kolvo = $orderDetails['products_count'];

//    if ($kolvo < $shop_opt_from) {
//        //$shop_nadbavka = $orderDetails['nadbavka'];
//        $result .= '<b">Розничная наценка</b>: ' . get_price($nadbavka) . ' ' . getCurrencySymb($order['currency']);
//        if($toAdmin) $result .=  ' (' . $nadbavka . '$)';
//        $result .= '</b><br />';
//    }
    //vdd($orderDetails);

    // $message_table .= $nadbavka;
    // $message_table .= $delivery_price_msg;
    $result .= 'Общее количество товаров: ' . $kolvo . ' <br />';

    //vd($orderDetails);

    $result .= '<h2><b>Всего к оплате</b>: ' . get_price($orderDetails['full_summa']) . ' ' . $orderDetails['currencySymb'];
    if ($toAdmin) $result .= ' (' . $orderDetails['full_summa'] . '$)';
    $result .= '</h2>';

    //if ($order['delivery'] != 'Новая Почта')
    $result .= 'Доставка: ' . $order['delivery'] . '<br />';

    if ($order['npnp']) {
        $npnp_price = getOption('npnp_price');
        $result .= 'Предоплата наложенного платежа: ' . get_price($npnp_price) . ' ' . $orderDetails['currencySymb'] . '<br />';
        $result .= 'Остаток наложенного платежа: ' . get_price($orderDetails['full_summa'] - $npnp_price) . ' ' . $orderDetails['currencySymb'] . '<br />';
    }

    $result .= 'Адрес: ' . $order['adress'] . '<br />
					Оплата: ' . $order['payment'] . '<br />

					Валюта: ' . $orderDetails['currency']['name'] . '
					<br />
					Дополительная информация:<br />
					' . $order['adding'];

    if ($toAdmin) {
        $result .= '<br/><br><b><a href="http://peony.ua/admin/orders/edit/' . $order['id'] . '/' . adminLoginGetAdding() . '">Перейти к заказу</a></b>';
    }

    return $result;
    //$message = $message . $message_table;


    //echo $message;die();

//    // Обработка купона
//    if ($coupon) {
//        $coupon['used_date'] = date("Y-m-d H:i");
//        $coupon['used_by'] = $user['login'];
//        $coupon['order_id'] = $order['id'];
//        $CI->db->where('id', $coupon['id'])->limit(1)->update('coupons', $coupon);
//
//        if ($coupon['multi'] != 1) {
//            $dbins = array(
//                'coupon_id' => $coupon['id'],
//                'user_login' => $user['login'],
//                'date' => date("Y-m-d H:i"),
//                'order_id' => $order['id']
//            );
//            $CI->db->insert('coupons_using', $dbins);
//        }
//    }
}

function recalculateOrder($order_id){

}

