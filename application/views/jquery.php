<?php
if ($_SERVER['REQUEST_URI'] != '/') {
    ?>
    <script src="/js/jquery.min.js"></script>
    <?php
}
?>


<script src="/js/slick.min.js"></script>
<script src="/js/comon.min.js"></script>
<script src="/js/animenu.js"></script>
<!--<script src="/js/jquery.easing.js"></script>-->
<!--<script src="/js/layerslider.kreaturamedia.jquery.js"></script>-->
<!--<script src="/js/layerslider.transitions.js"></script>-->
<script src="/js/jquery-transit-modified.js"></script>
<script src="/js/zoomsl-3.0.min.js"></script>
<script src="/js/jquery.formstyler/jquery.formstyler.min.js"></script>

<!--Autocomplete-->
<script src="/js/autocomplete/jquery.ui.core.js"></script>
<script src="/js/autocomplete/jquery.ui.widget.js"></script>
<script src="/js/autocomplete/jquery.ui.position.js"></script>
<script src="/js/autocomplete/jquery.ui.autocomplete.js"></script>

<script>
    var j = jQuery.noConflict();
    j("#birds").autocomplete({
        source: "/ajax/autocomplete/",
        minLength: 1
    });
</script>

<script>
    var j = jQuery.noConflict();
    j(document).ready(function () {
        j('#category_id').styler();
        console.log(1);
    })


j('.close').click(function () {
        //alert('closing');
        hideBackdrops();
    });

function hideBackdrops() {
    setTimeout(function () {
        j('.modal-backdrop').hide();
    }, 2000);
}

</script>

<?php if(isset($article)) { ?>
<script>
    j(document).ready(function () {
        // В 1 КЛИК
        j("#fast-order").submit(function () {
            var razmer = j("#razmer1").val();
            var kolvo = j("#kolvo").val();
            if (isSizeChecked() != false) {
                addProductToCart(<?=$article['id']?>,razmer,kolvo);

                //j('#modal-buy-one-click');
                loadModal('modal-buy-one-click');
                getMyCartData();
            }
        });

        // В КОРЗИНУ
        j("#myform").submit(function () {
            var razmer = j("#razmer1").val();
            var kolvo = j("#kolvo").val();
            if (isSizeChecked() != false) {
                addProductToCart(<?=$article['id']?>,razmer,kolvo);
                loadModal('modal-message');
            }
        });

        j('.header-currency-cont p').on("click", function () {
            var th = j(this);
            th.next('ul').fadeToggle(300);
        });

        function isSizeChecked() {
            var razmer = j("#razmer1").val();
            //alert(razmer);
            if (razmer == '') {
                j("#razmererror").show();
                return false;
            }
            return true;
        }

        function addProductToCart(product_id, size, count) {
            $.ajax({
                /* адрес файла-обработчика запроса */
                url: '/ajax/to_cart/',
                /* метод отправки данных */
                method: 'POST',
                /* данные, которые мы передаем в файл-обработчик */
                data: {
                    "shop_id": product_id,
                    "size": size,
                    "count": count
                },

            }).done(function (data) {
                //$("#my_cart_count").html(data);
                console.log('Всего: '+data);
//                var addedMessage1 = "1 единица товара <strong>[shop_name] [razmer] размера </string> добавлена в корзину";
//                var addedMessage2 = "[count] единицы товара <strong>[shop_name] [razmer] размера </string> добавлены в корзину";
//                var addedMessage3 = "[count] единиц товара <strong>[shop_name] [razmer] размера </string> добавлено в корзину";
//                var addedMessage = addedMessage3;
//                if(count == 1) addedMessage = addedMessage1;
//                else if(count < 1 && count < 5) addedMessage = addedMessage2;
//                addedMessage = addedMessage.replace('[count]', count);
//                addedMessage = addedMessage.replace('[shop_name]', "<?//=$article['name']?>// (<?//=$article['color']?>//)");
//                addedMessage = addedMessage.replace('[razmer]', size);


                addedMessage = '<?=getOption('popup_added_product_to_cart')?>';
                $('#modal-message-content').html(addedMessage);

                $("#my_cart_count").html(data);

            });

        }

    });
</script>
<?php } ?>

