<?php
$show_only = false;
if (userdata('category_id') !== false)
    $show_only = userdata('category_id');
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

                <!--            <div class="ajax-debug">Ajax Debug: <span id="log"></span></div>-->

                <div class="content">
                    <div class="top_menu">
                        <div class="top_menu_link"><a href="/admin/shop/">Товары</a></div>
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/add/">Добавить товар</a></div>
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/import/">Импорт</a></div>-->
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/export/">Экспорт</a></div>-->
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/currencies/">Валюты</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/specifications/">Спецификации</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/create_extended_price/">Создать спецификацию</a></div>
                        <div class="top_menu_link"><a href="/import/yandex_market.xml">YML (XML)</a></div>
                        <div class="top_menu_link">
                            <form method="post" action="/admin/shop/set_category/">
                                <input type="hidden" name="back" value="<?= $_SERVER['REQUEST_URI'] ?>"/>
                                Выбор раздела:
                                <SELECT name="category_id" onchange="submit();">

                                    <option value="all">Все</option>
                                    <?php
                                    $count = count($categories);
                                    for ($i = 0; $i < $count; $i++) {
                                        $cat = $categories[$i];
                                        if($cat['type'] == 'shop') {
                                            echo '<option value="' . $cat['id'] . '"';
                                            if ($this->session->userdata('shop_category_id') == $cat['id']) echo ' selected';
                                            echo '>' . $cat['name'] . '</option>';
                                            $subcats = $this->mcats->getSubCategories($cat['id']);
                                            if ($subcats) {
                                                $subcount = count($subcats);
                                                for ($j = 0; $j < $subcount; $j++) {
                                                    $sub = $subcats[$j];
                                                    echo '<option value="' . $sub['id'] . '"';
                                                    if ($this->session->userdata('shop_category_id') == $sub['id']) echo ' selected';
                                                    echo '>&nbsp;└&nbsp;' . $sub['name'] . '</option>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </SELECT>
                            </form>
                        </div>



                        <div class="top_menu_link">
                            <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                                Поиск:<input type="text" name="search"
                                             value="<?php if (isset($_POST['search'])) echo $_POST['search']; ?>"
                                             style="width:500px"/>
                                <input type="submit" value="Искать"/>
                            </form>
                        </div>

                    </div>

                    <?php
                    if(isset($_GET['action']) && $_GET['action'] == 'this' && $_GET['type'] == 'checked'){
                        foreach ($shop as $article){
                            $this->db->where('id', $article['id'])->limit(1)->update('shop', array('mailer_checked'=>1));
                        }
                        //vdd(request_uri(false,true));
                        redirect(request_uri(false,true));
                    }
                    $mailer = 'check';
                    if($shop) {
                        foreach ($shop as $article) {
                            if ($article['mailer_checked'] == 1) {
                                $mailer = 'uncheck';
                                break;
                            }
                        }
                    } else echo '<br /><br /><b>В этом разделе товаров нет...</b><br />';
                    ?>

                    <div class="pagination"><?= $pager ?></div>

                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr bgcolor="#EEEEEE">
                            <?php
                            $order_by = userdata('order_by');
                            if(!$order_by || $order_by == 'ASC') $order_by = 'DESC';
                            else $order_by = 'ASC';

                            ?>
                            <th valign="top">
                                <a href="/admin/shop/?sort_by=id&order_by=<?=$order_by?>">ID</a>
                            </th>
                            <th valign="top">
                                <a href="/admin/shop/?sort_by=name&order_by=<?=$order_by?>">Название</a>
                            </th>
                            <th valign="top">
                                <a href="/admin/shop/?sort_by=articul&order_by=<?=$order_by?>">Артикул</a>
                                <form method="post">
                                    <?php
                                    $filter_articul = '';
                                    if(userdata('filter_articul')) $filter_articul = userdata('filter_articul');
                                    ?>
                                    <input style="width: 110px" type="text" name="filter_articul" value="<?=$filter_articul?>" placeholder="Часть артикула" />
                                </form>
                            </th>
                            <th valign="top">Раздел</th>
                            <th valign="top"><a href="/admin/shop/?sort_by=price&order_by=<?=$order_by?>">Цена</a></th>
                            <th valign="top">
                                <a href="/admin/shop/?sort_by=season&order_by=<?=$order_by?>">Сезон</a>
                                <form method="post">
                                    <?php
                                    $season_text = '- Сезон -';
                                    if(userdata('filter_season') != false) $season_text = '- Сбросить -';
                                    $seasons = getOption('seasons');
                                    if($seasons) $seasons = explode('|',$seasons);
                                    ?>
                                    <select onchange="submit()" name="filter_season">
                                        <option value=""><?=$season_text?></option>
                                        <?php
                                        if(is_array($seasons)){
                                            foreach ($seasons as $season){
                                                echo '<option value="'.$season.'"';
                                                if(userdata('filter_season') == $season) echo ' selected';
                                                echo '>'.$season.'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </form>
                            </th>

                            <th valign="top"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                                <?php
                                $discount_text = '- Скидка -';
                                if(userdata('filter_discount') != false) $discount_text = '- Сбросить -';
                                ?>
                                <a href="/admin/shop/?sort_by=discount&order_by=<?=$order_by?>">Скидка</a><br />
                                <form method="post">
                                    <select onchange="submit()" name="filter_discount">
                                        <option value=""><?=$discount_text?></option>
                                        <option value="5"<?php if(userdata('filter_discount') == 5) echo ' selected'; ?>>5%</option>
                                        <option value="10"<?php if(userdata('filter_discount') == 10) echo ' selected'; ?>>10%</option>
                                        <option value="15"<?php if(userdata('filter_discount') == 15) echo ' selected'; ?>>15%</option>
                                        <option value="20"<?php if(userdata('filter_discount') == 20) echo ' selected'; ?>>20%</option>
                                        <option value="25"<?php if(userdata('filter_discount') == 25) echo ' selected'; ?>>25%</option>
                                        <option value="30"<?php if(userdata('filter_discount') == 30) echo ' selected'; ?>>30%</option>
                                        <option value="35"<?php if(userdata('filter_discount') == 35) echo ' selected'; ?>>35%</option>
                                        <option value="40"<?php if(userdata('filter_discount') == 40) echo ' selected'; ?>>40%</option>
                                        <option value="45"<?php if(userdata('filter_discount') == 45) echo ' selected'; ?>>45%</option>
                                        <option value="50"<?php if(userdata('filter_discount') == 50) echo ' selected'; ?>>50%</option>
                                    </select>
                                </form>
                            </th>
                            <th valign="top"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>><a href="/admin/shop/?sort_by=warehouse_sum&order_by=<?=$order_by?>">На складе</a></th>
                            <th valign="top" style="align: center;">
                                <a href="/admin/shop/?sort_by=image_no_logo&order_by=<?=$order_by?>">Фото<br/>без лого</a>
                            </th>
                            <th valign="top"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                                Опции<br/>
                                <a onclick="return confirm('Вы точно хотите очистить новинки для рассылки?')"
                                   href="<?=request_uri(false,true)?>?action=all&type=new"><img id="mailer_new_all"
                                                                                class="mailer_action img-not-active"
                                                                                title="Очистить рассылку новинок"
                                                                                src="/img/admin/new.gif"/></a>
                                <a onclick="return confirm('Вы точно хотите очистить sale для рассылки?')"
                                   href="<?=request_uri(false,true)?>?action=all&type=sale"><img id="mailer_sale_all"
                                                                                 class="mailer_action img-not-active"
                                                                                 title="Очистить рассылку о распродаже"
                                                                                 src="/img/admin/sale.png"/></a>
                                <?php if($mailer == 'uncheck') { ?>
                                <a onclick="return confirm('Вы точно хотите очистить выбранные товары?')"
                                   href="<?=request_uri(false,true)?>?action=all&type=checked"><img id="mailer_checked_all"
                                                                                    class="mailer_action img-not-active"
                                                                                    title="Очистить выбранное"
                                                                                    src="/img/admin/checked.gif"/></a>
                                <?php } else { ?>
                                    <a href="<?=request_uri(false,true)?>?action=this&type=checked"><img id="mailer_checked_all"
                                                                                        class="mailer_action img-active"
                                                                                        title="Выбрать все на текущей странице"
                                                                                        src="/img/admin/checked.gif"/></a>
                                <?php } ?>
                            </th>
                            <th></th>
                            <th valign="top">Действия</th>
                        </tr>
                        <?php
                        
                        if ($shop) {
                            $count = count($shop);
                            $sizes = explode('|', getOption('sizes'));
                            for ($i = 0; $i < $count; $i++) {
                                //echo $i.': ';vd($shop[$i]);echo '<hr>';
                                $article = $shop[$i];
                                $user = $this->users->getUserByLogin($article['login']);
                                ?>
                                <tr class="list">
                                    <td><?=$article['id']?></td>
                                    <td>
                                        <a href="/admin/shop/edit/<?= $article['id'] ?>/"
                                        class="tooltip"><?= $article['name'] ?> (<?= $article['color'] ?>)<span>
                                                <?= $article['name'] ?> (<?= $article['color'] ?>)<br/>
                                                <img style="float: left" width="125px"
                                                     src="<?= $article['image'] ?>"><br/>
                                                <div style="font-size: 13px">
                                                    <?php
                                                    if ($article['warehouse_sum'] > 0) {
                                                        $warehouse = json_decode($article['warehouse'], true);
                                                        //vd($warehouse);
                                                        if (is_array($warehouse)) {
                                                            echo '<b>Размеры</b><br />';
                                                            foreach ($sizes as $size) {
                                                                if (isset($warehouse[$size]) && $warehouse[$size] > 0) {
                                                                    echo $size . ': ' . $warehouse[$size] . '<br />';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($article['season'] != '') echo '<b>Сезон</b>: ' . $article['season'] . '<br/>';
                                                    if ($article['tkan'] != '') echo '<b>Ткань</b>: ' . $article['tkan'] . '<br/>';
                                                    if ($article['sostav'] != '') echo '<b>Состав</b>: ' . $article['sostav'] . '<br/>';
                                                    if ($article['height'] != '') echo '<b>Длина</b>: ' . $article['height'] . '<br/>';
                                                    if ($article['hand_height'] != '') echo '<b>Длина рукава</b>: ' . $article['hand_height'] . '<br/>';
                                                    ?>

                                                </div></span></a>
                                    </td>
                                    <td><?= $article['articul'] ?></td>
                                    <td><? $cat = $this->mcats->getCategoryById($article['category_id']);
                                        echo '<a href="/admin/categories/edit/' . $cat['id'] . '/" title="Перейти к редактированию раздела">' . $cat['name'] . '</a>'; ?></td>

                                    <td><?= round($article['price'], 2) ?>$</td>
                                    <td><?=$article['season']?></td>
                                    <td<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>><?= $article['discount'] ?>
                                        %
                                    </td>
                                    <td<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>><?= $article['warehouse_sum'] ?></td>

                                    <td>
                                        <?php
                                        if($article['image_no_logo'] == '') echo '<b>Нет</b>';
                                        else echo 'Да';
                                        ?>
                                    </td>

                                    <td<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                                        <img
                                            class="mailer_action<?php if ($article['mailer_new'] == 0) echo ' img-not-active'; ?>"
                                            action="new" value="<?= $article['mailer_new'] ?>"
                                            shop_id="<?= $article['id'] ?>" src="/img/admin/new.gif"
                                            title="Рассылка новинок" id="mailer_new_<?= $article['id'] ?>"/>
                                        <img
                                            class="mailer_action<?php if ($article['mailer_sale'] == 0) echo ' img-not-active'; ?>"
                                            action="sale" value="<?= $article['mailer_sale'] ?>"
                                            shop_id="<?= $article['id'] ?>" src="/img/admin/sale.png"
                                            title="Рассылка о распродаже" id="mailer_sale_<?= $article['id'] ?>"/>
                                        <img
                                            class="mailer_action<?php if ($article['mailer_checked'] == 0) echo ' img-not-active'; ?>"
                                            action="checked" value="<?= $article['mailer_checked'] ?>"
                                            shop_id="<?= $article['id'] ?>" src="/img/admin/checked.gif" title="Выбрать"
                                            id="mailer_checked_<?= $article['id'] ?>"/>
                                    </td>

                                    <td width="16">
                                    <?php
                                    if ($article['need_text'] == 1) echo '<img src="/img/admin/txt.png" title="Необходимо написать текст" />';
                                    ?>
                                    </td>
                                    
                                    <td>

                                        <a href="/admin/shop/active/<?= $article['id'] ?>/"><?php
                                            if ($article['active'] == 1)
                                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                            else
                                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                            ?></a>


                                        <a href="/admin/shop/edit/<?= $article['id'] ?>/"><img src="/img/edit.png"
                                                                                               width="16px"
                                                                                               height="16px" border="0"
                                                                                               title="Редактировать"/></a>

                                        <?php if($article['base_ids'] != NULL){ ?>
                                        <a<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>
                                            onclick="return confirm('Точно разорвать связь с ТоргСофтом?')"
                                            href="/admin/shop/break/<?= $article['id'] ?>/"><img src="/img/admin/break.png"
                                                                                               border="0" alt="Разорвать связь с ТоргСофтом"
                                                                                               title="Разорвать связь с ТоргСофтом"/></a>
                                        <?php } ?>

                                        <a<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>
                                            onclick="return confirm('Удалить?')"
                                            href="/admin/shop/del/<?= $article['id'] ?>/"><img src="/img/del.png"
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
include("footer.php");
?>