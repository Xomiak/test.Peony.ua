<div id="wrap">
	<a id="top" name="top" accesskey="t"></a>
	<div id="page-header">
		<div class="headerbar">
			<div class="inner"><span class="corners-top"><span></span></span>

			<div id="site-description">
				<a href="http://<?=$_SERVER['SERVER_NAME']?>/forum/" title="Список форумов" id="logo"><img src="/img/forum/site_log.gif" width="149" height="52" alt="" title=""></a>
				<span class="forum_name"><?=$forum_name?></span>
				<p><?=$slogan?></p>
			</div>

		
			<div id="search-box">
				<form action="/forum/search/" method="post" id="search">
				<fieldset>
					<input name="keywords" id="keywords" type="text" maxlength="128" title="Ключевые слова" class="inputbox search" value="Поиск…" onclick="if(this.value=='Поиск…')this.value='';" onblur="if(this.value=='')this.value='Поиск…';">
					<input class="button2" value="Поиск" type="submit"><br>
					<a href="/forum/search/" title="Параметры расширенного поиска">Расширенный поиск</a>

				</fieldset>

				</form>
			</div>
		

			<span class="corners-bottom"><span></span></span></div>
		</div>

		<div class="navbar">
			<div class="inner"><span class="corners-top"><span></span></span>

			<ul class="linklist navlinks">
				<li class="icon-home"><a href="/forum/" accesskey="h">Главная страница форума</a> </li>

				<li class="rightside"><a href="#" onclick="fontsizeup(); return false;" onkeypress="return fontsizeup(event);" class="fontsize" title="Изменить размер шрифта">Изменить размер шрифта</a></li>

				
			</ul>

			<?php                        
                        if(!$user)
                        {
                        ?>
                        <strong>Авторизация</strong><br />
                        <form action="/login/" method="post"><input type="hidden" name="back" value="<?=$_SERVER['REQUEST_URI']?>" />
			Логин: <input type="text" name="login" /> Пароль: <input type="password" name="pass" /> <input type="submit" value="Войти" class="button2" />
                        </form>
                        <?php
                        }
                        else
                        {
                        ?>
                        <table cellpadding="0" cellspacing="0" border="0">
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
                        <?php
                        }
                        ?>

			<span class="corners-bottom"><span></span></span></div>
		</div>

	</div>

	<a name="start_here"></a>
	<div id="page-body">