<?php
$is_action_now = getOption('is_action_now');
if ($is_action_now) {
    $show_timer = false;
    //$timer_end_date = getOption('timer_end_date');

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
            }
        }
        $show_timer = true;
    }

    if ($show_timer) {
        if ($timer_end_date_unix > time()) {
            ?>
            <script src="/js/jquery.countdown.js"></script>
            <script type="text/javascript">
                jQuery(function () {
                    var note = jQuery('#note'),
                        ts = new Date(<?=$year?>,<?=($month - 1)?>, <?=$day?>),
                        newYear = false;

                    if ((new Date()) > ts) {
                        // The new year is here! Count towards something else.
                        // Notice the *1000 at the end - time must be in milliseconds
                        ts = (new Date()).getTime() + 10 * 24 * 60 * 60;
                        newYear = false;
                    }

                    jQuery('#countdown').countdown({
                        timestamp: ts,
                        callback: function (days, hours, minutes) {
                            var message = "";

                            message += days + " day" + ( days == 1 ? '' : 's' ) + ", ";
                            message += hours + " hour" + ( hours == 1 ? '' : 's' ) + ", ";
                            message += minutes + " minute" + ( minutes == 1 ? '' : 's' );
                        }
                    });
                });
            </script>
            <?php
        }
    }
}
?>
<!--END counter-->

<?php
if (isset($article)) {
    ?>
    <!--Multizoom script-->
    <script type="text/javascript" src="/js/multizoom.js"></script>
    <script type="text/javascript">

        jQuery(document).ready(function () {

            /***************************************************************
             ************NEWWWWWW SLIDER AND ZOOOOOOOOOOM*******************
             ***************************************************************/

            if (jQuery(window).width() > '768') {
                /*jQuery("#bx-pager li img.small-img").removeClass('small-img');
                jQuery("#full").removeClass('zoom-desktop');*/
                initTabZoom();
                jQuery('.zoom-desktop').imagezoomsl({
                    cursorshadecolor: '#842841',
                    cursorshadeborder: '1px solid #842841',
                    magnifiereffectanimate: 'fadeIn',
                    magnifierborder: '3px solid #842841',
                    zoomrange: [2, 3]

                });
            } else {
               initTabZoom();
                jQuery("#full").removeClass('zoom-desktop');
            }

function initTabZoom() {
    jQuery("#bx-pager li img").addClass('small-img');
    jQuery("#bx-pager li .small-img").click(function () {
        jQuery("#bx-pager li .small-img").css("opacity", "1");
        jQuery(this).css("opacity", "0.7");
        htmlStr = jQuery(this).attr("src");
        jQuery("#full").show().attr("src", htmlStr).addClass('zoom-desktop');

    });
}
            /*
             //jQuery("#bx-pager li").first().children().css("opacity", "0.7");
             jQuery("#bx-pager li .small-img").click(function () {
             jQuery("#bx-pager li .small-img").css("opacity", "1");
             jQuery(this).css("opacity", "0.7");
             htmlStr = jQuery(this).attr("src");
             jQuery("#full").show().attr("src", htmlStr).addClass("zoom-desktop");

             });






             jQuery('.zoom-desktop').imagezoomsl({
             cursorshadecolor: '#842841',
             cursorshadeborder: '1px solid #842841',
             magnifiereffectanimate: 'fadeIn',
             magnifierborder:'3px solid #842841',
             zoomrange: [2, 3]

             });*/
        });
        /***************************************************************
         ************END*******************
         ***************************************************************/


    </script>
    <!--END multizoom-->
    <?php
}
?>
<?php
//if ($_SERVER['REQUEST_URI'] == '/') {
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
//        jQuery('#layerslider').layerSlider({
//            thumbnailNavigation: 'hover',
//            hoverPrevNext: true,
//            keybNav: false,
//            navPrevNext: true,
//            navStartStop: false,
//            navButtons: false,
//            thumbnailNavigation: 'disabled',
//            pauseOnHover: false,
//            sliding: 'left',
//            showCircleTimer: false,
//            skin: 'creative',
//            //skinsPath: 'http://bumer.tonytemplates.com/buyshop/demo/skin/frontend/buyshop/default/css/skins/',
//            cbInit: function (element) {
//                jQuery('.ls-nav-prev').append('<i class="icon-arrow-right"></i>');
//                jQuery('.ls-nav-next').append('<i class="icon-arrow-left"></i>')
//            }
//        });
        /**Tabs___*/
        jQuery('ul.tabs>.tab-link').click(function () {
            var tab_id = jQuery(this).attr('data-tab');

            jQuery('ul.tabs>.tab-link').removeClass('current');
            jQuery('.tab-content').removeClass('current');

            jQuery(this).addClass('current');
            jQuery("#" + tab_id).addClass('current');
        });
        /******************END Tabs*/


        /**Front images effect___*/
        jQuery(function () {
            jQuery('.product-itm a').hover(function () {
                jQuery(this).children('.front').stop().fadeOut(400);
            }, function () {
                jQuery(this).children('.front').stop().fadeIn(200);
            });
        });
        /******************END front-images*/


    });
