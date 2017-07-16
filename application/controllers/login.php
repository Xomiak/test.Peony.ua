<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->model('Model_users', 'users');
    }

    function soc()
    {
        loadHelper('geoip');
        $geoipArr = getDataByIp();
        if(isset($_GET['from']))
            userdata('from', $_GET['from']);
        $back = getenv("HTTP_REFERER");
        $this->load->library('uauth');
        $this->load->library('ulogin');
        $s_user = $this->ulogin->userdata();
        //vdd($s_user);
        ?>
        <script
            src="http//ulogin.ru/js/ulogin.js"></script><!--div id="uLogin_4712c9a2" data-uloginid="4712c9a2"></div-->
        <?php

        //vdd($s_user);
        if ($s_user) {
            $this->db->where('email', $s_user['email']);
            $this->db->limit(1);
            $user = $this->db->get('users')->result_array();

            if (!$user) {
//vdd('new');
                $bd_date = $s_user['bdate'];
                $dbarray = explode('.', $bd_date);
                if (is_array($dbarray)) {
                    if (strlen($dbarray[1]) == 1)
                        $dbarray[1] = '0' . $dbarray[1];
                    if (strlen($dbarray[0]) == 1)
                        $dbarray[0] = '0' . $dbarray[0];

                    $bd_date = $dbarray[2] . '-' . $dbarray[1] . '-' . $dbarray[0];
                    $bd_date_unix = mktime(0, 0, 0, $dbarray[1], $dbarray[0], $dbarray[2]);
                }

                $passgen = $this->getActiveCode();


                $from = "";
                if (userdata('adwords') !== false) $from = userdata('adwords');

                if ($s_user['sex'] == 1) $s_user['sex'] = 'm';
                elseif ($s_user['sex'] == 2) $s_user['sex'] = 'w';
                $dbins = array(
                    'login' => $s_user['email'],
                    'email' => $s_user['email'],
                    'active' => 1,
                    'city' => $s_user['city'],
                    'country' => $s_user['country'],
                    'name' => $s_user['first_name'],
                    'lastname' => $s_user['last_name'],
                    'avatar' => $s_user['photo'],
                    'reg_date' => date("Y-m-d H:i"),
                    'reg_ip' => $_SERVER['REMOTE_ADDR'],
                    'network' => $s_user['network'],
                    'photo' => $s_user['photo'],
                    'profile' => $s_user['profile'],
                    'uid' => $s_user['uid'],
                    'sex' => $s_user['sex'],
                    'register_from' => 'ulogin',
                    'pass' => md5($passgen),
                    'bd_date' => $bd_date,
                    'bd_date_unix' => $bd_date_unix,
                    'email_active' => 0,
                    'from' => $from,
                    'geoip' => json_encode($geoipArr)
                );
                if($s_user['network'] == 'vkontakte')
                    $dbins['vk_id'] = $s_user['uid'];

                if (isset($_GET['dropshipper'])) {
                    $dbins['type'] = 11;
                    $dbins['user_type'] = 'Дропшиппер';
                    $dbins['user_type_id'] = 11;
                    $this->load->helper('mail_helper');
                    $to = getOption('admin_email');
                    $subject = "Зарегистрировался новый дропшиппер!";
                    $msg = "Дропшиппер:  " . $s_user['last_name'] . ", " . $s_user['first_name'] . " (" . $s_user['email'] . ")";
                    mail_send($to, $subject, $msg);
                }
                //var_dump($dbins); die();

                $this->db->insert('users', $dbins);
//vdd("sd");
                $mailer_bd_header = "http://www.peony.ua/upload/email/e5771e5fc2f1f4416d0657605466b66a.jpg";
                $content = getOption('mail_new_client_message');
                $mailer_bd_footer = getOption('mailer_bd_footer');

                $message = $this->createEmail($mailer_bd_header, $content, $mailer_bd_footer);

//vd($message);

                $this->load->helper('mail_helper');
                $sended = mail_send($s_user['email'], 'Добро пожаловать на PEONY.ua !', $message);

//vd($sended);
                $user = $this->users->getUser($s_user['email']);
                // vdd($user);

                if ($user) {
                    $this->currectBdDate($user, $s_user);
                    /*
                    $sc = $this->users->getSubscriber($s_user['email']);
                    if(!$sc)
                    {
                        $sc = array(
                            'email' => $s_user['email'],
                            'user_id' => $user['id'],
                            'name'      => $s_user['first_name'],
                            'email_md5' => md5($s_user['email']),
                            'active'    => 1
                        );
                        $this->db->insert('subscribe', $sc);
                    }
                    else if($sc['active'] == 0)
                    {
                        $sc['active'] = 1;
                        
                        $this->db->where('id', $sc['id']);
                        $this->db->limit(1);
                        $this->db->update('subscribe', $sc);
                    }
                 */
                    set_userdata('login', $user['email']);
                    if ($user['from'])
                        set_userdata('adwords', $user['from']);
                    set_userdata('pass', $user['pass']);
                    set_userdata('email', $user['email']);
                    set_userdata('type', $user['type']);
                    set_userdata('user_type_id', $user['user_type_id']);
                    if($user['vk_access_token'] != NULL && $user['vk_access_token'] != '')
                        set_userdata('access_token', $user['vk_access_token']);
                    if ($back != '/my_cart/') {
                        set_userdata('msg', getOption('authorize_ok_message'));
                    }
                }

            } else {
                //vdd($user);
                $user = $user[0];

                $this->currectBdDate($user, $s_user);

                $dbins = array();
                if (($user['avatar'] != $s_user['photo']) && $s_user['photo'] != '') {
                    $dbins['avatar'] = $s_user['photo'];
                    $dbins['photo'] = $s_user['photo'];
                }

                if($geoipArr)
                    $dbins['geoip'] = json_encode($geoipArr);

                if(($s_user['network'] == 'vkontakte') && ($user['vk_id'] != $s_user['uid']))
                    $dbins['vk_id'] = $s_user['uid'];

                if (isset($_GET['dropshipper'])) {
                    if($user['type'] != 11 && $user['user_type_id'] != 11) {
                        $dbins['type']          = $user['type']             = 11;
                        $dbins['user_type']     = $user['user_type']        = 'Дропшиппер';
                        $dbins['user_type_id']  = $user['user_type_id']     = 11;

                        $this->load->helper('mail_helper');
                        $to = getOption('admin_email');
                        $subject = "Клиент изменил свой тип на Дропшиппера!";
                        $msg = "Клиент " . $user['lastname'] . ", " . $user['name'] . " (" . $user['email'] . ")" . " изменил свой тип на Дропшиппера!";
                        mail_send($to, $subject, $msg);
                    }
                }

                if (($user['name'] == '' || $user['name'] == NULL) && $user['name'] != $s_user['first_name']) $user['name'] = $s_user['first_name'];
                if (($user['lastname'] == '' || $user['lastname'] == NULL) && $user['lastname'] != $s_user['last_name']) $user['lastname'] = $s_user['last_name'];


                if (($user['network'] != $s_user['network'])) {
                    $dbins['network'] = $s_user['network'];
                    if (strpos($user['profile'], $s_user['profile']) === false)
                        $dbins['profile'] = $user['profile'] . '|' . $s_user['profile'];
                }
                if (isset($s_user['city']) && $user['city'] == '') $dbins['city'] = $s_user['city'];
                if (isset($s_user['country']) && $user['country'] == '') $dbins['country'] = $s_user['country'];

                if ($user['bd_date'] == '') {
                    $bd_date = $s_user['bdate'];
                    $dbarray = explode('.', $bd_date);
                    if (is_array($dbarray)) {

                        $bd_date = $dbarray[2] . '-' . $dbarray[1] . '-' . $dbarray[0];
                        $dbins['bd_date'] = $bd_date;
                    }
                }

                if ($user['bd_date_unix'] == 0) {
                    $dbarray = explode('-', $user['bd_date']);
                    vd($dbarray);
                    if (is_array($dbarray)) {
                        $bd_date_unix = mktime(0, 0, 0, $dbarray[1], $dbarray[2], $dbarray[0]);
                        $dbins['bd_date_unix'] = $bd_date_unix;
                    }
                }

                $dbins['last_login_ip'] = $_SERVER['REMOTE_ADDR'];
                $dbins['last_login_date'] = date("Y-m-d H:i");
                if (userdata('adwords') !== false)
                    $dbins['from'] = userdata('adwords');

                $this->db->where('id', $user['id']);
                $this->db->limit(1);
                $this->db->update('users', $dbins);
                //vdd($user['bd_date_unix']);
                set_userdata('login', $user['login']);
                set_userdata('pass', $user['pass']);
                set_userdata('email', $user['email']);
                set_userdata('type', $user['type']);
                set_userdata('user_type_id', $user['user_type_id']);
//                if($user['vk_access_token'] != NULL && $user['vk_access_token'] != '')
//                    set_userdata('access_token', $user['vk_access_token']);
                
                if($user['vk_id'] != NULL) set_userdata('vk_id', $user['vk_id']);

                if ($back != '/my_cart/') {
                    set_userdata('msg', getOption('authorize_ok_message'));
                }
                if (isset($_GET['dropshipper'])) {
                    set_userdata('msg', "Поздравляем! Вы успешно стали нашим партнёром!");
                }

            }
        }

        //var_dump(userdata('login')); die();
        //$back = '/';

        //if(userdata('last_uri') != false) $back = userdata('last_uri');
        redirect($back);
    }

    function getActiveCode($chars_min = 6, $chars_max = 10, $use_upper_case = true, $include_numbers = true, $include_special_chars = false)
    {
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

    public function index()
    {
        if (isset($_POST['login']) && isset($_POST['pass'])) {

            $user = $this->users->getUserByLogin($_POST['login']);
            if (!$user) {
                ?>
                <script>
                    alert("Логин или пароль введены не верно!");
                </script>
                <?php
                set_userdata('login_err', 'Логин или пароль введены не верно!');
            } else {
                if ($user['pass'] != md5($_POST['pass'])) {
                    ?>
                    <script>
                        alert("Логин или пароль введены не верно!");
                    </script>
                    <?php
                    set_userdata('login_err', 'Логин или пароль введены не верно!');
                } else {
                    if ($user['activation'] == 0) {
                        ?>
                        <script>
                            alert("Ваш аккаунт ещё не подтверждён! Перейдите по ссылке, высланной Вам на e-mail!");
                        </script>
                        <?php
                        set_userdata('login_err', 'Для входа Вы должны активировать аккаунт!<br /><a rel="nofollow" href="/register/send-activation-code/' . $user['id'] . '/">Отправить повторно код активации</a>');
                        //echo userdata('login_err');
                        //die();
                    } else {
                        set_userdata('login', $user['login']);
                        set_userdata('pass', $user['pass']);
                        set_userdata('type', $user['type']);
                        $this->users->setLastDateAndIp($user['login']);
                        set_userdata('msg', getOption('authorize_ok_message'));
                        set_userdata('user_type_id', $user['user_type_id']);
                    }
                }
            }
        }

        // if(isset($_POST['back']))
        //redirect($_POST['back']);
        /*
        if(userdata('last_url') != null)
        {
            redirect(userdata('last_url'));
        }

        else*/
        redirect('/user/mypage/');
    }

    public function logout()
    {
        unset_userdata('login');
        unset_userdata('pass');
        unset_userdata('type');
        unset_userdata('user_type_id');
        unset_userdata('access_token');
        unset_userdata('user_id');
        unset_userdata('group_id');
        unset_userdata('owner_id');
        unset_userdata('albumOrGroup');

        /*
        if(userdata('last_url') != null)
        {
            redirect(userdata('last_url'));
        }

        else*/
        redirect('/');
    }

    function currectBdDate($user, $s_user)
    {
        $bd_date = $s_user['bdate'];
        $dbarray = explode('.', $bd_date);
        if (is_array($dbarray)) {
            if (strlen($dbarray[1]) == 1)
                $dbarray[1] = '0' . $dbarray[1];
            if (strlen($dbarray[0]) == 1)
                $dbarray[0] = '0' . $dbarray[0];

            $bd_date = $dbarray[2] . '-' . $dbarray[1] . '-' . $dbarray[0];
            $bd_date_unix = mktime(0, 0, 0, $dbarray[1], $dbarray[0], $dbarray[2]);
            if ($bd_date != $user['bd_date'] || $bd_date_unix != $user['bd_date_unix']) {

                $dbins = array(
                    'bd_date_unix' => $bd_date_unix,
                    'bd_date' => $bd_date
                );

                $this->db->where('id', $user['id']);
                $this->db->limit(1);
                $this->db->update('users', $dbins);
            }
        }
    }


    private function createEmail($header, $content, $footer, $articles = false)
    {
        $email = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <style type="text/css">
            .ReadMsgBody {width: 100%; background-color: #ffffff;}
            .ExternalClass {width: 100%; background-color: #ffffff;}
            body     {width: 100%; /* background-color: #ffffff; */ margin:0; padding:0; -webkit-font-smoothing: antialiased;font-family: Georgia, Times, serif}
            table {border-collapse: collapse;}
            a{ color: #000; text-decoration: none;}

            @media only screen and (max-width: 640px)  {
                            body[yahoo] .deviceWidth {width:440px!important; padding:0;}
                            body[yahoo] .center {text-align: center!important;}
                    }

            @media only screen and (max-width: 479px) {
                            body[yahoo] .deviceWidth {width:280px!important; padding:0;}
                            body[yahoo] .center {text-align: center!important;}
                    }
        </style>
    </head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: \'Trebuchet MS\', Helvetica, sans-serif;">
       <!-- One Column -->
            <table width="580"  class="deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eeeeed" style="margin:0 auto;">
                <tr>
                    <td valign="top" style="padding:0" bgcolor="#ffffff">
                        <a style="color: #000; text-decoration: none;" href="#"><img  class="deviceWidth" src="' . $header . '" alt="" border="0" style="width: 680px; height: auto; display: block; border-radius: 4px;" /></a>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #000; font-weight: normal; text-align: center; font-family: \'Trebuchet MS\', Helvetica, sans-serif; line-height: 24px; padding:10px 8px 10px 8px" bgcolor="#fff">
                        ' . $content . '
                    </td>
                </tr>
            </table><!-- End One Column -->


            <!-- Two Column (Images Stacked over Text) -->
            <table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth" bgcolor="#fff" style="margin:0 auto;">
                <tr>
                    <td class="center" style="padding:10px 0 0 5px">
            ';

        if ($articles) {
            $currensy_grn = $this->model_options->getOption('usd_to_uah');
            $currensy_rub = $this->model_options->getOption('usd_to_rur');
            $count = count($articles);
            $col = 0;
            $email .= '<h3>Последние поступления</h3>
                      <table width="99%" border="0" cellpadding="0" cellspacing="0" class="deviceWidth" style="margin-bottom: 15px;">';
            for ($i = 0; $i < $count; $i++) {
                $a = $articles[$i];
                $cat = $this->categories->getCategoryById($a['category_id']);
                $url = '/' . $cat['url'] . '/' . $a['url'] . '/';

                if ($i % 4 == 0) $email .= "<tr>";
                $email .= '
                      <td align="center" valign="top" width="25%">
                               <p style="mso-table-lspace:0;mso-table-rspace:0; margin:0">
                                   <a style="color: #000; text-decoration: none;" href="http://www.peony.ua' . $url . '">
                                       <img width="150" src="http://www.peony.ua' . $a['image'] . '" alt="" border="0" style="width: 150px" class="deviceWidth" />
                                       ' . $a['name'] . ' (' . $a['color'] . ')
                                   </a>
                               </p>
                      </td>
                      ';
                if ($i % 4 == 3) $email .= "</tr>";


            }
            $email .= '</table>';
        }

        $email .= '<hr color="#aaa" />' . $footer . '

    </body>
</html>';

        return $email;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */