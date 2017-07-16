<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_main', 'main');

    }

    public function index()
    {
        if (isset($_GET['clear_cache'])) {
            var_dump('cache');
            clearCache();
            echo "Кэш успешно очищен!";
        }

        if (isset($_GET['numbers'])) {
            $this->load->library('image_lib');
            $model = getModel("shop");
            $articles = $model->getArticles();
            foreach ($articles as $article) {

                if ($article['image'] != '' && $article['id_on_image'] == 0) {
                    $config['source_image'] = '.' . $article['image'];
                    $config['wm_text'] = $article['id'];
                    $config['wm_type'] = 'text';
                    $config['wm_font_path'] = './system/fonts/texb.ttf';
                    $config['wm_font_size'] = '42';
                    $config['wm_font_color'] = '842841';
                    $config['wm_vrt_alignment'] = 'bottom';
                    $config['wm_hor_alignment'] = 'left';
                    $config['wm_hor_offset'] = '20';
                    $config['wm_vrt_offset'] = '-20';

                    $this->image_lib->initialize($config);

                    $res = $this->image_lib->watermark();

                    if ($res)
                        $this->db->where('id', $article['id'])->limit(1)->update('shop', array('id_on_image' => 1));

                    //var_dump($res);
                    //echo '<img src="' . $article['image'] . '"/><br/>';
                    //die();
                }


                if ($article['image_no_logo'] != '' && $article['id_on_no_logo_image'] == 0) {
                    $config['source_image'] = '.' . $article['image_no_logo'];
                    $config['wm_text'] = $article['id'];
                    $config['wm_type'] = 'text';
                    $config['wm_font_path'] = './system/fonts/texb.ttf';
                    $config['wm_font_size'] = '42';
                    $config['wm_font_color'] = '842841';
                    $config['wm_vrt_alignment'] = 'bottom';
                    $config['wm_hor_alignment'] = 'left';
                    $config['wm_hor_offset'] = '20';
                    $config['wm_vrt_offset'] = '-20';

                    $this->image_lib->initialize($config);

                    $res = $this->image_lib->watermark();

                    if ($res)
                        $this->db->where('id', $article['id'])->limit(1)->update('shop', array('id_on_no_logo_image' => 1));

                    //var_dump($res);
                    //echo '<img src="' . $article['image'] . '"/><br/>';
                    //die();
                }


                if ($article['image_vk'] != '' && $article['id_on_image_vk'] == 0) {
                    $config['source_image'] = '.' . $article['image_vk'];
                    $config['wm_text'] = $article['id'];
                    $config['wm_type'] = 'text';
                    $config['wm_font_path'] = './system/fonts/texb.ttf';
                    $config['wm_font_size'] = '42';
                    $config['wm_font_color'] = '842841';
                    $config['wm_vrt_alignment'] = 'bottom';
                    $config['wm_hor_alignment'] = 'left';
                    $config['wm_hor_offset'] = '20';
                    $config['wm_vrt_offset'] = '-20';

                    $this->image_lib->initialize($config);

                    $res = $this->image_lib->watermark();

                    if ($res)
                        $this->db->where('id', $article['id'])->limit(1)->update('shop', array('id_on_image_vk' => 1));

//                    var_dump($res);
//                    echo '<img src="' . $article['image'] . '"/><br/>';
//                    die();
                }

                $mImages = getModel('images');
                $images = $mImages->getByShopId($article['id']);
                foreach ($images as $image) {
                    if ($image['id_on_image'] == 0) {
                        $config['source_image'] = '.' . $image['image'];
                        $config['wm_text'] = $article['id'];
                        $config['wm_type'] = 'text';
                        $config['wm_font_path'] = './system/fonts/texb.ttf';
                        $config['wm_font_size'] = '42';
                        $config['wm_font_color'] = '842841';
                        $config['wm_vrt_alignment'] = 'bottom';
                        $config['wm_hor_alignment'] = 'left';
                        $config['wm_hor_offset'] = '20';
                        $config['wm_vrt_offset'] = '-20';

                        $this->image_lib->initialize($config);

                        $res = $this->image_lib->watermark();
                        if ($res)
                            $this->db->where('id', $image['id'])->limit(1)->update('images', array('id_on_image' => 1));
                    }
                }
            }
        }

        if (isset($_GET['set'])) {
            $articles = $this->shop->getArticles();
            foreach ($articles as $article) {
                if (preg_match('/[A-Z]/', $article['url']) == 1) {
                    $url = strtolower($article['url']);
                    vd($article['url']);
                    $this->db->where('id', $article['id'])->limit(1)->update('shop', array("url" => $url));
                    //die();
                }
            }
            $articles = $this->model_categories->getCategories();
            foreach ($articles as $article) {
                if (preg_match('/[A-Z]/', $article['url']) == 1) {
                    vd($article['url']);
                    $url = strtolower($article['url']);
                    $this->db->where('id', $article['id'])->limit(1)->update('categories', array("url" => $url));
                    //die();
                }
            }

            $this->load->model('Model_articles');
            $articles = $this->model_articles->getArticles();
            foreach ($articles as $article) {
                if (preg_match('/[A-Z]/', $article['url']) == 1) {
                    $url = strtolower($article['url']);
                    vd($article['url']);
                    $this->db->where('id', $article['id'])->limit(1)->update('articles', array("url" => $url));
                    //die();
                }
            }

            $this->load->model('Model_menus');
            $articles = $this->Model_menus->getMenus();
            foreach ($articles as $article) {
                if (preg_match('/[A-Z]/', $article['url']) == 1) {
                    $url = strtolower($article['url']);
                    vd($article['url']);
                    $this->db->where('id', $article['id'])->limit(1)->update('menus', array("url" => $url));
                    //die();
                }
            }
        }
        $data['title'] = "Главная";
        $data['main'] = $this->ma->getMain();

        $this->load->view('admin/main', $data);
    }

    public
    function slider()
    {
        $data['title'] = "Слайдер";

        $this->load->view('admin/slider', $data);
    }

    public
    function edit()
    {
        if (isset($_POST['title'])) {
            $dbins = array(
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'keywords' => $_POST['keywords'],
                'h1' => $_POST['h1'],
                'col1' => $_POST['col1'],
                'col2' => $_POST['col2'],
                'col3' => $_POST['col3'],
                'seo' => $_POST['seo'],
                'pagination' => $_POST['pagination']
            );
            $this->db->where('id', 1);
            $this->db->update('main', $dbins);
            redirect('/admin/');

        }


        $data['title'] = "Главная - Редактирование";
        $data['main'] = $this->ma->getMain();
        $this->load->view('admin/main_edit', $data);
    }

    function tkdz()
    {
        $data['title'] = "URLs TKDZ";

        if (isset($_GET['del'])) {
            $this->db->where('id', $_GET['del']);
            $this->db->limit(1);
            $this->db->delete('tkdz');
        }

        if (isset($_POST['add'])) {
            $url = $this->input->post('url');
            $dbins = array(
                'url' => $url,
                'title' => $this->input->post('title'),
                'keywords' => $this->input->post('keywords'),
                'description' => $this->input->post('description'),
                'h1' => $this->input->post('h1'),
                'seo' => $this->input->post('seo'),
                'robots' => $this->input->post('robots'),
                'canonical' => $this->input->post('canonical')
            );

            $old = $this->model_tkdz->getByUrl($url);
            if ($old) {
                $this->db->where('id', $old['id']);
                $this->db->limit(1);
                $this->db->update('tkdz', $dbins);
            } else {
                $this->db->insert('tkdz', $dbins);
            }
        }

        if (isset($_POST['url'])) {
            $data['tkdz'] = $this->model_tkdz->getByUrl($_POST['url']);
        }
        $this->load->view('admin/tkdz', $data);
    }
}