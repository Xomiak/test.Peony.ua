<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

define('MAIN_URL',"https://".$_SERVER['SERVER_NAME']);
//=====================================================
// VkMarket CodeIgniter
//-----------------------------------------------------
// Модуль переноса товаров в ВК
//-----------------------------------------------------
// Copyright (c) 2016 -=Xom!aK=-
//=====================================================
class Vkmarket {

    private $owner_id = '-124660621';
    //идентификатор группы без "-" (и такое надо)
    private $group_id = '124660621';

    private $vk_category_id = 1;// Аудио - и видеотехника
    //ключ безопасности
    private $access_token = '52aef627392133024c904946fe76180929e76e9757116f293e93014d7ff8e04dacc47ab71accd2c95d4a3';

    //параметры для получения access_token
    //id созданого приложения
    private $client_id = '5527453';
    //секретный ключ созданого приожения
    private $client_secret = 'x79GDm1RFs6yh9XQHJZs';

    private $user_id = '00000000';

    private $CI;

    public function __construct($params = array())
    {
        $this->CI = & get_instance();

        $this->CI->load->helper('sstring_helper');

        $this->CI->load->helper('curl_helper');

        $this->client_id = getOption('vk_client_id');
        $this->client_secret = getOption('vk_client_secret');

        if(userdata('access_token') !== false)
            $this->access_token = userdata('access_token');

        if(isset($params['owner_id']))
            $this->owner_id = '-'.$params['owner_id'];

        if(isset($params['group_id']))
        {
            $this->group_id = $params['group_id'];
            $this->owner_id = '-'.$params['group_id'];
        }

        if(isset($params['access_token']))
            $this->access_token = $params['access_token'];

        if(isset($params['client_id']))
            $this->client_id = $params['client_id'];
        if(isset($params['client_secret']))
            $this->client_secret = $params['client_secret'];


        if(userdata('vk_user_id') !== false)
            $this->user_id = userdata('vk_user_id');

        $this->owner_id = '-'.userdata('group_id');
        $this->group_id = userdata('group_id');
    }



    public function authorize()
    {
        $redirect_uri = "https://".$_SERVER['SERVER_NAME'].request_uri(false, true);

        if(isset($_GET['code']) && !empty($_GET['code']))
        {
            //получение "access_token" через параметр code
            $data = Array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $redirect_uri,
                'code' => $_GET['code'],
            );

            $data_param = Array();
            foreach ($data as $key => $value) {
                $data_param[] = $key . '=' . $value;
            }

            $url = 'https://oauth.vk.com/access_token?'.implode('&',$data_param);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            $content = curl_exec($curl);
            curl_close($curl);

            $token = json_decode($content);

            if(isset($token->access_token))
            {
                set_userdata('access_token', $token->access_token);
                set_userdata('vk_user_id', $token->user_id);
                $this->access_token = $token->access_token;
                $this->user_id      = $token->user_id;

                $activation = false;

                if(!isset($token->email) || $token->email == NULL){
                    $user = getUserByVkId($token->user_id);
                    if(!$user) {
                        redirect('/export/email_required/?back=' . urlencode($_SERVER['REQUEST_URI']));
                        die();
                    } else $token->email = $user['email'];
                }

                if(isset($token->email) && $token->email != '') {
                    $values = array(
                        'email' => $token->email,
                        'access_token' => $token->access_token,
                        'vk_user_id' => $token->user_id
                    );

                    if($activation)
                        $values['activation'] = 0;
                    enter_login($token->email, $values);

                    echo "Разрешение от ВК получено!<br />Обновите страницу!";
                    redirect('/export/to-market/');
                    return true;
                }
            }
        }


        //формируем ссылку для авторизации пользователя и полученя параметра "code"
        $data = Array(
            'client_id' => $this->client_id,
            'redirect_uri' => $redirect_uri,
            'display' => 'popup',
            'scope' => 'market,photos,offline,groups,wall,mail',
            'response_type' => 'code',
            'v' => '5.50',
        );

        $data_param = Array();
        foreach ($data as $key => $value):
            $data_param[] = $key . '=' . $value;
        endforeach;

