<?php include("application/views/head_new.php"); ?>
<?php include("application/views/header_new.php"); ?>
<?php
// Попап Каникулы

$userCountry = userdata('userCountry');
$userCity = userdata('userCity');

$currency = userdata('currency');
if (!$currency) $currency = 'uah';

$currensy_grn = getCurrencyValue('UAH');
$currensy_rub = getCurrencyValue('RUB');

$npnp_price = getOption('npnp_price');

$holidays_enable = getOption('holidays_enable');
if ($holidays_enable == 1) {
    $holidays_start = explode('-', getOption('holidays_start'));
    $holidays_end = explode('-', getOption('holidays_end'));
    if (((is_array($holidays_start)) && (is_array($holidays_end))) && count($holidays_start) > 1 && count($holidays_end) > 1) {
        $unix_start = mktime(0, 0, 0, $holidays_start[1], $holidays_start[2], $holidays_start[0]);
        $unix_end = mktime(0, 0, 0, $holidays_end[1], $holidays_end[2], $holidays_end[0]);
        $unix_now = time();
        if ($unix_now > $unix_start && $unix_now < $unix_end) {

            ?>
            <div class="modal fade my-cart-holidays bs-example-modal-lg" tabindex="-1" role="dialog"
                 aria-labelledby="mySmalModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content razmernaya-setka">
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                        <?= getOption('holidays_popup') ?>

                    </div>
                </div>
            </div>
            <?php
        }
    }
}
vd(userdata('type_id'));
?>
<section class="container basket" id="wrap">
    <div id="pagetpl">

        <div id="result_div">
            <?php include("application/views/shop/my_cart_complete.tpl.php"); ?>
        </div>
        <?php

        //if(isDebug()){
        //	vd($user);
        //}

        if (isset($coupon['err'])) {
            echo '<div class="coupon-err">' . $coupon['err'] . '</div>';
        }
        ?>

        <!--script>
                        $(document).ready(function() {
                        // Назначение события
                            $("input").change(function() {
                            // Посылка запроса
                            $.post("/ajax/cart_save/",{
                            // Параметр передаваемый в скрипт
                            <?php
        for ($i = 0; $i < $count; $i++) {
            ?>
                                kolvo_<?= $i ?>: $("#kolvo_<?= $i ?>").val(),
                                <?php
        }
        ?>
                            },function(data) {
                            // Присвоение возвращённых данных (data), элементу с id=result
                            $("#result").html(data);
                            });

                            });

                        });
                    </script-->
        <div id="result"></div>

        <?php
        if ($count > 0) {
        ?>

        <!--<div class="login">
                        <h3>Авторизация</h3>
                        <?php
        if ($user) {
            ?>
                            Вы авторизированы, как <?= $user['name'] ?>.
                            <?php
        } else {
            ?>
                            <form action="/login/" method="post">
                                <input type="hidden" name="action" value="login" />
                                <input type="hidden" name="back" value="<?= $_SERVER['REQUEST_URI'] ?>" />
                                e-mail:<br />
                                <input type="text" name="login" /><br />
                                Пароль:<br />
                                <input type="password" name="pass" /><br />
                <a href="/register/forgot/" rel="nofollow">Забыли пароль?</a><br />
                                <input class="mycart_send" type="submit" value="Вход" />
                            </form>
                            <?php
        }
        ?>
                    </div>-->
        <section class="container basket cart-container">
            <a name="step_2"></a>
            <?php
            if (!userdata('login')) {
                ?>
                <div class="modal fade bs-example-modal-sm order-without-autorization" id="modal-authorize"
                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <button class="close" type="button" data-dismiss="modal">&times;</button>
                            <div id="soc-login" class="soc-login">
                                <h3>Представьтесь, пожалуйста, любым удобным для Вас способом:</h3>

                                <ul class="auth-methods">
                                    <li title="Вы сможете отслеживать состояние своих заказов, первыми узнавать о новинках и
                                    акциях!"><strong>1. Авторизироваться через соц.сеть</strong>
                                        <div id="uLogine4531d6a"
                                             data-ulogin="display=panel;fields=first_name,last_name,email,photo;optional=bdate,city,country,phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,google,yandex,facebook;hidden=googleplus,twitter,livejournal,linkedin,foursquare,youtube,openid,lastfm,liveid,soundcloud,steam,flickr,uid,webmoney,tumblr,dudu,vimeo,instagram,wargaming;redirect_uri=http://<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
                                        <div class="auth-description">Вы сможете отслеживать свои заказы в Личном
                                            кабинете
                                        </div>
                                    </li>
                                    <li title="Указать все свои данные вручную"><strong>2. Указать мои данные
                                            вручную</strong>
                                        <button id="no-register" onclick="no_register()" data-dismiss="modal">Оформить
                                            заказ без
                                            авторизации
                                        </button>
                                        <div class="auth-description">У Вас не будет доступа к личному кабинету</div>
                                    </li>

                                    <p style="font-weight: bold; color: red;">* Оформив заказ без авторизации Вы не
                                        сможете
                                        отслеживать состояние своих заказов, т.к. не будет доступа к личному
                                        кабинету!</p>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <?php

            }
            ?>
            <script type="text/javascript">
                function no_register() {

                    $("#cart-adress").show();
                    $("#one-click").hide();
                    //jQuery('#modal_no_register').modal('show');
                }


                function show_form() {
                    //$("#soc-login").hide();
                    $("#cart-adress").show();
                    $("#one-click").hide();

                }


            </script>

            <div id="cart-adress" class="cart-adress"<?php if (!userdata('login'))
                echo ' style="display:none"'; ?>>
                <form id="address-form" class="address-form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
                    <input type="hidden" name="action" value="order"/>
                    <?php
                    if (isDebug())
                        require($_SERVER['DOCUMENT_ROOT'] . '/application/views/shop/new_form.php');
                    else{
                        $sModel = getModel('shop');
                        $addrArr = $sModel->getAddrByLogin(userdata('login'));

                        if (isDebug()) {
                            if ((is_array($addrArr)) && count($addrArr) > 0) {
                                include($_SERVER['DOCUMENT_ROOT'] . '/application/views/shop/addr.php');
                                echo '<link rel="stylesheet" href="/js/validation/css/validationengine.jquery.css" type="text/css"/>

<script src="/js/validation/languages/jquery.validationengine-ru.js" type="text/javascript" charset="utf-8"></script>
<script src="/js/validation/jquery.validationengine.js" type="text/javascript" charset="utf-8"></script>

<script>
    $(document).ready(function(){
        $("#address-form").validationEngine();
    });
</script>';
                            }
                            else require($_SERVER['DOCUMENT_ROOT'] . '/application/views/shop/new_addr.php');
                        } else {
                            require($_SERVER['DOCUMENT_ROOT'] . '/application/views/shop/new_addr.php');
                        }
                        ?>
                        <input id="finish" type="submit" value="Отправить заказ"
                               onclick="yaCounter26267973.reachGoal('zakaz'); ga('send', 'event', 'zakaz', 'click');">
                    </form>
                    <?php
                    }
                }
                ?>
                <!--div><span class="rel2">Наложенный платёж</span><input  type="radio" name="payment" value="Наложенный платёж"></div-->

            </div>

            <div id="new_addr_div">

            </div>
        </section>

        <!--POP_UP_Conditions-->
        <div class="modal fade my-cart-code-help bs-example-modal-lg" tabindex="-1" role="dialog"
             aria-labelledby="mySmalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content razmernaya-setka">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <?= getOption('my_cart_code_help') ?>

                </div>
            </div>
        </div>

        <?php
        $message = '<p class="without-register">Сделав заказ без авторизации Вы не сможете отслеживать Ваши заказы! Вы точно хотите продолжить?</p>
