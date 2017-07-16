<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function buy_one_click($withButton = true, $product_id = false, $getInHtml = false)
{
    $CI = &get_instance();
    if ($product_id) {
// добавляем товар в корзину

    }
    if ($getInHtml)
        ob_start();
    if ($withButton) {
        ?>
        <div class="one-click-div">
            <input <?php if ($product_id) echo ' product_id="' . $product_id . '"'; ?> style="height:32px !important;"
                                                                                       type="button"
                                                                                       id="button_goto_one_click"
                                                                                       value="Купить  в  1  клик"/>
            <!-- <input <?php if ($product_id) echo ' product_id="' . $product_id . '"'; ?> type="button" id="button_goto_one_click" data-toggle="modal" data-target=".buy_one_click" value="Купить  в  1  клик" /> -->
        </div>
        <div <?php if ($product_id) echo ' product_id="' . $product_id . '"'; ?> id="trigger_button_goto_one_click"
                                                                                 data-toggle="modal"
                                                                                 data-target=".buy_one_click"></div>
        <?php
    }
    ?>
    <!--BUY ONE CLICK-->
    <div class="modal fade buy_one_click bs-example-modal-md" tabindex="-1" role="dialog"
         aria-labelledby="mySmalModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content razmernaya-setka">
                <div class="close" type="button" data-dismiss="modal">&times;</div>
                <div id="one-click" class="cart-adress" style="width: 100%; text-align: center">
                    <div id="one-click-form" class="address-form">
                        <input type="hidden" name="action" value="one_click_order"/>

                        <h2>Купить в 1 клик</h2>

                        <div id="one_click_cart_details"></div>

                        <h3>Укажите Ваш контактный номер телефона:</h3>
                        <link rel="stylesheet" href="/css/intlTelInput.css?37">

                        <div id="one-click-content" class="form-group">
                            <?php
                            $CI->load->helper('geoip_helper');
                            $country = getUserCountry();
                            //if(!$country)
                            $telCode = '+380';
                            if ($country == 2 || $country == 'Россия') $telCode = '+7';
                            if (userdata('tel') !== false)
                                $telCode = userdata('tel');
                            ?>
                            <input type="hidden" name="action" value="order"/>

                            <div style="width: 100%; text-align: center">

                                <input type="tel" id="phone" class="form-control" name="one_click_tel"
                                       value="<?= $telCode ?>">
                                <span style="color: green; font-size: 23px; position: absolute;" id="valid-msg"
                                      class="hide">&#10004;</span>
                                <span title="Введённый Вами номер телефона указан не верно!"
                                      style="color: red; font-size: 23px; position: absolute;" id="error-msg"
                                      class="hide">&#10008;</span>
                                <div class="fast-order-submit-div">
                                    <input id="fast_order_finish" type="button" value="Сделать заказ"
                                           onclick="yaCounter26267973.reachGoal('zakaz'); ga('send', 'event', 'zakaz_one_click_korzina', 'click');">
                                </div>

                                <div style="clear: both;width: 100%" id="fastorderresult"></div>

                            </div>
                        </div>


                        <!--                    ОБРАБОТЧИК НОМЕРА ТЕЛЕФОНА В БЫСТРОМ ЗАКАЗЕ-->
                        <script src="/js/jquery-1.11.1.min.js"></script>
                        <script src="/js/intlTelInput.min.js"></script>
                        <script>
                            function getMyCartData() {
                                // alert('click');
                                $.ajax({
                                    url: '/ajax/get_my_cart_details/',
                                    method: 'post',
                                    async: false,
                                    data: {
                                        'type': "my_cart"
                                    },

                                }).done(function (data) {
                                    $("#one_click_cart_details").html(data);
                                });
                            }

                            var telInput = $("#phone"),
                                errorMsg = $("#error-msg"),
                                validMsg = $("#valid-msg");
                            var validTel = false;

                            $("#phone").intlTelInput({
                                initialCountry: "auto",
                                autoHideDialCode: true,
                                preferredCountries: ['ua', 'ru'],
                                separateDialCode: false,
                                geoIpLookup: function (callback) {
                                    $.get('http://ipinfo.io', function () {
                                    }, "jsonp").always(function (resp) {
                                        var countryCode = (resp && resp.country) ? resp.country : "";
                                        callback(countryCode);
                                    });
                                },
                                utilsScript: "/js/utils.js?7"
                            }).done(function () {
                                // analytics
                                $('.selected-flag').one('click', function () {
                                    ga('send', 'event', 'buy-one-click', 'clicked selected country');
                                });
                                $('#phone').one('keyup', function () {
                                    ga('send', 'event', 'buy-one-click', 'typed something in input');
                                });
                            });

                            var reset = function () {
                                telInput.removeClass("error");
                                errorMsg.addClass("hide");
                                validMsg.addClass("hide");
                            };

                            // on blur: validate
                            telInput.blur(function () {
                                isValidTel();
                            });

                            // on keyup / change flag: reset
                            telInput.on("keyup change", isValidTel);


                            function isValidTel() {
                                reset();
                                if ($.trim(telInput.val())) {
                                    if ($.trim(telInput.val())) {
                                        if (telInput.intlTelInput("isValidNumber")) {
                                            validMsg.removeClass("hide");
                                            //$("#fast_order_finish").removeAttr('disabled');
                                            validTel = true;
                                        } else {
                                            //$("#fast_order_finish").attr('disabled','disabled');
                                            telInput.addClass("error");
                                            errorMsg.removeClass("hide");
                                            validTel = false;
                                        }
                                    }
                                }
                            }


                            $(document).ready(function () {
                                //                ONE_CLICK
                                function one_click() {
                                    // alert('one_click');
                                    getMyCartData();
                                    $("#buy_one_click").modal('show');
                                    //  get_one_click_content();
                                }

                                $("#button_goto_one_click").click(function () {
                                    var position = $(this).attr('position');
                                    var product_id = $(this).attr('product_id');
                                    var razmer = false;
                                    if ($("#razmer").length)
                                        razmer = $("#razmer").val();
                                    if ($("#razmer1").length)
                                        razmer = $("#razmer1").val();
                                    if (razmer == '') {
                                        $("#razmererror").show(500);
                                    } else {
                                        //alert(razmer);
                                        if (position != 'my_cart' && product_id != undefined) {
                                            add_product_to_cart(product_id, true);
                                            getMyCartData();
                                            $("#trigger_button_goto_one_click").trigger('click');
                                        }
                                    }

                                });

                                $("#add_product_to_cart").click(function () {
                                    //alert("asd");
                                    add_product_to_cart(<?=$product_id?>);
                                });


                                // добавляем товар в корзину без перезагрузки страницы
                                function add_product_to_cart(product_id, fastOrder = false) {
                                    console.log('Добавляем товар ' + product_id + ' в корзину...');
                                    var kolvo = $("#kolvo").val();
                                    var razmer = false;
                                    if ($("#razmer").length)
                                        razmer = $("#razmer").val();
                                    if ($("#razmer1").length)
                                        razmer = $("#razmer1").val();

                                    if (razmer == '') razmer = false;

                                    if (razmer != false && kolvo > 0) {
                                        $.ajax({
                                            type: 'POST',
                                            url: '/ajax/to_cart/',
                                            async: false,
                                            data: {
                                                'shop_id': product_id,
                                                'kolvo': kolvo,
                                                'razmer': razmer,
                                                'type': 'buy_one_click'
                                            },
                                            success: function (data) {
                                                j('#fast-order-content').html("Товар успешно добавлен в корзину!");
                                                j("#my_cart_count").html(data);
                                                setTimeout(closeModal, 1500);
                                                //alert(data);
                                                function closeModal() {
                                                    $('#fast-order').modal('hide');
                                                    $('.modal-backdrop').hide();
                                                }
                                            },
                                            error: function (xhr, str) {
                                                alert('Возникла ошибка: ' + xhr.responseCode);
                                            }
                                        });


                                    }


                                    //alert(razmer);


                                }
                                <?php
                                $user = false;
                                if (!isset($user) && userdata('login') != false)
                                    $user = getUserIdBylogin(userdata('login'), true);
                                if ($user) echo '$("#phone").intlTelInput("setNumber", "' . $user['tel'] . '");';
                                ?>
                                var countryData = $.fn.intlTelInput.getCountryData();
                                $("#phone").on("countrychange", function (e, countryData) {
                                    var countryIso2 = countryData.iso2;
                                    var country = countryIso2;
                                    if (countryIso2 == 'ru') country = 'Россия';
                                    else if (countryIso2 == 'ua') country = 'Украина';
                                    userdata('set', 'country', country);
                                    isValidTel();
                                });

                                isValidTel();

                                $("#fast_order_finish").keypress(function (e) {
                                    if (e.keyCode == 13) {
                                        //нажата клавиша enter - здесь ваш код
                                        createOrderByOneClick();
                                    } else isValidTel();
                                });

                                function createOrderByOneClick() {
                                    if (validTel) {
                                        $("#fastorderresult").html('Приступаем к оформлению заказа...');
                                        var tel = $("#phone").val();
                                        userdata('set', 'tel', tel);

                                        $.ajax({
                                            url: '/ajax/create_one_click_order/',
                                            method: 'post',
                                            async: false,
                                            data: {
                                                'one_click_tel': tel
                                            },

                                        }).done(function (data) {
                                            $("#fastorderresult").html(data);
                                        });
                                    } else {
                                        $("#fastorderresult").hide();
                                        $("#fastorderresult").html('<span style="color:red">Введите корректный номер телефона в международном формате!</span>');
                                        $("#fastorderresult").show(1000);
                                        setTimeout(function () {
                                            $("#fastorderresult").hide(2000);
                                        }, 4000);
                                    }
                                }

                                //isValidTel();
                                $("#fast_order_finish").click(function () {
                                    $("#fastorderresult").html('Приступаем...');
                                    // ONE ORDER CLICK
                                    //if(isValidTel()){
                                    createOrderByOneClick();
                                    //}
                                });
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($getInHtml) {
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}

function buy_one_click2($withButton = true, $product_id = false)
{
    $CI = &get_instance();
    if ($withButton) {
        ?>
        <div class="one-click-div">
            <input onclick="getMyCartData()" type="button" id="button_goto_one_click" data-toggle="modal"
                   data-target=".buy_one_click" value="Купить  в  1  клик"/>
        </div>
        <?php
    }
    ?>
    <!--BUY ONE CLICK-->
    <div class="modal fade buy_one_click bs-example-modal-md" tabindex="-1" role="dialog"
         aria-labelledby="mySmalModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content razmernaya-setka">
                <button class="close" type="button" data-dismiss="modal">&times;</button>
                <div id="one-click" class="cart-adress" style="width: 100%; text-align: center">
                    <div id="one-click-form" class="address-form">
                        <input type="hidden" name="action" value="one_click_order"/>

                        <h2>Купить в 1 клик</h2>

                        <div id="one_click_cart_details"></div>

                        <h3>Укажите Ваш контактный номер телефона:</h3>
                        <link rel="stylesheet" href="/css/intlTelInput.css?37">

                        <div id="one-click-content" class="form-group">
                            <?php
                            $CI->load->helper('geoip_helper');
                            $country = getUserCountry();
                            //if(!$country)
                            $telCode = '+380';
                            if ($country == 2 || $country == 'Россия') $telCode = '+7';
                            if (userdata('tel') !== false)
                                $telCode = userdata('tel');
                            ?>
                            <input type="hidden" name="action" value="order"/>

                            <div style="width: 100%; text-align: center">

                                <input type="tel" id="phone" class="form-control" name="one_click_tel"
                                       value="<?= $telCode ?>">
                                <span style="color: green; font-size: 23px; position: absolute;" id="valid-msg"
                                      class="hide">&#10004;</span>
                                <span title="Введённый Вами номер телефона указан не верно!"
                                      style="color: red; font-size: 23px; position: absolute;" id="error-msg"
                                      class="hide">&#10008;</span>
                                <div class="fast-order-submit-div">
                                    <input id="fast_order_finish" type="button" value="Сделать заказ"
                                           onclick="yaCounter26267973.reachGoal('zakaz'); ga('send', 'event', 'zakaz_one_click_korzina', 'click');">
                                </div>

                                <div style="clear: both;width: 100%" id="fastorderresult"></div>

                            </div>
                        </div>


                        <!--                    ОБРАБОТЧИК НОМЕРА ТЕЛЕФОНА В БЫСТРОМ ЗАКАЗЕ-->
                        <script src="/js/jquery-1.11.1.min.js"></script>
                        <script src="/js/intlTelInput.min.js"></script>
                        <script>
                            function getMyCartData() {
                                // alert('click');
                                $.ajax({
                                    url: '/ajax/get_my_cart_details/',
                                    method: 'post',
                                    async: false,
                                    data: {
                                        'type': "my_cart"
                                    },

                                }).done(function (data) {
                                    $("#one_click_cart_details").html(data);
                                });
                            }

                            var telInput = $("#phone"),
                                errorMsg = $("#error-msg"),
                                validMsg = $("#valid-msg");
                            var validTel = false;

                            $("#phone").intlTelInput({
                                initialCountry: "auto",
                                autoHideDialCode: true,
                                preferredCountries: ['ua', 'ru'],
                                separateDialCode: false,
                                geoIpLookup: function (callback) {
                                    $.get('http://ipinfo.io', function () {
                                    }, "jsonp").always(function (resp) {
                                        var countryCode = (resp && resp.country) ? resp.country : "";
                                        callback(countryCode);
                                    });
                                },
                                utilsScript: "/js/utils.js?7"
                            }).done(function () {
                                // analytics
                                $('.selected-flag').one('click', function () {
                                    ga('send', 'event', 'buy-one-click', 'clicked selected country');
                                });
                                $('#phone').one('keyup', function () {
                                    ga('send', 'event', 'buy-one-click', 'typed something in input');
                                });
                            });

                            var reset = function () {
                                telInput.removeClass("error");
                                errorMsg.addClass("hide");
                                validMsg.addClass("hide");
                            };

                            // on blur: validate
                            telInput.blur(function () {
                                isValidTel();
                            });

                            // on keyup / change flag: reset
                            telInput.on("keyup change", isValidTel);


                            function isValidTel() {
                                reset();
                                if ($.trim(telInput.val())) {
                                    if ($.trim(telInput.val())) {
                                        if (telInput.intlTelInput("isValidNumber")) {
                                            validMsg.removeClass("hide");
                                            //$("#fast_order_finish").removeAttr('disabled');
                                            validTel = true;
                                        } else {
                                            //$("#fast_order_finish").attr('disabled','disabled');
                                            telInput.addClass("error");
                                            errorMsg.removeClass("hide");
                                            validTel = false;
                                        }
                                    }
                                }
                            }

                            $(document).ready(function () {
                                //                ONE_CLICK
                                function one_click() {
                                    //alert('one_click');
                                    getMyCartData();
                                    $("#buy_one_click").modal('show');
                                    //  get_one_click_content();
                                }

                                $("#button_goto_one_click").click(function () {
                                    var position = $(this).attr('position');
                                    var product_id = $(this).attr('product_id');
                                    var razmer = $("razmer1").val();
                                    //alert(razmer);

                                    //alert(article_id);
                                    if (position != 'my_cart' && product_id != undefined) {
                                        if (razmer != '') {
                                            createOrderByOneClick();
                                        } else $("#razmererror").show(500);
                                        //add_product_to_cart(product_id);
                                    }
                                });


                                function add_product_to_cart(product_id) {
                                    //alert('adding one click');
                                    var kolvo = 1;
                                    var razmer = 0;
                                    if ($("#razmer").length) {
                                        alert('razmer');
                                    }
                                    if ($("#razmer1").length) {
                                        alert('razmer1');
                                    }
                                    $.ajax({
                                        url: '/ajax/to_cart/',
                                        method: 'post',
                                        async: false,
                                        data: {
                                            'shop_id': product_id,
                                            'kolvo': kolvo,
                                            'razmer': razmer
                                        },

                                    }).done(function (data) {
                                        $("#fastorderresult").html(data);
                                    });
                                }
                                <?php
                                $user = false;
                                if (!isset($user) && userdata('login') != false)
                                    $user = getUserIdBylogin(userdata('login'), true);
                                if ($user) echo '$("#phone").intlTelInput("setNumber", "' . $user['tel'] . '");';
                                ?>
                                var countryData = $.fn.intlTelInput.getCountryData();
                                $("#phone").on("countrychange", function (e, countryData) {
                                    var countryIso2 = countryData.iso2;
                                    var country = countryIso2;
                                    if (countryIso2 == 'ru') country = 'Россия';
                                    else if (countryIso2 == 'ua') country = 'Украина';
                                    userdata('set', 'country', country);
                                    isValidTel();
                                });

                                isValidTel();

                                $("#fast_order_finish").keypress(function (e) {
                                    if (e.keyCode == 13) {
                                        //нажата клавиша enter - здесь ваш код
                                        createOrderByOneClick();
                                    } else isValidTel();
                                });


                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}