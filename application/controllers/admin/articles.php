<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Articles extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->helper('admin_helper');
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_articles', 'marticles');
        $this->load->model('Model_categories', 'mcats');
        $this->load->model('Model_options', 'options');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_images', 'images');
    }

    function upload_foto()
    {
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/articles/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/articles/' . date("Y-m-d") . '/', 0777);
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/articles/' . date("Y-m-d");
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();

            $width = $this->options->getOption('article_foto_max_width');
            $height = $this->options->getOption('article_foto_max_height');
            if (!$width) $width = 200;
            if (!$height) $height = 200;

            if (($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
            if (($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];


            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            copy($ret['full_path'], str_replace('/articles/', '/original/', $ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
            $articles_watermark = $this->options->getOption('articles_watermark');
            if ($articles_watermark === false) $articles_watermark = 1;
            if ($articles_watermark) {
                // Получаем файл водяного знака
                $watermark_file = $this->options->getOption('watermark_file');
                if ($watermark_file === false) $watermark_file = 'img/logo.png';
                //
                // Получаем вертикальную позицию водяного знака
                $watermark_vertical_alignment = $this->options->getOption('watermark_vertical_alignment');
                if ($watermark_vertical_alignment === false) $watermark_vertical_alignment = 'bottom';
                // Получаем горизонтальную водяного знака
                $watermark_horizontal_alignment = $this->options->getOption('watermark_horizontal_alignment');
                if ($watermark_horizontal_alignment === false) $watermark_horizontal_alignment = 'center';
                //			   
                // Получаем прозрачность водяного знака
                $watermark_opacity = $this->options->getOption('watermark_opacity');
                if ($watermark_opacity === false) $watermark_opacity = '20';
                //

                $config['source_image'] = $ret["file_path"] . $ret['file_name'];
                $config['create_thumb'] = FALSE;
                $config['wm_type'] = 'overlay';
                $config['wm_opacity'] = $watermark_opacity;
                $config['wm_overlay_path'] = $watermark_file;
                $config['wm_hor_alignment'] = $watermark_horizontal_alignment;
                $config['wm_vrt_alignment'] = $watermark_vertical_alignment;
                $this->image_lib->initialize($config);
                $this->image_lib->watermark();
            }


            return $ret;
        }
    }

    function upload_thumb_foto($file)
    {
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/articles/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/articles/' . date("Y-m-d") . '/', 0777);
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/articles/' . date("Y-m-d");
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file)) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();

            $width = $this->options->getOption('main_image_width');
            $height = $this->options->getOption('main_image_height');
            if (!$width) $width = 200;
            if (!$height) $height = 200;

            if (($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
            if (($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];


            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            copy($ret['full_path'], str_replace('/articles/', '/original/', $ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
            $articles_watermark = $this->options->getOption('articles_watermark');
            if ($articles_watermark === false) $articles_watermark = 1;
            if ($articles_watermark) {
                // Получаем файл водяного знака
                $watermark_file = $this->options->getOption('watermark_file');
                if ($watermark_file === false) $watermark_file = 'img/logo.png';
                //
                // Получаем вертикальную позицию водяного знака
                $watermark_vertical_alignment = $this->options->getOption('watermark_vertical_alignment');
                if ($watermark_vertical_alignment === false) $watermark_vertical_alignment = 'bottom';
                // Получаем горизонтальную водяного знака
                $watermark_horizontal_alignment = $this->options->getOption('watermark_horizontal_alignment');
                if ($watermark_horizontal_alignment === false) $watermark_horizontal_alignment = 'center';
                //			   
                // Получаем прозрачность водяного знака
                $watermark_opacity = $this->options->getOption('watermark_opacity');
                if ($watermark_opacity === false) $watermark_opacity = '20';
                //

                $config['source_image'] = $ret["file_path"] . $ret['file_name'];
                $config['create_thumb'] = FALSE;
                $config['wm_type'] = 'overlay';
                $config['wm_opacity'] = $watermark_opacity;
                $config['wm_overlay_path'] = $watermark_file;
                $config['wm_hor_alignment'] = $watermark_horizontal_alignment;
                $config['wm_vrt_alignment'] = $watermark_vertical_alignment;
                $this->image_lib->initialize($config);
                $this->image_lib->watermark();
            }


            return $ret;
        }
    }

    function upload_image_in_category($file)
    {
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/articles/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/articles/' . date("Y-m-d") . '/', 0777);
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/articles/' . date("Y-m-d");
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file)) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();

            $width = $this->options->getOption('image_in_category_width');
            $height = $this->options->getOption('image_in_category_height');
            if (!$width) $width = 200;
            if (!$height) $height = 200;

            if (($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
            if (($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];


            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            copy($ret['full_path'], str_replace('/articles/', '/original/', $ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
            $articles_watermark = $this->options->getOption('articles_watermark');
            if ($articles_watermark === false) $articles_watermark = 1;
            if ($articles_watermark) {
                // Получаем файл водяного знака
                $watermark_file = $this->options->getOption('watermark_file');
                if ($watermark_file === false) $watermark_file = 'img/logo.png';
                //
                // Получаем вертикальную позицию водяного знака
                $watermark_vertical_alignment = $this->options->getOption('watermark_vertical_alignment');
                if ($watermark_vertical_alignment === false) $watermark_vertical_alignment = 'bottom';
                // Получаем горизонтальную водяного знака
                $watermark_horizontal_alignment = $this->options->getOption('watermark_horizontal_alignment');
                if ($watermark_horizontal_alignment === false) $watermark_horizontal_alignment = 'center';
                //			   
                // Получаем прозрачность водяного знака
                $watermark_opacity = $this->options->getOption('watermark_opacity');
                if ($watermark_opacity === false) $watermark_opacity = '20';
                //

                $config['source_image'] = $ret["file_path"] . $ret['file_name'];
                $config['create_thumb'] = FALSE;
                $config['wm_type'] = 'overlay';
                $config['wm_opacity'] = $watermark_opacity;
                $config['wm_overlay_path'] = $watermark_file;
                $config['wm_hor_alignment'] = $watermark_horizontal_alignment;
                $config['wm_vrt_alignment'] = $watermark_vertical_alignment;
                $this->image_lib->initialize($config);
                $this->image_lib->watermark();
            }


            return $ret;
        }
    }

    function add_image()
    {
        $image = '';
        if (isset($_POST['image'])) $image = $_POST['image'];
        if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки 
            if ($_FILES['userfile']['name'] != '') {
                $imagearr = $this->upload_foto();
                $image = '/upload/articles/' . date("Y-m-d") . '/' . $imagearr['file_name'];
            }
        }

        if ($image) {
            $active = 0;
            if (isset($_POST['active']) && $_POST['active'] == true) $active = 1;
            $show_in_bottom = 0;
            if (isset($_POST['show_in_bottom']) && $_POST['show_in_bottom'] == true) $show_in_bottom = 1;
            $dbins = array(
                'image' => $image,
                'article_id' => $_POST['article_id'],
                'show_in_bottom' => $show_in_bottom,
                'active' => $active
            );

            $this->db->insert('images', $dbins);

            redirect('/admin/articles/edit/' . $_POST['article_id'] . '/#images');
        }
    }

    function edit_image()
    {
        if (isset($_POST['image_id'])) {
            $image = $this->images->getById($_POST['image_id']);
            if (isset($_POST['delete']) && $_POST['delete'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image['image']);
                $this->db->where('id', $image['id']);
                $this->db->delete('images');
            } else {
                if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки 
                    if ($_FILES['userfile']['name'] != '') {
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $image['image']);
                        $imagearr = $this->upload_foto();
                        $image['image'] = '/upload/articles/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                    }
                }
                $image['active'] = 0;
                if (isset($_POST['active']) && $_POST['active'] == true) $image['active'] = 1;
                $image['show_in_bottom'] = 0;
                if (isset($_POST['show_in_bottom']) && $_POST['show_in_bottom'] == true) $image['show_in_bottom'] = 1;

                $this->db->where('id', $image['id']);
                $this->db->update('images', $image);
            }
            redirect('/admin/articles/edit/' . $_POST['article_id'] . '/#images');
        }
    }

    public function index()
    {
        $data['title'] = "Статьи";

        if (isset($_POST['search'])) {
            $data['articles'] = $this->marticles->adminSearch($_POST['search']);

            $data['pager'] = '';
        } else {
            if ($this->session->userdata('category_id') != null)
                $a = $this->marticles->getCountArticlesInCategory($this->session->userdata('category_id'));
            else
                $a = $this->db->count_all('articles');

            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = 35;
            $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/articles/';
            $config['total_rows'] = $a;
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

            if ($page_number > 1) $this->session->set_userdata('articlesFrom', $from);
            else $this->session->unset_userdata('articlesFrom');
            //////////

            if ($this->session->userdata('category_id') != null)
                $data['articles'] = $this->marticles->getArticlesByCategory($this->session->userdata('category_id'), $per_page, $from);
            else
                $data['articles'] = $this->marticles->getArticles($per_page, $from);
        }


        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/articles', $data);
    }

    public function set_category()
    {
        if (isset($_POST['category_id']) && $_POST['category_id'] == 'all') $this->session->unset_userdata('articles_category_id');
        else if (isset($_POST['category_id'])) $this->session->set_userdata('articles_category_id', $_POST['category_id']);
        redirect("/admin/articles/");
    }

    public function category($id)
    {
        $this->session->set_userdata('articles_category_id', $id);
        redirect("/admin/articles/");
    }

    public function add()
    {

        $err = '';
        if (isset($_POST['name']) && $_POST['name'] != '') {
            //if(!$this->marticles->getArticleByName($_POST['name']))
            //{


            $this->load->helper('translit_helper');
            $url = translitRuToEn($_POST['name']);

            $active = 0;
            if (isset($_POST['active']) && $_POST['active'] == true) $active = 1;
            $h1 = '';
            if ($h1 == '') $h1 = $_POST['name'];
            $title = '';
            if ($title == '') $title = $_POST['name'];
            $keywords = '';
            if ($keywords == '') $keywords = $_POST['name'];
            $description = '';
            if ($description == '') $description = $_POST['name'];

            $category_id = '';
            $cat_ids = $_POST['category_id'];
            $ccount = count($cat_ids);
            for ($i = 0; $i < $ccount; $i++) {
                $category_id .= $cat_ids[$i];
                if (($i + 1) < $ccount) $category_id .= '*';
            }

            $youtube = '';
            if ($youtube == '') $youtube = $_POST['youtube'];
            $source = '';
            if (isset($_POST['source'])) $source = $_POST['source'];
            $glavnoe = 0;
            if (isset($_POST['glavnoe']) && $_POST['glavnoe'] == true) $glavnoe = 1;

            $bottom1 = 0;
            if (isset($_POST['bottom1']) && $_POST['bottom1'] == true) $bottom1 = 1;

            $bottom2 = 0;
            if (isset($_POST['bottom2']) && $_POST['bottom2'] == true) $bottom2 = 1;

            $bottom3 = 0;
            if (isset($_POST['bottom3']) && $_POST['bottom3'] == true) $bottom3 = 1;

            $podglavnoe = 0;
            if (isset($_POST['podglavnoe']) && $_POST['podglavnoe'] == true) $podglavnoe = 1;
            $theme = '';
            if (isset($_POST['theme']) && $_POST['theme'] == true) $theme = 1;
            $important = '';
            if (isset($_POST['important']) && $_POST['important'] == true) $important = 1;
            $author = '';
            if (isset($_POST['author']) && $_POST['author'] == true) $author = 1;
            $showintop = '';
            if (isset($_POST['showintop']) && $_POST['showintop'] == true) $showintop = 1;
            $always_first = '';
            if (isset($_POST['always_first']) && $_POST['always_first'] == true) $always_first = 1;
            $mailer = 0;
            if (isset($_POST['mailer']) && $_POST['mailer'] == true) $mailer = 1;
            $show_login = 0;
            if (isset($_POST['show_login']) && $_POST['show_login'] == true) $show_login = 1;
            $social_buttons = 0;
            if (isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) $social_buttons = 1;

            $show_comments = 0;
            if (isset($_POST['show_comments']) && $_POST['show_comments'] == true) $show_comments = 1;

            $image = '';
            if (isset($_POST['image'])) $image = $_POST['image'];
            if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки 
                if ($_FILES['userfile']['name'] != '') {
                    $imagearr = $this->upload_foto();
                    $image = '/upload/articles/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }

            $short_content = '';
            if (isset($_POST['short_content'])) $short_content = $_POST['short_content'];
            $dbins = array(
                'name' => $_POST['name'],
                'url' => $url,
                'category_id' => $category_id,
                'short_content' => $short_content,
                'content' => $_POST['content'],
                'h1' => $h1,
                'image' => $image,
                'num' => $_POST['num'],
                'time' => date("H:i"),
                'date' => date("Y-m-d"),
                'active' => $active,
                'title' => $title,
                'youtube' => $youtube,
                'source' => $source,
                'glavnoe' => $glavnoe,
                'podglavnoe' => $podglavnoe,
                'theme' => $theme,
                'important' => $important,
                'author' => $author,
                'showintop' => $showintop,
                'keywords' => $keywords,
                'description' => $description,
                'robots' => "index, follow",
                'count' => 0,
                'seo' => "",
                'mailer' => $mailer,
                'login' => $this->session->userdata('login'),
                'show_login' => $show_login,
                'social_buttons' => $social_buttons,
                'always_first' => $always_first,
                'show_comments' => $show_comments,
                'bottom1' => $bottom1,
                'bottom2' => $bottom2,
                'bottom3' => $bottom3
            );
            $this->db->insert('articles', $dbins);
            redirect("/admin/articles/");
            //}
            //else $err = 'Такая страница уже существует!';
        }

        $data['mailer_articles_def'] = $this->options->getOption('mailer_articles_def');
        if (!$data['mailer_articles_def'] === false) $data['mailer_articles_def'] = 1;
        $data['article_in_many_categories'] = $this->options->getOption('article_in_many_categories');
        if (!$data['article_in_many_categories'] === false) $data['article_in_many_categories'] = 0;
        //var_dump("asd");die();

        $data['title'] = "Добавление статьи";
        $data['err'] = $err;
        $data['num'] = $this->marticles->getNewNum();
        //$data['articles'] = $this->marticles->getArticles();
        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/articles_add', $data);
    }

    public function edit($id)
    {
        $article = $this->marticles->getArticleById($id);
        if(userdata('type') != 'admin' && $article['need_text'] == 0){
            echo 'У Вас нет доступа для редактирования данного товара!';
            die();
        }

        $err = '';
        if (isset($_POST['name']) && $_POST['name'] != '') {
            $url = $_POST['url'];
            if ($_POST['url'] == '') {
                $this->load->helper('translit_helper');
                $url = translitRuToEn($_POST['name']);
            }

            $active = 0;
            if (isset($_POST['active']) && $_POST['active'] == true) $active = 1;
            $h1 = $_POST['h1'];
            if ($h1 == '') $h1 = $_POST['name'];
            $title = $_POST['title'];
            if ($title == '') $title = $_POST['name'];
            $keywords = $_POST['keywords'];
            if ($keywords == '') $keywords = $_POST['name'];
            $description = $_POST['description'];
            if ($description == '') $description = $_POST['name'];

            //var_dump($_POST['category_id']);die();
            $category_id = '';
            $cat_ids = $_POST['category_id'];
            $ccount = count($cat_ids);
            for ($i = 0; $i < $ccount; $i++) {
                $category_id .= $cat_ids[$i];
                if (($i + 1) < $ccount) $category_id .= '*';
            }
            $youtube = '';
            if ($youtube == '') $youtube = $_POST['youtube'];
            $source = '';
            if ($source == '') $source = $_POST['source'];
            $glavnoe = 0;
            if (isset($_POST['glavnoe']) && $_POST['glavnoe'] == true) $glavnoe = 1;


            $bottom1 = 0;
            if (isset($_POST['bottom1']) && $_POST['bottom1'] == true) $bottom1 = 1;

            $bottom2 = 0;
            if (isset($_POST['bottom2']) && $_POST['bottom2'] == true) $bottom2 = 1;

            $bottom3 = 0;
            if (isset($_POST['bottom3']) && $_POST['bottom3'] == true) $bottom3 = 1;


            $podglavnoe = 0;
            if (isset($_POST['podglavnoe']) && $_POST['podglavnoe'] == true) $podglavnoe = 1;
            $theme = '';
            if (isset($_POST['theme']) && $_POST['theme'] == true) $theme = 1;
            $important = '';
            if (isset($_POST['important']) && $_POST['important'] == true) $important = 1;
            $author = '';
            if (isset($_POST['author']) && $_POST['author'] == true) $author = 1;
            $showintop = '';
            if (isset($_POST['showintop']) && $_POST['showintop'] == true) $showintop = 1;
            $always_first = '';
            if (isset($_POST['always_first']) && $_POST['always_first'] == true) $always_first = 1;
            $mailer = 0;
            if (isset($_POST['mailer']) && $_POST['mailer'] == true) $mailer = 1;
            $show_login = 0;
            if (isset($_POST['show_login']) && $_POST['show_login'] == true) $show_login = 1;
            $social_buttons = 0;
            if (isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) $social_buttons = 1;
            $show_comments = 0;
            if (isset($_POST['show_comments']) && $_POST['show_comments'] == true) $show_comments = 1;

            $image = '';
            if (isset($_POST['image'])) $image = $_POST['image'];
            if (isset($_POST['image_del']) && $_POST['image_del'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                $image = '';

            }
            if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки			   
                if ($_FILES['userfile']['name'] != '') {
                    $imagearr = $this->upload_foto();
                    if ($image != '') unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                    $image = '/upload/articles/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }
            /////////////////////////////////////////////// 

            $image_thumb = '';
            if (isset($_POST['image_thumb'])) $image_thumb = $_POST['image_thumb'];
            if (isset($_POST['image_thumb_del']) && $_POST['image_thumb_del'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image_thumb);
                $image_thumb = '';

            }
            if (isset($_FILES['image_thumb'])) {                    // проверка, выбран ли файл картинки			   
                if ($_FILES['image_thumb']['name'] != '') {
                    $imagearr = $this->upload_thumb_foto('image_thumb');
                    if ($image_thumb != '') unlink($_SERVER['DOCUMENT_ROOT'] . $image_thumb);
                    $image_thumb = '/upload/articles/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }
            //////////////////////////////////////
            $image_in_category = '';
            if (isset($_POST['image_in_category'])) $image_in_category = $_POST['image_in_category'];
            if (isset($_POST['image_in_category_del']) && $_POST['image_in_category_del'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image_in_category);
                $image_in_category = '';

            }
            if (isset($_FILES['image_in_category'])) {                    // проверка, выбран ли файл картинки			   
                if ($_FILES['image_in_category']['name'] != '') {
                    $imagearr = $this->upload_image_in_category('image_in_category');
                    if ($image_in_category != '') unlink($_SERVER['DOCUMENT_ROOT'] . $image_in_category);
                    $image_in_category = '/upload/articles/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }
            $date = $_POST['date'];
            if (!$date || $date == '') $date = date("Y-m-d");
            //////////////////////////////////////////
            $dbins = array(
                'name' => $_POST['name'],
                'url' => $url,
                'category_id' => $category_id,
                'short_content' => $_POST['short_content'],
                'content' => $_POST['content'],
                'h1' => $h1,
                'image' => $image,
                'image_thumb' => $image_thumb,
                'image_in_category' => $image_in_category,
                'num' => $_POST['num'],
                'time' => $_POST['time'],
                'date' => $date,
                'youtube' => $youtube,
                'active' => $active,
                'source' => $source,
                'glavnoe' => $glavnoe,
                'podglavnoe' => $podglavnoe,
                'theme' => $theme,
                'important' => $important,
                'author' => $author,
                'showintop' => $showintop,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'robots' => $_POST['robots'],
                'count' => $_POST['count'],
                'seo' => $_POST['seo'],
                'mailer' => $mailer,
                'show_login' => $show_login,
                'social_buttons' => $social_buttons,
                'always_first' => $always_first,
                'show_comments' => $show_comments,
                'bottom1' => $bottom1,
                'bottom2' => $bottom2,
                'bottom3' => $bottom3
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('articles', $dbins);

            if (isset($_POST['send_about_active']) && $_POST['send_about_active'] == true) {
                $article = $this->marticles->getArticleById($id);
                if ($article) {
                    $user = $this->users->getUserByLogin($article['login']);
                    if ($user) {
                        $this->load->helper('mail_helper');
                        $message = 'Добрый день!<br />
			Ваша статья "<strong>' . $_POST['name'] . '</strong>" успешно добавлена и одобрена администрацией!<br />
			Благодарим Вас за проявленный интерес к нашему сайту!<br /><br />
			С Уважением, Администрация сайта <a href="http://' . $_SERVER['SERVER_NAME'] . '/">' . $_SERVER['SERVER_NAME'] . '</a>';
                        mail_send($user['email'], 'Ваша статья добавлена!', $message);
                    }
                }
            }

            if (isset($_POST['save_and_stay']))
                redirect("/admin/articles/edit/" . $id . "/");
            else
                redirect("/admin/articles/");
        }

        $data['article'] = $article;
        $data['images'] = $this->images->getByArticleId($id);
        $data['title'] = "Редактирование статьи";
        $data['err'] = $err;
        $data['num'] = $this->marticles->getNewNum();
        $data['categories'] = $this->mcats->getCategories();
        //$data['articles'] = $this->marticles->getArticles();
        $this->load->view('admin/articles_edit', $data);
    }

    public function up($id)
    {
        $cat = $this->marticles->getArticleById($id);
        if (($cat) && $cat['num'] > 0) {
            $num = $cat['num'] - 1;
            $oldcat = $this->marticles->getArticleByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('articles', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num + 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('articles', $dbins);
            }
        }
        $url = '/admin/articles/';
        if ($this->session->userdata('articlesFrom') !== false) $url .= $this->session->userdata('articlesFrom') . '/';
        redirect($url);
    }

    public function down($id)
    {
        $cat = $this->marticles->getArticleById($id);
        if (($cat) && $cat['num'] < ($this->marticles->getNewNum() - 1)) {
            $num = $cat['num'] + 1;
            $oldcat = $this->marticles->getArticleByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('articles', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num - 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('articles', $dbins);
            }
        }
        $url = '/admin/articles/';
        if ($this->session->userdata('articlesFrom') !== false) $url .= $this->session->userdata('articlesFrom') . '/';
        redirect($url);
    }

    public function del($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $art = $this->db->get('articles')->result_array();
        if ($art) {
            $art = $art[0];
            if ($art['image'] != '') {
                unlink($_SERVER['DOCUMENT_ROOT'] . $art['image']);
                unlink(str_replace('/articles/', '/original/', $_SERVER['DOCUMENT_ROOT'] . $art['image']));
            }
        }
        $this->db->where('id', $id)->limit(1)->delete('articles');
        $url = '/admin/articles/';
        if ($this->session->userdata('articlesFrom') !== false) $url .= $this->session->userdata('articlesFrom') . '/';
        redirect($url);
    }

    public function active($id)
    {
        $this->ma->setActive($id, 'articles');
        $url = '/admin/articles/';
        if ($this->session->userdata('articlesFrom') !== false) $url .= $this->session->userdata('articlesFrom') . '/';
        redirect($url);
    }

    public function always_first($id)
    {
        $this->ma->setAlwaysFirst($id, 'articles');
        $url = '/admin/articles/';
        if ($this->session->userdata('articlesFrom') !== false) $url .= $this->session->userdata('articlesFrom') . '/';
        redirect($url);
    }
}