<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        $isNeedLogin = true;
        if(!isset($_POST['torgsoft']) && isset($_GET['torgsoft'])) $_POST['torgsoft'] = $_GET['torgsoft'];
        if (isset($_POST['torgsoft']) && $_POST['torgsoft'] == 'import') $isNeedLogin = false;



        if ($isNeedLogin) isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_shop', 'shop');
        $this->load->helper('tasks_helper');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_vk', 'vk');
    }


    function db_backup()
    {
        // Загружает класс DB utility
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/backup/' . date("Y-m-d") . 'mybackup.sql')) {
            $this->load->dbutil();

            $prefs = array(
                'tables' => array('shop'),  // Array of tables to backup.
                'ignore' => array(),           // List of tables to omit from the backup
                'format' => 'txt',             // gzip, zip, txt
                'filename' => 'backup/' . date("Y-m-d") . 'mybackup.sql',    // File name — NEEDED ONLY WITH ZIP FILES
                'add_drop' => TRUE,              // Whether to add DROP TABLE statements to backup file
                'add_insert' => TRUE,              // Whether to add INSERT data to backup file
                'newline' => "\n"               // Newline character used in backup file
            );

            // Получает бэкап и пишет его в переменную
            $backup =& $this->dbutil->backup($prefs);

            // Load the file helper and write the file to your server
            $this->load->helper('file');
            write_file('backup/' . date("Y-m-d") . 'mybackup.sql', $backup);
        }
