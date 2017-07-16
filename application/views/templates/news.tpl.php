<?php //include("application/views/header_new.php"); ?>
<?php
$autoload = true;
?>
<!-- main page - start -->

<section class = "container news-list">
	<div class="breadcrumbs">
		<div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
			</span>&nbsp;-&nbsp;
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/"><?=$category['name']?></a>
			</span>
		</div>
	</div>
	<div class="news-container">
    <h1><?=$category['name']?></h1>
	<div  id="articles">
	    <?php
		if(isset($articles) && $articles !== false)
		{
			$count = count($articles);
			for($i = 0; $i < $count; $i++)
			{
				echo getArticleHtml($articles[$i]);
			}
		}
		?>
	</div>
	<?php
	if(check_smartphone())
	{
		?>
		<!-- <div class="pagination">
			<div class="pager"><?=$pager?></div>
		</div> -->
		<?php
	}
	?>

</div>
</section>

<!-- main page - end -->

<?php include("application/views/footer_new.php"); ?>
