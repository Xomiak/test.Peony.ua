<?php include("application/views/header.php"); ?>



	<div id="page_wrap">
		
		<div class="shop_left_sidebar">
		<?php showTopMenu2(); ?>
		</div>

			<?php //include("application/views/shop_leftside.php"); ?>

			
			
		<div class="page_right_sidebar" >
		<div class="breadcrumbs">
				<div xmlns:v="http://rdf.data-vocabulary.org/#">
				<span typeof="v:Breadcrumb">
					<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
				</span>
                                &nbsp;-&nbsp;
                                <?=$category['name']?>
			</div>
		</div>
		
		<h1><?=$category['title']?></h1>
		
		<?php include("application/views/sort.php"); ?>

		<div class="clear"></div>
		<div class="pager" style="margin: 0;margin-right: 10px;"><?=$pager?></div>
		<div class="clear"></div>
		<br /><br />

		<?php
		if(isset($articles) && $articles !== false)
		{	
			if($articles)
			{
				$count = count($articles);
				for($i = 0; $i < $count; $i++)
				{
					$art = $articles[$i];
					showProductInCategory($art, $i);
				}
			}
			else echo "На данный момент акций нет. Подписывайтесь на нашу рассылку, чтобы следить за новинками и акциями!";
		}

		?>
		<div class="clear"></div>
		<div class="pager" style="margin: 0;margin-right: 10px;"><?=$pager?></div>
		<div class="clear"></div>
		<br /><br />
		<?php
		if($page_number == 1) echo '<div class="description">'.$category['seo'].'</div>';
		?>
		
	<div class="clear"></div>	
</div>                                
</div>  
</div>    <div class="clear"></div>	                            	
<?php include("application/views/footer.php"); ?>