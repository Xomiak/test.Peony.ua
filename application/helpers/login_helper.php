<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function isAdminLogin()
{
    $CI = &get_instance();
    if(request_uri(false, true) == '/admin/import/')
        return true;
    // автологин через ссылку из письма админу
    if(isset($_GET['admin_auto_auth']) && $_GET['admin_auto_auth'] == true && userdata('type') != 'admin' && userdata('type') != 'moder' && isset($_GET['login']) && isset($_GET['hash'])){
        // return $url . '?from=email&admin_auto_auth=true&login='.$user['login'].'&password='.md5($user['pass']);
        $user = getUserIdBylogin($_GET['login'], true);
        if($user != false && $_GET['hash'] == md5($user['pass'])){
            if($user['type'] == 'admin' || $user['type'] == 'moder') {
                set_userdata('login', $user['login']);
                set_userdata('pass', $user['pass']);
                set_userdata('type', $user['type']);
                set_userdata('name', $user['login']);
                $logs = getOption('logs');
                if ($logs) {
                    $dbins = array(
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'text' => "Автоматическая авторизация в админпанели по ссылке из почты - успешна. Логин: " . $user['login'],
                        'ip' => $this->GetRealIp(),
                        'login' => $user['login'],
                        'type' => "admin",
                        'error' => "0"
                    );
                    $this->db->insert('logs', $dbins);
                } else redirect('/admin/login/');
            }
        }
    }

    if (!userdata('login')) {
        set_userdata('back', $_SERVER['REQUEST_URI']);
        if (isset($_GET['login']) && isset($_GET['password'])) {
            redirect('/admin/login/?login=' . $_GET['login'] . '&password=' . $_GET['password'] . '&back=' . urlencode($_SERVER['REQUEST_URI']));
        } else redirect('/admin/login/');
    } else {
        $login = userdata('login');
        $user = $CI->db->where('login', $login)->get('users')->result_array();
        if (!$user) redirect('/admin/login/');
        else $user = $user[0];
        if (userdata('type') != 'admin' && userdata('type') != 'moder') redirect('/admin/login/');
        if (userdata('type') != $user['type']) redirect('/admin/login/');
        if (userdata('pass') != $user['pass']) redirect('/admin/login/');
    }
}

function getUserByVkId($vk_id)
{
    $CI = &get_instance();
    $CI->load->model("Model_users", "users");
    $user = $CI->users->getByVkId($vk_id);
    return $user;
}

function enter_login($email, $values = false)
{
    $CI = &get_instance();
    $CI->load->model("Model_users", "users");
    $userInfo = false;
    $result = false;
    if (isset($values['vk_user_id']) && isset($values['access_token'])) { // если авторизация через ВК
        $params = array(
            'uids' => $values['vk_user_id'],
            'fields' => 'uid,first_name,last_name,sex,bdate,photo_big,country,city,photo_100,domain',
            'access_token' => $values['access_token']
        );

        $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);
        if (isset($userInfo['response'][0]['uid'])) {
            $userInfo = $userInfo['response'][0];
            $result = true;
        }
    }

    $user = $CI->users->getUser($email);
    if ($user && isset($values['access_token'])) {
        $dbins = array(
            'vk_access_token' => $values['access_token']
        );
        $CI->db->where('id', $user['id'])->limit(1)->update('users', $dbins);
    }
    if (!$user) {
        $user = $CI->users->getByVkId($values['vk_user_id']);
        if ($user && isset($values['access_token'])) {
            $dbins = array(
                'vk_access_token' => $values['access_token']
            );
            $CI->db->where('id', $user['id'])->limit(1)->update('users', $dbins);
        }
    }
