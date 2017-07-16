<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function preloader(){
    $CI = & get_instance();
    $cache = $CI->config->item('cache');
    $cacheTime = $CI->config->item('cache_time');
//if(isDebug()) vdd($cacheTime);

    if($cache) {
        $CI->load->driver('cache', array('adapter' => 'file'));

        if (isset($_GET['refreshCache'])) {
            //vdd('refresh');
            $url = request_uri(true, true);
            $CI->cache->delete(md5($url));
            alert($url);
            redirect($url);
        }
    }

    if(userdata('notfirst') === false){
        loadHelper('geoip');
        $country = getUserCountry();
        if($country) set_userdata('userCountry', $country);
        $city = getUserCity();
        if($city) set_userdata('userCity', $city);

        //if($country == 'Украина') set_userdata('currency', 'uah');
        if($country == 'Россия') set_userdata('currency', 'rub');
        //elseif($country == '') set_userdata('currency','uah');
        else set_userdata('currency','uah');


        set_userdata('notfirst', true);
    }

//    if(isDebug()){
//        $CI->output->enable_profiler(TRUE);
//    } else $CI->output->enable_profiler(FALSE);
}

function cacheUrl($url = false){
    if(!$url) $url = $_SERVER['REQUEST_URI'];

    if($url != '/'){
        $url = substr($url, 1);
        $url = substr($url, 0, -1);
    } else $url = 'home';

    return str_replace('/','-',$url);
}

function showCache($type, $params = false){

    $CI = & get_instance();
    $cache = $CI->config->item('cache');
    $cacheTime = $CI->config->item('cache_time');
    // Ищем, есть ли кэшированный head для текущего урла
    $cachedAll = true;
    $headHtml = '';
    if($cache)
        $headHtml = $CI->partialcache->get('head_'.cacheUrl(), $cacheTime);
    if(!$headHtml){
        $cachedAll = false;
    }
    $headerHtml = $CI->load->view('header_new', false, true);

    $contentHtml = '';
    if($cache) {
        $contentHtml = $CI->partialcache->get(cacheUrl(), 600);
        if(!$contentHtml) $cachedAll = false;
    }

    if($cachedAll) {
        $footerNoCached = $CI->load->view('footer_no_cached.php', false, true);

        echo $headHtml;
        echo $headerHtml;
        $contentHtml = str_replace('[no_cached]', $footerNoCached, $contentHtml);
        if($type == 'shop'){
            //vd($params['article']);
            $reviewsHtml = $CI->load->view('mod/reviews.inc.php', $params, true);
            $contentHtml = str_replace('[reviews]', $reviewsHtml, $contentHtml);
        }
        return $contentHtml;
    }
    return false;
}

function showDebugDetails(){
    $CI = & get_instance();
    echo '<br/>Elapsed time: '.$CI->benchmark->elapsed_time();
    echo '<br/>Memory Usage: '.$CI->benchmark->memory_usage();
    echo '<br/>Memory Usage: '.$CI->benchmark->memory_usage();
}


// COUNTRIES, CITIES AND ADDRESSESS
function getCountryById($id){
    return getItemById($id,'countries');
}

function getCountries(){
    $model = getModel('shop');
    return $model->getCountries();
}

function getCountryByName($name, $createNew = true){
    $name = mb_ucfirst($name);
    $CI = & get_instance();
    $CI->load->model('Model_shop','shop');
    $result = $CI->shop->getCountryByName($name);
    if(!$result && !$createNew) return false;

    if(!$result)
        $CI->db->insert('countries', array('name' => $name));
    $result = $CI->shop->getCountryByName($name);
    return $result;
}

function getCityById($id){
    return getItemById($id,'cities');
}

function getCityByName($name, $country_id = false, $country = false){
    $name = mb_ucfirst($name);
    $CI = & get_instance();
    $CI->load->model('Model_shop','shop');
    if(!$country_id && $country != false)
        $country = getCountryByName($country);
    if(isset($country['id'])) $country_id = $country['id'];
    return $CI->shop->getCityByName($name, $country_id);
}

