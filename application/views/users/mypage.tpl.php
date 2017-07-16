<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>

<section class = "container user-date">
	<div class = "breadcrumbs">
		<div xmlns:v = "http://rdf.data-vocabulary.org/#">
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>&nbsp;-&nbsp;
			Личный кабинет
		</div>
	</div>

	<h1>Личный кабинет</h1>
	<?php
	if($user['email'] == NULL) echo '<div class="msg">Внимание! У Вас не заполнено поле e-mail! Вам необходимо указать его, чтобы получать уведомления!</div><br />';
	?>
	<ul>
		<li><span>Фамилия и имя:</span><?= $user['name'] ?> <?=$user['lastname']?></li>
		<li><span>дата рождения:</span><?= $user['bd_date'] ?></li>
		<li><span>телефон:</span><?= $user['tel'] ?></li>
		<li><span>e-mail:</span><?= $user['email'] ?></li>
		<li><span>страна:</span><?= $user['country'] ?></li>
		<li><span>город:</span><?= $user['city'] ?></li>
		<li><span>адрес:</span><?= $user['adress'] ?></li>
	</ul>

	<?php
	if ($user['login'] == $this->session->userdata('login')) {
		?>
		<a rel = "nofollow" href = "/user/edit-mypage/">Редактировать мои данные</a><br/>
	<?php
	}

	if(userdata('type') == '11'){
		include("application/views/users/dropship.inc.php");
	}

	if(isset($orders))
	{
		include("application/views/shop/my_orders_history.tpl.php");
	}
	// if (isset($my_cart_and_orders) && !empty($my_cart_and_orders)) {
	// 	include("application/views/mod/mycart_table.mod.php");
	// } else {
	// 	echo '<center>Ваша корзина пуста</center>';
	// }
	?>

</section>
<?php include("application/views/footer_new.php"); ?>
