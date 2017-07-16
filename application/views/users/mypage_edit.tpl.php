<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>
	<script>
		var pass_valid = true;
		var j = jQuery.noConflict();
		j(document).ready(function () {
			j('#form_edit_mypage').on('submit', function () {
				if (!pass_valid || ($("#pass1").val() != $("#pass2").val()))
					pass_valid = false;
				return pass_valid;
			});
		});
	</script>
	<section class = "container user-edit">

		<div class = "breadcrumbs">
			<div xmlns:v = "http://rdf.data-vocabulary.org/#">
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>&nbsp;-&nbsp;
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/user/mypage/">Личный кабинет</a>
			</span>&nbsp;-&nbsp;
				Редактирование моих данных
			</div>
		</div>

			<h2>Редактирование профиля пользователя <span><?= $user['name'] ?></span></h2>
			<form id = "form_edit_mypage" action = "/user/edit-mypage/" method = "post">
				<input type = "hidden" name = "save" value = "ok"/>
				<div class = "form-group">
					<label>Имя:</label>
					<input type = "text" name = "name" value = "<?= $user['name'] ?>"/>
				</div>
				<div class = "form-group">
					<label>Фамилия:</label>
					<input type = "text" name = "lastname" value = "<?= $user['lastname'] ?>"/>
				</div>
				<div class = "form-group">
					<label>Дата рождения:</label>
					<input type = "date" name = "bd_date" placeholder = "В формате: ГГГГ-ММ-ДД" value = "<?= $user['bd_date'] ?>"/>
				</div>
				<div class = "form-group">
					<label>Телефон:</label>
					<input type = "text" name = "tel" value = "<?= $user['tel'] ?>"/>

				</div>
				<div class = "form-group">
					<label>E-mail:</label>
					<input disabled required type = "text" name = "email" value = "<?= $user['email'] ?>"/>

				</div>
				<div class = "form-group">
					<label>Страна:</label>
					<input type = "text" name = "country" value = "<?= $user['country'] ?>"/>

				</div>
				<div class = "form-group">
					<label>Город:</label>
					<input type = "text" name = "city" value = "<?= $user['city'] ?>"/>

				</div>
				<div class = "form-group">
					<label>Адрес:</label>
					<input type = "text" name = "adress" value = "<?= $user['adress'] ?>"/>

				</div>
				<div class = "form-group">
					<input type = "checkbox" name = "mailer" <?php if ($user['mailer'] == 1) echo " checked"; ?> />
					<span>Подписка на новости и акции</span>
				</div>
				<div class = "form-group">

					<input type = "submit" value = "Сохранить"/>
				</div>
				</form>
	</section>
<?php include("application/views/footer_new.php"); ?>