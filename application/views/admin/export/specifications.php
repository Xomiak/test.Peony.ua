<?php
include("application/views/admin/header.php");
?>
    <table width="100%" height="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("application/views/admin/menu.php"); ?></td>
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

                <!--            <div class="ajax-debug">Ajax Debug: <span id="log"></span></div>-->

                <div class="content">
                    <div class="top_menu">
                        <div class="top_menu_link"><a href="/admin/shop/">Товары</a></div>
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/add/">Добавить товар</a></div>
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/currencies/">Валюты</a></div>

                        <div class="top_menu_link"><a href="/admin/shop/specifications/">Спецификации</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/create_extended_price/">Создать спецификацию</a></div>
                        <div class="top_menu_link"><a href="/import/yandex_market.xml">YML (XML)</a></div>

                    </div>


                    <div class="pagination"><?= $pager ?></div>

                    <?php if(userdata('msg') !== false) echo '<div class="msg">'.userdata('msg').'</div>'; unset_userdata('msg'); ?>

                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr bgcolor="#EEEEEE">

                            <th valign="top">
                                ID
                            </th>
                            <th valign="top">
                                Название
                            </th>
                            <th valign="top">
                                Дата
                            </th>
                            <th valign="top">
                                Прайс
                            </th>
                            <th valign="top">
                                Фотки
                            </th>
                            <th valign="top">Действия</th>
                        </tr>
                        <?php
                        //vd($specifications);
                        if ($specifications) {
                            $count = count($specifications);
                            $sizes = explode('|', getOption('sizes'));
                            for ($i = 0; $i < $count; $i++) {
                                //echo $i.': ';vd($specifications[$i]);echo '<hr>';
                                $spec = $specifications[$i];
                                ?>
                                <tr class="list">
                                    <td><?=$spec['id']?></td>
                                    <td><input type="text" value="<?=$spec['saved_name']?>" class="spec_name" spec_id="<?=$spec['id']?>" /></td>
                                    <td><?=$spec['date']?></td>
                                    <td><a href="<?=$spec['link_xls']?>">Скачать прайс</a></td>
                                    <td><a href="<?=$spec['link_fotos']?>">Скачать фотки</a></td>

                                    <td>
                                        <a href="/admin/shop/specifications/check_by_old/<?=$spec['id']?>" title="Выбрать все товары из данной спецификации (все текущие выбранные позиции очистятся и заменятся)"><img src="/img/admin/checked.gif" /></a>
                                        <a href="/admin/shop/specifications/recreate/<?= $spec['id'] ?>/"><img src="/img/admin/copy.png" width="16px" height="16px" border="0" title="Пересобрать спецификацию" /></a>
                                        <a<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>
                                            onclick="return confirm('Удалить?')"
                                            href="/admin/shop/specifications/del/<?=$spec['id']?>/"><img src="/img/del.png"
                                                                                               border="0" alt="Удалить"
                                                                                               title="Удалить"/></a>
                                    </td>
                                </tr>
                                <?php

                            }
                        }
                        ?>
                    </table>
                    <br/>
                    <div class="pagination"><?= $pager ?></div>
                </div>
            </td>
        </tr>
    </table>

    <script>var mailer_checked_count =<?=$mailer_checked_count?>;</script>
    <div class="admin-message">
        <div id="message-text" class="message-text">
            Выбрано <?= $mailer_checked_count ?> позиций
        </div>
    </div>

    <script>
        $(document).ready(function () {
//                        var j = jQuery.noConflict();
//                        j( "#search" ).autocomplete({
//                            source: "/ajax/autocomplete/",
//                            minLength: 1
//                        });

            $(".spec_name").change(function () {
                var id = $(this).attr('spec_id');
                var name = $(this).val();
                $.ajax({
                    /* адрес файла-обработчика запроса */
                    url: '/admin/ajax/specifications/set_name/',
                    /* метод отправки данных */
                    method: 'POST',
                    /* данные, которые мы передаем в файл-обработчик */
                    data: {
                        "action": 'set_name',
                        "id": id,
                        "name": name
                    },

                }).done(function (data) {
                    setAdminMessage('set name');
                    $("#log").html(data);
                });
            });

            $(".mailer_action").click(function () {
                var type = 'mailer';
                var id = $(this).attr("shop_id");
                var action = $(this).attr("action");
                var value = $(this).attr("value");
                if (value == '1') value = 0;
                else value = 1;
                $.ajax({
                    /* адрес файла-обработчика запроса */
                    url: '/admin/ajax/admin_action/',
                    /* метод отправки данных */
                    method: 'POST',
                    /* данные, которые мы передаем в файл-обработчик */
                    data: {
                        "action": action,
                        "type": type,
                        "obj": id,
                        "value": value
                    },

                }).done(function (data) {
                    if (data != 'error') {
                        var thisClass = type + "_" + action + "_" + id;
                        $("#" + thisClass).attr("value", value);
                        if (value == 0) {
                            $("#" + thisClass).addClass("img-not-active");
                        }
                        else {
                            $("#" + thisClass).removeClass("img-not-active");
                        }
                    }

                    setAdminMessage(action);
                    $("#log").html(data);
                });
            });
        });


    </script>

<?php
include("application/views/admin/footer.php");
?>