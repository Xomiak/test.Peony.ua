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
                            <th>Статус</th>
                            <th>Действия</th>

                        </tr>
                        <?php
                        $count = count($mailer_sms);
                        for($i = 0; $i < $count; $i++)
                        {
                            $e = $mailer_sms[$i];
                            ?>
                            <tr class="list">
                                <td><?=$e['id']?></td>
                                <td><?=$e['start']?></td>
                                <td><a href="/admin/mailer/sms/edit/<?=$e['id']?>/" title="Перейти к редактированию"><?=$e['name']?></a> (<a href="/admin/mailer/edit/<?=$e['id']?>/#emails_list" title="К частичной отправке">Ч</a>)</td>

                                <td><?=$e['status']?></td>

                                <td>
                                    <?php if($e['status'] == 'new') {?>
                                        <a onclick="return confirm('Вы точно готовы запустить массовую рассылку всем клиентам?')" href="/admin/mailer/sms/edit/<?=$e['id']?>/?start_now=true"><img src="/img/admin/send.png" title="Запустить рассылку" /></a>
                                        <a href="/admin/mailer/sms/edit/<?=$e['id']?>/?add_sms_cron=true"><img src="/img/admin/send.png" title="Добавить рассылку в задачи крона" /></a>
                                    <?php } ?>
                                    <a href="/admin/mailer/sms/edit/<?=$e['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>

                                    <?php if($e['status'] != 'new') {?>
                                        <a href="/admin/mailer/sms/edit/<?=$e['id']?>/?restart=true"><img src="/img/admin/reset.png" title="Перезапустить" /></a>
                                    <?php } ?>

                                    <a onclick="return confirm('Удалить?')" href="/admin/mailer/sms/del/<?=$e['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
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