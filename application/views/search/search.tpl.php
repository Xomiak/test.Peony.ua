<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>
<?php
$autoload = true;
$category['type'] = 'search';
$category['id']	= -2;
$category['name'] = 'Поиск';
?>
<!-- main page - start -->
<div class = "container">
	<?php include("application/views/shop_leftside.php"); ?>

	<div class = "catalog-cont">
		<div class="breadcrumbs">
			<div xmlns:v="http://rdf.data-vocabulary.org/#">
				<span typeof="v:Breadcrumb">
					<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
				</span>&nbsp;-&nbsp;
				<span typeof="v:Breadcrumb">
					<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/search/">Поиск</a>
				</span>
			</div>
		</div>

		<h1>Поиск по запросу "<?=userdata('search')?>"</h1>
		<?php
		if(!$articles)
		{
			?>
			<p>По Вашему запросу ничего не найдено, возможно вам будут интересны следующие новости:</p>
			<?php
			$this->db->where('active', 1);
			$this->db->order_by('id', 'DESC');
			$this->db->limit(7);
			$articles = $this->db->get('articles')->result_array();
			if($articles)
			{
				$count = count($articles);
				for($i = 0; $i < $count; $i++)
				{
					$a = $articles[$i];
					$this->db->where('id', $a['category_id']);
					$this->db->where('active', 1);
					$this->db->limit(1);
					$cat = $this->db->get('categories')->result_array();
					if($cat)
					{
						$cat = $cat[0];
						?>
						<p class="news_poisk"><a href="/<?=$cat['url']?>/<?=$a['url']?>/"><span><?=$a['name']?><span></a></p>
						<?php
					}
				}
			}			
		}
		else
		{
			?>
			<div id="articles">
			<?php
			if(isset($articles) && $articles !== false)
			{
				$count = count($articles);
				for($i = 0; $i < $count; $i++)
				{
					$art = $articles[$i];
					if($art['active'] != 0)
						echo getProductHtml($art);
				}
			}
			?>
			</div>
		<?php
		}
		?>

		</div>

		</div>


		
		<div class="clear"></div>
		</div></div>

<?php include("application/views/footer_new.php"); ?>