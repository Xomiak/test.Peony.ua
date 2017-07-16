<?php
if(isset($_GET['admin-panel-hide']))
{
	if($_GET['admin-panel-hide'] == 'show') unset_userdata('admin-panel-hide');
	elseif($_GET['admin-panel-hide'] == true) set_userdata('admin-panel-hide', true);
	else unset_userdata('admin-panel-hide');
	redirect($this->uri->uri_string());
}
if(userdata('admin-panel-hide') === false)
{

	$this->db->where('active', 0);
	$this->db->from('comments');
	$comments = $this->db->count_all_results();
	$this->db->where('viewed', 0);
	$this->db->from('orders');
	$orders = $this->db->count_all_results();

	?>
	
	<link rel="stylesheet" href="/css/admin-panel.css">

	<div class="admin-panel-white"></div>
	<div class="admin-panel">
	    <a target="_blank" href="/admin">Панель администратора</a> |
	    <?php
	    if(request_uri() == '/') echo ' <a target="_blank" href="/admin/banners/?type-slider">Слайдер</a> |';
	    elseif(isset($page)) echo '<a target="_blank" href="/admin/pages/edit/'.$page['id'].'">Редактировать страницу</a> | <a target="_blank" href="/admin/pages/add/">Создать страницу</a> | ';
	    elseif(isset($article)) 
	    {
	    	$type = 'articles';
	    	if($category['type'] != 'articles') $type = $category['type'];

	    	echo '<a target="_blank" href="/admin/'.$type.'/edit/'.$article['id'].'">Редактировать материал</a> | <a target="_blank" href="/admin/'.$type.'/add/">Добавить материал</a> | ';
	    }
	    elseif(isset($category)) 
		{
			$type = 'articles';
	    	if($category['type'] != 'articles') $type = $category['type'];

			echo '<a target="_blank" href="/admin/categories/edit/'.$category['id'].'">Редактировать раздел</a> | <a target="_blank" href="/admin/categories/add/">Создать раздел</a> | <a target="_blank" href="/admin/'.$type.'/add/?category_id='.$category['id'].'">Добавить материал</a> | ';
		}
	    elseif(isset($author)) echo '<a target="_blank" href="/admin/authors/edit/'.$author['id'].'">Редактировать автора</a> | ';
	    ?>
	    <a target="_blank" href="/admin/options/">Опции</a>

		



	    <div class="admin-panel-shop-statistics" style="display: inline;">
			<?php
			$ordersString = "0";
			if($orders > 0) $ordersString = '<span class="admin-panel-orders-count">'.$orders.'</span>';
			?>
			<a href="/admin/orders/" target="_blank">Заказы (<?=$ordersString?>)</a>

			<?php
			if($comments > 0)
			{
				$commentsString = "0";
				if($orders > 0) $commentsString = '<span class="admin-panel-orders-count">'.$comments.'</span>';
				?>
				<a href="/admin/comments/" target="_blank">Отзывы (<?=$commentsString?>)</a>
				<?php
			}
			?>
		</div>

		<a style="float: right" href="<?=$_SERVER['REQUEST_URI']?>?admin-panel-hide=true"><div style="display: inline;" class="admin-panel-hide" title="Спрятать панель администратора">спрятать панель админа</div></a>
	</div>
<?php
}
else
{
	?>
	
	<div class="hidden-admin-panel" style="position: fixed; right: 5px;top:0; background-color: black;" title="Показать панель администратора"><a href="<?=$_SERVER['REQUEST_URI']?>?admin-panel-hide=show">Показать панель админа</a></div>
	<?php
}
?>