//    if(!$user && $userInfo !== false){
//        $user = $CI->users->searchByProfile('vk.com/' . $userInfo['domain']);
//    }
    if (!$user) {
        if (isset($values['vk_user_id']) && isset($values['access_token'])) { // если авторизация через ВК
            if ($result) {
                $s_user = $userInfo;
                $s_user['email'] = $email;
                $bd_date = false;
                if (isset($s_user['bdate'])) $bd_date = $s_user['bdate'];
                if ($bd_date) {
                    $dbarray = explode('.', $bd_date);
                    if (is_array($dbarray)) {
                        if (strlen($dbarray[1]) == 1)
                            $dbarray[1] = '0' . $dbarray[1];
                        if (strlen($dbarray[0]) == 1)
                            $dbarray[0] = '0' . $dbarray[0];

                        $bd_date = $dbarray[2] . '-' . $dbarray[1] . '-' . $dbarray[0];
                        $bd_date_unix = mktime(0, 0, 0, $dbarray[1], $dbarray[0], $dbarray[2]);
                    }
                }

                $passgen = getRandCode();


                $from = "";
                if (userdata('adwords') !== false) $from = userdata('adwords');

                if ($s_user['sex'] == 1) $s_user['sex'] = 'm';
                elseif ($s_user['sex'] == 2) $s_user['sex'] = 'w';
                if ($s_user['country'] == 2) $s_user['country'] = "Украина";
                $dbins = array(
                    'login' => $s_user['email'],
                    'email' => $s_user['email'],
                    'active' => 1,
                    'country' => $s_user['country'],
                    'name' => $s_user['first_name'],
                    'lastname' => $s_user['last_name'],
                    'avatar' => $s_user['photo_100'],
                    'reg_date' => date("Y-m-d H:i"),
                    'reg_ip' => $_SERVER['REMOTE_ADDR'],
                    'network' => 'vkontakte',
                    'photo' => $s_user['photo_big'],
                    'profile' => 'http://vk.com/' . $s_user['domain'],
                    'sex' => $s_user['sex'],
                    'register_from' => 'export',
                    'pass' => md5($passgen),
                    'bd_date' => $bd_date,
                    'bd_date_unix' => $bd_date_unix,
                    'from' => $from,
                    'email_active' => 0,
                    'vk_id' => $values['vk_user_id'],
                    'user_type_id' => 12,
                    'user_type' => 'ВК',
                    'vk_access_token' => $values['access_token']
                );

                if (isset($values['activation'])) { // нужна ли активация
                    $dbins['activation_code'] = $passgen;
                    $dbins['activation'] = 0;
                    $dbins['active'] = 0;
                }
                $CI->db->insert('users', $dbins);

                $user = $CI->users->getUser($s_user['email']);

                if (isset($values['activation'])) { // нужна ли активация
                    $back = urlencode($_SERVER['REQUEST_URI']);
                    if (isset($_GET['back'])) $back = $_GET['back'];
                    $message = '

Для активации Вашей учётной записи Вам необходимо перейти по следующей ссылке:<br />
	<a href="http://' . $_SERVER['SERVER_NAME'] . '/register/activation/' . $user['id'] . '/' . $passgen . '/?back=' . $back . '">активировать аккаунт</a>.<br /><br/>

Благодарим Вас за проявленный интерес к нашему сайту!
С уважением, администрация сайта ' . $_SERVER['SERVER_NAME'] . '!
';
                    $CI->load->helper('mail_helper');
                    mail_send($s_user['email'], 'PEONY - активация учётной записи', $message, "html");
                    set_userdata('no_auth', 'true');
                    //set_userdata('msg', 'Для активации Вашей учётной записи Вам необходимо перейти по ссылке, отправленной Вам на e-mail');
                } else {
                    $message = '
						Вы успешно зарегистрировались на сайте ' . $_SERVER['SERVER_NAME'] . '!<br />
						Ваши данные для входа:<br />
						Логин: ' . $_POST['login'] . '<br />
						Пароль: ' . $_POST['pass'] . '<br />
						<br />
						
						Благодарим Вас за проявленный интерес к нашему сайту!<br />
						<i>Администрация сайта <a href="http://' . $_SERVER['SERVER_NAME'] . '/">' . $_SERVER['SERVER_NAME'] . '</a>!</i>
						';
                    //set_userdata('msg', 'Вы успешно зарегистрировались!');
                    $CI->load->helper('mail_helper');
                    mail_send($s_user['email'], 'PEONY - Регистрация', $message, "html");
                }
            }
        }
    }

    if ($user && $user['activation'] != 0) {
        set_userdata('login', $user['login']);
        if ($user['from'])
            set_userdata('adwords', $user['from']);
        set_userdata('pass', $user['pass']);
        set_userdata('email', $user['email']);
        set_userdata('type', $user['type']);
        set_userdata('user_type_id', $user['user_type_id']);
        //die();
    }
}