function addCity($name, $country){
    $name = mb_ucfirst($name);
    $country = mb_ucfirst($country);
    $CI = & get_instance();
    $country = getCountryByName($country);
    if($country){
        $dbins = array(
            'name' => $name,
            'country'   => $country['name'],
            'country_id' => $country['id']
        );
        $CI->db->insert('cities', $dbins);

        return getCityByName($name, $country['id']);
    }

    return false;
}

function getAllAddrByUserId($user_id){
    $model = getModel('shop');
    return $model->getAllAddrByUserId($user_id);
}
function getAddrById($id){
    $model = getModel('shop');
    return $model->getAddr($id);
}

function getAddrsByLogin($login){
    $model = getModel('shop');
    return $model->getAddrByLogin($login);
}

function getDefaultAddrByLogin($login){
    $model = getModel('shop');
    return $model->getDefaultAddrByLogin($login);
}
function getDefaultAddrByUserId($user_id){
    $model = getModel('shop');
    return $model->getDefaultAddrByUserId($user_id);
}

function addAddrFromUser($user, $default = 0){

    $model = getModel('shop');
    $allAddr = $model->getAllAddrByUserId($user['id']);
    if(!$allAddr)
        $default = 1;

    $name = $user['name'];
    if($user['lastname'] != ''){
        if($name != '') $name .= ' ';
        $name .= $user['lastname'];
    }
    $country = false;
    $country_id = 0;
    $city = false;
    $city_id = 0;
    if($user['country'] != ''){
        $country = getCountryByName(trim($user['country']));
        if($country) {
            $country_id = $country['id'];
            $country = $country['name'];
        }
    }
    if($user['city'] != ''){
        $city = getCityByName(trim($user['city']), $country_id);
        if(!$city && $country_id != 0)
            $city = addCity(trim($user['city']), $country);
        if($city) {
            $city_id = $city['id'];
            $city = $city['name'];
        }
    }

    $unixAdded = time();

    if($user['tel'] == NULL) $user['tel'] = '';
    if($user['adress'] == NULL) $user['adress'] = '';
    if($user['zip'] == NULL) $user['zip'] = '';
    if($user['np'] == NULL) $user['np'] = '';
    if($user['city'] == NULL) $user['city'] = '';
    if($user['country'] == NULL) $user['country'] = '';

    $dbins = array(
        'user_id'   => $user['id'],
        'login'     => $user['login'],
        'name'      => $name,
        'tel'       => $user['tel'],
        'country'   => $country,
        'city'      => $city,
        'country_id'=> $country_id,
        'city_id'   => $city_id,
        'adress'    => $user['adress'],
        'np'        => $user['np'],
        'zip'       => $user['zip'],
        'passport'  => $user['passport'],
        'default'   => $default,
        'date_added'=> date("Y-m-d H:i"),
        'unix_added'=> $unixAdded
    );

    $CI = &get_instance();
    $CI->db->insert('addr', $dbins);

    $CI->db->where('unix_added', $unixAdded);
    $CI->db->where('user_id', $user['id']);
    $addr = $CI->db->get('addr')->result_array();
    if(isset($addr[0])) return $addr[0];

    return false;
}

function getDeliveryByName($name){
    $model = getModel('shop');
    return $model->getDeliveryByName($name);
}
function getDeliveries(){
    $model = getModel('shop');
    return $model->getDeliveries(1);
}
function getDeliveriesByCountryId($country_id){
    $model = getModel('shop');
    return $model->getDeliveriesByCountryId($country_id);
}

// **COUNTRIES, CITIES AND ADDRESSESS

function xml_to_array($XML){
    $XML = trim($XML);
    $returnVal = $XML;

// Expand empty tags
    $emptyTag = '<(.*)/>';
    $fullTag = '<\\1></\\1>';
    $XML = preg_replace ("|$emptyTag|", $fullTag, $XML);

    $matches = [];
    if (preg_match_all('|<(.*)>(.*)</\\1>|Ums', trim($XML), $matches))
    {
        // Если есть элементы, тогда вернуть массив, иначе текст
        if (count($matches[1]) > 0) $returnVal = [];
        foreach ($matches[1] as $index => $outerXML)
        {
            $attribute = $outerXML;
            $value = xml_to_array($matches[2][$index]);
            if (! isset($returnVal[$attribute])) $returnVal[$attribute] = [];
            $returnVal[$attribute][] = $value;
        }
    }
// Bring un-indexed singular arrays to a non-array value.
    if (is_array($returnVal)) foreach ($returnVal as $key => $value)
    {
        if (is_array($value) && count($value) == 1 && key($value) === 0)
        {
            $returnVal[$key] = $returnVal[$key][0];
        }
    }
    return $returnVal;
}

