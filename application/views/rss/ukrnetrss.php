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
}

$content = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns="http://backend.userland.com/rss2" xmlns:yandex="http://news.yandex.ru">
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

                if($articles)
                {
                    $count = count($articles);
                    for($i = 0; $i < $count; $i++)
                    {
                        $a = $articles[$i];
                        
			$content .= '<item>';
			$a['short_content'] 	= stripslashes($a['short_content']);
			$a['name']		= htmlspecialchars($a['name']);
			$link = '/'.$a['url'].'/';
			$this->db->where('id',$a['category_id']);
			$category = $this->db->get('categories')->result_array();
			$cat = '';
			if($category)
			{
				$category = $category[0];
				$cat = $category['name'];
				
				$link = '/'.$category['url'].$link;
				
				while($category['parent'] != 0)
				{
					$this->db->where('id', $category['parent']);
					$category = $this->db->get('categories')->result_array();
					if($category)
					{
						$category = $category[0];
						$link = '/'.$category['url'].$link;
					}
				}
			}
			
			// ПЕРЕВОДИМ ДАТУ В ФОРМАТ RFC822
			$dt_date = explode('-',$a['date']);
			$dt_time = explode(':',$a['time']);
			$date = date(DATE_RFC822, mktime($dt_time[0],$dt_time[1],0,$dt_date[1],$dt_date[0],$dt_date[2]));
			//
			
			$content .= '
			<title>'.$a['name'].'</title>
			<link>http://'.$_SERVER['SERVER_NAME'].$link.'</link>
			<description><![CDATA[';
			if($a['image'] != '')
				$content .= '<a href="http://'.$_SERVER['SERVER_NAME'].$link.'"><img height="200px" border="0" src="http://'.$_SERVER['SERVER_NAME'].$a['image'].'" /></a>';
			$content .= $a['short_content'].']]></description>
			<pubDate>'.$date.'</pubDate>
			<category>'.$cat.'</category>
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