<?php include("application/views/header2.php"); ?>



	<div>

			<?php include("application/views/menu_pages.php"); ?>

		<div class="page_right_sidebar_news">
				<br />
				<div class="breadcrumbs">
				<div xmlns:v="http://rdf.data-vocabulary.org/#">
				<span typeof="v:Breadcrumb">
					<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
				</span>
                                &nbsp;->&nbsp;
                                <?=$category['name']?>
			</div>
		</div>
		<h1 class="pages">
			<span><?=$category['name']?></span>
		</h1>	

		<?php
		if(isset($articles) && $articles !== false)
		{
			$count = count($articles);
			for($i = 0; $i < $count; $i++)
			{
				$art = $articles[$i];
				$darr = explode('-', $art['date']);
				if(($i%2) == 0) echo '';
				?>
				<div class="news-block-cat">
					<div class="news-title"><h2><a class="" href="/<?=$category['url']?>/<?=$art['url']?>/"><?=$art['name']?></a></h2></div>
						<div class="date"><?=$art['date']?></div>
					<?php
					if($art['image'] != '')
					{
						?>
						<div class="news-img">
							<a href="/<?=$category['url']?>/<?=$art['url']?>/">
								<img class="rukovoditeli" alt="<?=$art['name']?>" title="<?=$art['name']?>" src="<?=$art['image']?>" width="300px" height="210px" border="0" />
							</a>
						</div>
						<?php
					}
					elseif($art['youtube'] != '')
					{
						$y = $art['youtube'];
							$pos = strpos($y,'v=');
							if($pos)
							{
								$pos = $pos + 2;
								$end = strpos($y,'&',$pos);
								$y = substr($y,$pos,$end-$pos);
							}
							if($y != '')
							{
								?>
								<div class="news-img">
										<a href="/<?=$category['url']?>/<?=$art['url']?>/">
											<img class="rukovoditeli" alt="<?=$art['name']?>" title="<?=$art['name']?>" src="http://i4.ytimg.com/vi/<?=$y?>/default.jpg" width="300px" border="0" />
										</a>
								</div>
								<?php
							}
						

					}
					?>										
					<div class="news-cnt-wrap">					
					
					<div class="news-text">
						<?php
						//$val = mb_substr ($art['content'], 0, 50);
						$art['content'] = strip_tags($art['content'],'<p>');
						if(strlen($art['content']) > 120)
						{
							$pos = mb_strpos($art['content'], ' ', 120);
							//var_dump($pos);
							$val = mb_substr($art['content'],0,$pos);
							echo $val.' ...';
						}
						else echo $art['content'];
						?><br/><br/>
					<a class="dalee" href="/<?=$category['url']?>/<?=$art['url']?>/">ЧИТАТЬ ДАЛЬШЕ >></a>
					</div>	
					</div>
					
				</div>				
				<?php
				if(($i%2) == 1) echo '<div class="clear" ></div><hr/ style="width: 100%;">';
				//if(($i%2) == 1) echo '<div class="separator"><hr/ style="width:100%;"></div>';
			}
		}
		?>
		<div class="clear"></div>
		<div class="pager"><?=$pager?></div>
	<div class="clear"></div>	
</div>                                
</div>                                </div>    <div class="clear"></div>	                            	
<?php include("application/views/footer.php"); ?>
