<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>
    <!--==============================content================================-->

    <section class="container news-list">
        <div class="breadcrumbs">
            <div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>/
                Оплата заказа через ИнтерКассу
            </div>
        </div>

        <h1>Оплата через ИнтерКассу</h1>
        <?php
        vd($order);
        $currency_val = 1;
        if($order['currency'] == 'uah')
            $currency_val = $this->model_options->getOption('usd_to_uah');
        elseif($order['currency'] == 'rub')
            $currency_val = $this->model_options->getOption('usd_to_rur');
        $summa = $order['full_summa'] * $currency_val;
        ?>
        Заказ №<?=$order['id']?>.<br />
        Сумма заказа: <?=$summa?> <?=$order['currency']?><br />
<!--        <article class="article-content">-->
<!--            <form name="payment" method="post" action="https://sci.interkassa.com/" method="post" accept-charset="UTF-8">-->
<!--                <input type="hidden" name="ik_co_id" value="580cd2253b1eaf70748b456c">-->
<!--                <input type="hidden" name="ik_am" value="--><?//=$summa?><!--">-->
<!--                <input type="hidden" name="ik_cur" value="--><?//=strtoupper($order['currency'])?><!--">-->
<!--                <input type="hidden" name="ik_pm_no" value="--><?//=$order['id']?><!--">-->
<!--                <input type="hidden" name="ik_desc" value="Оплата заказа №--><?//=$order['id']?><!-- в интернет-магазине --><?//=$_SERVER['SERVER_NAME']?><!--">-->
<!--                <input type="submit" name="process" value="Оплатить">-->
<!--            </form>-->
<!---->
<!--        </article>-->
    </section>
<?php include("application/views/footer_new.php"); ?>