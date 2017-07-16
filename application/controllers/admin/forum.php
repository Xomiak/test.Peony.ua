<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forum extends CI_Controller {

         public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
	    isAdminLogin();
            $this->load->model('Model_admin','ma');
            $this->load->model('Model_forum','forum');
        }
	
	public function index()
	{
            $data['title']  = "Статические страницы";
            
            $this->load->view('admin/pages',$data);
	}
        
        public function sections()
        {
            $data['title']      = "Разделы форума";
            $data['hSections']  = $this->forum->getSectionsByParentId(0);
            $this->load->view('admin/forum/sections', $data);
        }
        
        public function sections_add()
        {
            if(isset($_POST['add']) || isset($_POST['add_and_edit']))
            {
                $_POST['name'] = trim($_POST['name']);
                $url = $_POST['url'];
                if($url == '')
                {
                    $this->load->helper('translit_helper');
                    $url = translitRuToEn($_POST['name']);
                    while($this->forum->getSectionByUrlAndParentId($url,$_POST['parent_id']))
                        $url .= '-1';
                }
                
                $num = $this->forum->getNewSectionNum($_POST['parent_id']);
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
                $title = $_POST['title'];
                if($title == '') $title = $_POST['name'];
                $keywords = $_POST['keywords'];
                if($keywords == '') $keywords = $_POST['name'];
                $description = $_POST['description'];
                if($description == '') $description = $_POST['name'];
                
                $create_topics = 0;
                if(isset($_POST['create_topics']) && $_POST['create_topics'] == true) $create_topics = 1;
                
                $dbins = array(
                    'parent_id'         => $_POST['parent_id'],
                    'name'              => $_POST['name'],
                    'num'               => $num,
                    'title'             => $title,
                    'keywords'          => $keywords,
                    'description'       => $description,
                    'robots'            => $_POST['robots'],
                    'seo'               => $_POST['seo'],
                    'active'            => $active,
                    'descr'             => $_POST['descr'],
                    'url'               => $url,
                    'date'              => $_POST['date'],
                    'time'              => $_POST['time'],
                    'views'             => $_POST['views'],
                    'create_topics'     => $create_topics
                );
                
                $this->db->insert('forum_sections', $dbins);
                
                if(isset($_POST['add_and_edit']))
                {
                    $section = $this->forum->getSectionByUrlAndParentId($url,$_POST['parent_id']);
                    if($section)
                        redirect("/admin/forum/sections/edit/".$section['id']."/");
                    else
                        echo 'Добавленный раздел не найден в базе! Возможно, он не был добавлен!';
                }
                else
                    redirect("/admin/forum/sections/");
            }
            
            $data['title']  = "Добавление раздела форума";
            $data['sections'] = $this->forum->getSectionsByParentId(0);
            $this->load->view('admin/forum/sections_add',$data);
        }
        
        public function sections_edit($id)
        {
            $section = $this->forum->getSectionById($id);
            
            if(isset($_POST['save']) || isset($_POST['save_and_edit']))
            {
                $_POST['name'] = trim($_POST['name']);
                $url = $_POST['url'];
                if($url == '')
                {
                    $this->load->helper('translit_helper');
                    $url = translitRuToEn($_POST['name']);
                    while($this->forum->getSectionByUrlAndParentId($url,$_POST['parent_id']))
                        $url .= '-1';
                }
                
                $num = $section['num'];
                
                if($_POST['old_parent_id'] != $_POST['parent_id'])
                {           
                    $num = $this->forum->getNewSectionNum($_POST['parent_id']);
                }
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
                $title = $_POST['title'];
                if($title == '') $title = $_POST['name'];
                $keywords = $_POST['keywords'];
                if($keywords == '') $keywords = $_POST['name'];
                $description = $_POST['description'];
                if($description == '') $description = $_POST['name'];
                
                $create_topics = 0;
                if(isset($_POST['create_topics']) && $_POST['create_topics'] == true) $create_topics = 1;
                
                $dbins = array(
                    'parent_id'         => $_POST['parent_id'],
                    'name'              => $_POST['name'],
                    'num'               => $num,
                    'title'             => $title,
                    'keywords'          => $keywords,
                    'description'       => $description,
                    'robots'            => $_POST['robots'],
                    'seo'               => $_POST['seo'],
                    'active'            => $active,
                    'descr'             => $_POST['descr'],
                    'url'               => $url,
                    'date'              => $_POST['date'],
                    'time'              => $_POST['time'],
                    'views'             => $_POST['views'],
                    'create_topics'     => $create_topics
                );
                
                $this->db->where('id', $id);
                $this->db->update('forum_sections', $dbins);
                
                if(isset($_POST['save_and_edit']))
                {
                    redirect("/admin/forum/sections/edit/".$section['id']."/");
                }
                else
                    redirect("/admin/forum/sections/");
            }
            
            $data['title']  = "Редактирование раздела форума";
            $data['section'] = $section;
            $data['sections'] = $this->forum->getSectionsByParentId(0);
            $this->load->view('admin/forum/sections_edit',$data);
        }
        
        public function sections_up($id)
        {
            $section = $this->forum->getSectionById($id);
            if($section)
            {
                if($section['num'] > 0)
                {
                    $prev = $this->forum->getPrevSectionByNumAndParentId($section['num'], $section['parent_id']);
                    if($prev)
                    {
                        $num = $prev['num'] + 1;
                        $dbins = array('num' => $num);
                        $this->db->where('id',$prev['id']);
                        $this->db->update('forum_sections', $dbins);
                        
                        $dbins = array('num' => $prev['num']);
                        $this->db->where('id',$section['id']);
                        $this->db->update('forum_sections', $dbins);
                    }
                }
            }
            redirect("/admin/forum/sections/");
        }
        
        public function sections_down($id)
        {
            $section = $this->forum->getSectionById($id);
            if($section)
            {
                if($section['num'] < ($this->forum->getNewSectionNum($section['parent_id'])-1))
                {
                    $next = $this->forum->getNextSectionByNumAndParentId($section['num'], $section['parent_id']);
                    if($next)
                    {                        
                        $num = $section['num'];
                        $dbins = array('num' => $num);
                        $this->db->where('id',$next['id']);
                        $this->db->update('forum_sections', $dbins);
                        
                        $num = $section['num'] + 1;
                        $dbins = array('num' => $num);
                        $this->db->where('id',$section['id']);
                        $this->db->update('forum_sections', $dbins);
                    }
                }
            }
            redirect("/admin/forum/sections/");
        }
        
        
        public function sections_del($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('forum_sections');
            redirect("/admin/forum/sections/");
        }
        
        public function sections_active($id)
        {
            $section = $this->forum->getSectionById($id);
            $active = 0;
            if($section['active'] == 0) $active = 1;
            
            $dbins = array('active' => $active);
            $this->db->where('id', $id);
            $this->db->update('forum_sections', $dbins);
            
            redirect("/admin/forum/sections/");
        }
        
