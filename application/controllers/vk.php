<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Vk extends CI_Controller
{
    var $vkmarket = false;
    var $vkalbum = false;
    var $group_id = '124660621';
    var $user = false;
    var $user_info = false;


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_categories', 'categories');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_images', 'images');
        $this->load->model('Model_vk', 'vk');

        $this->load->library('Vkmarket');
        //$this->load->library('Vkalbum');

        isLogin();

        $this->createVKMarket();
        //$this->createVKAlbum();

    }


    private function createVKMarket()
    {
        $params = array(
            'group_id' => $this->group_id
        );
        $this->user = userdata('login');
        $this->user_info = $this->users->getUserByLogin($this->user);
        $this->vkmarket = new Vkmarket($params);
    }
    private function createVKAlbum()
    {
        $params = array(
            'group_id' => $this->group_id
        );
        $this->user = userdata('login');
        $this->user_info = $this->users->getUserByLogin($this->user);
        $this->vkmarket = new Vkalbum($params);
    }

    public function exportToMarket($from = 0, $count = 20)
    {
        if (isset($_GET['albums'])) {
            $this->addProductsToAlbums();
        } elseif(isset($_GET['edit'])){
            $this->editProducts();
        } else {
            $products = $this->shop->getArticles($count, $from, 'ASC', 1);
            //vdd($products);
            foreach ($products as $product) {
                $this->exportProduct($product);
            }
        }
        //die("asd");
    }

    public function createAlbum($name){
        $album_id = $this->vkalbum->add_vk_album($name);
        if($album_id){
//            $dbins = array(
//                'vk_album_id'   => $result->response->aid,
//                'category_id'   =>
//            );
            $res = $this->vkalbum->add_vk_image('/upload/images/045bbf066973cc3a9c19c87ab986b265.jpg',$album_id);
            vd($res);
        }

    }

    public function exportCategory($categopry_id){
        $cat = $this->model_categories->getCategoryById($categopry_id);
        if($cat){
            $vk_album_id = $this->vkalbum->add_vk_album($cat['name']);
            if($vk_album_id){
                $shop = $this->shop->getArticlesByCategory($categopry_id, -1,-1,1);
                if($shop){
                    $i = 0;
                    //vdd($shop);
                    foreach ($shop as $item){
                        echo $i.'. exporting photo '.$item['name'].' ('.$item['color'].')...';
                        if($item['image'] != ''){
                            $description = "Артикул: ".$item['articul']."
";
                            if($item['tkan'] != '') $description .= 'Ткань: '.$item['tkan'].'
';
                            if($item['sostav'] != '') $description .= 'Состав: '.$item['sostav'].'
';
                            $sizes = str_replace('*',', ',$item['razmer']);
                            $description .= 'Размеры: '.$sizes.'

';
                            if($item['short_content'] != '') $description .= strip_tags($item['short_content']);
                            elseif($item['content'] != '') $description .= strip_tags($item['content']);
                            $res = $this->vkalbum->add_vk_image($item['image'], $vk_album_id, $description);
                            if($res) echo 'done<br />';
                        }
                    }
                }
            }
        }
    }

    public function addProductsToAlbums()
    {
        $products = $this->shop->getArticles();
        $user_id = getUserIdBylogin(userdata('login'));
        foreach ($products as $product) {
            $carr = explode('*',$product['category_id']);
            if(is_array($carr)){
                $vkId = $this->vk->getVkProductId($product['id'], '58777985', $user_id);
                if ($vkId) {
                    foreach ($carr as $item){
                        $cat = $this->model_categories->getCategoryByid($item);
                        if($cat){
                            $album_id = $this->vk->getVkAlbumIdByCategoryId($cat['id'], '124660621', $user_id);
                            if($album_id){
                                $ret = $this->vkmarket->add_product_to_album($vkId, $album_id);
                                if($ret) echo 'Товар '.$product['name'].' ('.$product['color'].') успешно помещён в альбом '.$cat['name'].'<br />';
                            }
                        }
                    }
                }
            }
        }
    }

    public function editProducts(){
        $start = -1;
        $count = -1;
        if(isset($_GET['start'])) $start = $_GET['start'];
        if(isset($_GET['count'])) $count = $_GET['count'];
        $products = $this->shop->getArticles($count,$start,"ASC");
        $user_id = getUserIdBylogin(userdata('login'));
        $vk_nadbavka = getOption('vk_nadbavka'); // получаем розничную надбавку для ВК
        if(!$vk_nadbavka) $vk_nadbavka = 0;

        foreach ($products as $product) {
            //vdd($product);
            $vkId = $this->vk->getVkProductId($product['id'], '58777985', $user_id);
            echo 'vk-id: '.$vkId.'<br />';
            if ($vkId) {
                $p = $this->vkmarket->get_vk_product($vkId);
                if(isset($p->response[1])) {
                    $vkProduct = $p->response[1];
                    //vdd($vkProduct);
                    $data['name'] = $product['name'] . ' (' . $product['color'] . ')';
                    $content = 'Артикул: ' . $product['articul'];
                    if ($product['tkan'] != '') $content .= '
    Ткань: ' . $product['tkan'];
                    if ($product['sostav'] != '') $content .= '
    Состав: ' . $product['sostav'];
                    $razmeri = str_replace('*', ', ', $product['razmer']);
                    $content .= '
    Размеры: ' . $razmeri;
                    if ($product['content'] != '') $content .= '
    ' . $product['content'];

                    $data['description'] = $content;

                    $data['category_id'] = 1;

                    if ($product['discount'] != 0)
                        $data['price'] = getNewPrice($product['price'], $product['discount']) + $vk_nadbavka;
                    else
                        $data['price'] = $product['price'] + $vk_nadbavka;

                    //$data['price'] = 1;

                    if(isset($vkProduct->photos[0]->pid)){
                        $data['main_photo_id'] = $vkProduct->photos[0]->pid;
                    }elseif ($product['image'] != '') {
                        $pr_img = $product['image'];
                        //vd($pr_img);
                        $photo = $this->vkmarket->add_vk_image($pr_img, 'product');
                        // 0 - это номер изображения, так их можна загрузить 5 штук для товара (бред какой-то!)
                        if (!isset($photo->response[0]->pid)):
                            return $photo;
                        endif;
                        //но нам необходим только его id
                        $data['main_photo_id'] = $photo->response[0]->pid;
                    }
                    $data['photo_ids'] = '';
                    // Проверяем доп. фото
                    $i = 1;
                    while(isset($vkProduct->photos[$i]->pid)){
                        if($data['photo_ids'] != '') $data['photo_ids'] .= ',';
                        $data['photo_ids'] .= $vkProduct->photos[$i]->pid;
                        $i++;
                    }

                    $ret = $this->vkmarket->edit_vk_product($vkId, $data);
                    //vd($ret);
                    if ($ret) echo 'Товар ' . $data['name'] . ' успешно обновлён!<br />';
                    //die();
                }
            }
        }
    }

    private function exportProduct($product)
    {
        if (!$this->user) $this->users->getUserByLogin($this->user);


        $exists = $this->vk->isProductExists($product['id'], $this->user, $this->group_id);
        if (!$exists) {
            $cat = $this->categories->getCategoryById($product['category_id']);
            $vk_category_id = $this->model_categories->getVkCategoryId($cat['id'], $this->group_id);

            // подгружаем доп. фото товара
            $images = $this->images->getByShopId($product['id'], 1, 1);
            $vk_product_id = $this->vkmarket->add_vk_product($product, $vk_category_id, $this->user, $images);
            //$vk_product_id = false;
            //vdd($vk_product_id);
            if ($vk_product_id && $vk_product_id != '') {
                $album_id = $vk_category_id;
                // vd($album_id);
                echo '<hr>';
                $dbins = array(
                    'shop_id' => $product['id'],
                    'vk_album_id' => $album_id,
                    'vk_product_id' => $vk_product_id,
                    'name' => $cat['name'],
                    'user_id' => $this->user_info['id'],
                    'user_login' => $this->user,
                    'group_id' => $this->group_id
                );
                // добавляем запись в базу о созданном альбоме
                $this->db->insert('vkmarket_product_to_vk', $dbins);
                echo '<p>Товар добавлен: <b>' . $product['name'] . '</b></p>';
                //die();
            } else {
                $this->vkmarket->authorize();
                die();
            }
            // vdd("as");
        } else {
            // Редактирование уже добавленного товара
            echo '<p>Товар <b>' . $product['name'] . '</b> уже есть</p>';
        }
    }

    public function auth()
    {
        $group_id = $this->group_id;

        $params = array(
            'group_id' => $group_id
        );
        $this->user = userdata('login');
        $this->user_info = $this->users->getUserByLogin($this->user);
        //$this->vkmarket = new Vkmarket($params);

        $this->vkmarket->authorize();
    }

    public function exportCategories()
    {
        $categories = $this->categories->getCategories(1, 'shop');
        foreach ($categories as $category) {
            $exists = $this->vk->isCategoryExists($category['id'], $this->user, $this->group_id);
            if (!$exists) {
                if ($category['image'] == '')
                    $album_id = $this->vkmarket->add_vk_album($category['name']);
                else
                    $album_id = $this->vkmarket->add_vk_album($category['name'], $category['image']);
                //vdd($album_id);
                if (!isset($album_id->error)) {
                    //vdd($album_id);
                    $dbins = array(
                        'category_id' => $category['id'],
                        'vk_album_id' => $album_id,
                        'name' => $category['name'],
                        'user_id' => $this->user_info['id'],
                        'user_login' => $this->user['login'],
                        'group_id' => $this->group_id
                    );
                    // добавляем запись в базу о созданном альбоме
                    $this->db->insert('vkmarket_category_to_vk', $dbins);
                    echo '<p>Добавлена категория <b>' . $category['name'] . '</b></p>';
                } else {
                    $this->vkmarket->authorize();
                    break;
                }
                //vdd("as");
            } else echo '<p>Ккатегория <b>' . $category['name'] . '</b> уже есть</p>';
        }
    }


    public function index()
    {
        if (isset($_GET['test'])) {

            $album_id = $test->add_vk_album("asdqwezxc", "/upload/images/d5605b504983ed4f66d5071091fe94a2.jpg");
            vd($album_id);
            if ($album_id)
                $this->db->insert('sm_shop_category_to_vk', array('category_id' => $category['id'], 'vk_album_id' => $album_id, 'name' => $category['name']));
            die();
        }
    }
}