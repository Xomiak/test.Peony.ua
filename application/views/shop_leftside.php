<div class = "category-cont">

	<span>Категории товара</span>
	<ul>
		<?php
		$this->db->where('active', 1);
		$this->db->where('type', 'shop');
		$this->db->order_by('num', 'ASC');
		$cats = $this->db->get('categories')->result_array();
		if($cats)
		{
			$count = count($cats);
			for($i = 0; $i < $count; $i++)
			{
				$cat = $cats[$i];
				?>
				<li class="<?php if(isset($category) && $category['id'] == $cat['id']) echo 'active_filter'; ?>"><a rel="nofollow" href="/<?=$cat['url']?>/"><?=$cat['name']?></a></li>
				<?php
			}
		}
		?>
	</ul>

	<?php
	if(isset($razmer)) {
		?>

		<span>Размер</span>
<?php
	if(isset($razmer) && isset($color) && isset($sostav)) {
		if ($razmer != false || $color != false || $sostav != false) {
			?>
			<a href="/<?= $category['url'] ?>/"><span
					style="font: 12px Tahoma;text-transform: uppercase;display: inline-block;margin-bottom:20px;border-bottom:1px dotted #ff2b2b; color: black">Сбросить фильтр <span
						class="reset_filtersX">х</span></span></a>
			<?php
		}
	}
	?>
		<ul>
			<?php
			$sizes = getOption('sizes');
			$params =  explode("|", $sizes);
			//$params = $this->db->get('razmer')->result_array();
			$count = count($params);
			for ($i = 0; $i < $count; $i++) {
				$param = $params[$i];
				?>
				<li class="<?php if ($razmer == $param) echo 'active_filter'; ?>"><a href="/<?= $category['url'] ?>/filter/razmer/<?= urlencode($param) ?>/"><?= $param?></a>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}
	?>
	<?=getBanners('left_top');?> 

	<h2 class="news-title-catalog">Новости компании</h2>
	<ul class="news-side-bar">
		<?php
		$this->db->where('active', 1);
		$this->db->where('category_id', 33);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(2);
		$news =  $this->db->get('articles')->result_array();
		if($news)
		{
			$count = count($news);
			for($i = 0; $i < $count; $i++)
			{
				$n = $news[$i];
				?>
				<a href="/our-news/<?=$n['url']?>/">
					<li class="left-side-new">
						<h3><?=$n['name']?></h3>
						<?php
						if($n['image'] != '')
							echo '<img src="'.CreateThumb(200,100,$n['image'],'our-news').'" alt="'.$n['name'].'" />';
						?>
						<div class="left-side-new-short"><?=strip_tags($n['short_content'])?></div>
					</li>
				</a>
				<?php
			}
		}
		?>
		<li class="left-side-all-news">
			<a href="/our-news/">Все новости >></a>
		</li>
	</ul>
	<br />
	<?php
	getBanners('left');
	?>
</div>