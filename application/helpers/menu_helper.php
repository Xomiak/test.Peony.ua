<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function showSlider($name, $noJs = false)
{	?>
<!--	<link rel="stylesheet" type="text/css" media="all" href="//slider.peony.ua/revslider/public/assets/css/settings.css"/>-->
<!--	<script type="text/javascript" src="//slider.peony.ua/assets/js/includes/jquery/jquery.js"></script>-->
<!--	<script type="text/javascript" src="//slider.peony.ua/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>-->
<!--	<script type="text/javascript" src="//slider.peony.ua/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>-->
<!--	<script type="text/javascript" id="revslider_script" src="//slider.peony.ua/assets/js/revslider.js"></script>-->
<!---->
<!--	<div class="revslider" data-alias="new2"></div>-->
<!--	-->
<!--	<link rel="stylesheet" type="text/css" media="all" href="/includes/revslider/revslider/public/assets/css/settings.css" />-->
<!--	<script type="text/javascript" src="/includes/revslider/assets/js/includes/jquery/jquery.js"></script>-->
<!--	<script type="text/javascript" src="/includes/revslider/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>-->
<!--	<script type="text/javascript" src="/includes/revslider/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>-->
<!--	<script type="text/javascript" id="revslider_script" src="/includes/revslider/assets/js/revslider.js"></script>-->
<!--	<div class="revslider" data-alias="new2"></div>-->
	<?=file_get_contents("https://slider.peony.ua/slider.php")?>

	<?php

}

function showTopMenu()
{
    $CI = & get_instance();
    
    $CI->db->where('type','top');
    $CI->db->where('parent_id','0');
    $CI->db->where('active','1');
    $CI->db->order_by('num','ASK');
    $menu = $CI->db->get('menus')->result_array();
    if($menu)
    {
	?>
	<ul id="menu" class = "animenu__nav">
		<li class="home-page">
			<a href = "/"><span class = "icon-home2"></span></a>
		</li>

	    <?php
	    $count = count($menu);
	    for($i = 0; $i < $count; $i++)
	    {
		$m = $menu[$i];
		?>
		<li<?php if(strpos($_SERVER['REQUEST_URI'], $m['url']) !== false) echo ' class="active"'; ?>>
			<?php
			if($m['url'] != '#')
			{
				?>
				<a href="//<?=$_SERVER['SERVER_NAME']?><?=$m['url']?>">
				<?php
			}
			?>

				<?=$m['name']?>

			<?php
			if($m['url'] != '#')
			{
				?>
				</a>
				<?php
			}
			?>
		<?php
		
		$CI->db->where('type','top');
		$CI->db->where('parent_id',$m['id']);
		$CI->db->where('active','1');
		$CI->db->order_by('num','ASK');
		$submenu = $CI->db->get('menus')->result_array();
		if($submenu)
		{
		    ?>
			<ul  class = "animenu__nav__child">
			<?php
			$subcount = count($submenu);
			for($j = 0; $j < $subcount; $j++)
			{
			    $sm = $submenu[$j];
			    ?>
			    <li>
				<a href="//<?=$_SERVER['SERVER_NAME']?><?=$sm['url']?>"><?=$sm['name']?></a>
			    </li>
			    <?php
			}
			?>
			</ul>

		    <?php
		}
		
		?>
		</li>
		<?php
	    }
	    ?>
	</ul>
	<?php
    }
}

function showTopMenu2()
{
    $CI = & get_instance();
    
    $CI->db->where('type','top2');
    $CI->db->where('parent_id','0');
    $CI->db->where('active','1');
    $CI->db->order_by('num','ASK');
    $menu = $CI->db->get('menus')->result_array();
    if($menu)
    {
	?>
	<nav class="cl-effect-17" style="float:left;">
		<?php
		$count = count($menu);
		for($i = 0; $i < $count; $i++)
		{
		    $m = $menu[$i];
		    ?>

			<a <?php if(strpos($_SERVER['REQUEST_URI'], $m['url']) !== false){ if(($i+1) == $count) echo ' class="active last"'; else echo ' class="active"'; }?> href="<?=$m['url']?>" data-hover="<?=$m['name']?>"<?php if(($i+1) == $count) echo ' class="last"'; ?>><?=$m['name']?></a><br />

		    <?php
		}
		?>
	</nav>

	<?php
    }
}


