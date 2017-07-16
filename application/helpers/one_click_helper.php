<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: XomiaK
 * Date: 26.04.2017
 * Time: 14:21
 */


function buy_one_click_in_my_cart($withButton = true, $product_id = false){
    $CI = &get_instance();
    $user = false;
    if(userdata('login') !== false)
        $user = getUserIdBylogin(userdata('login'),true);
    if($withButton){
        //data-toggle="modal" data-target=".buy_one_click"
        ?>
        <form id="fast-order">
        <div class="one-click-div">
            <button id="button_goto_one_click" onclick="getMyCartData()">Купить  в  1  клик</button>

        </div>
        </form>
        <?php
    }
    ?>

    <!--BUY ONE CLICK-->
    <div class="modal fade buy_one_click bs-example-modal-md" id="modal-buy-one-click" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content razmernaya-setka">
                <button class="close" type="button" data-dismiss="modal">&times;</button>
                <div id="one-click" class="cart-adress" style="width: 100%; text-align: center">
                    <div id="one-click-form" class="address-form" >
                        <input type="hidden" name="action" value="one_click_order"/>

                        <h2>Купить в 1 клик</h2>

                        <div id="one_click_cart_details"></div>

                        <h3>Укажите Ваш контактный номер телефона:</h3>
                        <link rel="stylesheet" href="/css/intlTelInput.css?37">

                        <div id="one-click-content" class="form-group">
                            <?php
                            $telCode = '';
                            if(isset($user['tel'])) {
                                $telCode = $user['tel'];
                                $telCode = trim(str_replace(' ','', $telCode));
                                //$telCode = str_replace('+','', $telCode);
                            }
                            else {
                                $CI->load->helper('geoip_helper');
                                $country = getUserCountry();
                                //if(!$country)
                                $telCode = '+380';
                                if ($country == 2 || $country == 'Россия') $telCode = '+7';
                                if (userdata('tel') !== false)
                                    $telCode = userdata('tel');
                            }
                            $country = '';
                            if(isset($user['country'])) $country = $user['country'];
                            else{
                                $userCountry = getUserCountry();
                                if($userCountry){
                                    $country = getCountryByName($userCountry);
                                    if(isset($country['name']))
                                        $country = $country['name'];
                                }
                            }
                            ?>
                            <input type="hidden" name="action" value="order" />
                            <input type="hidden" name="country" value="<?=$country?>" id="hiddenCountry" />

                            <div style="width: 100%; text-align: center">

                                <input type="tel" id="phone" class="form-control" name="one_click_tel" value="<?=$telCode?>">
                                <span style="color: green; font-size: 23px; position: absolute;" id="valid-msg" class="hide-tel" type="tel">&#10004;</span>
                                <span title="Введённый Вами номер телефона указан не верно!" type="tel" style="color: red; font-size: 23px; position: absolute;" id="error-msg" class="hide-tel">&#10008;</span>
                                <span id="isValidTel"></span>

                                <h3>Укажите Ваш e-mail:</h3>
                                <input style="width: 205px; display: inline" type="email" id="one_click_email" class="form-control" name="one_click_email" value="<?php if(isset($user['email'])) echo $user['email']; ?>">
                                <span style="color: green; font-size: 23px; position: absolute;" type="email" id="valid-email-msg" class="hide-email">&#10004;</span>
                                <span title="Введённый Вами e-mail указан не верно!" type="email" style="color: red; font-size: 23px; position: absolute;" id="error-email-msg" class="hide-email">&#10008;</span>
                                <span id="isValidEmail"></span>

                                <br /><p></p>
                                <div class="fast-order-submit-div" style="padding-top: 10px">
                                    <input id="fast_order_finish" type="button" value="Сделать заказ"
                                           onclick="yaCounter26267973.reachGoal('zakaz'); ga('send', 'event', 'zakaz_one_click_korzina', 'click');">
                                </div>

                                <div style="clear: both;width: 100%" id="fastorderresult"></div>

                            </div>
                        </div>


                        <!--                    ОБРАБОТЧИК НОМЕРА ТЕЛЕФОНА В БЫСТРОМ ЗАКАЗЕ-->
                        <script src="/js/jquery-1.11.1.min.js"></script>
                        <script src="/js/intlTelInput.min.js"></script>
                        <?php if($_SERVER['REQUEST_URI'] == '/my_cart/'){ ?>
                            <script src="/js/one_click_my_cart.js"></script>
                        <?php } else { ?>
                            <script src="/js/one_click.js"></script>
                        <?php } ?>



                        <script>
                            <?php
                            $user = false;
                            if(!isset($user) && userdata('login') != false)
                                $user = getUserIdBylogin(userdata('login'), true);
                            if($user) echo '$("#phone").intlTelInput("setNumber", "'.$user['tel'].'");';
                            ?>

                            $("#fast-order").submit(function () {
                                return false;
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function get_buy_one_click_form($withButton = true, $product_id = false){
    ob_start();
    $CI = &get_instance();
    $user = false;
    if(userdata('login') !== false)
        $user = getUserIdBylogin(userdata('login'),true);
    if($withButton){
        //data-toggle="modal" data-target=".buy_one_click"
        ?>
        <form id="fast-order">
            <div class="one-click-div">
                <button id="button_goto_one_click" onclick="getMyCartData()">Купить  в  1  клик</button>

            </div>
        </form>
        <?php
    }
    ?>

    <!--BUY ONE CLICK-->
    <div class="modal fade buy_one_click bs-example-modal-md" id="modal-buy-one-click" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content razmernaya-setka">
                <button class="close" type="button" data-dismiss="modal">&times;</button>


                        <?=get_one_click_form_content()?>

                    </div>
                </div>
            </div>

    <?php
    $html = ob_get_contents();
    ob_clean();
    return $html;
}

function get_one_click_form_content(){
    $CI = &get_instance();
    ob_start();
    ?>
    <div id="one-click" class="cart-adress" style="width: 100%; text-align: center">
        <div id="one-click-form" class="address-form" >
            <input type="hidden" name="action" value="one_click_order"/>

            <h2>Купить в 1 клик</h2>

            <div id="one_click_cart_details"></div>

            <h3>Укажите Ваш контактный номер телефона:</h3>
            <link rel="stylesheet" href="/css/intlTelInput.css?37">

            <div id="one-click-content" class="form-group">
                <?php
                $telCode = '';
                if(isset($user['tel'])) {
                    $telCode = $user['tel'];
                    $telCode = trim(str_replace(' ','', $telCode));
                    //$telCode = str_replace('+','', $telCode);
                }
                else {
//                    $CI = $this;
                    $CI->load->helper('geoip_helper');
                    $country = getUserCountry();
                    //if(!$country)
                    $telCode = '+380';
                    if ($country == 2 || $country == 'Россия') $telCode = '+7';
                    if (userdata('tel') !== false)
                        $telCode = userdata('tel');
                }
                $country = '';
                if(isset($user['country'])) $country = $user['country'];
                else{
                    $userCountry = getUserCountry();
                    if($userCountry){
                        $country = getCountryByName($userCountry);
                        if(isset($country['name']))
                            $country = $country['name'];
                    }
                }
                ?>
                <input type="hidden" name="action" value="order" />
                <input type="hidden" name="country" value="<?=$country?>" id="hiddenCountry" />

                <div style="width: 100%; text-align: center">

                    <input type="tel" id="phone" class="form-control" name="one_click_tel" value="<?=$telCode?>">
                    <span style="color: green; font-size: 23px; position: absolute;" id="valid-msg" class="hide-tel" type="tel">&#10004;</span>
                    <span title="Введённый Вами номер телефона указан не верно!" type="tel" style="color: red; font-size: 23px; position: absolute;" id="error-msg" class="hide-tel">&#10008;</span>
                    <span id="isValidTel"></span><span id="telDebug"></span>

                    <h3>Укажите Ваш e-mail:</h3>
                    <input style="width: 205px; display: inline" type="email" id="one_click_email" class="form-control" name="one_click_email" value="<?php if(isset($user['email'])) echo $user['email']; elseif(userdata('email') !== false) echo userdata('email');?>">
                    <span style="color: green; font-size: 23px; position: absolute;" type="email" id="valid-email-msg" class="hide-email">&#10004;</span>
                    <span title="Введённый Вами e-mail указан не верно!" type="email" style="color: red; font-size: 23px; position: absolute;" id="error-email-msg" class="hide-email">&#10008;</span>
                    <span id="isValidEmail"></span>

                    <br /><p></p>
                    <div class="fast-order-submit-div" style="padding-top: 10px">
                        <input id="fast_order_finish" type="button" value="Сделать заказ"
                               onclick="yaCounter26267973.reachGoal('zakaz'); ga('send', 'event', 'zakaz_one_click_korzina', 'click');">
                    </div>

                    <div style="clear: both;width: 100%" id="fastorderresult"></div>

                </div>
            </div>


            <!--                    ОБРАБОТЧИК НОМЕРА ТЕЛЕФОНА В БЫСТРОМ ЗАКАЗЕ-->
            <script src="/js/jquery-1.11.1.min.js"></script>
            <script src="/libs/intl-tel-input/js/intlTelInput.min.js"></script>

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

                var emailInput = $("#one_click_email"),
                    errorEmailMsg = $("#error-email-msg"),
                    validEmailMsg = $("#valid-email-msg");
                var validEmail = false;


                // инифиализация инпута для ввода номера телефона
                $("#phone").intlTelInput({
                    initialCountry: "auto",
                    autoHideDialCode: false,
                    preferredCountries: ['ua','ru'],
                    formatOnDisplay: false,
                    separateDialCode: false,
                    geoIpLookup: function(callback) {
                        $.get('http://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                            var countryCode = (resp && resp.country) ? resp.country : "";
                            callback(countryCode);
                        });
                    },
                    utilsScript: "/libs/intl-tel-input/js/utils.js"
                }).done(function() {
                    // analytics
                    $('.selected-flag').one('click', function() {
                        ga('send', 'event', 'buy-one-click', 'clicked selected country');
                    });
                    $('#phone').one('keyup', function() {
                        ga('send', 'event', 'buy-one-click', 'typed something in one click tel input');
                    });
                });

                var resetTel = function() {
                    telInput.removeClass("error-tel");
                    errorMsg.addClass("hide-tel");
                    validMsg.addClass("hide-tel");
                };
                var resetEmail = function() {
                    emailInput.removeClass("error-email");
                    errorEmailMsg.addClass("hide-email");
                    validEmailMsg.addClass("hide-email");
                };

                // on blur: validate
                telInput.blur(function() {
                    isValidTel();
                    console.log('blur');
                });

                // emailInput.blur(function() {
                //     isValidEmail();
                //     console.log('blur');
                // });

                // on keyup / change flag: reset
                telInput.on("keyup change", isValidTel);
                emailInput.on("keyup change", isValidEmail);


                // Функция получения и проверки данных при нажатии на кнопку "Купить в 1 клик"
                function onLoadOneClick() {
                    getMyCartData();
                    isValidTel();
                    isValidEmail();
                }

                function isValidTel() {

                    resetTel();
                    var isValid = $("#phone").intlTelInput("isValidNumber");
                    var phoneLength = $("#phone").val().length;
                    //$("#telDebug").html(phoneLength);

                    if(! isValid && phoneLength >= 13)
                        isValid = true;
                    if (isValid) {

                        validMsg.removeClass("hide-tel");
                        validTel = true;
                        //$("#isValidTel").html("Yes");
                    } else {
                        telInput.addClass("error-tel");
                        errorMsg.removeClass("hide-tel");
                        validTel = false;
                        //$("#isValidTel").html("No");
                    }
                    console.log('tel valid: '+ validTel);

                }

                function isValidEmail() {
                    resetEmail();
                    var emailAddress = emailInput.val();
                    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
                    var result = pattern.test(emailAddress);
                    console.log(result);
                    if(result){
                        validEmailMsg.removeClass("hide-email");
                        validEmail = true;
                        //$("#isValidEmail").html("Yes");
                    } else {
                        emailInput.addClass("error-email");
                        errorEmailMsg.removeClass("hide-email");
                        validEmail = false;
                        //$("#isValidEmail").html("No");
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
                        //alert(article_id);
                        if(position != 'my_cart' && product_id != undefined){
                            add_product_to_cart(product_id);
                        }
                    });


                    function add_product_to_cart(product_id) {
                        alert('adding one click');
                        var kolvo = 1;
                        var razmer = 0;
                        if($("#razmer").length){
                            alert('razmer');
                        }
                        if($("#razmer1").length){
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


                    // Фиксируем изменение страны в выпадающем инпуте ввода номера телефона
                    var countryData = $.fn.intlTelInput.getCountryData();
                    $("#phone").on("countrychange", function(e, countryData) {
                        var countryIso2 = countryData.iso2;
                        var country = countryIso2;
                        if(countryIso2 == 'ru') country = 'Россия';
                        else if(countryIso2 == 'ua') country = 'Украина';
                        userdata('set','country',country);
                        isValidTel();
                    });

                    isValidTel();
                    isValidEmail();

                    $("#fast_order_finish").keypress(function(e){
                        if(e.keyCode==13){
                            //нажата клавиша enter - здесь ваш код
                            createOrderByOneClick();
                        } else {
                            isValidTel();
                            isValidEmail();
                        }
                    });

                    function createOrderByOneClick() {
                        if(validTel && validEmail) {
                            $("#fastorderresult").html('Приступаем к оформлению заказа...');
                            var tel = $("#phone").val();
                            var email = $("#one_click_email").val();
                            var country = $("#hiddenCountry").val();
                            userdata('set','tel',tel);
                            userdata('set','email',email);

                            //alert('Всё ок: '+tel+" email: "+email);

                            // Отправляем aJax запрос на оформление заказа в 1 клик
                            $.ajax({
                                url: '/ajax/create_one_click_order/',
                                method: 'post',
                                async: false,
                                data: {
                                    'tel': tel,
                                    'email': email,
                                    'country': country
                                },

                            }).done(function (data) {
                                $("#fastorderresult").html(data);
                            });
                        } else {
                            $("#fastorderresult").hide();
                            $("#fastorderresult").html('<span style="color:red">Заполните корректно необходиме поля!</span>');
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
                        createOrderByOneClick();
                    });

                    isValidTel();
                    isValidEmail();
                });/**
                 * Created by XomiaK on 02.05.2017.
                 */

            </script>
            <!--                <script src="/js/one_click_my_cart.js"></script>-->



            <script>
                <?php
                $user = false;
                if(!isset($user) && userdata('login') != false)
                    $user = getUserIdBylogin(userdata('login'), true);
                if($user) echo '$("#phone").intlTelInput("setNumber", "'.$user['tel'].'");';
                ?>

                $("#fast-order").submit(function () {
                    return false;
                });
            </script>

        </div>
    </div>
<?php
    $html = ob_get_contents();
    ob_clean();
    return $html;
}