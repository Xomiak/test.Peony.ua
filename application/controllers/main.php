<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Main extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
//        $this->load->driver('cache');

        $this->load->model('Model_articles', 'art');
        $this->load->model('Model_banners', 'banners');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_categories', 'cat');
        $this->load->model('Model_afisha', 'afisha');
        $this->load->model('Model_schedule', 'schedule');
        //$this->load->model('Model_users','users');

        if (isset($_GET['from']))
            $_GET['adwords'] = $_GET['from'];

        if (isset($_GET['adwords'])) {
            set_userdata('adwords', $_GET['adwords']);
            adWordsLog();
            redirect(request_uri(false, true));
        }
        preloader();
        isLogin();
    }


    public function index()
    {
        $cache = $this->config->item('cache');
        $cacheTime = $this->config->item('cache_time');

        $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);

        $this->load->model('Model_main', 'main');
        $this->load->helper('menu_helper');

        $tkdzst = $this->main->getMain();
        $data = $tkdzst;

        $data['title'] = $tkdzst['title'];
        $data['keywords'] = $tkdzst['keywords'];
        $data['description'] = $tkdzst['description'];
        $data['robots'] = "index, follow";
        $data['h1'] = $tkdzst['h1'];
        $data['seo'] = $tkdzst['seo'];
        $data['col1'] = $tkdzst['col1'];
        $data['col2'] = $tkdzst['col2'];
        $data['banners'] = $this->banners->getByType('main', 1);

        $isActionNow = isActionNow();
        $data['isActionNow'] = $isActionNow;

        // Ищем, есть ли кэшированный head для текущего урла
        $headHtml = '';
        if($cache)
            $headHtml = $this->partialcache->get('head-'.cacheUrl(), $cacheTime);
        if(!$headHtml){
            $headHtml = $this->load->view('head_new', $data, true);
            if($cache)
                $this->partialcache->save('head-'.cacheUrl(), $headHtml);
        }

        echo $headHtml;

        $headerHtml = $this->load->view('header_new', $data, true);
        echo $headerHtml;

        $footerNoCached = $this->load->view('footer_no_cached.php', false, true);

        $html = '';
        if($cache)
            $html = $this->partialcache->get(cacheUrl(), 600);
        if(!$html) {

            ob_start();

            $data['latest'] = $this->shop->getArticlesByCategory(12, 9, 0, 1, 'DESC', 'num', false, false, 1);


            if ($isActionNow)
                $data['tab2'] = $this->shop->getActions(9, 0);
            else $data['tab2'] = $this->shop->getArticlesByCategory(35, 30, 0, 1, 'DESC', 'num', false, false, 1);

            shuffle($data['tab2']);

            $data['sale'] = $this->shop->getArticlesByCategory(19, 30, 0, 1, 'DESC', 'num', false, false, 1);
            shuffle($data['sale']);
            //$data['important']	    = $this->art->getLastImportant(1, -1, 3);
            $data['articles'] = $this->art->getLastArticles(3, 1);
            //$data['video']	    = $this->art->getGlavnoe(1, -1, -1, 1, 2);
            // var_dump($data['video']);
            //$data

            $template = 'main';
            //if(isset($_GET['revslider']))
            $template = 'main_new';

            $this->load->view($template, $data);
        } else {
            $html = str_replace('[no_cached]', $footerNoCached, $html);
            echo $html;
        }
    }

    public function price_download()
    {
        $file = getOption('pdf_file');
        $this->load->model('Model_main', 'main');
        $tkdzst = $this->main->getMain();
        $price_downloads_count = $tkdzst['price_downloads_count'] + 1;
        $price_downloads_count_today = $tkdzst['price_downloads_count_today'] + 1;
        $this->db->where('id', 1)->limit(1)->update('main', array('price_downloads_count' => $price_downloads_count, 'price_downloads_count_today' => $price_downloads_count_today));
        redirect($file);
    }

    function file_force_download($file)
    {
        if (file_exists($file)) {
            header('X-Accel-Redirect: ' . $file);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            exit;
        }
    }


    public function banner_redirect($id)
    {
        $link = $this->model_banners->getLink($id);
        $this->model_banners->countPlus($id);
        redirect($link);
    }

    public function subscription()
    {
        if (isset($_POST['email']) && $_POST['email'] != '') {
            $this->db->where('email', $_POST['email']);
            $this->db->limit(1);
            $subs = $this->db->get('subscription')->result_array();

            if (!$subs) {
                $dbins = array(
                    'email' => $_POST['email'],
                    'date' => date("Y-m-d H:i")
                );

                $this->db->insert('subscription', $dbins);
            }

            $this->session->set_userdata('subscription', true);
        }

        $redirect = '/';
        if ($this->session->userdata('last_url') !== false)
            $redirect = $this->session->userdata('last_url');

        redirect($redirect);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */