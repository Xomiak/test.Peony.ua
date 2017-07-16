<?php
$main_currency = getMainCurrency();
$currency = $this->session->userdata('currency');

if (!$currency)
    $currency = $main_currency;

$currency = userdata('currency');
if(!$currency) $currency = 'uah';

$my_cart = array();
if ($this->session->userdata('my_cart') !== false)
$my_cart = $this->session->userdata('my_cart');
if (userdata('coupon') !== false) {
$coupon = userdata('coupon');
$coupon = couponCheck($coupon);
//	vd($coupon);
if(isset($coupon['err']))
{
unset_userdata('coupon');
unset($coupon);
}
}
//var_dump($my_cart);
$count = count($my_cart);

//var_dump($my_cart);

//var_dump($my_cart);
$full_price = 0;
$full_price2 = 0;
$full_price3 = 0;

$notSalePrice = 0;

$currensy_grn = getCurrencyValue('UAH');
$currensy_rub = getCurrencyValue('RUB');


?>

<script>
    var russiaDelivery = 0;
    var fullPrice = 0;
    var discountPrice = 0;
    var currencyUah = <?=$currensy_grn?>;
    var currencyRub = <?=$currensy_rub?>;
    var shopCount = <?=shop_count()?>;
</script>

    <form id="shop_form" method = "post" action = "<?= $_SERVER['REQUEST_URI'] ?>">
        <input type="HIDDEN" name="resumm" value="true" />
        <div id = "my_cart" class = "cart-container">
            <?php
            if ($count > 0)
            {
            ?>
            <div class = "responsive-table">
                <table>
                    <thead>
                    <tr>
                        <td style = "width: 100px"></td>
                        <td style = "width: 230px">Наименование товара</td>
                        <td style = "width: 70px">Размер</td>
                        <td style = "width: 110px">Количество</td>
                        <td style = "width: 160px" colspan="2">Удалить</td>
                        <td style = "width: 240px">Цена</td>
                        <td style = "width: 240px">Сумма</td>
                    </tr>
                    </thead>
                    <?php
                    $count = count($my_cart);
                    $countProducts = shop_count();
                    //vd($my_cart);
                    for ($i = 0; $i < $count; $i++) {
                        $mc = $my_cart[$i];
                        $shop = $this->shop->getArticleById($mc['shop_id']);
                        $cat = $this->model_categories->getCategoryById($shop['category_id']);
                        ?>
                        <tr style = "border-top:1px solid #c2c2c2;">
                            <td class = "b-img">
                                <a href = "/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                                    <img src = "<?= CreateThumb2(200, 150, $shop['image'], 'cart'); ?>"/>
                                </a>
                            </td>
                            <td>
                                <a href = "/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                                    <?= $shop['name'] ?> (<?=$shop['color']?>)

                                </a>
                                <?php
                                if($shop['discount'] > 0) echo '<span style="color:red">Sale!</span>';
                                ?>
                            </td>
                            <td>
                                <?php
                                $razmer = explode('*', $shop['razmer']);

                                $rcount = count($razmer);

                                for ($i2 = 0; $i2 < $rcount; $i2++) {
                                    ?>
                                    <p<?php if (!isset($mc['kolvo_' . $razmer[$i2]]) || $mc['kolvo_' . $razmer[$i2]] == 0)
                                        echo ' style="display:none;"';?>><?= $razmer[$i2] ?></p>
                                    <?php
                                }
                                ?>
                            </td>
                            <td>
                                <?php

                                $razmer = explode('*', $shop['razmer']);
                                $rcount = count($razmer);
                                $kolvo = 0;
                                $prsizes = 0;
                                for ($i2 = 0; $i2 < $rcount; $i2++) {
                                    if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != 0) {
                                        $kolvo = $kolvo + $mc['kolvo_' . $razmer[$i2]];
                                        $prsizes++;
                                        ?>
                                        <p>
                                            <input class = "cart_numb" id = "kolvo_<?= $razmer[$i2] ?>_<?= $mc['shop_id'] ?>" type = "num" name = "kolvo_<?= $razmer[$i2] ?>_<?= $mc['shop_id'] ?>" value = "<?= $mc['kolvo_' . $razmer[$i2]] ?>"/>&nbsp;шт.
                                        </p>
                                        <!--script>
                                            $(document).ready(function() {
                                            // Назначение события
                                                $("#kolvo_<?= $razmer[$i2] ?>_<?= $i ?>").keyup(function() {
                                                // Посылка запроса
                                                var kolvo = $("#kolvo_<?= $razmer[$i2] ?>_<?= $i ?>").val();

                                                $.post("/ajax/cart_actions/",{
                                                // Параметр передаваемый в скрипт
                                                state: "1",
                                                save_kolvo: "1",

                                                },function(data) {
                                                // Присвоение возвращённых данных (data), элементу с id=result
                                                $("#summ_<?= $i ?>").html(data);
                                                });


                                                });

                                            });
                                        </script-->
                                        <?php
                                    } else {
                                        ?>
                                        <p style = "display: none">
                                            <input class = "cart_numb" id = "kolvo_<?= $i2 ?>" type = "num" name = "kolvo_<?= $razmer[$i2] ?>_<?= $mc['shop_id'] ?>" value = "0"/>&nbsp;шт.
                                        </p>
                                        <?php
                                    }
                                }
                                ?>

                            </td>
                            <td>
                                <?php
                                for ($i2 = 0; $i2 < $rcount; $i2++) {
                                    if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != 0) {
                                        echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '?del_shop_id=' . $mc['shop_id'];
                                        if($prsizes > 1)
                                            echo '&razmer=' . $razmer[$i2] ;
                                        echo '">X</a></p>';
                                    }
                                }
                                ?>
                                <!--							<a class = "mycart_del" href = "--><?//= $_SERVER['REQUEST_URI'] ?><!--?del_shop_id=--><?//= $mc['shop_id'] ?><!--" onclick = "return confirm('Вы действительно хотите удалить?');">Удалить товар</a>-->
                            </td>
                            <td>
                                <a class = "mycart_del" href = "?del_shop_id=<?=$mc['shop_id'] ?>" onclick = "return confirm('Вы действительно хотите удалить?');">X</a>
                            </td>
                            <td>
                                <?php $res = getNewPrice($shop['price'], $shop['discount']);
                                $res = round($res,2);
                                //$full_price = $full_price + $res;
                                echo '<i class="curval price-usd"'.($currency != 'usd'? ' style="display: none"':'').'>' . $res ?>&nbsp;<?= ' $</i>' ?>
                                <?php

                                $price = $res * $currensy_grn;
                                echo '<i class="curval price-uah"'.($currency != 'uah'? ' style="display: none"':'').'>' . round($price, 2) . ' грн</i>';
                                $price = $res * $currensy_rub;
                                echo '<i class="curval price-rub"'.($currency != 'rub'? ' style="display: none"':'').'>' . round($price, 2) . ' р</i>';
                                ?>
                            </td>
                            <td>
                                <div id = "summ_<?= $i ?>">
                                    <?php $res = round($res,2) * $kolvo;
                                    $res = round($res,2);
                                    $full_price = $full_price + $res;
                                    $full_price = round($full_price,2);
                                    if($shop['discount'] == 0){
                                        $notSalePrice = round($notSalePrice + $res,2);
                                    }

                                    echo '<i class="curval price-usd"'.($currency != 'usd'? ' style="display: none"':'').'>' . $res . ' $</i>' ?>
                                    <?php
                                    //$currensy_grn = $this->model_options->getOption('usd_to_uah');
                                    $price = $res * $currensy_grn;
                                    echo '<i class="curval price-uah"'.($currency != 'uah'? ' style="display: none"':'').'>' . round($price, 2) . ' грн</i>';
                                    //$currensy_rub = $this->model_options->getOption('usd_to_rur');
                                    $price = $res * $currensy_rub;
                                    echo '<i class="curval price-rub"'.($currency != 'rub'? ' style="display: none"':'').'>' . round($price, 2) . ' р</i>';
                                    ?>
                                </div>
                            </td>

                        </tr>
                        <?php
                    }
                    if ($count == 0) {
                        ?>
                        <tr>
                            <td colspan = "5">Ваша корзина пуста...</td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <a name="full-price"></a>
            <div class = "full-price">
                <p class = "coupon">Промокод<img id="my-cart-code-help" title="Если у Вас есть промокод на скидку, укажите его здесь" data-target=".my-cart-code-help" data-toggle="modal" src="/img/system-help.png" />:
                    <input class="cart_numb" type = "text" name = "coupon" placeholder = "Промокод" value = "<?php if (isset($coupon['code']))
                        echo $coupon['code']; ?>"/>
                    <?php
                    if(isset($coupon['code'])) echo '<a href="/my_cart/?coupon_cancel=true"><input type="button" id="coupon_cancel" name="coupon_cancel" value="Отмена" /></a>';
                    else echo '<input type="button" value="OK" />';
                    ?>
                </p>

                <?php
                $full_price_discount = $full_price;
                if (isset($coupon) && !isset($coupon['err'])) {
                    $discount = $coupon['discount'];
                    if($coupon['type'] == 0)
                    {
                        if($coupon['not_sale'] == 1){
                            $res = $notSalePrice / 100 * $discount;
                            $full_price_discount = $full_price - $res;
                        } else {
                            $res = $full_price / 100 * $discount;
                            $full_price_discount = $full_price - $res;
                        }
                    }
                    elseif($coupon['type'] == 1)
                    {
                        $full_price_discount = $full_price - $discount;
                    }
                }
                ?>



                <ul>
                    <li>Товаров в корзине на сумму:</li>
                    <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span id="products_price_usd"><?= round($full_price,2) ?></span> $</li>
                    <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span id="products_price_uah"><?= round($full_price * $currensy_grn, 2) ?></span>&nbsp;грн.</li>
                    <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>><span id="products_price_rub"><?= round($full_price * $currensy_rub, 2) ?></span>&nbsp;р</li>
                </ul>

                <?php
                //vd(userdata('country'));
                $deliveryPrice = 0;
                $country = getMyCountry();
               // vd($country);
                //$country = 1;
                //if(userdata('country')!==false) $country = userdata('country');
                if($country && !is_array($country)){
                    $myCountry = getCountryByName($country);
                    if(!$myCountry) $myCountry = getCountryById($country);
                    if($myCountry)
                        $country = $myCountry;
                }
                //vd($country);
                if(is_array($country) && isset($country['delivery_price']) && $country['delivery_price'] > 0) {
                    if($country['bigopt_from'] < $countProducts)
                        $country['delivery_price'] = $country['bigopt_delivery_price'];
                    $deliveryPrice = $country['delivery_price'] * shop_count();

                        $countryName = '';
                        if(isset($country['name'])) $countryName = $country['name'];
                        else if($country) $countryName = $country;
                        //alert(post('country'));
                        //vd($_GET['country']);
                    $deliveryStyle = '';
                        ?>
                        <script>
                            russiaDelivery = <?=round($deliveryPrice, 2)?>;
                        </script>
                        <ul id="russia-delivery" style="<?= $deliveryStyle ?>">
                            <li class="red" style="">Стоимость доставки в Вашу страну (<?=$countryName?>):</li>
                            <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span><?= round($deliveryPrice, 2) ?></span> $
                            </li>
                            <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span><?= round($deliveryPrice * $currensy_grn, 2) ?></span>&nbsp;грн.&nbsp;
                            </li>
                            <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>>
                                <span><?= round($deliveryPrice * $currensy_rub, 2) ?></span>&nbsp;р
                            </li>
                        </ul>
                        <?php

                }

                // Если использован купон
                if(isset($coupon))
                {
                    ?>
                    <script>
                        discountPrice = <?=$full_price_discount?>;
                    </script>
                    <ul>
                        <li>Со скидкой:</li>
                        <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span id="discount_price_usd"><?= round($full_price_discount,2) ?></span> $</li>
                        <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span id="discount_price_uah"><?= round($full_price_discount * $currensy_grn,2) ?>&nbsp;</span>грн</li>
                        <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>><span id="discount_price_rub"><?= round($full_price_discount * $currensy_rub,2) ?></span>&nbsp;р</li>
                    </ul>
                    <ul>
                        <li>Инормация о промокоде:</li>
                        <li>
                            Скидка: <?=$coupon['discount']?><?php if($coupon['type'] == 1) echo ' USD'; elseif($coupon['type'] == 0)echo '%'; ?>
                        </li>
                    </ul>
                    <?php
                    $full_price = $full_price_discount;
                }
                ?>

                <?php
                $shop_nadbavka = 0;
                $kolvo = shop_count();
                if(isset($country['opt_from'])) $shop_opt_from = $country['opt_from'];
                if($kolvo < $shop_opt_from)
                {
                    $userType = false;
                    if($user)
                        $userType = getItemById($user['user_type_id'], 'user_types');
                    if(!isset($user_type['nadbavka'])){
                        $user_type = getItemById(1, 'user_types');
                    }
                    if(isset($user_type['nadbavka']))
                        $shop_nadbavka = $user_type['nadbavka'];


                    if(!$shop_nadbavka) $shop_nadbavka = 0;
                   // $shop_nadbavka = $shop_nadbavka * $kolvo;

                    if(isset($full_price_discount)&& $full_price_discount > 0) $full_price = $full_price_discount;

                    // Розничная надбавка в зависимости от страны
                    if(isset($country['nadbavka']) && $country['nadbavka'] > 0)
                        $shop_nadbavka += $country['nadbavka'];


                    $order_price = $shop_nadbavka + $full_price;
                    //vd($order_price);
//vd($shop_nadbavka);
                    if($shop_nadbavka > 0) {

                        set_userdata('order_price', $order_price);
                        ?>
                        <ul class="nadbavka">
                            <li class="red">Розничная надбавка:</li>
                            <li class="curval price-usd"<?= ($currency != 'usd' ? ' style="display: none"' : '') ?>>
                                <span><?= $shop_nadbavka ?></span> $
                            </li>
                            <li class="curval price-uah"<?= ($currency != 'uah' ? ' style="display: none"' : '') ?>>
                                <span><?= round($shop_nadbavka * $currensy_grn, 2) ?>&nbsp;</span>грн
                            </li>
                            <li class="curval price-rub"<?= ($currency != 'rub' ? ' style="display: none"' : '') ?>>
                                <span><?= round($shop_nadbavka * $currensy_rub, 2) ?></span>&nbsp;р
                            </li>
                        </ul>

                        <?php
                    }

                }
                // прибавляем стоимость доставки
                    $full_price += $deliveryPrice;

                $full_price += $shop_nadbavka;

                $npnp = userdata('npnp');
                $npnp_price = getOption('npnp_price');
                // Если через платёжные системы
                $paymentMethod = userdata('paymentmethod');
                //vd($paymentMethod);
                if($paymentMethod == 'liqpay'){
                    $paymentMethod = 'LiqPay';
                    $paymentMethodValue = $full_price/100*3;
                    if($npnp && $country == 1) {
                        $paymentMethodValue = $npnp_price / 100 * 3;
                        //var_dump($paymentMethodValue);
                    }
                    ?>
                    <!--ul class="paymentNadbavka">
                        <li>Услуги <?=$paymentMethod?>:</li>
                        <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span><?= $paymentMethodValue ?></span> $</li>
                        <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span><?= round($paymentMethodValue * $currensy_grn,2) ?>&nbsp;</span>грн</li>
                        <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>><span><?= round($paymentMethodValue * $currensy_rub,2) ?></span>&nbsp;р</li>
                    </ul-->
                    <?php
                    //$full_price += $paymentMethodValue;
                }



                if($npnp && $country == 1){
                    //vd($full_price*$currensy_grn);
                    $full_price = $full_price - $npnp_price;
                    ?>
                    <ul>
                        <li class="nadbavka">Предоплата:</li>
                        <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span id="full_price_usd"><?= round($npnp_price,2) ?></span> $</li>
                        <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span id="full_price_uah"><?= round(($npnp_price) * $currensy_grn,2) ?>&nbsp;</span>грн</li>
                        <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>><span id="full_price_rub"><?= round(($npnp_price) * $currensy_rub,2) ?></span>&nbsp;р</li>
                    </ul>
                    <ul>
                        <li class="nadbavka">Сумма наложенного платежа:</li>
                        <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span id="full_price_usd"><?= round($full_price,2) ?></span> $</li>
                        <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span id="full_price_uah"><?= round(($full_price) * $currensy_grn,2) ?>&nbsp;</span>грн</li>
                        <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>><span id="full_price_rub"><?= round(($full_price) * $currensy_rub,2) ?></span>&nbsp;р</li>
                    </ul>
                    <script>
                        fullPrice = <?=$full_price?>;
                    </script>
                    <?php
                }else {
                ?>
                    <ul>
                        <li class="nadbavka" style="font-weight: bolder">Всего к оплате:</li>
                        <li class="curval price-usd"<?=($currency != 'usd'? ' style="display: none"':'')?>><span id="full_price_usd"><?= round($full_price,2) ?></span> $</li>
                        <li class="curval price-uah"<?=($currency != 'uah'? ' style="display: none"':'')?>><span id="full_price_uah"><?= round(($full_price) * $currensy_grn,2) ?>&nbsp;</span>грн</li>
                        <li class="curval price-rub"<?=($currency != 'rub'? ' style="display: none"':'')?>><span id="full_price_rub"><?= round(($full_price) * $currensy_rub,2) ?></span>&nbsp;р</li>
                    </ul>
                    <script>
                        fullPrice = <?=$full_price?>;
                    </script>
                <?php } ?>


            <ul class="basket_btn_list">
                <li><a href = "/all">&larr; вернуться в каталог</a></li>
                <!--li><input class = "basket-but" type = "submit" value = "Пересчитать" name = "resumm"/></li-->
                <li class="one-click-div">
                    <input onclick="getMyCartData()" type="button" id="button_goto_one_click" position="my_cart" data-toggle="modal" data-target=".buy_one_click" value="Купить  в  1  клик" />
                </li>
                <li><?php
                    if(userdata('login') === false) {
                        ?>
                        <a href="#step_2">
                            <input id="button_goto_step_2" data-toggle = "modal" data-target = ".order-without-autorization" class="basket-but" type="button" value="Оформить доставку"/>
                        </a>

                        <script>
                            function hideButtonStep2() {
                                //$("#button_goto_step_2").hide();
                            }
                        </script>
                        <?php
                    }
                    ?></li>
            </ul>

            </div>
			<?php
            if(isset($coupon['not_sale']) && $coupon['not_sale'] == 1) {
                ?>
                <p class="no-opt-kolvo right-notification"><sup>*</sup>
                    Скидка не действует на товары из раздела Sale!
                </p>
                <?php
               // echo '<div style="clear:both"></div><div class="not-sale-discount" style="font-size: 13px;">Скидка не действует на товары из раздела Sale!</div>';
            }
			if($kolvo < $shop_opt_from)
			{
				?>
				<p class="no-opt-kolvo right-notification"><sup>*</sup>
					<?=str_replace('[shop_opt_from]',$shop_opt_from,getOption('shop_order_min_kolvo'))?>
				</p>
			<?php
			}
			?>
        </div>
    </form>
    <?php

}
else {
	echo '<div class="empty-basket"><img src="/images/empty_korz.png"/>

	<p>Ваша корзина пуста...
	Перейдите
	<a href="/all/">в каталог</a> и выберите товар!</p>
	</div>
	';
}
?>
<script src = "/js/jquery.min.js"></script>
<script>
    /**
     * Функция для отправки формы средствами Ajax
     * для обновления таблицы с товарами
     **/
    function AjaxFormRequest(result_id,form_id,url) {
        //alert(jQuery("#"+form_id).serialize());
        jQuery.ajax({
            url:     url, //Адрес подгружаемой страницы
            type:     "POST", //Тип запроса
            async: false,
            dataType: "html", //Тип данных
            data: jQuery("#"+form_id).serialize(),
            success: function(response) { //Если все нормально
                $("#result_div").html(response);
            },
            error: function(response) { //Если ошибка
                alert("Ошибка при отправке формы");
            }
        });
    }

    $(document).ready(function () {
        $( ".cart_numb" ).change(function() {
            AjaxFormRequest('result_div', 'shop_form', "/my_cart/complete/");
        });
    });
</script>

<?php include('application/views/jquery.php'); ?>
<?php
if(userdata('msg') !== false)
{
    getModalDialog('modal_login_ok', userdata('msg'));
    ?>
    <script type="text/javascript">
        j(document).ready(function () {
            //jQuery('#modal_login_ok').modal('show');
        });
    </script>
    <?php
    unset_userdata('msg');

}
?>
