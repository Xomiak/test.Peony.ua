
<?php
$currency = userdata('currency');
if (!$currency) $currency = 'uah';

$currensy_grn = getCurrencyValue('UAH');
$currensy_rub = getCurrencyValue('RUB');
$userCountry = userdata('userCountry');
$userCity = userdata('userCity');
?>
<div class="form-info">
    <h2>Ваши данные</h2>

    <div class="form-group">
        <label>Ваше имя:</label>
        <input id="form_name" type="text" name="name" value="<?php if ($user)
            echo $user['name'] ?>"/>
        <div class="form-error" id="form_name_err" style="display: none">Вы не ввели имя!</div>

    </div>
    <div class="form-group">
        <label>Ваша фамилия:</label>
        <input id="form_lastname" type="text" name="lastname" value="<?php if ($user)
            echo $user['lastname'] ?>"/>
        <div class="form-error" id="form_lastname_err" style="display: none">Вы не ввели
            фамилию!
        </div>

    </div>
    <div class="form-group">
        <label>E-mail:</label>
        <input id="form_email" type="email" name="email" value="<?php if ($user)
            echo $user['email'] ?>"/>
        <div class="form-error" id="form_email_err" style="display: none">Вы не ввели e-mail!
        </div>
    </div>

    <div class="form-group">
        <label>Телефон:</label>
        <input id="form_tel" type="text" name="tel" value="<?php if ($user)
            echo $user['tel'] ?>"/>
        <div class="form-error" id="form_tel_err" style="display: none">Вы не ввели телефон!
        </div>
    </div>
    <div class="form-group">
        <label>Дополнительная информация</label>
        <textarea name="adding"
                  placeholder="<?php if (isset($user) && $user['user_type_id'] == 11) echo 'Тут Вы должны указать все данные о Вашем клиенте, которому мы отправим заказ'; ?>"></textarea>
    </div>
