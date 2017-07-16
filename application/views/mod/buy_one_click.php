<!--BUY ONE CLICK-->
<div class="modal fade buy_one_click bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
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
                        $telCode = '+380';
                        if($country == 2) $telCode = '+7';
                        if(userdata('tel') !== false)
                            $telCode = userdata('tel');
                        ?>
                        <input type="hidden" name="action" value="order" />

                        <div style="width: 100%; text-align: center">

                            <input type="tel" id="phone" class="form-control" name="one_click_tel" value="<?=$telCode?>">
                            <span style="color: green; font-size: 23px; position: absolute;" id="valid-msg" class="hide">&#10004;</span>
                            <span title="Введённый Вами номер телефона указан не верно!" style="color: red; font-size: 23px; position: absolute;" id="error-msg" class="hide">&#10008;</span>
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
                            preferredCountries: ['ua','ru'],
                            separateDialCode: false,
                            geoIpLookup: function(callback) {
                                $.get('http://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                                    var countryCode = (resp && resp.country) ? resp.country : "";
                                    callback(countryCode);
                                });
                            },
                            utilsScript: "/js/utils.js?7"
                        }).done(function() {
                            // analytics
                            $('.selected-flag').one('click', function() {
                                ga('send', 'event', 'buy-one-click', 'clicked selected country');
                            });
                            $('#phone').one('keyup', function() {
                                ga('send', 'event', 'buy-one-click', 'typed something in input');
                            });
                        });

                        var reset = function() {
                            telInput.removeClass("error");
                            errorMsg.addClass("hide");
                            validMsg.addClass("hide");
                        };

                        // on blur: validate
                        telInput.blur(function() {
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


                            <?php
                            if(!isset($user) && userdata('login') != false)
                                $user = getUserIdBylogin(userdata('login'), true);
                            if($user) echo '$("#phone").intlTelInput("setNumber", "'.$user['tel'].'");';
                            ?>
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

                            $("#fast_order_finish").keypress(function(e){
                                if(e.keyCode==13){
                                    //нажата клавиша enter - здесь ваш код
                                    createOrderByOneClick();
                                } else isValidTel();
                            });

                            function createOrderByOneClick() {
                                if(validTel) {
                                    $("#fastorderresult").html('Приступаем к оформлению заказа...');
                                    var tel = $("#phone").val();
                                    userdata('set','tel',tel);

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
                                } else $("#fastorderresult").html('Что-то пошло не так... Проверьте введённые Вами данные!');
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