</script>
<?php
//}
?>

<?php
if (isset($autoload) && $autoload == true) {
    ?>
    <!-- СКРИПТ АВТОПОДГРУЗКИ ТОВАРОВ ПРИ СКРОЛЛИНГЕ -->
    <script type="text/javascript">

        jQuery(document).ready(function () {
            jQuery('.fast-order-a').click(
                function () {
                    //alert('start load');
                    jQuery("#fast-order-content").html("");
                    var shop_id = jQuery(this).attr("shop_id");

                    jQuery.ajax({
                        // адрес файла-обработчика запроса
                        url: '/ajax/get_fast_order/',
                        // метод отправки данных
                        method: 'POST',
                        // данные, которые мы передаем в файл-обработчик
                        data: {
                            "shop_id": shop_id,
                            "request_uri": "<?=$_SERVER['REQUEST_URI']?>"
                        }

                    }).done(function (data) {

                        jQuery("#fast-order-content").html(data);
                    });
                }
            );
            <?php
            $pager = getOption('pagination_shop');
            if ($category['type'] == 'articles')
                $pager = getOption('pagination_news');
            ?>
            var pagination_shop = <?=$pager?>;
            /* Переменная-флаг для отслеживания того, происходит ли в данный момент ajax-запрос. В самом начале даем ей значение false, т.е. запрос не в процессе выполнения */
            var inProgress = false;
            /* С какой статьи надо делать выборку из базы при ajax-запросе */
            var startFrom = pagination_shop;

            var getajax = false;
            if (startFrom == pagination_shop) getajax = true;

            var mob = <?php if (check_smartphone()) echo "true"; else echo "false";?>;

            var bottom = 800;
            if (mob == true) bottom = 1500;

            /* Используйте вариант $('#more').click(function() для того, чтобы дать пользователю возможность управлять процессом, кликая по кнопке "Дальше" под блоком статей (см. файл index.php) */
            jQuery(window).scroll(function () {
                //$('#more').click(function(){
                /* Если высота окна + высота прокрутки больше или равны высоте всего документа и ajax-запрос в настоящий момент не выполняется, то запускаем ajax-запрос */
                if (jQuery(window).scrollTop() + jQuery(window).height() >= jQuery(document).height() - bottom && !inProgress) {

                    jQuery.ajax({
                        /* адрес файла-обработчика запроса */
                        url: '/ajax/getnextrows/',
                        /* метод отправки данных */
                        method: 'POST',
                        /* данные, которые мы передаем в файл-обработчик */
                        data: {
                            "startFrom": startFrom,
                            "category_id": <?=$category['id']?>,
                            "getajax": getajax
                        },
                        beforeSend: function () {
                            /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
                            inProgress = true;
                        }
                        /* что нужно сделать до отправки запрса */

                        /* что нужно сделать по факту выполнения запроса */
                    }).done(function (data) {
                        /* Преобразуем результат, пришедший от обработчика - преобразуем json-строку обратно в массив */
                        data = jQuery.parseJSON(data);

                        /* Если массив не пуст (т.е. статьи там есть) */
                        if (data.length > 0) {

                            /* Делаем проход по каждому результату, оказвашемуся в массиве,
                             где в index попадает индекс текущего элемента массива, а в data - сама статья */
                            jQuery.each(data, function (index, data) {
                                /* Отбираем по идентификатору блок со статьями и дозаполняем его новыми данными */
                                jQuery("#articles").append(data).fadeIn(2000);
                                jQuery(function () {
                                    jQuery('.product-itm a').hover(function () {
                                        jQuery(this).children('.front').stop().fadeOut(400);
                                    }, function () {
                                        jQuery(this).children('.front').stop().fadeIn(200);
                                    });

                                });
                            });

                            /* По факту окончания запроса снова меняем значение флага на false */
                            inProgress = false;
                            // Увеличиваем на 10 порядковый номер статьи, с которой надо начинать выборку из базы
                            startFrom += pagination_shop;
                        }
                    });

                }
            });

        });

    </script>


    <?php
}
if (isset($page) && $page['url'] == 'reviews') {
    ?>
    <!-- СКРИПТ АВТОПОДГРУЗКИ ТОВАРОВ ПРИ СКРОЛЛИНГЕ -->
    <script type="text/javascript">
        console.log("starting...");
        <?php
        //$pager = getOption('pagination_shop');
        ?>
        var pagination_shop = 20;
        /* Переменная-флаг для отслеживания того, происходит ли в данный момент ajax-запрос. В самом начале даем ей значение false, т.е. запрос не в процессе выполнения */
        var inProgress = false;
        /* С какой статьи надо делать выборку из базы при ajax-запросе */
        var startFrom = pagination_shop;

        var getajax = false;
        if (startFrom == pagination_shop) getajax = true;

        var mob = <?php if (check_smartphone()) echo "true"; else echo "false";?>;

        var bottom = 800;
        if (mob == true) bottom = 1500;

        /* Используйте вариант $('#more').click(function() для того, чтобы дать пользователю возможность управлять процессом, кликая по кнопке "Дальше" под блоком статей (см. файл index.php) */
        jQuery(window).scroll(function () {
            console.log('scrolling...');
            //$('#more').click(function(){
            /* Если высота окна + высота прокрутки больше или равны высоте всего документа и ajax-запрос в настоящий момент не выполняется, то запускаем ajax-запрос */
            if (jQuery(window).scrollTop() + jQuery(window).height() >= jQuery(document).height() - bottom && !inProgress) {

                jQuery.ajax({
                    /* адрес файла-обработчика запроса */
                    url: '/ajax/getnextreviews/',
                    /* метод отправки данных */
                    method: 'POST',
                    /* данные, которые мы передаем в файл-обработчик */
                    data: {
                        "startFrom": startFrom,
                        "perPage": pagination_shop,
                        "getajax": getajax
                    },
                    beforeSend: function () {
                        /* меняем значение флага на true, т.е. запрос сейчас в процессе выполнения */
                        inProgress = true;
                    }
                    /* что нужно сделать до отправки запрса */

                    /* что нужно сделать по факту выполнения запроса */
                }).done(function (data) {
                    jQuery("#articles").append(data).fadeIn(2000);

                    /* По факту окончания запроса снова меняем значение флага на false */
                    inProgress = false;
                    // Увеличиваем на 10 порядковый номер статьи, с которой надо начинать выборку из базы
                    //startFrom += pagination_shop;
                    startFrom++;
                });
            }
        });
    </script>


    <?php
}
?>

