<?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" valign="top"><?php include("menu.php"); ?></td>
        <td width="20px"></td>
        <td valign="top">
            <div class="title_border">
                <div class="content_title"><h1><?=$title?></h1></div>
                <div class="back_and_exit">
		    		    русский <a href="/en<?=$_SERVER['REQUEST_URI']?>">english</a>

                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
			
			<?php
			if(isset($msg))
			{
				echo '<div class="msg">'.$msg.'</div>';
			}
			if($mailer_test == 1)
			{
				echo '<div class="msg">Включён тестовый режим! Рассылка отправится только админам! <a href="/admin/options/edit/96/">Изменить режим</a></div>';
			}
			?>

            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/mailer/">Рассылка</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/?auto=true">Авторассылки</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/add/">Создание рассылки</a></div>
					<div class="top_menu_link"><a href="/admin/mailer/?clear_mailing=true">Очистить список товаров</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/sms/">SMS рассылка</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/sms/add/">Создать SMS рассылку</a></div>
                </div>


				<table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">

                        <th>ID</th>
                        <th>Дата</th>
                        <th>Название</th>
                        <th>Без цены</th>
						<th>Статус</th>
                        <th>Действия</th>

                    </tr>
                    <?php
                    $count = count($emails);
                    for($i = 0; $i < $count; $i++)
                    {
                        $e = $emails[$i];
                        ?>
                        <tr class="list">
							<td><?=$e['id']?></td>
							<td><?=$e['date']?></td>
                            <td><a href="/admin/mailer/edit/<?=$e['id']?>/" title="Перейти к редактированию"><?=$e['name']?></a> (<a href="/admin/mailer/edit/<?=$e['id']?>/#emails_list" title="К частичной отправке">Ч</a>)</td>
                            <td>
                                <?php
                                if($e['no_price'] == 1) echo "да";
                                else echo "нет";
                                ?>
                            </td>
                            <td><?=$e['status']?></td>

                            <td>
								<a href="/admin/mailer/show/<?=$e['id']?>/" class="gallery"><img src="/img/admin/preview.png" title="Предпросмотр" /></a>&nbsp;
                                <a href="/admin/mailer/send/<?=$e['id']?>/"><img src="/img/admin/send.png" title="Запустить рассылку" /></a>
                                <a href="/admin/mailer/edit/<?=$e['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
								<a href="/admin/mailer/reset/<?=$e['id']?>/"><img src="/img/admin/reset.png" title="Перезапустить" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/mailer/del/<?=$e['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
        </div>
        </td>
    </tr>
</table>

<link rel="stylesheet" type="text/css" href="/fancybox/jquery.fancybox.css" media="screen" />
	<script type="text/javascript" src="/fancybox/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="/fancybox/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="/fancybox/jquery.fancybox-1.2.1.pack.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a.gallery, a.iframe").fancybox();

			$("a.gallery").fancybox(
			{								  
						"frameWidth" : 800,	 // ширина окна, px (425px - по умолчанию)
						"frameHeight" : 600 // высота окна, px(355px - по умолчанию)
											  
			});
		});
	</script>
<?php
include("footer.php");
?>