function showLeftMenu()
{
    $CI = & get_instance();
    
    $CI->db->where('type','left');
    $CI->db->where('subtype','first');
    $CI->db->where('parent_id',0);
    $CI->db->where('active',1);
    $first = $CI->db->get('menus')->result_array();
    if($first)
    {
	$count = count($first);
	for($i = 0; $i < $count; $i++)
	{
	    $f = $first[$i];
	    echo '<p class="left_menu_p"><a class="left_menu';
	    echo '" href="//'.$_SERVER['SERVER_NAME'].$f['url'].'" title="'.$f['name'].'">';
	    echo $f['name'].'</a></p>';
	    
	    // Получаем и выводим детей
	    $CI->db->where('parent_id',$f['id']);
	    $CI->db->where('active',1);
	    $first2 = $CI->db->get('menus')->result_array();
	    if($first2)
	    {
		$count2 = count($first2);
		for($i2 = 0; $i2 < $count2; $i2++)
		{
		    $f2 =$first2[$i2];
		    echo '<p class="left_menu_p"><span style="margin-left: 30px;">&nbsp;└&nbsp;</span><a class="left_menu';
		    echo '" href="//'.$_SERVER['SERVER_NAME'].$f2['url'].'" title="'.$f2['name'].'">';
		    echo $f2['name'].'</a></p>';
		}
	    }
	}
    }
    
      
    $CI->db->where('name', 'left_menu_type');
    $CI->db->limit(1);
    $left_menu_type = $CI->db->get('options')->result_array();
    if($left_menu_type) $left_menu_type = $left_menu_type[0]['value'];
    if(!$left_menu_type) $left_menu_type = 'menus';
    
    $parent_name = "parent";
    if($left_menu_type == "menus") $parent_name = "parent_id";
    
    //$CI->db->where('type','left');
    $CI->db->where('active','1');
    $CI->db->where($parent_name,'0');
    
    if($left_menu_type == 'categories') $CI->db->where('show_in_menu','1');
    
    $CI->db->order_by('num','ASK');
    $menu = $CI->db->get($left_menu_type)->result_array();
    if($menu)
    {
//	echo '<div class="left_menu">';
	$count = count($menu);
	for($i = 0; $i < $count; $i++)
	{
	    echo '<p class="left_menu_p"><a class="left_menu';
	    if($_SERVER['REQUEST_URI'] == $menu[$i]['url']) echo '_current';
	    if($left_menu_type == 'menus')
		echo '" href="//'.$_SERVER['SERVER_NAME'].$menu[$i]['url'].'" title="'.$menu[$i]['name'].'">';
	    else    echo '" href="//'.$_SERVER['SERVER_NAME'].'/'.$menu[$i]['url'].'/" title="'.$menu[$i]['name'].'">';
		
	    echo $menu[$i]['name'].'</a></p>';
////////////////////////////////// 2 ////////////	    
	    //$CI->db->where('type','left');
	    $CI->db->where('active','1');
	    $CI->db->where($parent_name,$menu[$i]['id']);
	    if($left_menu_type == 'categories') $CI->db->where('show_in_menu','1');
	    $CI->db->order_by('num','ASK');
	    $child = $CI->db->get($left_menu_type)->result_array();
	    if($child)
	    {
		$ccount = count($child);
		for($j = 0; $j < $ccount; $j++)
		{
		    $ch = $child[$j];
		    echo '<p class="left_menu_p"><span style="margin-left: 10px;">&nbsp;└&nbsp;</span><a class="left_menu';
		    if($_SERVER['REQUEST_URI'] == $ch['url']) echo '_current';
		    
		    if($left_menu_type == 'menus')
			echo '" href="//'.$_SERVER['SERVER_NAME'].$ch['url'].'" title="'.$ch['name'].'">';
		    else
			echo '" href="//'.$_SERVER['SERVER_NAME'].'/'.$menu[$i]['url'].'/'.$ch['url'].'/" title="'.$ch['name'].'">';
		    
		    echo $ch['name'].'</a></p>';
////////////////////////////// 3 //////////////////////		    
		    $CI->db->where('active','1');
		    $CI->db->where($parent_name,$ch['id']);
		    if($left_menu_type == 'categories') $CI->db->where('show_in_menu','1');
		    $CI->db->order_by('num','ASK');
		    $child2 = $CI->db->get($left_menu_type)->result_array();
		    
		    if($child2)
		    {
			$ccount2 = count($child2);
			for($j2 = 0; $j2 < $ccount2; $j2++)
			{
			    $ch2 = $child2[$j];
			    echo '<p class="left_menu_p"><span style="margin-left: 20px;">&nbsp;└&nbsp;</span><a class="left_menu';
			    if($_SERVER['REQUEST_URI'] == $ch2['url']) echo '_current';
			    echo '" href="//'.$_SERVER['SERVER_NAME'].$ch2['url'].'" title="'.$ch2['name'].'">'.$ch2['name'].'</a></p>';
			    
////////////////////////////////// 4 /////////////////////
			    $CI->db->where('active','1');
			    $CI->db->where($parent_name,$ch2['id']);
			    if($left_menu_type == 'categories') $CI->db->where('show_in_menu','1');
			    $CI->db->order_by('num','ASK');
			    $child3 = $CI->db->get($left_menu_type)->result_array();
			    
			    if($child3)
			    {
				$ccount3 = count($child3);
				for($j3 = 0; $j3 < $ccount3; $j3++)
				{
				    $ch3 = $child3[$j];
				    echo '<p class="left_menu_p"><span style="margin-left: 30px;">&nbsp;└&nbsp;</span><a class="left_menu';
				    if($_SERVER['REQUEST_URI'] == $ch3['url']) echo '_current';
				    echo '" href="//'.$_SERVER['SERVER_NAME'].$ch3['url'].'" title="'.$ch3['name'].'">'.$ch3['name'].'</a></p>';
				}
			    }
			}
		    }
		}
	    }
	}
	
    $CI->db->where('type','left');
    $CI->db->where('subtype','last');
    $CI->db->where('parent_id',0);
    $CI->db->where('active',1);
    $first = $CI->db->get('menus')->result_array();
    if($first)
    {
	$count = count($first);
	for($i = 0; $i < $count; $i++)
	{
	    $f = $first[$i];
	    echo '<p class="left_menu_p"><a class="left_menu';
	    echo '" href="//'.$_SERVER['SERVER_NAME'].$f['url'].'" title="'.$f['name'].'">';
	    echo $f['name'].'</a></p>';
	    
	    // Получаем и выводим детей
	    $CI->db->where('parent_id',$f['id']);
	    $CI->db->where('active',1);
	    $first2 = $CI->db->get('menus')->result_array();
	    if($first2)
	    {
		$count2 = count($first2);
		for($i2 = 0; $i2 < $count2; $i2++)
		{
		    $f2 =$first2[$i2];
		    echo '<p class="left_menu_p"><span style="margin-left: 30px;">&nbsp;└&nbsp;</span><a class="left_menu';
		    echo '" href="//'.$_SERVER['SERVER_NAME'].$f2['url'].'" title="'.$f2['name'].'">';
		    echo $f2['name'].'</a></p>';
		}
	    }
	}
    }
    
//	echo '</div>';
    }
}

