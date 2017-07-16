<?php include("application/views/header_new.php"); ?>
<!--==============================content================================-->

<section class = "container news-list">
    <div class="breadcrumbs">
        <div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
			</span>/
            <?=$page['name']?>
        </div>
    </div>
    <h1 class="pages"><?=$page['name']?></h1>
    <article class="article-content">



	<div class="revslider" data-alias="new2"></div>
    </article>
</section>
<?php include("application/views/footer_new.php"); ?>


