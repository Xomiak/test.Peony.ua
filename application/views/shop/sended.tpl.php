<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>
    <!--==============================content================================-->

    <section class = "container news-list">
    <div class="breadcrumbs">
        <div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
			</span>/
            Заказ оформлен успешно!
        </div>
    </div>
    <h1 class="pages"><?=$h1?></h1>
    <article class="article-content"><?=$msg?></article>
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
    </section>
<?php include("application/views/footer_new.php"); ?>
