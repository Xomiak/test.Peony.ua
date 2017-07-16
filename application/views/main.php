<?php include("application/views/header_new.php"); ?>

<!-- main page - start -->

<?php
showSlider('new2');
?>

<div class = "container-fluid bg-white">
<div class = "container">
<ul class = "tabs">
    <li class = "tab-link current" data-tab = "tab-1">Новинки</li>
    <li class = "tab-link" data-tab = "tab-2"><?php if($isActionNow) echo 'акционный товар'; else echo "Хит продаж";?></li>
    <li class = "tab-link" data-tab = "tab-3">sale</li>
    <a href="/all/"><li class = "tab-link">вся коллекция</li></a>
</ul>

<div id = "tab-1" class = "tab-content current">
    <?php
    if($latest)
    {
    	$count = count($latest);
		if($count > 9) $count = 9;
    	for($i = 0; $i < $count; $i++)
    	{
    		$p = $latest[$i];
    		// Показываем товар (находится в shop_helper.php)
    		echo getProductHtml($p);
    	}
    }
    ?>
    <div class = "product-itm">
        <a href = "/novinki/"><img alt="Перейти в раздел" src = "/img/more.png"></a>

    </div>
</div>
<div id = "tab-2" class = "tab-content">
	<?php
    if($tab2)
    {
    	$count = count($tab2);
		if($count > 9) $count = 9;
    	for($i = 0; $i < $count; $i++)
    	{
    		$p = $tab2[$i];
    		// Показываем товар (находится в shop_helper.php)
    		echo getProductHtml($p,false,true);
    	}
    }
    ?>  
    <div class = "product-itm">
    	<?php
    	if($isActionNow)
    	{
    		?><a href = "/actions/"><img alt="Перейти в раздел" src = "/img/more.png"></a><?php
    	}
    	else
    	{
    		?><a href = "/vibor-redakcii/"><img alt="Перейти в раздел" src = "/img/more.png"></a><?php
    	}
    	?>
        
    </div>
</div>
<div id = "tab-3" class = "tab-content">
	<?php
    if(isset($sale) && $sale !== false)
    {
    	$count = count($sale);
		if($count > 9) $count = 9;
    	for($i = 0; $i < $count; $i++)
    	{
    		$p = $sale[$i];
    		// Показываем товар (находится в shop_helper.php)
    		echo getProductHtml($p);
    	}
    }
    ?>
    
    <div class = "product-itm">
        <a href = "/sale/"><img alt="Перейти в раздел" alt="перейти в раздел" src = "/img/more.png"></a>

    </div>
</div>
</div>
</div>
<div class = "container-fluid paralax-bg">
    <div class = "container">
        <span class="paralax-title">Почему мы?</span>

        <p>Мы – украинский производитель платьев номер один, и мы всегда заботимся о том, чтобы наша одежда была удобной и долговечной. Мы можем гарантировать высочайшее качество своей продукции. Решив купить платья оптом у нас, вы получите партию добротной одежды.</p>

        <p>Ткани и фурнитура</p>

        <p>На наших фабриках используется высококлассный трикотаж, что позволяет готовым изделиям сохранять свои формы и цвет. А современная прочная фурнитура – это еще один довод в пользу удобности вещей Пиони.
        </p>
    </div>
    <div class = "paralax"></div>
</div>

<div class = "container-fluid bg-white all-baners">
    <div class = "container baners">
		<a href = "/sale/" class="first-baner">
			<img alt="Sale - перейти в раздел" src = "/img/baner1.jpg">
		</a>
		<a href = "/dropshipping/" class="second-baner">
			<img alt="Условия для дропшиппинга" src = "/img/baner2.jpg">
		</a>
		<a href = "" class="third-baner">
			<!--img src = "img/baner1.jpg"-->
		</a>
		<a href = "" class="fourth-baner">
			<!--img src = "img/baner1.jpg"-->
		</a>

    </div>

</div>


<?php

$category['seo'] = $col1;
if($category['seo'] != '')
{
	$seo = '<div class = "container-fluid bg-white"><div class = "container"><div class="xview-source" style="background-color: #fff">';

	$pos = strpos($category['seo'],'</p>');
	if($pos)
	{
		$pos = $pos + 4;
		$first = substr($category['seo'],0, $pos);
		$end = substr($category['seo'], $pos);
		$seo .= $first;
		$seo .= '<a id="description-show" class="xnocookies" style="font-size:11px;color:black;cursor:pointer">Читать дальше</a>';
		$seo .= '<div class="xhideme xnocookies" style="display: block;">';
		$seo .= $end;
		$seo .= '</div></div></div></div>';

		echo $seo;
	}

	//if($page_number == 1) echo '<div class="description">'.$category['seo'].'</div>';
}

?>
<!-- main page - end -->

<?php
if(userdata('message') !== false)
{
    showMessage(userdata('message'));
    unset_userdata('message');
}

?>

<?php include("application/views/footer_new.php"); ?>