<?php include("application/views/head.php"); ?>
<?php include("application/views/header.php"); ?>

<div class="container">

    <div class="contant_wraper">


        <h1>Фотогалерея</h1>

    

        <div class="photo_main_wrap">

                
        <?php
        
        
        
        if($categories)
        {
           // var_dump($images);die();
            $count = count($categories);
            $cols = 0;
            for($i = 0; $i < $count; $i++)
            {

                $img = $categories[$i];
                $darr = explode('-',$img['date']);
                $date = '';
                if($darr)
                {
                    $date = $darr[2].'-'.$darr[1].'-'.$darr[0];
                    $img['date'] = $date;
                }
                ?>
                <div class="photo_item">
					<p class="photo_item_date1"><?=$img['date']?></p>
                    <p class="photo_item_text"><a href="/gallery/<?=$img['url']?>/"><?=$img['name']?></a></p>
                    <a href="/gallery/<?=$img['url']?>/"><img alt="<?=$img['name']?>" title="<?=$img['name']?>" width="160px" height="120px" src="<?=CreateThumb(279, 175, $img['image'], 'gallery')?>" /></a>
                    
                </div>
                <?php

            }
        }
        ?>                                    
        </div>

            <div class="bottom-wrap">

                <div class="gallery-pagination">
                    <?=$pager?>
                </div>

            </div>

        


        </div>
		<?php include("application/views/menu_floating.tpl.php"); ?>
<div class="clear"></div>
    </div>

<?php include("application/views/footer.php") ?>