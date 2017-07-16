<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('./application/thumbs/ThumbLib.inc.php');


class Categories extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->driver('cache');

        $this->load->helper('login_helper');
        $this->load->model('Model_articles', 'art');
        $this->load->model('Model_categories', 'cat');
        $this->load->model('Model_pages', 'pages');
        $this->load->model('Model_options', 'options');
        $this->load->model('model_users', 'users');
        $this->load->model('Model_comments', 'comments');
        $this->load->model('Model_main', 'main');
        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_images', 'images');
        $this->session->set_userdata('last_url', $_SERVER['REQUEST_URI']);
        $this->load->helper('one_click_helper');

        preloader();

        // Если зашли через iFrame (VK)
        if (isset($_GET['viewer_id'])) {
            vkLogin();
        }
        isLogin();

        //$this->load->library('vkalbum');

        if (isset($_GET['from']))
            $_GET['adwords'] = $_GET['from'];

        if (isset($_GET['adwords'])) {
            set_userdata('adwords', $_GET['adwords']);
            adWordsLog();
            redirect(request_uri(false, true));
        }

    }


    public function index()
    {
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
        $this->output->cache(15);
    }


    public function category($url, $filter = false, $filter_value = false)
    {
        $sort = false;
        $cache = $this->config->item('cache');
        $cacheTime = $this->config->item('cache_time');

        $footerNoCached = $this->load->view('footer_no_cached.php', false, true);

        $this->session->set_userdata('category_url', $_SERVER['REQUEST_URI']);

        $options = $this->main->getMain();
        $category = $this->cat->getCategoryByUrl($url, 1);


        if ($category) {
            //$articles = $this->art->getArticlesByCategory($category['id'],-1,-1,1, $category['order_by']);

            //var_dump($articles);die();
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            //$per_page = $options['pagination'];


//			// ФИЛЬТР
//			if(isset($_GET['razmer'])) { $this->session->set_userdata('razmer', urldecode($_GET['razmer'])); redirect('/'.$url.'/'); }
//			if(isset($_GET['color'])) { $this->session->set_userdata('color', urldecode($_GET['color'])); redirect('/'.$url.'/'); }
//			if(isset($_GET['sostav'])) { $this->session->set_userdata('sostav', urldecode($_GET['sostav'])); redirect('/'.$url.'/'); }
//			if(isset($_GET['filter_reset']))
//			{
//				$this->session->unset_userdata('razmer');
//				$this->session->unset_userdata('color');
//				$this->session->unset_userdata('sostav');
//				redirect('/'.$url.'/');
//			}
//
//			$razmer = $this->session->userdata('razmer');
//			$color = $this->session->userdata('color');
//			$sostav = $this->session->userdata('sostav');
//			///////////

            $razmer = false;
            $color = false;
            $sostav = false;
            if ($filter == 'razmer') {
                $razmer = $filter_value;
                set_userdata('filter_razmer', $filter_value);
            } else unset_userdata('filter_razmer');

//if(isDebug()){
//    vd($filter);vd($razmer);
//}
            // СОРТИРОВКА
            $sort_order_by = 'DESC';
            $sort_sort_by = 'num';

            if (isset($_POST['sort']))
                $this->session->set_userdata('sort', $_POST['sort']);

            if ($this->session->userdata('sort') !== false) {
                $sort = $this->session->userdata('sort');
                if ($sort == 'price_min_max') {
                    $sort_order_by = 'ASC';
                    $sort_sort_by = 'price';
                } elseif ($sort == 'price_max_min') {
                    $sort_order_by = 'DESC';
                    $sort_sort_by = 'price';
                } elseif ($sort == 'ASC') {
                    $sort_order_by = 'ASC';
                    $sort_sort_by = 'num';
                } elseif ($sort == 'DESC') {
                    $sort_order_by = 'DESC';
                    $sort_sort_by = 'num';
                }
            }

            if (isset($_POST['per_page'])) {
                // if($_POST['per_page'] == 'all') $_POST['per_page'] = 9999;
                set_userdata('per_page', $_POST['per_page']);
            }

            //////////////
            ///
            ///


            // Ищем, есть ли кэшированный head для текущего урла
            $headHtml = '';
            if($cache)
                $headHtml = $this->partialcache->get('head_'.cacheUrl(), $cacheTime);
            if(!$headHtml){
                $data['title'] = $category['title'] . $this->model_options->getOption('global_title');
                $data['keywords'] = $category['keywords'] . ', ' . $this->model_options->getOption('global_keywords');
                $data['description'] = $category['description'] . $this->model_options->getOption('global_description');
                $data['robots'] = $category['robots'];
                $headHtml = $this->load->view('head_new', $data, true);
                if($cache)
                    $this->partialcache->save('head_'.cacheUrl(), $headHtml);
            }
            echo $headHtml;

            if(!isset($data)) $data = array();
            $headerHtml = $this->load->view('header_new', $data, true);
            echo $headerHtml;

            $contentHtml = '';
            if($cache) {
                if(!isset($sort))
                    $contentHtml = $this->partialcache->get(cacheUrl(), $cacheTime);
                else $contentHtml = $this->partialcache->get(cacheUrl() . $sort, $cacheTime);
            }
            if(!$contentHtml) {
                ob_start();
                $data['cachePostfix'] = $sort;
                $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url . '/';
                $config['prefix'] = '!';
                //$config['use_page_numbers']	= TRUE;
                if ($category['type'] == 'articles') {
                    $config['total_rows'] = $this->art->getCountArticlesInCategory($category['id'], 1);
                    $per_page = $this->model_options->getOption('pagination_news');
                    if (!$per_page) $per_page = 10;
                } elseif ($category['type'] == 'shop') {
                    if ($category['url'] == 'all' || $category['url'] == 'all-for-dropshippers')
                        $config['total_rows'] = $this->shop->getCountArticlesByParentCategory(0, 1, $razmer, $color, $sostav);
                    else
                        $config['total_rows'] = $this->shop->getCountArticlesInCategory($category['id'], 1, $razmer, $color, $sostav);

                    $per_page = $this->model_options->getOption('pagination_shop');
                    if (!$per_page) $per_page = 10;
                    if (userdata('per_page') !== false) {
                        $per_page = userdata('per_page');
                        if ($per_page == 'all') $per_page = 9999;
                    }
                    if ($category['url'] == 'all-for-dropshippers') $per_page = 99999;
                }
                //var_dump($config['total_rows']);
                $config['num_links'] = 3;
                $config['first_link'] = '<span class="icon-arrow-full-right"></span>'; // в самое начало
                $config['last_link'] = '<span class="icon-arrow-full-left"></span>'; // в самый конец
                $config['next_link'] = '<span class="icon-arrow-left"></span>'; // назад
                $config['prev_link'] = '<span class="icon-arrow-right"></span>'; // вперёд

                $config['num_tag_open'] = '<span class="pagerNum">';
                $config['num_tag_close'] = '</span>';
                $config['cur_tag_open'] = '<span class="pagerCurNum">';
                $config['cur_tag_close'] = '</span>';
                $config['prev_tag_open'] = '<span class="pagerPrev">'; // назад
                $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
                $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">'; // вперёд
                $config['next_tag_close'] = '</span>';
                $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">'; // в конец
                $config['last_tag_close'] = '</span>';
                $config['first_tag_open'] = '<span class="pagerFirst">'; // в начало
                $config['first_tag_close'] = '</span>&nbsp;&nbsp;';

                $config['per_page'] = $per_page;
                $config['uri_segment'] = 2;
                $from = intval(str_replace('!', '', $this->uri->segment(2)));
                //echo $from;die();
                $page_number = $from / $per_page + 1;
                $this->pagination->initialize($config);
                $data['pager'] = $this->pagination->create_links();
                //////////

                if ($category['type'] == 'articles')
                    $data['articles'] = $this->art->getArticlesByCategory($category['id'], $per_page, $from, 1, $category['order_by']);
                elseif ($category['type'] == 'shop') {
                    if ($category['url'] == 'all' || $category['url'] == 'all-for-dropshippers')
                        $data['articles'] = $this->shop->getArticlesByParentCategory(0, $per_page, $from, 1, $sort_order_by, $sort_sort_by, $razmer, $color);
                    else
                        $data['articles'] = $this->shop->getArticlesByCategory($category['id'], $per_page, $from, 1, $sort_order_by, $sort_sort_by, $razmer, $color, $sostav);
                }

                $page_no = '';
                if ($page_number > 1)
                    $page_no = ' (стр. ' . $page_number . ')';


                $data['subcategories_cols_count'] = $this->options->getOption('subcategories_cols_count');
                if (!$data['subcategories_cols_count']) $data['subcategories_cols_count'] = 3;
                $data['subcategories_image_width'] = $this->options->getOption('subcategories_image_width');
                if (!$data['subcategories_image_width']) $data['subcategories_image_width'] = 200;

                $data['total_rows'] = $config['total_rows'];
                $data['per_page'] = $per_page;
                $data['razmer'] = $razmer;
                $data['color'] = $color;
                $data['sostav'] = $sostav;

                $data['title'] = $category['title'] . $page_no . $this->model_options->getOption('global_title');
                $data['keywords'] = $category['keywords'] . ', ' . $page_no . $this->model_options->getOption('global_keywords');
                $data['description'] = $category['description'] . $page_no . $this->model_options->getOption('global_description');
                $data['robots'] = $category['robots'];

                if ($razmer) {
                    //TKDZ Generator

                    $cname = $category['name'];
                    $lname = mb_strtolower($category['name']);

                    // Title
                    if (mb_strpos($data['title'], $lname) !== false)
                        $data['title'] = str_replace($lname, $lname . ' ' . $razmer . ' размера', $data['title']);
                    elseif (mb_strpos($data['title'], $cname) !== false)
                        $data['title'] = str_replace($cname, $cname . ' ' . $razmer . ' размера', $data['title']);
                    else
                        $data['title'] = $razmer . ' размер, ' . $data['title'];

                    // Description
                    if (mb_strpos($data['description'], $lname) !== false)
                        $data['description'] = str_replace($lname, $lname . ' ' . $razmer . ' размера', $data['description']);
                    elseif (mb_strpos($data['description'], $cname) !== false)
                        $data['description'] = str_replace($cname, $cname . ' ' . $razmer . ' размера', $data['description']);
                    else
                        $data['description'] = $razmer . ' размер, ' . $data['description'];

                    // H1
                    if (mb_strpos($category['h1'], $lname) !== false)
                        $category['h1'] = str_replace($lname, $lname . ' ' . $razmer . ' размера', $category['h1']);
                    elseif (mb_strpos($category['h1'], $cname) !== false)
                        $category['h1'] = str_replace($cname, $cname . ' ' . $razmer . ' размера', $category['h1']);
                    else
                        $category['h1'] = $category['h1'] . ', ' . $razmer . ' размер';

                    // SEO text
                    $category['seo'] = '';
                    if (!$data['articles']) $data['robots'] = "noindex, follow";
                }

                $data['category'] = $category;
                $data['page_number'] = $page_number;
                $data['subcategories'] = $this->cat->getSubCategories($category['id'], 1);

                $data['glavnoe'] = $this->art->getPodGlavnoe($category['id']);
                //$data['h1']             = $category['h1'];
                $data['seo'] = $category['seo'];
                //$data['glavnoe']	    = $this->art->getGlavnoe();
                $this->load->view('templates/' . $category['template'], $data);
            }

            $contentHtml = str_replace('[no_cached]', $footerNoCached, $contentHtml);
            echo $contentHtml;

            //$this->output->cache(15);
        } else // Если не категория, то страница
        {
            $page = false;
            // Ищем, есть ли кэшированный head для текущего урла
            $headHtml = '';
            if($cache)
                $headHtml = $this->partialcache->get('head_'.cacheUrl(), $cacheTime);
            if(!$headHtml){
                $page = $this->pages->getPageByUrl($url);
                $data['title'] = $page['title'] . $this->model_options->getOption('global_title');
                $data['keywords'] = $page['keywords'] . $this->model_options->getOption('global_keywords');
                $data['description'] = $page['description'] . $this->model_options->getOption('global_description');
                $data['robots'] = $page['robots'];
                $headHtml = $this->load->view('head_new', $data, true);
                if($cache)
                    $this->partialcache->save('head_'.cacheUrl(), $headHtml);
            }

            echo $headHtml;

            $headerHtml = $this->load->view('header_new', false, true);
            echo $headerHtml;

            $contentHtml = '';
            if($cache)
                $contentHtml = $this->partialcache->get(cacheUrl(), $cacheTime);
            if(! $contentHtml) {
                if(!$page)
                    $page = $this->pages->getPageByUrl($url);
                if ($page) {
                    if ($page['active'] != '1') err404();
                    else {
                        if ($_SERVER['REQUEST_URI'] == '/contacts/') {
                            // КАПЧА
                            $this->load->helper('captcha');
                            $vals = array(
                                'img_path' => './captcha/',
                                'font_path' => './system/fonts/texb.ttf',
                                'img_url' => 'http://' . $_SERVER['SERVER_NAME'] . '/captcha/'
                            );

                            $cap = create_captcha($vals);

                            $data = array(
                                'captcha_time' => $cap['time'],
                                'ip_address' => $this->input->ip_address(),
                                'word' => $cap['word']
                            );

                            $query = $this->db->insert_string('captcha', $data);
                            $this->db->query($query);

                            $data['cap'] = $cap;

                            if (isset($_POST['name'])) {
                                // КАПЧА
                                $expiration = time() - 7200; // Two hour limit
                                $this->db->query("DELETE FROM captcha WHERE captcha_time < " . $expiration);

                                // Then see if a captcha exists:
                                $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
                                $binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
                                $query = $this->db->query($sql, $binds);
                                $row = $query->row();
                                ///////////

                                var_dump($row->count);
                                if ($row->count == 0) {
                                    $err['captcha'] = 'Вы не ввели правильные цифры!';
                                }


                                $data['err'] = $err;

                                if (!$err) {
                                    $admin_email = $this->model_options->getOption('admin_email');
                                    if ($admin_email !== false) {
                                        $this->load->helper('mail_helper');

                                        $msg = 'Имя: ' . $_POST['name'] . '<br>
									Город: ' . $_POST['city'] . '<br>
									e-mail: <a href="mailto:' . $_POST['email'] . '">' . $_POST['email'] . '</a><br>
									Тема: <strong>' . $_POST['tema'] . '</strong><br>
									Сообщение:<br>' . $_POST['message'];

                                        $data['sended'] = mail_send($admin_email, 'ФОРМА ОБРАТНОЙ СВЯЗИ: ' . $_POST['tema'], $msg);

                                        unset($_POST);
                                        $this->session->set_userdata('sended', 'true');
                                        redirect($_SERVER['REQUEST_URI']);
                                    }
                                }

                            }
                        } elseif ($page['url'] == 'reviews') {
                            $this->load->library('partialcache');
                            $this->load->helper('modules_helper');
                        }

//vd($data['comments']);

                        $data['title'] = $page['title'] . $this->model_options->getOption('global_title');
                        $data['keywords'] = $page['keywords'] . $this->model_options->getOption('global_keywords');
                        $data['description'] = $page['description'] . $this->model_options->getOption('global_description');
                        $data['robots'] = $page['robots'];
                        $data['page'] = $page;
                        $data['server_name'] = $this->model_options->getOption('server_name');
                        //$data['glavnoe']	= $this->art->getPodGlavnoe($category['id']);
                        //$data['h1']             = $category['h1'];
                        $data['seo'] = $page['seo'];
                        //$data['glavnoe']	    = $this->art->getGlavnoe();
                        $data['articles'] = $this->art->getLastArticles(3, 1);
                        if ($page['template'] != '')
                            $this->load->view('templates/' . $page['template'], $data);
                        else
                            $this->load->view('templates/page.tpl.php', $data);
                        //$this->output->cache(15);
                    }
                } else err404();
            } else {
                $contentHtml = str_replace('[no_cached]', $footerNoCached, $contentHtml);
                echo $contentHtml;
            }
        }
    }

    public function subcategory($url, $parent_url)
    {
        $footerNoCached = $this->load->view('footer_no_cached.php', false, true);

        $cache = $this->config->item('cache');
        $cacheTime = $this->config->item('cache_time');
        if (isDebug()) {
            if (isset($_FILES['userfile'])) {
                vd($_FILES['userfile']['name']);
            }
        }
        $options = $this->main->getMain();
        $category = $this->cat->getCategoryByUrl($url, 1);
        $parent = $this->cat->getCategoryByUrl($parent_url, 1);


        if (($category) && ($parent)) {

            //$articles = $this->art->getArticlesByCategory($category['id'],-1,-1,1, $category['order_by']);
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            $per_page = $options['pagination'];
            $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $parent_url . '/' . $url . '/';
            $config['total_rows'] = $this->art->getCountArticlesInCategory($category['id'], 1);
            $config['num_links'] = 3;
            $config['prefix'] = '!';
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'Следующая >';
            $config['prev_link'] = '< Предыдущая';

            $config['num_tag_open'] = '<span class="pagerNum">';
            $config['num_tag_close'] = '</span>';
            $config['cur_tag_open'] = '<span class="pagerCurNum">';
            $config['cur_tag_close'] = '</span>';
            $config['prev_tag_open'] = '<span class="pagerPrev">';
            $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
            $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
            $config['next_tag_close'] = '</span>';
            $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
            $config['last_tag_close'] = '</span>';
            $config['first_tag_open'] = '<span class="pagerFirst">';
            $config['first_tag_close'] = '</span>&nbsp;&nbsp;';

            $config['per_page'] = $per_page;
            $config['uri_segment'] = 3;
            $from = intval(str_replace('!', '', $this->uri->segment(3)));
            $page_number = $from / $per_page + 1;
            $this->pagination->initialize($config);
            $data['pager'] = $this->pagination->create_links();
            //////////
            $data['articles'] = $this->art->getArticlesByCategory($category['id'], $per_page, $from, 1, $category['order_by']);
            $page_no = '';
            if ($page_number > 1)
                $page_no = ' (стр. ' . $page_number . ')';

            $data['subcategories_cols_count'] = $this->options->getOption('subcategories_cols_count');
            if (!$data['subcategories_cols_count']) $data['subcategories_cols_count'] = 3;
            $data['subcategories_image_width'] = $this->options->getOption('subcategories_image_width');
            if (!$data['subcategories_image_width']) $data['subcategories_image_width'] = 200;

            $data['title'] = $category['title'] . $page_no . $this->model_options->getOption('global_title');
            $data['keywords'] = $category['keywords'] . ', ' . $page_no . $this->model_options->getOption('global_keywords');
            $data['description'] = $category['description'] . $page_no . $this->model_options->getOption('global_description');
            $data['robots'] = $category['robots'];
            $data['category'] = $category;
            $data['page_number'] = $page_number;
            $data['subcategories'] = $this->cat->getSubCategories($category['id'], 1);
            $data['parent'] = $parent;
            //$data['h1']             = $category['h1'];
            $data['seo'] = $category['seo'];
            //$data['glavnoe']	    = $this->art->getGlavnoe();
            $this->load->view('templates/' . $category['template'], $data);
            //$this->output->cache(15);
        } else {
            $category = $parent;
            $type = 'articles';
            //echo $url.'<br />'.$parent['name'];die();

//            // Ищем, есть ли кэшированный head для текущего урла
//            $cachedAll = true;
//            $headHtml = '';
//            if($cache)
//                $headHtml = $this->partialcache->get('head_'.cacheUrl(), $cacheTime);
//            if(!$headHtml){
//                $cachedAll = false;
//            }
//            $headerHtml = $this->load->view('header_new', false, true);
//
//            $contentHtml = '';
//            if($cache) {
//                $contentHtml = $this->partialcache->get(cacheUrl(), 600);
//                if(!$contentHtml) $cachedAll = false;
//                else
//            }
//
//            if($cachedAll){
//                echo $headHtml;
//                echo $headerHtml;
//                $contentHtml = str_replace('[no_cached]', $footerNoCached, $contentHtml);
//                echo $contentHtml;
//            } else {

                $article = $this->art->getArticleByUrlAndCategoryId($url, $parent['id'], $category['order_by']);

                if (!$article) {
                    $articles = $this->art->getArticlesByUrl($url);
                    //var_dump($articles);die();
                    if ($articles) {
                        $count = count($articles);
                        for ($i = 0; $i < $count; $i++) {
                            $article = $articles[$i];
                            if ($article) {
                                //$category = $this->cat->getCategoryById($article['category_id']);
                                if ($category['parent'] != 0) {
                                    $p = $this->cat->getCategoryById($category['parent']);
                                    //var_dump($p);
                                    if ($p['id'] != $parent['id']) $article = false;
                                    else break;
                                } else $article = false;
                            }
                        }
                    }
                    //var_dump($article);die();


                }

                if (!$article) {
                    $article = $this->shop->getArticleByUrlAndCategoryId($url, $parent['id'], $category['order_by']);

                    if (!$article) {
                        $articles = $this->shop->getArticlesByUrl($url);
                        //var_dump($articles);die();
                        if ($articles) {
                            $count = count($articles);
                            for ($i = 0; $i < $count; $i++) {
                                $article = $articles[$i];
                                if ($article) {
                                    //$category = $this->cat->getCategoryById($article['category_id']);
                                    if ($category['parent'] != 0) {
                                        $p = $this->cat->getCategoryById($category['parent']);
                                        //var_dump($p);
                                        if ($p['id'] != $parent['id']) $article = false;
                                        else break;
                                    } else $article = false;
                                }
                            }
                        }
                    }
                    if ($article) {
                        $cached = showCache('shop', array('article' => $article));
                        if($cached){
                            echo $cached;
                            die();
                        }
                        $type = 'shop';
                        if ($article['title'] == $article['name'])
                            $article['title'] = $article['name'] . ' (' . $article['color'] . ') купить ' . mb_strtolower($category['name']);

                        if ($article['description'] == $article['name'])
                            $article['description'] = $article['name'] . ' (' . $article['color'] . ') - купить ' . mb_strtolower($category['name']) . ' оптом от производителя';
                    }
                //}


                if (!$article) err404();


                //$category = $this->cat->getCategoryById($article['category_id']);
                $comments = $this->comments->getCommentsToArticle($article['id']);

                // КАПЧА
                $this->load->helper('captcha');
                $vals = array(
                    'img_path' => './captcha/',
                    'font_path' => './system/fonts/texb.ttf',
                    'img_url' => 'http://' . $_SERVER['SERVER_NAME'] . '/captcha/'
                );

                $cap = create_captcha($vals);

                $data = array(
                    'captcha_time' => $cap['time'],
                    'ip_address' => $this->input->ip_address(),
                    'word' => $cap['word']
                );

                $query = $this->db->insert_string('captcha', $data);
                $this->db->query($query);

                $data['cap'] = $cap;
                //


//			$data['user']		= $this->users->getUserByLogin($article['login']);



                $data['articles'] = $this->art->getLastArticles(3, 1);
                $data['article'] = $article;
                $data['category'] = $category;
                $data['comments'] = $comments;
                $data['parent'] = $parent;
                if ($type == 'shop') {
                    $show_in_bottom = 1;
                    if ($article['discount'] > 0)
                        $show_in_bottom = 0;
                    $data['images'] = $this->images->getByShopId($article['id'], 1, $show_in_bottom);
                } else
                    $data['images'] = $this->images->getByArticleId($article['id'], 1, 1);



                $data['title'] = $article['title'] . $this->model_options->getOption('global_title');
                $data['keywords'] = $article['keywords'] . $this->model_options->getOption('global_keywords');
                $data['description'] = $article['description'] . $this->model_options->getOption('global_description');
                $data['robots'] = $article['robots'];
                $data['parent'] = $parent;

                $data['reviewsHtml'] = $this->load->view('mod/reviews.inc.php', $data, true);

                // КЭШИРУЕМ HEAD
                $headHtml = $this->load->view('head_new', $data, true);
                if($cache)
                    $this->partialcache->save('head_'.cacheUrl(), $headHtml);

                //$data['server_name']	= $this->model_options->getOption('server_name');

                //$data['h1']             = $category['h1'];
                $data['seo'] = $category['seo'];
                //$data['glavnoe']	    = $this->art->getGlavnoe();
                    $headerHtml = $this->load->view('header_new', false, true);
                echo $headHtml.$headerHtml;
                ob_start();
                if ($parent['content_template'] != '')
                    $this->load->view('templates/' . $parent['content_template'], $data);
                else
                    $this->load->view('templates/new.tpl.php', $data);
            }

//            if ($type == 'shop')
//                $this->shop->countPlus($article['id']);
//            else
//                $this->art->countPlus($article['id']);



            //$this->output->cache(15);
        }
        $this->db->close();
    }

    public function action()
    {
        $per_page = $this->model_options->getOption('pagination_shop');
        if (!$per_page) $per_page = 10;

        $this->load->library('pagination');

        $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/action/';
        $config['total_rows'] = $this->shop->getActionsCount();
        $config['num_links'] = 3;
        $config['prefix'] = '!';
        $config['first_link'] = 'в начало';
        $config['last_link'] = 'в конец';
        $config['next_link'] = 'Следующая >';
        $config['prev_link'] = '< Предыдущая';

        $config['num_tag_open'] = '<span class="pagerNum">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagerCurNum">';
        $config['cur_tag_close'] = '</span>';
        $config['prev_tag_open'] = '<span class="pagerPrev">';
        $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
        $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
        $config['next_tag_close'] = '</span>';
        $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
        $config['last_tag_close'] = '</span>';
        $config['first_tag_open'] = '<span class="pagerFirst">';
        $config['first_tag_close'] = '</span>&nbsp;&nbsp;';

        $config['per_page'] = $per_page;
        $config['uri_segment'] = 2;
        $from = intval(str_replace('!', '', $this->uri->segment(2)));
        $page_number = $from / $per_page + 1;
        $this->pagination->initialize($config);
        $data['pager'] = $this->pagination->create_links();
        //////////
        $data['articles'] = $this->shop->getActions($per_page, $from);
        $page_no = '';
        if ($page_number > 1)
            $page_no = ' (стр. ' . $page_number . ')';

        $category = $this->model_categories->getCategoryById(31);

        $data['title'] = $category['title'] . $page_no . $this->model_options->getOption('global_title');
        $data['keywords'] = $category['keywords'] . ', ' . $page_no . $this->model_options->getOption('global_keywords');
        $data['description'] = $category['description'] . $page_no . $this->model_options->getOption('global_description');
        $data['robots'] = $category['robots'];
        $data['category'] = $category;
        $data['page_number'] = $page_number;


        //$data['h1']             = $category['h1'];
        $data['seo'] = $category['seo'];
        //$data['glavnoe']	    = $this->art->getGlavnoe();
        $this->load->view('templates/' . $category['template'], $data);
        //$this->output->cache(15);

    }

    public function search() // поиск
    {
        $search_google = $this->model_options->getOption('search_google');
        if ($search_google === false) $search_google = 0;

        $search = false;
        if (isset($_POST['search'])) {
            $this->session->set_userdata('search', trim($_POST['search']));
        }
        $search = $this->session->userdata('search');


        if ($search_google == 1) {
            $data['title'] = "Поиск: " . $search . $this->model_options->getOption('global_title');
            $data['keywords'] = '';
            $data['description'] = '';
            $data['robots'] = "noindex, follow";
            $data['seo'] = "";
            $this->load->view('search/google_search.tpl.php', $data);


        } else {
            $this->load->library('pagination');
            //$data['articles']	= $this->art->Search(trim($_POST['search']));
            $data['articles'] = $this->shop->Search($search, -1, -1, 1);
            //$data['articles'] = $data['articles'] + $data['articles1'];
            //var_dump($data['articles']);

            $config['base_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/search/';
            $config['prefix'] = '!';
            //$config['use_page_numbers']	= TRUE;
            $config['total_rows'] = count($data['articles']);
            $per_page = $this->model_options->getOption('pagination_shop');
            //$per_page = 3;
            if (!$per_page) $per_page = 10;
            $config['num_links'] = 3;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'ВПЕРЕД';
            $config['prev_link'] = 'НАЗАД';

            $config['num_tag_open'] = '<span class="pagerNum">';
            $config['num_tag_close'] = '</span>';
            $config['cur_tag_open'] = '<span class="pagerCurNum">';
            $config['cur_tag_close'] = '</span>';
            $config['prev_tag_open'] = '<span class="pagerPrev">';
            $config['prev_tag_close'] = '</span>&nbsp;&nbsp;';
            $config['next_tag_open'] = '&nbsp;&nbsp;<span class="pagerNext">';
            $config['next_tag_close'] = '</span>';
            $config['last_tag_open'] = '&nbsp;&nbsp;<span class="pagerLast">';
            $config['last_tag_close'] = '</span>';
            $config['first_tag_open'] = '<span class="pagerFirst">';
            $config['first_tag_close'] = '</span>&nbsp;&nbsp;';

            $config['per_page'] = $per_page;
            $config['uri_segment'] = 2;
            $from = intval(str_replace('!', '', $this->uri->segment(2)));
            //echo $from;die();
            $page_number = $from / $per_page + 1;
            $this->pagination->initialize($config);
            $data['pager'] = $this->pagination->create_links();


            $data['articles'] = $this->shop->Search($search, $per_page, $from, 1);


            $data['title'] = "Поиск: " . $search . $this->model_options->getOption('global_title');
            $data['keywords'] = '';
            $data['description'] = '';
            $data['robots'] = "noindex, follow";
            $data['seo'] = "";
            $this->load->view('search/search.tpl.php', $data);
        }
        $this->db->close();
    }

    public function archive() // архив
    {
        if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])) {
            $day = $_POST['day'];
            if ($day == '1') $day = '01';
            if ($day == '2') $day = '02';
            if ($day == '3') $day = '03';
            if ($day == '4') $day = '04';
            if ($day == '5') $day = '05';
            if ($day == '6') $day = '06';
            if ($day == '7') $day = '07';
            if ($day == '8') $day = '08';
            if ($day == '9') $day = '09';

            $month = $_POST['month'];
            if ($month == '1') $month = '01';
            if ($month == '2') $month = '02';
            if ($month == '3') $month = '03';
            if ($month == '4') $month = '04';
            if ($month == '5') $month = '05';
            if ($month == '6') $month = '06';
            if ($month == '7') $month = '07';
            if ($month == '8') $month = '08';
            if ($month == '9') $month = '09';

            $year = $_POST['year'];


            $data['articles'] = $this->art->Archive($year . '-' . $month . '-' . $day);
            $_POST['search'] = $year . '-' . $month . '-' . $day;
            $data['title'] = "Поиск: " . $_POST['search'] . $this->model_options->getOption('global_title');
            $data['keywords'] = $_POST['search'] . $this->model_options->getOption('global_keywords');
            $data['description'] = "Поиск: " . $_POST['search'] . $this->model_options->getOption('global_description');
            $data['robots'] = "noindex, follow";
            $data['seo'] = "";
            $this->load->view('archive.tpl.php', $data);
        }
    }

    function banner($id)
    {
        $this->load->model('Model_banners', 'banners');
        $banner = $this->banners->getBannerById($id);
        if ($banner) {
            $this->banners->countPlus($id);
            redirect($banner['url']);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */