<?php include("application/views/header.php");?>
<script type="text/javascript" src="/js/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-personalized-1.5.2.packed.js"></script>
<script type="text/javascript" src="/js/sprinkle.js"></script>

<?php
// ЕСТЬ ЛИ АКЦИЯ
if(isDiscount($article)) $article['discountNow'] = true;
?>

<div class="clear"></div>	
<div class="breadcrumbs">
	<div xmlns:v="http://rdf.data-vocabulary.org/#">
		<span typeof="v:Breadcrumb">
			<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
		</span>
		&nbsp;->&nbsp;

		<span typeof="v:Breadcrumb">
			<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/"><?=$category['name']?></a>
		</span>
		&nbsp;->&nbsp;



		<?=$article['name']?>
	</div>
</div>
<?php include("application/views/product.php"); ?>
<div class="ppoduct_right_sidebar">
	<h2 class="tovar"><span itemprop="name">
		<?=$article['h1']?>
		<?php if (isClientAdmin()) echo '<a href="/admin/shop/edit/'.$article['id'].'/" rel="nofollow"><img src="/img/edit.png" border="0" title="Перейти к редактированию" /></a>';?>
	</span>		
</h2>

<div class="content">	
	<?php
//vd($article['sale']);

	// ПОКАЗ ДРУГИХ ЦВЕТОВ, ЕСЛИ ОНИ ЕСТЬ
	$name = $article['name'];
	$pos = strpos($article['name'],'/');			
	if($pos !== false)
	{
		$name = substr($name, 0, $pos);				
	}

	$this->db->where('active', 1);
			//$this->db->where('id <>', $article['id']);
	$this->db->like('name', $name);
	$colors = $this->db->get('shop')->result_array();

	if($colors)
	{
		?>
		<div class="shop-colors">
			<?php
			$count = count($colors);
			for($i = 0; $i < $count; $i++)
			{
				$col = $colors[$i];
				$cat = $this->cat->getCategoryById($col['category_id']);
					// 55x80
				?>
				<div class="shop-color<?php if($col['id'] == $article['id']) echo ' shop-active'; ?>">
					<a href="/<?=$cat['url']?>/<?=$col['url']?>/"><img src="<?=CreateThumb(55,80,$col['image'],'shop_55x80')?>" alt="<?=$col['name']?>" title="<?=$col['name']?>" /></a>
				</div>
				<?php
			}
			?>
			<div class="clear"></div>
		</div>
		<?php
	}


	?>
	<div class="clear"></div>
	<div class="shop_part">
		<div class="shoppart_left">

			<form method="post" action="/add_to_cart/">
				<input type="hidden" name="shop_id" value="<?=$article['id']?>" />
				<input type="hidden" name="back" value="<?=$_SERVER['REQUEST_URI']?>" />

				<?php

				if(isset($article['discountNow']))
				{

					?>
					<?php
					if($article['akciya_start'] != '' && $article['akciya_end'])
					{
						?>
						<span class="action-time">
							Акция с <?=getWordDate($article['akciya_start'], false)?> по <?=getWordDate($article['akciya_end'])?>
						</span><br />
						<?php
					}
					?>

					<div class="old-price">





						<span>
							<?php

							echo $article['price'].' $';									


							echo ' / ';

							$currensy_grn = $this->model_options->getOption('usd_to_uah');
							echo ($article['price'] * $currensy_grn).' грн';

							echo ' / ';

							$currensy_rub = $this->model_options->getOption('usd_to_rur');
							echo ($article['price'] * $currensy_rub).' р.';

							?>
						</span>
					</div>
					<?php
				}

				?>

				<div class="price<?php if(isset($article['discountNow'])) echo '-discount'; ?>">
					<span>
						<?php
        				//vd($article['price']);
						if(isDiscount($article))
							$article['price'] = getNewPrice($article['price'], $article['discount']);

						echo $article['price'].' $';									


						echo ' / ';

						$currensy_grn = $this->model_options->getOption('usd_to_uah');
						echo ($article['price'] * $currensy_grn).' грн';

						echo ' / ';

						$currensy_rub = $this->model_options->getOption('usd_to_rur');
						echo ($article['price'] * $currensy_rub).' р.';

						?>
					</span>
				</div>

				<div class="sizeandnumber">
					<div class="size">
						Выберите размер:<br />
						<?php
						$razmer = explode('*',$article['razmer']);
						if(is_array($razmer))
						{
							$count = count($razmer);
							for($i = 0; $i < $count; $i++)
							{
								echo '<input required type="radio" name="razmer" value="'.$razmer[$i].'">'.$razmer[$i].'&nbsp;';
							}			
						}
						?>
					</div>
					<div class="number">
						<span>Кол-во (шт.):</span>&nbsp; &nbsp;<input required type="text" name="kolvo" value="1"/> <span class="minus"></span><span class="plus"></span>

					</div>
				</div>
				<div class="sendtocart">
					<a id="shop-<?=$article['id']?>"><div class="incart"><input type="submit" value="В корзину"/></div></a>
				</div>
			</form>
		</div>
		<div class="shoppart_right">


			<div class="us">
				<span class="us01"><img src="/images/us01.png"/></span><span class="us02"><a href="#openModal">Условия сотрудничества</a></span>

				<div id="openModal" class="modalDialog">
					<div>
						<a href="#close" title="Закрыть" class="close">X</a>
						<div id="text" style="height:400px; overflow:auto;">
							<?=$this->model_options->getOption('usloviya')?>
						</div>
					</div>
				</div>
			</div>



			<div class="us">
				<span class="us01"><img src="/images/us02.png"/></span><span class="us02"><a href="#openModal1">Как заказать</a></span>

				<div id="openModal1" class="modalDialog">
					<div>
						<a href="#close" title="Закрыть" class="close">X</a>
						<div id="text" style="height:400px; overflow:auto;">
							<?=$this->model_options->getOption('zakaz')?>
						</div>
					</div>
				</div>
			</div>


			<div class="us">
				<span class="us01"><img src="/images/us03.png"/></span><span class="us02"><a href="#openModal2">Оплата и доставка заказа</a></span>

				<div id="openModal2" class="modalDialog">
					<div>
						<a href="#close" title="Закрыть" class="close">X</a>
						<div id="text" style="height:400px; overflow:auto;">
							<?=$this->model_options->getOption('oplata')?>
						</div>
					</div>
				</div>
			</div>      
		</div>



	</div>
	<div id="tabvanilla" class="widget">

		<ul class="tabnav">
			<li style="margin:0;"><a href="#popular">Описание</a></li>
			<li><a href="#recent">Характеристики</a></li>
			<li><a href="#featured">Размерная сетка</a></li>				
			<li><a href="#reviews">Отзывы</a></li> 
		</ul>

		<div id="popular" class="tabdiv">
			<ul>
				<h1 class="shop_h1" style="font-size: 16px; font-weight: normal">
					<?php
					$h1 = "";
					if($category['name'] != 'Новинки' && $category['name'] != 'SALE' && $category['name'] != 'Выбор редакции' && $category['name'] != 'Вся коллекция')
						$h1 = 'Купить женские '.mb_strtolower($category['name']).' ';
					
					$h1 .= $article['name'];

					if($article['color'] != '')
						$h1.= ' "'.$article['color'].'"';

					if($category['name'] != 'Новинки' && $category['name'] != 'SALE' && $category['name'] != 'Выбор редакции' && $category['name'] != 'Вся коллекция')
						$h1 .= ' от производителя';

					if(strlen($h1) > 70) $h1 = str_replace('от производителя', 'оптом', $h1);
					if(strlen($h1) > 70) $h1 = str_replace('оптом', '', $h1);
					if(strlen($h1) > 70) $h1 = str_replace('Купить ж', 'Ж', $h1);
					if(strlen($h1) > 70) $h1 = str_replace("Женские ".mb_strtolower($category['name']), $category['name'], $h1);
					echo $h1;

					?>
					
				</h1>
				<div class="descr"  itemprop="description">
					<?=$article['content']?>
				</div>
			</ul>
		</div><!--/popular-->

		<div id="recent" class="tabdiv">
			<div class='harki'>РАЗМЕРНЫЙ РЯД</div>
			<?php 
			$razmer = explode('*', $article['razmer']);		
			if($razmer)
			{
				$count = count($razmer);
				for($i = 0; $i < $count; $i++)
				{
					$r = $razmer[$i];
					?>
					<span class="harki-param"><?=$r?></span>
					<?php
				}
			}
			?>
			<div class="clear"></div>
			<div class='harki'>ЦВЕТ</div>
			<span class="harki-param"><?=$article['color']?></span>

			<div class="clear"></div>
			<div class='harki'>СОСТАВ</div>
			<span class="harki-param"><?=$article['sostav']?></span>
			<div class="clear"></div>
		</div><!--/recent-->

		<div id="featured" class="tabdiv">
			<ul>
				<img class="shop_one_setka" stylE="width: 540px;" src="/images/setka1.jpg">
			</ul>
		</div><!--featured-->
		<div id="reviews" class="tabdiv">
			<div class="reviews">
				<?php 
				$this->load->helper('modules');


					addReviews($article);
					showReviews($article);

				?>
			</div>
		</div>


	</div>
	<div class="shop_knopki" style="display: none;">
		<a href="/usloviya-sotrudnichestva/">Условия сотрудничества</a><br/>
		<a href="/kak-zakazat/">Как заказать</a><br/>
		<a href="/oplata-i-dostavka/">Оплата и доставка заказа</a><br/>


	</div>






</div>
<div class="clear"></div>
<?php

		// Проверка на вывод кнопок соц. сетей

$social_buttons = 0;
if(isset($article['social_buttons']) && $article['social_buttons'] !== false) $social_buttons = $article['social_buttons'];
if($social_buttons)
{
	?>

	<?php
}
?>



</div>



<div class="clear"></div>
</div>
<div class="clear"></div>	


<?php
relatedProducts($category, $article); 
?>
<?php include("application/views/footer.php"); ?>