<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shop extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_shop', 'mshop');
        $this->load->model('Model_categories', 'mcats');
        $this->load->model('Model_options', 'options');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_images', 'images');
    }


    function upload_foto($filename = 'userfile', $watermark_text = false)
    {
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/', 0777);
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/shop/' . date("Y-m-d");
        $config['allowed_types'] = 'jpg|png|gif|jpe';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['encrypt_name'] = true;
        $config['overwrite'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($filename)) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();
            if (!$ret) return false;

            copy($ret["file_path"] . $ret['file_name'], $_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/' . $ret['file_name']);

            $width = $this->options->getOption('article_foto_max_width');
            $height = $this->options->getOption('article_foto_max_height');
            if (!$width) $width = 200;
            if (!$height) $height = 200;

            if (($ret['image_width'] != '') && $ret['image_width'] < $width) $width = $ret['image_width'];
            if (($ret['image_height'] != '') && $ret['image_height'] < $height) $height = $ret['image_height'];


            $config['image_library'] = 'GD2';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 1500;
            $config['height'] = 1500;
            $config['source_image'] = $ret["file_path"] . $ret['file_name'];
            $config['new_image'] = $ret["file_path"] . $ret['file_name'];
            $config['thumb_marker'] = '';
            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            if ($watermark_text) {
                $config['source_image'] = $ret["file_path"] . $ret['file_name'];
                $config['wm_text'] = $watermark_text;
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
            }

            return $ret;
        }
    }

    function upload_thumb_foto($file)
    {
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/', 0777);
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/shop/' . date("Y-m-d");
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

            //copy($ret['full_path'],str_replace('/shop/','/original/',$ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
            $article_watermark = $this->options->getOption('article_watermark');
            if ($article_watermark === false) $article_watermark = 1;
            if ($article_watermark) {
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
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/', 0777);
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/original/' . date("Y-m-d") . '/', 0777);
        }

        //////
        // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/shop/' . date("Y-m-d");
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

            copy($ret['full_path'], str_replace('/shop/', '/original/', $ret['full_path']));

            // Проверяем нужен ли водяной знак на картинках в статьях
            $article_watermark = $this->options->getOption('article_watermark');
            if ($article_watermark === false) $article_watermark = 1;
            if ($article_watermark) {
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
                $imagearr = $this->upload_foto('userfile', $_POST['article_id']);
                $image = '/upload/shop/' . date("Y-m-d") . '/' . $imagearr['file_name'];
            }
        }

        if ($image) {
            $active = 0;
            if (isset($_POST['active']) && $_POST['active'] == true) $active = 1;
            $show_in_bottom = 0;
            if (isset($_POST['show_in_bottom']) && $_POST['show_in_bottom'] == true) $show_in_bottom = 1;

            $num = $this->images->getNewNumForShop($_POST['article_id']);

            $dbins = array(
                'image' => $image,
                'shop_id' => $_POST['article_id'],
                'show_in_bottom' => $show_in_bottom,
                'active' => $active,
                'num' => $num
            );
            $article['id'] = post('article_id');

            $this->db->insert('images', $dbins);


            redirect('/admin/shop/edit/' . $_POST['article_id'] . '/#images');
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
                        $image['image'] = '/upload/shop/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                    }
                }
                $image['active'] = 0;
                $image['num'] = $_POST['num'];
                if (isset($_POST['active']) && $_POST['active'] == true) $image['active'] = 1;
                $image['show_in_bottom'] = 0;
                if (isset($_POST['show_in_bottom']) && $_POST['show_in_bottom'] == true) $image['show_in_bottom'] = 1;

                $this->db->where('id', $image['id']);
                $this->db->update('images', $image);
            }
            redirect('/admin/shop/edit/' . $_POST['article_id'] . '/#images');
        }
    }

    public function index()
    {
        if (isset($_GET['search_links'])) {
            $this->db->like('content', '<a ');
            $articles = $this->db->get('shop')->result_array();
            vd(count($articles));
            foreach ($articles as $a) {
                $cat = $this->model_categories->getCategoryById($a['category_id']);
                echo "http://peony.ua/" . $cat['url'] . '/' . $a['url'] . '/<br/>';
                echo '<a href="/admin/shop/edit/' . $a['id'] . '/" target="_blank">' . $a['name'] . ' (' . $a['color'] . ')</a>:<br /> ';
                vd($a['content']);
                echo '<hr/>';
            }
        }

        if (isset($_GET['sort_by']) && isset($_GET['order_by'])) {
            set_userdata('sort_by', $_GET['sort_by']);
            set_userdata('order_by', $_GET['order_by']);
            redirect('/admin/shop/');
        }

        ///// FILTERS /////
        $filters = false;
        // articul part
        if (isset($_POST['filter_articul'])) {
            if ($_POST['filter_articul'] != '')
                set_userdata('filter_articul', $_POST['filter_articul']);
            else unset_userdata('filter_articul');
            redirect('/admin/shop/');
        }
        if (userdata('filter_articul')) $filters['articul'] = userdata('filter_articul');

        // season
        if (isset($_POST['filter_season'])) {
            if ($_POST['filter_season'] != '')
                set_userdata('filter_season', $_POST['filter_season']);
            else unset_userdata('filter_season');
            redirect('/admin/shop/');
        }
        if (userdata('filter_season')) $filters['season'] = userdata('filter_season');

        // discount
        if (isset($_POST['filter_discount'])) {
            if ($_POST['filter_discount'] != '')
                set_userdata('filter_discount', $_POST['filter_discount']);
            else unset_userdata('filter_discount');
            redirect('/admin/shop/');
        }
        if (userdata('filter_discount')) $filters['discount'] = userdata('filter_discount');

        ///// FILTERS END /////

        $this->load->model('Model_comments', 'comments');
        $i = 0;
        if (isset($_GET['no_reviews_links'])) {
            $shop = $this->mshop->getArticles(100, 0, 'DESC', 1);
            foreach ($shop as $item) {
                $c = $this->comments->getCommentsToShop($item['id']);
                if (!$c) {
                    $i++;
                    echo "http://" . $_SERVER['SERVER_NAME'] . getFullUrl($item) . '<br />';
                }
            }

        }

        if (isset($_GET['settitles'])) {
            $articles = $this->mshop->getArticles();
            $usd_to_uah = getCurrencyValue('UAH');
            foreach ($articles as $article) {
                $cat = $this->model_categories->getCategoryById($article['category_id']);
                $price = $article['price'] * $usd_to_uah;
                $price = round($price, 2);
                $title = $article['name'] . ' (' . $article['color'] . ') - ' . $price . ' грн. Купить ' . $cat['name'];
                $description = 'Купить ' . $cat['name'] . ' ' . $article['name'] . ' (' . $article['color'] . ') - ' . $price . ' грн';
                $dbins = array(
                    'title' => $title,
                    'description' => $description
                );
                $this->db->where('id', $article['id'])->limit(1)->update('shop', $dbins);
            }
        }

        if (isset($_GET['action']) && (isset($_GET['type'])) && $_GET['action'] == 'all') {
            $type = $_GET['type'];

            $dbins = array(
                'mailer_' . $type => 0
            );
            $this->db->update('shop', $dbins);
            //redirect('/admin/shop');
        }

        if (isset($_GET['comments'])) {
            $this->load->model('model_comments', 'comments');


            $articles = $this->mshop->getArticles();
            $count = count($articles);
            for ($i = 0; $i < $count; $i++) {
                $dbins = array(
                    'rating' => 0,
                    'voitings' => 0
                );
                $a = $articles[$i];
                $comments = $this->comments->getCommentsToShop($a['id']);

                if ($comments) {
                    $rating = 0;
                    $voitings = 0;

                    foreach ($comments as $c) {
                        $dbins['rating'] = $dbins['rating'] + $c['rate'];
                        $dbins['voitings'] = $dbins['voitings'] + 1;
                    }
                }

                $this->db->where('id', $a['id']);
                $this->db->limit(1);
                $this->db->update('shop', $dbins);

                echo $a['id'] . ' - ' . $dbins['voitings'] . '<br />';
            }
        }
        $data['title'] = "Товары";

        if (isset($_POST['search'])) {
            $data['shop'] = $this->mshop->adminSearch($_POST['search']);
            //vd($data['shop']);

            $data['pager'] = '';
        } else {
            if ($this->session->userdata('shop_category_id') != null)
                $a = $this->mshop->getCountArticlesInCategory($this->session->userdata('shop_category_id'), -1, false, false, true, $filters);
            else
                $a = $this->mshop->getCountAll($filters);

            // vd($a);

            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = 35;
            $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/shop/';
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

            if ($page_number > 1) $this->session->set_userdata('shopFrom', $from);
            else $this->session->unset_userdata('shopFrom');
            //////////

            $order_by = userdata('order_by');
            $sort_by = userdata('sort_by');
            if (!$order_by) $order_by = 'DESC';
            if (!$sort_by) $sort_by = 'num';

            if ($this->session->userdata('shop_category_id') != null)
                $data['shop'] = $this->mshop->getArticlesByCategory(userdata('shop_category_id'), $per_page, $from, -1, $order_by, $sort_by, false, false, -1, true, $filters);
            else
                $data['shop'] = $this->mshop->getArticles($per_page, $from, $order_by, -1, false, $sort_by, $filters);
        }

        $data['mailer_checked_count'] = $this->mshop->getMailerCount('checked');

        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/shop.php', $data);
    }

    public function set_category()
    {
        if (isset($_POST['category_id']) && $_POST['category_id'] == 'all') $this->session->unset_userdata('shop_category_id');
        else if (isset($_POST['category_id'])) $this->session->set_userdata('shop_category_id', $_POST['category_id']);
        $back = "/admin/shop/";
        if (isset($_POST['back'])) $back = $_POST['back'];
        redirect($back);
    }

    public function category($id)
    {
        $this->session->set_userdata('shop_category_id', $id);
        redirect("/admin/shop/");
    }

    public function add()
    {
        if (isset($_GET['category'])) {
            $cat = $_GET['category'];
            if ($cat == 'Блуза') $_GET['category_id'] = 23;
            elseif ($cat == 'Switshot') $_GET['category_id'] = 24;
            elseif ($cat == 'Брюки') $_GET['category_id'] = 27;
            elseif ($cat == 'Комбинезон') $_GET['category_id'] = 28;
            elseif ($cat == 'Куртка') $_GET['category_id'] = 30;
            elseif ($cat == 'Лосины') $_GET['category_id'] = 26;
            elseif ($cat == 'Платье') $_GET['category_id'] = 21;
            elseif ($cat == 'Платье,Свитшот') $_GET['category_id'] = 21;
            elseif ($cat == 'Сарафан') $_GET['category_id'] = 29;
            elseif ($cat == 'Юбка') $_GET['category_id'] = 25;
        }

        $err = '';
        if (isset($_POST['name']) && $_POST['name'] != '') {
            //if(!$this->mshop->getArticleByName($_POST['name']))
            //{

            $_POST['name'] = trim(post('name'));

            $url = '';
            if (isset($_POST['url'])) $url = $_POST['url'];
            if ($url == '') {
                $this->load->helper('translit_helper');
                // Проверяем существование урла
                $url = createUrl($_POST['name']);
                $url2 = $url;
                //var_dump($url);die();

                $this->db->where('url', $url2);
                $this->db->limit(1);
                $res = $this->db->get('shop')->result_array();
                $resc = 1;
                $url2 = $url;
                while ($res) {
                    $url2 = $url . '-' . $resc;
                    $this->db->where('url', $url2);
                    $this->db->limit(1);
                    $res = $this->db->get('shop')->result_array();
                    $resc++;
                }
                $url = $url2;
                //var_dump($url);die();
                ///////////////
            }

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

            $razmer = '';
            $razmers = $_POST['razmer'];
            $ccount = count($razmers);
            for ($i = 0; $i < $ccount; $i++) {
                $razmer .= $razmers[$i];
                if (($i + 1) < $ccount) $razmer .= '*';
            }

            $youtube = '';
            if ($youtube == '') $youtube = $_POST['youtube'];

            $social_buttons = 0;
            if (isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) $social_buttons = 1;

            $show_comments = 0;
            if (isset($_POST['show_comments']) && $_POST['show_comments'] == true) $show_comments = 1;

            $akciya = 0;
            if (isset($_POST['akciya']) && $_POST['akciya'] == true) $akciya = 1;

            $sale = 0;
            if (isset($_POST['sale']) && $_POST['sale'] == true) $sale = 1;

            $mailer_new = 0;
            if (isset($_POST['mailer_new']) && $_POST['mailer_new'] == true) $mailer_new = 1;
            else {
                $otherColor = $this->mshop->getArticleByName($_POST['name']);
                if (!$otherColor) $mailer_new = 1;
            }

            //$image = '';

            $glavnoe = 0;
            if (isset($_POST['glavnoe']) && $_POST['glavnoe'] == true) $glavnoe = 1;

            $need_text = 0;
            if (isset($_POST['need_text']) && $_POST['need_text'] == true) $need_text = 1;

            $short_content = '';
            if (isset($_POST['short_content'])) $short_content = $_POST['short_content'];
            $data['num'] = $this->mshop->getNewNum();
            $date = date("Y-m-d");
            $time = date("H:i");

            $sizes_to_good_ids = $base_ids = $warehouse = NULL;


            if (isset($_GET['stgi'])) {
                $sizes_to_good_ids = urldecode($_GET['stgi']);
            }

            if (isset($_GET['base_ids'])) {
                $base_ids = urldecode($_GET['base_ids']);
            }
            if (isset($_GET['warehouse'])) {
                $warehouse = urldecode($_GET['warehouse']);
            }

            //vdd($warehouse);

            $dbins = array(
                'sizes_to_good_ids' => $sizes_to_good_ids,
                'base_ids' => $base_ids,
                'warehouse' => $warehouse,
                'name' => $_POST['name'],
                'url' => $url,
                'category_id' => $category_id,
                'short_content' => $short_content,
                'content' => $_POST['content'],
                'h1' => $h1,
                'num' => $data['num'],
                'time' => $time,
                'date' => $date,
                'active' => $active,
                'title' => $title,
                'youtube' => $youtube,
                'keywords' => $keywords,
                'description' => $description,
                'robots' => "index, follow",
                'count' => 0,
                'seo' => '',
                'login' => $this->session->userdata('login'),
                'social_buttons' => $social_buttons,
                'show_comments' => $show_comments,
                'price' => $_POST['price'],
                'articul' => $_POST['articul'],
                'glavnoe' => $glavnoe,
                'razmer' => $razmer,
                'color' => $_POST['color'],
                'tkan' => $_POST['tkan'],
                'sostav' => $_POST['sostav'],
                'season' => post('season'),
                'akciya' => $akciya,
                'mailer_new' => $mailer_new,
                'sale' => $sale,
                'need_text' => $need_text,
                'height' => post('height'),
                'hand_height' => post('hand_height'),
                'tags' => $_POST['tags']
            );
            // vdd($dbins);
            $this->db->insert('shop', $dbins);

            $this->db->where('name', $_POST['name']);
            $this->db->where('url', $url);
            $this->db->where('date', $date);
            $this->db->where('time', $time);
            $this->db->limit(1);
            $art = $this->db->get('shop')->result_array();

            if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['userfile']['name'] != '') {
                    $imagearr = $this->upload_foto('userfile', $art[0]['id']);
                    $image = '/upload/shop/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }
            if ($image != '' && $image != false) {
                $this->db->where('id', $art[0]['id'])->limit(1)->update('shop', array('image' => $image));
            }


            if (isset($_POST['mail_to_copywraiter']) && $_POST['mail_to_copywraiter'] == true) {  // если требуется уведомление копирайтера о необходимости описания, отправляем письмо


                $message = "Добрый день!<br />На сайт " . $_SERVER['SERVER_NAME'] . " добавлен новый товар, требующий описание!<br />";
                if (isset($art[0]['id'])) $message .= 'Ссылка для редактирования: <a href="http://' . $_SERVER['SERVER_NAME'] . '/admin/shop/edit/' . $art[0]['id'] . '/">http://' . $_SERVER['SERVER_NAME'] . '/admin/shop/edit/' . $art[0]['id'] . '/</a>';
                $this->load->helper('mail_helper');
                mail_send(getOption('copywriter_email'), 'Требуется описание для нового товара', $message);
            }
            redirect("/admin/shop/");
            //}
            //else $err = 'Такая страница уже существует!';
        }

        $data['mailer_article_def'] = $this->options->getOption('mailer_article_def');
        if (!$data['mailer_article_def'] === false) $data['mailer_article_def'] = 1;
        $data['article_in_many_categories'] = $this->options->getOption('article_in_many_categories');
        if ($data['article_in_many_categories'] === false) $data['article_in_many_categories'] = 0;
        $data['num'] = $this->mshop->getNewNum();
        //var_dump("asd");die();

        $data['title'] = "Добавление товара";
        $data['err'] = $err;

        //$data['shop'] = $this->mshop->getArticles();
        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/shop_add', $data);
    }

    public function edit($id)
    {
        $article = $this->mshop->getArticleById($id);
        $err = '';
        if (isset($_POST['name']) && $_POST['name'] != '') {
            $url = $_POST['url'];
            if (isset($_POST['url'])) $url = $_POST['url'];
            if ($url == '') {
                $this->load->helper('translit_helper');
                // Проверяем существование урла
                $url = createUrl($_POST['name']);
                $url2 = $url;
                //var_dump($url);die();

                $this->db->where('url', $url2);
                $this->db->limit(1);
                $res = $this->db->get('shop')->result_array();
                $resc = 1;
                $url2 = $url;
                while ($res) {
                    $url2 = $url . '-' . $resc;
                    $this->db->where('url', $url2);
                    $this->db->limit(1);
                    $res = $this->db->get('shop')->result_array();
                    $resc++;
                }
                $url = $url2;
                //var_dump($url);die();
                ///////////////
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

            $razmer = '';
            $razmers = $_POST['razmer'];
            $ccount = count($razmers);
            for ($i = 0; $i < $ccount; $i++) {
                $razmer .= $razmers[$i];
                if (($i + 1) < $ccount) $razmer .= '*';
            }

            $youtube = '';
            if ($youtube == '') $youtube = $_POST['youtube'];

            $glavnoe = 0;
            if (isset($_POST['glavnoe']) && $_POST['glavnoe'] == true) $glavnoe = 1;

            $akciya = 0;
            if (isset($_POST['akciya']) && $_POST['akciya'] == true) $akciya = 1;

            $sale = 0;
            if (isset($_POST['sale']) && $_POST['sale'] == true) $sale = 1;

            $akciya_start_unix = 0;
            if ($_POST['akciya_start'] != '') {
                $darr = explode('-', $_POST['akciya_start']);
                $akciya_start_unix = mktime(0, 0, 0, $darr[1], $darr[2], $darr[0]);
            }
            $akciya_end_unix = 0;
            if ($_POST['akciya_end'] != '') {
                $darr = explode('-', $_POST['akciya_end']);
                $akciya_end_unix = mktime(0, 0, 0, $darr[1], $darr[2], $darr[0]);
            }

            $mailer_new = 0;
            if (isset($_POST['mailer_new']) && $_POST['mailer_new'] == true) $mailer_new = 1;

            $need_text = 0;
            if (isset($_POST['need_text']) && $_POST['need_text'] == true) $need_text = 1;

            $mailer_sale = 0;
            if (isset($_POST['mailer_sale']) && $_POST['mailer_sale'] == true) $mailer_sale = 1;

            $social_buttons = 0;
            if (isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) $social_buttons = 1;
            $show_comments = 0;
            if (isset($_POST['show_comments']) && $_POST['show_comments'] == true) $show_comments = 1;

            $ended = 0;
            if (isset($_POST['ended']) && $_POST['ended'] == true) $ended = 1;

            $image = '';
            if (isset($_POST['image'])) $image = $_POST['image'];
            if (isset($_POST['image_del']) && $_POST['image_del'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                $image = '';
            }
            if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['userfile']['name'] != '') {
                    $imagearr = $this->upload_foto('userfile', $article['id']);
                    //var_dump($imagearr);die();
                    if ($image != '') unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                    $image = '/upload/shop/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }

            if (isset($_POST['image_no_logo'])) $image_no_logo = $_POST['image_no_logo'];
            if (isset($_POST['image_no_logo_del']) && $_POST['image_no_logo_del'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image_no_logo);
                $image_no_logo = '';
            }
            if (isset($_FILES['image_no_logo'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['image_no_logo']['name'] != '') {
                    $imagearr = $this->upload_foto('image_no_logo', $article['id']);
                    if ($image_no_logo != '') unlink($_SERVER['DOCUMENT_ROOT'] . $image_no_logo);
                    $image_no_logo = '/upload/shop/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }

            $image_vk = '';
            if (isset($_POST['image_vk'])) $image_vk = $_POST['image_vk'];
            if (isset($_POST['image_vk_del']) && $_POST['image_vk_del'] == true) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $image_vk);
                $image_vk = '';
            }
            if (isset($_FILES['image_vk'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['image_vk']['name'] != '') {
                    $imagearr = $this->upload_foto('image_vk', $article['id']);
                    if ($image_vk != '') unlink($_SERVER['DOCUMENT_ROOT'] . $image_vk);
                    $image_vk = '/upload/shop/' . date("Y-m-d") . '/' . $imagearr['file_name'];
                }
            }
            //////////////////////////////////////////

            $rating = $_POST['rating'];
            $voitings = $_POST['voitings'];
            if ($rating == 0) $voitings = 0;


            $dbins = array(
                'name' => $_POST['name'],
                'url' => $url,
                'category_id' => $category_id,
                'short_content' => $_POST['short_content'],
                'content' => $_POST['content'],
                'h1' => $h1,
                'image' => $image,
                'image_no_logo' => $image_no_logo,
                'image_vk' => $image_vk,
                'num' => $_POST['num'],
                'youtube' => $youtube,
                'active' => $active,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'robots' => 'index, follow',
                'count' => $_POST['count'],
                'seo' => $_POST['seo'],
                'social_buttons' => $social_buttons,
                'show_comments' => $show_comments,
                'price' => $_POST['price'],
                'articul' => $_POST['articul'],
                'glavnoe' => $glavnoe,
                'razmer' => $razmer,
                'color' => $_POST['color'],
                'tkan' => $_POST['tkan'],
                'sostav' => $_POST['sostav'],
                'season' => post('season'),
                'akciya' => $akciya,
                'need_text' => $need_text,
                'akciya_start' => $_POST['akciya_start'],
                'akciya_start_unix' => $akciya_start_unix,
                'akciya_end' => $_POST['akciya_end'],
                'akciya_end_unix' => $akciya_end_unix,
                'discount' => $_POST['discount'],
                'sale' => $sale,
                'mailer_new' => $mailer_new,
                'mailer_sale' => $mailer_sale,
                'ended' => $ended,
                'rating' => $rating,
                'voitings' => $voitings,
                'moder' => userdata('login'),
                'moder_last_date' => date("Y-m-d H:i"),
                'height' => post('height'),
                'hand_height' => post('hand_height'),
                'tags' => $_POST['tags']

            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('shop', $dbins);

            if (isset($_POST['mail_to_copywraiter']) && $_POST['mail_to_copywraiter'] == true) {  // если требуется уведомление копирайтера о необходимости описания, отправляем письмо
                $this->db->where('id', $id);
                $this->db->limit(1);
                $art = $this->db->get('shop')->result_array();

                $message = "Добрый день!<br />На сайте " . $_SERVER['SERVER_NAME'] . " отмечен существующий товар, требующий описание!<br />";
                if (isset($art[0]['id'])) $message .= 'Ссылка для редактирования: <a href="http://' . $_SERVER['SERVER_NAME'] . '/admin/shop/edit/' . $art[0]['id'] . '/">http://' . $_SERVER['SERVER_NAME'] . '/admin/shop/edit/' . $art[0]['id'] . '/</a>';
                $this->load->helper('mail_helper');
                mail_send(getOption('copywriter_email'), 'Требуется описание для отмеченного товара', $message);
            } else if ($need_text == 0 && $article['need_text'] == 1 && $article['content'] != post('content')) {
                // уведомляем администратора, что описание для товара готово
                $this->load->helper('mail_helper');
                $to = getOption('admin_email');
                $message = 'У товара [' . $id . '] <b>' . post('name') . ' (' . post('color') . ')</b> пользователем <b>' . userdata('login') . '</b> была снята отметка о том, что требуется описание!';
                mail_send($to, 'Описание для ' . post('name') . ' (' . post('color') . ') готово', $message);
            }

            if (isset($_POST['send_about_active']) && $_POST['send_about_active'] == true) {

            }

            if (isset($_POST['save_and_stay']))
                redirect("/admin/shop/edit/" . $id . "/");
            else
                redirect("/admin/shop/");
        }
        $data['article_in_many_categories'] = $this->options->getOption('article_in_many_categories');
        if ($data['article_in_many_categories'] === false) $data['article_in_many_categories'] = 0;
        $data['article'] = $article;
        $data['images'] = $this->images->getByShopId($id);
        $data['title'] = "Редактирование товара";
        $data['err'] = $err;
        $data['num'] = $this->mshop->getNewNum();
        $data['categories'] = $this->mcats->getCategories();
        //$data['shop'] = $this->mshop->getArticles();
        $this->load->view('admin/shop_edit', $data);
    }

    public function up($id)
    {
        $cat = $this->mshop->getArticleById($id);
        if (($cat) && $cat['num'] > 0) {
            $num = $cat['num'] - 1;
            $oldcat = $this->mshop->getArticleByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('shop', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num + 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('shop', $dbins);
            }
        }
        $url = '/admin/shop/';
        if ($this->session->userdata('shopFrom') !== false) $url .= $this->session->userdata('shopFrom') . '/';
        redirect($url);
    }

    public function down($id)
    {
        $cat = $this->mshop->getArticleById($id);
        if (($cat) && $cat['num'] < ($this->mshop->getNewNum() - 1)) {
            $num = $cat['num'] + 1;
            $oldcat = $this->mshop->getArticleByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('shop', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num - 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('shop', $dbins);
            }
        }
        $url = '/admin/shop/';
        if ($this->session->userdata('shopFrom') !== false) $url .= $this->session->userdata('shopFrom') . '/';
        redirect($url);
    }

    public function break_torgsoft($id)
    {
        $dbins = array(
            'base_ids' => NULL,
            'warehouse' => NULL,
            'sizes_to_good_ids' => NULL,
            'warehouse_sum' => 0
        );
        $this->db->where('id', $id)->limit(1)->update('shop', $dbins);

        $url = '/admin/shop/';
        if ($this->session->userdata('shopFrom') !== false) $url .= $this->session->userdata('shopFrom') . '/';
        redirect($url);
    }

    public function del($id)
    {
        if (userdata('type') == 'admin') {
            $this->db->where('id', $id);
            $this->db->limit(1);
            $art = $this->db->get('shop')->result_array();
            if ($art) {
                $art = $art[0];
                if ($art['image'] != '') {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $art['image']);
                    unlink(str_replace('/shop/', '/original/', $_SERVER['DOCUMENT_ROOT'] . $art['image']));
                }
            }
            $this->db->where('id', $id)->limit(1)->delete('shop');
            $url = '/admin/shop/';
            if ($this->session->userdata('shopFrom') !== false) $url .= $this->session->userdata('shopFrom') . '/';

            // SAVE LOG
            $dbins = array(
                'date' => date("Y-m-d"),
                'time' => date("H:i"),
                'text' => "Удаление товара ID: " . $id . " " . $art['name'] . ' (' . $art['color'] . ')',
                'ip' => GetRealIp(),
                'login' => userdata('login'),
                'type' => "admin",
                'error' => "0"
            );
            $this->db->insert('logs', $dbins);

            redirect($url);
        } else {
            echo 'У Вас нет доступа для данной опперации!';
            $this->load->helper('mail_helper');
            $to = getOption('admin_email');
            $msg = 'Пользователь ' . userdata('login') . ' пытался удалить товар ID: ' . $id . '<br />IP: ' . GetRealIp() . '<br />Дата: ' . date("Y-m-d H:i:s");
            mail_send($to, 'Опперация заблокирована!', $msg);
        }
    }

    public function active($id)
    {
        $this->ma->setActive($id, 'shop');
        $url = '/admin/shop/';
        if ($this->session->userdata('shopFrom') !== false) $url .= $this->session->userdata('shopFrom') . '/';
        redirect($url);
    }

    public function always_first($id)
    {
        $this->ma->setAlwaysFirst($id, 'shop');
        $url = '/admin/shop/';
        if ($this->session->userdata('shopFrom') !== false) $url .= $this->session->userdata('shopFrom') . '/';
        redirect($url);
    }

    public function export()
    {
        $currensy_rub = getOption('currensy_rub');
        header("Content-Description: File Transfer\r\n");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . 'export_' . date("Y-m-d_H-i") . '.csv');
        $fp = fopen('php://output', 'w');
        header("Content-Type: text/csv; charset=CP1251\r\n");

        $dbins = array(
            iconv('UTF-8', 'CP1251', 'ID товара'),
            iconv('UTF-8', 'CP1251', 'ID раздела'),
            iconv('UTF-8', 'CP1251', 'Название товара'),
            iconv('UTF-8', 'CP1251', 'Артикул'),
            iconv('UTF-8', 'CP1251', 'Цена'),
            iconv('UTF-8', 'CP1251', 'Размеры'),
            iconv('UTF-8', 'CP1251', 'Фото (url)'),
            iconv('UTF-8', 'CP1251', 'Youtube'),
            iconv('UTF-8', 'CP1251', 'Краткое описание'),
            iconv('UTF-8', 'CP1251', 'Контент'),
            iconv('UTF-8', 'CP1251', 'Цвет'),
            iconv('UTF-8', 'CP1251', 'Состав')
        );

        //headers
        fputcsv($fp, $dbins, ';', '"');

        $articles = $this->mshop->getArticles();
        //var_dump($articles);die();
        $count = count($articles);
        for ($i = 0; $i < $count; $i++) {
            $p = $articles[$i];


            //else $brand = '';

            /*
            $images = '';
            $imgs = $this->images->getByShopId($p['id']);
            if($imgs)
            {
             $imgcount = count($imgs);
             for($j = 0; $j < $imgcount; $j++)
             {
                  $img = $imgs[$j];
                  $images .= $img['id'].':'.$img['image'];
                  if(($j+1) < $imgcount)
                  {
                       $images .= '
';
                  }
             }
            }
            */

            $razmer = $p['razmer'];


            $row = array(
                iconv('UTF-8', 'CP1251', $p['id']),
                iconv('UTF-8', 'CP1251', $p['category_id']),
                iconv('UTF-8', 'CP1251', $p['name'] . ' (' . $p['color'] . ')'),
                iconv('UTF-8', 'CP1251', $p['articul']),
                iconv('UTF-8', 'CP1251', ($p['price'] * $currensy_rub)),
                iconv('UTF-8', 'CP1251', $p['razmer']),
                iconv('UTF-8', 'CP1251', $p['image']),
                iconv('UTF-8', 'CP1251', $p['youtube']),
                iconv('UTF-8', 'CP1251', $p['short_content']),
                iconv('UTF-8', 'CP1251', $p['content']),
                iconv('UTF-8', 'CP1251', $p['color']),
                iconv('UTF-8', 'CP1251', $p['tkan'])
            );

            fputcsv($fp, $row, ';', '"');
        }

        fclose($fp);
    }

    public function exportAdwords()
    {

        header("Content-Description: File Transfer\r\n");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . 'export_' . date("Y-m-d_H-i") . '.csv');
        $fp = fopen('php://output', 'w');
        header("Content-Type: text/csv; charset=CP1251\r\n");

        $dbins = array(
            iconv('UTF-8', 'CP1251', 'Deal ID'),
            iconv('UTF-8', 'CP1251', 'Deal name'),
            iconv('UTF-8', 'CP1251', 'Final URL'),
            iconv('UTF-8', 'CP1251', 'Image URL'),
            iconv('UTF-8', 'CP1251', 'Subtitle'),
            iconv('UTF-8', 'CP1251', 'Description'),
            iconv('UTF-8', 'CP1251', 'Price'),
            iconv('UTF-8', 'CP1251', 'Sale price'),
            iconv('UTF-8', 'CP1251', 'Category'),
            iconv('UTF-8', 'CP1251', 'Contextual keywords'),
            iconv('UTF-8', 'CP1251', 'Address'),
            iconv('UTF-8', 'CP1251', 'Tracking template'),
            iconv('UTF-8', 'CP1251', 'Custom parameter')
        );

        //headers
        fputcsv($fp, $dbins, ';', '"');

        $articles = $this->mshop->getArticles();
        $currensy_grn = getOption('currensy_grn');
        //var_dump($articles);die();
        $count = count($articles);
        for ($i = 0; $i < $count; $i++) {
            $p = $articles[$i];

            $category = $this->model_categories->getCategoryById($p['category_id']);
            //else $brand = '';

            /*
            $images = '';
            $imgs = $this->images->getByShopId($p['id']);
            if($imgs)
            {
             $imgcount = count($imgs);
             for($j = 0; $j < $imgcount; $j++)
             {
                  $img = $imgs[$j];
                  $images .= $img['id'].':'.$img['image'];
                  if(($j+1) < $imgcount)
                  {
                       $images .= '
';
                  }
             }
            }
            */

            $razmer = $p['razmer'];


//            $dbins = array(
//                iconv('UTF-8', 'CP1251', 'Deal ID'),
//                iconv('UTF-8', 'CP1251', 'Deal name'),
//                iconv('UTF-8', 'CP1251', 'Final URL'),
//                iconv('UTF-8', 'CP1251', 'Image URL'),
//                iconv('UTF-8', 'CP1251', 'Subtitle'),
//                iconv('UTF-8', 'CP1251', 'Description'),
//                iconv('UTF-8', 'CP1251', 'Price'),
//                iconv('UTF-8', 'CP1251', 'Sale price'),
//                iconv('UTF-8', 'CP1251', 'Category'),
//                iconv('UTF-8', 'CP1251', 'Contextual keywords'),
//                iconv('UTF-8', 'CP1251', 'Address'),
//                iconv('UTF-8', 'CP1251', 'Tracking template'),
//                iconv('UTF-8', 'CP1251', 'Custom parameter')
//            );
            $row = array(
                iconv('UTF-8', 'CP1251', $p['id']),
                iconv('UTF-8', 'CP1251', $p['name'] . ' (' . $p['color'] . ')'),
                iconv('UTF-8', 'CP1251', getFullUrl($p)),
                iconv('UTF-8', 'CP1251', $p['image']),
                iconv('UTF-8', 'CP1251', 'Peony - одежда от производителя'),
                iconv('UTF-8', 'CP1251', 'Купить ' . $category['name'] . ' оптом'),
                iconv('UTF-8', 'CP1251', ($p['price'] * $currensy_grn)),
                iconv('UTF-8', 'CP1251', (getNewPrice($p['price'], $p['discount']) * $currensy_grn)),
                iconv('UTF-8', 'CP1251', $category['name']),
                iconv('UTF-8', 'CP1251', $p['content']),
                iconv('UTF-8', 'CP1251', $p['color']),
                iconv('UTF-8', 'CP1251', $p['tkan'])
            );

            fputcsv($fp, $row, ',', '"');
        }

        fclose($fp);
    }

    function upload_csv($file = 'userfile')
    {
        // Проверка наличия папки текущей даты. Если нет, то создать
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/csv/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/csv/', 0777);
        }


        //////
        // Функция загрузки
        $config['upload_path'] = 'upload/csv/';
        $config['overwrite'] = true;
        $config['allowed_types'] = 'csv';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file)) {
            echo $this->upload->display_errors();
            die();
        } else {
            $ret = $this->upload->data();
            return $ret;
        }
    }

    public function import()
    {
        //var_dump(unserialize('a:4:{i:0;s:9:"2013-12/1";i:1;s:9:"2014-01/1";i:2;s:9:"2014-01/2";i:3;s:9:"2014-02/2";} '));
        $data['msg'] = '';
        if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл
            if ($_FILES['userfile']['name'] != '') {
                $imagearr = $this->upload_csv();
                $file = '/upload/csv/' . $imagearr['file_name'];
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
                if (isset($_POST['price_only'])) {
                    $f = fopen($_SERVER['DOCUMENT_ROOT'] . $file, "rt") or die("Ошибка чтения подгруженного файла!");
                    $namearr = array();
                    for ($i = 0; $data = fgetcsv($f, 1000, ";"); $i++) {
                        $num = count($data);
                        $this->mshop->setPriceByArticul($data[0], $data[1]);
                    }
                    fclose($handle);
                } else {
                    if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . $file, "r")) !== FALSE) {
                        $headers = fgetcsv($handle, 0, ';', '"');

                        $old = 0;
                        $new = 0;
                        //var_dump($headers);die();
                        $k = 0;
                        while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {

                            $num = count($data);
                            $id = trim($data[0]);

                            $parr = array();
                            $category_id = iconv('CP1251', 'UTF-8', $data[1]);
                            $name = iconv('CP1251', 'UTF-8', $data[2]);
                            $articul = iconv('CP1251', 'UTF-8', $data[3]);
                            $price = iconv('CP1251', 'UTF-8', $data[4]);
                            $razmer = iconv('CP1251', 'UTF-8', $data[5]);
                            $image = iconv('CP1251', 'UTF-8', $data[6]);
                            $youtube = iconv('CP1251', 'UTF-8', $data[7]);
                            $short_content = iconv('CP1251', 'UTF-8', $data[8]);
                            $content = iconv('CP1251', 'UTF-8', $data[9]);

                            $color = iconv('CP1251', 'UTF-8', $data[10]);
                            $tkan = iconv('CP1251', 'UTF-8', $data[11]);

                            $youtube = str_replace('http://youtu.be/', '', $youtube);
                            //$images = iconv('CP1251','UTF-8',$data[14]);

                            if (strpos($image, 'http://') !== false) {
                                $string = file_get_contents($image);
                                if ($string) {
                                    $ipos = strrpos($image, '/');
                                    $iname = substr($image, $ipos + 1);
                                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/')) {
                                        mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/');
                                    }
                                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/shop/' . date("Y-m-d") . '/' . $iname, $string);
                                    $image = '/upload/shop/' . date("Y-m-d") . '/' . $iname;

                                }
                            }


                            // echo $bron.'<HR>';

                            $name = trim($name);

                            $keywords = $name . ', купить ' . $name;
                            $description = 'У Нас Вы можете купить ' . $name;

                            $dbins = array(
                                'category_id' => $category_id,
                                'name' => $name,
                                'articul' => $articul,
                                'razmer' => $razmer,
                                'image' => $image,
                                'youtube' => $youtube,
                                'short_content' => $short_content,
                                'content' => $content,
                                'price' => $price,
                                'title' => $name,
                                'keywords' => $keywords,
                                'description' => $description,
                                'youtube' => $youtube,
                                'color' => $color,
                                'tkan' => $tkan
                            );

                            if ($name != '' && $category_id != '') {
                                $is_new = true;
                                if ($id != '') {
                                    $shop = $this->mshop->getProductById($id);
                                    if ($shop) $is_new = false;
                                }

                                if (!$is_new) {
                                    $this->db->where('id', $id);
                                    $this->db->limit(1);
                                    $this->db->update('shop', $dbins);
                                    $old++;
                                } else {
                                    $dbins['num'] = $this->mshop->getNewNum();
                                    $this->load->helper('translit_helper');
                                    $dbins['url'] = createUrl($name);


                                    $this->db->insert('shop', $dbins);
                                    //var_dump("add");die();
                                    $new++;
                                }
                            }
                        }
                        fclose($handle);
                        $data['msg'] = '<p class="msg">Импорт успешно завершён!<br />Изменено позиций: ' . $old . '<br />Добавлено позиций: ' . $new . '</p>';
                    } else {
                        die("Ошибка чтения подгруженного файла!");
                    }
                    //////////////////////////////
                    /*
                             $f = fopen($_SERVER['DOCUMENT_ROOT'].$file, "rt") or die("Ошибка чтения подгруженного файла!");
                             $namearr = array();
                             for ($i=0; $data=fgetcsv($f,1000,";"); $i++) {
                                  $num = count($data);
                                  if($i == 0)
                                  {
                                       for ($c=0; $c<$num; $c++)
                                       {
                                        array_push($namearr, iconv('CP1251','UTF-8',$data[$c]));
                                       }
                                  }
                                  else
                                  {
                                       $id = trim($data[0]);
                                       $parr = array();

                                       $category_id = iconv('CP1251','UTF-8',$data[1]);
                                       $brand = iconv('CP1251','UTF-8',$data[2]);
                                       $name = iconv('CP1251','UTF-8',$data[3]);
                                       $articul = iconv('CP1251','UTF-8',$data[4]);
                                       $price = iconv('CP1251','UTF-8',$data[5]);
                                       $image = iconv('CP1251','UTF-8',$data[6]);
                                       $youtube = iconv('CP1251','UTF-8',$data[7]);
                                       $short_content = iconv('CP1251','UTF-8',$data[8]);
                                       $content = iconv('CP1251','UTF-8',$data[9]);
                                       $tab2 = iconv('CP1251','UTF-8',$data[10]);
                                       $tab3 = iconv('CP1251','UTF-8',$data[11]);
                                       $tab4 = iconv('CP1251','UTF-8',$data[12]);


                                       $brand_id = 0;
                                       if($brand != '')
                                       {
                                        $brand = $this->brands->getBrand($brand);
                                        if($brand) $brand_id = $brand['id'];
                                       }

                                      // echo $bron.'<HR>';

                                       $dbins = array(
                                        'category_id'		=> $category_id,
                                        'brand_id'		=> $brand_id,
                                        'name'		=> $name,
                                        'articul'	=> $articul,
                                        'image'		=> $image,
                                        'content'	=> $content,
                                        'tab2'		=> $tab2,
                                        'tab3'		=> $tab3,
                                        'tab4'		=> $tab4,
                                        'price'		=> $price
                                       );

                                       if($id != '')
                                       {
                                        $this->db->where('id', $id);
                                        $this->db->limit(1);
                                        $this->db->update('shop', $dbins);
                                       }
                                       else
                                       {
                                        $this->db->insert('shop', $dbins);
                                        var_dump("add");die();
                                       }

                                       //var_dump($data);
                                       //echo '<hr>';
                                  }
                             }
                             */
                }
            }
        }
        $data['title'] = "Импорт CSV";
        $this->load->view('admin/import_csv', $data);
    }

    private function clear_old_export()
    {
        $msg = "";
        $this->load->helper('file');

        $path = "./upload/";
        $folder_name = "export/";
        $files = get_filenames($path . $folder_name);
        $old = strtotime("-1 month");

        $count = count($files);
        for ($i = 0; $i < $count; $i++) {
            $f = $files[$i];
            $file = $path . $folder_name . '/' . $f;
            $funix = filectime($file);
            if ($funix < $old) {
                unlink($file);
                $msg .= "Файл " . $file . " удалён<br />";
            }
        }
        return $msg;
    }

    public function createCheckedPrice()
    {
        $msg = $this->clear_old_export();

        $shop = $this->mshop->getForMailer("checked");
        $this->load->helper('file');
        $this->load->helper('translit_helper');
        $this->load->helper('export_helper');
        create_images_folder($shop);
        create_price_xls($shop);

        $name = create_zip_file("./upload/temp/checked/", "price.xls", "images");
        $data['name'] = $name;

        $data['msg'] = $msg;
        $data['title'] = "Создать прайс";
        $this->load->view('admin/price_add', $data);
    }

    public function createExtendedPrice()
    {
        if (isset($_POST['create']) || isset($_POST['recreate'])) {
            $articles = Array();
            if (isset($_POST['recreate'])) {
                $artIds = json_decode(post('products'));
                $i = 0;
                foreach ($artIds as $id) {
                    $art = $this->mshop->getArticleById($id);
                    if ($art) {
                        $articles[$i] = $art;
                        $i++;
                    }
                }
                $_POST['rows'] = json_decode(post('rows'));
            } else
                $articles = $this->mshop->getForMailer('checked');


            $image_no_logo = post('image_no_logo');

            $this->load->helper('file');
            $this->load->helper('translit_helper');
            $this->load->helper('export_helper');
            create_images_folder2($articles, $image_no_logo);
            $config = array(
                'create_price_percents' => post('create_price_percents'),
                'min_warehouse' => post('min_warehouse'),
                'warehouse_zaniz' => post('warehouse_zaniz')
            );
            create_price_xls2($articles, post('rows'), $config);

            $name = create_zip_file("./upload/temp/checked/", "price.xls", "images");
            $link_fotos = "/upload/export/" . $name . ".zip";
            $link_xls = "/upload/export/" . $name . ".xls";
            $data['name'] = $name;

            if (post('save') == true) {               /// СОХРАНЯЕМ СПЕЦИФИКАЦИЮ
                $products = array();
                foreach ($articles as $article)
                    array_push($products, $article['id']);

                $products = json_encode($products);
                if ($image_no_logo) $image_no_logo = 1;
                else $image_no_logo = 0;
                $rows = json_encode(post('rows'));
                $dbins = array(
                    'date' => date("Y-m-d H:i"),
                    'rows' => $rows,
                    'image_no_logo' => $image_no_logo,
                    'products' => $products,
                    'saved_name' => post('saved_name'),
                    'link_xls' => $link_xls,
                    'link_fotos' => $link_fotos,
                    'min_warehouse' => post('min_warehouse'),
                    'warehouse_zaniz' => post('warehouse_zaniz'),
                    'create_price_percents' => post('create_price_percents')
                );

                $this->db->insert('specifications', $dbins);
            }
        }
        $data['msg'] = '';
        $data['title'] = "Создать спецификацию";
        if (isset($_POST['create'])) $data['title'] = "Спецификация создана успешно";
        $this->load->view('admin/export/extended_price.php', $data);
    }

    public function specifications_recreate($id)
    {
        $spec = $this->mshop->getSpecificationById($id);
        if ($spec) {
            $_POST['create_price_percents'] = $spec['create_price_percents'];
            $_POST['min_warehouse'] = $spec['min_warehouse'];
            $_POST['warehouse_zaniz'] = $spec['warehouse_zaniz'];
            $_POST['save'] = true;
            if ($spec['image_no_logo'] == 1) $_POST['image_no_logo'] = true;
            $_POST['recreate'] = true;
            $_POST['products'] = $spec['products'];
            $_POST['rows'] = $spec['rows'];
            $_POST['saved_name'] = 'Копия ' . $spec['saved_name'];
            $this->createExtendedPrice();
        }
    }

    public function specifications()
    {

        // ПАГИНАЦИЯ //
        $this->load->library('pagination');
        $per_page = 35;
        $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/shop/';
        $config['total_rows'] = $this->mshop->getSpecificationsCount();
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

        if ($page_number > 1) $this->session->set_userdata('shopFrom', $from);
        else $this->session->unset_userdata('shopFrom');
        //////////

        $order_by = userdata('order_by');
        $sort_by = userdata('sort_by');
        if (!$order_by) $order_by = 'DESC';
        if (!$sort_by) $sort_by = 'num';

        $data['specifications'] = $this->mshop->getSpecifications($per_page, $from);

        $data['msg'] = '';
        $data['title'] = "Спецификации";
        $this->load->view('admin/export/specifications.php', $data);
    }

    public function specifications_del($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        $art = $this->db->get('specifications')->result_array();
        if ($art) {
            $art = $art[0];
            if ($art['link_xls'] != '') {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $art['link_xls']);
                @unlink($_SERVER['DOCUMENT_ROOT'] . $art['link_fotos']);
            }
        }
        $this->db->where('id', $id)->limit(1)->delete('specifications');
        $url = '/admin/shop/specifications/';

        redirect($url);
    }

    public function check_by_old($id)
    {
        $spec = $this->mshop->getSpecificationById($id);
        if ($spec) {
            $products = json_decode($spec['products']);
            if ($products) {
                $this->db->update('shop', array('mailer_checked' => 0));
                foreach ($products as $product) {
                    $this->db->where('id', $product)->limit(1)->update('shop', array('mailer_checked' => 1));
                }
                set_userdata('msg', 'Все товары данной спецификации выбраны!');
            }
        }
        redirect('/admin/shop/specifications');
    }

    public function currencies()
    {
        if(isset($_GET['test'])){
            $uahToUsd = getCurrencyTypeValue('UAH');
            vd($uahToUsd);

            vdd($uahToUsd / 0.43);

            die();
        }

        $currencies = $this->mshop->getCurrencies();
        $checked = false;
        if (isset($_GET['edit'])) $checked = $this->mshop->getCurrencyById($_GET['edit']);
        if (isset($_GET['edit']) && isset($_POST['save'])) {
            // СОХРАНЯЕМ ИЗМЕНЕИЯ
            //if($_POST['value'] != $checked['value']){
            $history = json_decode($checked['history'], true);
            $new['date'] = date("Y-m-d H:i");
            $new['old_value'] = $checked['value'];
            $new['new_value'] = $_POST['value'];
            if (is_array($history))
                $history[count($history)] = $new;
            else $history[0] = $new;

            $auto_update = 0;
            $main = 0;
            if (isset($_POST['auto_update']) && $_POST['auto_update'] == true) $auto_update = 1;
            if (isset($_POST['main']) && $_POST['main'] == true) $main = 1;

            // vdd($main);
            $dbins = array(
                'history' => json_encode($history),
                'value' => $_POST['value'],
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'symb' => $_POST['symb'],
                'auto_update' => $auto_update,
                'auto_update_plus' => $_POST['auto_update_plus'],
                'main' => $main
            );
            $this->db->where('id', $_GET['edit'])->limit(1)->update('currencies', $dbins);

            if ($main == 1)
                $this->db->where('id <>', $_GET['edit'])->update('currencies', array('main' => 0));
            //}

            /** изменяем курс рубля, если отредактировали UAH */
            if(post('code') == 'UAH'){
                $rub = getCurrencyByCode('RUB');
                $newRub = round(post('value') / $rub['auto_update_plus']);
                if($rub['value'] != $newRub){
                    $this->db->where('code', 'RUB')->update('currencies', array('value' => $newRub));
                }
            }

            redirect('/admin/shop/currencies/');
        }
        $data['title'] = "Валюты";

        $data['currencies'] = $currencies;
        $this->load->view('admin/currencies', $data);
    }

}