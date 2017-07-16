<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comments extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('login_helper');
        isAdminLogin();
        $this->load->model('Model_admin', 'ma');
        $this->load->model('Model_categories', 'cat');
        $this->load->model('Model_articles', 'art');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_comments', 'comments');
        $this->load->model('Model_users','users');
    }


    public function index()
    {

        if(isset($_GET['edit']) && isset($_POST['comment'])){
            $dbins = array(
                'name'  => post('name'),
                'comment'   => post('comment')
            );
            $this->db->where('id', $_GET['edit'])->limit(1)->update('comments', $dbins);
            redirect(request_uri(false,true));
        }

        $comments = $this->comments->getComments();
        // ПАГИНАЦИЯ //
        $this->load->library('pagination');
        $per_page = 50;
        $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/comments/';
        $config['total_rows'] = count($comments);
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
        //////////
        $data['comments'] = $this->comments->getComments($per_page, $from, 'active');

        $data['title'] = "Отзывы";

        //$data['categories'] = $this->mcats->getCategories();
        $this->load->view('admin/comments', $data);
    }

    public function active($id)
    {
        $c = $this->comments->getCommentById($id);
        if ($c) {
            if ($c['shop_id'] != 0) {
                $shop = $this->shop->getArticleById($c['shop_id']);
                if ($shop) {
                    $dbins['rating'] = $shop['rating'] + $c['rate'];
                    $dbins['voitings'] = $shop['voitings'] + 1;

                    $this->db->where('id', $shop['id'])->limit(1)->update('shop', $dbins);
                }
            }
        }
        $dbins = array(
            'active' => 1
        );
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->update('comments', $dbins);
        redirect('/admin/comments/');
    }

    public function del($id)
    {
        $c = $this->comments->getCommentById($id);
        if(isset($c['images']) && $c['images'] != '') @unlink($_SERVER['DOCUMENT_ROOT'].$c['images']);
        $this->db->where('id', $id)->limit(1)->delete('comments');
        $back = userdata('last_url');
        if (!$back) $back = "/admin/categories/";
        //if(isset($_GET['back'])) $back = urldecode($_GET['back']);
        //die();
        redirect($back);
    }
}