<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Export extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_articles','art');
        $this->load->model('Model_shop','shop');
        $this->load->model('Model_categories','cat');
        $this->load->model('Model_vk','vk');        
        $this->load->model('Model_users','users');
        $this->load->library('Vkalbum');
        $this->load->library('Vkmarket');



        if(isset($_GET['from']))
            $_GET['adwords'] = $_GET['from'];

        if(isset($_GET['adwords']))
        {
            set_userdata('adwords', $_GET['adwords']);
            adWordsLog();
            redirect(request_uri(false, true));
        }

        isLogin();
    }

    public function toAlbums(){
        if(isset($_GET['newWin']) && userdata('login') !== false){

        } else {
            unset_userdata('lastProductId');
            $data['title'] = "Экспорт товаров в фотоальбомы ВК (для дропшипперов)" . $this->model_options->getOption('global_title');
            $data['keywords'] = "Экспорт в ВК" . $this->model_options->getOption('global_keywords');
            $data['description'] = "Экспорт в ВК" . $this->model_options->getOption('global_description');
            $data['robots'] = "noindex, nofollow";
            $data['h1'] = "Экспорт товаров в фотоальбомы ВК";
            $data['seo'] = "";
            //if (userdata('login') !== false)
                $this->load->view('export/to_albums.tpl.php', $data);
//            else {
//                $data['msg'] = 'Для начала Вам необходимо <a data-target="#login-logout" data-toggle="modal" class="login-logout" href="#">авторизироваться</a>';
//                $this->load->view('msg.tpl.php', $data);
//            }
        }
    }

    public function toMarket(){
        unset_userdata('lastProductId');
        //unset_userdata('access_token');
        $data['title'] = "Экспорт товаров в группу ВК (для дропшипперов)" . $this->model_options->getOption('global_title');
        $data['keywords'] = "Экспорт в ВК маркет" . $this->model_options->getOption('global_keywords');
        $data['description'] = "Экспорт в ВК маркет" . $this->model_options->getOption('global_description');
        $data['robots'] = "noindex, nofollow";
        $data['h1'] = "Экспорт товаров в группу ВК";
        $data['seo'] = "";
        //if(userdata('login') !== false)
            $this->load->view('export/to_market.tpl.php', $data);
//        else {
//            $data['msg'] = 'Для начала Вам необходимо <a data-target="#login-logout" data-toggle="modal" class="login-logout" href="#">авторизироваться</a>';
//            $this->load->view('msg.tpl.php', $data);
//        }
    }

    public function email_required(){
        $data = array();
        if(isset($_POST['email'])){

            $email = post('email');
            $activation = true;
            $validation = filter_var($email, FILTER_VALIDATE_EMAIL);
            if($validation){
                $values = array(
                    'email' => post('email'),
                    'access_token' => userdata('access_token'),
                    'vk_user_id' => userdata('vk_user_id'),
                    'activation'    => 0
                );
                enter_login($email,$values);
                $data['msg'] = "Проверьте почту!<br />На Ваш электронный адрес высланы инструкции по активации аккаунта";
            } else {
                $data['msg'] = '<div style="text-align: center;" class="form-group">
<div class="error">Проверьте правильность введённого Вами адреса!</div>
                    <form method="post">
                        <input class="my-input" type="email" required name="email" placeholder="Укажите Ваш e-mail" value="'.post('email').'" /><br />
                        <input class="export-button" type="submit" value="Далее" name="add_email" />
                    </form></div>';
            }
        } else {
            $data['msg'] = '<div style="text-align: center;" class="form-group">
<p>Т.к. к Вашей учётной записи в ВК не подкреплён электронный адрес, Вам необходимо указать его для активации Вашего аккаунта.<br /><br/></p>
                    <form method="post">
                        <input class="my-input" type="email" required name="email" placeholder="Укажите Ваш e-mail" /><br />
                        <input class="export-button" type="submit" value="Далее" name="add_email" />
                    </form>
                    </div>';
        }

        $data['title'] = "Требуется подтверждение электронной почты" . $this->model_options->getOption('global_title');
        $data['keywords'] = "Экспорт в ВК маркет" . $this->model_options->getOption('global_keywords');
        $data['description'] = "Экспорт в ВК маркет" . $this->model_options->getOption('global_description');
        $data['robots'] = "noindex, nofollow";
        $data['h1'] = "Требуется подтверждение электронной почты";

        $data['seo'] = "";
        $this->load->view('msg.tpl.php', $data);
    }

    public function test(){

        $ret = $this->vkalbum->get_photo(424813786,253861781);
        vd($ret);


        //$this->vkalbum->authorize();
//        $access_token = '59107b73c4b34a52ebeaeb5ec5252f10bda107764b9de96c4aaafada37790c31b0fc026a2eccd61592298';
//        //$ret = $this->vkalbum->add_vk_album("tes124t");
//        $params = array(
//            'access_token'  => $access_token,
//            'user_id'   => 253861781
//        );
//        $res = new Vkalbum($params);
//        $album_id = 236244360;
//        $img = $res->add_vk_image("/upload/logos/00763e6065b93b750995260d39de9419.jpg",$album_id);
//        vd($img);
    }

    public function index()
    {

        if(isset($_GET['ratingrand']))
        {
            $this->db->where('voitings', 0);
            $articles = $this->db->get('shop')->result_array();
            foreach ($articles as $a) {
                $rand_users = rand(1, 3);
                $rating = array();
                $rsum = 0;
                for ($i = 0; $i < $rand_users; $i++) {
                    $rating[$i] = rand(4, 5);
                    $rsum += $rating[$i];
                }

                $dbins = array(
                    'voitings' => $rand_users,
                    'rating' => $rsum
                );
                $this->db->where('id', $a['id'])->limit(1)->update('shop', $dbins);
            }
        }

        $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);

        $this->load->model('Model_main','main');
        $this->load->helper('menu_helper');

        $tkdzst = $this->main->getMain();
        $data = $tkdzst;

        $data['title']          = $tkdzst['title'];
        $data['keywords']       = $tkdzst['keywords'];
        $data['description']    = $tkdzst['description'];
        $data['robots']         = "index, follow";
        $data['h1']             = $tkdzst['h1'];
        $data['seo']            = $tkdzst['seo'];
        $data['col1']		= $tkdzst['col1'];
        $data['col2']		= $tkdzst['col2'];
        $data['banners']	= $this->banners->getByType('main', 1);

        $isActionNow = isActionNow();
        $data['isActionNow'] = $isActionNow;

        $data['latest'] = $this->shop->getArticlesByCategory(12, 9, 0, 1,'DESC','num',false, false, 1);


        if($isActionNow)
            $data['tab2'] = $this->shop->getActions(9,0);
        else $data['tab2'] = $this->shop->getArticlesByCategory(35, 30, 0, 1,'DESC','num',false, false, 1);

        shuffle($data['tab2']);

        $data['sale'] = $this->shop->getArticlesByCategory(19, 30, 0, 1,'DESC','num',false, false, 1);
        shuffle($data['sale']);
        //$data['important']	    = $this->art->getLastImportant(1, -1, 3);
        $data['articles'] = $this->art->getLastArticles(3, 1);
        //$data['video']	    = $this->art->getGlavnoe(1, -1, -1, 1, 2);
        // var_dump($data['video']);
        //$data

        $template = 'main';
        if(isset($_GET['revslider'])) $template = 'main_new';
        $this->load->view($template, $data);
    }



    public function banner_redirect($id)
    {
        $link = $this->model_banners->getLink($id);
        $this->model_banners->countPlus($id);
        redirect($link);
    }

    public function subscription()
    {
        if(isset($_POST['email']) && $_POST['email'] != '')
        {
            $this->db->where('email', $_POST['email']);
            $this->db->limit(1);
            $subs = $this->db->get('subscription')->result_array();

            if(!$subs)
            {
                $dbins = array(
                    'email'		=> $_POST['email'],
                    'date'		=> date("Y-m-d H:i")
                );

                $this->db->insert('subscription', $dbins);
            }

            $this->session->set_userdata('subscription', true);
        }

        $redirect = '/';
        if($this->session->userdata('last_url') !== false)
            $redirect = $this->session->userdata('last_url');

        redirect($redirect);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */