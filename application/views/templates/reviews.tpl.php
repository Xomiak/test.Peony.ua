<?php //include("application/views/header_new.php"); ?>
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
        <?php
        $content = shortCodes($page['content']);


        ?>
        <?=$content?>
    <section class = "container reviews">
        <ul id="articles">
        <?php
        $pager = 20;
        $comments = $this->comments->getComments($pager,0, 'id','DESC',1);
        //vd($comments);
        if($comments){
            foreach($comments as $r){
                echo getReview($r);
            }
        }
        ?>
            </ul>
</section>
    </section>
<?php include("application/views/footer_new.php"); ?>