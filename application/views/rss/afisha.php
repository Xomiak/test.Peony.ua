<?php
function cut_str($str, $pos) {
	$str = preg_replace('/&#?[a-z0-9]{2,8};/i', '', strip_tags(htmlspecialchars_decode($str)));
	$str = preg_replace('/&/i', '', $str);
	if (!empty($str)){
		$temppos = 0;
		
		for ($i = 0; $i < $pos; $i++){
			
			$temppos2 = strpos($str, '.', $temppos);
			if ($temppos <= $temppos2){
				$temppos = strpos($str, '.', $temppos) + 1;
			}
			else/*if (strlen($str) < $temppos)*/
				return $str;
		
		}	
		return substr($str, 0, $temppos);
		
	}
	else 
		return '';
}$content = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
                <title>'.$rss_channal_title.'</title>
		<link>http://'.$_SERVER['SERVER_NAME'].'/</link>
		<description>'.$rss_channal_description.'</description>
		<language>ru</language>
		<copyright>Copyright '.date("Y").', '.$_SERVER['SERVER_NAME'].'</copyright>
		<img>
			<url>http://'.$_SERVER['SERVER_NAME'].'/img/rss_logo.png</url>
			<title>'.$_SERVER['SERVER_NAME'].' - '.$rss_channal_description.'</title>
			<link>http://'.$_SERVER['SERVER_NAME'].'</link>
		</img>
';

                if($schedule)
                {
                    $count = count($schedule);
                    for($i = 0; $i < $count; $i++)
                    {
                        $a = $schedule[$i];
                        $this->db->where('id', $a['afisha_id']);
                        $this->db->limit(1);
                        $afisha = $this->db->get('afisha')->result_array();
                        if($afisha) $afisha = $afisha[0];
                        
			$content .= '<item>';
			$a['short_content'] 	= stripslashes($afisha['short_content']);
			$a['name']		= htmlspecialchars($afisha['name']);
			$link = '/afisha/'.$afisha['url'].'/';

			
			// ПЕРЕВОДИМ ДАТУ В ФОРМАТ RFC822
			$dt_date = explode('-',$a['date']);
			$dt_time = explode(':',$a['time']);
			$date = date(DATE_RFC822, $a['date_unix']);
			//
                        
                        $afisha['image'] = str_replace('/afisha/', '/original/', $afisha['image']);
			
			$content .= '
			<title>'.$a['name'].'</title>
			<link>http://'.$_SERVER['SERVER_NAME'].$link.'</link>
			<description><![CDATA[';
			if($afisha['image'] != '')
				$content .= '<a href="http://'.$_SERVER['SERVER_NAME'].$link.'"><img height="200px" border="0" src="http://'.$_SERVER['SERVER_NAME'].$afisha['image'].'" /></a>';
			$content .= $a['short_content'].'Дата: '.$a['date'].' '.$a['time'].']]></description>
                        
			<pubDate>'.$date.'</pubDate>
			
';
                        
                        $content .= '</item>
';
                    }
                    
                    $content .=
                    '
                    </channel>
</rss>
                    ';
                    echo $content;                    

                }
                ?>