function isAdminEdit($id, $type = 'shop')
{
    $html = '';
    if (isClientAdmin()) {
        $html = '<a rel="nofollow" href="/admin/' . $type . '/edit/' . $id . '/">
                <img border="0" title="Перейти к редактированию" src="/img/edit.png">
                </a>';
    }
    return $html;
}

function isClientAdmin()
{
    $CI = &get_instance();

    if (userdata('login')) {
        $login = userdata('login');
        $user = $CI->db->where('login', $login)->get('users')->result_array();
        if (!$user) return false;
        else $user = $user[0];
        if (userdata('type') != 'admin' && userdata('type') != 'moder') return false;
        if (userdata('type') != $user['type']) return false;
        if (userdata('pass') != $user['pass']) return false;

        return true;
    } else return false;
}

function isLogin()
{
    $CI = &get_instance();
    if (userdata('login') != null && userdata('pass') != null && userdata('type') != null) {
        $login = userdata('login');
        $pass = userdata('pass');
        $type = userdata('type');
        $vk_id = userdata('vk_id');
        if ($login != 'undefined')
            $CI->db->where('login', $login);
        elseif ($vk_id)
            $CI->db->where('vk_id', $login);
        $CI->db->limit(1);
        $user = $CI->db->get('users')->result_array();
        if (!$user) {
            unset_userdata('login');
            unset_userdata('pass');
            unset_userdata('type');
            set_userdata('login_err', 'Подмена пользователя!');
        } else {
            $user = $user[0];
            if ($user['pass'] != $pass) {
                unset_userdata('login');
                unset_userdata('pass');
                unset_userdata('type');
                set_userdata('login_err', 'Несоответствие паролей!');
            } elseif ($user['type'] != $type) {
                unset_userdata('login');
                unset_userdata('pass');
                unset_userdata('type');
                set_userdata('login_err', 'Подмена типа пользователя!');
            }
            /*            elseif($user['activation'] == 0)
                        {
                            unset_userdata('login');
                            unset_userdata('pass');
                            unset_userdata('type');
                            set_userdata('login_err','Пользователь не активирован!');
                        }
            */
        }
    } else {
        // Авто авторизация через hash
        if (isset($_GET['auto_auth']) && $_GET['auto_auth'] == 'true' && isset($_GET['user_id']) && isset($_GET['hash'])) {
            $CI->load->model('Model_users','users');
            $user = $CI->users->getUserById($_GET['user_id']);
            if(($user != false) && $_GET['hash'] == md5($user['pass'])){
                // авторизируемся
                set_userdata('login', $user['login']);
                set_userdata('adwords', 'email');
                set_userdata('pass', $user['pass']);
                set_userdata('email', $user['email']);
                set_userdata('type', $user['type']);
                set_userdata('user_type_id', $user['user_type_id']);
            }
        }
    }
}

function getCurrentUser()
{
    $CI = &get_instance();
    $CI->load->model('Model_users', 'users');
    return $CI->users->getCurrentUser();
}

function vk_authorize()
{
    $redirect_uri = "http://" . $_SERVER['SERVER_NAME'] . request_uri(false, true);

    $client_id = getOption('vk_client_id');
    $client_secret = getOption('vk_client_secret');
    if (isset($_GET['code']) && !empty($_GET['code'])) {
        //получение "access_token" через параметр code

        $data = Array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'code' => $_GET['code'],
        );

        $data_param = Array();
        foreach ($data as $key => $value) {
            $data_param[] = $key . '=' . $value;
        }

        $url = 'https://oauth.vk.com/access_token?' . implode('&', $data_param);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $content = curl_exec($curl);
        curl_close($curl);

        $token = json_decode($content);

        if (isset($token->access_token)) {
            $access_token = $token->access_token;
            $user_id = $token->user_id;

            set_userdata('access_token', $access_token);
            set_userdata('vk_user_id', $user_id);

            echo "Разрешение от ВК получено!<br />Обновите страницу!";
            redirect('/export/');
            return true;
        }
    }


    //формируем ссылку для авторизации пользователя и полученя параметра "code"
    $data = Array(
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'display' => 'popup',
        'scope' => 'market,photos,offline,groups,wall,email',
        'response_type' => 'code',
        'v' => '5.50',
    );

    $data_param = Array();
    foreach ($data as $key => $value):
        $data_param[] = $key . '=' . $value;
    endforeach;

    $url = 'https://oauth.vk.com/authorize?' . implode('&', $data_param);
    return '<a href="' . $url . '" class = "export-btn">Авторизироваться для экспорта</a>';

}

