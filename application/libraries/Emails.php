<?php
class Emails {
    var $CI;
    var $header = "http://peony.ua/img/logo.png";
    var $name = "";
    var $subject = "";
    var $content = "";
    var $footer = "";
    var $articles = false;

    public function __construct($config = array())
    {
        $this->CI =& get_instance();

        $this->footer = getOption('mailer_footer');
    }

    public function setHeader($val)
    {
        $this->header = $val;
    }
    public function setName($val)
    {
        $this->name = $val;
    }
    public function setContent($val)
    {
        $this->content = $val;
    }
    public function setFooter($val)
    {
        $this->footer = $val;
    }
    public function setArticles($val)
    {
        $this->articles = $val;
    }
    public function setSubject($val)
    {
        $this->subject = $val;
    }

    public function send($to)
    {
        $this->CI->load->helper('mail_helper');
        return mail_send($to, $this->subject, $this->createEmail());
    }

// Создаём вёрстку письма
    public function createEmail()
    {
        $header     = $this->header;
        $name       = $this->name;
        $content    = $this->content;
        $footer     = $this->footer;
        $articles   = $this->articles;

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
                        <a style="color: #000; text-decoration: none;text-align:center;" href="http://'.$_SERVER['SERVER_NAME'].'"><img  class="deviceWidth" src="'.$header.'" alt="" border="0" style="text-align:center; width: auto; height: auto; display: block; " /></a>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #000; font-weight: normal; text-align: center; font-family: \'Roboto\', Helvetica, sans-serif; line-height: 24px; padding:10px 8px 10px 8px" bgcolor="#fff">
                        <h1 style="font-size: 24px; font-weight: normal;">'.$name.'</h1>
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
            $currensy_grn = $this->CI->model_options->getOption('usd_to_uah');
            $currensy_rub = $this->CI->model_options->getOption('usd_to_rur');
            $count = count($articles);
            $col = 0;
            $email .= '<h3>Последние поступления</h3>
                      <table width="99%" border="0" cellpadding="0" cellspacing="0" class="deviceWidth" style="margin-bottom: 15px;">';
            for($i = 0; $i < $count; $i++)
            {
                $a = $articles[$i];
                $cat = $this->CI->model_categories->getCategoryById($a['category_id']);
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