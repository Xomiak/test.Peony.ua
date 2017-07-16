<?php //include("application/views/header_new.php"); ?>
    <div class="container news-list">
        <div class="breadcrumbs">
            <div xmlns:v="http://rdf.data-vocabulary.org/#">
				<span typeof="v:Breadcrumb">
					<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
				</span>
                &nbsp;-&nbsp;
                <?php
                $catsarr = array();
                $ccount = 0;
                $catsarr[$ccount] = $category;

                while($catsarr[$ccount]['parent'] != 0)
                {
                    $catsarr[$ccount+1] = $this->cat->getCategoryById($catsarr[$ccount]['parent']);
                    $ccount++;
                }

                $catsarr = array_reverse($catsarr);

                $ccount = count($catsarr);
                $url = '';
                for($i = 0; $i < $ccount; $i++)
                {
                    $url .= $catsarr[$i]['url'].'/';
                    ?>
                    <span typeof="v:Breadcrumb">
						<a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$url?>"><?=$catsarr[$i]['name']?></a>
					</span>
                    &nbsp;-&nbsp;
                    <?php
                }
                ?>
                <?=$article['name']?>
            </div>
        </div>
        <div class="news-container" itemscope itemtype="http://schema.org/Article">
            <meta itemprop="about" content="<?=$category['name']?>" />
            <meta itemprop="author" content="<?=$article['login']?>" />
            <meta itemprop="datePublished" content="<?=$article['date']?> <?=$article['time']?>" />

            <h1 itemprop="name"><?=$article['h1']?><?php if (isClientAdmin()) echo '<a href="/admin/articles/edit/'.$article['id'].'/" rel="nofollow"><img src="/img/edit.png" border="0" title="Перейти к редактированию" /></a>';?></h1>


            <?php
//            if($article['image'] != '')
//            {
//                echo '<img itemprop="image" src="'.$article['image'].'" alt="'.$article['name'].'" title="'.$article['name'].'" style="width:100%" />';
//
//            }
            ?>

            <?=$article['content']?>
            <div style="clear: both; width: 100%">&nbsp;</div>

            <?php
            $y = $article['youtube'];
            if($article['youtube'] != '')
            {
                $pos = strpos($y,'v=');
                if($pos)
                {
                    $pos = $pos + 2;
                    $end = strpos($y,'&',$pos);
                    $y = substr($y,$pos,$end-$pos);
                }
                if($y != '')
                    echo '<div class="youtube"><iframe width="400" height="280" src="http://www.youtube.com/embed/'.$y.'" frameborder="0" allowfullscreen></iframe></div><br />';
            }


            ?>

            <?php

            // Проверка на вывод кнопок соц. сетей

            //	$social_buttons = 0;
            //	if(isset($article['social_buttons']) && $article['social_buttons'] !== false) $social_buttons = $article['social_buttons'];
            //	if($social_buttons)
            //	{
            ?>
            <!-- КНОПКИ СОЦИАЛОК -->
            <script type="text/javascript">(function(w,doc) {
                    if (!w.__utlWdgt ) {
                        w.__utlWdgt = true;
                        var d = doc, s = d.createElement('script'), g = 'getElementsByTagName';
                        s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
                        s.src = ('https:' == w.location.protocol ? 'https' : 'http')  + '://w.uptolike.com/widgets/v1/uptolike.js';
                        var h=d[g]('body')[0];
                        h.appendChild(s);
                    }})(window,document);
            </script>
            <div data-share-size="30" data-like-text-enable="false" data-background-alpha="0.0" data-pid="1294091" data-mode="share" data-background-color="ededed" data-share-shape="round-rectangle" data-icon-color="ffffff" data-share-counter-size="11" data-text-color="000000" data-buttons-color="ff9300" data-counter-background-color="ffffff" data-follow-ok="group/52128400343108" data-share-counter-type="common" data-orientation="horizontal" data-following-enable="true" data-sn-ids="fb.tw.ok.vk.gp." data-selection-enable="true" data-share-style="1" data-follow-vk="peony_shop" data-counter-background-alpha="1.0" data-top-button="false" data-follow-fb="Peony.Shop.Ukraine" class="uptolike-buttons" ></div>
            <!-- //КОНКИ СОЦИАЛОК -->
            <?php
            //	}
            ?>
            <?php
            if(isset($images) && count($images) > 0)
            {
                echo '<div class="article_images">';
                $count = count($images);
                ?>
                <table align="center">

                    <?php
                    for($i = 0; $i < $count; $i++)
                    {

                        if($i == 0 || $i%4 == 0)
                            echo '<tr>';
                        ?>
                        <td align="center">
                            <a rel="lightbox[images]" href="<?=$images[$i]['image']?>">
                                <img src="<?php echo CreateThumb(200, 200, $images[$i]['image'], 'news_images') ?>" alt="<?=$article['name']?> - Фото №<?=($i+1)?>" title="<?=$article['name']?> - Фото №<?=($i+1)?>" border="0" />
                            </a>
                        </td>
                        <?php
                        if(($i+1) == $count || ($i+1)%4 == 0)
                            echo '</tr>';

                    }
                    ?>
                </table>
                <?php
                echo '</div>';
            }
            ?>

        </div>


    </div>





<?php include("application/views/footer_new.php") ?>