function vkLogin()
{
    if (isset($_GET['viewer_id'])) {
        set_userdata('iframe', true);
        $vk_id = $_GET['viewer_id'];
        $CI = &get_instance();
        $CI->load->model('Model_users', 'users');
        $user = $CI->users->getByVkId($vk_id);
        $isNew = false;
        if (!$user) { // Добавляем пользователя ВК
            $api_id = 5701998; // Insert here id of your application
            $secret_key = 'o1gePfx1JBpY7CtFzcSv'; // Insert here secret key of your application
            $VK = new vkapi($api_id, $secret_key);
            $resp = $VK->api('getProfiles', array('uids' => $vk_id,
                'fields' => 'uid, first_name, last_name, education, university,university_name, faculty, faculty_name, graduation'));
            if (isset($resp['response'][0])) $resp = $resp['response'][0];

            $passgen = getRandCode();

            $name = "undefined";
            $lastname = "undefined";
            if (isset($resp['first_name'])) $name = $resp['first_name'];
            if (isset($resp['last_name'])) $lastname = $resp['last_name'];

            $dbins = array(
                'login' => $vk_id,
                'active' => 1,
                'email' => '',
                'name' => $name,
                'lastname' => $lastname,
                'reg_date' => date("Y-m-d H:i"),
                'reg_ip' => $_SERVER['REMOTE_ADDR'],
                'pass' => md5($passgen),
                'network' => "vkontakte",
                'profile' => "http://vk.com/id" . $vk_id,
                'uid' => $vk_id,
                'vk_id' => $vk_id,
                'register_from' => 'vk_iframe',
                'email_active' => 0,
                'from' => "vk_iframe",
                'user_type_id' => 1,
                'user_type' => 'Посетитель'
            );

            //vd($dbins);die();
            $CI->db->insert('users', $dbins);

            $user = $CI->users->getByVkId($vk_id);

            $isNew = true;
        }

        if (!$isNew) {
            $dbins['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
            $dbins['last_login_date'] = date("Y-m-d H:i");

            $CI->db->where('id', $user['id']);
            $CI->db->limit(1);
            $CI->db->update('users', $dbins);
        }
        //vdd($user['bd_date_unix']);
        set_userdata('login', $user['login']);
        set_userdata('pass', $user['pass']);
        set_userdata('email', $user['email']);
        set_userdata('type', $user['type']);
        set_userdata('user_type_id', $user['user_type_id']);
        if ($user['vk_access_token'] != NULL && $user['vk_access_token'] != '')
            set_userdata('access_token', $user['vk_access_token']);
        if ($user['vk_id'] != NULL) set_userdata('vk_id', $user['vk_id']);

        //vd($user);
    }
}

function addUser($user)
{
    $CI = &get_instance();
    $CI->db->insert('users', $user);

    // достаём добавленного пользователя
    foreach ($user as $key => $value) {
        $CI->db->where($key, $value);
    }
    $CI->db->limit(1);
    $user = $CI->db->get('users')->result_array();
    if (isset($user[0])) return $user[0];
    else return false;
}

function adminLoginGetAdding($url = ''){
    $login = getOption('email_autologin_login');
    if($login != false) {
        $user = getUserIdBylogin($login, true);
        if ($user)
            return $url . '?from=email&admin_auto_auth=true&login=' . $user['login'] . '&password=' . md5($user['pass']);
    }
    return '';
}

function getUserDescription($user){
    $model = getModel('shop');
    $ordersCount = $model->getUserOrdersCount($user['id']);
    $descr = 'Всего заказов: '.$ordersCount.'<br />Дата регистрации: '.$user['reg_date'];
    return $descr;
}