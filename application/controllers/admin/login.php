<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

    function GetRealIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function index()
    {
        $back = '/admin/';
        if(userdata('back') !== false){
            $back = userdata('back');
            unset_userdata('back');
        }

        if (isset($_GET['back'])) $back = urldecode($_GET['back']);
        if (isset($_GET['login']) && isset($_GET['password'])) {
            $_POST['login'] = $_GET['login'];
            $_POST['pass'] = $_GET['password'];
        }
        if (isset($_POST['login']) && isset($_POST['pass'])) {
            $logs = getOption('logs');
            $this->load->model('Model_admin', 'ma');
            $user = $this->ma->getUser($_POST['login']);
            if (!$user) {
                set_userdata('login_err', 'Логин либо пароль введены не верно!');
                if ($logs) {
                    $dbins = array(
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'text' => "Пользователь не существует: попытка входа в админку",
                        'ip' => $this->GetRealIp(),
                        'login' => $_POST['login'],
                        'type' => "admin",
                        'error' => "1"
                    );
                    $this->db->insert('logs', $dbins);
                }
                redirect('/admin/');
            } else if ($user['pass'] != md5($_POST['pass'])) {
                set_userdata('login_err', 'Логин либо пароль введены не верно!');
                if ($logs) {
                    $dbins = array(
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'text' => "Не верный пароль: попытка входа в админку. Логин: " . $_POST['login'] . " Пароль: " . $_POST['pass'],
                        'ip' => $this->GetRealIp(),
                        'login' => $_POST['login'],
                        'type' => "admin",
                        'error' => "1"
                    );
                    $this->db->insert('logs', $dbins);
                }
                redirect('/admin/');
            } else if ($user['type'] != 'admin' && $user['type'] != 'moder') {
                set_userdata('login_err', 'У Вас не достаточно прав для доступа в админпанель!');
                if ($logs) {
                    $dbins = array(
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'text' => "Недостаточно прав: попытка входа в админку. Логин: " . $_POST['login'],
                        'ip' => $this->GetRealIp(),
                        'login' => $_POST['login'],
                        'type' => "admin",
                        'error' => "1"
                    );
                    $this->db->insert('logs', $dbins);
                }
                redirect('/admin/');
            } else {
                set_userdata('login', $user['login']);
                set_userdata('pass', $user['pass']);
                set_userdata('type', $user['type']);
                set_userdata('name', $user['login']);
                if ($logs) {
                    $dbins = array(
                        'date' => date("Y-m-d"),
                        'time' => date("H:i"),
                        'text' => "Авторизация в админпанели успешна. Логин: " . $_POST['login'],
                        'ip' => $this->GetRealIp(),
                        'login' => $_POST['login'],
                        'type' => "admin",
                        'error' => "0"
                    );
                    $this->db->insert('logs', $dbins);
                }
                redirect($back);
            }
        } else {
            $data['title'] = "Авторизация";
            $this->load->view('admin/login', $data);
        }
    }

    public function logoff()
    {
        $logs = getOption('logs');
        if ($logs) {
            $dbins = array(
                'date' => date("Y-m-d"),
                'time' => date("H:i"),
                'text' => "Выход из админпанели",
                'ip' => $this->GetRealIp(),
                'login' => userdata("login"),
                'type' => "admin",
                'error' => "0"
            );
            $this->db->insert('logs', $dbins);
        }

        unset_userdata('login');
        unset_userdata('pass');
        unset_userdata('type');
        unset_userdata('name');

        redirect("/admin/");
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */