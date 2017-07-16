<?php
include("header.php");
?>
    <table width="100%" height="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("menu.php"); ?></td>
            <td width="20px"></td>
            <td valign="top">
                <div class="title_border">
                    <div class="content_title"><h1><?= $title ?></h1></div>
                    <div class="back_and_exit">
                        русский <a href="/en<?= $_SERVER['REQUEST_URI'] ?>">english</a>

                        <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться
                                на сайт ></a></span>
                        <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                    </div>
                </div>

                <?php
                if (isset($msg)) {
                    echo '<div class="msg">' . $msg . '</div>';
                }

                ?>



                <div class="content">
                    <div class="top_menu">
                        <div class="top_menu_link"><a href="/admin/mailer/">Рассылка</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/?auto=true">Авторассылки</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/add/">Создание рассылки</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/?clear_mailing=true">Очистить список
                                товаров</a></div>
                    </div>

                    В очереди на отправку <strong><?= $queueCount ?></strong> писем. За сегодня отправлено <strong><?=$sendedCount?></strong>

                    <?php
                    if(isset($complete) && $complete == 1)
                        echo '<a href="/admin/mailer/?auto=true&history=false">Очередь писем</a>';
                    else echo '<a href="/admin/mailer/?auto=true&history=true">Отправленные письма</a>';
                    ?>

                    <?php if ($queueCount > 0) { ?><a style="float: right"
                        onclick="return confirm('Вы точно хотите очистить всю очередь?')"
                        href="/admin/mailer/?auto=true&clear_queue=true">Очистить всю очередь рассылки</a><?php } ?>

<!--                    <div id="create_auto_mailer" style="cursor: pointer; text-decoration: underline">Сгенерировать авторассылки</div>-->
                    <div class="pagination"><?= $pager ?></div>
                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr bgcolor="#EEEEEE">
                            <th>ID</th>
                            <th>Кому</th>
                            <th>Тема</th>
                            <th>Статус</th>
                            <?php
                            if(isset($complete) && $complete == 1)
                                echo '<th>Дата отправки</th>';
                            ?>
                            <th>Действия</th>
                        </tr>
                        <?php
                        if ($queue) {
                            foreach ($queue as $item){
                                ?>
                                <tr class="list" id="tr-<?=$item['id']?>">
                                    <td><?=$item['id']?></td>
                                    <td><?=$item['to_email']?></td>
                                    <td><?=$item['subject']?></td>

                                    <td>
                                        <?php
                                        if($item['complete'] == 1) echo 'Отправлено';
                                        else echo 'В очереди';
                                        ?>
                                    </td>
                                    <?php
                                    if(isset($complete) && $complete == 1)
                                        echo '<td>'.$item['complete_date'].'</td>';
                                    ?>
                                    <td>
                                        <a href="/admin/mailer/queue_message/<?=$item['id']?>/" class="gallery"><img src="/img/admin/preview.png" title="Предпросмотр" /></a>
                                        <img class="delete" queue_id="<?=$item['id']?>" src="/img/del.png" title="Удалить" />
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                    <div class="pagination"><?= $pager ?></div>
                </div>

                <div class="content">


                    <h2>Информация об авторассылках</h2>
                    <table width="100%">
                        <tr>
                            <th>Новые поступления (<span id="mailer_new_count"><?= count($mailer_new) ?></span>)</th>
                            <th>Уценённые (<?= count($mailer_sale) ?>)</th>
                        </tr>
                        <tr>
                            <td>[ <a href="/admin/mailer/?auto=true&type=new&clear=true">очистить</a> ]</td>
                            <td>[ <a href="/admin/mailer/?auto=true&type=sale&clear=true">очистить</a> ]</td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <table>
                                    <?php
                                    foreach ($mailer_new as $a) {
                                        ?>
                                        <tr id="mailer_new_<?= $a['id'] ?>">
                                            <td>
                                                <a title="Перейти к редактированию статьи"
                                                   href="/admin/shop/edit/<?= $a['id'] ?>/"
                                                   class="tooltip"><?= $a['name'] ?> (<?= $a['color'] ?>)<span><img
                                                            width="125px" src="<?= $a['image'] ?>"></span></a>
                                            </td>
                                            <td>
                                                <a class="mailer_stop" type="new" shop_id="<?= $a['id'] ?>"><img
                                                        title="Убрать из рассылки" src="/img/admin/stop.png"/></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <strong><a href="/admin/mailer/add/?type=new">Создать ручную рассылку
                                        новинок</a></strong>
                            </td>

                            <td valign="top">
                                <table>
                                    <?php
                                    foreach ($mailer_sale as $a) {
                                        ?>
                                        <tr id="mailer_sale_<?= $a['id'] ?>">
                                            <td>
                                                <a title="Перейти к редактированию статьи"
                                                   href="/admin/shop/edit/<?= $a['id'] ?>/"
                                                   class="tooltip"><?= $a['name'] ?> (<?= $a['color'] ?>)<span><img
                                                            width="125px" src="<?= $a['image'] ?>"></span></a>
                                            </td>
                                            <td>
                                                <a class="mailer_stop" type="sale" shop_id="<?= $a['id'] ?>"><img
                                                        title="Убрать из рассылки" src="/img/admin/stop.png"/></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <strong><a href="/admin/mailer/add/?type=sale">Создать ручную рассылку sale</a></strong><br /><br />
                                <h3>Тест</h3>
                                <form method="post">
                                    <?php
                                    if(isset($_GET['sale_test']) && $_GET['sale_test'] == 'sended') echo "<strong>Отправлено!</strong><br />";
                                    ?>
                                    <input type="hidden" name="sale_test" value="true">
                                    <input type="text" name="email" value="<?=getOption('admin_email')?>" required><br /><input type="submit" value="Выслать тестовое письмо">
                                </form>
                            </td>
                        </tr>
                    </table>
                    <script>
                        $(document).ready(function () {
//                        var j = jQuery.noConflict();
//                        j( "#search" ).autocomplete({
//                            source: "/ajax/autocomplete/",
//                            minLength: 1
//                        });

                            // Сгенерировать авторассылки
                            $("#create_auto_mailer").click(function () {
                                showAdminMessage("Приступаем...");
//                                $.ajax({
//                                    method: 'POST',
//                                    async: false,
//                                    url: '/cron/create_mailer_sale_crons/',
//                                    success: function(data) {
//                                        showAdminMessage("Генерация авторассылки Sale готова!");
//                                        $.ajax({
//                                            method: 'POST',
//                                            async: false,
//                                            url: '/cron/create_mailer_new_crons/',
//                                            success: function(data) {
//                                                showAdminMessage("Генерация авторассылки новинок готова!");
//                                            },
//                                            error: function(data) {
//                                                showAdminMessage("Ошибка генерации авторассылки новинок!");
//                                            }
//                                        });
//                                    },
//                                    error: function(data) {
//                                        showAdminMessage("Ошибка генерации авторассылки Sale!");
//                                    }
//                                });
                            });

                            $(".mailer_stop").click(function () {
                                var id = $(this).attr("shop_id");
                                var type = $(this).attr("type");
                                if (confirm("Вы точно хотите убрать товар из рассылки?")) {
                                    $.ajax({
                                        /* адрес файла-обработчика запроса */
                                        url: '/admin/ajax/admin_action/',
                                        /* метод отправки данных */
                                        method: 'POST',
                                        /* данные, которые мы передаем в файл-обработчик */
                                        data: {
                                            "action": "mailer_stop",
                                            "type": type,
                                            "obj": id
                                        },

                                    }).done(function (data) {
                                        if (data != 'error') {
                                            $("#mailer_" + type + "_count").html($("#mailer_" + type + "_count").html() - 1);
                                            $("#mailer_" + type + "_" + id).remove();
                                        }
                                    });
                                }
                            });


                        });
                    </script>


                </div>

            </td>
        </tr>
    </table>

    <div class="admin-message">
        <div id="message-text" class="message-text">
            
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="/fancybox/jquery.fancybox.css" media="screen" />
    <script type="text/javascript" src="/fancybox/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="/fancybox/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="/fancybox/jquery.fancybox-1.2.1.pack.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $(".delete").click(function () {
                var id = $(this).attr('queue_id');
                if(confirm('Точно удалить это письмо из очереди?')){
                    $.ajax({
                        method: 'POST',
                        url: '/admin/ajax/mailer/delete/'+id+'/',
                        success: function(data) {
                            $("#tr-"+id).hide();
                            showAdminMessage("Очередь ID:"+id+" удалена!");
                        },
                        error: function(data) {
                            showAdminMessage("Ошибка удаления!");
                        }
                    });
                }
            });

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