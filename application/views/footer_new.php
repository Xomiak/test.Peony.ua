<!-- Footer -->
<div id="ontop" class="icon-arrow-left"></div>
<div class="container-fluid footer">
    <?php if ($_SERVER['REQUEST_URI'] != '/my_cart/') { ?>
    <footer class="container">
        <div class="col-3 about">
            <div><span class="icon-info"></span>О компании</div>

            <p>На страницах каталогов представлен полный ассортимент производимой продукции – это платья (в молодежном и
                деловом стиле, для повседневной носки и вечерние), жакеты, куртки, блузы.
                Коллекции одежды разработаны с учетом современных модных тенденций для широкой покупательской аудитории,
                поэтому женская одежда, купить которую легко у нас заинтересует не только стройных и юных девушек, но и
                обладательниц пышных форм и женщин более зрелого возраста.</p>
        </div>
        <div class="col-3 social">
            <div><span class="icon-world"></span>Социальные сети</div>

            <?php
//            $country = userdata('country');
//            if(!$country)
//                $country = getTabGeo();
//
//            if($country){
//                if($country == 'UA') $country = 'Украина';
//                set_userdata('country', $country);
//            }
            //if(isDebug()) var_dump($country);
            $country = 'украина';
            if(mb_strtolower($country) != 'украина' && mb_strtolower($country) != 'ukraine' && mb_strtolower($country) != 'ua'){
            ?>
            <!-- VK Widget -->
            <script type="text/javascript" src="//userapi.com/js/api/openapi.js?22"></script>
            <div class="vk">
                <div id="vk_groups"></div>
                <script type="text/javascript">
                    VK.Widgets.Group("vk_groups", {
                        mode: 0,
                        width: "200",
                        height: "290",
                        color1: 'FFFFFF',
                        color2: '000000',
                        color3: '842841'
                    }, 58777985);
                </script>
            </div>
            <?php } else echo '<a target="_blank" href="https://vk.com/peony_shop" rel="nofollow"><img src="/img/vk-image.png" alt="Наша группа в ВК" /></a>';?>

            <!-- Facebook Widget -->
            <div class="fb">
                <iframe
                    src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FPeony.Shop.Ukraine&width=200&amp;colorscheme=light&amp;show_faces=true&amp;stream=false&amp;header=false&amp;"
                    scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:230px;"
                    allowTransparency="true"></iframe>
            </div>
            <!--img src = "/img/del/qqqq.jpg"-->


        <span class="social-links" style="display: none">
            <a class="social youtube" rel="publisher nofollow" href="https://plus.google.com/105087454439615440898/"
               itemprop="Peony Youtube">Youtube</a>
            <a class="social google" rel="publisher nofollow" href="https://plus.google.com/+PeonyUa"
               itemprop="Peony G+">google+</a>
            <a class="social vkontakte" rel="publisher nofollow" href="https://vk.com/peony_shop" itemprop="Peony VK">vk.com</a>
            <a class="social facebook" rel="publisher nofollow" href="https://www.facebook.com/Peony.Shop.Ukraine/"
               itemprop="Peony FB">facebook.com</a>
        </span>
        </div>

        <div class="col-3 contact" itemscope itemtype="http://schema.org/Organization">
            <meta itemprop="name" content="Peony">
            <meta itemprop="url" content="//<?= $_SERVER['SERVER_NAME'] ?>">
            <div><span class="icon-envelope"></span>контакты</div>
            <ul>
                <li>
                    <soan itemprop="telephone">+7 (499) 350-27-93</soan>
                </li>
                <li>
                    <soan itemprop="telephone">+38 (050) 316-85-76</soan>
                    <img alt="Мы в Вайбере: +38 (050) 316-85-76"
                         title="Мы в Вайбере: +38 (050) 316-85-76" src="/img/viber.png"
                         style="width: 18px; height: 18px;"/></li>
                <li>
                    <soan itemprop="telephone">+38 (097) 259-29-26</soan>
                    <img alt="Мы в Вайбере: +38 (097) 259-29-26"
                         title="Мы в Вайбере: +38 (097) 259-29-26" src="/img/viber.png"
                         style="width: 18px; height: 18px;"/>
                    <img alt="Мы в Hangouts: +38 (097) 259-29-26"
                         title="Мы в Hangouts: +38 (097) 259-29-26" src="/img/hangouts.png"
                         style="width: 18px; height: 18px;"/>

                </li>
                <li>
                    <soan itemprop="telephone">+38 (063) 513-64-82</soan>
                </li>
            </ul>
            <ul>
                <li><span itemprop="email">info@peony.ua</span></li>
                <li>peony.ua</li>
            </ul>
            <div itemprop="address" itemprop="address" itemscope itemtype="//schema.org/PostalAddress">
                <meta itemprop="name" content="Peony">
                <meta itemprop="streetAddress" content="Пушкинская, 57"/>
                <meta itemprop="addressLocality" content="Одесса"/>
            </div>
        </div>

        <?php } else echo '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'; ?>

    </footer>
