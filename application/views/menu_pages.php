<style>
.cl-effect-17
	{
	margin-top: 41px;
	padding-left: 15px;
	margin-bottom: 15px;
	width: 225px;
	}
.cl-effect-17 a
	{
	margin-bottom: 5px;
	text-transform:uppercase;
	margin-right: 20px;
	font-size: 20px;
	//font-size: 19px;
	//color:red;
	}
.cl-effect-17  a.last
	{
	margin-right:0;
	}
nav a {
	position: relative;
	display: inline-block;
	outline: none;
	text-decoration: none;
	text-transform: uppercase;
	letter-spacing: 1px;
	font-weight: 400;
	text-shadow: 0 0 1px rgba(138,138,138,1);
	font-size: 1.35em;
	}

nav a:hover,
nav a:focus {
	outline: none;
}
/* Effect 17: move up fade out, push border */
.cl-effect-17 a {
	color: #8a8a8a;
	text-shadow: none;
	padding-left: 2px;
padding-right: 2px;
}

.cl-effect-17 a::before {
	color: #000;
	text-shadow: 0 0 1px rgba(138,138,138,1);
	content: attr(data-hover);
	position: absolute;
	-webkit-transition: -webkit-transform 0.3s, opacity 0.3s;
	-moz-transition: -moz-transform 0.3s, opacity 0.3s;
	transition: transform 0.3s, opacity 0.3s;
	pointer-events: none;
}

.cl-effect-17 a::after {
	content: '';
	position: absolute;
	left: 0;
	bottom: 0;
	width: 100%;
	height: 2px;
	background-color:#8a8a8a;
	opacity: 0;
	-webkit-transform: translateY(5px);
	-moz-transform: translateY(5px);
	transform: translateY(5px);
	-webkit-transition: -webkit-transform 0.3s, opacity 0.3s;
	-moz-transition: -moz-transform 0.3s, opacity 0.3s;
	transition: transform 0.3s, opacity 0.3s;
	pointer-events: none;
}

.cl-effect-17 a:hover::before,
.cl-effect-17 a:focus::before {
	opacity: 0;
	-webkit-transform: translateY(-2px);
	-moz-transform: translateY(-2px);
	transform: translateY(-2px);
}

.cl-effect-17 a:hover::after,
.cl-effect-17 a:focus::after {
	opacity: 1;
	-webkit-transform: translateY(0px);
	-moz-transform: translateY(0px);
	transform: translateY(0px);
}
.cl-effect-17 a.active 
	{
	color: white;
	background: black;
	}
.cl-effect-17 a.active::before
	{
	color:#fff;
	}
</style>

		<div class="page_left_sidebar_news">
			<?php //showTopMenu2(); ?>
			<hr/ style="width: 100%;margin-left: -1px;">
			<div class="clear"></div>
			<div class="left_sidebar_razdel">
				<?php
				$ni = false;
				if($this->uri->segment(1) != 'novosti-industrii' && $ni != false)
				{
				?>
				<div class="left_sidebar_razdel_news">
					<h2>Новости индустрии</h2>
				<?php
				$query = $this->db->query("SELECT * FROM `articles` WHERE image<>'' AND active=1 AND category_id=10 AND id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `articles` ) ORDER BY id LIMIT 1;")->result_array();
				if($query[0]) $new = $query[0];
				if($new)
				{
					$cat = $this->model_categories->getCategoryById($new['category_id']);
					
					$pos = mb_strpos($new['content'], ' ', 100);
					//var_dump($pos);
					$short_content = mb_substr($new['content'],0,$pos).' ...';
					
					?>
					<a href="/<?=$cat['url']?>/<?=$new['url']?>/"><h3><?=$new['name']?></h3></a>
					
					<a href="/<?=$cat['url']?>/<?=$new['url']?>/">
						<img src="<?=CreateThumb(200,145,$new['image'],'news_images')?>" alt="<?=$new['name']?>" title="<?=$new['name']?>" border="0" />
					</a>
					<div class="left-side-new-short-content"><?=$short_content?></div>
					
					<div class="read-more"><a href="/<?=$cat['url']?>/<?=$new['url']?>/"><span>Читать дальше >></span></a></div>
					<?php					
					//200x145
				}
				?>				
				</div>
				<hr style="margin-top: 5px;"/>
				<?php
				}
				?>
				
				
			<div class="left_sidebar_razdel_novinki">
                    
					<h2 style="margin-left:14px;">Новинки</h2>
					<?php
					
					
					$new = $this->model_shop->getArticlesByCategory(12,5,0,1);
					if($new)
					{
						shuffle($new);
						//var_dump($new);
						if(isset($new[0]))
						{
							$new = $new[0];
							$cat = $this->model_categories->getCategoryById(12);
						?>
			    
							<a class="adetails" href="/<?=$cat['url']?>/">
								<img src="<?=CreateThumb(225,330,$new['image'],'shop_leftsidebar_225x330')?>" alt="<?=$new['name']?>" title="<?=$new['name']?>" border="0" />
				    <div class="details1 details2">
	
									<p style="padding-top: 2px;">
										<span><?=$new['price']?> $ /</span>
									<span><?php
									$currensy_grn = $this->model_options->getOption('usd_to_uah');
									echo ($new['price'] * $currensy_grn);
									?> грн / 
									</span>
									<span><?php
									$currensy_rub = $this->model_options->getOption('usd_to_rur');
									echo ($new['price'] * $currensy_rub);
									?> руб
									</span>
					</p>
								</div>
							</a>
						<?php
						}
					}
					?>
				

			</div>
					
			<div class="left_sidebar_razdel_novinki">
                    
					<h2 style="margin-left:14px;">Вся коллекция</h2>
					<?php
					$this->db->where('active', 1);
					$this->db->order_by('id', 'DESC');
					$this->db->limit(10);
					$new = $this->db->get('shop')->result_array();
					if($new)
					{
						shuffle($new);
						if(isset($new[0]))
						{
							$new = $new[0];
							$cat = $this->model_categories->getCategoryById($new['category_id']);
						?>
			    
							<a class="adetails" href="/all/">
								<img src="<?=CreateThumb(225,330,$new['image'],'shop_leftsidebar_225x330')?>" alt="<?=$new['name']?>" title="<?=$new['name']?>" border="0" />
				    <div class="details1 details2">
	
									<p style="padding-top: 2px;">
										<span><?=$new['price']?> $ /</span>
									<span><?php
									$currensy_grn = $this->model_options->getOption('usd_to_uah');
									echo ($new['price'] * $currensy_grn);
									?> грн / 
									</span>
									<span><?php
									$currensy_rub = $this->model_options->getOption('usd_to_rur');
									echo ($new['price'] * $currensy_rub);
									?> руб
									</span>
					</p>
								</div>
							</a>
						<?php
						}
					}
					?>
				

			</div>
			
			<div class="left_sidebar_razdel_novinki">
                    
					<h2 style="margin-left:14px;">SALE</h2>
					<?php
					$new = $this->model_shop->getArticlesByCategory(19,5,0,1);
					if($new)
					{
						shuffle($new);
						if(isset($new[0]))
						{
							$new = $new[0];
							$cat = $this->model_categories->getCategoryById(19);
						?>
			    
							<a class="adetails" href="/<?=$cat['url']?>/">
								<img src="<?=CreateThumb(225,330,$new['image'],'shop_leftsidebar_225x330')?>" alt="<?=$new['name']?>" title="<?=$new['name']?>" border="0" />
				    <div class="details1 details2">
	
									<p style="padding-top: 2px;">
										<span><?=$new['price']?> $ /</span>
									<span><?php
									$currensy_grn = $this->model_options->getOption('usd_to_uah');
									echo ($new['price'] * $currensy_grn);
									?> грн / 
									</span>
									<span><?php
									$currensy_rub = $this->model_options->getOption('usd_to_rur');
									echo ($new['price'] * $currensy_rub);
									?> руб
									</span>
					</p>
								</div>
							</a>
						<?php
						}
					}
					?>
				

			</div>

		</div>
</div>