</div>
<div id="countries" class="form-info">
    <script src="/js/jquery.min.js"></script>
    <h2>Адрес доставки</h2>

    <div class="form-group">
        <?php
        if (isset($dpopship) && $dpopship == true) {    // Если дропшиппер
            //                               vd("DROPSHIP");
//                                $otherAddr = false;
//                                if (isset($addresses) && is_array($addresses) && count($addresses) > 0)
            $otherAddr = true;

            ?>
            <div class="form-group" <?php if (!$otherAddr) echo ' style="display:none"' ?>>
                <label>Клиент:</label>
                <select id="addr_id" name="addr_id" style="width: 100%;max-width: 300px;">
                    <option value="0">- Новый -</option>
                    <?php
                    foreach ($addresses as $addr) {
                        $addrLine = $addr['tel'] . ', ';
                        if ($addr['name'] != '') $addrLine .= $addr['name'] . ', ';
                        if ($addr['country'] != '') $addrLine .= $addr['country'] . ', ';
                        if ($addr['city'] != '') $addrLine .= $addr['city'] . ', ';
                        if ($addr['np'] != '') $addrLine .= ' Новая Почта: ' . $addr['np'];
                        echo '<option test="' . $addr['id'] . '_test' . '" value="' . $addr['id'] . '">' . $addrLine . '</option>';
                    }
                    ?>
                </select>
            </div>

            <script>
                $(document).ready(function () {
                    $("#addr_id").change(function () {
                        var addrId = $(this).val();
                        if (addrId == 0) {    // Очищаем поля
                            $("#form_ds_name").val("");
                            $("#form_ds_tel").val("");
                            $("#form_city").val("");
                            $("#input_np").val("");
                            $('#country').val("");

                            reset_shop_table();
                        } else {    // Достаём адрес клиента из базы
                            $.post("/ajax/get_addr/" + addrId + "/", {
                                async: false,
                                // Параметр передаваемый в скрипт
                            }, function (data) {
                                // Присвоение возвращённых данных (data), элементу с id=delivery
                                data = jQuery.parseJSON(data);

                                /* Если массив не пуст (т.е. статьи там есть) */
                                if (data) {
                                    userdata('set', 'country', data.country_id);
                                    $("#form_ds_name").val(data.name);
                                    $("#form_ds_tel").val(data.tel);
                                    $("#form_city").val(data.city);
                                    $("#input_np").val(data.np);
                                    $('#country').val(data.country_id);
                                    setDeliveryMethod(data.country_id);
                                    reset_shop_table();
                                }
                            });
                        }
                    });
                });
            </script>

            <div class="form-group">
                <label>ФИО:</label>
                <input id="form_ds_name" type="text" name="ds_name" value=""/>
                <div class="form-error" id="form_ds_name_err" style="display: none">Вы не ввели
                    ФИО
                    получателя!
                </div>
            </div>
            <div class="form-group">
                <label>Телефон:</label>
                <input id="form_ds_tel" type="text" name="ds_tel" value=""/>
                <div class="form-error" id="form_ds_name_err" style="display: none">Вы не ввели
                    телефон получателя!
                </div>
            </div>
            <?php
        }
        ?>
        <label>Страна:</label>
        <? //vd(userdata('userCountry'));?>
        <select id="country" name="country">
            <option></option>
            <?php
            $count = count($countries);
            for ($i = 0; $i < $count; $i++) {
                $country = $countries[$i];
                echo '<option';
                if ((userdata('country') == $country['name']) || (userdata('country') == $country['id'])) echo ' selected';
                elseif (isset($user['country']) && $user['country'] == $country['name']) echo ' selected';
                elseif ($userCountry == $country['name']) echo ' selected';
                echo ' value="' . $country['id'] . '">' . $country['name'] . '</option>';
            }
            ?>
            <option value="other">Другая...</option>
        </select>
        <div class="form-error" id="form_country_err" style="display: none">Вы не выбрали
            страну!
        </div>

    </div>
    <div id="other_country" style="display: none" class="form-group">
        <label>Страна:</label>
        <input type="text" name="other_country" value=""/>
    </div>

    <div class="form-group">
        <label>Город:</label>
        <input id="form_city" type="text" name="city" = "true" value = "<?= $userCity ?>"/>
    </div>
    <div class="form-error" id="form_city_err" style="display: none">Вы не указали город!</div>
    <div id="adres">


        <script>
            $(document).ready(function () {
                var selCountry = $("#country").val();
                if (selCountry != "") {
                    $(".inw").hide();
                    setDeliveryMethod(selCountry);
                }
            });

            /* Изменяем способы доставки в зависимости от страны */
            function setDeliveryMethod(country_id) {
                // Посылка запроса
                $.post("/ajax/country/", {
                    async: false,
                    // Параметр передаваемый в скрипт
                    "country_id": country_id
                }, function (data) {

                    // Присвоение возвращённых данных (data), элементу с id=delivery
                    $("#delivery").html(data);
                });
            }
            /* //Изменяем способы доставки в зависимости от страны */

            function reset_shop_table() {
                console.log('Обновляем данные заказа...');
                var path_complete = "/my_cart/complete/";
                if ($("#country").val() == 2) {
                    path_complete = path_complete + "?country=2";
                    //	alert(path_complete);
                }
                $.post(path_complete, {
                    // Параметр передаваемый в скрипт
                    "country": $("#country").val()
                }, function (data) {
                    // Присвоение возвращённых данных (data), элементу с id=result_div
                    $("#result_div").html(data);
                    <?php
                    if (isDebug()) {
                        //	echo 'alert($("#country").val());';
                    }
                    ?>
                    $("html, body").animate({scrollTop: 100}, 600);
                });
            }


            $(document).ready(function () {

                set_currencies();
                var isCountrySetted = false;

                $("#country").change(function () {
                    $(".inw").hide();
                    $("#delivery").show();
                    var sel = $('#country').val();

                    userdata('set', 'country', sel);
                    if (sel != 1) {
                        userdata('unset', 'npnp', false);
                        $('#npnp').removeAttr('checked');

                    }
                    //alert(sel);

                    setDeliveryMethod(sel);


                    if (sel == 'other') {
                        alert("Внимание! Стоимость доставки в Вашу страну необходимо уточнить у нашего оператора!");
                        $("#nalogenniy_platej").attr('disabled', 'disabled');
                        $("#nalogenniy_platej").removeAttr('checked');
                        $("#ukraine_only").hide(500);
                        $("#other_country").show();
                        $("#other_country input").attr('', '');
                    }
                    else if (sel == "") {
                        $("#delivery").hide();
                    }
                    else {
                        $("#other_country").hide();
                        $("#other_country input").removeAttr('');
                    }

                    set_currencies();
                    reset_shop_table();
                    if (sel != 1) {
                        //reset_shop_table();
                        $("#russia-delivery").show(2000);
                        var full = fullPrice + russiaDelivery;
                        $("#full_price_usd").html(full);
                        $("#full_price_uah").html(Math.round(full * currencyUah).toFixed(2));
                        $("#full_price_rub").html(Math.round(full * currencyRub).toFixed(2));
                        isCountrySetted = true;
                        $("html, body").animate({scrollTop: 100}, 600);
                    } else {
                        $("#russia-delivery").hide(2000);
                        if (isCountrySetted) {
                            $("#full_price_usd").html(fullPrice);
                            $("#full_price_uah").html(Math.round((fullPrice) * currencyUah).toFixed(2));
                            $("#full_price_rub").html(Math.round((fullPrice) * currencyRub).toFixed(2));
                        }
                    }

                });
            });

            function show_all_currencies() {
                $("#currency_usd").show();
                $("#currency_uah").show();
                $("#currency_rub").show();
            }

            function set_currencies() {
                var sel = $("#country").val();
                if (sel != '') {
                    if (sel == 1) {
                        show_all_currencies();
                        $(".currency_option").show();
                        $("#nalogenniy_platej").removeAttr('disabled');
                        $("#ukraine_only").show(500);
                        $("#currency_usd").attr('disabled', 'disabled');
                        $("#currency_rub").attr('disabled', 'disabled');
                        $("#currency_uah").removeAttr('disabled');
                        $("#currency_usd").hide();
                        $("#currency_rub").hide();
                        $("#order_currency_id").val("uah");
                        set_currency('uah');
                    } else if (sel == 2) {
                        show_all_currencies();
                        $(".currency_option").show();
                        $("#nalogenniy_platej").attr('disabled', 'disabled');
                        $("#nalogenniy_platej").removeAttr('checked');
                        $("#ukraine_only").hide(500);
                        $("#currency_uah").attr('disabled', 'disabled');
                        $("#currency_rub").removeAttr('disabled');
                        $("#currency_usd").removeAttr('disabled');
                        $("#order_currency_id").val("rub");
                        $("#currency_uah").hide();
                        set_currency('rub');
                    } else {
                        show_all_currencies();
                        $(".currency_option").show();
                        $("#nalogenniy_platej").attr('disabled', 'disabled');
                        $("#nalogenniy_platej").removeAttr('checked');
                        $("#ukraine_only").hide(500);
                        $("#currency_uah").attr('disabled', 'disabled');
                        $("#currency_rub").attr('disabled', 'disabled');
                        $("#currency_usd").removeAttr('disabled');
                        $("#order_currency_id").val("usd");
                    }
                }
            }

        </script>
        <style>
            .inw {
                display: none;
            }
        </style>
    </div>
    <h2 class="pay-var">Способ оплаты</h2>
    <ul class="pay-var-list">

        <li>
            <input required class="payment_method" checked id="bank_transfer" type="radio"
                   name="payment"
                   value="Перевод на карту Приват Банка" type="text" placeholder="">
            <label for="bank_transfer">Перевод на карту Приват Банка</label>

        </li>

        <?php
        $liqpay = $this->model_options->getOption('liqpay');
        if ($liqpay == 1) {
            ?>
            <li>
                <input class="payment_method" required type="radio" id="pay_by_liqpay"
                       name="payment" value="liqpay" type="text"
                       placeholder=""<?php if (userdata('paymentmethod') == 'liqpay') echo ' checked'; ?>>
                <label for="pay_by_liqpay">Банковской картой любого банка (LiqPay)</label>

            </li>
            <?php
        }
        $interkassa = $this->model_options->getOption('interkassa');
        if ($interkassa == 1 || isset($_GET['interkassa'])) {
            ?>
            <li>
                <input required class="payment_method" id="interkassa" type="radio"
                       name="payment"
                       value="Интеркасса" type="text"
                       placeholder=""<?php if (userdata('paymentmethod') == 'Интеркасса') echo ' checked'; ?>>
                <label for="interkassa">Интеркасса</label><span data-toggle="modal"
                                                                data-target=".interkassa"><img
                        style="cursor: pointer; padding-left: 5px;"
                        src="/img/info-green.png"
                        alt="info" title="Информация об Интеркассе"/></span>
            </li>
            <?php
        }
        ?>
        <li>
            <input required class="payment_method" id="international" type="radio"
                   name="payment"
                   value="Международный денежный перевод" type="text"
                   placeholder=""<?php if (userdata('paymentmethod') == 'Международный денежный перевод') echo ' checked'; ?>>
            <label for="international">Международный денежный перевод</label><span
                data-toggle="modal"
                data-target=".international"><img
                    style="cursor: pointer; padding-left: 5px;" src="/img/info-green.png"
                    alt="info" title="Информация о международных платежах"/></span>
        </li>
        <li style="clear:both;width: 100%;">
            <!--input type="hidden" name="currency" value="<?= $currency ?>" /--></li>
        <li>
            <label> Выбор валюты:</label>
            <select id="order_currency_id" name="currency">
                <option <?php if ($currency == 'usd') echo ' selected'; ?>
                    class="currency_option"
                    id="currency_usd"
                    value="usd">Долар
                </option>

                <option <?php if ($currency == 'uah') echo ' selected'; ?>
                    class="currency_option"
                    id="currency_uah"
                    value="uah">Гривна
                </option>

                <option <?php if ($currency == 'rub') echo ' selected'; ?>
                    class="currency_option"
                    id="currency_rub"
                    value="rub">Рубль
                </option>
            </select>
        </li>
        <li>
            <div class="oferta-div">
                <input id="oferta" type="checkbox" name="oferta"/> <label for="oferta">С <a
                        style="color: black; text-decoration: underline; font-size: 18px"
                        href="/oferta/" target="_blank">публичной офертой</a>
                    ознакомлен</label>
            </div>
            <div class="form-error" id="form_oferta_err" style="display: none">Вы должны
                подтвердить
                ознакомление!
            </div>
        </li>
    </ul>
