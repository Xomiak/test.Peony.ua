<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mailer extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_mailer', 'mailer');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_categories', 'categories');
        $this->load->helper('email');
        $this->load->helper('translit');
    }

    function upload_foto($name = 'userfile')
    {                                // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/email';
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($name)) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();
            return $ret;
        }
    }

    public function index()
    {
        if (isset($_GET['auto']) && $_GET['auto'] == 'true') {
            $this->auto();
        } else {
            // Очищаем список рассылки
            if (isset($_GET['clear_mailing']) && $_GET['clear_mailing'] == true) {
                $this->db->update('shop', array('tomail' => 0));
                $data['msg'] = 'Все товары очищены от рассылки!';
            }

            $data['title'] = "Рассылка";

//			$data['mailer_new'] = $this->shop->getForMailer('new');
//			$data['mailer_sale'] = $this->shop->getForMailer('sale');

            //$data['articles'] = $this->shop->getToMail();
            //$data['options'] = $this->mailer->getAllOptions();
            $data['emails'] = $this->mailer->getAll();
            $data['mailer_test'] = getOption('mailer_test');
            $this->load->view('admin/mailer', $data);
        }
    }

    public function edit($id)
    {
        $err = false;
        $mailer = $this->mailer->getById($id);
        $users = $this->users->getUsers();

        if(isset($_POST['name']))
        {
            $header = $_POST['header'];
            if (isset($_FILES['header'])) {					// проверка, выбран ли файл картинки
                if ($_FILES['header']['name'] != '') {
                    $imagearr = $this->upload_foto('header');
                    $header = '/upload/email/'.$imagearr['file_name'];
                    $this->mailer->setOption('header', $header);
                }
            }

            if($name != $_POST['name'])
            {
                $this->mailer->setOption('name', $_POST['name']);
                $name = $_POST['name'];
            }

            if($content != $_POST['content'])
            {
                $this->mailer->setOption('content', $_POST['content']);
                $content = $_POST['content'];
            }

            if($footer != $_POST['footer'])
            {
                $this->mailer->setOption('footer', $_POST['footer']);
                $footer = $_POST['footer'];
            }

            if($adding != $_POST['adding'])
            {
                $this->mailer->setOption('adding', $_POST['adding']);
                $adding = $_POST['adding'];
            }

            $no_price = 0;
            if(isset($_POST['no_price']) && $_POST['no_price'] == true) $no_price = 1;
            //$email_content = $this->createEmail($header,$name,$content,$articles,$adding,$footer);

            // ПРоверяем и сохраняем изминения в описаниях товаров

            $articles = unserialize($mailer['articles']);
            if($articles)
            {
                $count = count($articles);
                for($i = 0; $i < $count; $i++)
                {
                    if(isset($_POST['content_'.$articles[$i]['id']]) && $articles[$i]['content'] != $_POST['content_'.$articles[$i]['id']])
                        $articles[$i]['content'] = $_POST['content_'.$articles[$i]['id']];
                }
            }


            // Индивидуальная рассылка
            if(isset($_POST['emails_list_on']) && $_POST['emails_list_on'] == true)
            {
                $emails_list = $_POST['emails_list'];
            }
            else
                $emails_list = NULL;

            $dbins = array(
                'date'			=> date("Y-m-d"),
                'time'			=> date("H:i"),
                'name'			=> $name,
                'header'			=> $header,
                'content'      	=> $content,
                'footer'			=> $footer,
                'adding'			=> $adding,
                'articles'		=> serialize($articles),
                'no_price'		=> $no_price,
                'emails_list'	=> serialize($emails_list)
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('mailer',$dbins);

            redirect('/admin/mailer/');

        }

        $data['userTypes'] = $this->users->getUserTypes(1);
        $data['articles'] = unserialize($mailer['articles']);
        $data['users']	= $users;
        $data['emails_list'] = unserialize($mailer['emails_list']);
        $data['header'] = $mailer['header'];
        $data['name']	= $mailer['name'];
        $data['content']	= $mailer['content'];
        $data['adding']	= $mailer['adding'];
        $data['footer']	= $mailer['footer'];
        $data['title']  = "Редактирование рассылки";
        $data['edit']	= true;
        $data['err'] = $err;

        $this->load->view('admin/mailer_edit',$data);
    }

    public function auto()
    {
        // Отправка тестового письма
        if (isset($_POST['sale_test'])) {
            $mailer_sale_header = getOption('mailer_sale_header');
            $mailer_sale_subject = getOption('mailer_sale_subject');
            $mailer_sale_template = getOption('mailer_sale_template');
            $products = $this->shop->getForMailer('sale');
            $user_name = "Администратор";
            $content = str_replace('[name]', $user_name, $mailer_sale_template);
            $message = createEmail($mailer_sale_header, $mailer_sale_subject, $content, $products);
            //vdd($message);
            $this->load->helper('mail_helper');
            mail_send(post('email'), $mailer_sale_subject, $message);

            redirect('/admin/mailer/?auto=true&sale_test=sended');
        }

        /////////////////////

        if (isset($_GET['clear'])) {
            $this->db->update('shop', array("mailer_" . $_GET['type'] => 0));
            redirect('/admin/mailer/?auto=true');
        }

        if (isset($_GET['clear_queue']) && $_GET['clear_queue'] == true) {
            $this->db->where('complete', 0);
            $this->db->delete('mailer_cron');
            redirect('/admin/mailer/?auto=true');
        }

        $mailer_history = false;
        if (isset($_GET['history']) && $_GET['history'] == 'true') {
            set_userdata('mailer_history', true);
        } elseif (isset($_GET['history']) && $_GET['history'] == 'false') {
            unset_userdata('mailer_history');
        }
        if (userdata('mailer_history') === true)
            $mailer_history = true;

        $data['title'] = "Рассылка";

        $data['mailer_new'] = $this->shop->getForMailer('new');
        $data['mailer_sale'] = $this->shop->getForMailer('sale');

        // АВТОРАССЫЛКА
        $complete = 0;
        if ($mailer_history) $complete = 1;

        // ПАГИНАЦИЯ //
        $this->load->library('pagination');
        $per_page = 100;
        $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/mailer/';
        $config['suffix'] = '/?auto=true';
        $config['total_rows'] = $this->mailer->getQueueMailCount($complete);
        $config['num_links'] = 4;
        $config['first_link'] = 'в начало';
        $config['last_link'] = 'в конец';
        $config['next_link'] = 'далее';
        $config['prev_link'] = 'назад';

        $config['per_page'] = $per_page;
        $config['uri_segment'] = 3;
        $from = intval($this->uri->segment(3));
        $page_number = $from / $per_page + 1;
        $this->pagination->initialize($config);
        $data['pager'] = $this->pagination->create_links();

        $data['queue'] = $this->mailer->getMailerQueue($per_page, $from, $complete);

        $data['queueCount'] = $this->mailer->getQueueMailCount();
        $data['sendedCount'] = $this->mailer->getSendedMailCount(date("Y-m-d"));
        $data['complete'] = $complete;

        //$data['articles'] = $this->shop->getToMail();
        //$data['options'] = $this->mailer->getAllOptions();
//		$data['emails']	= $this->mailer->getAll();
//		$data['mailer_test'] = getOption('mailer_test');
        $this->load->view('admin/mailer_auto', $data);
    }

    public function add()
    {
        $type = false;
        if (isset($_GET['type'])) $type = $_GET['type'];

        if ($type) $header = getOption('mailer_' . $type . '_header');
        else $header = $this->mailer->getOption('header');

        if ($type) $name = getOption('mailer_' . $type . '_subject');
        else $name = $this->mailer->getOption('name');

        $content = $this->mailer->getOption('content');
        $footer = $this->mailer->getOption('footer');
        $adding = $this->mailer->getOption('adding');
        $articles = false;


        if ($type)
            $articles = $this->shop->getForMailer($type);
        else
            $articles = $this->shop->getToMail();

        if (isset($_POST['name'])) {
            if (isset($_FILES['header'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['header']['name'] != '') {
                    $imagearr = $this->upload_foto('header');
                    $header = '/upload/email/' . $imagearr['file_name'];
                    if (!$type) $this->mailer->setOption('header', $header);
                }
            }

            if ($name != $_POST['name']) {
                if (!$type) $this->mailer->setOption('name', $_POST['name']);
                $name = $_POST['name'];
            }

            if ($content != $_POST['content']) {
                if (!$type) $this->mailer->setOption('content', $_POST['content']);
                $content = $_POST['content'];
            }

            if ($footer != $_POST['footer']) {
                if (!$type) $this->mailer->setOption('footer', $_POST['footer']);
                $footer = $_POST['footer'];
            }

            if ($adding != $_POST['adding']) {
                if (!$type) $this->mailer->setOption('adding', $_POST['adding']);
                $adding = $_POST['adding'];
            }

            //$email_content = $this->createEmail($header,$name,$content,$articles,$adding,$footer);

            $no_price = 0;
            if (isset($_POST['no_price']) && $_POST['no_price'] == true) $no_price = 1;

            $dbins = array(
                'date' => date("Y-m-d"),
                'time' => date("H:i"),
                'name' => $name,
                'header' => $header,
                'content' => $content,
                'footer' => $footer,
                'adding' => $adding,
                'articles' => serialize($articles),
                'no_price' => $no_price
            );
            $this->db->insert('mailer', $dbins);


            redirect('/admin/mailer/');

        }

        $data['header'] = $header;
        $data['name'] = $name;
        $data['content'] = $content;
        $data['adding'] = $adding;
        $data['footer'] = $footer;
        $data['edit'] = false;

        $data['title'] = "Создание рассылки";
        $data['articles'] = $articles;


        $this->load->view('admin/mailer_add', $data);
    }

    public function sms()
    {
        if(isset($_GET['test'])){
            $this->load->helper('sms_helper');
            $tel = '0038(093) 4901286';
            $tel = preg_replace('/[^0-9]/', '', $tel);

            $text = 'Дорогие дамы! Компания PEONY поздравляет Вас с праздником и дарит праздничную скидку 10%! Промокод: WOMANSDAY. Детали на нашем сайте peony.ua';
            $ret = sms_send($tel, $text);
            vd($ret->ResultArray);
            die();
        }
        $data['mailer_sms'] = $this->mailer->getAllSmsMailers();

        $data['title'] = 'SMS рассылки';
        $this->load->view('admin/sms_mailer', $data);
    }

    public function sms_add()
    {
        //$data['sms'] = $this->mailer->getSmsMailer();
        if (isset($_POST)) {
            $start = post('start');
            $start_unix = 0;
            $arr = explode(' ', $start);
            if (is_array($arr)) {
                $darr = explode('-', $arr[0]);
                if (count($darr) == 3) {
                    $start_unix = mktime($arr[1], 0, 0, $darr[1], $darr[2], $darr[0]);
                }
            }
            if ($start_unix > 0) {

                $dbins = array(
                    'name' => post('name'),
                    'text' => post('text'),
                    'start' => $start,
                    'start_unix' => $start_unix
                );
                $this->db->insert('sms_mailers', $dbins);


                if (isset($_POST['add_sms_cron']) && $_POST['add_sms_cron'] == true) {
                    $this->db->where('name', post('name'));
                    $this->db->where('text', post('text'));
                    $this->db->where('start', $start);
                    $this->db->where('start_unix', $start_unix);
                    $sms = $this->db->get('sms_mailers')->result_array();
                    if (isset($sms[0])) {
                        $sms = $sms[0];
                        $this->load->model('Model_users', 'users');
                        $users = $this->users->getSmsUsers();
                        foreach ($users as $user) {
                            $text = str_replace('[name]', $user['name'], $sms['text']);
                            $dbins = array(
                                'sms_id' => $sms['id'],
                                'tel' => $user['tel'],
                                'text' => $text
                            );
                            $this->db->insert('sms_mailers_cron', $dbins);
                        }
                    }
                    $this->db->where('id', $sms['id'])->limit(1)->update('sms_mailers', array('status' => 'started'));
                }


                redirect('/admin/mailer/sms/');
            }
        }
        $data['title'] = 'Создание SMS рассылки';
        $data['action'] = 'add';
        $this->load->view('admin/sms_mailer_add_edit', $data);
    }

    public function sms_edit($id)
    {
        $sms = $this->mailer->getSmsMailerById($id);
        if (isset($_GET['start_now']) && $_GET['start_now'] == true) {
            $this->send_sms_now($sms);
        }

        if (isset($_GET['add_sms_cron']) && $_GET['add_sms_cron'] == true) {
            $this->add_sms_cron($sms);
            redirect('/admin/mailer/sms/');
        }

        if (isset($_GET['restart']) && $_GET['restart'] == true) {
            $this->db->where('id', $id)->limit(1)->update('sms_mailers', array('status'=>'new'));
            redirect('/admin/mailer/sms/');
        }

        if (isset($_POST)) {
            $start = post('start');
            $start_unix = 0;
            $arr = explode(' ', $start);
            if (is_array($arr)) {
                $darr = explode('-', $arr[0]);
                if (count($darr) == 3) {
                    $start_unix = mktime($arr[1], 0, 0, $darr[1], $darr[2], $darr[0]);
                }
            }
            if ($start_unix > 0) {

                $dbins = array(
                    'name' => post('name'),
                    'text' => post('text'),
                    'start' => $start,
                    'start_unix' => $start_unix
                );
                $this->db->where('id', $id)->limit(1)->update('sms_mailers', $dbins);


                if (isset($_POST['add_sms_cron']) && $_POST['add_sms_cron'] == true)
                    $this->add_sms_cron($sms);


                redirect('/admin/mailer/sms/');
            }
        }
        $data['sms'] = $sms;
        $data['title'] = 'Редактирование SMS рассылки';
        $data['action'] = 'edit';
        $this->load->view('admin/sms_mailer_add_edit', $data);
    }

    private function add_sms_cron($sms){
        $this->load->model('Model_users', 'users');
        $users = $this->users->getSmsUsers();
        foreach ($users as $user) {
            $user['tel'] = preg_replace('/[^0-9]/', '', $user['tel']);
            if(mb_strlen($user['tel']) > 8) {
                $text = str_replace('[name]', $user['name'], $sms['text']);
                $dbins = array(
                    'sms_id' => $sms['id'],
                    'tel' => $user['tel'],
                    'text' => $text
                );
                $this->db->insert('sms_mailers_cron', $dbins);
            }
        }
        $this->db->where('id', $sms['id'])->limit(1)->update('sms_mailers', array('status' => 'started'));
    }

    private function send_sms_now($sms){
        $this->load->model('Model_users', 'users');
        $this->load->helper('sms_helper');
        
        $users = $this->users->getSmsUsers();
        foreach ($users as $user) {
            $user['tel'] = preg_replace('/[^0-9]/', '', $user['tel']);
            if(mb_strlen($user['tel']) > 8) {
                $text = str_replace('[name]', $user['name'], $sms['text']);
                $result = sms_send($sms['tel'], $sms['text']);
                echo 'Отправка SMS на номер '.$user['tel'].' ('.$user['name'].' '.$user['lastname'].') Статус отправки: '.$result->ResultArray[1].'<br />';
            }
        }
        $this->db->where('id', $sms['id'])->limit(1)->update('sms_mailers', array('status' => 'complete'));
    }


    public function sms_send($id)
    {
        $this->load->helper('sms_helper');
    }

    function send_new($id)
    {
        $data['title'] = "Запуск рассылки";
        $data['mailer'] = $this->mailer->getById($id);
        $this->load->view('admin/mailer_send_new', $data);
    }

    function send($id)
    {
        $this->load->helper('mail_helper');

        $type = false;
        $mailer_test = getOption('mailer_test');
        if ($mailer_test == 0) $mailer_test = false; else $mailer_test = true;

        if ($mailer_test) $type = 'admin';

        $mailer = $this->mailer->getById($id);


        if ($mailer['status'] == 'Выполнено') {
            echo '<h1>Рассылка успешно отправлена!</h1>' . $mailer['log'];
        } else {
            $emails_list = unserialize($mailer['emails_list']);


            if ($emails_list == NULL) {
                $users = $this->users->getUsersByType(-1, -1, 'ASC', $type, 'id', 1);
                //vd($users);
                $emails_count = 0;
                $count = count($users);
                for ($i = 0; $i < $count; $i++) {
                    if (valid_email($users[$i]['email'])) {
                        $emails_list[$emails_count] = $users[$i]['email'];
                        $emails_count++;
                    }
                }

            }


            $message = createEmail($mailer['header'], $mailer['name'], $mailer['content'], unserialize($mailer['articles']), $mailer['adding'], $mailer['footer'], $mailer['no_price']);


            $mailer_one_step_mails_count = 2;
            $mailer_one_step_mails_count = getOption('mailer_one_step_mails_count');
            $log = $mailer['log'];

            $sended_count = $mailer['sended_count'];

            $emails_count = $mailer['emails_count'];
            if ($sended_count == 0)
                $emails_count = count($emails_list);


            echo '<h1>В процессе (' . $sended_count . ' из ' . $emails_count . ')</h1>';
            $fin = false;
            for ($i = 0; $i < $mailer_one_step_mails_count; $i++) {
                if (isset($emails_list[$i])) {
                    echo 'Отправка на: ' . $emails_list[$i] . '...';

                    $isSended = false;
                    $isSended = mail_send($emails_list[$i], $mailer['name'], $message, "html", $mailer_test);
                    if ($isSended) {
                        echo ' успешно<br />';
                        $log .= 'Отправка на: ' . $emails_list[$i] . '... успешно<br />';
                    } else {
                        echo '<span style="color:red"> ошибка</span><br />';
                        $log .= 'Отправка на: ' . $emails_list[$i] . '... <span style="color:red"> ошибка</span><br />';
                    }

                } else {
                    $fin = true;
                    break;
                }

                $sended_count++;
            }

            $sended = array_splice($emails_list, 0, $mailer_one_step_mails_count);

            if ($fin == true || !$emails_list || $emails_list == null) {
                $dbins['status'] = 'Выполнено';
            } else {
                $dbins['status'] = 'В процессе (' . $sended_count . ' из ' . $emails_count . ')';
            }

            $dbins['sended_count'] = $sended_count;
            $dbins['emails_count'] = $emails_count;
            $dbins['log'] = $log;


            $dbins['emails_list'] = serialize($emails_list);

            //vdd($dbins);


            $this->db->where('id', $id);
            $this->db->update('mailer', $dbins);

            $next = "http://" . $_SERVER['SERVER_NAME'] . '/admin/mailer/send/' . $id . '/';
            ?>
            <script language='javascript'>
                var delay = 5000;
                setTimeout("document.location.href='<?=$next?>'", delay);
            </script>

            <?php
        }
        // vdd($emails_list);

        //$users = $this->users->getUsers($start, $mailer_one_step_mails_count, 'ASC', $type);

        // СДЕЛАТЬ EMAILS_LIST БУФЕРОМ
        // Отправил, удалил из массива и базы
        //
        // также, в отображении


        //sleep(15);
        //redirect('/admin/mailer/send/'.$id.'/?start='.$last_id);


    }

    function show($id)
    {
        $this->load->helper('mail_helper');
        $mailer = $this->mailer->getById($id);
        echo createEmail($mailer['header'], $mailer['name'], $mailer['content'], unserialize($mailer['articles']), $mailer['adding'], $mailer['footer'], $mailer['no_price']);
    }

    function queueMessage($id)
    {
        $queue = $this->mailer->getQueueById($id);
        if (isset($queue['message'])) echo $queue['message'];
    }


    public function del($id)
    {
        $this->db->where('id', $id)->limit(1)->delete('mailer');
        redirect("/admin/mailer/");
    }

    public function reset($id)
    {
        $this->db->where('id', $id)->limit(1)->update('mailer', array('last_sended_user_id' => 0, 'status' => 'Перезапущен'));
        redirect("/admin/mailer/");
    }


}