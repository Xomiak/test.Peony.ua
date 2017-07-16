<?php include("application/views/head.php"); ?>
<?php include("application/views/header.php"); ?>

<div class="container">

    <div class="contant_wraper">
		<h1><?=$category['name']?></h1>
		<p class="photo_item_date"><?=$category['date']?></p>
        
        <?php
        
        
        
        if($images)
        {
           // var_dump($images);die();
            $count = count($images);
            $cols = 0;
            for($i = 0; $i < $count; $i++)
            {
                if($i == 0 || $i == 3 || $i == 6 || $i == 9 || $i == 12 || $i == 15 || $i == 18)
                {
                    ?>
                    <div class="gallery-block">
                    <?php
                }
                $img = $images[$i];
                ?>
                <a rel="lightbox[gallery]" href="/upload/gallery/categories/<?=$category['folder']?>/normal/<?=$img['image']?>"><img style="margin: 10px;margin-right: 23px;" alt="<?=$img['name']?>" title="<?=$img['name']?>" width="160px" height="120px" src="<?=CreateThumb(279, 175, '/upload/gallery/categories/'.$category['folder'].'/normal/'.$img['image'], 'gallery')?>" /></a>
                <?php
                if($i == 2 || $i == 5 || $i == 8 || $i == 11 || $i == 14 || $i == 17 || $i == 20)
                {
                    ?>
                    </div>
                    <?php
                }
		elseif(($i+1) == $count)
		{
		    ?>
		    </div>
		    <?php
		}
            }
        }
        ?>                                    
    <center></center>

            <div class="empty">

                <div class="gallery-pagination">
                    <?=$pager?>
                </div>

            </div>
</div>


		<?php include("application/views/menu_floating.tpl.php"); ?>
        <div class="clear"></div>





    </div>

<?php include("application/views/footer.php") ?>