<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>

<section class = "container user-date">
	<h1>Заказ №<?=$order['id']?></h1>
<?php
$full_price = 0;
//vd($order);
?>
<ul>
	<li>
		<span>Дата заказа:</span>
		<?=$order['date']?> <?=$order['time']?>
	</li>
	<li>
		<span>Статус:</span>
		<?=getStatus($order['status'])?>
        <?php
        if($order['status'] == 'new')
            echo '[<a class="show-order" href="/user/order-cancel/'.$order['id'].'/" onclick="return confirm(\'Вы точно хотите отменить заказ №'.$order['id'].'?\')">Отменить заказ</a>]';
        elseif($order['status'] != 'done' && $order['status'] != 'canceled')
            echo '[<a class="show-order" href="/user/order-done/'.$order['id'].'/" onclick="return confirm(\'Вы точно хотите подтвердить получение заказа №'.$order['id'].'\')">Подтвердить получение</a>]';
        ?>
	</li>
	
	<li>
		<span>Доставка:</span>
		<?=$order['delivery']?>
	</li>
	<li>
		<span>Метод оплаты:</span>
		<?=$order['payment']?>
        <?php
        if(isNeedPay($order)){
            echo ' [ <a href="/payment/liqpay/'.$order['id'].'/">перейти к оплате</a> ]';
        }
        ?>
	</li>
	<li>
		<span>Валюта:</span>
        <?php
        $currency = getCurrencyByCode(strtoupper($order['currency']));
        if($currency) echo $currency['name'];
        ?>

	</li>

	<li>
		<span>Данные доставки:</span>
		<?=$order['adress']?>
	</li>
	<li>
		<span>Общая сумма:</span>
		<i><?=$order['summa']?> $ / </i>
        <i><?php
            $currensy_grn = $this->model_options->getOption('usd_to_uah');
            echo ($order['summa'] * $currensy_grn);
            ?> грн / 
        </i>
        <i><?php
            $currensy_rub = $this->model_options->getOption('usd_to_rur');
            echo ($order['summa'] * $currensy_rub);
            ?> руб
        </i>
	</li>
</ul>

	<div class = "cart-container">
		<div class = "responsive-table">
<table class="user-all-order">
    <tr>
		<th></th>
        <th>Название</th>
        <th>Размер / Кол-во</th>
        <th>Цена товара</th>
        <th>Количество</th>
        <th>Сумма</th>
    </tr>
    <?php
    $my_orders = unserialize($order['products']);
 
    for ($i = 0; $i < count($my_orders); $i++) {
        $mc = $my_orders[$i];
		//vdd($mc);
        $shop = $this->model_shop->getArticleById($mc['shop_id']);
        $url = getFullUrl($shop);
        //$cat = $this->model_categories->getCategoryById($shop['category_id']);
        //$shop['name'] = unserialize($shop['name']);
        ?>
        <tr>
            <td>
            	<a href="<?=$url?>">
                	<img src="<?= CreateThumb2(120, 80, (isset($shop['image']) && !empty($shop['image']))? $shop['image'] : '/img/net_foto.png', 'cart'); ?>"/>
            	</a>
            </td>
            <td class="cart_name_row" >
                <a href="<?=$url?>">
                   <?= $shop['name'] ?> (<?= $shop['color']?>)
                </a>
            </td>
            <td>
                <?php 
                $summa = 0;
                $kolvo = 0;
                $razmer = explode('*', $shop['razmer']);
                if(is_array($razmer))
					{
						$count = count($razmer);
						for($j = 0; $j < $count; $j++)
						{
							$r = $razmer[$j];
							if(isset($mc['kolvo_'.$r]) && $mc['kolvo_'.$r] > 0)
							{
								echo $r.' / '.$mc['kolvo_'.$razmer[$j]];
                                if(($j+1) < $count) echo '<br />';
                                $res = getAkciyaPrice($shop) * $mc['kolvo_'.$razmer[$j]];
                                $summa = $summa + $res;
                                $kolvo += $mc['kolvo_'.$razmer[$j]];
							}
						}
					}
                ?>
            </td>
        

            <td>
                <i><?=$shop['price']?> $ </i>
                <i><?php
                    $currensy_grn = $this->model_options->getOption('usd_to_uah');
                    echo ($shop['price'] * $currensy_grn);
                    ?> UAH
                </i>
                <i><?php
                    $currensy_rub = $this->model_options->getOption('usd_to_rur');
                    echo ($shop['price'] * $currensy_rub);
                    ?> RUB
                </i>
            </td>
            <td class="cart_price_row">
            	<?=$kolvo?>
            </td>
            <td class="cart_price_row">
                <p id="summ_<?= $i ?>">
                    <?php
                    $product_full_price = $summa;

                    $full_price += $product_full_price;
                    ?>
                    <i><?=$product_full_price?> $</i>
	                <i><?php
	                    $currensy_grn = $this->model_options->getOption('usd_to_uah');
	                    echo ($product_full_price * $currensy_grn);
	                    ?> UAH
	                </i>
	                <i><?php
	                    $currensy_rub = $this->model_options->getOption('usd_to_rur');
	                    echo ($product_full_price * $currensy_rub);
	                    ?> RUB
	                </i>
                </p>
            </td>
           
        </tr>
        <?php
    }
    ?>

</table>
<a href="/user/mypage/">Назад в личный кабинет</a>

</section>
<?php include("application/views/footer_new.php"); ?>