function getRandCode($chars_min = 5, $chars_max = 10, $use_upper_case = false, $include_numbers = true, $include_special_chars = false) {
    $length = rand($chars_min, $chars_max);
    $selection = 'aeuoyibcdfghjklmnpqrstvwxzQWERTYUIOPASDFGHJKLZXCVBNM';
    if ($include_numbers) {
        $selection .= "1234567890";
    }
    if ($include_special_chars) {
        $selection .= "!@\"#$%&[]{}?|";
    }

    $password = "";
    for ($i = 0; $i < $length; $i++) {
        $current_letter = $use_upper_case ? (rand(0, 1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];
        $password .= $current_letter;
    }

    return $password;
}

function sanitize_output($buffer) {

    $search = array(
        '/\>[^\S ]+/s',  // вырезаем после тегов все отступы, кроме пробелов
        '/[^\S ]+\</s',  // вырезаем перед тегами все отступы, кроме пробелов
        '/(\s)+/s'       // заменяем несколько пробелов одним
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}

function getRating($a)
{
    if(isset($a['rating']) && isset($a['voitings']))
    {
        if($a['rating'] > 0 && $a['voitings'] > 0)
            return ($a['rating']/$a['voitings']);
        else
            return 0;
    }
    return false;
}

function getUserIdBylogin($login, $returnFullData = false){
    $CI = & get_instance();
    $CI->load->model('Model_users','users');
    $user = $CI->users->getUserByLogin($login);
    if($user && !$returnFullData) return $user['id'];
    elseif($user) return $user;

    return false;
}

function shortCodes($content)
{
    if(strpos($content,'[UAH]') !== false)
    {
        $uah = getCurrencyValue('UAH');
        $content = str_replace('[UAH]', $uah, $content);
    }
    if(strpos($content,'[RUB]') !== false)
    {
        $rub = getCurrencyValue('RUB');
        $content = str_replace('[RUB]', $rub, $content);
    }
    if(strpos($content,'[shop_nadbavka]') !== false) {
        $content = str_replace('[shop_nadbavka]', getOption('shop_nadbavka'), $content);
    }
    if(strpos($content,'[dealer_price_adding]') !== false) {
        $content = str_replace('[dealer_price_adding]', getOption('dealer_price_adding'), $content);
    }
    if(strpos($content,'[npnp_price_uah]') !== false) {
        $npnp_price = getOption('npnp_price');
        $currency_uah = getCurrencyValue('UAH');
        $value = $npnp_price * $currency_uah;
        $content = str_replace('[npnp_price_uah]', $value, $content);
    }
    return $content;
}

function getTags($shop, $category = false){
    if(!$category) {
        $CI = & get_instance();
        $category = $CI->model_categories->getCategoryById($shop['category_id']);
    }
    
    $name = $shop['name'] . ' (' . $shop['color'] . ')';
    if(strpos($name, $category['name']) === false)
        $name = $category['name_one'] . ' ' . $name;

    $tags = '';

    if($shop['tags'] != NULL && $shop['tags'] != ''){   // проверяем, указан ли тэг в товаре
        $tags .= $shop['tags'].', ';
    }
    $tags .= $shop['name'].', '.$category['name'].', '.$name.', купить '.$category['name'].', peony';
    $tags .= ', '.$category['name_buy'].', '.$category['name_buy'].' '.$shop['name'];
    if($shop['tkan'] != '') $tags .= ', '.$category['name_one'].' '.$shop['tkan'];
    if($shop['season'] != '') $tags .= ', '.$category['name_one'].' '.$shop['season'];
    if($shop['sostav'] != '') $tags .= ', '.$category['name_one'].' '.$shop['sostav'];

    $tag = str_replace('женскую','', $category['name_buy']);
    $tag = str_replace('женский','', $tag);
    $tag = str_replace('женские','', $tag);

    $category['name'] = mb_strtolower($category['name']);
    $category['name_one'] = mb_strtolower($category['name_one']);

    if($category['name'] == 'платья') $tags .= ', '.$category['name_one'].' длинное, женское длинное платье';
    elseif($category['name'] == 'блузки') $tags .= ', '.$category['name_one'].' длинная, женская длинная блузка';

    $tags .= ', '.$category['name_one'].' женское, '.$category['name_one'].' женское приталенное, женские '.$category['name'].', женские '.$category['name'].' оптом, '.$category['name'].' от производителя,';
     $tags .= ', '.$category['name_one'].', красивые '.$category['name'].', женские '.$category['name'].' оптом, '.$category['name_one'].' большой размер, модные женские '.$category['name'].', женское '.$category['name_one'].' от производителя, нарядные женские '.$category['name'];

    if($tag != $category['name_buy'])
        $tags .= ', '.$tag;

    return $tags;
}

function adWordsLog()
{
    $CI = & get_instance();

    $url = "";
    if(isset($_GET['url'])) $url = $_GET['url'];
    $targetid = "";
    if(isset($_GET['targetid'])) $targetid = $_GET['targetid'];
    $network = "";
    if(isset($_GET['network'])) $network = $_GET['network'];
    $network = "";
    if(isset($_GET['adtype'])) $adtype = $_GET['adtype'];
    $product_id = "";
    if(isset($_GET['product_id'])) $product_id = $_GET['product_id'];
    $keyword = "";
    if(isset($_GET['keyword'])) $keyword = $_GET['keyword'];
    $dbins = array(
        'date'          => date("Y-m-d H:i"),
        'unix'          => time(),
        'adwords_id'	=> $_GET['adwords'],
        'url'			=> $url,
        'targetid'		=> $targetid,
        'ip'            => GetRealIp(),
        'network'       => $network,
        'adtype'        => $adtype,
        'product_id'    => $product_id,
        'keyword'       => $keyword
    );
    $CI->db->insert('adwords', $dbins);
}

// Удаляем пустые элементы массива
function array_clean($array) {
    $ret = array();
    $ri = 0;
    if(is_array($array)){
        $count = count($array);
        for($i = 0; $i < $count; $i++)
        {
            if((isset($array[$i])) && is_array($array[$i])) {
                $ret[$ri] = $array[$i];
                $ri++;
            }
        }
    }
    return $ret;
}

function arrayDelCopies($array){
    $ret = array();
    $ids = array();
    $i = 0;

    foreach ($array as $item){
        if(isset($item['id'])){
            if(!in_array($item['id'], $ids)){
                array_push($ret,$item);
                array_push($ids,$item['id']);
            }
        }
    }
    return $ret;
}

function arrayGetPart($array, $per_page, $from){
    $ret = array();
    $count = count($array);
    if($count < ($per_page+$from)) $count = $per_page+$from;
    for($i = $from; $i < ($count);$i++){
        if(isset($array[$i])){
            //vd($i);
            array_push($ret,$array[$i]);
        }
    }
    return $ret;
}

function writeOrderFile($order, $config = false, $fromProm = false)
{
    $CI = & get_instance();
    $conf = array(
        'host'  => 'peony.ftp.ukraine.com.ua',
        'login' => 'peony_import',
        'password' => '123qweasdzxc',
        'path'      => '/'
    );
    if(isset($config['host'])) $conf['host'] = $config['host'];
    if(isset($config['login'])) $conf['host'] = $config['login'];
    if(isset($config['password'])) $conf['host'] = $config['password'];
    if(isset($config['path'])) $conf['host'] = $config['path'];

    //$CI->load->model('Model_shop', 'shop');
    $CI->load->model('Model_users', 'users');
    $CI->load->model('Model_shop', 'shop');
    $order = $CI->shop->getOrderById($order);
    if($order)
    {
      //  vdd($order);
        $adress = '';
      //  vd($order);
        $user = $CI->users->getUserById($order['user_id']);
        //echo '<hr>'; vd($order); echo '<hr>';vd($user);
        $details = false;
        if($order['details'] != NULL) $details = json_decode($order['details'], true);

       // $details = array();
        if(!isset($details['city'])) $details['city'] =  $user['city'];
        if(!isset($details['country'])) $details['country'] =  $user['country'];
        if(!isset($details['adress'])) $details['adress'] =  $user['adress'];
        if(!isset($details['tel'])) $details['tel'] =  $user['tel'];
        if(!isset($details['zip'])) $details['zip'] =  $user['zip'];
        if(!isset($details['passport'])) $details['passport'] =  $user['passport'];
        if(!isset($details['np'])) $details['np'] = $user['np'];

        $adress = $details['country'].', '.$details['city'].', ';
        //if($details['tel'] != '') $adress .= $details['tel'].', ';
        if($details['country'] != 'Украина') $adress .= ' Паспорт: '.$details['passport'];

//        if($order['delivery'] == 'Новая Почта') {
//            $order['delivery'] .= ' №' . $details['np'];
//            $adress .= 'Новая Почта №'.$details['np'];
//        }

        $saleType = 1;
        if($order['status'] == 'payed')
            $saleType = 3;
//        elseif($order['status'] == 'processing')
//            $saleType = 2;

        if($details['np'] != '') $details['adress'] .= ' НП №'.$details['np'];
        $comment = '';
        $order['currency'] = strtoupper($order['currency']);
        if($order['currency'] != 'USD'){
            $cursArr = json_decode($order['currencies'],true);
            $comment = 'Валюта: '.$order['currency'].' Курс: '.$cursArr[$order['currency']];
        }


        $mytext = '[Client]
Name='.$user['lastname']." ".$user['name'].'
MPhone='.$details['tel'].'
ZIP='.$details['zip'].'
Country='.$details['country'].'
City='.$details['city'].'
Address='.$details['adress'].'
EMail= '.$user['email'].'

[Options]
SaleType='.$saleType.'
OrderNumber='.$order['id'].'
Comment='.$comment.'
DeliveryCondition='.$order['delivery'];
        if($order['delivery'] == 'Новая Почта' && $order['delivery_np'] != NULL)
            $mytext .= ' №'.$order['delivery_np'];
       $mytext .= "\r\n";
        $my_cart = unserialize($order['products']);
        $pcount = count($my_cart);
        $allcount = 0;
        $promPrice = 0;
        $promAddingCount = 0;
        for($j = 0;$j < $pcount; $j++)
        {
            $mc = $my_cart[$j];
            $product = $CI->shop->getProductById($mc['shop_id']);
            //$cat = $CI->model_categories->getCategoryById($product['category_id']);
            $razmer = explode('*',$product['razmer']);
            $rcount = count($razmer);
            $parent = false;
            $product['price'] = getNewPrice($product['price'], $product['discount']);
            $akciya = isDiscount($product);
            //var_dump($_POST['currency']);

            //$price_one = round($price,2);

            if($order['code'] != 'NULL'){
                $coupon = getCoupon($order['code']);
                if($coupon && $product['discount'] == 0){
                    $product['price'] = getNewPrice($product['price'], $coupon['discount']);
                }
            }

            $pres = 0;
            for($i2 = 0; $i2 < $rcount; $i2++)
            {
                if(isset($mc['kolvo_'.$razmer[$i2]]))
                {
                    $kolvo = $mc['kolvo_'.$razmer[$i2]];
                    $goodID = '';
                    $sizes = json_decode($product['sizes_to_good_ids'], true);
                    if(isset($sizes[$razmer[$i2]])) $goodID = $sizes[$razmer[$i2]];
                    if($kolvo > 0) {
                        // prom
                        if($kolvo < 4){
                            $promAddingCount += $kolvo;
                            if($product['discount'] > 0){
                                $promPrice += getNewPrice($product['price'], $product['discount']);
                            } else $promPrice += $product['price'];
                        }
                        // *prom
                        $allcount++;
                        $mytext .=  '[' . $allcount . ']
GoodID=' . $goodID .  '
Price=' . $product['price']  . '
Count=' . $kolvo  . '

';
                    }
                }
            }
        }

        if($order['country_id'] > 1){
            $shipId = 1852;
            $delivery_price = 1.5;
            if($order['products_count'] > 49) {
                $shipId = 1889;
                $delivery_price = 1;
            }
            $allcount++;
            $mytext .= "\r\n" . '[' . $allcount . ']' . "\r\n" . '
GoodID='.$shipId . "\r\n" . '
Price=' . $delivery_price . "\r\n" . '
Count='. $order['products_count'] . "\r\n" . '

';

        }


        if($order['nadbavka'] != 0 && $order['prom_id'] == 0)
        {
            // получаем тип пользователя для надбавки
            $type = $CI->users->getUserTypeById($user['user_type_id']);
            $delivery_price = $order['nadbavka'];
            if($delivery_price == -1) $delivery_price = $type['delivery_price'];
            if($delivery_price == -1) $delivery_price = getOption('shop_nadbavka');
            $goodID = $type['goodID'];
            if($goodID == 0) $goodID = 3175;

            $allcount++;
            $mytext .= "\r\n" . '[' . $allcount . ']' . "\r\n" . '
GoodID='.$goodID . "\r\n" . '
Price=' . $delivery_price . "\r\n" . '
Count=1' . "\r\n" . '

';
        } elseif($order['prom_id'] > 0){
            $goodID = 4315;
            $prom_price_adding = getOption('prom_price_adding');
            if($prom_price_adding > 0){
                $allcount++;
                $mytext .= "\r\n" . '[' . $allcount . ']' . "\r\n" . '
GoodID='.$goodID . "\r\n" . '
Price=' . $prom_price_adding . "\r\n" . '
Count='. $promAddingCount . "\r\n" . '

';
            }
        }
//echo str_replace("\r\n",'<br>',$mytext);die();
        $mytext = iconv('UTF-8','WINDOWS-1251', $mytext);

        $ret = save_ftp_file($order['id'].'.sal', $mytext, $config);

        $CI->db->where('id', $order['id'])->limit(1)->update('orders',array('torgsoft_file'=>1));

        if($ret) return true;

    }
    return false;
}

//// Запись файла через ftp
function save_ftp_file($file, $content, $config = false)
{
    $conf = array(
        'host'  => 'peony.ftp.ukraine.com.ua',
        'login' => 'peony_import',
        'password' => '123qweasdzxc',
        'path'      => '/'
    );
    if($config) {
        if (isset($config['host'])) $conf['host'] = $config['host'];
        if (isset($config['login'])) $conf['host'] = $config['login'];
        if (isset($config['password'])) $conf['host'] = $config['password'];
        if (isset($config['path'])) $conf['host'] = $config['path'];
    }
    else
    {
        $conf['host'] = getOption('ftp_host');
        $conf['login'] = getOption('ftp_login');
        $conf['password'] = getOption('ftp_password');
    }

    $ftp_path = "ftp://".$conf['login'].":".$conf['password']."@".$conf['host'].$conf['path'].$file;

    $stream_options = array('ftp' => array('overwrite' => true));
    $stream_context = stream_context_create($stream_options);
    if ($fh = fopen($ftp_path, 'w', 0, $stream_context))
    {
        // Writes contents to the file
        $ret = fputs($fh, $content);
        // Closes the file handle
        fclose($fh);

        if($ret) return true;
    }
    return false;
}

function get_ftp_file($file, $config = false)
{
    $conf = array(
        'host'  => 'peony.ftp.ukraine.com.ua',
        'login' => 'peony_ru',
        'password' => '123qweasdzxc',
        'path'      => '/'
    );
    if($config) {
        if (isset($config['host'])) $conf['host'] = $config['host'];
        if (isset($config['login'])) $conf['host'] = $config['login'];
        if (isset($config['password'])) $conf['host'] = $config['password'];
        if (isset($config['path'])) $conf['host'] = $config['path'];
    }
    else
    {
        $conf['host'] = getOption('ftp_host');
        $conf['login'] = getOption('ftp_login');
        $conf['password'] = getOption('ftp_password');
    }

    $arr = explode('/', $file);
    vd($arr[count($arr)-1]);

    // объявление переменных
    $local_file = $file;
    $server_file = $file;

// установка соединения
    $conn_id = ftp_connect($conf['host']);

// вход с именем пользователя и паролем
    $login_result = ftp_login($conn_id, $conf['login'], $conf['password']);

// попытка скачать $server_file и сохранить в $local_file
    if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
        echo "Произведена запись в $local_file\n";
        return true;
    } else {
        echo "Не удалось завершить операцию\n";
    }

// закрытие соединения
    ftp_close($conn_id);

    return false;
}

function check_smartphone() {
    //return true;
    $phone_array = array('iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
    $agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );

    foreach ($phone_array as $value) {

        if ( strpos($agent, $value) !== false ) return true;

    }

    return false;

}

function getModalDialog($id, $message, $otherClose = false)
{
    ?>
    <div class="modal fade bs-example-modal-sm" id = "<?=$id?>" tabindex = "-1" role = "dialog" aria-labelledby = "myModalLabel" aria-hidden = "true">
        <div class="modal-dialog modal-sm registration-modal">
            <div class="modal-content">
                <button class="close" type="button" <?php if(!$otherClose) echo 'data-dismiss="modal"'; else echo 'onclick="'.$id.'()"'; ?>>&times;</button>
               <?=$message?>
            </div>
        </div>
    </div>
    <?php
}

function getDiscount($oldPrice, $newPrice)
{
    if($oldPrice != 0) {
        $res = ($oldPrice - $newPrice) * 100 / $oldPrice;
        return round($res, -0.5);
    } else return 0;
}

function delFromCategory($cats, $delCat)
{
    $arr = explode('*', $cats);
    $count = count($arr);
    $ret = "";
    for($i = 0; $i < $count; $i++)
    {
        if($arr[$i] != $delCat)
        {
            $ret .= $arr[$i];
            if(($i+1) < $count) $ret .= '*';
        }
    }
    return $ret;
}

function showMessage($msg)
{
    ?>
    <div class="modal fade bs-example-modal-sm" id = "qqq" tabindex = "-1" role = "dialog" aria-labelledby = "myModalLabel" aria-hidden = "true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <button class="close" type="button" data-dismiss="modal">&times;</button>
                <?=$msg?>
            </div>
        </div>
    </div>
    <?php
}

function str_replace_once($search, $replace, $text) 
{ 
   $pos = strpos($text, $search); 
   return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text; 
} 

function GetRealIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function debug($item)
{
		if($_SERVER['REMOTE_ADDR'] == '195.138.64.78' || $_SERVER['REMOTE_ADDR'] == '178.251.110.53')
		{
			vd($item);
		}
}

function isDebug()
{
    if($_SERVER["REMOTE_ADDR"] == '195.138.64.78' || $_SERVER['REMOTE_ADDR'] == '178.251.104.231') return true;
    if(userdata('login') == 'anton@rap.org.ua' || userdata('login') == 'xomiak@rap.org.ua') return true;
    return false;
}

function getFullUrl($article)
{
    //vd($article);
    $CI = & get_instance();
    $url = '';
    $cat = $CI->model_categories->getCategoryById($article['category_id']);
    if($cat)
    {
        $url = '/'.$cat['url'].'/'.$article['url'].'/';
    }
    return $url;
}

function getNewCommentsCount()
{
    $CI = & get_instance();
    $CI->db->where('active', 0);
    $CI->db->from('comments');
    return $CI->db->count_all_results();
}



function getLangs() {
    $CI = & get_instance();
    $langs = explode('|', $CI->model_options->getOption('languages'));
    for ($i = 1; $i < count($langs); $i++) {
        $langs[$i] = trim($langs[$i]);
    }
    return $langs;
}

function getFileType($url)
{
    $ret = $url;
    $pos = strrpos($url,'.');
    if($pos)
    {
        $ret = substr($ret,$pos+1);
    }
    return $ret;
}

function arrayDelNulled($array)
{
    $ret = array();
    $r = 0;
    if(is_array($array))
    {
        $c = count($array);
        for($i = 0; $i < $c; $i++)
        {
            if(trim($array[$i]) != '')
            {
                $ret[$r] = trim($array[$i]);
                $r++;
            }
        }
    }
    return $ret;
}

function clearCache()
{
    $folder = 'application/cache';
    if (is_dir($folder)) {

        $handle = opendir($folder);
        while ($subfile = readdir($handle)) {
            if ($subfile == '.' || $subfile == '..' || $subfile == '.htaccess' || $subfile == 'index.html') continue;
            else {
                @unlink("{$folder}/{$subfile}");
            }

        }
        @closedir($handle);
        if (@rmdir($folder)) return true;
        else return false;
    } else {
        if (@unlink($folder)) return true;
        else return false;
    }
    return false;
}

// Первый символ в верхнем регистре
function mb_ucfirst($str) {
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc.mb_substr($str, 1);
}