// Загружает помощник download и отправляет файл на ваш десктоп
        //$this->load->helper('download');
        //force_download('mybackup.gz', $backup);
    }

    public function index()
    {
        $this->db_backup();

        $msg = "";

        $actionUpdate = true;
        $actionLost = true;
        $actionNew = true;
        $actionNothingDoing = false;
        $invisible = false;
        $torgsoftAction = false;

        // Проверяем, откуда поступило обращение: из браузера или из ТоргСофта
        if(post('torgsoft') == 'import'){
            // Если из ТоргСофта, то делаем только обновление синхронизированных товаров и не выводим таблицы на экран
            $torgsoftAction = true;
            $actionNew = false;
            $actionLost = false;
            $invisible = true;
        }
        if(isset($_GET['nothing_doing']) && $_GET['nothing_doing'] == true){
            $actionNothingDoing = true;
        } elseif(isset($_GET['update_only']) && $_GET['update_only'] == true){
            $actionLost = false;
            $actionNew = false;
            $actionUpdate = true;
        } elseif(isset($_GET['lost_only']) && $_GET['lost_only'] == true){
            $actionUpdate = false;
            $actionNew = false;
            $actionLost = true;
        } elseif(isset($_GET['new_only']) && $_GET['new_only'] == true){
            $actionUpdate = false;
            $actionLost = false;
            $actionNew = true;
        }


        if(isset($_GET['invisible']) && $_GET['invisible'] == true)
            $invisible = true;

        if(!$actionNothingDoing) {
            $import_filemtime = getOption('import_filemtime');
            $filemtime = filemtime($_SERVER['DOCUMENT_ROOT'].'/import/import.csv');
            if($filemtime == $import_filemtime && !isset($_GET['strong_parse'])) {
                echo '<p>С последнего импорта csv файл не изменился!</p>';
                echo '<p><a href="'.request_uri(false,false,"strong_parse=true").'">Запустить принудительно</a></p>';
                die();
            }
            else setOption('import_filemtime', $filemtime);

            $this->load->library('csvreader');
            $articles = $this->csvreader->parse_file('import/import.csv');//path to csv file

            $goods = array();
            $new = array();

            $i = 0;
            foreach ($articles as $p) {
                $i++;
                $p['Material'] = iconv("CP1251", "UTF-8", $p['Material']);
                $p['Description'] = iconv("CP1251", "UTF-8", $p['Description']);
                $p['Color'] = iconv("CP1251", "UTF-8", $p['Color']);
                $p['GoodTypeFull'] = iconv("CP1251", "UTF-8", $p['GoodTypeFull']);
                $p['GoodName'] = iconv("CP1251", "UTF-8", $p['GoodName']);
                $p['Barcode'] = iconv("CP1251", "UTF-8", $p['Barcode']);
                //$art = $this->shop->searchByNameArticulColor($p['Description'], $p['Articul'], $p['Color'], -1);
                $art = $this->shop->searchByBaseId($p['GoodID']);
//                if($art['name'] == 'Эмилия' && $art['color'] == 'black') {
//                    vd($art);
//                    echo '<hr>';
//                }

                //if (!$art) $this->shop->searchByNameArticulColor($p['Description'], $p['Articul'], $p['Color'], -1);

                if (isset($art['name']) && $art['name'] == 'Ромашка') $art = false;

                if (($actionUpdate) && $art) {
                    /// Вычисляем, проверяем и сохраняем скидку
                    $art['price'] = $p['RetailPrice'];
                    $art['barcode'] = $p['Barcode'];
                    $art['price'] = str_replace(',', '.', $art['price']);

                    $art['new_price'] = $p['RetailPriceWithDiscount'];
                    $art['new_price'] = str_replace(',', '.', $art['new_price']);
                    $discount = 0;
                    if ($p['RetailPriceWithDiscount'] != $art['price']) {
                        $discount = getDiscount($art['price'], $art['new_price']);
                        if (strpos($art['category_id'], '19') === false) {
                            $art['category_id'] .= '*19';

                        }
                    }

                    // Проверяем, не было ли скидки ранее, либо-же, она была меньше...
                    $removedFromMailSale = array();
                    $import_removed_from_mail_sale_ids = getOption('import_removed_from_mail_sale_ids');
                    if($import_removed_from_mail_sale_ids) {
                        $import_removed_from_mail_sale_ids = explode('|', $import_removed_from_mail_sale_ids);
                    }
                    if(is_array($import_removed_from_mail_sale_ids))
                        $removedFromMailSale = $import_removed_from_mail_sale_ids;

                    if ($art['name'] != 'Ромашка') {
                        if(!in_array($art['id'], $removedFromMailSale)) {
                            if ($discount > $art['discount']) {
                                $art['discount_date'] = date("Y-m-d H:i");

                                // Проверяем, не было ли уже задачи на добавление товара в рассылку
                                $shopMailerSaleCount = $this->shop->getMailerCountByArticul('sale', $art['articul']);
                                if ($shopMailerSaleCount == 0) {    // если ни один цвет товара не добавлен в рассылку
                                    // выбираем тот цвет, остаток у которого больше всего и добавляем в рассылку
                                    $biggest_id = $this->shop->getShopIdByArticulWithMaxWarehouse($art['articul']);
                                    if ($biggest_id) {

                                        if(!in_array($art['id'], $removedFromMailSale))
                                            $this->db->where('id', $biggest_id)->limit(1)->update('shop', array('mailer_sale' => 1));
                                    }
                                }
                            }
                        }
                    }

                    $art['discount'] = $discount;

                    if ($discount == 0) {
                        $art['category_id'] = delFromCategory($art['category_id'], 19);
                        $art['sale'] = 0;
                    } else
                        $art['sale'] = 1;

                    $art['price'] = str_replace(',', '.', $art['price']);
                    $art['price'] = $art['price'];
                    /// ***

                    if ($art['tkan'] != $p['Material']) $art['tkan'] == $p['Material'];
                    /// Размеры

                    $razmer = explode("*", $art['razmer']);
                    $razmer_filter = explode('|', $art['razmer_filter']);

                    /**************************************************************/
                    // if(isset($razmer[0]) && $razmer[0] > 40 && $p['TheSize'] < 5) $razmer = array($p['TheSize']);
                    // Проверка наличия размера
//                if($p['WarehouseQuantity'] == 0 && in_array($p['TheSize'], $razmer)) // Если размер закончился, убираем с сайта
//                {
//                    $art['razmer'] = "";
//                    $rc = count($razmer);
//                    for($ri = 0; $ri< $rc; $ri++)
//                    {
//                        if($razmer[$ri] != $p['TheSize'])
//                            $art['razmer'] .= $razmer[$ri];
//                        if(($ri+1) < $rc) $art['razmer'] .= '*';
//                    }
//                }
//                elseif($p['WarehouseQuantity'] > 0 && !in_array($p['TheSize'], $razmer)) // если размер появился, добавляем на сайт
//                {
//                    if($art['razmer'] != '') $art['razmer'] .= '*';
//                    $art['razmer'] .= $p['TheSize'];
//                }

                    /**************************************************************/

                    if (!in_array($p['TheSize'], $razmer)) {
                        if ($art['razmer'] != '') $art['razmer'] .= '*';
                        $art['razmer'] .= $p['TheSize'];
                    }
                    $filterSize = '*'.$p['TheSize'].'*';
                    if (!in_array($p['TheSize'], $razmer_filter)) {
                        if ($art['razmer_filter'] != '') $art['razmer_filter'] .= '|';
                        $art['razmer_filter'] .= $filterSize;
                    }
                    /**************************************************************/
                    //////
                    $warehouse_old = $warehouse = json_decode($art['warehouse'], true);
                    if (isset($warehouse[0])) unset($warehouse[0]);
                    //vdd($warehouse);
                    if (!is_array($warehouse)) $warehouse = array();
                    else // проверка на не существующие значения размеров
                    {
                        $keys = array_keys($warehouse);
                        $rk = count($warehouse);
                        for ($ri = 0; $ri < $rk; $ri++) {
                            if (!in_array($keys[$ri], $razmer)) {
                                unset($warehouse[$keys[$ri]]);
                            }

                        }
                    }
                    $warehouse[$p['TheSize']] = $p['WarehouseQuantity'];


                    // GoodIDs
                    $base_ids = json_decode($art['base_ids'], true);
                    if (!is_array($base_ids)) $base_ids = array();
                    if (!in_array($p['GoodID'], $base_ids))
                        array_push($base_ids, $p['GoodID']);
                    $art['base_ids'] = json_encode($base_ids);

                    // Sizes to GoodIDs
                    $sizes = json_decode($art['sizes_to_good_ids'], true);
                    if (!is_array($sizes)) $sizes = array();
                    if (!isset($sizes[$p['TheSize']]))
                        $sizes[$p['TheSize']] = $p['GoodID'];

                    $art['sizes_to_good_ids'] = json_encode($sizes);

                    $art['category_id'] = str_replace('********************************************************************************************', '*', $art['category_id']);
                    $art['category_id'] = str_replace('**', '*', $art['category_id']);
                    $art['category_id'] = str_replace('**', '*', $art['category_id']);

                    $art['razmer'] = str_replace('**', '*', $art['razmer']);
                    $art['razmer'] = str_replace('**', '*', $art['razmer']);
                    $price = $art['price'];
                    $art['price'] = round($price, 2);
                    //vdd($warehouse);
                    ksort($warehouse);
                    $art['warehouse'] = json_encode($warehouse);

                    // Сверяем новые и старые значения склада
                    // и если они разные, создаём задачу на
                    // обновление товара
                    if (is_array($warehouse)) {
                        foreach ($warehouse as $key => $value) {
                            if ($value == 0 && isset($warehouse_old[$key]) && $warehouse_old[$key] > 0) {     // если один из размеров закончился, создаём задачу на обновление в ВК
                                $msg .= 'Один из размеров товара ' . $art['name'] . ' (' . $art['color'] . ') закончился. Создаём задачу на обновление...<br />';
                                //vd($warehouse);echo '<br>';vd($warehouse_old);

                                $users = $this->users->getUsersByTypeId(12);    // получаем всех пользователей с типом ВК
                                if ($users) {
                                    foreach ($users as $user) {
                                        if ($user['vk_access_token'] != NULL) {  // проверяем, есть ли у пользователя ключ доступа к ВК API
                                            $vks = $this->vk->getVkMarketProducts($user['login'], $art['id']); // достаём id товаров в вк, которые необходимо обновить
                                            if ($vks) {
                                                foreach ($vks as $vk) {
                                                    $dbins = array(
                                                        'shop_id' => $art['id'],
                                                        'type' => 'vk',
                                                        'command' => 'refresh',
                                                        'completed' => 0,
                                                        'login' => $user['login'],
                                                        'user_id' => $user['id'],
                                                        'comment' => 'Один из размеров товара ' . $art['name'] . ' (' . $art['color'] . ') закончился. Создаём задачу на обновление.',
                                                        'vk_type' => 'vk_market',
                                                        'vk_id' => $vk['vk_product_id']
                                                    );
                                                    addTask($dbins);
                                                }
                                            }
                                            $vks = $this->vk->getVkAlbumProducts($user['login'], $art['id']); // достаём id фотографий в вк, которые необходимо обновить
                                            if ($vks) {
                                                foreach ($vks as $vk) {
                                                    $dbins = array(
                                                        'shop_id' => $art['id'],
                                                        'type' => 'vk',
                                                        'command' => 'refresh',
                                                        'completed' => 0,
                                                        'login' => $user['login'],
                                                        'user_id' => $user['id'],
                                                        'comment' => 'Один из размеров товара ' . $art['name'] . ' (' . $art['color'] . ') закончился. Создаём задачу на обновление.',
                                                        'vk_type' => 'vk_album',
                                                        'vk_id' => $vk['pid']
                                                    );
                                                    addTask($dbins);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //vdd($warehouse);
                    }

                    //vdd($art['warehouse']);
                    $this->db->where('id', $art['id']);
                    $this->db->limit(1);
                    $this->db->update('shop', $art);

                    $art['import'] = $p;
                    array_push($goods, $art);
                    // vdd($art['price']);

                } else {
                    if ($actionNew) {
                        $art = $this->shop->searchByNameArticulColorArray($p['Description'], $p['Articul'],false,true);

                        $p['finded'] = $art;
                        array_push($new, $p);
                    }
                }
            }

            // Удаляем из рассылки не нужные товары
            $removedFromMailSale = array();
            $import_removed_from_mail_sale_ids = getOption('import_removed_from_mail_sale_ids');
            if($import_removed_from_mail_sale_ids) {
                $import_removed_from_mail_sale_ids = explode('|', $import_removed_from_mail_sale_ids);
            }
            if(is_array($import_removed_from_mail_sale_ids)) {
                $removedFromMailSale = $import_removed_from_mail_sale_ids;
                foreach ($removedFromMailSale as $item){
                    $this->db->where('id', $item)->limit(1)->update('shop', array('mailer_sale' => 0));
                }
            }

            $data['articles'] = $goods;
            $data['new'] = $new;

//        $i = 0;
//        foreach($result as $p){
//            $i++;
//            $p['Description'] = iconv("CP1251","UTF-8",$p['Description']);
//            $p['Color'] = iconv("CP1251","UTF-8",$p['Color']);
//            $art = $this->shop->searchByNameArticulColor($p['Description'], $p['Articul'], $p['Color']);
//            if($art)
//            {
//                echo $i;
//                vdd($art);
//            }
//        }
            $razmer = $this->shop->getAllSizes();
            $rarr = array();
            foreach ($razmer as $r) {
                array_push($rarr, $r['name']);
            }

            $data['razmer'] = $rarr;


            // Пересчитываем товары по размерам
            $this->db->where('warehouse != ', 'NULL');
            $products = $this->db->get('shop')->result_array();
            if ($products) {
                foreach ($products as $p) {
                    $warehouse = json_decode($p['warehouse'], true);
                    if (is_array($warehouse)) {
                        $sum = array_sum($warehouse);
                        if ($sum != $p['warehouse_sum']) {
                            // если новая сумма больше, то проверяем задачу и создаём новую задачу на обновление
                            if ($sum > $p['warehouse_sum']) {
                                $msg .= 'Сумма количества товаров стала больше, чем была. Товар ' . $p['name'] . ' (' . $p['color'] . '). Добавляем задачу на обновление...<br />';

                                $this->load->model('Model_users', 'users');
                                $users = $this->users->getUsersByTypeId(12);    // получаем всех пользователей с типом ВК
                                if ($users) {
                                    foreach ($users as $user) {
                                        if ($user['vk_access_token'] != NULL) {  // проверяем, есть ли у пользователя ключ доступа к ВК API
                                            $dbins = array(
                                                'shop_id' => $p['id'],
                                                'type' => 'vk',
                                                'command' => 'refresh',
                                                'completed' => 0,
                                                'login' => $user['login'],
                                                'user_id' => $user['id'],
                                                'comment' => 'Сумма количества товаров стала больше, чем была. Товар ' . $p['name'] . ' (' . $p['color'] . '). Добавляем задачу на обновление.'
                                            );
                                            addTask($dbins);
                                        }
                                    }

                                }
                            }

                            $dbins = array('warehouse_sum' => $sum);
                            $this->db->where('id', $p['id']);
                            $this->db->limit(1);
                            $this->db->update('shop', $dbins);
                        }
                    }
                }
            }

            // Получаем товары с сайта, которые не нашлись в базе
            $lost = array();
            if ($actionLost) {
                $this->db->where('base_ids', NULL);
                $lost = $this->db->get('shop')->result_array();
                $data['lost'] = $lost;
            }
        }

        $data['title'] = "Импорт из ТоргСофта";


        $import_send_email = getOption('import_send_email');
        if($import_send_email){
            if((isset($_GET['cron']) && $_GET['cron'] == true) || $torgsoftAction == true) {
                loadHelper('mail');
                $admMsg = date("Y-m-d H:i") . ' произошла синхронизация товаров с ТоргСофтом';
                if ($torgsoftAction) $admMsg .= ' <b>по запросу со стороны ТоргСофта</b>';
                $admMsg .= '<br>Штамп времени: ' . $filemtime . '<br /><br/><i>Это уведомление можно отключить в опциях, опция: <b>Импорт - присылать уведомление (import_send_email)</b></i>';
                mail_send(getOption('admin_email'), 'Произошла синхронизация товаров с ТоргСофтом', $admMsg);
            }
        }
        // Проверяем, нужно ли выводить таблицы на экран
        if(! $invisible) {
            $data['msg'] = $msg;

            $data['categories'] = $this->model_categories->getCategories();

            $this->load->view('admin/import', $data);

        }
    }

    function set_color()
    {
        isAdminLogin();

        if (isset($_GET['id']) && isset($_GET['color']) && isset($_GET['base_ids']) && isset($_GET['stgi'])) {

            $sizes_to_good_ids = $base_ids = $warehouse = NULL;


            if(isset($_GET['stgi'])){
                $sizes_to_good_ids = urldecode($_GET['stgi']);
            }

            if(isset($_GET['base_ids'])){
                $base_ids = urldecode($_GET['base_ids']);
            }
            if(isset($_GET['warehouse'])){
                $warehouse = urldecode($_GET['warehouse']);
            }

            $dbins = array(
                'sizes_to_good_ids' => $sizes_to_good_ids,
                'base_ids'  => $base_ids,
                'warehouse' => $warehouse
            );

            $this->db->where('id', $_GET['id']);
            $this->db->limit(1);
            $this->db->update('shop', $dbins);
            echo 'Цвет изменён!<br /><a href="/admin/shop/edit/' . $_GET['id'] . '/">перейти к редактированию товара</a>';
        }
    }

}