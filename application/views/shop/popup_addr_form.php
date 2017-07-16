<!--<link rel="stylesheet" href="/js/validation/css/validationengine.jquery.css" type="text/css"/>-->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js" type="text/javascript"></script>-->
<!--<script src="/js/validation/languages/jquery.validationengine-ru.js" type="text/javascript" charset="utf-8"></script>-->
<!--<script src="/js/validation/jquery.validationengine.js" type="text/javascript" charset="utf-8"></script>-->

<?php
//var_dump($addr);
$userCountry = userdata('userCountry');
$userCity = userdata('userCity');

$currency = userdata('currency');
if (!$currency) $currency = 'uah';

$currensy_grn = getCurrencyValue('UAH');
$currensy_rub = getCurrencyValue('RUB');

$countries = $this->shop->getCountries();

$npnp_price = getOption('npnp_price');
?>

<h1>Редактирование адреса</h1>
<div class="popup_form_block">

        <div class="form-info popup-info" style="width: 50%;padding-right: 20px;">
            <div class="form-group">
                <h2>Данные получателя</h2>
                <label>ФИО:</label>
                <input class="validate[required]" id="form_name" type="text" name="name" value="<?php
                if (isset($addr['name']))
                    echo $addr['name'];
                elseif($user['user_type_id'] != 11)
                    echo $user['name'].' '.$user['lastname'];
                ?>"/>
                <div class="form-error" id="form_name_err" style="display: none">Вы не ввели ФИО получателя!</div>

            </div>

            <!--div class="form-group">
            <label>E-mail:</label>
            <input id="form_email" type="email" name="email" value="<?php if (isset($addr['email']))
                echo $addr['email'] ?>"/>
            <div class="form-error" id="form_email_err" style="display: none">Вы не ввели e-mail!
            </div>
        </div-->

            <div class="form-group">
                <label>Телефон для уведомления:</label>
                <input class="validate[required,custom[tel]]" id="form_tel" type="text" name="tel" value="<?php
                if (isset($addr['tel']))
                    echo $addr['tel'];
                elseif($user['user_type_id'] != 11)
                    echo $user['tel'];
                ?>"/>
                <div class="form-error" id="form_tel_err" style="display: none">Вы не ввели телефон!
                </div>
            </div>
            <div class="form-group">
                <label>Дополнительная информация</label>
                <textarea name="adding"
                          placeholder="<?php if (isset($user) && $user['user_type_id'] == 11) echo 'Тут Вы должны указать все дополнительные данные о Вашем клиенте, которому мы отправим заказ'; ?>"><?php if (isset($addr['adding']))
                        echo $addr['adding'] ?></textarea>
            </div>
            <div class="form-group">
                <label>Страна:</label>
                <? //vd(userdata('userCountry'));?>
                <select class="validate[required]" id="country" name="country">
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
                <input required id="form_city" type="text" name="city" = "true" value = "<?= $userCity ?>"/>
            </div>
            <div class="form-error" id="form_city_err" style="display: none">Вы не указали город!</div>


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
        </div>
        <div class="form-info popup-info" style="width: 50%;padding-right: 20px;">
            <div id="delivery">
            </div>

            <div id="passport" class="form-group inw">
                <label>Паспорт:</label>
                <input type="text" name="passport" value="<?php if ($user)
                    echo $user['passport'] ?>"/>
                <div class="form-error" id="form_passport_err" style="display: none">Вы не указали № паспорта!</div>
            </div>
            <div id="adress" class="form-group inw">
                <label>Адрес:</label>
                <input type="text" name="adress" value="<?php if ($user)
                    echo $user['adress'] ?>"/>
                <div class="form-error" id="form_adress_err" style="display: none">Вы не указали адрес!</div>
            </div>
            <div id="zip" class="form-group inw">
                <label>Почтовый индекс:</label>
                <input type="text" name="zip" value="<?php if ($user)
                    echo $user['zip'] ?>"/>
                <div class="form-error" id="form_zip_err" style="display: none">Вы не указали индекс!</div>
            </div>
            <div id="np" class="form-group inw" style="text-align: right">

                <label style="display: inline;">№ отделения:</label>
                <input class="validate[required,custom[onlyLetterNumber]]" data-errormessage-value-missing="Номер отделения обязателен к заполнению!" style="width: 100px;" id="input_np" type="text" name="np" value="<?php if ($user)
                    echo $user['np'] ?>"/>
                <div class="form-error" id="form_np_err" style="display: none">Вы не указали отделение Новой Почты!</div>
                <div style="clear: both"></div>

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

            <script type="text/javascript">

                var formSuccess = true;

                function hideErrors() {
                    j('#form_name_err').hide();
                    j('#form_lastname_err').hide();
                    j('#form_email_err').hide();
                    j('#form_tel_err').hide();
                    j('#form_country_err').hide();
                    j('#form_city_err').hide();
                    j('#form_oferta_err').hide();
                    j('#form_delivery_err').hide();
                    j('#form_np_err').hide();
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

//					if(shopCount <= 3)
//					{
//						alert("min");
//						$('#modal_kolvo').modal('show');
//						return false;
//					}
//					return false;

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

                    $("#save_addr").click(function () {
                        console.log("Click");
                        checkAddrForm();
                        //alert(formSuccess);
                        if(formSuccess == true){
                            console.log("Добавляем...");
                        } else alert("Ошибка");


                        return false;
                    });
                });

                function checkAddrForm() {
                    $.validationEngine.defaults.validateAttribute = "class";
                    $("#popup_form").validationEngine('validate');
                    console.log("Проверяем форму...");
                    // alert("Проверяем форму...");
//                timerErrShow = 4000;
//                formSuccess = true;
//
//                if (jQuery.trim($("#form_name").val()) == '') {
//                    formSuccess = false;
//                    console.log('Error: name');
//                    $("#form_name_err").show(1000);
//                    setTimeout(hideErrors, timerErrShow);
//                }
//
//                if ($("#form_tel").val() == '') {
//                    formSuccess = false;
//                    console.log('Error: tel');
//                    $("#form_tel_err").show(1000);
//                    setTimeout(hideErrors, timerErrShow);
//                }
//                if ($("#country").val() == '') {
//                    formSuccess = false;
//                    console.log('Error: country');
//                    $("#form_country_err").show(1000);
//                    setTimeout(hideErrors, timerErrShow);
//                }
//
//                if($(":radio[name=delivery]").val() == 'Новая Почта'){
//                    if($("#input_np").val() == '') {
//                        formSuccess = false;
//                        console.log('Error: np');
//                        $("#form_np_err").show(1000);
//                        setTimeout(hideErrors, timerErrShow);
//                    }
//                }
//                if (jQuery.trim($("#form_city").val()) == '') {
//                    formSuccess = false;
//                    console.log('Error: city');
//                    $("#form_city_err").show(1000);
//                    setTimeout(hideErrors, timerErrShow);
//                }
                    // return formSuccess;
                }
            </script>
        </div>
        <div class="clr"></div>



</div>
<input type="button" id="save_addr" value="Сохранить адрес" />