        $url = 'https://oauth.vk.com/authorize?' . implode('&', $data_param);
        echo '<a href="' . $url . '" style="font-family: \'Bebas Neue Reg\',sans-serif;font-size: 22px;">Начать перенос товаров!</a>';

    }

    //создание нового альбома в группе
    //в случае успеха возвращаем id альбома
    //при ошибке API - объект ошибки
    //при ошибке данных(пустые) - false
    public function add_vk_album($album_name = '', $album_img = '')
    {
        //$album_name - имя создаваемого альбома
        //$album_img - серверный путь к изображению, что нужно загрузить для альбома

        if(!empty($album_name)):

            //загружаем изображение для альбома
            //вызываем функцию загрузки изображение вконтаке
            //в результате должны получить объект фотографии
            $main_photo_id = false;
            if($album_img != '') {
                $photo = $this->add_vk_image($album_img, 'album');

                // 0 - это номер изображения, так их можна загрузить 5 штук (бред какой-то!)
                if (isset($photo->response[0]->pid)) {
                    $main_photo_id = $photo->response[0]->pid;
                }
                //но нам необходим только его id
                //$main_photo_id = $photo->response[0]->pid;
            }

            //проблема бывает с кодированием html символов в названии категории
            //поэтому их необходимо преобразоватьв нормальные
            $album_name = trim(strip_tags(htmlspecialchars_decode($album_name)));

            $url = 'https://api.vk.com/method/market.addAlbum';
            $data = Array(
                'owner_id' => $this->owner_id,
                'title' => $album_name,
                'photo_id' => $main_photo_id,
                'access_token' => $this->access_token
            );

            $result = get_curl($url,$data);
            //echo "asd";
            //vdd($result);

            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                $this->showError($result->error);
                return $result;
            endif;

            //при успешном запросе возвращаем id созданого альбома
            return $result->response->market_album_id;
        else:
            return false;
        endif;
    }


    function getGroups(){
        $url = 'https://api.vk.com/method/groups.get';
        $data = Array(
            'user_id' => $this->user_id,
            'extended' => 1,
            'filter' => 'moder',
            'access_token' => $this->access_token
        );

        $result = get_curl($url,$data);
//        vd($result->error);
        if(isset($result->response))
            return $result->response;
        elseif(isset($result->error))
            $this->showError($result->error);

        return $result;
    }

    public function showError($error)
    {
        echo 'Ошибка: '.$error->error_code.' '.$error->error_msg.'<br />Возможно, Вам необходимо переавторизироваться на сайте! Для этого, перейдите по ссылке: <a href="/login/logout/">Переавторизироваться</a>';
        die();
        //vd($error);
    }

    //загрузка изображения на сервер VK
    //в случае успеха возвращает объект изображения
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    public function add_vk_image($img_path = '', $server_type = 'product')
    {
        //$img_path - путь к загружаемому изображению
        //$server_type - определяет, для кого загружается изображения, так как
        //               видите ли для загрузки фото товара и альбома существуют отдельные методы
        //               (отдельные методы КАРЛ!!!!!!)

        //путь к изображению по умолчанию
        if($server_type == 'album'):
            //изображение для альбома
            $default_image = 'https://peony.ua/upload/images/045bbf066973cc3a9c19c87ab986b265.jpg';
        else:
            //изображение для товара
            $default_image = 'https://'.$_SERVER['SERVER_NAME'].'/img/vk/no_product_image.jpg';
        endif;


        //проверяем есть ли изображение вообще
        if(!empty($img_path) && $img_path != ""):
           // vdd("asd");
            //если есть, то
            //так как вконтакте требует изображение
            //не меньше чем 400х400px для товара и 1280х720px для альбома
            //необходимо проверить размер изображение

            //ГЛАВНАЯ фишка в том, что бы указать серверный путь к изображению

            //вот такие вот дела((
            $image_server_path = $_SERVER['DOCUMENT_ROOT'].$img_path;

            //но для проверки размера изображение необходи путь url
            //то есть https://mysite.com/image/my_image.jpg
            $image_path = 'https://'.$_SERVER['SERVER_NAME'].$img_path;
            //вот так вот и живем

            $image_size = getimagesize($image_path);

            //если размеры изображени есть, то проверяем их
            if($image_size):
                //минимальные размеры фото товара
                $width = 400;
                $height = 400;
                if($server_type == 'album'):
                    //минимальные размеры фото альбома
                    $width = 1280;
                    $height = 720;
                endif;

                if($image_size[0] < $width || $image_size[1] < $height):
                    //если размеры меньше минимальных, то присваиваем стандартное изображение
                    $image_server_path = $default_image;
                endif;
            else:
                //если не удалось получить размеры изображения, то ставим изображение по умолчанию
                $image_server_path = $default_image;
               // vdd($image_server_path);
            endif;
        else:

            //если изображения нет, то ставим стандартное изображение
            $image_server_path = $default_image;
        endif;
        /** ***************** **/

        //необходимо загрузить изображение товара на сервер
        //для этого необходимо сделать 3 шага(КАРЛ 3!!)

        //1 - шаг (photos.getMarketUploadServer)
        //получаем url, куда будет загружаться изображение

        //определяем для кого загружается изображение
        if($server_type == 'album'):
            //для альбома
            $url = 'https://api.vk.com/method/photos.getMarketAlbumUploadServer';
            $data = Array(
                'group_id' => $this->group_id,
                'access_token' => $this->access_token
            );
        else:
            //для товара
            $url = 'https://api.vk.com/method/photos.getMarketUploadServer';
            $data = Array(
                'group_id' => $this->group_id,
                'main_photo' => 1,
                'access_token' => $this->access_token
            );
        endif;

        $upload_server = get_curl($url,$data);


        if(!isset($upload_server->response->upload_url)):
            //если адрес загрузки сервера не вернулся, то возвращаем объект ошибки
            $this->showError($upload_server->error);
            return $upload_server;
        endif;

        //если вернулся путь для загрузки, то идем дальше
        $upload_url = $upload_server->response->upload_url;

        //2 - метод. Отправка изображения методом POST на полученный url
//vd($image_server_path);
        $upload_data = set_post_curl($upload_url,$image_server_path);
       // vdd($upload_data);

        if(!isset($upload_data->server) && !isset($upload_data->photo) && !isset($upload_data->hash)):
            //если НЕ вернулись все нужные данные, то возвращаем ошибку
            $this->showError("Ошибка загрузки обложки");
            return $upload_data;
        endif;

        //если отправка удачно прошла, то переходим к 3 шагу

        //3 - шаг (photos.saveMarketPhoto). Сохранение изображение

        //опять же определяем для кого грузим фото
        if($server_type == 'album'):
            //для альбома
            $url = 'https://api.vk.com/method/photos.saveMarketAlbumPhoto';
            $data = Array(
                'group_id' => $this->group_id,
                'photo' => $upload_data->photo,
                'server' => $upload_data->server,
                'hash' => $upload_data->hash,
                'access_token' => $this->access_token
            );
        else:
            //для товара
            $url = 'https://api.vk.com/method/photos.saveMarketPhoto';
            $data = Array(
                'group_id' => $this->group_id,
                'photo' => $upload_data->photo,
                'server' => $upload_data->server,
                'hash' => $upload_data->hash,
                'crop_data' => $upload_data->crop_data,
                'crop_hash' => $upload_data->crop_hash,
                'access_token' => $this->access_token
            );
        endif;

        $result = get_curl($url,$data);

        if(isset($result->error)):
            //если вернулась ошибка, то возвращаем ее объект
            return $result;
        endif;
        //если запрос успешен, то возвращаем объект изображения
        return $result;
    }

    //добавление нового товара в группу
    //в случае успеха возвращае id товара в группе
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    public function add_vk_product($product, $desc = "", $vk_album_id = false, $images = false)
    {
        //$name - название товара
        //$desc - описание товара
        //$price - цена товара
        //$product_status - доступность товара (1/0 - недоступен/доступен)
        //$pr_img - серверный путь к изображению для товара
        //$pr_url - ссылка на тарницу товара в магазине

        //if (!$user_login) $user_login = userdata('login');

        if (!empty($product['name']) && !empty($product['price']) && ($product['warehouse_sum'] > 0)) {

            $main_photo_id = false;
            $photo_ids = false;
            //вызываем функцию загрузки изображение VK
            //в результате должны получить объект фотографии
            //vd($product);
            if ($product['image'] != '') {
                $pr_img = $product['image'];
                //vd($pr_img);
                $photo = $this->add_vk_image($pr_img, 'product');
               // echo $photo->response[0]->pid;
                // 0 - это номер изображения, так их можна загрузить 5 штук для товара (бред какой-то!)
                if (!isset($photo->response[0]->pid)):
                    return $photo;
                endif;
                //но нам необходим только его id
                $main_photo_id = $photo->response[0]->pid;
            }
            if($images){
                $images_ids = "";
                $count = count($images);
                if($count > 4) $count = 4;
                for($i = 0; $i < $count; $i++){
                    $photos = $this->add_vk_image($images[$i]['image'], 'product');
                    //vd($photos);echo '<hr>';
                    if(isset($photos->response[0]->pid)){
                        if($images_ids != "") $images_ids .= ',';
                        $images_ids .= $photos->response[0]->pid;
                    }
                }
                if($images_ids != '') $photo_ids = $images_ids;
                //vd($images_ids);
            }



            /** *************************** **/

            //так как цена товара в контакте не может быть нулевой, и пустой
            //то при необходимости нужно присвоить цене минимальное значение 0.01
            $vk_nadbavka = userdata('adding_price');
            if(!$vk_nadbavka) $vk_nadbavka = 0;
                $price = getPriceInCurrency($product['price'], $product['discount']);
            if (empty($price) && $price > 0) {
                $price = 0.01;
            } else {
                //если цена есть, округляем ее до двух символов после запятой
                $price = round($price, 2);
            }

            $price = $price + $vk_nadbavka;

            //если получилось загрузить изображение то переходим к непосредственному созданию товаров вконтакте

            //присваиваем category_id с VK
            //там есть стандартный набор категорий, к которым можно отнести товар

            //$vk_category_id = $category;

            //проверка имени
            $name = $product['name'] . ' (' . $product['color'] . ')';
            if (strlen($name) < 4) {
                //минимальная длинна названия товара 4 символа
                $name .= '____';
            }



            //проверка описания
            if (empty($desc) || strlen($desc) < 10) {
                //минимальная длинна описания 10 символов
                $desc = 'Описание товара';
            }

            //сверху описания добавляем ссылку на товар
//            $pr_url = "https://".$_SERVER['SERVER_NAME'];
//            if(!$category)
//            {
//                $this->CI->load->model('Model_categories', 'categories');
//                $category = $this->categories->getCategoryById($product['category_id']);
//            }
//            $pr_url .= '/'.$category['url'].'/'.$product['url'].'/';
//            if(!empty($pr_url)):
//                $desc = $desc.'
//Для оформления заказа перейдите на сайт:'.PHP_EOL.PHP_EOL.$pr_url;


            //проблема бывает с кодированием html символов в названии и описании товара
            //поэтому их необходимо преобразовать в нормальные
            $name = trim(strip_tags(htmlspecialchars_decode($name)));
            $desc = trim(strip_tags(htmlspecialchars_decode(getAnons($desc))));


            $url = 'https://api.vk.com/method/market.add';
            $data = Array(
                'owner_id' => $this->owner_id,
                'group_id'  => $this->group_id,
                'name' => $name,
                'description' => $desc,
                'category_id' => 1,
                'price' => $price,
                'deleted' => 0,
                'main_photo_id' => $main_photo_id,
                'access_token' => $this->access_token
            );
            if($photo_ids) $data['photo_ids'] = $photo_ids;

            $result = get_curl($url, $data);
//vd($result);
            // Помещаем в нужную подборку

            //if(!$vk_album_id) $vk_album_id = userdata('vk_album_id');
            if(userdata('vk_album_id') !== false) $vk_album_id = userdata('vk_album_id');
            //vd($result);
            if (isset($result->response->market_item_id) && $vk_album_id !== false) {
                //return $result->response->market_item_id;
                //echo $result->response->market_item_id;
                //die();
                //$this->CI->load->model('Model_vk', 'vk');
                //$album_id = $category;

//vd($album_id);
                $url = 'https://api.vk.com/method/market.addToAlbum';
                $data = Array(
                    'owner_id' => $this->owner_id,
                    'album_ids' => $vk_album_id,
                    'group_id'  => $this->group_id,
                    'item_id' => $result->response->market_item_id,
                    'access_token' => $this->access_token
                );

                $result2 = get_curl($url, $data);
                //echo "ДОБАВИЛИ ТОВАР в альбом:".$vk_album_id;

                //vd($result2);
                return $result->response->market_item_id;
                //vd($result->response->market_item_id);
            }

            if (isset($result->error)) {
                //если вернулась ошибка, то возвращаем ее объект
                $this->showError($result->error);
                return $result;
            }
        }
        //при успешном запросе возвращаем id созданого товара
        return $result->response->market_item_id;
    }

    //добавление товара в альбом
    //при успешно выполнении возвращает 1
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    function add_product_to_album($item_id = '', $album_ids = '')
    {
        //описание параметров
        //$item_id - id товара
        //$albums_ids - id альбомов в которое нужно добавить товар
        //              если несколько, то разделяются запятой

        if(!empty($item_id) && !empty($album_ids)):
            $url = 'https://api.vk.com/method/market.addToAlbum';
            $data = Array(
                'owner_id' => $this->owner_id,
                'item_id' => $item_id,
                'album_ids' => $album_ids,
                'access_token' => $this->access_token
            );

            $result = $this->get_curl($url,$data);

            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                return $result;
            endif;

            //если запрос успешен, то возвращаем его результат (1)
            return $result->response;
        else:
            return false;
        endif;
    }

    //удаление товара из альбома
    //в случае успеха возвращает 1
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    function remove_product_from_album($item_id = '', $album_ids = '')
    {
        //$item_id - id товара
        //$albums_ids - id альбомов в которое нужно добавить товар
        //              если несколько, то id разделяются запятой

        if(!empty($item_id) && !empty($album_ids)):
            $url = 'https://api.vk.com/method/market.removeFromAlbum';
            $data = Array(
                'owner_id' => $this->owner_id,
                'item_id' => $item_id,
                'album_ids' => $album_ids,
                'access_token' => $this->access_token
            );

            $result = $this->get_curl($url,$data);
            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                return $result;
            endif;

            //если запрос успешен, то возвращаем его результат (1)
            return $result->response;
        else:
            return false;
        endif;
    }

    //получение данных товара в группе
    //в случае успеха возвращает объект товара
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    function get_vk_product($item_ids = '')
    {
        //$item_ids - id товара (можно указать несколько)

        if(!empty($item_ids)):
            $item_ids = $this->owner_id.'_'.$item_ids;
            $url = 'https://api.vk.com/method/market.getById';
            $data = Array(
                'item_ids' => $item_ids,
                'extended' => 1,
                'access_token' => $this->access_token
            );

            $result = $this->get_curl($url,$data);

            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                return $result;
            endif;

            //если запрос успешен, то возвращаем объект товара
            return $result;
        else:
            return false;
        endif;
    }

    //получение данных о группе
    //в случае успеха возвращает объект группы
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    function get_group($group_ids = '')
    {
        //$group_id - id группы (можно указать несколько)

        if(!empty($group_ids)):
            //$group_ids = $this->owner_id.'_'.$group_ids;
            $url = 'https://api.vk.com/method/groups.getById';
            $data = Array(
                'group_ids' => $group_ids,
                'fields' => 'market',
                'access_token' => $this->access_token
            );

            $result = $this->get_curl($url,$data);

            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                return $result;
            endif;

            //если запрос успешен, то возвращаем объект товара
            return $result->response;
        else:
            return false;
        endif;
    }

    //редактирование товара в группе
    //в случае успеха возвращает 1
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    function edit_vk_product($item_id = '', $data = Array())
    {
        //$item_ids - id товара (можно указать несколько)
        //$data - массив параметров в связке [имя]=>[значение], которые должны быть изменены

        if(!empty($item_id) && !empty($data)):
            $url = 'https://api.vk.com/method/market.edit';
            $data['owner_id'] = $this->owner_id;
            $data['name'] = trim(strip_tags(htmlspecialchars_decode($data['name'])));
            $data['description'] = trim(strip_tags(htmlspecialchars_decode(getAnons($data['description'],120))));
            $data['item_id'] = $item_id;
            $data['access_token'] = $this->access_token;


            $result = $this->get_curl($url,$data);

            //vd($result);

            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                return $result;
            endif;
            //если запрос успешен, то возвращаем его результат (1)
            return $result->response;
        else:
            return false;
        endif;
    }

    //удаление товара с группы
    //в случае успеха возвращает 1
    //при ошибке API - объект ошибки
    //при ошибке данных (пустые) - false
    function delete_vk_product($item_id = '')
    {
        //$item_id - id товара, что необходимо удалить

        if(!empty($item_id)):
            $url = 'https://api.vk.com/method/market.delete';

            $data = Array(
                'owner_id' => $this->owner_id,
                'item_id' => $item_id,
                'access_token' => $this->access_token
            );

            $result = $this->get_curl($url,$data);

            if(isset($result->error)):
                //если вернулась ошибка, то возвращаем ее объект
                return $result;
            endif;
            //если запрос успешен, то возвращаем его результат (1)
            return $result->response;
        else:
            return false;
        endif;

    }

    //получение списка товаров группы
    //в случае успеха возвращаем массив объектов товаров
    //при ошибке API - объект ошибки
    public function get_all_products($offset = '', $count = '')
    {
        //$offset - с какой позиции начинать выборку
        //$count - сколько товаров выбирать

        $url = 'https://api.vk.com/method/market.get';
        $data = Array(
            'owner_id' => $this->owner_id,
            'access_token' => $this->access_token
        );

        if(!empty($offset) && !empty($count)):
            $data['offset'] = $offset;
            $data['count'] = $count;
        endif;

        $result = $this->get_curl($url,$data);

        if(isset($result->error)):
            $this->showError($result->error);
            //если вернулась ошибка, то возвращаем ее объект
            return $result;
        endif;

        //при успешном запросе возвращаем id созданого альбома
        return $result->response;

    }

    //получения количества товаров в группе
    public function get_product_total_counts()
    {
        $url = 'https://api.vk.com/method/market.get';
        $data = Array(
            'owner_id' => $this->owner_id,
            'access_token' => $this->access_token
        );

        $result = $this->get_curl($url,$data);

        if(isset($result->error)):
            //если вернулась ошибка, то возвращаем ее объект
            return $result;
        endif;

        //при успешном запросе возвращаем id созданого альбома
        return $result->response[0];
    }

    //получение данных по указаному url
    //используется для вызова методов API вконтакте
    //при ошибке возвращает false
    function get_curl($url='', $data = Array())
    {
        //описание параметров
        //$url - урл, куда будет идти запрос
        //$data - список GET параметров в связке [имя]=>[значение], для передачи на указанный урл

        if(!empty($url) && !empty($data)):

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $out = curl_exec($curl);
            curl_close($curl);
            //ставим паузу от 0,2 до 0,3 секунды
            $rand_time_out = rand(250000, 320000);
            usleep($rand_time_out);

            return json_decode($out);
        else:
            return false;
        endif;
    }

    //отправка данных(изображения/файла) методом POST на указанный url
    //при ошибке возвращает false
    function set_post_curl($upload_url, $img_url)
    {
        //описание параметров
        //$upload_url - урл, куда будет идти запрос
        //$img_url - путь к файлу (серверный)

        if(!empty($upload_url) && !empty($img_url)):
            $post_params = array(
                'file' => '@'.$img_url
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $upload_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
            $out = curl_exec($curl);
            curl_close($curl);

            return json_decode($out);
        else:
            return false;
        endif;
    }

}