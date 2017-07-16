<?php //include("application/views/head_new.php"); ?>
<?php
$my_cart = $this->session->userdata('my_cart');
$cart_count = 0;
if($my_cart) $cart_count = shop_count();
$currency = userdata('currency');
if(!$currency) $currency = 'uah';
?>
<body>
<?php
if(isClientAdmin()) include("application/views/admin/client/frontend_admin_panel.php");
?>


<div class = "container-fluid header-top">
    <div class = "container">
    <span>Украинский производитель и оптовый поставщик женской одежды</span>
        <?php                
         if($this->session->userdata('login') !== false)
         {
            ?>
            <a href="/price_download/" rel="nofollow" class="download">Скачать каталог</a>

			 <a href="/login/logout/" class="login-logout">Выход</a></span>
            <a href="/user/mypage/" class="login-logout">Мой Кабинет</a></span>

            <?php
         }
         else
         {
             ?>
            <a href = "#" class = "download" data-toggle = "modal" data-target = "#download">Скачать каталог</a>
            <a href = "#" class = "login-logout" data-toggle = "modal" data-target = "#login-logout">Войти</a>
            <?php
         }
         ?>          
    </div>
</div>
<div class="container-fluid sticky">
<header class = "container header" itemprop="author" itemscope itemtype="http://schema.org/LocalBusiness" itemref="">
    <meta itemprop="name" content="Peony">
    <meta itemprop="description" content="Украинский производитель и оптовый поставщик женской одежды">
    <div itemprop="address"  itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <meta itemprop="name" content="Peony">
        <meta itemprop="streetAddress" content="Пушкинская, 55" />
        <meta itemprop="addressLocality" content="Одесса"/>
        <meta itemprop="email" content="info@peony.ua"/>


        <meta itemprop="telephone" content="+7 (499) 350-27-93"/>
        <meta itemprop="telephone" content="+38 (050) 316-85-76"/>
        <meta itemprop="telephone" content="+38 (097) 259-29-26"/>
        <meta itemprop="telephone" content="+38 (063) 513-64-82"/>

    </div>
    <div class = "header-cart">
        <p><a rel="nofollow" href = "/my_cart/"><span>Корзина (<span id="my_cart_count"><?=$cart_count?></span>)</span><span class = "icon-cart"></span></a></p>
        <form action="/search/" method="post" id="autosearch">
            <input id="birds" required type = "search" name="search" placeholder = "Поиск" role="textbox" aria-autocomplete="list" aria-haspopup="true" class="ui-autocomplete-input">
<div class="header-currency-cont">
    <?php
    $currencySymb['usd'] = '&#36;';
    $currencySymb['uah'] = '&#8372;';
    $currencySymb['rub'] = '&#x584;';
    ?>
    <p id="current-currency"><span><?=$currencySymb[$currency]?></span><?=mb_strtoupper($currency)?></p>
    <ul id="currency-change-ul">
        <?php
        if($_SERVER['REQUEST_URI'] == '/my_cart/'){
            ?>
<!--            <li><a class="currency-change" currency="usd"><span>&#36;</span>USD</a></li>-->
            <li><a class="currency-change" currency="uah"><span>&#8372;</span>UAH</a></li>
            <li><a class="currency-change" currency="rub"><span>&#x584;</span>RUB</a></li>
            <?php
        } else{ ?>
<!--            <li><a id="currency_change_usd" class="currency-change" currency="usd"><span>&#36;</span>USD</a></li>-->
            <li><a id="currency_change_uah" class="currency-change" currency="uah"><span>&#8372;</span>UAH</a></li>
            <li><a id="currency_change_rub" class="currency-change" currency="rub"><span>&#x584;</span>RUB</a></li>
        <?php } ?>
    </ul>


        
</div>
            <button type = "submit" class = "icon-search"></button>
        </form>

    </div>
    <div class = "header-menu">
        <a itemprop="url" href = "/" class = "logo"><img itemprop="image" src = "/img/logo.png" alt="PEONY - украинский производитель женской одежды"></a><span style="position: absolute;top: 5px;">™</span>

        <?php
        if($_SERVER['REQUEST_URI'] != '/my_cart/') {
            $is_action_now = getOption('is_action_now');
            if ($is_action_now) {
                $startOrEnd = 'начала';
                $showAction = true;
                $timer_ended = getOption('timer_start_date');

                $timer_end_date_arr = explode('-', $timer_ended);
                if (is_array($timer_end_date_arr)) {
                    $day = $timer_end_date_arr[2];
                    $month = $timer_end_date_arr[1];
                    $year = $timer_end_date_arr[0];
                    $timer_end_date_unix = mktime(0, 0, 0, $month, $day, $year);

                    if ($timer_end_date_unix < time()) {
                        $timer_ended = getOption('timer_end_date');
                        $timer_end_date_arr = explode('-', $timer_ended);
                        if (is_array($timer_end_date_arr)) {
                            $day = $timer_end_date_arr[2];
                            $month = $timer_end_date_arr[1];
                            $year = $timer_end_date_arr[0];
                            $timer_end_date_unix = mktime(0, 0, 0, $month, $day, $year);
                            if ($timer_end_date_unix > time()) {
                                $startOrEnd = '  &nbsp;&nbsp;&nbsp;конца';
                            } else $showAction = false;
                        }
                    }
                    $show_timer = true;
                }
                if ($showAction) {
                    ?>
                    <!--START counter-->
                    <div class="counter">
                        <p>До <?= $startOrEnd ?> <a title="Узнать подробнее о текущей акции" style="color:#842841" href="<?=getOption('action_new_url')?>"><span>акции</span></a></p>
                        <div id="countdown">

                        </div>
                        <span class="times">Дней, часов и минут</span>
                    </div>
                    <!--END counter-->
                    <?php
                }
            }
        }
        ?>
		
        <nav class = "animenu">
            <button class = "animenu__toggle">
                <div class = "mobile-but">
                    <span class = "animenu__toggle__bar"></span>
                    <span class = "animenu__toggle__bar"></span>
                    <span class = "animenu__toggle__bar"></span>
                </div>
                
                <span class = "name-menu">МЕНЮ</span>

            </button>
            <?php showTopMenu(); ?>
        </nav>
    </div>

</header>
</div>


<!-- End header -->
<!-- -->

										