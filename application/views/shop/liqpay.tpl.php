<?php include("application/views/head_new.php"); ?><?php
//if($currency == 'РУБ') $currency = 'rur';
$currency = 'USD';
//vd($usd_full_price * $currency_value);
//vd($full_price);
?>
<?php include("application/views/header_new.php"); ?>
    <div class="container news-list">
    <div class="breadcrumbs">
        <div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>/
            <span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?= $_SERVER['SERVER_NAME'] ?>/user/mypage/">Личный кабинет</a>
			</span>/
            Оплата заказа
        </div>
    </div>
    <section class="container user-date">
        <h1>Оплата заказа №<?=$order['id']?></h1>

        <?php
        $currency = $order['currency'];
        if ($currency == NULL)
            $currency = 'uah';
        $npnp_price = getOption('npnp_price');
        $currency_grn = getCurrencyValue('UAH');
        $currency_rub = getCurrencyValue('RUB');
        $goPay = isNeedPay($order);
        //vd($order);
        ?>
        <ul>
            <li>
                <span>Дата заказа:</span>
                <?= $order['date'] ?> <?= $order['time'] ?>
            </li>

            <li>
                <span>ID оплаты в LiqPay:</span>
                <?= $liqpay_order_id ?>
            </li>
            <?php
            if ($order['npnp'] == 1 && !isset($_GET['pay_all_price'])) {
                ?>
                <li>
                    <span>Предоплата:</span>
                    <?php
                    if ($currency == 'usd') echo $npnp_price . ' $';
                    elseif ($currency == 'rub') echo round($npnp_price * $currency_rub,2) . ' р';
                    else echo ($npnp_price * $currency_grn) . ' грн';

                    if ($goPay) echo ' (<b>не получена</b>)';
                    ?>
                </li>
                <li>
                    <span>Сумма наложенного платежа:</span>
                    <?php
                    $ostatok = $order['full_summa'] - $npnp_price;
                    if ($currency == 'usd') echo $ostatok . ' $';
                    elseif ($currency == 'rub') echo round($ostatok * $currency_rub,2) . ' р';
                    else echo ($ostatok * $currency_grn) . ' грн';

                    // if($goPay) echo ' (<b>не получена</b>)';
                    ?>
                    [<a style="text-transform: unset; font-size: 12px" href="<?=request_uri()?>?pay_all_price=true">Оплатить заказ полностью</a>]
                </li>
                <?php
            }
            ?>
            <!--li>
                <span>За пользование LiqPay:</span>
                <?=round($liqpay_value,2)?> <?=getCurrencyTypeValue($currency)?>
            </li-->

            <li>
                <span>Итого к оплате:</span>
                <?=$full_price?> <?=getCurrencySymb($currency)?>
            </li>

        </ul>
        <?= $form ?>

    </section>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </div>
<?php include("application/views/footer_new.php"); ?>