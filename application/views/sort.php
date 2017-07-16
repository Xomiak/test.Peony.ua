<!-- start filter and pager-->
<?php
$sort = false;
if($this->session->userdata('sort') !== false)
{
	$sort = $this->session->userdata('sort');
}
?>
<div class = "catalog-filter">
    <div class = "sort">
        <span>Сортировать по:</span>

        <form method = "post">
            <select name = "sort" onchange="this.form.submit()">
                <option value="num"<?php if($sort == "num") echo ' selected'; ?>>Новинки</option>
                <option value="price_min_max"<?php if($sort == "price_min_max") echo ' selected'; ?>>Цене (возр)</option>
				<option value="price_max_min"<?php if($sort == "price_max_min") echo ' selected'; ?>>Цене (убыв)</option>
            </select>
        </form>
    </div>

    <?php
    if(check_smartphone())
    {
        ?>
        <!-- <div class = "count">
            <span>Показать товаров:</span>

            <form method = "post">
                <select name = "per_page" onchange="this.form.submit()">
                    <option<?php if($per_page == '12') echo " selected"; ?> value = "12">12</option>
                    <option<?php if($per_page == '24') echo " selected"; ?> value = "24">24</option>
                    <option<?php if($per_page == '48') echo " selected"; ?> value = "48">48</option>
                    <option<?php if($per_page == '9999') echo " selected"; ?> value = "all">Все</option>
                </select>
            </form>
        </div> -->
        <?php
    }
    ?>

</div>
<?php
if(check_smartphone())
{
?>
<div class = "pagination">
 	<?php
	if($per_page == 9999) $per_page = $total_rows;
	?>
    <!-- p class="count-itm">Показано товаров <span><?=$per_page?></span> из <span><?=$total_rows?></span></p>
    <div class="pager">
    	<?=$pager?>        
    </div> -->
</div>
<?php
}
?>
<!-- END filter and pager-->