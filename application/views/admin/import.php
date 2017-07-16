<?php
$show_only = false;
if (userdata('category_id') !== false)
    $show_only = userdata('category_id');
include("header.php");
?>
    <script>
        // var j = jQuery.noConflict();

        $(document).ready(function () {
            $('.view-source .hideme').hide(500);
            $('.view-source a.nocookies').toggle(
                function () {
                    $(this).siblings('.hideme').stop(false, true).slideDown(500);
                    $(this).html('Спрятать');
                },
                function () {
                    $(this).siblings('.hideme').stop(false, true).slideUp(500);
                    $(this).html('Показать');
                }
            );
        });

    </script>

    <table width="100%" height="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("menu.php"); ?></td>
            <td width="20px"></td>
            <td valign="top">
                <div class="title_border">
                    <div class="content_title"><h1><?= $title ?></h1></div>
                    <div class="back_and_exit">
                        русский <a href="/en<?= $_SERVER['REQUEST_URI'] ?>">english</a>

                        <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться
                                на сайт ></a></span>
                        <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                    </div>
                </div>

                <div class="content">
                    <div class="top_menu">
                        <!--                        <div class="top_menu_link"><a href="/admin/pages/">Страницы</a></div>-->
                        <!--                        <div class="top_menu_link"><a href="/admin/pages/add/">Добавить страницу</a></div>-->
                        <div class="top_menu_link"><a href="/admin/import/?nothing_doing=true">Все действия</a></div>
                        <div class="top_menu_link"><a href="/admin/import/?update_only=true">Только обновить</a></div>
                        <div class="top_menu_link"><a href="/admin/import/?lost_only=true">Только не найденные в
                                базе</a></div>
                        <div class="top_menu_link"><a href="/admin/import/?new_only=true">Только не найденные на
                                сайте</a></div>

                    </div>

                    <?php
                    if (isset($_GET['nothing_doing']) && $_GET['nothing_doing'] == true) {
                        ?>
                        <h2>Выберите, что Вам необходимо сделать:</h2>
                        <ul>
                            <li style="padding-bottom: 20px">
                                <a href="/admin/import/?update_only=true">Только обновить уже синхронизированные
                                    товары</a>
                                <?php
                                //if(file_exists('upload/cache/admin_last_import_update_only.html')) echo ' (<a target="_blank" href="/upload/cache/admin_last_import_update_only.html">смотреть результаты последнего запуска</a>)';
                                ?>
                            </li>
                            <li style="padding-bottom: 20px">
                                <a href="/admin/import/?lost_only=true">Только показать товары, которые есть на сайте,
                                    но не синхронизированы с ТоргСофтом</a>
                                <?php
                                //if(file_exists('upload/cache/admin_last_import_lost_only.html')) echo ' (<a target="_blank" href="/upload/cache/admin_last_import_lost_only.html">смотреть результаты последнего запуска</a>)';
                                ?>
                            </li>
                            <li style="padding-bottom: 20px">
                                <a href="/admin/import/?new_only=true">Только показать товары, которые есть в ТоргСофте,
                                    но нет на сайте</a>
                                <?php
                                //if(file_exists('upload/cache/admin_last_import_new_only.html')) echo ' (<a target="_blank" href="/upload/cache/admin_last_import_new_only.html">смотреть результаты последнего запуска</a>)';
                                ?>
                            </li>
                            <li style="padding-bottom: 40px">
                                <a href="/admin/import/"><strong>Сделать все 3 действия</strong></a>
                            </li>
                        </ul>
                        <?php
                    }

                    //ob_start();
                    ?>
                    <?= $msg ?>

                    <?php if (isset($articles) && $articles != false) { ?>
                    <a name="done"></a>
                    <div class="view-source">

                        <h2>Успешно синхронизированы</h2>
                        <!--                        <a class="nocookies" style="font-size:11px;color:black" href="#">Показать</a>-->
                        <!--                        <div class="hideme nocookies" style="display: block;">-->
                        <div>
                            <table width="100%" cellpadding="1" cellspacing="1" border="1">
                                <tr bgcolor="#EEEEEE">
                                    <th>i</th>
                                    <th>GoodID</th>
                                    <th>GoodName</th>
<!--                                    <th>Barcode</th>-->
                                    <th>RetailPrice</th>
                                    <th>DiscountPrice</th>
                                    <th>Discount %</th>
                                    <th>TheSize</th>
                                    <th>Warehouse</th>
                                    <th>|</th>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th style="max-width: 100px">Остаток</th>
                                    <th>Всего</th>
                                    <th></th>

                                </tr>
                                <?php
                                //vd($articles);
                                $i = 0;
                                $sizes = getOption('sizes');
                                $sizes = explode('|', $sizes);
                                foreach ($articles as $art) {

                                    $i++;
                                    ?>
                                    <tr class="list">
                                        <td><?= $i ?></td>
                                        <td><?= $art['import']['GoodID'] ?></td>

                                        <td><?= $art['import']['Articul'] ?>: <?= $art['import']['Description'] ?>
                                            (<?= $art['import']['Color'] ?>)
                                        </td>
                                        <!--td><?= $art['import']['Barcode'] ?></td-->
                                        <td><?= $art['import']['RetailPrice'] ?>$</td>
                                        <td><?= $art['import']['RetailPriceWithDiscount'] ?>$</td>
                                        <td>
                                            <?= getDiscount($art['price'], $art['new_price']) ?>
                                        </td>
                                        <td><?= $art['import']['TheSize'] ?></td>
                                        <td><?= $art['import']['WarehouseQuantity'] ?></td>


                                        <td>|</td>
                                        <td><?= $art['id'] ?></td>
                                        <td><a target="_blank" title="Перейти к редактированию статьи"
                                               href="<?= getFullUrl($art) ?>" class="tooltip"><?= $art['name'] ?>
                                                (<?= $art['color'] ?>)<span><img width="125px"
                                                                                 src="<?= $art['image'] ?>"></span></a>
                                        </td>
                                        <td style="font-size: 12px;">
                                            <?php

                                            //echo $art['warehouse'];

                                            $warehouse = json_decode($art['warehouse'], true);

                                            $sum = array_sum($warehouse);
                                            foreach ($sizes as $s) {
                                                if (isset($warehouse[$s])) echo $s . ': ' . $warehouse[$s] . ' ';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div title=""><?= $sum ?></div>
                                        </td>
                                        <td><a target="_blank" title="Перейти к редактированию статьи"
                                               href="/admin/shop/edit/<?= $art['id'] ?>/"><img src="/img/edit.png"/></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                        <?php } ?>

                        <?php if (isset($lost) && $lost != false) { ?>
                            <a name="lost"></a>
                            <h2>Не найденные товары с сайта</h2>
                            <div class="view-source">
                                <!--                                <a class="nocookies" style="font-size:11px;" href="#">Показать</a>-->
                                <!--                                <div class="hideme nocookies" style="display: block;">-->
                                <div>
                                    <table width="100%" cellpadding="1" cellspacing="1" border="1">
                                        <tr bgcolor="#EEEEEE">
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Название</th>
                                            <th>Артикул</th>
                                            <th>Цвет</th>
                                            <th>Цена</th>
                                            <th>Размнры</th>
                                            <th></th>


                                        </tr>
                                        <?php
                                        //vd($articles);
                                        $i = 0;
                                        foreach ($lost as $art) {
                                            $i++;
                                            if ($art['active'] == 1) {
                                                ?>
                                                <tr class="list">
                                                    <td><?= $i ?></td>
                                                    <td><?= $art['id'] ?></td>
                                                    <td><a target="_blank" title="Перейти к редактированию статьи"
                                                           href="<?= getFullUrl($art) ?>"
                                                           class="tooltip"><?= $art['name'] ?>
                                                            (<?= $art['color'] ?>)<span><img width="125px"
                                                                                             src="<?= $art['image'] ?>"></span></a>
                                                    </td>
                                                    <td><?= $art['articul'] ?></td>
                                                    <td><?= $art['color'] ?></td>
                                                    <td><?= $art['price'] ?>$</td>
                                                    <td><?= $art['razmer'] ?></td>
                                                    <td><a target="_blank" title="Перейти к редактированию статьи"
                                                           href="/admin/shop/edit/<?= $art['id'] ?>/"><img
                                                                src="/img/edit.png"/></a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (isset($new) && $new != false) { ?>
                            <a name="new"></a>
                            <h2>Не найденные товары из базы</h2>
                            <div class="view-source">
                                <!--                                <a class="nocookies" style="font-size:11px;" href="#">Показать</a>-->
                                <!--                                <div class="hideme nocookies" style="display: block;">-->
                                <div>
                                    <table width="100%" cellpadding="1" cellspacing="1" border="1">
                                        <tr bgcolor="#EEEEEE">
                                            <th></th>
                                            <th>i</th>
                                            <th>GoodID</th>
                                            <th>GoodName</th>
                                            <th>RetailPrice</th>
                                            <th>DiscountPrice</th>
                                            <th>Discount %</th>
                                            <th>TheSize</th>
                                            <th>Warehouse</th>
                                            <th>|</th>
                                            <th>Найденное</th>


                                        </tr>
                                        <?php
                                        //vd($articles);
                                        $i = 0;
                                        foreach ($new as $art) {
                                            $i++;
                                            if ($art['WarehouseQuantity'] != 0) {
                                                $inShop = $this->shop->searchByBaseIdCount($art['GoodID']);
                                                if (!$inShop) {
                                                    // Собираем все разберы товара по артикулу
                                                    $stgi = false;
                                                    $base_ids = false;
                                                    $warehouse = false;
                                                    foreach ($new as $item) {
                                                        if ($item['Articul'] == $art['Articul'] && $item['Color'] == $art['Color']) {
                                                            if(!$stgi) $stgi = array();
                                                            $stgi[$item['TheSize']] = $item['GoodID'];
                                                            if(!$warehouse) $warehouse = array();
                                                            $warehouse[$item['TheSize']] = $item['WarehouseQuantity'];
                                                            if(!$base_ids) $base_ids = array();
                                                            array_push($base_ids, $item['GoodID']);
                                                        }
                                                    }
                                                    
                                                    $dont_show = false;
                                                    $import = $this->shop->getInImportByGoodID($art['GoodID']);
                                                    if (isset($import['dont_show']) && $import['dont_show'] == 1)
                                                        $dont_show = true;
                                                    if (!$dont_show) {
                                                        ?>
                                                        <tr class="list" id="not_founded_<?= $art['GoodID'] ?>">
                                                            <td><img class="dont_show" GoodId="<?= $art['GoodID'] ?>"
                                                                     id="dont_show_<?= $art['GoodID'] ?>"
                                                                     src="/img/admin/dont_show.png"
                                                                     width="24px"
                                                                     title="Больше не показывать эту позицию при импорте"/>
                                                            </td>
                                                            <td><?= $i ?></td>
                                                            <td><?= $art['GoodID'] ?></td>
                                                            <!--td><?= $art['Articul'] ?>: <?= $art['Description'] ?> (<?= $art['Color'] ?>)</td-->
                                                            <td><?= $art['GoodName'] ?></td>
                                                            <td><?= $art['RetailPrice'] ?>$</td>
                                                            <td><?= $art['RetailPriceWithDiscount'] ?>$</td>
                                                            <td>
                                                                <?= getDiscount($art['RetailPrice'], $art['RetailPriceWithDiscount']) ?>
                                                            </td>
                                                            <td><?= $art['TheSize'] ?></td>
                                                            <td><?= $art['WarehouseQuantity'] ?></td>
                                                            <td>|</td>
                                                            <td>
                                                                <?php
                                                                if ($art['finded']) {


                                                                    echo '<ul>';
                                                                    $arr = $art['finded'];
                                                                    foreach ($arr as $a) {
                                                                        ?>
                                                                        <li>
                                                                            <a style="font-size: 13px; font-weight: bold"
                                                                               target="_blank"
                                                                               href="/admin/shop/edit/<?= $a['id'] ?>/"><?= $a['name'] ?>
                                                                                (<?= $a['color'] ?>)</a>:<br/>
                                                                            - <a style="font-size: 13px"
                                                                                 onclick="return confirm('Вы точно хотите поменять <?= $a['color'] ?> на <?= $art['Color'] ?>?')"
                                                                                 target="_blank"
                                                                                 href="/admin/import/set_color/?articul=<?= $art['Articul'] ?>&warehouse=<?= urlencode(json_encode($warehouse)) ?>&base_ids=<?= urlencode(json_encode($base_ids)) ?>&stgi=<?= urlencode(json_encode($stgi)) ?>&id=<?= $a['id'] ?>&color=<?= urlencode($art['Color']) ?>">связать</a>
                                                                            <?php
                                                                            if ($a['sizes_to_good_ids'] != NULL) {
                                                                                $stgi = json_decode($a['sizes_to_good_ids'], true);
                                                                                if (!isset($stgi[$art['TheSize']])) {
                                                                                    ?>
                                                                                    <br/>
                                                                                    - <a style="font-size: 13px"
                                                                                         target="_blank"
                                                                                         href="/admin/shop/edit/<?= $a['id'] ?>/?add_size=<?= $art['TheSize'] ?>">добавить <?= $art['TheSize'] ?>
                                                                                        размер</a>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    echo '</ul>';
                                                                } else
                                                                    echo '<p style="font-size:13px">Соответствий не найдено</p>';


                                                                //vd($stgi);
                                                                if(!isset($warehouse)) $warehouse = false;
                                                                ?>
                                                                <a style="font-size: 13px; font-weight: bold"
                                                                   target="_blank"
                                                                   href="/admin/shop/add/?articul=<?= $art['Articul'] ?>&warehouse=<?= urlencode(json_encode($warehouse)) ?>&base_ids=<?= urlencode(json_encode($base_ids)) ?>&stgi=<?= urlencode(json_encode($stgi)) ?>&GoodID=<?= $art['GoodID'] ?>&name=<?= urlencode($art['Description']) ?>&price=<?= str_replace(',', '.', $art['RetailPrice']) ?>&color=<?= urlencode($art['Color']) ?>&category=<?= urlencode($art['GoodTypeFull']) ?>&tkan=<?= urlencode($art['Material']) ?>">Добавить
                                                                    как новый</a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>

                        <?php
//                        $page = ob_get_contents();
//                        if(isset($_GET['update_only']) && $_GET['update_only'] == true && !isset($_GET['nothing_doing'])){
//                            @unlink('upload/cache/admin_last_import_update_only.html');
//                            file_put_contents('upload/cache/admin_last_import_update_only.html',$page);
//                        } elseif(isset($_GET['lost_only']) && $_GET['lost_only'] == true && !isset($_GET['nothing_doing'])){
//                            @unlink('upload/cache/admin_last_import_lost_only.html');
//                            file_put_contents('upload/cache/admin_last_import_lost_only.html',$page);
//                        } elseif(isset($_GET['new_only']) && $_GET['new_only'] == true && !isset($_GET['nothing_doing'])){
//                            @unlink('upload/cache/admin_last_import_new_only.html');
//                            file_put_contents('upload/cache/admin_last_import_new_only.html',$page);
//                        }
                        ?>
                            <!--                                    <script src="/js/jquery-1.7.2.min.js"></script>-->
                            <script>
                                var j = jQuery.noConflict();

                                //                        function dontShowAgain(GoodID) {
                                //                            $.ajax({
                                //                                url: '/admin/ajax/import/dont_show/?GoodID='+GoodID,
                                //                                method: 'POST',
                                //                                data: {
                                //                                    "GoodID": GoodID
                                //                                },
                                //
                                //                            }).done(function (data) {
                                //                                $("#not_founded_"+GoodID).hide();
                                //                            });
                                //                        }

                                j(document).ready(function{
                                    j(".dont_show").click(function{
                                        alert("asd");
//                                var GoodID = j(this).attr('GoodID');
//                                if(confirm("Точно больше не показывать данную позицию?")){
//                                    dontShowAgain(GoodID);
//                                }
                                    });
                                });
                            </script>
                        <?php } ?>


                        <!--                <table width="100%" cellpadding="1" cellspacing="1">-->
                        <!--                    <tr>-->
                        <!--                        <th>Товар в базе</th>-->
                        <!--                        <th>Найденный товар</th>-->
                        <!--                    </tr>-->
                        <!--                    --><?php
                        //                    //vd($articles);
                        //                    $i = 0;
                        //                    foreach($articles as $p){
                        //                        $i++;
                        //                        $p['Description'] = iconv("CP1251","UTF-8",$p['Description']);
                        //                        $p['Color'] = iconv("CP1251","UTF-8",$p['Color']);
                        //                        ?>
                        <!--                        <tr class="list">-->
                        <!--                            <td width="50%">-->
                        <!--                                <table>-->
                        <!--                                    <tr>-->
                        <!--                                        <td><b>--><? //=$i?><!--</b></td>-->
                        <!--                                        <td>--><? //=$p['GoodID']?><!--</td>-->
                        <!--                                        <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['GoodName'])?><!--</td>-->
                        <!--                                        <td>--><? //=$p['Description']?><!--</td>-->
                        <!--                                        <td>--><? //=$p['Articul']?><!--</td>-->
                        <!--                                        <td>--><? //=$p['RetailPrice']?><!--</td>-->
                        <!--                                        <td>--><? //=$p['WarehouseQuantity']?><!--</td>-->
                        <!--                                        <td>--><? //=$p['TheSize']?><!--</td>-->
                        <!--                                        <td>--><? //=$p['Color']?><!--</td>-->
                        <!--                                        <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['Material'])?><!--</td>-->
                        <!--                                        <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['FashionName'])?><!--</td>-->
                        <!--                                        <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['GoodTypeFull'])?><!--</td>-->
                        <!--                                    </tr>-->
                        <!--                                </table>-->
                        <!--                            </td>-->
                        <!--                            <td width="50%">-->
                        <!--                                --><?php
                        //                                $art = $this->shop->searchByNameArticulColor($p['Description'], $p['Articul'], $p['Color']);
                        //                                if($art)
                        //                                {
                        //                                    ?>
                        <!--                                    <table>-->
                        <!--                                        <tr>-->
                        <!--                                            <td>--><? //=$art['name']?><!--</td>-->
                        <!--                                            <td>--><? //=$art['articul']?><!--</td>-->
                        <!--                                            <td>--><? //=$art['color']?><!--</td>-->
                        <!--                                            <td></td>-->
                        <!--                                        </tr>-->
                        <!--                                    </table>-->
                        <!--                                    --><?php
                        //                                }
                        //                                else echo 'Не найден';
                        //                                ?>
                        <!--                            </td>-->
                        <!---->
                        <!---->
                        <!--                        </tr>-->
                        <!--                        --><?php
                        //                        ///die();
                        //                    }
                        //                    ?>
                        <!--                </table>-->


                        <!--                <table width="100%" cellpadding="1" cellspacing="1">-->
                        <!--                    <tr bgcolor="#EEEEEE">-->
                        <!--                        <th>#</th>-->
                        <!--                        <th>GoodID</th>-->
                        <!--                        <th>GoodName</th>-->
                        <!--                        <th>Description</th>-->
                        <!--                        <th>Articul</th>-->
                        <!--                        <th>RetailPrice</th>-->
                        <!--                        <th>WarehouseQuantity</th>-->
                        <!--                        <th>TheSize</th>-->
                        <!--                        <th>Color</th>-->
                        <!--                        <th>Material</th>-->
                        <!--                        <th>FashionName</th>-->
                        <!--                        <th>GoodTypeFull</th>-->
                        <!---->
                        <!--                    </tr>-->
                        <!--                    --><?php
                        //                    //vd($articles);
                        //                    $i = 0;
                        //                    foreach($articles as $p){
                        //                        $i++;
                        //                        ?>
                        <!--                        <tr class="list">-->
                        <!--                            <td><b>--><? //=$i?><!--</b></td>-->
                        <!--                            <td>--><? //=$p['GoodID']?><!--</td>-->
                        <!--                            <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['GoodName'])?><!--</td>-->
                        <!--                            <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['Description'])?><!--</td>-->
                        <!--                            <td>--><? //=$p['Articul']?><!--</td>-->
                        <!--                            <td>--><? //=$p['RetailPrice']?><!--</td>-->
                        <!--                            <td>--><? //=$p['WarehouseQuantity']?><!--</td>-->
                        <!--                            <td>--><? //=$p['TheSize']?><!--</td>-->
                        <!--                            <td>--><? //=iconv("CP1251","UTF-8",$p['Color'])?><!--</td>-->
                        <!--                            <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['Material'])?><!--</td>-->
                        <!--                            <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['FashionName'])?><!--</td>-->
                        <!--                            <td>-->
                        <? //=iconv("CP1251","UTF-8",$p['GoodTypeFull'])?><!--</td>-->
                        <!---->
                        <!--                        </tr>-->
                        <!--                        --><?php
                        //                        ///die();
                        //                    }
                        //                    ?>
                        <!--                </table>-->
                    </div>
            </td>
        </tr>
    </table>
<?php
include("footer.php");
?>