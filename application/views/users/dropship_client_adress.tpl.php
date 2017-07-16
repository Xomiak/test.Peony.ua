<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>

<section class = "container user-date">
    <h1><?=$h1?></h1>

    <section class = "container user-edit">
        <div class = "breadcrumbs">
            <div xmlns:v = "http://rdf.data-vocabulary.org/#">
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>&nbsp;-&nbsp;
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/user/mypage/">Личный кабинет</a>
			</span>&nbsp;-&nbsp;
                <?= $h1 ?>
            </div>
        </div>

        <form id = "form_edit_mypage" action = "<?=$_SERVER['REQUEST_URI']?>" method = "post">
            <input type = "hidden" name = "action" value = "<?=$action?>"/>
            <div class = "form-group">
                <label>ФИО:</label>
                <input type = "text" name = "name" value = "<?= ($client ? $client['name']: '') ?>"/>
            </div>

            <div class = "form-group">
                <label>Телефон:</label>
                <input type = "text" name = "tel" value = "<?= ($client ? $client['tel']: '') ?>"/>

            </div>

            <div class = "form-group">
                <label>Страна:</label>
                <input type = "text" name = "country" value = "<?= ($client ? $client['country']: '') ?>"/>

            </div>
            <div class = "form-group">
                <label>Город:</label>
                <input type = "text" name = "city" value = "<?= ($client ? $client['city']: '') ?>"/>

            </div>
            <div class = "form-group">
                <label>Отделение Новой Почты:</label>
                <input type = "text" name = "np" value = "<?= ($client ? $client['np']: '') ?>"/>

            </div>
            <div class = "form-group">
                <label>Адрес:</label>
                <input type = "text" name = "adress" value = "<?= ($client ? $client['adress']: '') ?>"/>

            </div>

            <div class = "form-group">
                <?php
                $btnValue = 'Добавить';
                if($action == 'edit') $btnValue = 'Сохранить';
                ?>
                <input type = "submit" value = "<?=$btnValue?>"/>
            </div>
        </form>
    </section>


</section>
<?php include("application/views/footer_new.php"); ?>
