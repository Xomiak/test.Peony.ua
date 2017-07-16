<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


function mail_send($to,$subject,$msg, $type = "html", $test = false)
{
    $CI = & get_instance();
    $CI->load->library('email');
	
	$config['mailtype'] = $type;
	
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = getOption('smtp_host');
    $config['smtp_user'] = getOption('smtp_user');
    $config['smtp_pass'] = getOption('smtp_pass');
    $config['smtp_port'] = getOption('smtp_port');
    

    
    $CI->email->initialize($config);
    
    $CI->email->from(getOption('smtp_user'), $_SERVER['SERVER_NAME']);
    $CI->email->to($to);
    
    $CI->email->subject($subject);
    $CI->email->message($msg);
    $ret = $CI->email->send();
    
    //$ret = false;
    if(!$ret)
    {
		//var_dump("smtp FALSE!");
		$headers = "";
		if($type == "html")
			$headers = "Content-Type: text/html; charset=utf-8 ";
	
			if(!$test) $ret = mail($to, $subject, $msg, $headers);
    }
	
	//echo $CI->email->print_debugger(); die();
	
    return $ret;
    
}

function createEmail($header = '/upload/email/42ed846af09614f5cb4bf3ffc7cdeb6e.jpg', $name = "Новости от PEONY", $content = "", $articles = false, $adding = false, $footer = false, $no_price = 0, $user = false, $unsubscribeLink = true)
{
    $CI = & get_instance();
    if(!$footer) $footer = getOption('mailer_footer');

    $userHash = '';
    if($user != false){
        $userHash = '?from=email&auto_auth=true&user_id='.$user['id'].'&hash='.md5($user['pass']);
    }
    
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
						<a style="color: #000; text-decoration: none;" href="#"><img  class="deviceWidth" src="http://' . $_SERVER['SERVER_NAME'] . $header . '" alt="" border="0" style="width: 680px; height: auto; display: block; border-radius: 4px;" /></a>
					</td>
				</tr>
                <tr>
                    <td style="font-size: 13px; color: #000; font-weight: normal; text-align: center; font-family: \'Roboto\', Helvetica, sans-serif; line-height: 24px; padding:10px 8px 10px 8px" bgcolor="#fff">
                        <h1 style="font-size: 25px;">' . $name . '</h1>
                        ' . $content . '
                    </td>
                </tr>
			</table><!-- End One Column -->

			<!-- Two Column (Images Stacked over Text) -->
			<table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth" bgcolor="#fff" style="margin:0 auto;">
				<tr>
					<td class="center" style="padding:10px 0 0 5px">
			';

    if($articles) {
        $currensy_grn = getCurrencyValue('UAH');
        $currensy_rub = getCurrencyValue('RUB');
        $count = count($articles);
        $col = 0;
        for ($i = 0; $i < $count; $i++) {
            $email .= '<table width="99%" border="0" cellpadding="0" cellspacing="0" class="deviceWidth" style="margin-bottom: 30px;">
                            <tr>';

            $a = $articles[$i];
            $cat = $CI->model_categories->getCategoryById($a['category_id']);
            $url = '/' . $cat['url'] . '/' . $a['url'] . '/' . $userHash;

            $img = '
				  <td align="center" valign="top" width="49%">
						   <p style="mso-table-lspace:0;mso-table-rspace:0; margin:0">
							   <a style="color: #000; text-decoration: none;" href="//'. $_SERVER['SERVER_NAME'] . $url . '">
								   <img width="267" src="//'. $_SERVER['SERVER_NAME'] . $a['image'] . '" alt="" style="width: 267px; border: 1px solid #d4d4d4; border-radius: 5px;" class="deviceWidth" />								   
							   </a>
						   </p>
				  </td>
				  ';

            $descr = '
				  <td width="49%" valign="top">
				    <table width="100%">
				        <tr>
				            <td style="border-bottom: 1px solid #d5d5d5; font-size: 13px;" valign="top">
				                
						        <h2><a style="color: #000; text-decoration: none;font-weight: normal" href="//' . $_SERVER['SERVER_NAME'] . $url . '">' . $cat['name_one'] . ' <b style="font-weight: bolder">' . $a['name'] . '</b></a> &nbsp;&nbsp;&nbsp;<span style="color: #842841; font-size: 26px;">'.$a['discount'].'%</span></h2>
						        
						   ' . $a['content'] .
                '</td>
                            <td>
                        </tr>
                     </table>';

//            $descr .= '<hr size="color:#d5d5d5; background-color:#d5d5d5; background:#d5d5d5; height: 1px; border-top: 1px solid #d5d5d5;" />';
            if ($no_price != 1) {
                $a['old_price'] = $a['price'];
                $a['price'] = getNewPrice($a['price'], $a['discount']);

                if ($a['discount'] > 0) {
                    $descr .= '
                                <table width="100%" style="text-decoration: line-through; color: #444; font-size: 14px; font-style: italic; font-weight: 300;">
								   <tr>
									   <td style="padding: 5px;">
										   <i>' . $a['old_price'] . ' $</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>' . round($a['old_price'] * $currensy_grn, 2) . ' грн</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>' . round($a['old_price'] * $currensy_rub, 2) . ' р</i>
									   </td>
								   </tr>
							   </table>';
                }
                $descr .= '
							   <table width="100%" style="color: #842841; font-size: 18px; font-style: italic; font-weight: 700;">
								   <tr>
									   <td style="padding: 5px;">
										   <i>' . $a['price'] . ' $</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>' . round($a['price'] * $currensy_grn, 2) . ' грн</i>
									   </td>
									   <td style="padding: 5px;">
										   <i>' . round($a['price'] * $currensy_rub, 2) . ' р</i>
									   </td>
								   </tr>
							   </table>
							   ';
            }

            $descr .= '<br />
						   <a style="color: #000; text-decoration: none;" href="//'. $_SERVER['SERVER_NAME'] . $url . '">
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

            if ($col == 0) {
                $email .= $img . $descr;
                $col = 1;
            } else {
                $email .= $descr . $img;
                $col = 0;
            }

            $email .= '</tr>
                        </table>';
        }
    }
    $email .= '<hr color="#aaa" />' . $footer;

    if($unsubscribeLink){
        $email .= '<p style="font-size: 10px; text-align:center;">Отписаться от рассылки Вы можете в своём <a href="http://'.$_SERVER['SERVER_NAME'].'/user/edit-mypage/'.$userHash.'"><b>личном кабинете</b></a>.</p>';
    }

    $email .='</td>
				</tr>
			</table><!-- End Two Column (Images Stacked over Text) -->
    </body>
</html>';

    return $email;
}


