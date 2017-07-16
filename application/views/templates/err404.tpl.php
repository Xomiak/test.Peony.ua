<?php
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
?>
<?php include("application/views/header_new.php"); ?>
<!--==============================content================================-->
		
	<div class="container">
<div class="category-cont">
	Популярные товары
</div>

		<div class="catalog-cont">
				<br />
				<div class="breadcrumbs">
						<div xmlns:v="http://rdf.data-vocabulary.org/#">
								 <span typeof="v:Breadcrumb">
									 <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
								 </span>

								 <?=$page['name']?>								 
						</div>
				</div>
				
		<h1 class="pages"><span><?=$page['name']?></span></h1>
		<?=$page['content']?>
	
		</div>

	</div>
		
</div>
<?php include("application/views/footer_new.php"); ?>