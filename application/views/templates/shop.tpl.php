<?php //include("application/views/header_new.php"); ?>
<?php
$autoload = true;

?>


    <!-- main page - start -->
    <div class="container">

        <?php include("application/views/shop_leftside.php"); ?>

        <div class="catalog-cont">

            <div class="breadcrumbs">
                <div xmlns:v="http://rdf.data-vocabulary.org/#">
				<span typeof="v:Breadcrumb">
					<a property="v:title" rel="v:url" href="http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
				</span>&nbsp;-&nbsp;
                    <?php if (isset($razmer) && $razmer !== false) { ?>
                        <span typeof="v:Breadcrumb">
						<a property="v:title" rel="v:url"
                           href="http://<?= $_SERVER['SERVER_NAME'] ?>/<?= $category['url'] ?>/"><?= $category['name'] ?></a>
					</span>&nbsp;-&nbsp;
                        <?= $razmer ?> размер
                    <?php } else { ?>
                        <?= $category['name'] ?>
                    <?php } ?>
                </div>
            </div>

            <h1><?= $category['h1'] ?></h1>

            <?php include("application/views/sort.php"); ?>
            
            <div id="articles">
                <?php
                $is_empty = false;
                if (isset($articles) && $articles !== false) {
                    $count = count($articles);
                    for ($i = 0; $i < $count; $i++) {
                        $art = $articles[$i];
                        echo getProductHtml($art, $category);
                    }
                } else {
                    echo 'В данном разделе пока пусто...';
                    $is_empty = true;
                }
                ?>
            </div>
            <div style="clear:both"></div>
            <!-- <button id="more">Дальше</button> -->
            <?php if (!$is_empty) include("application/views/sort.php"); ?>


            <?php
            if ($category['seo'] != '') {
                $seo = '<div class="xview-source">';

                $pos = strpos($category['seo'], '</p>');
                if ($pos) {
                    $pos = $pos + 4;
                    $first = substr($category['seo'], 0, $pos);
                    $end = substr($category['seo'], $pos);
                    $seo .= $first;
                    $seo .= '<a class="xnocookies" style="font-size:11px;color:black;cursor:pointer">Читать дальше</a>';
                    $seo .= '<div class="xhideme xnocookies" style="display: block;">';
                    $seo .= $end;
                    $seo .= '</div></div>';

                    echo $seo;
                }

                //if($page_number == 1) echo '<div class="description">'.$category['seo'].'</div>';
            }

            ?>


        </div>
    </div>

<?php include("application/views/footer_new.php"); ?>