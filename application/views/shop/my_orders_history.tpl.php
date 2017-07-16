<?php
$orders = $this->shop->getOrdersByUserId($user['id'], $sort);
$count = count($orders);
?>
<div class = "all-order">
	<h2 class = "user-h2">История заказов</h2>

	<form id = "form_sort" action = "<?= $_SERVER['REQUEST_URI'] ?>" method = "post">
		<span>Сортировать</span>
		<select name = "sort">
			<option value = "all" <?= (isset($sort) && $sort == 'all') ? 'selected' : '' ?> > Все</option>
			<option value = "new" <?= (isset($sort) && $sort == 'new') ? 'selected' : '' ?> ><?= getStatus('new') ?></option>
			<option value = "processing" <?= (isset($sort) && $sort == 'processing') ? 'selected' : '' ?> ><?= getStatus('processing') ?></option>
			<option value = "payed" <?= (isset($sort) && $sort == 'payed') ? 'selected' : '' ?> ><?= getStatus('payed') ?></option>
			<option value = "sended" <?= (isset($sort) && $sort == 'sended') ? 'selected' : '' ?> ><?= getStatus('sended') ?></option>
			<option value = "done" <?= (isset($sort) && $sort == 'done') ? 'selected' : '' ?> ><?= getStatus('done') ?></option>
			<option value = "canceled" <?= (isset($sort) && $sort == 'canceled') ? 'selected' : '' ?> ><?= getStatus('canceled') ?></option>
		</select>

		<input type = "submit" value = "Сортировать"/>
	</form>
	<script type = "text/javascript">
		var j = jQuery.noConflict();
		j(document).ready(function () {
			j('select[name=sort]').on('change', function () {
				alert("asd");
				j('#form_sort').submit();
			});
		});
	</script>
</div>
<div class = "cart-container">
	<div class = "responsive-table">
		<table class = "user-all-order">
			<tr>
				<th style = "width: 60px">№ заказа</th>
				<th style = "width: 100px">Дата</th>
				<th style = "width: 75px">Кол-во товаров</th>
				<th style = "width: 100px">Сумма</th>
				<th style = "width: 75px">Статус</th>
				<th style = "width: 125px">Действия</th>
			</tr>
			<?php
			if ($orders) {
				for ($i = 0; $i < $count; $i++) {
					$order = $orders[$i];
					$currencies = false;
					if($order['currencies'] != NULL)
						$currencies = json_decode($order['currencies'], true);
					?>
					<tr class = "user-order <?= $order['status'] ?>">
						<td><a href = "/user/order-details/<?= $order['id'] ?>/"><?= $order['id'] ?></a></td>
						<td><?= $order['date'] ?> <?= $order['time'] ?></td>
						<td>
							<?php
							$products = unserialize($order['products']);
							echo shop_count($products);
							?>
						</td>
						<td>
							<i><?= round($order['summa'],2) ?> $</i>
		                <i><?php
							if(!isset($currencies['UAH']))
								$currencies['UAH'] = $this->model_options->getOption('usd_to_uah');
							echo round(($order['summa'] * $currencies['UAH']),2);
							?> uah
		                </i>
		                <i><?php
							if(!isset($currencies['RUB']))
								$currencies['RUB'] = $this->model_options->getOption('usd_to_rur');
							echo round(($order['summa'] * $currencies['RUB']),2);
							?> руб
		                </i>
						</td>
						<td><?= getStatus($order['status']) ?></td>
						<td>
							<a class = "show-order" href = "/user/order-details/<?= $order['id'] ?>/">Детальнее</a>
							<?php
							if ($order['status'] != 'payed' && $order['status'] != 'sended' && $order['status'] != 'done' && $order['status'] != 'sended' && $order['status'] != 'canceled') {
                                echo '<br /><a class="show-order" href="/payment/liqpay/' . $order['id'] . '/">Оплатить картой (LiqPay)</a>';
                                echo '<br /><a class="show-order" href="/payed/interkassa/?order_id=' . $order['id'] . '">Оплатить через Интеркассу</a>';
                            }
							if ($order['status'] == 'new')
								echo '<br /><a class="show-order" href="/user/order-cancel/' . $order['id'] . '/" onclick="return confirm(\'Вы точно хотите отменить заказ №' . $order['id'] . '?\')">Отменить заказ</a>';
							elseif ($order['status'] != 'done' && $order['status'] != 'canceled')
								echo '<br /><a class="show-order" href="/user/order-done/' . $order['id'] . '/" onclick="return confirm(\'Вы точно хотите подтвердить получение заказа №' . $order['id'] . '\')">Подтвердить получение</a>';
							?>

						</td>
					</tr>
				<?php
				}
			} else echo '<tr><td colspan="6">У Вас пока что нет заказов...</td></tr>';
			?>
		</table>
	</div>
</div>