<?php include("application/views/header_new.php"); ?>
<section class = "container basket" id = "wrap">


<?php
$my_cart = array();
if ($this->session->userdata('my_cart') !== false)
	$my_cart = $this->session->userdata('my_cart');
if (userdata('coupon') !== false)
	$coupon = userdata('coupon');
//var_dump($my_cart);
$count = count($my_cart);

//var_dump($my_cart);

//var_dump($my_cart);
$full_price = 0;
$full_price2 = 0;
$full_price3 = 0;
?>

<div id = "pagetpl">
<form method = "post" action = "<?= $_SERVER['REQUEST_URI'] ?>">
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
					<td style = "width: 120px">Цена</td>
					<td style = "width: 290px">Удалить</td>
				</tr>
				</thead>
				<?php
				$count = count($my_cart);
				for ($i = 0; $i < $count; $i++) {
					$mc = $my_cart[$i];
					$shop = $this->shop->getArticleById($mc['shop_id']);
					$cat = $this->model_categories->getCategoryById($shop['category_id']);
					?>
					<tr style = "border-top:1px solid #c2c2c2;">
						<td class = "b-img">
							<img src = "<?= CreateThumb2(200, 150, $shop['image'], 'cart'); ?>"/>
						</td>
						<td>
							<a href = "/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
								<?= $shop['name'] ?>
							</a>
						</td>
						<td>
							<?php
							$razmer = explode('*', $shop['razmer']);
							$rcount = count($razmer);
							for ($i2 = 0; $i2 < $rcount; $i2++) {
								?>
								<p<?php if (!isset($mc['kolvo_' . $razmer[$i2]]) || $mc['kolvo_' . $razmer[$i2]] == 0)
									echo ' style="display:none;"'; ?>><?= $razmer[$i2] ?></p>
							<?php
							}
							?>
						</td>
						<td>
							<?php

							$razmer = explode('*', $shop['razmer']);
							$rcount = count($razmer);
							$kolvo = 0;
							for ($i2 = 0; $i2 < $rcount; $i2++) {
								if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != 0) {
									$kolvo = $kolvo + $mc['kolvo_' . $razmer[$i2]];
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
							<div id = "summ_<?= $i ?>">
								<?php $res = get_price(getAkciyaPrice($shop)) * $kolvo;
								$full_price = $full_price + $res;
								echo $res; ?>&nbsp;<?= $currency ?><br/>
								<?php
								$currensy_grn = $this->model_options->getOption('usd_to_uah');
								$price = $res * $currensy_grn;
								echo $price . ' грн<br />';
								$currensy_rub = $this->model_options->getOption('usd_to_rur');
								$price = $res * $currensy_rub;
								echo $price . ' р';
								?>
							</div>
						</td>
						<td>
							<a class = "mycart_del" href = "<?= $_SERVER['REQUEST_URI'] ?>?del_shop_id=<?= $mc['shop_id'] ?>" onclick = "return confirm('Вы действительно хотите удалить?');"></a>
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
		<div class = "full-price">
			<p class = "coupon">Купон:
				<input type = "text" name = "coupon" placeholder = "Код купона" value = "<?php if (isset($coupon['code']))
					echo $coupon['code']; ?>"/>
			</p>

			<?php
			$full_price_discount = $full_price;
			if (isset($coupon) && !isset($coupon['err'])) {
				$discount = $coupon['discount'];
				$res = $full_price / 100 * $discount;
				$full_price_discount = $full_price - $res;
			}
			?>


			<ul>
				<li>Товаров в корзине на сумму:</li>
				<li><span><?= $full_price ?></span>&nbsp;<?= $currency ?>&nbsp;/</li>
				<li><span><?= ($full_price * $currensy_grn) ?></span>&nbsp;грн.&nbsp;/</li>
				<li><span><?= ($full_price * $currensy_rub) ?></span>&nbsp;р</li>
			</ul>


			<ul>
				<li>Со скидкой:</li>
				<li><span><?= $full_price_discount ?></span>&nbsp;<?= $currency ?>&nbsp;/</li>
				<li><span><?= ($full_price_discount * $currensy_grn) ?>&nbsp;</span>грн.&nbsp;/</li>
				<li><span><?= ($full_price_discount * $currensy_rub) ?></span>&nbsp;р</li>
			</ul>


		</div>
		<input class = "basket-but" type = "submit" value = "Пересчитать" name = "resumm"/>

	</div>


</form>
<?php
if (isset($coupon['err'])) {
	echo '<div class="coupon-err">' . $coupon['err'] . '</div>';
}
?>
<?php
}
else {
	echo '<div style="text-align:center;"><img src="/images/empty_korz.png"/></div><div style="text-align:center;margin-top:30px;">Ваша корзина пуста...<br/><br/>Перейдите <a href="/">в каталог</a> и выберите товар! </div>';
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
<div id = "result"></div>

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
<section class = "container basket">
	<?php
	if (!userdata('login')) {
		?>
		<div id = "soc-login" class = "soc-login">
			<h3 class = "cart_h3">Представьтесь, пожалуйста:</h3>

			<div id = "uLoginde88b987" data-ulogin = "display=panel;fields=first_name,last_name,email,photo;optional=bdate,city,country,phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,google,yandex,facebook;hidden=twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=//<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
			<div class = "cart-no-register">Вы сможете отслеживать состояние своих заказов,<br/> первыми узнавать о новинках и акциях!<br/><a id = "no-register" style = "cursor:pointer;" onclick = "no_register()">Оформить заказ без регистрации</a>
			</div>
		</div>
	<?php

	}
	?>
	<script type = "text/javascript">
		function no_register() {
			$("#cart-adress").show();
			$("#soc-login").hide();
		}
	</script>
	<div id = "cart-adress" class = "cart-adress"<?php if (!userdata('login'))
		echo ' style="display:none"'; ?>>
		<form class = "address-form" method = "post" action = "<?= $_SERVER['REQUEST_URI'] ?>">
			<h2>Адрес доставки</h2>
			<input type = "hidden" name = "action" value = "order"/>

			<div class = "form-info">

				<div class = "form-group">
					<label>Ваше имя:</label>
					<input type = "text" name = "name" required = "required" value = "<?php if ($user)
						echo $user['name'] ?>"/>

				</div>
				<div class = "form-group">
					<label>E-mail:</label>
					<input type = "email" name = "email" required = "true" value = "<?php if ($user)
						echo $user['email'] ?>"/>
				</div>
				<?php
				if (!$user) {
					?>

					Пароль *:
					<input id = "pass1" type = "password" name = "pass" value = ""/>
					Повтор пароля *:

					<input id = "pass2" type = "password" name = "pass2" value = ""/>
					<script>
						$("#pass1").keyup(function () {
							var valueX = $("#pass1").val();
							var valueY = $("#pass2").val();
							if (valueX != valueY) {
								$("#ispass").html("<span style='color: red'>Пароли не совпадают!</span>");
							}
							else {
								$("#ispass").html("<span style='color: green'>Ок!</span>");
							}
						});
						$("#pass2").keyup(function () {
							var valueX = $("#pass1").val();
							var valueY = $("#pass2").val();
							if (valueX != valueY) {
								$("#ispass").html("<span style='color: red'>Пароли не совпадают!</span>");
							}
							else {
								$("#ispass").html("<span style='color: green'>Ок!</span>");
							}
						});
					</script>

					<div id = "ispass"></div>

				<?php
				}
				?>
				<div class = "form-group">
					<label>Телефон:</label>
					<input type = "text" name = "tel" required value = "<?php if ($user)
						echo $user['tel'] ?>"/>
				</div>
				<div class = "form-group">
					<label>Страна</label>
					<input type = "text" name = "country" required value = "<?php if ($user)
						echo $user['country'] ?>"/>
				</div>

			</div>
			<div class = "form-info">

				<div class = "form-group">
					<label>Город:</label>
					<input type = "text" name = "city" required value = "<?php if ($user)
						echo $user['city'] ?>"/>
				</div>
				<div class = "form-group">
					<label>Адрес:</label>
					<input type = "text" name = "adress" required value = "<?php if ($user)
						echo $user['adress'] ?>"/>
				</div>
				<div class = "form-group">
					<label>Ваше сообщение</label>
					<textarea name = "message" placeholder = ""></textarea>
				</div>
			</div>
			<div class = "form-info">
				<h2>Способ доставки</h2>
				<ul>
					<li>
						<label>Курьером:</label>
						<input type = "radio" name = "name" type = "text" placeholder = "">
					</li>
					<li>
						<label>Почтой:</label>
						<input type = "radio" name = "name" type = "text" placeholder = "">
					</li>
				</ul>

			</div>

			<div class = "form-info">
				<h2>Способ оплаты</h2>
				<ul>
					<!--li>
						<?php
					$privat24 = $this->model_options->getOption('privat24');
					if ($privat24 == 1) {
						?>
							<label>Privat24:</label>
							<input type = "radio" name = "name" type = "text" placeholder = "">
						<?php
					}
					?>
					</li>
					<li>
						<?php
					$liqpay = $this->model_options->getOption('liqpay');
					if ($liqpay == 1) {
						?>
							<label>Liqpay:</label>
							<input type = "radio" name = "name" type = "text" placeholder = "">
						<?php
					}
					?>
					</li-->
					<li>
						<label>Наличными:</label>
						<input type = "radio" name = "name" type = "text" placeholder = "">
					</li>
					<li>
						<label> Выбор валюты:</label>
						<select required name = "currency">
							<option></option>
							<option value = "usd" selected>Долар</option>
							<option value = "uah">Гривна</option>
							<option value = "rub">Рубль</option>
						</select>
					</li>
				</ul>

			</div>
			<div class = "form-info">
				<input type = "submit" value = "оформить заказ">
			</div>
		</form>

		<?php
		}
		?>


		<a onclick = "history.back(); return false;" style = "cursor: pointer">Вернуться назад</a>
		<!--div><span class="rel2">Наложенный платёж</span><input required type="radio" name="payment" value="Наложенный платёж"></div-->


	</div>
</section>




<?php include("application/views/footer_new.php"); ?>