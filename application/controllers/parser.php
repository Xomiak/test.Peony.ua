<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parser extends CI_Controller {
	public function index()
	{
		//$this->load->view('welcome_message');
	}
        
        
        public function goroskop()
        {
            $horo = simplexml_load_string(file_get_contents('http://img.ignio.com/r/export/utf/xml/daily/com.xml'));

            $aries          = $horo->aries->today;
            $taurus         = $horo->taurus->today;
            $gemini         = $horo->gemini->today;
            $cancer         = $horo->cancer->today;
            $leo            = $horo->leo->today;
            $virgo          = $horo->virgo->today;
            $libra          = $horo->libra->today;
            $scorpio        = $horo->scorpio->today;
            $sagittarius    = $horo->sagittarius->today;
            $capricorn      = $horo->capricorn->today;
            $aquarius       = $horo->aquarius->today;
            $pisces         = $horo->pisces->today;

            
            $table = '
            <TABLE class="rowstyle-alt no-arrow sortcompletecallback-staticData-redraw" 
            id=theTable border=0>
            <TBODY>
			<TR>
            <TH><IMG alt="" src="/siteimg/goroskop/oven.png" border=0></TH>
            <TD>
            <P align=center><STRONG>ОВЕН</STRONG> <STRONG>(21 марта - 20 
            апреля)&nbsp;</STRONG></P>'.$aries.'</TD></TR>
            <TR class=alt>
            <TH><IMG alt="" src="/siteimg/goroskop/telec.png" border=0></TH>
            <TD>
            <P align=center><STRONG>ТЕЛЕЦ</STRONG> <STRONG>(21 апреля - 21 
            мая)&nbsp;</STRONG></P>'.$taurus.'</TD></TR>
            <TR>
            <TH><IMG alt="" src="/siteimg/goroskop/blizneci.png" border=0></TH>
            <TD>
            <P align=center><STRONG>БЛИЗНЕЦЫ</STRONG> <STRONG>(22 мая - 21 
            июня)&nbsp;</STRONG></P>'.$gemini.'</TD></TR>
            <TR class=alt>
            <TH><IMG alt="" src="/siteimg/goroskop/rak.png" border=0></TH>
            <TD>
            <P align=center><STRONG>РАК</STRONG> <STRONG>(22 июня - 23 
            июля)&nbsp;</STRONG></P>'.$cancer.'
            </TD></TR>
            <TR>
            <TH><IMG alt="" src="/siteimg/goroskop/lev.png" border=0></TH>
            <TD>
            <P align=center><STRONG>ЛЕВ</STRONG> <STRONG>(24 июля - 23 
            августа)</STRONG></P>'.$leo.'</TD></TR>
            <TR class=alt>
            <TH><IMG alt="" src="/siteimg/goroskop/deva.png" border=0></TH>
            <TD>
            <P align=center><STRONG>ДЕВА</STRONG> <STRONG>(24 августа - 23 
            сентября)</STRONG></P>'.$virgo.'</TD></TR>
            <TR>
            <TH><IMG alt="" src="/siteimg/goroskop/vesi.png" border=0></TH>
            <TD>
            <P align=center><STRONG>ВЕСЫ</STRONG> <STRONG>(24 сентября - 23 
            октября)</STRONG></P>'.$libra.'</TD></TR>
            <TR class=alt>
            <TH><IMG alt="" src="/siteimg/goroskop/scorpion.png" border=0></TH>
            <TD>
            <P align=center><STRONG>СКОРПИОН</STRONG> <STRONG>(24 октября - 22 
            ноября)</STRONG></P>'.$scorpio.'</TD></TR>
            <TR>
            <TH><IMG alt="" src="/siteimg/goroskop/strelec.png" border=0></TH>
            <TD>
            <P align=center><STRONG>СТРЕЛЕЦ</STRONG> <STRONG>(23 ноября - 21 
            декабря)</STRONG></P>'.$sagittarius.'</TD></TR>
            <TR class=alt>
            <TH><IMG alt="" src="/siteimg/goroskop/kozerog.png" border=0></TH>
            <TD>
            <P align=center><STRONG>КОЗЕРОГ</STRONG> <STRONG>(22 декабря - 20 
            января)&nbsp;</STRONG></P>'.$capricorn.'</TD></TR>
            <TR>
            <TH><IMG alt="" src="/siteimg/goroskop/vodoley.png" border=0></TH>
            <TD>
            <P align=center><STRONG>ВОДОЛЕЙ</STRONG> <STRONG>(21 января - 19 
            февраля)</STRONG></P>'.$aquarius.'</TD></TR>
            <TR class=alt>
            <TH><IMG alt="" src="/siteimg/goroskop/ribi.png" border=0></TH>
            <TD>
            <P align=center><STRONG>РЫБЫ</STRONG> <STRONG>(20 февраля - 20 
            марта)</STRONG></P>'.$pisces.'</TD></TR>
            <TR>
            <TD align=middle colSpan=2><A href="/siteimg/goroskop/goroskop.rar">СКАЧАТЬ 
            ГОРОСКОП НА ГОД</A> </TD></TR></TBODY></TABLE>
            ';
            
            
            
            $this->db->where('name', 'Гороскоп');
            $this->db->where('url', 'goroskop');
            $this->db->where('category_id', '13');
            $article = $this->db->get('articles')->result_array();
            if($article)
            {
                $dbins = array(
                    'content'   => $table
                );
                $this->db->where('id',$article[0]['id']);
                $this->db->update('articles',$dbins);
            }
        }
	
	public function recipes()
	{
		$this->db->order_by('name','random');
		$this->db->limit(1);
		$rec = $this->db->get('recipes')->result_array();
		if($rec)
		{
			$qq = $rec[0];
		}
		$cont = $qq['content'];
		$pos = stripos($cont, '<img');
		if($pos)
		{
		       
		       $pos = strpos($cont,'>',$pos);
		       if($pos)
		       {
			       //var_dump($pos);die();
			       $pos = $pos + 1;
			       $res = substr($cont, 0 , $pos);
			       $res .= '<p>&nbsp;</p>';
			       $res .= substr($cont, $pos);
			       $cont = $res;
		       }
		}
		
		$content = '<h2 align="center">'.$qq['name'].'</h2><br />'.$cont;
		
		$this->db->where('name', 'Рецепт дня');
		$this->db->where('url', 'recipe');
		$this->db->where('category_id', '13');
		$article = $this->db->get('articles')->result_array();
		if($article)
		{
		    $dbins = array(
			'content'   => $content
		    );
		    $this->db->where('id',$article[0]['id']);
		    $this->db->update('articles',$dbins);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */