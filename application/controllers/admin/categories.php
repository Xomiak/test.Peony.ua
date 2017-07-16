<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categories extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_categories', 'mcats');
    }

    function upload_foto()
    {                                // Функция загрузки и обработки фото
        $config['upload_path'] = 'upload/logos';
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
            return $ret;
        }
    }

    public function index()
    {
        $data['title'] = "Разделы";
        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/categories', $data);
    }

    public function add()
    {
        $err = '';
        if (isset($_POST['name']) && $_POST['name'] != '') {
            $url = $_POST['url'];
            if ($_POST['url'] == '') {
                $this->load->helper('translit_helper');
                $url = translitRuToEn($_POST['name']);
            }
            while ($this->mcats->getCategoryByUrl($url)) {
                $url .= "_1";
            }

            $active = 0;
            if (isset($_POST['active']) && $_POST['active'] == true) $active = 1;

            $image = '';
            if (isset($_POST['image'])) $image = $_POST['image'];
            if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['userfile']['name'] != '') {
                    $imagearr = $this->upload_foto();
                    $image = '/upload/logos/' . $imagearr['file_name'];
                }
            }

            $title = $_POST['title'];
            $keywords = $_POST['keywords'];
            $description = $_POST['description'];
            $h1 = $_POST['h1'];
            if ($title == "") $title = $_POST['name'];
            if ($keywords == "") $keywords = $_POST['name'];
            if ($description == "") $description = $_POST['name'];
            if ($h1 == "") $h1 = $_POST['name'];

            $show_in_menu = 0;
            if (isset($_POST['show_in_menu']) && $_POST['show_in_menu'] == true)
                $show_in_menu = 1;


            $dbins = array(
                'name' => $_POST['name'],
                'name_one' => $_POST['name_one'],
                'url' => $url,
                'num' => $_POST['num'],
                'parent' => $_POST['parent'],
                'active' => $active,
                'template' => $_POST['template'],
                'content_template' => $_POST['content_template'],
                'h1' => $h1,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'seo' => $_POST['seo'],
                'image' => $image,
                'show_in_menu' => $show_in_menu,
                'type' => $_POST['type']
            );
            $this->db->insert('categories', $dbins);

            $this->session->set_userdata('addCategoryParent', $_POST['parent']);
            $this->session->set_userdata('addCategoryTemplate', $_POST['template']);
            redirect("/admin/categories/");

        }
        $data['title'] = "Добавление раздела";
        $data['err'] = $err;
        $data['num'] = $this->mcats->getNewNum();
        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/categories_add', $data);
    }

    public function edit($id)
    {
        $err = '';
        if (isset($_POST['name']) && $_POST['name'] != '') {
            $url = $_POST['url'];
            if ($_POST['url'] == '') {
                $this->load->helper('translit_helper');
                $url = translitRuToEn($_POST['name']);
            }

            $active = 0;
            if (isset($_POST['active']) && $_POST['active'] == true) $active = 1;

            $image = '';
            if (isset($_POST['image'])) $image = $_POST['image'];
            if (isset($_POST['image_del']) && $_POST['image_del'] == true) {
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image))
                    unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                $image = '';

            }
            if (isset($_FILES['userfile'])) {                    // проверка, выбран ли файл картинки
                if ($_FILES['userfile']['name'] != '') {
                    $imagearr = $this->upload_foto();
                    if ($image != '') {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image))
                            unlink($_SERVER['DOCUMENT_ROOT'] . $image);
                    }
                    $image = '/upload/logos/' . $imagearr['file_name'];
                }
            }

            $h1 = $_POST['h1'];

            $title = $_POST['title'];
            $keywords = $_POST['keywords'];
            $description = $_POST['description'];
            if ($title == "") $title = $_POST['name'];
            if ($keywords == "") $keywords = $_POST['name'];
            if ($description == "") $description = $_POST['name'];
            if ($h1 == "") $h1 = $_POST['name'];

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

            $show_in_menu = 0;
            if (isset($_POST['show_in_menu']) && $_POST['show_in_menu'] == true)
                $show_in_menu = 1;

            $dbins = array(
                'name' => $_POST['name'],
                'name_one' => $_POST['name_one'],
                'url' => $url,
                'num' => $_POST['num'],
                'parent' => $_POST['parent'],
                'active' => $active,
                'template' => $_POST['template'],
                'content_template' => $_POST['content_template'],
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'h1' => $h1,
                'seo' => $_POST['seo'],
                'prom_content' => $_POST['prom_content'],
                'image' => $image,
                'show_in_menu' => $show_in_menu,
                'type' => $_POST['type'],
                'discount' => $_POST['discount'],
                'akciya_start' => $_POST['akciya_start'],
                'akciya_start_unix' => $akciya_start_unix,
                'akciya_end' => $_POST['akciya_end'],
                'akciya_end_unix' => $akciya_end_unix
            );
            $this->db->where('id', $id);
            $this->db->limit(1);
            $this->db->update('categories', $dbins);

            // ОЧИСТКА АКЦИИ ВО ВСЕХ ТОВАРАХ
            if (isset($_POST['clear_discount']) && $_POST['clear_discount'] == true) {
                $this->load->model('Model_shop', 'shop');
                $shop = $this->shop->getArticlesByCategory($id);
                if ($shop) {
                    $count = count($shop);
                    for ($i = 0; $i < $count; $i++) {
                        $s = $shop[$i];
                        if ($s['sale'] != 1) {
                            $dbins = array(
                                'discount' => 0,
                                'akciya_start' => '',
                                'akciya_start_unix' => 0,
                                'akciya_end' => '',
                                'akciya_end_unix' => 0
                            );

                            $this->db->where('id', $s['id']);
                            $this->db->limit(1);
                            $this->db->update('shop', $dbins);
                        }
                    }
                }
            }

            // ЕСЛИ УКАЗАНА АКЦИЯ, ЦЕПЛЯЕМ ЕЁ КО ВСЕМ ТОВАРАМ ЭТОГО РАЗДЕЛА
            if ($akciya_start_unix != 0 && $akciya_end_unix != 0) {
                $this->load->model('Model_shop', 'shop');
                $shop = $this->shop->getArticlesByCategory($id);

                //vdd(count($shop));

                if ($shop) {
                    $count = count($shop);
                    for ($i = 0; $i < $count; $i++) {
                        $s = $shop[$i];
                        if ($s['sale'] != 1) {
                            $dbins = array(
                                'discount' => $_POST['discount'],
                                'akciya_start' => $_POST['akciya_start'],
                                'akciya_start_unix' => $akciya_start_unix,
                                'akciya_end' => $_POST['akciya_end'],
                                'akciya_end_unix' => $akciya_end_unix
                            );

                            $this->db->where('id', $s['id']);
                            $this->db->limit(1);
                            $this->db->update('shop', $dbins);
                        }
                    }
                }
            }

            redirect("/admin/categories/");
        }
        $data['cat'] = $this->mcats->getCategoryById($id);
        $data['title'] = "Редактирование раздела";
        $data['err'] = $err;
        $data['num'] = $this->mcats->getNewNum();
        $data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/categories_edit', $data);
    }

    public function up($id)
    {
        $cat = $this->mcats->getCategoryById($id);
        if (($cat) && $cat['num'] > 0) {
            $num = $cat['num'] - 1;
            $oldcat = $this->mcats->getCategoryByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('categories', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num + 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('categories', $dbins);
            }
        }
        redirect('/admin/categories/');
    }

    public function down($id)
    {
        $cat = $this->mcats->getCategoryById($id);
        if (($cat) && $cat['num'] < ($this->mcats->getNewNum() - 1)) {
            $num = $cat['num'] + 1;
            $oldcat = $this->mcats->getCategoryByNum($num);
            $dbins = array('num' => $num);
            $this->db->where('id', $id)->limit(1)->update('categories', $dbins);
            if ($oldcat) {
                $dbins = array('num' => ($num - 1));
                $this->db->where('id', $oldcat['id'])->limit(1)->update('categories', $dbins);
            }
        }
        redirect('/admin/categories/');
    }

    public function del($id)
    {
        $this->db->where('id', $id)->limit(1)->delete('categories');
        redirect("/admin/categories/");
    }

    public function active($id)
    {
        $this->ma->setActive($id, 'categories');
        redirect('/admin/categories/');
    }
}