<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        $this->load->model('Model_articles', 'art');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_categories', 'categories');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_gallery', 'gallery');
        $this->load->model('Model_comments', 'comments');
        $this->load->model('Model_spambot', 'spambot');
        //$this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
    }

    public function index()
    {
        err404();
        $this->load->model('Model_main', 'main');
        $this->load->helper('menu_helper');
        $tkdzst = $this->main->getMain();
        $data['title'] = $tkdzst['title'];
        $data['keywords'] = $tkdzst['keywords'];
        $data['description'] = $tkdzst['description'];
        $data['robots'] = "index, follow";
        $data['h1'] = $tkdzst['h1'];
        $data['seo'] = $tkdzst['seo'];
        $data['glavnoe'] = $this->art->getGlavnoe();
        $this->load->view('main', $data);
    }

    function upload_foto($name = 'userfile1', $type = 'reviews')
    {
        $CI = &get_instance();
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $type . '/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $type . '/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/' . $type . '/' . date("Y-m-d");
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $CI->load->library('upload', $config);

        if (!$CI->upload->do_upload($name)) {
            echo $CI->upload->display_errors();
            die();
        } else {
            $ret = $CI->upload->data();

            $width = 800;
            $height = 800;

            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $CI->image_lib->initialize($config);
            $CI->image_lib->resize();

            //copy($ret['full_path'],str_replace('/articles/','/original/',$ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
//            $articles_watermark = getOption('articles_watermark');
//            /*if ($articles_watermark === false)*/ $articles_watermark = 1;
//            if ($articles_watermark) {
//                // Получаем файл водяного знака
//                $watermark_file = getOption('watermark_file');
//                if ($watermark_file === false) $watermark_file = 'img/logo.png';
//                //
//                // Получаем вертикальную позицию водяного знака
//                $watermark_vertical_alignment = getOption('watermark_vertical_alignment');
//                if ($watermark_vertical_alignment === false) $watermark_vertical_alignment = 'bottom';
//                // Получаем горизонтальную водяного знака
//                $watermark_horizontal_alignment = getOption('watermark_horizontal_alignment');
//                if ($watermark_horizontal_alignment === false) $watermark_horizontal_alignment = 'center';
//                //
//                // Получаем прозрачность водяного знака
//                $watermark_opacity = getOption('watermark_opacity');
//                if ($watermark_opacity === false) $watermark_opacity = '20';
//                //
//
//                $config['source_image'] = $ret["file_path"] . $ret['file_name'];
//                $config['create_thumb'] = FALSE;
//                $config['wm_type'] = 'overlay';
//                $config['wm_opacity'] = $watermark_opacity;
//                $config['wm_overlay_path'] = $watermark_file;
//                $config['wm_hor_alignment'] = $watermark_horizontal_alignment;
//                $config['wm_vrt_alignment'] = $watermark_vertical_alignment;
//                $CI->image_lib->initialize($config);
//                $CI->image_lib->watermark();
//            }


            return $ret;
        }
    }

    public function add()
    {
        vd($_FILES['userfile1']['name']);
        vd($_FILES['userfile2']['name']);
        //die();
        if ($this->input->post('comment') !== false && userdata('login') !== false) {
            $this->load->model('Model_users', 'users');
            $user = $this->users->getUserByLogin(userdata('login'));
            if ($user) {
                $rate = $this->input->post('rate');
                $shop_id = 0;
                $article_id = 0;
                if ($this->input->post('shop_id') != false) $shop_id = $this->input->post('shop_id');
                if ($this->input->post('article_id') != false) $article_id = $this->input->post('article_id');
                $images = '';
                $images = array();
                $filesCount = 1;
                while (isset($_FILES['userfile' . $filesCount]) && $_FILES['userfile' . $filesCount]['name'] != '') {
                    $imagearr = $this->upload_foto('userfile' . $filesCount);
                    $image = '/upload/reviews/'.date("Y-m-d").'/'.$imagearr['file_name'];

                    if ($image)
                        array_push($images, $image);

                    $filesCount++;
                }
               // vdd($images);
                $images = json_encode($images);
                //alert($images);


                if ($rate < 1) $rate = 5;
                $dbins = array(
                    'comment' => $this->input->post('comment'),
                    'rate' => $rate,
                    'login' => userdata('login'),
                    'name' => $user['name'],
                    'shop_id' => $shop_id,
                    'article_id' => $article_id,
                    'user_id' => $user['id'],
                    'ip' => getRealIp(),
                    'date' => date("Y-m-d"),
                    'time' => date("H:i"),
                    'active' => 0,
                    'images' => $images
                );

                $this->db->insert('comments', $dbins);

                set_userdata('msg', "Большое спасибо за Ваш отзыв! Он появится на сайте после проверки.");
            } 
        }
        if (isset($_POST['back'])) {
            redirect($_POST['back']);
        }
    }

    public function answer($id)
    {
        $comment = $this->comments->getCommentById($id);
        if ($comment) {
            $this->session->set_userdata('commentAnswer', $comment);
        }

        if ($this->session->userdata('last_url') !== false) {
            redirect($this->session->userdata('last_url') . '#add_comment_form');
        }
    }
}