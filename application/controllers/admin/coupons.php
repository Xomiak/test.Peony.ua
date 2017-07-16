<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupons extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_coupons','coupons');
            $this->load->model('Model_users','users');
        }


	public function index()
	{       
        $data['title']  = "Скидочные купоны";

        // ПАГИНАЦИЯ //
        $this->load->library('pagination');
        $per_page = 50;
        $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/coupons/';
        $config['total_rows'] = $this->coupons->getAllCount();
        $config['num_links'] = 4;
        $config['first_link'] = 'в начало';
        $config['last_link'] = 'в конец';
        $config['next_link'] = 'далее';
        $config['prev_link'] = 'назад';
        
        $config['per_page'] = $per_page;
        $config['uri_segment']     = 3;
        $from = intval($this->uri->segment(3));
        $page_number=$from/$per_page+1;
        $this->pagination->initialize($config);
        $data['pager']  = $this->pagination->create_links();

        $data['coupons'] = $this->coupons->getAll($per_page, $from);
        $this->load->view('admin/coupons',$data);
	}
        
        public function add()
        {
            if(isset($_POST['code']))
            {
                $multi = 0;
                if(isset($_POST['multi']) && $_POST['multi'] == true) $multi = 1;
                $not_sale = 0;
                if(isset($_POST['not_sale']) && $_POST['not_sale'] == true) $not_sale = 1;

                $dbins = array(
                    'code'          => $_POST['code'],
                    'discount'      => $_POST['discount'],
                    'start_date'    => $_POST['start_date'],
                    'end_date'      => $_POST['end_date'],
                    'user_login'    => $_POST['user_login'],
                    'info'          => $_POST['info'],
                    'created_date'  => date("Y-m-d H:i"),
                    'gived_by'     => userdata('login'),
                    'multi'      => $multi,
                    'not_sale'      => $not_sale,
                    'type'          => $_POST['type']
                );


                $this->db->insert('coupons', $dbins);

                set_userdata('coupon_discount', $_POST['discount']);
                set_userdata('coupon_info', $_POST['info']);

                if(isset($_POST['save_and_stay']))
                {
                    $new = $this->coupons->getByCode($_POST['code']);
                   redirect('/admin/coupons/edit/'.$new['id']);
                }
                else
                       redirect("/admin/coupons/");
            }

            //$data['new_code'] = getRandCode();

            $res = true;
            while($res != false){
                $data['new_code'] = getRandCode();
                $res = $this->coupons->getByCode($data['new_code']);
            }

            $data['title']  = "Создать купон";
            $data['err'] = '';
      

            $this->load->view('admin/coupons_add_edit',$data);
        }
        
        public function edit($id)
        {
            if(isset($_POST['code']))
            {
                $multi = 0;
                if(isset($_POST['multi']) && $_POST['multi'] == true) $multi = 1;
                $not_sale = 0;
                if(isset($_POST['not_sale']) && $_POST['not_sale'] == true) $not_sale = 1;

                $products_only = NULL;
                if($_POST['products_only'] != ''){
                    $arr = explode(',',post('products_only'));
                    if(is_array($arr)){
                        $products_only = json_encode($arr);
                    }
                }

                $dbins = array(
                    'code'          => $_POST['code'],
                    'discount'      => $_POST['discount'],
                    'start_date'    => $_POST['start_date'],
                    'end_date'      => $_POST['end_date'],
                    'user_login'    => $_POST['user_login'],
                    'info'          => $_POST['info'],
                    'edited_by'     => userdata('login'),
                    'multi'         => $multi,
                    'not_sale'      => $not_sale,
                    'type'          => $_POST['type'],
                    'products_only' => $products_only
                );

                $this->db->where('id', $id);
                $this->db->limit(1);
                $this->db->update('coupons', $dbins);

                if(isset($_POST['save_and_stay']))
                   redirect($_SERVER['REQUEST_URI']);
                else
                       redirect("/admin/coupons/");
            }
    		
            $coupon = $this->coupons->getById($id);
            $data['coupon'] = $coupon;
            $data['title']  = "Редактирование купона";
            $data['err'] = '';
            if($coupon['used_date'] != false) $data['err'] = 'Этот купон уже был использован '.$coupon['used_date'].' клиентом '.$coupon['used_by'];
            $this->load->view('admin/coupons_add_edit',$data);
        }
        
        
        
        public function del($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('coupons');
            redirect("/admin/coupons/");
        }
	
	public function active($id)
	{
		  $this->ma->setActive($id,'coupons');
		  redirect('/admin/coupons/');
	}

    function Create($params)
    {
        $dbins = array();

        if(isset($params['discount'])) $dbins['discount'] = $params['discount'];
        if(isset($params['start_date'])) $dbins['start_date'] = $params['start_date'];
        if(isset($params['end_date'])) $dbins['end_date'] = $params['end_date'];
        if(isset($params['user_login'])) $dbins['user_login'] = $params['user_login'];
        if(isset($params['info'])) $dbins['info'] = $params['info'];

        $dbins['created_date'] =  date("Y-m-d");
        $dbins['active'] = 1;

        $dbins['code'] = getRandCode(6,10,false);  // генерируем случайный код
        $old = $this->coupons->getByCode($dbins['code']);
        while ($old != false) { // проверяем, нет ли в базе такого кода. если есть, генерируем новый.
            $dbins['code'] = getRandCode(6,10,false);
            $old = $this->coupons->getByCode($dbins['code']);
        }

        $this->db->insert('coupons', $dbins);
    }

}