</div>
<div class="container-fluid footer-bot" style="text-align: center;color: white">
    <div class="footer-copy">
        &#174; <strong><a style="color: white" href="https://peony.ua">PEONY</a></strong>&#8482; &copy; 1998 - <?= date("Y") ?>
    </div>
</div>
<!-- fotter - end -->


<!--MODAL DIALOG-->
<div class="modal fade bs-example-modal-sm" id="login-logout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm registration-modal">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <h2>Авторизация</h2>
            <p>Выберите любую, удобную для Вас, соц. сеть, либо почтовую службу:</p>
            <script src="//ulogin.ru/js/ulogin.js"></script>
            <div id="uLoginde88b987"
                 data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=google,yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=http://<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-sm" id="download" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm registration-modal">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <h2>Авторизируйтесь</h2>
            <p style="font-size: 12px;">Для того что бы скачать каталог.</p>
            <div id="uLoginf418c86f"
                 data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=google,yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=http://<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
        </div>
    </div>
</div>

<div class="modal fade modal-fast-order bs-example-modal-lg" id="fast-order" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <div id="fast-order-content">

            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-message-order bs-example-modal-message" id="modal-message" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-message">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <div id="modal-message-content">

            </div>
        </div>
    </div>
</div>


<!--END ALL MODALLL -->


<?php
if ($_SERVER['REQUEST_URI'] != '/my_cart/')
    include('application/views/jquery.php');
?>

<?php
if (userdata('msg') !== false && !strpos($_SERVER['REQUEST_URI'], 'export')) {
    getModalDialog('modal_login_ok', userdata('msg'));
    ?>
    <script type="text/javascript">
        j(document).ready(function () {
            jQuery('#modal_login_ok').modal('show');
        });
    </script>
    <?php
    unset_userdata('msg');

}
?>


<!--<div class="export-to-vk" style="position: fixed; left: 0;top:50%">
    <a href="/export/"><img style="width: 30px" src="/img/export-to-vk.png" alt="Экспорт товаров в ВК" title="Экспорт товаров в ВК" /></a>
</div>-->
<?php
// Попап АКЦИЯ
$is_action_now = getOption('is_action_now');
if ($is_action_now == 1) {
    if (userdata('action_popup') != 'showed' && $_SERVER['REQUEST_URI'] != '/') {
        ?>
        <div class="modal fade bs-example-modal-sm" id="getActionPopup" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog registration-modal">
                <div class="modal-content" style="text-align: center">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <?= getOption('action_popup'); ?><br/>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function getAuthorize() {
                jQuery('#getActionPopup').modal('show');
            }
            j(document).ready(function () {
                setTimeout(getAuthorize, 5000);
            });
        </script>
        <?php
        set_userdata('action_popup', 'showed');
    } elseif($_SERVER['REQUEST_URI'] == '/'){
        ?>
        <div class="modal fade bs-example-modal-sm" id="getActionDetailsPopup" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog registration-modal">
                <div class="modal-content" style="text-align: center">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <?= getOption('action_details_popup'); ?><br/>
                </div>
            </div>
        </div>

        <script>
            function showActionDetailsPopup(){
                jQuery('#getActionDetailsPopup').modal('show');
            }
        </script>
        <?php
    }
}

// Попап с предложением авторизироваться
if (getOption('auth_popup') == 1 && userdata('login') != true && userdata('getAuthorizeShowed') != true) {
    ?>
    <div class="modal fade bs-example-modal-sm" id="getAuthorize" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm registration-modal">
            <div class="modal-content" style="text-align: center">
                <button class="close" type="button" data-dismiss="modal">&times;</button>
                <?= getOption('get_your_email'); ?><br/>
                <div id="uLoginea1ca6be"
                     data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook,google,yandex;hidden=openid,twitter,livejournal,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=http://<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
                <!--form method="post" action="/">
                    <input type="email" required name="get_your_email" placeholder="Ваш e-mail"><br>
                    <input type="submit" value="Хочу!">
                </form-->
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function getAuthorize() {
            jQuery('#getAuthorize').modal('show');
        }
        j(document).ready(function () {
            setTimeout(getAuthorize, 20000);
            j.post("/ajax/set_cookies/", {name: "getAuthorizeShowed", value: "true"});
        });
    </script>
    <?php
    //  set_userdata('getAuthorizeShowed', true);
}