////////////////////////////////////////////////////////////////////////////////
////////////////              TOPICS               /////////////////////////////
        public function topics()
        {
            $data['title']  = "Темы форума";
            if($this->session->userdata('section_id') !== false)
		$topics = $this->forum->getTopicsBySectionId($this->session->userdata('section_id'));
	    else
		$topics = $this->forum->getAllTopics();
            
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            
            $per_page = $this->forum->getOption('admin_topics_pagination_per_page');
            if(!$per_page) $per_page = 35;
            
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/forum/topics/';
            $config['total_rows'] = count($topics);
            $config['num_links'] = 4;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'далее';
            $config['prev_link'] = 'назад';
            
            $config['per_page'] = $per_page;
            $config['uri_segment']     = 4;
            $from = intval($this->uri->segment(4));
            $page_number=$from/$per_page+1;
            $this->pagination->initialize($config);
            
            $data['pager']	= $this->pagination->create_links();
            //////////
            
            if($this->session->userdata('section_id') !== false)
                $data['topics'] = $this->forum->getTopicsBySectionId($this->session->userdata('section_id'), -1, $per_page, $from);
            else
                $data['topics'] = $this->forum->getAllTopics(-1, $per_page, $from);
            
            $data['sections']   = $this->forum->getSectionsByParentId(0);
            $this->load->view('admin/forum/topics', $data);
        }
        
        public function topics_add()
        {
            if(isset($_POST['add']) || isset($_POST['add_and_edit']))
            {
                $_POST['name'] = trim($_POST['name']);
                $url = $_POST['url'];
                if($url == '')
                {
                    $this->load->helper('translit_helper');
                    $url = translitRuToEn($_POST['name']);
                    while($this->forum->getSectionByUrlAndParentId($url,$_POST['section_id']))
                        $url .= '-1';
                }
                
                $num = 0;
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
                $title = $_POST['title'];
                if($title == '') $title = $_POST['name'];
                $keywords = $_POST['keywords'];
                if($keywords == '') $keywords = $_POST['name'];
                $description = $_POST['description'];
                if($description == '') $description = $_POST['name'];
                
                $login = "";
                if($this->session->userdata('login') !== false) $login = $this->session->userdata('login');
                
                $dbins = array(
                    'section_id'        => $_POST['section_id'],
                    'name'              => $_POST['name'],
                    'num'               => $num,
                    'title'             => $title,
                    'keywords'          => $keywords,
                    'description'       => $description,
                    'robots'            => $_POST['robots'],
                    'seo'               => $_POST['seo'],
                    'active'            => $active,
                    'descr'             => $_POST['descr'],
                    'url'               => $url,
                    'date'              => $_POST['date'],
                    'time'              => $_POST['time'],
                    'views'             => $_POST['views'],
                    'login'             => $login,
                    'lastmsgdatetime'   => date("Y-m-d H:i:s")
                );
                
                $this->db->insert('forum_topics', $dbins);
                
                $topic = $this->forum->getTopicByUrlAndSectionId($url, $_POST['section_id']);
                
                if($topic)
                {
                    $dbins = array(
                        'topic_id'          => $topic['id'],
                        'message'           => $_POST['message'],
                        'login'             => $login,
                        'date'              => $_POST['date'],
                        'time'              => $_POST['time'],
                        'active'            => $active,
                        'topic_message'     => 1
                    );
                    $this->db->insert('forum_messages', $dbins);
                }
                else
                {
                    echo "Тема не добавлена в базу по неизвестной причине!";
                    die();
                }
                
                if(isset($_POST['add_and_edit']))
                {
                    $topic = $this->forum->getTopicByUrlAndSectionId($url,$_POST['section_id']);
                    if($topic)
                        redirect("/admin/forum/topics/edit/".$topic['id']."/");
                    else
                        echo 'Добавленный раздел не найден в базе! Возможно, он не был добавлен!';
                }
                else
                    redirect("/admin/forum/topics/");
            }
            
            $data['title']  = "Добавление темы форума";
            $data['sections'] = $this->forum->getParentSections(0);
            $this->load->view('admin/forum/topics_add',$data);
        }
        
        public function topics_edit($id)
        {
            $topic = $this->forum->getTopicById($id);
            
            if(isset($_POST['save']) || isset($_POST['save_and_edit']))
            {
                $_POST['name'] = trim($_POST['name']);
                $url = $_POST['url'];
                if($url == '')
                {
                    $this->load->helper('translit_helper');
                    $url = translitRuToEn($_POST['name']);
                    while($this->forum->getSectionByUrlAndParentId($url,$_POST['parent_id']))
                        $url .= '-1';
                }
                
                $num = 0;
                
                /*
                if($_POST['old_parent_id'] != $_POST['parent_id'])
                {
                    $num = $this->forum->getNewSectionNum($_POST['parent_id']);
                }
                */
                
                $active = 0;
                if(isset($_POST['active']) && $_POST['active'] == true) $active = 1;
                $title = $_POST['title'];
                if($title == '') $title = $_POST['name'];
                $keywords = $_POST['keywords'];
                if($keywords == '') $keywords = $_POST['name'];
                $description = $_POST['description'];
                if($description == '') $description = $_POST['name'];
                
                $login = "";
                if($this->session->userdata('login') !== false) $login = $this->session->userdata('login');
                
                $dbins = array(
                    'section_id'        => $_POST['section_id'],
                    'name'              => $_POST['name'],
                    'num'               => $num,
                    'title'             => $title,
                    'keywords'          => $keywords,
                    'description'       => $description,
                    'robots'            => $_POST['robots'],
                    'seo'               => $_POST['seo'],
                    'active'            => $active,
                    'descr'             => $_POST['descr'],
                    'url'               => $url,
                    'date'              => $_POST['date'],
                    'time'              => $_POST['time'],
                    'views'             => $_POST['views'],
                    'login'             => $login,
                    'lastmsgdatetime'   => date("Y-m-d H:i:s")
                );
                
                $this->db->where('id', $id);
                $this->db->update('forum_topics', $dbins);
                
                if(isset($_POST['save_and_edit']))
                {
                    redirect("/admin/forum/topics/edit/".$topic['id']."/");
                }
                else
                    redirect("/admin/forum/topics/");
            }
            
            $data['title']      = "Редактирование темы форума";
            $data['topic']      = $topic;
            $data['section']    = $this->forum->getSectionById($topic['section_id']);
            $data['sections']   = $this->forum->getSectionsByParentId(0);
            $data['message']    = $this->forum->getTopicMessage($topic['id']);
            $this->load->view('admin/forum/topics_edit',$data);
        }
        
        public function topics_del($id)
        {
            $this->db->where('topic_id',$id)->delete('forum_messages');
            $this->db->where('id',$id)->limit(1)->delete('forum_topics');
            
            redirect("/admin/forum/topics/");
        }
        
        public function topics_active($id)
        {
            $topic = $this->forum->getTopicById($id);
            $active = 0;
            if($topic['active'] == 0) $active = 1;
            
            $dbins = array('active' => $active);
            $this->db->where('id', $id);
            $this->db->update('forum_topics', $dbins);
            
            redirect("/admin/forum/topics/");
        }
        
        public function topics_show_only_section_id($section_id = -1)
        {
            if($section_id == -1)
		  $this->session->unset_userdata('section_id');
		
	    
            if(isset($_POST['show_only_section_id']) && $_POST['show_only_section_id'] != -1)
                $this->session->set_userdata('section_id', $_POST['show_only_section_id']);
	    elseif($_POST['show_only_section_id'] == -1)
		  $this->session->unset_userdata('section_id');
            else
                $this->session->set_userdata('section_id', $section_id);
            redirect("/admin/forum/topics/");
        }
	
	public function topics_search()
	{
	  if(isset($_POST['search']))
	  {
		  $search = trim($_POST['search']);
		  $topics = $this->forum->getTopicsNamesLike($search);
		  
		  $data['sections']   = $this->forum->getSectionsByParentId(0);
		  $data['title']	= "Поиск по запросу: ".$search;
		  $data['topics']	= $topics;
		  $data['pager']	= '';
		  $this->load->view('admin/forum/topics', $data);
	  }
	}
        
        
