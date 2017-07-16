<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function prom_getOrders(){
$isNewOrders = false;
    $path = getOption('prom_orders_import_path');
    $xmlStr = file_get_contents($path);
    //vdd($xmlStr);
    if($xmlStr){
//            if(file_exists('import/prom_orders.xml'))
//                unlink('import/prom_orders.xml');
//            file_put_contents('import/prom_orders.xml', $xmlStr);

        loadLibrary('xml2array');

        $xmlwebi = new xml2array();

        $arr = $xmlwebi->xmlwebi($xmlStr);

        $mShop = getModel('shop');
        $mUsers = getModel('users');

        foreach ($arr['orders'][0]['#']['order'] as $order){
            $deliveryType = $paymentType = 'Не указан';
            if(isset($order['#']['paymentType'][0]['#'])) $paymentType = $order['#']['paymentType'][0]['#'];
            if(isset($order['#']['deliveryType'][0]['#'])) $deliveryType = $order['#']['deliveryType'][0]['#'];

            if($deliveryType == 'Нова Пошта')
                $deliveryType = 'Новая Почта';

            $orderArr = array(
                'prom_id' => $order['@']['id'],
                'status' => $order['@']['state'],
                'name' => $order['#']['name'][0]['#'],
                'tel' =>  $order['#']['phone'][0]['#'],
                'email' => $order['#']['email'][0]['#'],
                'date' => $order['#']['date'][0]['#'],
                'adress' => $order['#']['address'][0]['#'],
                'price' => $order['#']['priceUAH'][0]['#'],
                'payment' => $paymentType,
                'delivery' => $deliveryType
            );


            $products = array();
            //vdd($order['#']['items'][0]['#']['item']);
            //vdd(count($order['#']['items'][0]['#']['item']));
            foreach ($order['#']['items'][0]['#']['item'] as $item){
                
                $product = array(
                    'prom_id'   => $item['@']['id'],
                    'external_id'   => $item['#']['external_id'][0]['#'],
                    'name'          => $item['#']['name'][0]['#'],
                    'quantity'          => $item['#']['quantity'][0]['#'],
                    'url'          => $item['#']['url'][0]['#'],
                    'price'          => $item['#']['price'][0]['#'],
                    'sku'          => $item['#']['sku'][0]['#'],

                );

                array_push($products,$product);
// vd($product); echo '<hr>';
                // vdd($products);
                //vdd($item['#']['item'][0]['#']);
            }
            $orderArr['products'] = $products;
//vdd($orderArr);
            // Получаем статус
            $status = 'new';
            if($orderArr['status'] == 'accepted') $status = 'processing';
            elseif($orderArr['status'] == 'declined') $status = 'canceled';
            elseif($orderArr['status'] == 'closed') $status = 'sended';
            // Проверка, не был ли этот заказ добавлен ранее
            $oldOrder = $mShop->getOrderByPromId($orderArr['prom_id']);
            if(! $oldOrder){
                // Поиск клиента в нашей базе
                $user = false;
                if($orderArr['email'] != '')
                    $user = $mUsers->getUserByEmail($orderArr['email']);    // поиск по мылу
                if(!$user && $orderArr['email'] != '')
                    $user = $mUsers->getUserByLogin($orderArr['email']);    // поиск по логину
                if(!$user && $orderArr['tel'] != '')
                    $user = $mUsers->getUserByTel($orderArr['tel']);        // поиск по телефону

                if(!$user){     // добавление нового клиента
                    $name = $lastname = $login = '';
                    if($orderArr['email'] != '') $login = $orderArr['email'];
                    elseif($orderArr['tel'] != '') $login = $orderArr['tel'];
                    else $login = $orderArr['prom_id'];
                    if($orderArr['name'] != ''){
                        $nameArr = explode(' ', $orderArr['name']);
                        if(is_array($nameArr)){
                            if(isset($nameArr[0]))
                                $lastname = $nameArr[0];
                            if(isset($nameArr[1]))
                                $name = $nameArr[1];
                            if(isset($nameArr[2]))
                                $name .= ' '.$nameArr[2];
                        } else $name = $nameArr;
                    }
                    $dbins = array(
                        'login' => $login,
                        'name'  => $name,
                        'lastname' => $lastname,
                        'email' => $orderArr['email'], 
                        'tel' => $orderArr['tel'],
                        'type'  => 'client',
                        'active'=> 1,
                        'reg_date'=>date("Y-m-d"),
                        'adress' => $orderArr['adress'],
                        'register_from' => 'prom',
                        'from' => 'prom'
                    );
                    $user = addUser($dbins);
                }

                if($user){
                    // Добавляем заказ
                    $pArr = array();
                    $i = 0;
                   // vdd($products);

                    $priceAddingsCount = 0;
                    foreach ($products as $product){
                        $resArr = explode('-', $product['external_id']);
                        if(isset($resArr[0]) && isset($resArr[1])){
                            $pArr[$i]['shop_id'] = $resArr[0];
                            $pArr[$i]['kolvo_'.$resArr[1]] = $product['quantity'];
                            $pArr[$i]['kolvo'] = $product['quantity'] ;
                            $pArr[$i]['external_id'] = $product['external_id'];
                            $pArr[$i]['price'] = $product['price'];
                            $pArr[$i]['url'] = $product['url'];
                            $i++;
                        }
                    }

                    if($pArr){
                        $date = $time = $unix = 0;

                        $dtArr = explode(' ', $orderArr['date']);
                        if(isset($dtArr[0]) && isset($dtArr[1])){
                            $dArr = explode('.', $dtArr[0]); // date
                            if(is_array($dArr)){
                                $date = '20'.$dArr[2].'-'.$dArr[1].'-'.$dArr[0];
                                $dresarr = array('20'.$dArr[2],$dArr[1],$dArr[0]);
                                $dArr = $dresarr;
                            }
                            $time = $dtArr[1];
                            $tArr = explode(':', $dtArr[1]); // time

                            if(is_array($dArr) && is_array($tArr)){
                                $unix = mktime($tArr[0],$tArr[1],0,$dArr[1],$dArr[2],$dArr[0]);
                            }
                        }

                        $uah = getCurrencyValue('UAH');
                        $price = $orderArr['price'] / $uah;

                        $viewed = 0;
                        if($status != 'new') $viewed = 1;

                        $dbins = array(
                            'user_id'   => $user['id'],
                            'products'  => serialize($pArr),
                            'products_json'  => json_encode($pArr),
                            'adress'    => $orderArr['adress'],
                            'country'   => 'Украина',
                            'status'    => $status,
                            'currency'  => 'uah',
                            'prom_id'   => $orderArr['prom_id'],
                            'date'      => $date,
                            'time'      => $time,
                            'unix'      => $unix,
                            'domain'    => 'prom.ua',
                            'prom_price'=> $orderArr['price'],
                            'summa'     => $price,
                            'full_summa'=> $price,
                            'payment'   => $orderArr['payment'],
                            'delivery'  => $orderArr['delivery'],
                            'viewed'    => $viewed
                        );
                      //  vdd($dbins);
                        addOrder($dbins);
                        $isNewOrders = true;
                    }

                }

            } elseif($oldOrder['status'] != $status)
                editOrderStatus($oldOrder['id'], $status);





            //vd($orderArr);
        }


        //vdd($arr);


    }
    return $isNewOrders;
}