<script>
    /** Приклеенная шапка _*/
    jQuery(window).scroll(function (e) {
        parallax();


        if (jQuery(this).scrollTop() > 80) {
            jQuery('.container-fluid.sticky').addClass('sticky-header');
            jQuery('.counter').hide();
        } else {
            jQuery('.container-fluid.sticky').removeClass('sticky-header');
            jQuery('.counter').fadeIn(1300);
        }

    });

    function parallax() {
        var scrolled = jQuery(window).scrollTop();
        jQuery('.paralax').css('top', 100 - (scrolled * 0.35) + 'px');
    }

    /******************END  Приклеенная шапка */
</script>

<script>
    // Кнопка "Вверх"
    /**Paralaxx effect___*/
    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > 0) {
            jQuery('#ontop').fadeIn(1500);
        } else {
            jQuery('#ontop').fadeOut(1500);
        }
    });
    jQuery('#ontop').click(function () {
        jQuery('body,html').animate({scrollTop: 0}, 700);
        return false;
    });

    function loadModal(id = 'qqq') {
        jQuery('#'+id).modal('show');
    }

    setTimeout(loadModal, 4000);

    var j = jQuery.noConflict();

    var showed = false;
    j(document).ready(function () {
        j('.xhideme').slideUp(500);

        j('.xnocookies').click(
            function () {
                if (!showed) {
                    j(this).siblings('.xhideme').stop(false, true).slideDown(500);
                    showed = true;
                    j('#description-show').html('Спрятать');
                }
                else {
                    j('.xhideme').slideUp(500);
                    showed = false;
                    j('#description-show').html('Читать дальше');
                }

                //j(this).html('Спрятать');
            }
        );
    });