////////////////////////////////////////////////////////////////////////////////
////////////////              MESSAGES             /////////////////////////////
        public function messages()
        {
            $this->load->model('model_users', 'users');
            
            $data['title']  = "Сообщения форума";
            /*
            if($this->session->userdata('section_id') !== false)
		$topics = $this->forum->getTopicsBySectionId($this->session->userdata('section_id'));
	    else
            */
		$messages = $this->forum->getAllMessages();
            
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            
            $per_page = $this->forum->getOption('admin_messages_pagination_per_page');
            if(!$per_page) $per_page = 35;
            
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/forum/messages/';
            $config['total_rows'] = count($messages);
            $config['num_links'] = 4;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'далее';
            $config['prev_link'] = 'назад';
            
            $config['per_page'] = $per_page;
            $config['uri_segment']     = 4;
            $from = intval($this->uri->segment(4));
            $page_number=$from/$per_page+1;
            $this->pagination->initialize($config);
            
            $data['pager']	= $this->pagination->create_links();
            //////////
            
            /*
            if($this->session->userdata('section_id') !== false)
                $data['topics'] = $this->forum->getTopicsBySectionId($this->session->userdata('section_id'), -1, $per_page, $from);
            else
            */
                $data['messages'] = $this->forum->getAllMessages(-1, $per_page, $from);
            
            $data['sections']   = $this->forum->getSectionsByParentId(0);
            $this->load->view('admin/forum/messages', $data);
        }
	
	public function reserve_only()
        {
            $this->load->model('model_users', 'users');
            
            $data['title']  = "Помеченные сообщения форума";
            /*
            if($this->session->userdata('section_id') !== false)
		$topics = $this->forum->getTopicsBySectionId($this->session->userdata('section_id'));
	    else
            */
		$messages = $this->forum->getReserveMessages();
            
            // ПАГИНАЦИЯ //
            $this->load->library('pagination');
            
            $per_page = $this->forum->getOption('admin_messages_pagination_per_page');
            if(!$per_page) $per_page = 35;
            
            $config['base_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/admin/forum/messages/reserve_only/';
            $config['total_rows'] = count($messages);
            $config['num_links'] = 4;
            $config['first_link'] = 'в начало';
            $config['last_link'] = 'в конец';
            $config['next_link'] = 'далее';
            $config['prev_link'] = 'назад';
            
            $config['per_page'] = $per_page;
            $config['uri_segment']     = 5;
            $from = intval($this->uri->segment(5));
            $page_number=$from/$per_page+1;
            $this->pagination->initialize($config);
            
            $data['pager']	= $this->pagination->create_links();
            //////////
            
            /*
            if($this->session->userdata('section_id') !== false)
                $data['topics'] = $this->forum->getTopicsBySectionId($this->session->userdata('section_id'), -1, $per_page, $from);
            else
            */
                $data['messages'] = $this->forum->getReserveMessages(-1, $per_page, $from);
            
            $data['sections']   = $this->forum->getSectionsByParentId(0);
            $this->load->view('admin/forum/messages', $data);
        }
	
	public function messages_search()
	{
	  if(isset($_POST['search']))
	  {
		  $this->load->model('model_users', 'users');
		  
		  $search = trim($_POST['search']);
		  $messages = $this->forum->getMessagesContentLike($search);
		  		  
		  //$data['sections']   = $this->forum->getSectionsByParentId(0);
		  $data['title']	= "Поиск по запросу: ".$search;
		  $data['messages']	= $messages;
		  $data['pager']	= '';
		  $this->load->view('admin/forum/messages', $data);
	  }
	  else err404();
	}
	
	public function messages_search_user()
	{
	  if(isset($_POST['search']))
	  {
		  $this->load->model('model_users', 'users');
		  
		  $search = trim($_POST['search']);
		  $messages = $this->forum->getMessagesFromLogin($search);
		  		  
		  //$data['sections']   = $this->forum->getSectionsByParentId(0);
		  $data['title']	= "Поиск сообщений пользователя ".$search;
		  $data['messages']	= $messages;
		  $data['pager']	= '';
		  $this->load->view('admin/forum/messages', $data);
	  }
	  else err404();
	}
	
	
        
        public function messages_edit($id)
        {
            $data['title']  = "Редактирование сообщения форума";
            
            if(isset($_POST['save']) || isset($_POST['save_and_edit']))
            {
                $dbins = array(
                    'message'               => $_POST['message'],
                    'corrected'             => 1,
                    'corrected_by'          => $this->session->userdata('login'),
                    'corrected_datetime'    => date("Y-m-d H:i:s")
                );
                $this->db->where('id', $id);
                $this->db->update('forum_messages', $dbins);
                
                if(isset($_POST['save_and_edit']))
                    redirect("/admin/forum/messages/edit/".$id."/");
                else
                    redirect('/admin/forum/messages/');
            }
            
            $this->load->model('model_users', 'users');
            
            $message    = $this->forum->getMessageById($id);
            $topic      = $this->forum->getTopicById($message['topic_id']);
            $section    = $this->forum->getSectionById($topic['section_id']);
            $user       = $this->users->getUserByLogin($message['login']);
            $subsection = false;
            if($section['parent_id'] != 0)
            {
                $subsection = $section;
                $section    = $this->forum->getSectionById($subsection['parent_id']);
            }
            $data['message']    = $message;
            $data['topic']      = $topic;
            $data['section']    = $section;
            $data['subsection'] = $subsection;
            $data['user']       = $user;
            $this->load->view('admin/forum/messages_edit',$data);
        }
        
        public function messages_del($id)
        {
            $message = $this->forum->getMessageById($id);
            $this->db->where('id',$id)->limit(1)->delete('forum_messages');
            if($message['topic_message'] == 1)
            {
                $messages = $this->forum->getMessagesByTopicId($message['topic_id']);
                if(!$messages)
                    $this->db->where('id',$message['topic_id'])->limit(1)->delete('forum_topics');
            }
            
            redirect("/admin/forum/messages/");
        }
	
	public function messages_active($id)
        {
            $message = $this->forum->getMessageById($id);
            $active = 0;
            if($message['active'] == 0) $active = 1;
            
            $dbins = array('active' => $active);
            $this->db->where('id', $id);
            $this->db->update('forum_messages', $dbins);
            
            redirect("/admin/forum/messages/");
        }
	
	public function messages_reserve($id)
        {
            $message = $this->forum->getMessageById($id);
            $reserve = 0;
            if($message['reserve'] == 0) $reserve = 1;
            
            $dbins = array('reserve' => $reserve);
            $this->db->where('id', $id);
            $this->db->update('forum_messages', $dbins);
            
            redirect("/admin/forum/messages/");
        }
	
	
	
	//////////////////////////////////////////////////
	//////////////////// OPTIONS /////////////////////
        
        public function options()
	{
            $data['title']  = "Опции форума";
            $data['options'] = $this->forum->getAllOptions();
            $this->load->view('admin/forum/options',$data);
	}
	
	public function options_add()
        {
            $err = false;
            if(isset($_POST['name']))
            {
                $name = trim($_POST['name']);
                $rus = trim($_POST['rus']);
                $value = trim($_POST['value']);
                $adding = trim($_POST['adding']);
                if($name == '')
                    $err['name'] = "Название не может быть пустым!";
                if($rus == '')
                    $err['rus'] = "Описание не может быть пустым!";                
                
                if(!$err)
                {
                    $dbins = array(
                        'name'      => $name,
                        'rus'       => $rus,
                        'value'     => $value,
                        'adding'    => $adding
                    );
                    $this->db->insert('forum_options',$dbins);
                    redirect('/admin/forum/options/');
                }
            }
            $data['title']  = "Добавление опции";
            $data['err'] = $err;            
            $this->load->view('admin/forum/options_add',$data);
        }
	
	public function options_edit($id)
        {
            $err = false;
            if(isset($_POST['save']))
            {
                $name = trim($_POST['name']);
                $rus = trim($_POST['rus']);
                $value = trim($_POST['value']);
                $adding = trim($_POST['adding']);
                if($name == '')
                    $err['name'] = "Название не может быть пустым!";
                if($rus == '')
                    $err['rus'] = "Описание не может быть пустым!";
                                
                if(!$err)
                {
                    $dbins = array(
                        'name'      => $name,
                        'rus'       => $rus,
                        'value'     => $value,
                        'adding'    => $adding
                    );
                    $this->db->where('id', $id)->update('forum_options', $dbins);
                    redirect('/admin/forum/options/');
                }
            }
            $data['option'] = $this->forum->getOptionById($id);	    
            $data['title']  = "Редактирование опции форума";
            $data['err'] = $err;            
            $this->load->view('admin/forum/options_edit',$data);
        }
	
	public function options_del($id)
        {
            $this->db->where('id',$id)->limit(1)->delete('forum_options');
            redirect("/admin/forum/options/");
        }
        
}