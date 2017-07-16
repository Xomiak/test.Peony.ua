<?php
include("application/views/admin/header.php");
?>
    <!--    ===== LOADER =====-->
    <div class="loader" style="display: none">

        <div class="cssload-thecube">
            <h1>Жди</h1>
            <div class="cssload-cube cssload-c1"></div>
            <div class="cssload-cube cssload-c2"></div>
            <div class="cssload-cube cssload-c4"></div>
            <div class="cssload-cube cssload-c3"></div>
        </div>
    </div>
    <!--    ===== #/LOADER =====-->

    <script type="text/javascript">
        var loading = false;
        var setCount = false;
        $.ajaxSetup({
            async: false,
            type: 'POST'
        });

        function showLoader(delayTime = 500) {
//            if(loading == false) {
//                loading = true;
//                $(".loader").fadeIn(200).delay(delayTime);
//            }
        }

        function hideLoader(delayTime = 500) {
//            if(loading == true) {
//                $(".loader").fadeOut(200).delay(delayTime);
//
//                loading = false;
//            }
        }

        function resetOrdersTable() {
            if (loading == false)
                showLoader();
            //alert('Обновляем таблицу...');

            $.ajax({
                async: false,
                url: "/admin/ajax/get_order_products/<?=$order['id']?>/",
                data: {
                    'action': 'resetOrderTable'
                },
                success: function (data) {
                    console.log("Обновили!");
                    $("#order_products").html(data);
                    hideLoader();
                }
            });
            if (loading == true) {
                console.log("loading = true");
                hideLoader();
                loading = false;
            }
        }

        function setActiveSizes(sizes) {
            showLoader();
            setSizesList();
            var result = sizes.split('*');
            $(".size_val").each(function (index) {
                var optText = $(this).val();
                if (optText != '') {
                    if ($.inArray(optText, result) == -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                    console.log(index + ": " + $(this).text());
                }
            });
            hideLoader();
            return false;
        }

        function orderProductCountSet(shop_id, size, count) {
            if (setCount == true) {
                $.ajax({
                    url: "/admin/ajax/edit_order/<?=$order['id']?>/",
                    async: false,
                    data: {
                        'action': 'set_count',
                        'order_id': <?=$order['id']?>,
                        'shop_id': shop_id,
                        'count': count,
                        'size': size
                    },
                    success: function (data) {
                        setCount = false;
                        $("#result").html(data);
                        resetOrdersTable();
                    }
                });
                if (loading == true)
                    hideLoader();
            }
        }

        function addingProductToOrder(shop_id, size, count) {
            showLoader();
            $.ajax({
                async: false,
                url: "/admin/ajax/edit_order/<?=$order['id']?>/",
                data: {
                    'action': 'add',
                    'order_id': <?=$order['id']?>,
                    'shop_id': shop_id,
                    'count': count,
                    'size': size
                },
                success: function (data) {
                    $("#result").html(data);
                    resetOrdersTable();
                    hideLoader();
                }
            });
        }

        function delFromOrder(order_id, shop_id, size) {
            showLoader();

            $.ajax({
                async: false,
                url: "/admin/ajax/edit_order/<?=$order['id']?>/",
                data: {
                    'action': 'delete',
                    'order_id': order_id,
                    'shop_id': shop_id,
                    'size': size
                },
                success: function (data) {
                    $("#result").html(data);
                    resetOrdersTable();
                    hideLoader();
                }
            });
        }

        var products = [
            <?php
            $modelShop = getModel('shop');
            $all = $modelShop->getArticles(-1, -1);
            if($all){
            foreach ($all as $item){
            $sizesArr = $item['razmer'];
            $name = '[' . $item['id'] . '] ' . $item['name'] . ' (' . $item['color'] . ')';
            $label = $name .= ' Размеры: ' . str_replace('*', ', ', $item['razmer']);
            ?>
            {
                id: "<?=$item['id']?>",
                value: "<?=$name?>",
                label: "<?=$label?>",
                desc: "<?=$sizesArr?>",
                icon: "<?=$item['image']?>"
            },
            <?php
            }
            }
            ?>
        ];

        var users = [
            <?php
            $modelUsers = getModel('users');
            $all = $modelUsers->getUsers();
            if($all){
            foreach ($all as $item){
            $descr = getUserDescription($item);
            ?>
            {
                id: "<?=$item['id']?>",
                value: "[<?=$item['id']?>] <?=$item['lastname']?> <?=$item['name']?>",
                label: "[<?=$item['id']?>] <?=$item['lastname']?> <?=$item['name']?> (<?=$item['email']?>)",
                desc: " <?=$descr?>",
                icon: "<?=$item['photo']?>"
            },
            <?php
            }
            }
            ?>
        ];


        $(function () {
            function log2(message) {
                $("<div>").text(message).prependTo("#log2");
                $("#log").scrollTop(0);
            }


            $("#inp_client").easyAutocomplete({
                source: users,
                minLength: 2,
                select: function (event, ui) {
                    $("#user_id").val(ui.item.id);
                    $("#inp_client").val(ui.item.label);
                    $("#user_id").val(ui.item.value);
                    $("#client_description").html(ui.item.desc);
                    $("#user_photo").attr("src", ui.item.icon);

                    return false;
                }
            });
        });


        <?php
        if($order['dropship_id'] != 0) echo 'var isDropship = true;';
        else echo 'var isDropship = false;';
        ?>
        $(document).ready(function () {
            $('#is_dropship').change(function () {
                if(isDropship == true){
                    $("#dropship_adress").hide();
                    isDropship = false;
                } else{
                    $("#dropship_adress").show();
                    isDropship = true;
                }
            });
        });
    </script>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("application/views/admin/menu.php"); ?></td>
            <td width="20px"></td>
            <td valign="top">
                <div class="title_border">
                    <div class="content_title"><h1><?= $title ?></h1></div>
                    <div class="back_and_exit">
                        русский <a href="/en<?= $_SERVER['REQUEST_URI'] ?>">english</a>

                        <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                        <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                    </div>
                </div>

                <div class="content">
                    <div class="top_menu">
                        <div class="top_menu_link"><a href="/admin/orders/">Заказы</a></div>
                    </div>
                    <strong><font color="Red"><?= $err ?></font></strong>
                    <?php
                    if ($order['order_sms_sended'] == 1) {
                        echo 'SMS уведомление о заказе было отправлено: ' . json_decode($order['order_sms_result']);
                    }
                    if ($order['ttn_sms_sended'] == 1) {
                        echo 'SMS уведомление с ТТН было отправлено: ' . json_decode($order['ttn_sms_result']);
                    }
                    ?>
                    <form enctype="multipart/form-data" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <table>
                            <tr>
                                <td>ID:</td>
                                <td><input disabled type="text" name="id" size="50" value="<?= $order['id'] ?>"/></td>
                            </tr>
                            <tr>
                                <td>Дата:</td>
                                <td><input disabled type="text" size="50"
                                           value="<?= $order['date'] ?> <?= $order['time'] ?>"/></td>
                            </tr>
                            <tr>
                                <td>Клиент:</td>
                                <td>
                                    <? //vd($user);?>




                                    <?php
                                    $inpClient = '';
                                    if (isset($user) && $user != false) {
                                        $inpClient = '[' . $user['id'] . '] ' . $user['lastname'] . ' ' . $user['name'];
                                    }
                                    $country = $order['country'];
                                    if(!$country || $country == '') $country = $user['country'];
                                    $city = $order['city'];
                                    if(!$city || $city == '') $city = $user['city'];
                                    $np = $order['np'];
                                    if(!$np || $np == '') $np = $user['np'];

                                    ?>
                                    <img id="user_photo" src="/images/transparent_1x1.png" class="ui-state-default"
                                         alt=""><br/>
                                    <input id="inp_client" type="text" name="client" value="<?= $inpClient ?>"
                                           size="50"> [<a target="_blank"
                                                          href="/admin/users/edit/<?= $order['user_id'] ?>/">редактировать</a>]
                                    <input type="hidden" id="user_id"
                                           value="<?php if (isset($order['user_id'])) echo $order['user_id']; ?>">


                                    <p id="client_description"
                                       style="font-size: 12px"><?php if ($user) echo getUserDescription($user); ?></p>

                                </td>
                            </tr>
                            <tr>
                                <td>Телефон:</td>
                                <td><input required type="tel" name="tel" value="<?php if ($user) echo $user['tel']; ?>"  /></td>
                            </tr>
                            <tr>
                                <td>Страна:</td>
                                <td><input required type="text" name="country" value="<?php echo $country; ?>"  /></td>
                            </tr>
                            <tr>
                                <td>Город:</td>
                                <td><input required type="text" name="city" value="<?php echo $city; ?>"  /></td>
                            </tr>
                            <tr>
                                <td>Отделение НП:</td>
                                <td><input type="text" name="np" value="<?php echo $np; ?>"  /></td>
                            </tr>
                            <tr>
                                <td>Заказ от дропшиппера:</td>
                                <td>
                                    <input id="is_dropship" type="checkbox" name="is_dropship" <?php if($order['dropship_id'] != 0) echo ' checked';?> />
                                    <div id="dropship_adress"<?php if($order['dropship_id'] == 0) echo 'style="display:none;"'; ?>>
                                        <select name="addr_id">
                                            <option value="0"></option>
                                            <?php
                                            loadHelper('admin');
                                            $addrs = getAdresses();
                                            if($addrs){
                                                foreach ($addrs as $addr){
                                                    $addrStr = '['.$addr['id'].'] '.$addr['name'].', '.$addr['tel'].', '.$addr['country'].', '.$addr['city'].', НП№'.$addr['np'];
                                                    echo '<option value="'.$addr['id'].'"';
                                                    if($addr['id'] == $order['dropship_id']) echo ' selected';
                                                    echo '>'.$addrStr.'</option>';
                                                }
                                            }
                                            ?>

                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Оплата:</td>
                                <td>
                                    <SELECT name="payment" id="payment">
                                        <option>Не указан</option>
                                        <option value="Перевод на карту Приват Банка"<?php if ($order['payment'] == 'Перевод на карту Приват Банка') echo ' selected'; ?>>
                                            Перевод на карту Приват Банка
                                        </option>
                                        <option value="liqpay"<?php if ($order['payment'] == 'liqpay') echo ' selected'; ?>>
                                            liqpay
                                        </option>
                                        <option value="interkassa"<?php if ($order['payment'] == 'interkassa') echo ' selected'; ?>>
                                            interkassa
                                        </option>
                                        <option value="Международный денежный перевод"<?php if ($order['payment'] == 'Международный денежный перевод') echo ' selected'; ?>>
                                            Международный денежный перевод
                                        </option>

                                    </SELECT>
                                </td>

                            </tr>
                            <tr>
                                <td>Доставка:</td>
                                <td><input type="text" size="50" value="<?= $order['delivery'] ?>"/></td>
                            </tr>
                            <tr>
                                <td>Статус:</td>
                                <td>
                                    <SELECT name="status" id="status">
                                        <option value="new"<?php if ($order['status'] == 'new') echo ' selected'; ?>><?= getStatus('new') ?></option>
                                        <option value="processing"<?php if ($order['status'] == 'processing') echo ' selected'; ?>><?= getStatus('processing') ?></option>
                                        <option value="one_click"<?php if ($order['status'] == 'one_click') echo ' selected'; ?>><?= getStatus('one_click') ?></option>
                                        <option value="npnp_payed"<?php if ($order['status'] == 'npnp_payed') echo ' selected'; ?>><?= getStatus('npnp_payed') ?></option>
                                        <option value="payed"<?php if ($order['status'] == 'payed') echo ' selected'; ?>><?= getStatus('payed') ?></option>
                                        <option value="error"<?php if ($order['status'] == 'error') echo ' selected'; ?>><?= getStatus('error') ?></option>
                                        <option value="wait_accept"<?php if ($order['status'] == 'wait_accept') echo ' selected'; ?>><?= getStatus('wait_accept') ?></option>
                                        <option value="process"<?php if ($order['status'] == 'process') echo ' selected'; ?>><?= getStatus('process') ?></option>
                                        <option value="not_payed"<?php if ($order['status'] == 'not_payed') echo ' selected'; ?>><?= getStatus('not_payed') ?></option>
                                        <option value="sended"<?php if ($order['status'] == 'sended') echo ' selected'; ?>><?= getStatus('sended') ?></option>
                                        <option value="done"<?php if ($order['status'] == 'done') echo ' selected'; ?>><?= getStatus('done') ?></option>
                                        <option value="canceled"<?php if ($order['status'] == 'canceled') echo ' selected'; ?>><?= getStatus('canceled') ?></option>
                                        <option value=" fail"<?php if ($order['status'] == ' fail') echo ' selected'; ?>><?= getStatus(' fail') ?></option>
                                    </SELECT>
                                    <div id="sendSMS"></div>
                                    <?php
                                    //                                    $smsMessage = getOption('sms_sumTemplate');
                                    //                                    $orderPrice = $order['full_summa'] * getCurrencyValue($order['currency']);
                                    //                                    $orderPrice .= ' ' . $order['currency'];
                                    //                                    $smsMessage = str_replace('[order_id]', $order['id'], $smsMessage);
                                    //                                    $smsMessage = str_replace('[order_sum]', $orderPrice, $smsMessage);
                                    ?>


                                </td>
                            </tr>
                            <tr>
                                <td>ТТН:</td>
                                <td><input id="inp_ttn" type="text" size="50" name="ttn" value="<?= $order['ttn'] ?>"/>&nbsp;<span
                                            id="sms_message" style="color: red"></span></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr/>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top">Заказ:</td>
                                <td>

                                    <div style="" id="order_products">
                                        <?php
                                        //$my_cart = unserialize($page['products']);
                                        $data = $order['products'];
                                        $my_cart = $data;
                                        //var_dump($my_cart);
                                        $my_cart = unserialize($my_cart);
                                        //vd($my_cart);
                                        //var_dump($my_cart);
                                        $pcount = count($my_cart);
                                        loadHelper('admin');
                                        showOrderProducts($order['id'], true);

                                        //vd($my_cart);
                                        ?>
                                    </div>



                                    <div><b>Ссылка на оплату через LiqPay:</b> <a
                                                href="http://<?= $_SERVER['SERVER_NAME'] ?>/payment/liqpay/<?= $order['id'] ?>/"
                                                target="_blank">http://<?= $_SERVER['SERVER_NAME'] ?>
                                            /payment/liqpay/<?= $order['id'] ?>/</a></div>

                                    <div>Результат аякс запроса:</div>
                                    <div id="result"></div>


                                </td>
                            </tr>
                            <?php
                            //var_dump($order['currencies']);
                            $currencies = json_decode($order['currencies'], true);
                            //                        var_dump($currencies);
                            //                        if(!$currencies){
                            //                            echo "NO";
                            //                        }
                            ?>
                            <tr>
                                <td>Курс на момент оформления:</td>
                                <td>
                                    UAH: <?= $currencies['UAH'] ?><br/>
                                    RUB: <?= $currencies['RUB'] ?>
                                </td>
                            </tr>
                            <!--tr>
                                <td>Сумма:</td>
                                <td>
                                    <?= $order['summa'] ?>$
                                </td>
                            </tr>
                            <tr>
                                <td>Сумма в выбранной валюте:</td>
                                <td>
                                    <?php
                            if (isset($order['currency']) && $order['currency'] == 'uah')
                                $order['summa'] = $order['summa'] * $currencies['UAH'];
                            elseif (isset($order['currency']) && $order['currency'] == 'rub')
                                $order['summa'] = $order['summa'] * $currencies['RUB'];
                            ?>
                                    <?= $order['summa'] ?> <?= $order['currency'] ?>
                                </td>
                            </tr-->

                            <tr>
                                <td>Адрес:</td>
                                <td><textarea class="ckeditor" name="adress"><?= $order['adress'] ?></textarea></td>
                            </tr>

                            <?php
                            if ($order['pay_answer'] != '') {
                                ?>
                                <tr>
                                    <td>Ответ платёжной системы:</td>
                                    <td>
                                        <?php
                                        var_dump(unserialize($order['pay_answer']));
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                            <tr>
                                <td colspan="2"><input type="checkbox"
                                                       name="active"<? if ($order['active'] == 1) echo ' checked' ?> />
                                    Активный
                                </td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="save" value="Сохранить"/></td>
                                <td><input type="submit" name="save_and_stay" value="Сохранить и остаться"/></td>
                            </tr>
                        </table>
                    </form>
                </div>


            </td>
        </tr>
    </table>
    <div class="order-add-product" id="adding_product">
        <div style="position: absolute; right:4px;top:0;font-weight: bolder; cursor: pointer;"><span onclick="show_adding()" title="Показать"
                                                                                    id="adding_product_show">+</span>
        </div>
        <div id="adding_form">
            <div style="position: absolute; right:15px;top:0;font-weight: bolder; cursor: pointer;"><span onclick="hide_adding()" title="Спрятать"
                                                                                        id="adding_product_hide">&mdash;</span></div>
            <h3>Добавление товара в заказ</h3>


            <?php
            $inpClient = '';
            if (isset($user) && $user != false) {
                $inpClient = '[' . $user['id'] . '] ' . $user['lastname'] . ' ' . $user['name'];
            }
            ?>
            <img id="adding_product_image" src="/images/transparent_1x1.png"
                 class="ui-state-default"
                 alt=""><br/>
            <input id="inp_adding_product" placeholder="ID, либо название товара"
                   type="text" name="adding_product" value="" size="50"><br/>

            <select id="add_product_size" placeholder="Размер">
                <option value="0">- размер -</option>
                <?php
                $sizes = $this->shop->getAllSizes();
                foreach ($sizes as $size) {
                    echo '<option class="size_val" value="' . $size['name'] . '">' . $size['name'] . '</option>';
                }
                ?>
            </select>
            <br/>
            <input placeholder="Кол-во" type="number" value="1" id="add_product_count"/><br>
            <img id="added-product-image" src="" style="display: none" />
            <input type="hidden" id="add_product_id" value="">
            <button id="add_product_to_order" onclick="return false">Добавить в заказ
            </button>
        </div>
    </div>
    <script>
        function hide_adding() {
            $("#adding_form").hide();
            $("#adding_product_show").show();
        }
        function show_adding() {
            $("#adding_form").show();
            $("#adding_product_show").hide();
        }
        $(document).ready(function () {
            show_adding();
        });
    </script>
<?php
include("application/views/admin/footer.php");
?>