<?php include("application/views/head_new.php"); ?>
<?php include("application/views/header_new.php"); ?>
    <!--==============================content================================-->

    <section class="container news-list">
        <div class="breadcrumbs">
            <div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>/
                Оплата заказа через Интеркассу
            </div>
        </div>
        <?php
        if(!isset($order) && isset($_GET['order_id']))
            $order = $this->shop->getOrderById($_GET['order_id']);
        ?>
        <h1>Оплата заказа <b>№<?=$order['id']?></b>  через Интеркассу</h1>
        <?php
        //vd($order);
        $showPay = true;
        if(isset($payed) && $payed == true) {
            echo "<b>Статус платежа:</b> ".getStatus($status);

            if(userdata('login') !== false) {
                ?>
                <p>Отслеживать статус заказа Вы можете в своём <a href="/user/mypage/">личном кабинете</a>.</p>
                <?php
            }
                ?>

                <?php
//            $dataSet = $_POST;
//            $key = getOption('interkassa_secret');
//            unset($dataSet['ik_sign']);
//            ksort($dataSet, SORT_STRING);
//            array_push($dataSet,$key);
//            $signString = implode(':', $dataSet);
//            $sign = base64_encode(md5($signString, true));
            //vd($sign);
            if($status == 'not_payed' || $status == 'canceled' || $status == 'fail' || $status == 'new') $showPay = true;
            else $showPay = false;
        }

        if($order['status'] == 'done' || $order['status'] == 'sended'){
            $showPay = false;
            echo 'Ваш заказ уже был оплачен!<br />
                <a href="/user/order-details/'.$order['id'].'/?hash='.$user['pass'].'">Детальная информация о заказе №'.$order['id'].'</a>';
        }

        if($showPay){
            $currency_val = 1;
            if ($order['currency'] == 'uah')
                $currency_val = getCurrencyValue('UAH');
            elseif ($order['currency'] == 'rub')
                $currency_val = getCurrencyValue('RUB');
            $summa = $order['full_summa'] * $currency_val;
            ?>
            Заказ №<?= $order['id'] ?>.<br/>
            Сумма заказа: <?= $summa ?> <?= $order['currency'] ?><br/>
            <article class="article-content">
                <form name="payment" method="post" action="https://sci.interkassa.com/" method="post"
                      accept-charset="UTF-8">
                    <input type="hidden" name="ik_co_id" value="581a04f73c1eaf9c2e8b456f">
                    <input type="hidden" name="ik_am" value="<?= $summa ?>">
                    <input type="hidden" name="ik_cur" value="<?= strtoupper($order['currency']) ?>">
                    <input type="hidden" name="ik_pm_no" value="<?= $order['id'] ?>">
                    <input type="hidden" name="ik_desc"
                           value="Оплата заказа №<?= $order['id'] ?> в интернет-магазине <?= $_SERVER['SERVER_NAME'] ?>">
                    <input type="submit" name="process" value="Оплатить">
                </form>

            </article>
            <?php
        }
            ?>
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
    </section>
<?php include("application/views/footer_new.php"); ?>