function showGalleryLeftMenu()
{
    $CI = & get_instance();
    
    $CI->db->where('active','1');
    $CI->db->where('parent_id','0');
    $CI->db->order_by('num','ASK');
    $menu = $CI->db->get('gallery_categories')->result_array();
    if($menu)
    {
//	echo '<div class="left_menu">';
	$count = count($menu);
	for($i = 0; $i < $count; $i++)
	{
	    echo '<p class="left_menu"><a id="left_menu';
	    if($_SERVER['REQUEST_URI'] == '/gallery/'.$menu[$i]['url'].'/') echo '_current';
	    echo '" href="//'.$_SERVER['SERVER_NAME'].'/gallery/'.$menu[$i]['url'].'/" title="'.$menu[$i]['name'].'">'.$menu[$i]['name'].'</a></p>';
	    $parent = $menu[$i]['url'];
	    
	    $CI->db->where('active','1');
	    $CI->db->where('parent_id',$menu[$i]['id']);
	    $CI->db->order_by('num','ASK');
	    $child = $CI->db->get('gallery_categories')->result_array();
	    if($child)
	    {
		$ccount = count($child);
		for($j = 0; $j < $ccount; $j++)
		{
		    $ch = $child[$j];
		    $uri = '/gallery/'.$parent.'/'.$ch['url'].'/';
		    echo '<p class="left_menu"><span style="color: #496A97; margin: 0 12px;">•</span><a id="left_menu';
		    if($_SERVER['REQUEST_URI'] == $uri) echo '_current';
		    echo '" href="//'.$_SERVER['SERVER_NAME'].'/gallery/'.$parent.'/'.$ch['url'].'/" title="'.$ch['name'].'">'.$ch['name'].'</a></p>';
		}
	    }
	}
//	echo '</div>';
	
	?>
	
	</div>
	<?php
    }
}

