<?php //include("application/views/header_new.php"); ?>
	<!--==============================content================================-->

	<section class = "container contact-page">
		<div class="breadcrumbs">
			<div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/" style="color:#000;">Главная</a>
			</span>/
				<?=$page['name']?>
			</div>
		</div>

		<h1><?= $page['name'] ?></h1>

		<div class = "phone-number">
			<?= $page['content'] ?>
		</div>


		<?php
		if ($this->session->userdata('sended') == 'true') {
			?>
			<strong>Ваше письмо успешно отправлено!</strong>
		<?php
		$this->session->unset_userdata('sended');
		}
		else
		{
		?>

			<h2>Напишите нам письмо!</h2>

			<form id = "myform" method = "post" action = "<?= $_SERVER['REQUEST_URI'] ?>">

				<div class = "form-group">

					<label>Ваше имя</label>
					<input required type = "text" name = "name" id = "name" value = "<?php if (isset($_POST['name']))
						echo $_POST['name']; ?>"/>
					<?php
					if (isset($err['name'])) {
						?>
						<div class = "form-error"><?= $err['name'] ?></div>
					<?php
					}
					?>
				</div>

				<div class = "form-group">
					<label>Ваш город</label>
					<input required type = "text" name = "city" value = "<?php if (isset($_POST['city']))
						echo $_POST['city']; ?>"/>
					<?php
					if (isset($err['city'])) {
						?>
						<div class = "form-error"><?= $err['city'] ?></div>
					<?php
					}
					?>
				</div>

				<div class = "form-group">
					<label>Ваш e-mail</label>
					<input required type = "email" name = "email" value = "<?php if (isset($_POST['email']))
						echo $_POST['email']; ?>"/>
				</div>

				<div class = "form-group">
					<label>Тема</label>
					<input type = "text" name = "tema" id = "tema" value = "<?php if (isset($_POST['tema']))
						echo $_POST['tema']; ?>"/>
					<?php
					if (isset($err['tema'])) {
						?>
						<div class = "form-error"><?= $err['tema'] ?></div>
					<?php
					}
					?>
				</div>

				<div class = "form-group">
					<label>Ваше сообщение:</label>
					<textarea name = "message"></textarea>
					<?php
					if (isset($err['message'])) {
						?>
						<div class = "form-error"><?= $err['message'] ?></div>
					<?php
					}
					?>
				</div>

				<div class = "form-group">
					<label>Введите цифры с картинки</label>

					<div>
						<?= $cap['image'] ?>

						<input required type = "text" name = "captcha" value = ""/>
					</div>
					<?php
					if (isset($err['captcha'])) {
						?>
						<div class = "form-error"><?= $err['captcha'] ?></div>
					<?php
					}
					?>
				</div>
				<div class = "form-group">
					<input type = "submit" value = "ОТПРАВИТЬ"/>
				</div>

			</form>
			<script src = "http://code.jquery.com/jquery-1.9.1.min.js"></script>
			<script src = "http://jquery.bassistance.de/validate/jquery.validate.js"></script>
			<script src = "http://jquery.bassistance.de/validate/additional-methods.js"></script>
			<script>
				// just for the demos, avoids form submit
				jQuery.validator.setDefaults({
				});
				$("#myform").validate({
					rules: {
						name: {
							required: true,
							minlength: 3
						},
						city: {
							required: true,
							minlength: 3
						},
						email: {
							required: true,
							email: true
						},
						tema: {

						},
						message: {
							required: true,
							minlength: 10
						},
						captcha: {
							required: true,
							number: true,
							rangelength: [4, 4]
						}

					},
					messages: {
						name: {
							minlength: "Имя не может быть меньше {0} символов",
							required: "Введите Ваше имя"
						},
						city: {
							minlength: "Город не может быть меньше {0} символов",
							required: "Введите название Вашего города"
						},
						email: {
							email: "Неверный формат E-mail",
							required: "Введите E-mail"
						},
						tema: {

						},
						message: {
							minlength: "Сообщение не может быть меньше {0} символов",
							required: "Введите Ваше сообщение"
						},
						captcha: {
							rangelength: "Вам необходимо ввести {0} символа с картинки",
							required: "Введите число с картинки",
							number: "Значение должно быть цифровым"
						}
					}
				});
			</script>
		<?php
		}
		?>
		</div>


	</section>
<?php include("application/views/footer_new.php"); ?>