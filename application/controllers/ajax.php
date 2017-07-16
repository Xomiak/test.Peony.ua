<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->helper('shop_helper');
        $this->load->helper('order_helper');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_articles', 'articles');
        $this->load->model('Model_categories', 'categories');
        $this->load->model('Model_vk', 'vk');
        $this->load->model('Model_users', 'users');
    }

    public function showBlock($name){
        if($name == 'form_adress'){
            $user = false;
            if(userdata('login') != false)
                $user = $this->users->getUser(userdata('login'));

            $addr = false;
            $addr_id = post('addr_id');


            if($addr_id != 0){
                $model = getModel('shop');
                $addr = $model->getAddr($addr_id);
            }

            $data['addr_id'] = $addr_id;
            $data['addr'] = $addr;
            $data['user'] = $user;
            $data['title'] = 'Редактирование адреса';
            if($addr_id == 0) $data['title'] = 'Добавление адреса';
            $this->load->view('shop/popup_addr_form.php', $data);
        }
    }

    public  function get_my_cart_details(){
        $products = getMyCartData();
        //$currency = getCurrencyByCode(userdata('currency'));
        //vd($products);
        if(isset($products['products_count']) && $products['products_count'] > 0) {
            ?>
            <div class="cart-container">
            <div class="responsive-table fast-order">
            <table class="one-click-table" width="100%">
                <tr style="">
                    <th>Название</th>
                    <th>Размер</th>
                    <th>Цвет</th>
                    <th>Количество</th>
                    <th>Цена (за 1 единицу)</th>
                </tr>
            <?php
            foreach ($products['products'] as $product) {
                if (isset($product['id'])) {
                    $size = "";
                    if(isset($product['sizes'])){
                        $sizesCount = count($product['sizes']);
                        for($i = 0; $i < $sizesCount; $i++){
                            $item = $product['sizes'][$i];
                            $size .= $item;
                            if(($i+1) < $sizesCount) $size .= ' | ';
                        }
                    }
                    $shop = $this->shop->getArticleById($product['id']);
                    if ($shop) {
                        ?>
                        <tr>
                            <td><a href="<?=getFullUrl($shop)?>"><?=$shop['name']?></a></td>
                            <td><?=$size?></td>
                            <td><?=$shop['color']?></td>
                            <td><?=$product['count']?></td>
                            <td><?=get_price($product['final_price'])?> <?=$products['currencySymb']?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </table>
                <div class="one-click-addings">
                <?php
                if(isset($products['nadbavka']) && $products['nadbavka'] > 0)
                    echo 'Розничная надбавка: '.get_price($products['nadbavka']).' '.$products['currencySymb'].'<br/>';
                if(isset($products['deliveryPriceFull']) && $products['deliveryPriceFull'] > 0)
                    echo 'Доставка в Вашу страну: '.get_price($products['deliveryPriceFull']).' '.$products['currencySymb'].'<br/>';
                ?>
                    <strong>Всего: </strong><?=get_price($products['full_summa'])?> <?=$products['currencySymb']?>
                </div>
            </div>
            </div><br /><br />
            <?php
        }
        //vd($products);
    }

    public function fast_order(){
        $user = false;
        if(userdata('login') !== false)
            $user = getUserIdBylogin(userdata('login'), true);
        loadHelper('one_click');
        //echo get_one_click_form_content();

        echo get_one_click_form_content();

    }

    public function create_one_click_order(){
        //$my_cart = userdata('my_cart');
        if(!isset($_POST['one_click_tel']) && isset($_GET['one_click_tel'])) $_POST['one_click_tel'] = $_GET['one_click_tel'];

        $login = $email = $tel = "";
        if(isset($_POST['tel'])) $tel = post('tel');
        if(isset($_POST['email'])) $email = post('email');
        if(!isset($_POST['login'])) $login = $email;
        set_userdata('tel', $tel);
        set_userdata('login', $login);
        set_userdata('email', $email);

        $userInfo = array(
            'login' => post('login'),
            'email' => userdata('email'),
            'tel'   => userdata('tel')
        );
        loadHelper('order');
        $order = createNewOrder($userInfo, array('one_click_order' => 1, 'one_click_tel' => post('one_click_tel')));
        if($order){
            ?>
            Перенаправление...
            <script>
                window.location.href = 'http://<?=$_SERVER['SERVER_NAME']?>/my_cart/sended/<?=$order['id']?>/';
            </script>
            <?php
        } else echo 'При оформлении заказа в 1 клик произошла ошибка! Рекомендуем совершить заказ через корзину, указав свои контактные данные, либо связаться с нашим менеджером.';
    }

    public function fast_order_form(){
        ?>
        <input id="phone" type="tel" required name="tel">
        <span id="valid-msg" class="hide">✓ Правильный</span>
        <span id="error-msg" class="hide">Не правильный номер</span>


        <link rel="stylesheet" href="/css/prism.css">
        <link rel="stylesheet" href="/css/intlTelInput.css?1475869934842">
        <link rel="stylesheet" href="/css/demo.css?1475869934842">
        <link rel="stylesheet" href="/css/isValidNumber.css?1475869934842">

        <script src="/js/prism.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="/js/intlTelInput.js?1475869934842"></script>
        <script src="/js/isValidNumber.js?1475869934842"></script>
        <script>
            $(document).ready(function () {


            var telInput = $("#phone"),
                errorMsg = $("#error-msg"),
                validMsg = $("#valid-msg");

            // initialise plugin
            telInput.intlTelInput({
                utilsScript: "/js/utils.js"
            });

            var reset = function() {
                telInput.removeClass("error");
                errorMsg.addClass("hide");
                validMsg.addClass("hide");
            };

            // on blur: validate
            telInput.blur(function() {
                reset();
                if ($.trim(telInput.val())) {
                    if (telInput.intlTelInput("isValidNumber")) {
                        validMsg.removeClass("hide");
                    } else {
                        telInput.addClass("error");
                        errorMsg.removeClass("hide");
                    }
                }
            });

            // on keyup / change flag: reset
            telInput.on("keyup change", reset);
            });
        </script>
        <?php
    }

    public function getAddr($id){
        $this->load->model('Model_users','users');
        $addr = $this->users->getAddressById($id);
        if($addr) {
            echo json_encode($addr);
        }
    }

    public function userdata($action)
    {
        if (isset($_GET['name'])) $_POST['name'] = $_GET['name'];
        if (isset($_GET['value'])) $_POST['value'] = $_GET['value'];
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($action == 'set') {
            set_userdata($name, $value);
            echo $name.": ".$value;
        } elseif ('unset'){
            unset_userdata($name);
            echo $name." unsetted";
        }
    }

    public function vk($action)
    {
        $this->load->library('vkmarket');
        $this->vkmarket = new vkmarket();
        if ($action == 'get_group' && isset($_POST['group_id'])) {
            $group = $this->vkmarket->get_group(post('group_id'));
            if (isset($group[0])) {
                if ($group[0]->market->enabled == 1) {
                    if (isset($group[0]->market->currency->name)) {
                        $currency = strtolower($group[0]->market->currency->name);
                        set_userdata('currency', $currency);
                        echo $currency;
                    }
                } else echo "no_market";
            }
        } elseif ($action == 'create_export') {
            if (userdata('random_id') == false) {
                // генерируем random_id
                $random_id = getRandCode(10, 30);
                $res = $this->vk->getExportLogByRandomId($random_id);
                while ($res) {
                    $random_id = getRandCode(10, 30);
                    $res = $this->vk->getExportLogByRandomId($random_id);
                }
                // создаём экспорт
                set_userdata('random_id', $random_id);
                $dbins = array(
                    'random_id' => $random_id,
                    'type' => post('type'),
                    'login' => userdata('login'),
                    'date' => date("Y-m-d"),
                    'time' => date("H:i"),
                    'unix' => time(),
                    'vk_user_id' => userdata('vk_user_id'),
                    'vk_group_id' => post('vk_group_id'),
                    'category_id' => post('category_id')
                );

                $this->db->insert('export_logs', $dbins);
            } else $random_id = $this->userdata('random_id');
            echo $random_id;
        }
    }

    private function getAlbumsIdsByCategoryIds($category_id, $vk_user_id = false, $group_id = false)
    {
        $msg = "";
        if (!$vk_user_id) $vk_user_id = userdata('vk_user_id');
        if (!$group_id) $group_id = userdata('group_id');
        $idsarr = explode('*', $category_id);
        $albums_ids = "";
        if (is_array($idsarr)) {
            $count = count($idsarr);
            for ($i = 0; $i < $count; $i++) {
                $category_id = $idsarr[$i];
                $vk_album_id = $this->vk->getMarketCategory($category_id, $vk_user_id, $group_id);
                if (!$vk_album_id) { // создаём подборку
                    $cat = $this->model_categories->getCategoryById($category_id, 1);
                    if ($cat) {
                        $vk_album_id = $this->addMarketAlbum($cat, $vk_user_id, $group_id);
                    }
                }
                if ($vk_album_id) {
                    $albums_ids .= $vk_album_id;
                    if (($i + 1) < $count) $albums_ids .= ',';
                }
            }
        }

        return $albums_ids;
    }

    private function exportProduct($product, $vk_album_id = false)
    {
        $this->load->model('Model_images', 'images');

        $user = $this->users->getUserByLogin(userdata('login'));
        $group_id = userdata('group_id');
        $vk_user_id = userdata('vk_user_id');
        if (!$vk_album_id)
            $vk_album_id = userdata('vk_album_id');


        $exists = $this->vk->isProductExists($product['id'], $vk_user_id, $group_id);
        //vdd($group_id);
        $cat = $this->categories->getCategoryById($product['category_id']);
        $vk_category_id = $this->model_categories->getVkCategoryId($cat['id'], $group_id);
        $desc = $this->getDescription($product, false);
        $vk_album_id = $this->vk->getMarketCategory($cat['id'], $vk_user_id, $group_id);
        //echo $cat['id'].' '.$vk_user_id.' '.$group_id."/r/n";
        $name = $product['name'] . ' (' . $product['color'] . ')';
        //$vk_album_ids = $this->getAlbumsIdsByCategoryIds($product['category_id'], $vk_user_id, $group_id);

        if (!$exists) { // если товар ещё небыл добавлен
            // подгружаем доп. фото товара
            $images = $this->images->getByShopId($product['id'], 1, 1);


            //echo "Найдена подборка ID: ".$vk_album_id;

            $vk_product_id = $this->vkmarket->add_vk_product($product, $desc, $vk_album_id, $images);

            //$vk_product_id = false;
            //vdd($vk_product_id);
            if ($vk_product_id && $vk_product_id != '') {
                // vd($album_id);

                //$this->vkmarket->add_product_to_album($vk_product_id, $vk_album_ids);

                $dbins = array(
                    'shop_id' => $product['id'],
                    'vk_album_id' => $vk_album_id,
                    'vk_product_id' => $vk_product_id,
                    'name' => $name,
                    'category_name' => $cat['name'],
                    'date' => date("Y-m-d"),
                    'time' => date("H:i"),
                    'user_id' => $vk_user_id,
                    'login' => userdata('login'),
                    'group_id' => $group_id
                );
                // добавляем запись в базу о созданном товаре
                $this->db->insert('vkmarket_product_to_vk', $dbins);
                //echo $product['name'].' ('.$product['color'].') добавлен в подборку '.$vk_album_id;
                return $vk_product_id;
                //die();
            } else {
                $this->vkmarket->authorize();
                die();
            }
            // vdd("as");
        } else { // Редактирование уже добавленного товара
            $vk_nadbavka = userdata('adding_price');
            if (!$vk_nadbavka) $vk_nadbavka = 0;
            $price = getPriceInCurrency($product['price'], $product['discount']);
            if (empty($price) && $price > 0) {
                $price = 0.01;
            } else {
                //если цена есть, округляем ее до двух символов после запятой
                $price = round($price, 2);
            }

            $price = $price + $vk_nadbavka;

            $data = array();
            $data['name'] = $name;
            $data['description'] = $desc;
            $data['price'] = $price;
            $data['category_id'] = 1;

            $vk_product = $this->vkmarket->get_vk_product($exists['vk_product_id']);
            //$this->vkmarket->add_product_to_album($exists['vk_product_id'], $vk_album_ids);

            $response = false;

            if ($vk_product) {
                $photo_ids = "";
                if (isset($vk_product->response[1]->photos[0]->pid))
                    $data['main_photo_id'] = $vk_product->response[1]->photos[0]->pid;

                $i = 1;
                while (isset($vk_product->response[1]->photos[$i]->pid)) {
                    if ($i > 1) $photo_ids .= ',';
                    $photo_ids .= $vk_product->response[1]->photos[$i]->pid;
                    $i++;
                }

                $data['photo_ids'] = $photo_ids;
                $response = $this->vkmarket->edit_vk_product($exists['vk_product_id'], $data);
            } else {
                // удаление данных о товаре из нашей базы, т.к. он не найден в ВК
                // и добавление товара заново
                echo 'товар был удалён!';
            }


            if (!$response)
                echo 'error';
            else
                echo $product['name'] . ' (' . $product['color'] . ') был успешно обновлён!';
        }
    }

    function to_market($action)
    {
        $msg = "";
        $this->load->library('Vkmarket');

        if (!userdata('currency')) {
            userdata('currency', 'uah');
        }
//        $lastProductId = userdata('lastProductId');
//        $index = userdata('index');

        if (userdata('login') == false) {
            echo 'Необходимо заново пройти авторизацию. Обновите страницу...';
            die();
        }

        if (isset($_GET['category_id'])) $_POST['category_id'] = $_GET['category_id'];
        if (isset($_GET['vk_album_id'])) $_POST['vk_album_id'] = $_GET['vk_album_id'];
        if (isset($_GET['index'])) $_POST['index'] = $_GET['index'];
        if (isset($_GET['lastProductId'])) $_POST['lastProductId'] = $_GET['lastProductId'];

        $vk_album_id = post('vk_album_id');
        $lastProductId = post('lastProductId');
        $index = post('index');
        $category_id = post('category_id');
        $group_id = userdata('group_id');
        $vk_user_id = userdata('vk_user_id');
        $products = false;

        if ($action == 'add') {
            $sort = post('sort');
            if (!$sort) $sort = userdata('sort');
            if (!$sort) $sort = 'ASC';

            $group_id = userdata('group_id');
            $params = array(
                'group_id' => $group_id
            );

            //vdd($params);

            //  $vkmarket = new Vkmarket($params);

            if ($category_id == false || $category_id == 'all' || $category_id == -1)
                $products = $this->shop->getArticlesSortedByCategory(1, $index, $sort, true);
            else $products = $this->shop->getArticlesByCategory($category_id, 1, $index, 1, $sort,'id',false,false,-1,true);

            if (isset($products[0])) {
                $product = $products[0];
                //echo "Приступаем к экспорту товара ".$products[0]['name'].' ('.$products[0]['color'].')';
                //die();
                $new_product = $this->exportProduct($product);
                if ($new_product && userdata('vk_album_id') !== false ) {
                    $cat = $this->model_categories->getCategoryById($product['category_id']);
                    $vk_album_id = $this->vk->getAlbumByName($cat['name'], $vk_user_id, $group_id);
                    if (!$vk_album_id) { // создаём подборку
                        $vk_album_id = $this->addMarketAlbum($cat, $vk_user_id, $group_id);
                        if ($vk_album_id) $msg .= 'Создана подборка ' . $cat['name'] . "\r\n";
                        else $msg .= "Ошибка создания подборки" . "\r\n";
                    }
                    $vk_album_ids = $this->getAlbumsIdsByCategoryIds($product['category_id'], $vk_user_id, $group_id);
                    $vk_album_ids .= ',20';
                    if ($vk_album_ids)
                        $this->vkmarket->add_product_to_album($new_product, $vk_album_ids);
                    $msg .= 'Товар ' . $product['name'] . ' (' . $product['color'] . ') добавлен в подборку ' . $cat['name'] . "\r\n";
                }
            } else {
                $this->load->helper('mail_helper');
                $user = $this->users->getUserByLogin(userdata('login'));
                if ($category_id > 0) {
                    $cat = $this->model_categories->getCategoryById($category_id);
                } else {
                    $cat['name'] = "Вся коллекция Peony";
                }
                $msg = 'Пользователь ' . $user['name'] . ' ' . $user['lastname'] . ' (' . userdata('login') . ') экспортировал товары (' . $cat['name'] . ') МАРКЕТ в ВК, установив наценку ' . userdata('adding_price') . ' ' . userdata('currency') . '<br />
<a href="https://vk.com/club' . userdata('group_id') . '">перейти в группу</a><br />Клиенту присвоен тип "ВК"</br />Всего обработано ' . $index . ' позиций';
                mail_send(getOption('admin_email'), 'Произведён экспорт в группу ВК', $msg);

                $random_id = userdata('random_id');

                // Сохраняем лог экспорта
                if ($random_id) {
                    $dbins = array(
                        'ended' => 1,
                        'end_datetime' => date("Y-m-d H:i")
                    );
                    $this->db->where('random_id', $random_id);
                    $this->db->limit(1);
                    $this->db->update('export_logs', $dbins);
                } else {
                    $dbins = array(
                        'type' => 'vkmarket',
                        'login' => userdata('login'),
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'unix' => time(),
                        'vk_user_id' => $vk_user_id,
                        'vk_group_id' => $group_id,
                        'category_id' => $category_id,
                        'log' => 'Обработано ' . $index . ' позиций'
                    );
                    $this->db->insert('export_logs', $dbins);
                }


                // Мегяем тип пользователя на ВК
                if ($user['type'] != 'admin' && $user['user_type_id'] != 12)
                    $this->db->where('id', $user['id'])->limit(1)->update('users', array('type' => 12, 'user_type' => 'ВК', 'user_type_id' => 12));
                echo "finish";
                die();
            }
            echo $msg;
        } elseif ($action == 'add_album') {
            $cat = false;
            if ($category_id > 0) {
                $cat = $this->model_categories->getCategoryById($category_id);
            } else {
                $cat = array(
                    'name' => 'Коллекция одежы PEONY',
                    'id' => -1,
                    'image' => '/upload/logos/2e90ec33ef67e0710672f7e4f27e3081.jpg' // Указать путь к картинке для общей подборки!!!
                );
            }
            if ($cat) {
                $vk_album_id = $this->addMarketAlbum($cat, $vk_user_id, $group_id);
                if ($vk_album_id) echo $vk_album_id;
                else echo "Ошибка создания подборки";
            } else echo "error! category id=" . $category_id . " not exists!";
        } elseif ($action == 'check_category') {
            if (isset($_POST['group_id'])) {
                $group_id = $_POST['group_id'];
            }
            $vk_album_id = $this->vk->getMarketCategory($category_id, $vk_user_id, $group_id);
            if ($vk_album_id) echo $vk_album_id;
            else echo 'no_category';
        }
    }

    private function addMarketAlbum($cat, $vk_user_id = false, $group_id = false)
    {
        if (!$vk_user_id) $vk_user_id = userdata('vk_user_id');
        if (!$group_id) $group_id = userdata('group_id');
        $image = false;
        if ($cat['image'] != '') $image = $cat['image'];
        $vk_album_id = $this->vkmarket->add_vk_album($cat['name'].' PEONY', $image);
        if ($vk_album_id) {
            $dbins = array(
                'category_id' => $cat['id'],
                'vk_album_id' => $vk_album_id,
                'name' => $cat['name'],
                'user_id' => $vk_user_id,
                'group_id' => $group_id,
                'login' => userdata('login'),
                'date' => date("Y-m-d"),
                'time' => date("H:i")
            );
            $this->db->insert('vkmarket_category_to_vk', $dbins);

            set_userdata('vk_album_id', $vk_album_id);
            return $vk_album_id;
        } else {
            return false;
        }
    }

    function export($action)
    {
        $this->load->library('Vkalbum');

        // проверяем GET параметры
        if (isset($_GET['vk_album_id'])) $_POST['vk_album_id'] = $_GET['vk_album_id'];
        if (isset($_GET['category_id'])) $_POST['category_id'] = $_GET['category_id'];
        if (isset($_GET['lastProductId'])) $_POST['lastProductId'] = $_GET['lastProductId'];
        $category_id = post('category_id');
        $index = post('index');
        $group_id = post('group_id');
        $albumOrGroup = post('albumOrGroup');
        if($albumOrGroup == 'album'){
            unset_userdata('group_id');
            $group_id = false;
        } elseif(!$group_id) $group_id = userdata('group_id');


        $cat = array();
        if ($category_id == 'all' || $category_id == -1) {
            //$category_id = $cat['id'] = -1;
            $category_id = 21;
            //$cat['name'] = "Коллекция PEONY";
        } else {
            $cat = $this->categories->getCategoryById($category_id);
        }

        if ($action == 'create_album') {
            $album_id = false;


            if ($cat) {
                //if (userdata('create_new_album') != true)
                    $album_id = $this->vk->getUserAlbumId($cat['id'],false,$group_id); // проверяем, есть ли альбом

                if ($album_id) {
                    echo $album_id;
                    die();
                }
                // создаём новый альбом
                $album_id = $this->vkalbum->add_vk_album($cat['name'],'',$group_id);
                if ($album_id) {
                    $dbins = array(
                        'vk_album_id' => $album_id,
                        'group_id'  => $group_id,
                        'category_id' => $category_id,
                        'owner_id' => userdata('owner_id'),
                        'login' => userdata('login'),
                        'user_id' => userdata('vk_user_id'),
                        'name' => $cat['name'],
                        'access_token' => userdata('access_token')
                    );
                    $this->db->insert('vk_albums', $dbins);
                    echo $album_id;
                } else echo 'error!';
            } else echo 'error!';
        } // загрузка фото
        elseif ($action == 'add_foto') {
            $vk_user_id = userdata('vk_user_id');
            $shop = false;
            $sort = post('sort');
            if ($category_id == 'all' || $category_id == -1)
                $shop = $this->shop->getArticlesForExport(1, $index, 'category_id', $sort, 'DESC', true, true);
            else
                $shop = $this->shop->getArticlesByCategory($category_id, 1, $index, 1, $sort, 'id',false,false,-1,true);
            if (isset($shop[0])) {
                $item = $shop[0];
                $vk_album_id = false;

                $vk_album = $this->vk->getUserAlbum($item['category_id'], $vk_user_id, $group_id);
                if(isset($vk_album['vk_album_id'])) $vk_album_id = $vk_album['vk_album_id'];
                //$vk_album_id = $this->vk->getUserAlbumId($item['category_id'], $vk_user_id, $group_id);
                // Если есть альбом, то проверяем его существование в ВК
                if($vk_album_id){
                    $owner_id = $vk_user_id;
                    if($group_id)
                        $owner_id = '-'.$group_id;

                    $vkvk_album = $this->vkalbum->get_album($vk_album_id, $owner_id);
                    //vd($vkvk_album);
                    if(isset($vkvk_album['response']) && count($vkvk_album['response']) == 0){
                        // Пересоздаём альбом
                        $vk_album_id = $this->vkalbum->add_vk_album($cat['name'].' PEONY','',$group_id);
                        if($vk_album_id){
                            $this->db->where('id', $vk_album['id'])->limit(1)->update('vk_albums', array('vk_album_id'=>$vk_album_id));
                            $this->db->where('album_id', $vk_album['vk_album_id'])->update('vk_albums_images', array('album_id'=>$vk_album_id));
                            echo "Альбом не найден, создан новый"."\r\n";
                        }
                    }
                }

                $cat = $this->model_categories->getCategoryById($item['category_id']);
                if (!$vk_album_id) {  // Создаём новый альбом
                    $category_id = $cat['id'];
                    $vk_album_id = $this->vkalbum->add_vk_album($cat['name'].' PEONY','',$group_id);

                    if ($vk_album_id) {
                        $dbins = array(
                            'vk_album_id' => $vk_album_id,
                            'group_id'      => $group_id,
                            'category_id' => $category_id,
                            'owner_id' => userdata('owner_id'),
                            'login' => userdata('login'),
                            'user_id' => userdata('vk_user_id'),
                            'name' => $cat['name'],
                            'access_token' => userdata('access_token')
                        );
                        $this->db->insert('vk_albums', $dbins);
                        echo "Создан новый альбом: " . $cat['name']."\r\n";
                    } else echo "Ошибка создания альбома: " . $cat['name'];
                }

                if (!$vk_album_id) {
                    echo "Ошибка: не определён альбом!";
                    //alert("no vk_album_id: ".$vk_album_id);
                    die();
                }

                if ($item['image_vk'] != NULL && $item['image_vk'] != '') $item['image'] = $item['image_vk'];
                if ($item['image'] != '') {
                    // проверяем, не была ли эта картинка загружена ранее
                    $img = $this->vk->getImageByAlbumUserIdProductId($vk_album_id, $vk_user_id, $item['id'], $group_id);
                    if (isset($img['pid'])) {
                        $description = $this->getDescription($item);

                        $image_id = $img['pid'];
                        // Проверяем, есть ли фото
                        $photo = $this->vkalbum->get_photo($image_id, $vk_user_id, $group_id);

                        //vd($photo['error']);

                        if(isset($photo['error'])){
                            // Загружаем фото заново
                            $ret = $this->vkalbum->add_vk_image($item['image'], $vk_album_id, $description, $group_id);
                            if(isset($ret->response[0]->pid)){
                                $this->db->where('id',$img['id'])->limit(1)->update('vk_albums_images', array('pid' => $ret->response[0]->pid));
                                echo 'Ранее экспортированный товар ' . $item['name'] . ' (' . $item['color'] . ') не был найден в альбомах и был добавлен заново (' . $cat['name'] . ')';
                            }
                        } else {

                            // Редактируем загруженную ранее картинку

                            $data = array(
                                'description' => $description
                            );
                            if ($group_id) $data['group_id'] = $group_id;
                            $response = $this->vkalbum->edit_vk_foto($image_id, $data);
                            if ($response) {
                                echo 'Товар ' . $item['name'] . ' (' . $item['color'] . ') успешно обновлён (' . $cat['name'] . ')';
                            } else {
                                echo 'Ошибка обновления товара ' . $item['name'] . ' (' . $item['color'] . ')!' . "\r\n";
                            }
                        }
                    } else {

                        // Загружаем новую картинку
                        $description = $this->getDescription($item);
                        $ret = $this->vkalbum->add_vk_image($item['image'], $vk_album_id, $description, $group_id);
                       // vd($ret);
                        if ($ret) {
                            if (isset($ret->error)) {
                                echo $ret->error->error_msg;
                                die();
                            }
                            //vd($ret);
                            $pid = $ret->response[0]->pid;
                            $dbins = array(
                                'pid' => $pid,
                                'user_id' => $vk_user_id,
                                'album_id' => $vk_album_id,
                                'group_id'  => $group_id,
                                'login' => userdata('login'),
                                'product_id' => $item['id'],
                                'access_token' => userdata('access_token'),
                                'date' => date("Y-m-d"),
                                'time' => date("H:i"),
                                'unix' => time()
                            );
                            $this->db->insert('vk_albums_images', $dbins);
                            echo "Товар ".$item['name'].' ('.$item['color'].') успешно добавлен! ('.$cat['name'].')';
                            die();
                        } else {
                            echo 'error';
                            //vd($vk_album_id);
                            //alert("add_vk_image error: ".$ret);
                        }
                    }
                }
            }else{
                // FINISH
                $this->load->helper('mail_helper');
                $user = false;
                $this->load->model('Model_users', 'users');
                $user = $this->users->getUserByLogin(userdata('login'));
                $cat_name = "Все товары";
                if ($category_id != -1 && $category_id != 'all') {
                    $cat = $this->model_categories->getCategoryById($category_id);
                    if ($cat) $cat_name = $cat['name'];
                }

                // Сохраняем лог экспорта
                $random_id = userdata('random_id');
                if ($random_id) {
                    $dbins = array(
                        'ended' => 1,
                        'end_datetime' => date("Y-m-d H:i")
                    );
                    $this->db->where('random_id', $random_id);
                    $this->db->limit(1);
                    $this->db->update('export_logs', $dbins);
                } else {
                    $dbins = array(
                        'type' => 'vkalbums',
                        'login' => userdata('login'),
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'unix' => time(),
                        'vk_user_id' => $vk_user_id,
                        'category_id' => $category_id,
                        'log' => 'Обработано ' . $index . ' позиций'
                    );
                    $this->db->insert('export_logs', $dbins);
                }

                // отправляем письмо
                if(!$group_id) {
                    $msg = 'Пользователь ' . $user['name'] . ' ' . $user['lastname'] . ' (' . userdata('login') . ') экспортировал товары (' . $cat_name . ') себе в ВК, установив наценку ' . userdata('adding_price') . ' ' . userdata('currency') . '<br />
<a href="https://vk.com/id' . userdata('vk_user_id') . '">перейти на страницу пользователя</a>';

                }else {
                    $msg = 'Пользователь ' . $user['name'] . ' ' . $user['lastname'] . ' (' . userdata('login') . ') экспортировал товары (' . $cat_name . ') в фотоальбомы группы в ВК, установив наценку ' . userdata('adding_price') . ' ' . userdata('currency') . '<br />
<a href="https://vk.com/club' . userdata('group_id') . '">перейти в группу</a>. Если не открывается, то:<br />
<a href="https://vk.com/public' . userdata('group_id') . '">перейти в группу</a>';
                }
                mail_send(getOption('admin_email'), 'Произведён экспорт в ВК', $msg);
                echo 'finish';
            }
        } elseif ($action == 'check_category') {
            $category_id = post('category_id');
            $group_id = post("group_id");
            $album_id = $this->vk->getUserAlbumId($category_id, false,$group_id);

            if ($album_id) echo $album_id;
            else echo "no_category";
        }
    }

    private function getDescription($item, $addPrice = true)
    {
        $description = "";

        $cat = $this->model_categories->getCategoryById($item['category_id']);
        if ($cat['name_one'] == '' || $cat['name_one'] == NULL) $cat['name_one'] = $cat['name'];

        $price = $sprice = $curval = "";
        if ($addPrice) {
            $price = getNewPrice($item['price'], $item['discount']);


            // выставляем в нужной валюте
            $currency = userdata('currency');
            $curval = "$";
            if (!$currency) $currency = 'uah';

            if ($currency == 'uah') {
                $val = getCurrencyValue('UAH');
                $price = $price * $val;
                $curval = ' грн';
            } elseif ($currency == 'rub') {
                $val = getCurrencyValue('RUB');
                $price = $price * $val;
                $curval = ' руб';
            }

            // надбавка
            $adding_price = userdata('adding_price');
            if ($adding_price != '' && $adding_price != 0) {
                $price = (double)$price + (double)$adding_price;
            }

            $sprice = 'Данный товар закончился';
            if($item['warehouse_sum'] > 0)
                $sprice = 'Цена: ' . $price . $curval;
            $description = $cat['name_one'] . ' ' . $item['name'] . ' (' . $item['color'] . ')' . "
" . $sprice . '

';
        }


        $description .= "Артикул: " . $item['articul'] . "
";
        if ($item['tkan'] != '') $description .= 'Ткань: ' . $item['tkan'] . '
';
        if ($item['sostav'] != '') $description .= 'Состав: ' . $item['sostav'] . '
';
        $razmery = "";
        $sizes = str_replace('*', ', ', $item['razmer']);

        $warehouse = json_decode($item['warehouse'],true);
        $arr = explode('*', $item['razmer']);
        $first = true;
        if(is_array($arr)){
            foreach ($arr as $r){
                if(isset($warehouse[$r]) && $warehouse[$r] > 0){
                    if(!$first) $razmery .= ', ';
                    $razmery .= $r;
                    $first = false;
                }
            }
        }

        $description .= 'Размеры: ' . $razmery . '

';
        if ($item['short_content'] != '') $description .= strip_tags($item['short_content']);
        elseif ($item['content'] != '') $description .= strip_tags($item['content']);

        return $description;
    }

    public function rate()
    {
        $data['msg'] = 'Спасибо. Ваш голос учтен';
        $data['status'] = 'OK';
        echo json_encode($data);
    }

    function set_cookies()
    {
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        set_userdata($name, $value);
    }

    private function upload_foto($name = 'userfile1'){
        // Проверка наличия папки текущей даты. Если нет, то создать
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/revirews/'.date("Y-m-d").'/'))
        {
            mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/revirews/'.date("Y-m-d").'/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] 	= 'upload/revirews/'.date("Y-m-d");
        $config['allowed_types'] 	= 'jpg|png|gif|jpe';
        $config['max_size']		= '0';
        $config['max_width']  	= '0';
        $config['max_height']  	= '0';
        $config['encrypt_name']	= true;
        $config['overwrite']  	= false;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload($name))
        {
            echo $this->upload->display_errors();
            die();
        }
        else
        {
            $ret = $this->upload->data();

            $width = $this->options->getOption('article_foto_max_width');
            $height = $this->options->getOption('article_foto_max_height');
            if(!$width) $width = 200;
            if(!$height) $height = 200;

            if(($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
            if(($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];


            $config['image_library'] 		= 'GD2';
            $config['create_thumb'] 		= TRUE;
            $config['maintain_ratio'] 		= TRUE;
            $config['width'] 			= $width;
            $config['height'] 			= $height;
            $config['source_image'] 		= $ret["file_path"].$ret['file_name'];
            $config['new_image']		= $ret["file_path"].$ret['file_name'];
            $config['thumb_marker']	= '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            //copy($ret['full_path'],str_replace('/articles/','/original/',$ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
            $articles_watermark = $this->options->getOption('articles_watermark');
            if($articles_watermark === false) $articles_watermark = 1;
            if($articles_watermark)
            {
                // Получаем файл водяного знака
                $watermark_file = $this->options->getOption('watermark_file');
                if($watermark_file === false) $watermark_file = 'img/logo.png';
                //
                // Получаем вертикальную позицию водяного знака
                $watermark_vertical_alignment = $this->options->getOption('watermark_vertical_alignment');
                if($watermark_vertical_alignment === false) $watermark_vertical_alignment = 'bottom';
                // Получаем горизонтальную водяного знака
                $watermark_horizontal_alignment = $this->options->getOption('watermark_horizontal_alignment');
                if($watermark_horizontal_alignment === false) $watermark_horizontal_alignment = 'center';
                //
                // Получаем прозрачность водяного знака
                $watermark_opacity = $this->options->getOption('watermark_opacity');
                if($watermark_opacity === false) $watermark_opacity = '20';
                //

                $config['source_image'] 	= $ret["file_path"].$ret['file_name'];
                $config['create_thumb'] 	= FALSE;
                $config['wm_type'] 		= 'overlay';
                $config['wm_opacity']	= $watermark_opacity;
                $config['wm_overlay_path'] 	= $watermark_file;
                $config['wm_hor_alignment'] 	= $watermark_horizontal_alignment;
                $config['wm_vrt_alignment'] 	= $watermark_vertical_alignment;
                $this->image_lib->initialize($config);
                $this->image_lib->watermark();
            }



            return $ret;
        }
    }

    public function upload_review_foto($type)
    {
        $random_name = true;

        $path = 'temp';
        //$upload_dir = 'upload/';
        $upload_dir = 'upload/'.$type.'/'; //Создадим папку для хранения изображений
        $allowed_ext = array('jpg','jpeg','png','gif'); //форматы для загрузки

        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$upload_dir))
            mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$upload_dir,0777);
//
        $upload_dir .= date("Y-m-d").'/';
//
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$upload_dir))
            mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$upload_dir,0777);

        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
            exit_status('Ошибка при отправке запроса на сервер!');
        }


        if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){

            $pic = $_FILES['pic'];

            if(!in_array(get_extension($pic['name']),$allowed_ext)){
                exit_status('Разрешена загрузка следующих форматов: '.implode(',',$allowed_ext));
            }


//Загружаем файл во на сервер в нашу папку и посылаем команду о том, что все ОК и файл загружен
            if(move_uploaded_file($pic['tmp_name'], $upload_dir.$pic['name'])){

                $image = '/'.$upload_dir.$pic['name'];

                //if($type == 'articles') {
                $this->load->Model('Model_images','images');
                $article_id = 0;                
                $show_in_bottom = 0;
                if(isset($_POST['review'])) $show_in_bottom = $_POST['show_in_bottom'];
                $product_id = 0;
                if(isset($_POST['product_id'])) $product_id = $_POST['product_id'];
                $num = 0;

                $dbins = array(
                    'image'			=> $image,
                    'article_id'	=> $article_id,
                    'product_id'	=> $product_id,
                    'show_in_bottom'	=> $show_in_bottom,
                    'active'			=> 1,
                    'num'				=> $num,
                    'date'			=> date("Y-m-d H:i"),
                    'date_unix'		=> time(),
                    'login'			=> userdata('login')
                );
                $this->db->insert('images', $dbins);
                exit_status('Файл Был успешно загружен!');
                //} else exit_status('Не верный тип, куда кидать!');
            } else exit_status('Ошибка сохранения файла на сервере!');

        }

        exit_status('Во время загрузки произошли ошибки');
    }

    public function add_review()
    {

        //vd($_POST);
        if ($this->input->post('comment') !== false && userdata('login') !== false) {
            $this->load->model('Model_users', 'users');
            $user = $this->users->getUserByLogin(userdata('login'));
            if ($user) {
                $rate = $this->input->post('rate');
                $shop_id = 0;
                $article_id = 0;
                if ($this->input->post('shop_id') != false) $shop_id = $this->input->post('shop_id');
                if ($this->input->post('article_id') != false) $article_id = $this->input->post('article_id');
                $images = '';
                $images = array();
                $filesCount = 1;
                while (isset($_FILES['files']['userfile'.$filesCount])) {
                    $image = $this->upload_foto('userfile'.$filesCount);
                    if($image)
                        array_push($images, $image);

                    $filesCount++;
                }

                if($images) {
                    $images = json_encode($images);
                    alert($images);
                }

                if ($rate < 1) $rate = 5;
                $dbins = array(
                    'comment' => $this->input->post('comment'),
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
                    'images' => $images
                );

                $this->db->insert('comments', $dbins);

                echo "Большое спасибо за Ваш отзыв! Он появится на сайте после проверки.";
            } else echo 'Ошибка пользователя';
        } else err404();
    }

    public function setka($id)
    {
        $data['id'] = $id;
        $this->load->view('ajax/setka.ajax.php', $data);
    }

    function admin_save_price()
    {
        if (isset($_POST['price']) && isset($_POST['shop_id']) && isAdminLoginHidden() == true) {
            $this->load->helper('login_helper');

            $dbins = array(
                'price' => $_POST['price']
            );
            $this->db->where('id', $_POST['shop_id']);
            $this->db->limit(1);
            $this->db->update('shop', $dbins);

            return $_POST['price'];
        }

    }

    function reloadMyCartTable()
    {
        echo "asd";
    }

    function get_fast_order()
    {
        if (count($_POST) == 0) $_POST = $_GET;
        $id = $this->input->post('shop_id');
        if ($id) {
            $a = $this->shop->getArticleById($id);
            if ($a) {
                $html = getFastOrderHtml($a);
                //showFastOrder($a);
                echo $html;
            } else echo "Товар не найден!";
        }
    }

    function set_shop_opt_from()
    {
        if (isset($_POST['country_id'])) {
            $country = $_POST['country_id'];
            $opt_from = getOption('shop_opt_from');
            if ($country == 1) unset_userdata('opt_from');
            else {
                $opt_from = getOption('shop_opt_from_other');
                userdata('opt_from', $opt_from);
            }
            echo $opt_from;
        }
    }

    function autocomplete()
    {
        $this->load->model('Model_shop', 'shop');
        $this->load->helper('thumbs_helper');
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

        $projects = $this->shop->Search($keyword, 20, 0, 1);

        $output = '[';
        // результат формируется циклом
        if ($projects) {
            $count = count($projects);
            for ($i = 0; $i < $count; $i++) {
                $p = $projects[$i];

                $discount = false;


                // $price_class_adding = '';
                // if($discount) $price_class_adding = ' old-price';

                // $price = '<p class = "itm-price'.$price_class_adding.'">';
                //          $price .= $p['price'].' $';
                //          $price .=  ' / ';
                //          $currensy_grn = getOption('usd_to_uah');
                //          $price .= ($p['price'] * $currensy_grn).' грн';

                //          $price .=  ' / ';

                //          $currensy_rub = getOption('usd_to_rur');
                //          $price .=  ($p['price'] * $currensy_rub).' р.';
                //          $price .=  '</p>';
                $p['new_price'] = getNewPrice($p['price'], $p['discount']);
                $price = round($p['new_price'],2) . '$';
                $price .= ' / ';
                $currensy_grn = getOption('usd_to_uah');
                $price .= round($p['new_price'] * $currensy_grn,2) . ' грн';
                $price .= ' / ';
                $currensy_rub = getOption('usd_to_rur');
                $price .= round($p['new_price'] * $currensy_rub,2) . ' р.';

                $old_price = "";
                if (isDiscount($p)) {
                    $discount = " autocomplete-discount";

                    $p['price'] = round($p['price'], 2);
                    $price2 = round($p['price'],2) . '$';
                    $price2 .= ' / ';
                    $currensy_grn = getOption('usd_to_uah');
                    $price2 .= round($p['price'] * $currensy_grn,2) . ' грн';
                    $price2 .= ' / ';
                    $currensy_rub = getOption('usd_to_rur');
                    $price2 .= round($p['price'] * $currensy_rub,2) . ' р.';

                    $old_price = $price2;
                }

                $url = getFullUrl($p);

                $notinw = 0;
                if ($p['warehouse_sum'] == 0) $notinw = 1;

                $image = CreateThumb2(60, 90, $p['image'], 'autocomplete');

//                    vd($p);
                if ($i == 0) {
                    $output .= '{ "id": "' . $p['id'] . '", "image": "' . $image . '", "label": "' . $p['name'] . ' (' . $p['color'] . ')", "value": "' . $p['name'] . ' (' . $p['color'] . ')", "url": "' . $url . '", "price": "' . $price . '", "old_price": "' . $old_price . '", "discount": "' . $discount . '", "notinw": "' . $notinw . '"}';
                } else {
                    $output .= ',{ "id": "' . $p['id'] . '", "image": "' . $image . '", "label": "' . $p['name'] . ' (' . $p['color'] . ')", "value": "' . $p['name'] . ' (' . $p['color'] . ')", "url": "' . $url . '", "price": "' . $price . '", "old_price": "' . $old_price . '", "discount": "' . $discount . '", "notinw": "' . $notinw . '"}';
                }
            }
        }
        // закрывающий тег json формата
        $output .= ']';
        // возвращаем данные
        echo $output;
    }

    function country()
    {
        $this->load->helper('modules_helper');
        if (isset($_POST['country_id'])) {
            $country_id = $_POST['country_id'];
            $opt_from = getOption('shop_opt_from');
            if ($country_id == 1) unset_userdata('opt_from');
            else {
                $opt_from = getOption('shop_opt_from_other');
                set_userdata('opt_from', $opt_from);
            }
            echo showDelivery($_POST['country_id']);
        }
    }

    function adress()
    {
        if (isset($_POST['delivery'])) {

            $delivery = $_POST['delivery'];
            echo $delivery;
            $this->load->library('Model_users', 'users');
            $user = $this->users->getUserByLogin(userdata('login'));

            $this->load->helper('modules_helper');

            $show = array(
                'zip' => false,
                'passport' => false,
                'adress' => true,
                'np' => false
            );


            if ($delivery == 'novaposhta') {
                $show['np'] = true;
                $show['adress'] = false;
            } elseif ($delivery == 'russianpost') {
                $show['zip'] = true;
            } elseif ($delivery == 'avtotreyding') {
                $show['passport'] = true;
            } elseif ($delivery == 'kse') {
                $show['zip'] = true;
            } elseif ($delivery == 'ems') {
                $show['zip'] = true;
            } elseif ($delivery == 'deloviyelinii') {
                $show['passport'] = true;
            } elseif ($delivery == 'baykalservice') {
                $show['passport'] = true;
            }


            showAdress($show, $user);
        } else echo "no delivery";
    }

    function umnog($a, $b)
    {

        echo $a * $b . '$<br/>';
        $currensy_grn = $this->model_options->getOption('usd_to_uah');
        $product_full_price2 = $a * $currensy_grn * $b;
        echo $product_full_price2 . ' грн<br/>';
        $currensy_rub = $this->model_options->getOption('usd_to_rur');
        $product_full_price3 = $a * $currensy_rub * $b;
        echo $product_full_price3 . ' р.';

    }

    function cart_save()
    {
        $this->load->model('Model_shop', 'shop');
        $summa = 0;
        $my_cart = $this->session->userdata('my_cart');
        if ($my_cart) {
            $count = count($my_cart);
            for ($i = 0; $i < $count; $i++) {
                if (isset($_POST['kolvo_' . $i]) && $_POST['kolvo_' . $i] != $my_cart[$i]['kolvo']) $my_cart[$i]['kolvo'] = $_POST['kolvo_' . $i];


                $product = $this->shop->getProductById($my_cart[$i]['shop_id']);
                $res = $product['price'] * $my_cart[$i]['kolvo'];
                $summa = $summa + $res;

            }
        }
        $this->session->set_userdata('my_cart', $my_cart);
        echo $summa . '$ / ';
        $currensy_grn = $this->model_options->getOption('usd_to_uah');
        $product_full_price2 = $summa * $currensy_grn;
        echo $product_full_price2 . ' грн / ';
        $currensy_rub = $this->model_options->getOption('usd_to_rur');
        $product_full_price3 = $summa * $currensy_rub;
        echo $product_full_price3 . ' р.';
    }

    function to_cart()
    {
        set_currency();

        //unset_userdata('my_cart');
        $main_currency = getMainCurrency();
        $currency = $this->session->userdata('currency');

        if (!$currency)
            $currency = $main_currency;

        if (count($_POST) == 0) $_POST = $_GET;
        //vd($_POST);
        if (isset($_POST['shop_id'])) {
            //var_dump($_POST);die();
            if(isset($_POST['count'])) $_POST['kolvo'] = post('count');
            if(isset($_POST['size'])) $_POST['razmer'] = post('size');
            if (!isset($_POST['kolvo'])) $_POST['kolvo'] = 1;
            $my_cart = array();
            if ($this->session->userdata('my_cart') !== false) $my_cart = $this->session->userdata('my_cart');

            $shop = $this->shop->getArticleById($_POST['shop_id']);
            //vd($shop['razmer']);
            $razmer = explode('*', $shop['razmer']);
            //$razmer = json_decode($shop['warehouse']);

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

                $razmer = explode('*', $shop['razmer']);
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

            $this->session->set_userdata('my_cart', $my_cart);

            echo shop_count();

            //$my_cart = $this->session->userdata('my_cart');
        }
    }

    function cart_actions()
    {
        $my_cart = $this->session->userdata('my_cart');
        $cart_count = 0;
        if ($my_cart) $cart_count = count($my_cart);

        if (isset($_POST['cart_count'])) {
            $my_cart = array();
            if ($this->session->userdata('my_cart') !== false) $my_cart = $this->session->userdata('my_cart');
            if (!$my_cart) echo 0;
            else echo count($my_cart);
        }
        if (isset($_POST['del_from_cart'])) {
            $newarr = array();
            for ($i = 0; $i < $cart_count; $i++) {
                if ($my_cart[$i]['shop_id'] != $_POST['del_from_cart']) {
                    array_push($newarr, $my_cart[$i]);
                }
            }
            $my_cart = $newarr;

            $this->session->set_userdata('my_cart', $my_cart);

            echo count($my_cart);
        }
    }

    public function getNextReviews()
    {
        $this->load->model('Model_comments', 'comments');
        $this->load->model('Model_users', 'users');
        $this->load->helper('modules_helper');
        $per_page = $_POST['perPage'];
        $from = $_POST['startFrom'];
        $reviews = $this->comments->getComments($per_page, $from, 'id', 'DESC', 1);
        if ($reviews) {
            foreach ($reviews as $review) {
                if (isset($_POST['getajax'])) {
                    echo getReview($review, true);
                    unset($_POST['getajax']);
                } else getReview($review);

            }
        }
    }

    public function getNextRows()
    {
        if (!isset($_POST['category_id'])) $_POST['category_id'] = 21;
        $cat = false;
        if ($_POST['category_id'] == -2)
            $cat['type'] = 'search';
        else
            $cat = $this->model_categories->getCategoryById($_POST['category_id']);


        if ($cat['type'] == 'articles') {
            $news = $this->articles->getArticlesByCategory($_POST['category_id'], 4, $_POST['startFrom'], 1);

            if ($news) {
                $count = count($news);
                $articles = array();
                for ($i = 0; $i < $count; $i++) {
                    $articles[$i] = getArticleHtml($news[$i], $cat);
                }

                $json = json_encode($articles);
                echo $json;
            }
        } elseif ($cat['type'] == 'search') {
            $search = userdata('search');
            if ($search) {
                $shop = $this->shop->Search($search, 18, $_POST['startFrom']);
                //$shop = $this->shop->getArticlesByCategory($_POST['category_id'], 18, $_POST['startFrom'], 1);
                if ($shop) {
                    $count = count($shop);
                    $articles = array();
                    for ($i = 0; $i < $count; $i++) {
                        if ($i == 0)
                            $articles[$i] = getProductHtml($shop[$i], false, true);
                        else
                            $articles[$i] = getProductHtml($shop[$i]);
                    }

                    echo json_encode($articles);
                }
            }
        } else {
            $razmer = false;
            if(userdata('filter_razmer') !== false) $razmer = userdata('filter_razmer');
            $shop = $this->shop->getArticlesByCategory($_POST['category_id'], 18, $_POST['startFrom'], 1, 'DESC', 'num', $razmer);
            if ($shop) {
                $count = count($shop);
                $articles = array();
                for ($i = 0; $i < $count; $i++) {
                    if (isset($_POST['getajax']) && $i == 0)
                        $articles[$i] = getProductHtml($shop[$i], false, true);
                    else
                        $articles[$i] = getProductHtml($shop[$i]);
                }

                echo json_encode($articles);
            }
        }
    }

    // Уведомление о наличии
    public function say_me_available()
    {
        $user = getCurrentUser();
        if (isset($_GET['shop_id'])) $_POST['shop_id'] = $_GET['shop_id'];
        if (isset($_GET['razmer'])) $_POST['razmer'] = $_GET['razmer'];
        if (isset($_POST['shop_id']) && isset($_POST['razmer'])) {
            $shop_id = $this->input->post('shop_id');
            $dbins = array(
                'shop_id' => $shop_id,
                'razmer' => $this->input->post('razmer'),
                'date' => date("Y-m-d"),
                'time' => date("H:i"),
                'unix' => time(),
                'ip' => GetRealIp(),
                'email' => $this->input->post('email'),
                'url' => $this->input->post('url')
            );
            if ($user) {
                $dbins['user_id'] = $user['id'];
            }

            $this->db->insert('say_me_available', $dbins);
            $email_say_me_available = getOption('email_say_me_available');
            if ($email_say_me_available) {

                $shop = $this->shop->getArticleById($shop_id);
                $message = 'Был произведён запрос на уведомление о появлении:<br />
						' . $shop['name'] . ' (' . $shop['color'] . ')<br />
						Размер: ' . $this->input->post('razmer') . '<br />
						e-mail: ' . $this->input->post('email') . '<br />
						Дата: ' . date("Y-m-d H:i");

                if ($user) {
                    $message .= '<br />Клиент: ' . $user['name'] . ': <a href="https://' . $_SERVER['SERVER_NAME'] . '/admin/users/edit/' . $user['id'] . '/">https://' . $_SERVER['SERVER_NAME'] . '/admin/users/edit/' . $user['id'] . '/</a>';
                }
                $this->load->helper('mail_helper');
                $to = getOption('admin_email');
                mail_send($to, 'Запрос на уведомление', $message);
            }
        }
    }
}