<?php include("application/views/head_new.php"); ?><?php include('application/views/header.php'); ?>
<?php include('application/views/left.inc.php'); ?>
<?php include('application/views/right.inc.php'); ?>

<div id="content">
    <center><?php getBanners('top'); ?></center>
    <div class="kroshki">
        <div xmlns:v="http://rdf.data-vocabulary.org/#">
            <span typeof="v:Breadcrumb">
                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
            </span>
            &nbsp;»&nbsp;
            <?=$h1?>
        </div>
    </div>
    <h1 class="long">
    <?=$h1?>
    </h1>
    
    <?php
    if($users)
    {
        ?>
        <table width="100%" cellpadding="5" cellspacing="5">
            <tr>
                <td align="center">
                    <strong>Аватар</strong>
                </td>
                <td align="center">
                    <strong>Имя</strong>
                </td>
                <td align="center">
                    <strong>Оценка</strong>
                </td>
                <td>
                </td>
                <td align="center">
                    <strong>Пол</strong>
                </td>
                <td align="center">
                    <strong>Дата регистрации</strong>
                </td>
                <td align="center">
                    <strong>Кол-во фото</strong>
                </td>
                <td align="center">
                    <strong>Блог</strong>
                </td>
            </tr>
        <?php
        $count = count($users);
        for($i = 0; $i < $count; $i++)
        {
            $u = $users[$i];
            ?>
            <tr>
                <td align="center">
                    <a href="/user/<?=$u['id']?>/">
                        <img src="<?php
                        if($u['avatar'] == '') echo '/img/no_ava.png';
                        else echo $u['avatar'];
                        ?>" alt="Страница пользователя <?=$u['login']?>" title="Страница пользователя <?=$u['login']?>" border="0" width="50px" />
                    </a>
                </td>
                <td align="center">
                    <a href="/user/<?=$u['id']?>/">
                        <?=$u['name']?>
                    </a>
                </td>
                
                <td align="center">
                    <?=$u['rating']?>
                </td>
                <td>
                    <?php
                    if($u['login'] != $this->session->userdata('login') && $u['zvanie'] != 'ветеран')
                    {
                        
                        //$user = $this->users->getUserById($id);
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = time();
			$user_login = $this->session->userdata('login');
			$rating = $this->users->getRating($u['login'], $ip, $user_login);
                        			
			$rating_period = $this->options->getOption('rating_period');
			$all_ok = false;
			if($rating_period != 0)
			{                            
				if(!$rating) $all_ok = true;
				elseif(isset($rating['time']))
				{
                                    //echo $time - $rating['time'];
                                    //echo '<BR><BR><BR>';
                                    //echo $rating_period;
					if(($time - $rating['time']) > $rating_period)
                                        {
						$all_ok = true;
                                        }
				}
			}
			else $all_ok = true;
			
			if($all_ok)
			{
                            
                    ?>
                            <a rel="nofollow" href="/rating/<?=$u['id']?>/"><img src="/img/plus.png" border="0" title="Голосовать" alt="Голосовать" />
                    <?php
                        }
                    }
                    ?>
                                    
                </td>
                <td align="center">
                    <?php
                    if($u['sex'] == 'm') echo 'мужской';
                    elseif($u['sex'] == 'w') echo 'женский';
                    else echo 'не указан';
                    ?>
                </td>
                <td align="center">
                    <?=$u['reg_date']?>
                </td>
                <td align="center">
                    <?php
                    $fotoscount = 0;
                    if($u['foto'] != '') $fotoscount++;
                    if($u['foto2'] != '') $fotoscount++;
                    echo $fotoscount;
                    ?>
                </td>
                <td align="center">
                    <?php
                    $blog = $this->blogs->getBlogByLogin($u['login'],1);
                    if(!$blog) echo 'нет';
                    else
                    {
                        ?>
                        <a href="/blog/user/<?=$blog['url']?>/"><?=$blog['name']?></a>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </table>
        <?php
    }
    ?>
    
    
    <div class="clear"></div>
    <center><?php getBanners('bottom'); ?></center>
</div>

<?php include('application/views/footer.php'); ?>