// Export to VK
    if (userdata('iframe') == true)
        echo '<a target="_blank" href = "/export/" class = "export-to-vk vk-export-to-vk">Экспорт товаров</a>';
    else
        echo '<a href = "/export/" class = "export-to-vk">Экспорт товаров</a>';
    ?>
    <!-- Sliza.ru - Widget -->
    <script type="text/javascript" src="https://sliza.ru/widget.php?id=2823&h=523dc6221e9060e1389624adf19b0b00&t=s" async defer></script>
    <!-- /// -->
<?php
if(!check_smartphone()) {
?>
    <script position="left-top" type="text/javascript" src="https://vzakupke.com/widget/assets/js/initSiteWidget.js" charset="UTF-8"></script>

    <?php
}
// ЖИВОСАЙТ - ВЫВОДИТЬ ИЛИ НЕТ
$jivosite = getOption('jivosite');
//if($_SERVER['REMOTE_ADDR'] == '178.251.110.58') $jivosite = false;
if ($jivosite == 1) {
    ?>
    <!-- BEGIN JIVOSITE CODE {literal} -->
    <script type='text/javascript'>
        (function () {
            var widget_id = 'wR6HdJ6F5m';
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.async = true;
            s.src = '//code.jivosite.com/script/widget/' + widget_id;
            var ss = document.getElementsByTagName('script')[0];
            ss.parentNode.insertBefore(s, ss);
        })();</script>
    <!-- {/literal} END JIVOSITE CODE -->
    <?php
}
?>

<?php require('application/views/ga.php'); ?>



<?php require('application/views/ecommerce.php'); ?>
<script async="async" src="//w.uptolike.com/widgets/v1/zp.js?pid=1294091" type="text/javascript"></script>



<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter26267973 = new Ya.Metrika({
                    id: 26267973,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true,
                    trackHash: true,
                    ecommerce: "dataLayer"
                });
            } catch (e) {
            }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () {
                n.parentNode.insertBefore(s, n);
            };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/26267973" style="position:absolute; left:-9999px;" alt="yandex.metrika"/>
    </div>
</noscript>
<!-- /Yandex.Metrika counter -->


<?php
$dynx_itemid = '';
$dynx_itemid2 = '';
$dynx_pagetype = 'other';
$dynx_totalvalue = 0;

$ecomm_prodid = '';
$ecomm_pagetype = 'other';
$ecomm_totalvalue = 0;


if (isset($article['color'])) {
    $ecomm_prodid = $article['id'];
    $ecomm_pagetype = 'product';
    $ecomm_totalvalue = $article['price'];

    $dynx_itemid = $article['id'];
    $dynx_itemid2 = $article['name'];
    $dynx_pagetype = "product";
    $dynx_totalvalue = $article['price'];
} elseif (isset($category['name'])) {
    $ecomm_prodid = $category['id'];
    $ecomm_pagetype = 'category';
    $ecomm_totalvalue = 0;

    $dynx_itemid = $category['id'];
    $dynx_itemid2 = $category['name'];
    $dynx_pagetype = "category";

}
if ($_SERVER['REQUEST_URI'] == '/') {
    $dynx_pagetype = 'home';
    $ecomm_pagetype = 'home';
} elseif ($_SERVER['REQUEST_URI'] == '/my_cart/') {
    $dynx_pagetype = 'cart';
    $ecomm_pagetype = 'cart';
    if (!isset($full_price)) $full_price = userdata('full_price');
    if (isset($full_price)) {
        $ecomm_totalvalue = $full_price;
        $dynx_totalvalue = $full_price;
    }
}
?>
<!-- /Yandex.Metrika counter -->

<!-- Код тега ремаркетинга Google -->
<!--------------------------------------------------
С помощью тега ремаркетинга запрещается собирать информацию, по которой можно идентифицировать личность пользователя. Также запрещается размещать тег на страницах с контентом деликатного характера. Подробнее об этих требованиях и о настройке тега читайте на странице http://google.com/ads/remarketingsetup.
--------------------------------------------------->
<script type="text/javascript">
    var google_tag_params = {
        ecomm_prodid: '<?=$ecomm_prodid?>',
        ecomm_pagetype: '<?=$ecomm_pagetype?>',
        ecomm_totalvalue: <?=$ecomm_totalvalue?>,

    };
</script>
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 943237681;
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="google.conversion"
             src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/943237681/?value=0&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
</body>
</html><?php

$html = ob_get_clean();
// сохраняем кеш
if($this->config->item('cache') && $_SERVER['REQUEST_URI'] != '/contacts/') {
    $cacheUrl = cacheUrl();
    if (isset($cachePostfix))
        $cacheUrl .= $cachePostfix;
    $this->partialcache->save($cacheUrl, $html);
}

if(isset($reviewsHtml))
    $html = str_replace('[reviews]',$reviewsHtml,$html);

echo $html;
//showDebugDetails();