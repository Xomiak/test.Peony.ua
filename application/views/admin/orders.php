<?php
include("header.php");
loadHelper('order');
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

                <div class="content">
                    <div class="top_menu">
                        <!--                        <div class="top_menu_link"><a href="/admin/pages/">Страницы</a></div>-->
                        <!--                        <div class="top_menu_link"><a href="/admin/pages/add/">Добавить страницу</a></div>-->
                        <div class="top_menu_link">
                            <form method="post">
                                Показать:
                                <SELECT name="status" onchange="submit()">
                                    <option
                                        value="-1"<?php if ($status == '-1') echo ' selected'; ?>> -- Все -- </option>
                                    <option
                                        value="new"<?php if ($status == 'new') echo ' selected'; ?>><?= getStatus('new') ?></option>
                                    <option
                                        value="processing"<?php if ($status == 'processing') echo ' selected'; ?>><?= getStatus('processing') ?></option>
                                    <option
                                        value="payed"<?php if ($status == 'payed') echo ' selected'; ?>><?= getStatus('payed') ?></option>
                                    <option
                                        value="sended"<?php if ($status == 'sended') echo ' selected'; ?>><?= getStatus('sended') ?></option>
                                    <option
                                        value="done"<?php if ($status == 'done') echo ' selected'; ?>><?= getStatus('done') ?></option>
                                    <option
                                        value="canceled"<?php if ($status == 'canceled') echo ' selected'; ?>><?= getStatus('canceled') ?></option>
                                </SELECT>
                            </form>
                        </div>
                        <div class="top_menu_link">
                            <a href="/admin/orders/?get_from_prom=true">Проверить заказы на Проме</a>
                        </div>
                        <div class="top_menu_link" style="float: right">
                            <strong style="color: red"><?=$msg?></strong>
                        </div>
                    </div>


                    <!--div class="graph">
                        <?php
                        $date1 = date("Y-m-d", strtotime("-4 WEEK"));
                        $date2 = date("Y-m-d");
                        if(isset($_GET['date1'])) $date1 = $_GET['date1'];
                        if(isset($_GET['date2'])) $date2 = $_GET['date2'];
                        ?>
                        <form method="get">
                            Показать с <input name="date1" value="<?=$date1?>"> по <input name="date2" value="<?=$date1?>"> <input type="submit">
                        </form>
                        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                        <div id="chart_div" style="width: 100%; height: 500px;"></div>

                        <script>
                            google.charts.load('current', {'packages':['corechart']});
                            google.charts.setOnLoadCallback(drawChart);
                            function drawChart() {
                                var data = google.visualization.arrayToDataTable([
                                    ['День', 'Сумма заказов', 'Сумма товаров'],
                                    <?php
//                                    list($y,$m,$d) = explode("-",$date1);
//                                    $time1 = mktime(0,0,0,$m,$d,$y);
//                                    list($y,$m,$d) = explode("-",$date2);
//                                    $time2 = mktime(0,0,0,$m,$d,$y);
//                                    for ($i = $time1; $i <= $time2; $i+=24*60*60) {
//                                        $date = date("Y-m-d", $i);
//                                        $count = $this->shop->getCountOrdersByDate($date);
//                                        $d = date("d.m", $i);
//                                        $kolvo = 0;
//                                        if($count > 0){
//                                            $orders = $this->shop->getOrdersByDate($date);
//                                            foreach($orders as $order){
//                                                $cart = unserialize($order['products']);
//                                                foreach($cart as $item) {
//                                                    $kolvo += $item['count'];
//                                                }
//                                            }
//                                        }



                                        ?>
                                        ['<?=$d?>',  <?=$count?>, <?=$kolvo?>],
                                        <?php
                                   // }
                                    ?>
                                ]);

                                var options = {
                                    title: 'График заказов',
                                    hAxis: {title: 'Дата',  titleTextStyle: {color: '#333'}},
                                    vAxis: {minValue: 0}
                                };

                                var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                                chart.draw(data, options);
                            }
                        </script>
                    </div-->

                    <div class="pagination"><?= $pager ?></div>
                    <br/>

                    <link rel="stylesheet" type="text/css" href="/fancybox/jquery.fancybox.css" media="screen"/>
                    <script type="text/javascript" src="/fancybox/jquery-1.3.2.min.js"></script>
                    <script type="text/javascript" src="/fancybox/jquery.easing.1.3.js"></script>
                    <script type="text/javascript" src="/fancybox/jquery.fancybox-1.2.1.pack.js"></script>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("a.gallery, a.iframe").fancybox({
                                onCancel: function () {
                                    alert('closing');
                                },
                                beforeClose: function () {
                                    alert('close')
                                }
                            });

                            $("a.gallery").fancybox(
                                {
                                    onCancel: function () {
                                        alert('closing');
                                    },
                                    beforeClose: function () {
                                        alert('close')
                                    },
                                    "frameWidth" : 800,	 // ширина окна, px (425px - по умолчанию)
                                    "frameHeight" : 600 // высота окна, px(355px - по умолчанию)

                                });
                        });
                    </script>

                    <form method="post">
                        <div id="multiactions">
                            Изменить статус на
                            <select name="status">
                                <option value="new"><?=getStatus('new')?></option>
                                <option value="processing"><?=getStatus('processing')?></option>
                                <option value="payed"><?=getStatus('payed')?></option>
                                <option value="sended"><?=getStatus('sended')?></option>
                                <option value="done"><?=getStatus('done')?></option>
                                <option value="canceled"><?=getStatus('canceled')?></option>
                            </select>
                            <input type="checkbox" checked name="mail_to_client"><img src="/img/admin/mail.png" title="Уведомить клиентов об изменении статуса заказа" style="width: 32px; height: 32px;" />
                            <input type="submit" name="set_status" value="Применить">
                        </div>
                        <table width="100%" cellpadding="1" cellspacing="1" id="orderstable">
                        <tr bgcolor="#EEEEEE">
                            <th><input type="checkbox" name="maincheck" id="maincheck" title="Выбрать все" /></th>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Заказчик</th>
                            <th>Сумма ($)</th>
                            <th>Сумма в валюте</th>
                            <th>Товары</th>
                            <th>Адрес</th>
                            <th>Оплата</th>
                            <th>Доставка</th>
                            <th>Акционный</th>
                            <th>Откуда</th>
                            <th>Действия</th>

                        </tr>
                        <?php
                        loadHelper('order');
                        $count = count($pages);
                        for ($i = 0; $i < $count; $i++) {
                            $page = $pages[$i];
                            $currencies = json_decode($page['currencies'], true);
                            //vd($currencies);
                            $user = $this->users->getUserById($page['user_id']);
                            ?>
                            <tr class="list<?php
                            if ($page['status'] == 'new') echo ' new-order';
                            elseif ($page['status'] == 'one_click') echo ' one_click';
                            elseif ($page['status'] == 'processing') echo ' processing-order';
                            elseif ($page['status'] == 'payed') echo ' payed-order';
                            elseif ($page['status'] == 'sended') echo ' sended-order';
                            elseif ($page['status'] == 'done') echo ' done-order';
                            elseif ($page['status'] == 'canceled') echo ' canceled-order';
                            ?>">
                                <td><input class="mc" type="checkbox" name="list[]" value="<?=$page['id']?>" /></td>
                                <td><a href="/admin/orders/edit2/<?= $page['id'] ?>/"><?= $page['id'] ?><a/></td>
                                <td><?= $page['date'] ?> <?= $page['time'] ?></td>
                                <td>
                                    <select title="Изменить статус заказа" class="status_sel" order_id="<?=$page['id']?>" style="width: 130px;" name="status" id="status-order-<?=$page['id']?>">
                                    <?php
                                    if($page['viewed'] == 0) echo '<img class="eye" src="/img/admin/eye.png" title="Не просмотренный заказ!" />';
                                    echo getStatus($page['status']);
                                    $statuses = getStatuses(1);
                                   // vdd($statuses);
                                    if($statuses){
                                        foreach ($statuses as $status){
                                            echo '<option value="'.$status['status'].'"';
                                            if($page['status'] == $status['status']) echo ' selected';
                                            echo '>'.$status['name'].'</option>';
                                        }
                                    }
                                    ?>
                                    </select>
                                </td>
                                <td><a href="/admin/users/edit/<?= $page['user_id'] ?>/"><?= $user['name'] ?>
                                        , <?= $user['lastname'] ?></a></td>
                                <td align="center"><?= $page['full_summa'] ?></td>
                                <td align="center">
                                    <?php
                                    if (isset($page['currency']) && $page['currency'] == 'uah') {
                                        if(isset($currencies['UAH']))
                                            $currensy_grn = $currencies['UAH'];
                                        else
                                            $currensy_grn = getCurrencyValue('UAH');
                                        $page['full_summa'] = $page['full_summa'] * $currensy_grn;
                                    } elseif (isset($page['currency']) && $page['currency'] == 'rub') {
                                        if(isset($currencies['RUB']))
                                            $currensy_rub = $currencies['RUB'];
                                        else
                                            $currensy_rub = getCurrencyValue('RUB');
                                        $page['full_summa'] = $page['full_summa'] * $currensy_rub;
                                    }
                                    ?>
                                    <?= $page['full_summa'] ?> <?= $page['currency'] ?>
                                    <?php
                                    $coupon = false;
                                    if ($page['code']) {
                                        $coupon = getCoupon($page['code']);
                                        if($coupon['type'] == 0) $coupon['discount'] .= '%';
                                        else $coupon['discount'] .= '$';
                                        echo '<br/><span style="font-size: 12px;">(со скидкой по купону: <b>' . $page['code'] . '</b> - '.$coupon['discount'].')</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <table class="products" border="0" style="font-size: 12px;">
                                        <th>Товар</th>
                                        <th>Цвет</th>
                                        <th>Кол-во</th>
                                        <th>Цена за 1 шт.</th>
                                        <?php
                                        //$my_cart = unserialize($page['products']);
                                        $data = $page['products'];
                                        $my_cart = $data;
                                        //var_dump($my_cart);
                                        $my_cart = unserialize($my_cart);
                                        //var_dump($my_cart);
                                        $pcount = count($my_cart);
                                        for ($j = 0; $j < $pcount; $j++) {
                                            $mc = $my_cart[$j];
                                            $product = $this->shop->getProductById($mc['shop_id']);
                                            $cat = $this->categories->getCategoryById($product['category_id']);
                                            $parent = false;
                                            $razmer = explode('*', $product['razmer']);
                                            if ($product['parent_category_id'] != 0) $parent = $this->categories->getCategoryById($product['parent_category_id']);

                                            ?>
                                            <tr>

                                                <td>
                                                    <a href="/<?php if ($parent) echo $parent['url'] . '/'; ?><?= $cat['url'] ?>/<?= $product['url'] ?>/"
                                                       target="_blank"><?= $product['name'] ?></a>
                                                </td>
                                                <td>
                                                    <?= $product['color'] ?>
                                                </td>
                                                <td align="center">
                                                    <?php
                                                    $rcount = count($razmer);
                                                    $mc['kolvo'] = 0;
                                                    for ($i2 = 0; $i2 < $rcount; $i2++) {
                                                        if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] > 0) {
                                                            echo $razmer[$i2] . ': ' . $mc['kolvo_' . $razmer[$i2]] . '<br />';
                                                            $mc['kolvo'] += $mc['kolvo_' . $razmer[$i2]];
                                                        }
                                                    }
                                                    ?>
                                                    <strong>Всего: <?= shop_sizes_count($mc) ?></strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    //vd($coupon);
                                                    $price = getNewPrice($product['price'], $product['discount']);
                                                    if($coupon != false && $product['discount'] == 0)
                                                        $price = getNewPrice($product['price'], $coupon['discount']);
                                                    echo $price . ' USD';
                                                    //if (isDiscount($product, $page['date'])) echo ' (<b>Акция</b>)';
                                                    if($product['discount'] > 0)  echo ' (<b>Sale</b> - '.$product['discount'].'%)';
                                                    elseif($coupon) echo ' (<b>Акция</b>)';
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                </td>
                                <?php
                                if($page['adress'] == 0 || $page['adress'] == '') $page['adress'] = $page['delivery_info'];
                                ?>
                                <td><?= $page['adress'] ?></td>
                                <td><?= $page['payment'] ?></td>
                                <td><?= $page['delivery'] ?></td>

                                <td>
                                    <?php
                                    if ($page['akciya'] == 1) echo '<b>Да</b>';
                                    else echo 'нет';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($page['from'] != false)
                                        echo $page['from'];
                                    elseif($user['from'] != false)
                                        echo $user['from'];

                                    if($user['domain'] != false)
                                        echo '<br/>Заказ с сайта: '.$user['domain'];
                                    ?>
                                </td>
                                <td>
                                    <a href="/demo/order.php" class="gallery"><img src="/img/admin/preview.png" title="Предпросмотр"></a>

                                    <!--a href="/admin/pages/active/<?= $page['id'] ?>/"><?php
                                    if ($page['active'] == 1)
                                        echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                    else
                                        echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                    ?></a-->

                                    <a href="/admin/orders/edit2/<?= $page['id'] ?>/"><img src="/img/edit.png"
                                                                                          width="16px" height="16px"
                                                                                          border="0"
                                                                                          title="Редактировать"/></a>
                                    <a class="create_torgsoft_file" order_id="<?= $page['id'] ?>"
                                       style="cursor: pointer"><img src="/img/admin/torgsoft.png" width="16px"
                                                                    height="16px" border="0"
                                                                    title="Создать файл для импорта заказа в ТоргСофт"/></a>
                                    <a onclick="return confirm('Удалить?')"
                                       href="/admin/orders/del/<?= $page['id'] ?>/"><img src="/img/del.png" border="0"
                                                                                         alt="Удалить" title="Удалить"/></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    </form>
                </div>
            </td>
        </tr>
    </table>

    <br/>
    <div class="pagination"><?= $pager ?></div>

    <script>
        $(document).ready(function () {

            $(".status_sel").change(function () {
                var orderId = $(this).attr('order_id');
                var value = $(this).val();
                //alert(value);
                setOrderStatus(orderId, value);
            });

            $("#maincheck").click( function() {
                if($('#maincheck').attr('checked')){
                    $('.mc').attr('checked', true);
                    $("#multiactions").show(1000);
                } else {
                    $('.mc').attr('checked', false);
                }
            });
            $(".mc").change(function () {
                $("#multiactions").show(1000);


            });


            $(".create_torgsoft_file").click(function () {
                var id = $(this).attr("order_id");
                $.ajax({
                    /* адрес файла-обработчика запроса */
                    url: '/admin/ajax/admin_action/',
                    /* метод отправки данных */
                    method: 'POST',
                    /* данные, которые мы передаем в файл-обработчик */
                    data: {
                        "action": "create_torgsoft_file",
                        obj: id
                    },
                    beforeSend: function () {
                        /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */

                    }
                    /* что нужно сделать до отправки запрса */

                    /* что нужно сделать по факту выполнения запроса */
                }).done(function (data) {
                    alert(data);
                });
            });
        });

        function setOrderStatus(orderId, value, action = "set_status") {
            $.ajax({
                url: '/admin/ajax/edit_order/' + orderId + '/',
                method: 'post',
                async: false,
                data: {
                    "orderId": orderId,
                    "value": value,
                    "action": action
                },

            }).done(function (data) {
                console.log(data);
                showAdminMessage(data);

            });
        }

    </script>
    <div class="admin-message" style="display: none;">
        <div id="message-text" class="message-text"></div>
    </div>
<?php
include("footer.php");
?>