function getBanners($position)
{
    $CI = & get_instance();
    
    $CI->db->where('active','1');
    $CI->db->where('position',$position);
    $banners = $CI->db->get('banners')->result_array();
    if($banners)
    {
	$count = count($banners);
	for($i = 0; $i < $count; $i++)
	{
	    echo '<div class="banner';
	    if($position == 'bottom') echo '-bottom';
	    echo '">';
	    $b = $banners[$i];
	    if($b['url'] != '') echo '<a href="/banner/'.$b['id'].'/">';
	    // $b['content'] = str_replace('<p>','',$b['content']);
	    // $b['content'] = str_replace('</p>','',$b['content']);
	    // echo $b['content'];
	    echo '<img src="'.$b['image'].'" alt="'.$b['name'].'" title="'.$b['name'].'" '.$b['format'].' />';
	    if($b['url'] != '') echo '</a>';
	    echo '</div>';
	}
    }
}

function userMenu()
{
    $CI = & get_instance();
    
    if($CI->session->userdata('login') != null && $CI->session->userdata('pass') != null && $CI->session->userdata('type') != null)
    {
	$login  = $CI->session->userdata('login');
        $pass   = $CI->session->userdata('pass');
        $type   = $CI->session->userdata('type');
        $CI->db->where('login',$login);
        $CI->db->limit(1);
        $user = $CI->db->get('users')->result_array();
	if($user)
	{
	    $user = $user[0];
	    ?>
	    <div class="user_menu">
		Добро пожаловать,&nbsp;
			    <strong>
				<a rel="nofollow" href="/user/mypage/">
				    <?=$user['login']?>
				</a>!
			    </strong>
		<table border="0" cellpadding="2" cellspacing="2">
		    <tr>
			<td>
			    <a rel="nofollow" href="/user/mypage/">
			    <?php
			    if($user['avatar'] != '')
			    {
				?>
				
				<img src="<?=$user['avatar']?>" height="75px" border="0" alt="Персональная страница пользователя <?=$user['login']?>" title="Персональная страница пользователя <?=$user['login']?>" />			    
				<?php
			    }
			    else
			    {
				?>
				
				<img src="/img/no_ava.png" height="75px" border="0" alt="Персональная страница пользователя <?=$user['login']?>" title="Персональная страница пользователя <?=$user['login']?>" />			    
				<?php
			    }
			    ?>
			    </a>
			</td>
			<td>
			    <?php
			    $new_msg_count = 0;
			    $myarticles_count = 0;
			    $CI->db->where('login',$user['login']);
			    $CI->db->where('active',1);
			    $articles = $CI->db->get('articles')->result_array();
			    if($articles) $myarticles_count = count($articles);
			    
			    if($user['type'] == 'admin')
				echo '<a rel="nofollow" href="/admin/">Админка</a><br />';
			    ?>
			    <a rel="nofollow" href="/user/mypage/">Моя страница</a>			    
			    <br />
			    <a rel="nofollow" href="/add/article/">Добавить статью</a>
			    <br />
			    <a rel="nofollow" href="/user/mypage/#articles">Мои статьи [<strong><?=$myarticles_count?></strong>]</a>
			    <br />			    
			    <a rel="nofollow" href="/login/logout/">Выход</a>
			</td>
		    </tr>
		</table>
	    </div>
	    <?php
	}
    }
    else
    {
	?>
	<strong>Авторизация</strong>
	<?php	
	if($CI->session->userdata('login_err') != null)
	{
	    ?>
	    <div class="login_err">
		<?=$CI->session->userdata('login_err')?>
	    </div>
	    <?php
	    $CI->session->unset_userdata('login_err');
	}
	?>
	<form method="post" action="/login/">
	    <input type="text" name="login" onblur="if (this.value == '') {this.value = 'Логин'; this.style.color=''}" onfocus="if (this.value == 'Логин') {this.value = ''; this.style.color='#000'}" value="Логин" />
	    <br />
	    <input type="password" name="pass" onblur="if (this.value == '') {this.value = 'Пароль'; this.style.color=''}" onfocus="if (this.value == 'Пароль') {this.value = ''; this.style.color='#000'}" value="Пароль" />
	    <br />
	    <a rel="nofollow" href="/register/">Регистрация</a>
	    <br />
	    <a rel="nofollow" href="/register/forgot/">Забыли пароль?</a>
	    <br />
	    <input type="submit" value="Вход" />
	    
	</form>
	<?php
    }
}