</div>
<div class="form-info">
    <div id="delivery">
    </div>

    <div id="passport" class="form-group inw">
        <label>Паспорт:</label>
        <input type="text" name="passport" value="<?php if ($user)
            echo $user['passport'] ?>"/>
    </div>
    <div id="adress" class="form-group inw">
        <label>Адрес:</label>
        <input type="text" name="adress" value="<?php if ($user)
            echo $user['adress'] ?>"/>
    </div>
    <div id="zip" class="form-group inw">
        <label>Почтовый индекс:</label>
        <input type="text" name="zip" value="<?php if ($user)
            echo $user['zip'] ?>"/>
    </div>
    <div id="np" class="form-group inw" style="text-align: right">

        <label style="display: inline;">№ отделения:</label>
        <input style="width: 100px;" id="input_np" type="text" name="np" value="<?php if ($user)
            echo $user['np'] ?>"/>
        <div style="clear: both"></div>

        <?php
        if(userdata('type_id') != 11){
        ?>
        <label for="npnp"><input
                id="npnp" style="width: auto" type="checkbox"
                <?php if (userdata('npnp')) echo ' checked'; ?>
                name="npnp"/> Наложенный платёж <span
                style="font-size: 11px; font-weight: normal">(с предоплатой
                                    <span class="curval price-uah"<?php if ($currency != 'uah') echo ' style="display:none"'; ?>><?= ($npnp_price * $currensy_grn) ?>
                                        грн</span>
                                    <span class="curval price-usd"<?php if ($currency != 'usd') echo ' style="display:none"'; ?>><?= $npnp_price ?>
                                        $</span>
                                    <span class="curval price-rub"<?php if ($currency != 'rub') echo ' style="display:none"'; ?>><?= ($npnp_price * $currensy_rub) ?>
                                        р</span>
                                    )</span><span
                data-toggle="modal"
                data-target=".nalogenniy"><img
                    style="cursor: pointer; padding-left: 5px;" src="/img/info-green.png"
                    alt="info"
                    title="Информация об оплате наложенным платежом"/></span></label>
    </div>
    <?php } ?>

    <script type="text/javascript">
        function hideErrors() {
            j('#form_name_err').hide();
            j('#form_lastname_err').hide();
            j('#form_email_err').hide();
            j('#form_tel_err').hide();
            j('#form_country_err').hide();
            j('#form_city_err').hide();
            j('#form_oferta_err').hide();
        }
        function userdata(action, name, value) {
            j.ajax({
                url: '/ajax/userdata/' + action + '/?name=' + name + '&value=' + value,
                method: 'post',
                async: false,
                data: {
                    "name": name,
                    "value": value
                },

            }).done(function (data) {
                console.log(data);
            });
        }

        function get_one_click_content() {
            j.ajax({
                url: '/ajax/fast_order_form/',
                method: 'post',
                async: false,
                data: {},

            }).done(function (data) {
                j("#one-click-content").html(data);
            });
        }

        $(document).ready(function () {

            $("#delivery_2").click(function () {
                $("#input_np").focus();
            });

            $("#npnp").change(function () {
                var npnp = $(this).prop('checked');
                if (npnp == true) {
                    userdata('set', 'npnp', true);
                    console.log('Наложенный');
                } else {
                    userdata('unset', 'npnp', false);
                    console.log('Не наложенный');
                }
                reset_shop_table();
            });

            $(".payment_method").click(function () {
                var paymentMethod = $(this).val();
                userdata('set', 'paymentmethod', paymentMethod);
                reset_shop_table();
            });

            var timerErrShow = 4000;
            // ВАЛЮТА В ЗАВИСИМОСТИ ОТ СПОСОБА ДОСТАВКИ
            $('input[name=payment]').change(function () {
                console.log($('input:radio[name=payment]:checked').val());
                if ($('input:radio[name=payment]:checked').val() === 'interkassa') {
                    $("#currency_usd").removeAttr('disabled');
                    $("#currency_rub").removeAttr('disabled');
                    $("#currency_usd").removeAttr('title');
                    $("#currency_rub").removeAttr('title');
                } else {
                    $("#currency_usd").attr('disabled', 'disabled');
                    $("#currency_usd").attr('title', 'С выбранным способом оплаты нельзя платить в этой валюте');
                    $("#currency_rub").attr('disabled', 'disabled');
                    $("#currency_rub").attr('title', 'С выбранным способом оплаты нельзя платить в этой валюте');
                }
            });

            // ПРОВЕРК5А ПРАВИЛЬНОСТИ ЗАПОЛНЕНИЯ ФОРМЫ
            $('#address-form').submit(function () {
                alert("start");
                var chkOk = checkAddrForm();
                alert(chkOk);
            });


            $("#input_np").keydown(function (event) {
                // Разрешаем: backspace, delete, tab и escape
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                    // Разрешаем: Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Разрешаем: home, end, влево, вправо
                    (event.keyCode >= 35 && event.keyCode <= 39)) {
                    // Ничего не делаем
                    return;
                }
                else {
                    // Обеждаемся, что это цифра, и останавливаем событие keypress
                    if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                        event.preventDefault();
                    }
                }
            });
        });

        function checkAddrForm() {
            var isOked = true;
//					if(shopCount <= 3)
//					{
//						alert("min");
//						$('#modal_kolvo').modal('show');
//						return false;
//					}
//					return false;
            if ($("#oferta").prop('checked') == false) {
                isOked = false;
                $("#form_oferta_err").show();
                setTimeout(hideErrors, timerErrShow);
            }
            if (jQuery.trim($("#form_name").val()) == '') {
                isOked = false;
                $("#form_name_err").show();
                setTimeout(hideErrors, timerErrShow);
            }
            if (jQuery.trim($("#form_lastname").val()) == '') {
                isOked = false;
                $("#form_lastname_err").show();
                setTimeout(hideErrors, timerErrShow);
            }
            if ($("#form_email").val() == '') {
                isOked = false;
                $("#form_email_err").show();
                setTimeout(hideErrors, timerErrShow);
            }
            if ($("#form_tel").val() == '') {
                isOked = false;
                $("#form_tel_err").show();
                setTimeout(hideErrors, timerErrShow);
            }
            if ($("#country").val() == '') {
                isOked = false;
                $("#form_country_err").show();
                setTimeout(hideErrors, timerErrShow);
            }
            if (jQuery.trim($("#form_city").val()) == '') {
                isOked = false;
                $("#form_city_err").show();
                setTimeout(hideErrors, timerErrShow);
            }

            return isOked;
        }
    </script>
    <br/><br/><br/><br/>

</div>