</script>

<!-- РЕЙТИНГ ЗВЁЗДЫ -->
<link href="/css/jquery.rating.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="/js/jquery.rating.js"></script>
<script type="text/javascript">
    var j = jQuery.noConflict();
    j(document).ready(function () {
        // настройки звёзд в карточке товара
        j('div.rating').rating({
            readOnly: true,
            image: '/images/stars.png',
            loader: '/images/ajax-loader.gif',
            width: 32
        });

        j('div.rating_mini').rating({
            readOnly: true,
            image: '/images/stars16.png',
            loader: '/images/ajax-loader.gif',
            width: 16
        });

        // вывод звёзд в попапе "Оставить отзыв"
        j(function () {

            j('#rating_shop').rating({
                fx: 'half',
                image: '/images/stars.png',
                loader: '/images/ajax-loader.gif',
                width: 32,
                url: '/ajax/rate/'
            });
        })
    });

</script>


<script>
    function set_currency(currency) {
        r(".curval").hide();
        r(".price-"+currency).show();
        r("#currency-change-ul").hide();
        if(currency == 'usd') r("#current-currency").html('<span>&#36;</span>USD');
        else if(currency == 'rub') r("#current-currency").html('<span>&#x584;</span>RUB');
        else if(currency == 'uah') r("#current-currency").html('<span>&#8372;</span>UAH');
        console.log('Переключили валюту на: '+currency);
        userdata('set','currency', currency);

        // r(".product_price").html(r(thi))

        var elems = r(".product_price");
        var elemsTotal = elems.length;
        for(var i=0; i<elemsTotal; ++i){
            r(elems[i]).html(r(elems[i]).attr(currency));
        }
        var elems = r(".product_old_price");
        var elemsTotal = elems.length;
        for(var i=0; i<elemsTotal; ++i){
            r(elems[i]).html(r(elems[i]).attr(currency));
        }
    }
    var r = jQuery.noConflict();
    var showCurrenciesList = false;
    r(document).ready(function () {
        //r(".curval").hide();

        r("#current-currency").click(function () {
            if(showCurrenciesList == false) {
                r("#currency-change-ul").show();
                showCurrenciesList = true;
            }
            else {
                r("#currency-change-ul").hide();
                showCurrenciesList = false;
            }
        });

        r(".currency-change").click(function () {


            var currency = r(this).attr('currency');
            // alert(currency);
            set_currency(currency);

            showCurrenciesList = false;
        });


//        r("#currency_change_rub").click(function () {
//            r(".curval").hide();
//            r(".price-rub").show();
//        });
//
//        r("#currency_change_uah").click(function () {
//            r(".curval").hide();
//            r(".price-uah").show();
//        });
//
//        r("#currency_change_usd").click(function () {
//            //r(".curval").hide();
//            r(".price-usd").show();
//        });
    });




    function showUah() {
        r(".curval").hide();
        r(".price-uah").show();
    }

    function userdata(action, name, value) {
        r.ajax({
            url: '/ajax/userdata/' + action + '/?name=' + name + '&value=' + value,
            method: 'post',
            async: false,
            data: {
                "name": name,
                "value": value
            },

        }).done(function (data) {
            console.log('Выбрана страна id: ' + value);
        });
    }
</script>
<!-- /РЕЙТИНГ ЗВЁЗДЫ -->
<!--script src="/js/snow.js"></script!-->