<button class="yesno" type="button" onclick="show_form()" data-dismiss="modal">Да</button>
<button class="yesno" type="button" data-dismiss="modal" data-toggle="modal" data-target="#modal_authorize">Нет</button>';
        getModalDialog('modal_no_register', $message);

        //        $message = '<p class="without-register">Просто укажите совой номер телефона и мы свяжемся с Вами для уточнения деталей заказа!</p>
        //        <p><input type="text" id="one_click_tel" name="one_click_tel" value="+380" /></p>
        //<button class="yesno" type="button" onclick="one_click_send" data-dismiss="modal">Заказать!</button>';
        //        getModalDialog('modal_one_click', $message);
        ?>
        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
</section>

<!--POP_UP_Payment-->
<!--noindex-->

<?php
buy_one_click_in_my_cart(false, false);
?>

<!--INTERKASSA-->
<div class="modal fade interkassa bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content razmernaya-setka">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <?= getOption('interkassa_info') ?>
        </div>
    </div>
</div>
<div class="modal fade international bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content razmernaya-setka">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <?= getOption('international_info') ?>
        </div>
    </div>
</div>
<div class="modal fade nalogenniy bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content razmernaya-setka">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <?= shortCodes(getOption('nalogenniy_info')) ?>
        </div>
    </div>
</div>
<!--/noindex-->
<!--END Payment modal-->
<div id="holidays" data-target=".my-cart-holidays" data-toggle="modal"></div>
<script>
    $(document).ready(function () {
        $("#holidays").trigger("click");
    });
</script>

<?php include("application/views/footer_new.php"); ?>

