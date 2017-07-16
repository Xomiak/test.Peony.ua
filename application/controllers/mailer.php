<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailer extends CI_Controller {
	public function __construct()
        {
            parent::__construct();
	    $this->load->helper('login_helper');
            $this->load->model('Model_articles','articles');
            $this->load->model('Model_shop','shop');
            $this->load->model('Model_categories','categories');
            $this->load->model('Model_users','users');
            $this->load->model('Model_options','options');
            $this->load->model('Model_mailer','mailer');
            $this->load->helper('mail_helper');
        }
	
        public function index()
        {
            if($this->mailer->getOption('send_emails') != 0)
            {
                $last = $this->mailer->getLastMailer();
                if(!$last) $this->send();
                else
                {
                    $date_now = date("Y-m-d");
                    $mk_now = mktime();
                    $darr = explode('-',$last['date']);
                    $tarr = explode(':',$last['time']);
                    $mk_last = mktime($tarr[0],$tarr[1],0,$darr[1],$darr[2],$darr[0]);
                    $diff = $mk_now - $mk_last;
                    $days =  intval($diff/60/60/24);
                    if($this->mailer->getOption('sending_frequency') < $days)
                    {
                        $this->send();
                    }
                    else echo 'Ещё рано!';
                    echo $days;
                }
            }
            else echo 'Рассылка отключена!';
        }
        
	public function send($message, $subject)
	{
        $this->load->helper('mail_helper');
        $users = $this->users->getUsersForMailer();
        foreach($users as $user)
        {
            $message = str_replace('[name]', $user['name'], $message);
            mail_send($user['email'],$subject,$message);
        }
    }


        public function bd_mailing()
        {
            // РАССЫЛКА ПОЗДРАВЛЕНИЙ С ДР!!!!
            $mailer_bd_on = getOption('mailer_bd_on');
            if(isset($_GET['date'])) $mailer_bd_on = 1;

            if($mailer_bd_on == 1)   // проверяем, вкелючена ли рассылка поздравления с ДР
            {
                if(isset($_GET['date']))
                    $users = $this->users->getUsersByBdDate($_GET['date']);
                else
                    $users = $this->users->getUsersByBdDateNow();

                if($users)
                {
                    $mailer_bd_header = getOption('mailer_bd_header');
                    $mailer_bd_footer = getOption('mailer_footer');

                    

                    $count = count($users);
                    for($i = 0; $i < $count; $i++)
                    {
                        $user = $users[$i];
                        $user_type = $this->users->getUserTypeById($user['user_type_id']);

                        $code = false;
                        if($user_type['bd_mailing'] == 1)
                        {
                            $content = $user_type['congratulation'];
                            if($user_type['discount'] > 0)
                            {
                                $params['discount'] = $user_type['discount'];
                                $params['start_date'] = date("Y-m-d");
                                $params['end_date'] = date("Y-m-d", mktime(date('H'), date('i'), date('s'), date('m'), date('d')+14, date('y'))); 
                                $params['user_login'] = $user['login'];
                                $params['info'] = "Подарок на день рождения: Скидка в ".$user_type['discount'].'% на одноразовую покупку в течении 14 дней.';
                                $params['gived_by'] = "cron";
                                $code = createCoupon($params);
                                $discount_code = '<h2 style="text-align: center;">Код купона: <span style="color: Red">'.$code.'</span></h2>';
                                $content = str_replace('[discount_code]', $discount_code, $content);
                            }

                            $articles = $this->shop->getArticles(8,0,'DESC',1,true);
                            //vdd($articles);

                            $message = $this->createEmailShort($mailer_bd_header, $user['name'].", PEONY поздравляет Вас с Днём рождения!", $content, $mailer_bd_footer, $articles);
                           // echo $message;
                           // die();
                        }
                    }
                }
            }
        }

    public function mailerSend($type)
    {
        $template = getOption('mailer_'.$type.'_template');
        $header = getOption('mailer_'.$type.'_header');
        $subject = getOption('mailer_'.$type.'_subject');
        $articles = $this->shop->getForMailer($type);
        $footer = getOption('mailer_footer');
        $active = getOption('mailer_'.$type.'_active');

        if(!$active) die(); // Проверяем, включена ли рассылка
        if($type == 'new' && $articles == false) // Проверяем, есть ли новинки
            die();


        if(!$subject) $subject = "Сообщение от PEONY";


        if($template != false && $header != false)
        {
            $message = $this->createEmail($header, $subject, $template, $footer,$articles);
            echo $message;
            //$this->send($message, $subject);
        }
    }


    private function createEmail($header, $name, $content, $footer, $articles = false, $no_price = 0)
    {
        $email = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <style type="text/css">
            h2
            {
                font-family: "Roboto","Roboto",Helvetica,sans-serif;
            }
            .ReadMsgBody {width: 100%; background-color: #ffffff;}
            .ExternalClass {width: 100%; background-color: #ffffff;}
            body	 {width: 100%; /* background-color: #ffffff; */ margin:0; padding:0; -webkit-font-smoothing: antialiased;font-family: "Roboto",Georgia, Times, serif}
            table {border-collapse: collapse;}
			a{ color: #000; text-decoration: none;}

            @media only screen and (max-width: 640px)  {
                            body[yahoo] .deviceWidth {width:440px!important; padding:0;}
                            body[yahoo] .center {text-align: center!important;}
                    }

            @media only screen and (max-width: 479px) {
                            body[yahoo] .deviceWidth {width:280px!important; padding:0;}
                            body[yahoo] .center {text-align: center!important;}
                    }
        </style>
    </head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: \'Roboto\', Helvetica, sans-serif;">
       <!-- One Column -->
			<table width="580"  class="deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eeeeed" style="margin:0 auto;">
				<tr>
					<td valign="top" style="padding:0" bgcolor="#ffffff">
						<a style="color: #000; text-decoration: none;" href="#"><img  class="deviceWidth" src="http://'.$_SERVER['SERVER_NAME'].$header.'" alt="" border="0" style="width: 680px; height: auto; display: block; border-radius: 4px;" /></a>
					</td>
				</tr>
                <tr>
                    <td style="font-size: 13px; color: #000; font-weight: normal; text-align: center; font-family: \'Roboto\', Helvetica, sans-serif; line-height: 24px; padding:10px 8px 10px 8px" bgcolor="#fff">
                        <h1 style="font-size: 38px;">'.$name.'</h1>
                        '.$content.'
                    </td>
                </tr>
			</table><!-- End One Column -->

			<!-- Two Column (Images Stacked over Text) -->
			<table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth" bgcolor="#fff" style="margin:0 auto;">
				<tr>
					<td class="center" style="padding:10px 0 0 5px">
			';

        $currensy_grn = $this->model_options->getOption('usd_to_uah');
        $currensy_rub = $this->model_options->getOption('usd_to_rur');
        $count = count($articles);
        $col = 0;
        for($i = 0; $i < $count; $i++)
        {
            $email .= '<table width="99%" border="0" cellpadding="0" cellspacing="0" class="deviceWidth" style="margin-bottom: 30px;">
                            <tr>';

            $a = $articles[$i];
            $cat = $this->categories->getCategoryById($a['category_id']);
            $url = '/'.$cat['url'].'/'.$a['url'].'/';

            $img = '
				  <td align="center" valign="top" width="49%">
						   <p style="mso-table-lspace:0;mso-table-rspace:0; margin:0">
							   <a style="color: #000; text-decoration: none;" href="http://www.peony.ua'.$url.'">
								   <img width="267" src="http://www.peony.ua'.$a['image'].'" alt="" style="width: 267px; border: 1px solid #d4d4d4; border-radius: 5px;" class="deviceWidth" />
							   </a>
						   </p>
				  </td>
				  ';

            $descr = '
				  <td width="49%" valign="top">
				    <table width="100%">
				        <tr>
				            <td style="border-bottom: 1px solid #d5d5d5; font-size: 13px;" valign="top">
						        <h2><a style="color: #000; text-decoration: none;font-weight: normal" href="http://www.peony.ua'.$url.'">'.$cat['name_one'].' <b style="font-weight: bolder">'.$a['name'].'</b></a></h2>
						   '.$a['content'].
                            '</td>
                            <td>
                        </tr>
                     </table>';

//            $descr .= '<hr size="color:#d5d5d5; background-color:#d5d5d5; background:#d5d5d5; height: 1px; border-top: 1px solid #d5d5d5;" />';
            if($no_price != 1)
            {
                $a['old_price'] = $a['price'];
                $a['price'] = getNewPrice($a['price'], $a['discount']);

                if($a['discount'] > 0)
                {
                    $descr .= '
                                <table width="100%" style="text-decoration: line-through; color: #444; font-size: 14px; font-style: italic; font-weight: 300;">
								   <tr>
									   <td style="padding: 5px;">
										   <i>'.$a['old_price'].' $</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>'.($a['old_price'] * $currensy_grn).' грн</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>'.($a['old_price'] * $currensy_rub).' р</i>
									   </td>
								   </tr>
							   </table>';
                }
                $descr .= '
							   <table width="100%" style="color: #842841; font-size: 18px; font-style: italic; font-weight: 700;">
								   <tr>
									   <td style="padding: 5px;">
										   <i>'.$a['old_price'].' $</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>'.($a['old_price'] * $currensy_grn).' грн</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>'.($a['old_price'] * $currensy_rub).' р</i>
									   </td>
								   </tr>
							   </table>
							   ';
            }

            $descr .= '<br />
						   <a style="color: #000; text-decoration: none;" href="http://www.peony.ua'.$url.'">
							   <table>
								   <tr>
									   <td style="background-color: #842841; color: #fff; padding: 15px; font-size: 18px">
										   ПОДРОБНЕЕ
									   </td>
								   </tr>
							   </table>
						   </a>
				  </td>
				  ';

            if($col == 0)
            {
                $email .= $img.$descr;
                $col = 1;
            }
            else
            {
                $email .= $descr.$img;
                $col = 0;
            }

            $email .= '</tr>
                        </table>';
        }

        $email .= '<hr color="#aaa" />'.$footer.'
						<p style="font-size: 10px; text-align:center;">Отписаться от рассылки Вы можете в своём <a href="http://www.peony.ua/user/edit-mypage/"><b>личном кабинете</b></a>.</p>
					</td>
				</tr>
			</table><!-- End Two Column (Images Stacked over Text) -->
    </body>
</html>';

        return $email;
    }

        private function createEmailShort($header, $name, $content, $footer, $articles = false)
         {
                  $email = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <style type="text/css">
            .ReadMsgBody {width: 100%; background-color: #ffffff;}
            .ExternalClass {width: 100%; background-color: #ffffff;}
            body     {width: 100%; /* background-color: #ffffff; */ margin:0; padding:0; -webkit-font-smoothing: antialiased;font-family: Georgia, Times, serif}
            table {border-collapse: collapse;}
            a{ color: #000; text-decoration: none;}
        
            @media only screen and (max-width: 640px)  {
                            body[yahoo] .deviceWidth {width:440px!important; padding:0;}
                            body[yahoo] .center {text-align: center!important;}
                    }
        
            @media only screen and (max-width: 479px) {
                            body[yahoo] .deviceWidth {width:280px!important; padding:0;}
                            body[yahoo] .center {text-align: center!important;}
                    }        
        </style>
    </head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: \'Roboto\', Helvetica, sans-serif;">
       <!-- One Column -->
            <table width="580"  class="deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eeeeed" style="margin:0 auto;">
                <tr>
                    <td valign="top" style="padding:0" bgcolor="#ffffff">
                        <a style="color: #000; text-decoration: none;" href="#"><img  class="deviceWidth" src="'.$header.'" alt="" border="0" style="width: 680px; height: auto; display: block; border-radius: 4px;" /></a>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #000; font-weight: normal; text-align: center; font-family: \'Roboto\', Helvetica, sans-serif; line-height: 24px; padding:10px 8px 10px 8px" bgcolor="#fff">
                        <h1 style="font-size: 24px;">'.$name.'</h1>
                        '.$content.'
                    </td>
                </tr>
            </table><!-- End One Column -->


            <!-- Two Column (Images Stacked over Text) -->
            <table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth" bgcolor="#fff" style="margin:0 auto;">
                <tr>
                    <td class="center" style="padding:10px 0 0 5px">
            ';

            if($articles)
            {
                $currensy_grn = $this->model_options->getOption('usd_to_uah');
                $currensy_rub = $this->model_options->getOption('usd_to_rur');
                $count = count($articles);
                $col = 0;
                $email .= '<h3>Последние поступления</h3>
                      <table width="99%" border="0" cellpadding="0" cellspacing="0" class="deviceWidth" style="margin-bottom: 15px;">';
                for($i = 0; $i < $count; $i++)
                {
                      $a = $articles[$i];
                      $cat = $this->categories->getCategoryById($a['category_id']);
                      $url = '/'.$cat['url'].'/'.$a['url'].'/';

                      if($i%4 == 0) $email .= "<tr>";
                      $email .= '
                      <td align="center" valign="top" width="25%">
                               <p style="mso-table-lspace:0;mso-table-rspace:0; margin:0">
                                   <a style="color: #000; text-decoration: none;" href="http://www.peony.ua'.$url.'">
                                       <img width="150" src="http://www.peony.ua'.$a['image'].'" alt="" border="0" style="width: 150px" class="deviceWidth" />
                                       '.$a['name'].' ('.$a['color'].')
                                   </a>
                               </p>
                      </td>
                      ';
                      if($i%4 == 3) $email .= "</tr>";


                }
                $email .= '</table>';
            }

            $email .= '<hr color="#aaa" />'.$footer.'

    </body>
</html>';

         return $email;
         }
}