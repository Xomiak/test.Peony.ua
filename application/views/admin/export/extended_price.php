<?php
include("application/views/admin/header.php");
?>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("application/views/admin/menu.php"); ?></td>
            <td width="20px"></td>
            <td valign="top">
                <div class="title_border">
                    <div class="content_title"><h1><?=$title?></h1></div>
                    <div class="back_and_exit">
                        русский <a href="/en<?=$_SERVER['REQUEST_URI']?>">english</a>

                        <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                        <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                    </div>
                </div>

                <div class="content">
                    <div class="top_menu">
                        <div class="top_menu_link"><a href="/admin/shop/">Товары</a></div>
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/add/">Добавить товар</a></div>
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/import/">Импорт</a></div>-->
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/export/">Экспорт</a></div>-->
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/currencies/">Валюты</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/specifications/">Спецификации</a></div>

                        <div class="top_menu_link"><a href="/admin/shop/create_extended_price/">Создать спецификацию</a></div>
                        <div class="top_menu_link"><a href="/import/yandex_market.xml">YML (XML)</a></div>

                    </div>
                    <?=$msg?>

                    <?php if(isset($name)) {?>

                        Архив с фото: <input size="70" type="text" value="/upload/export/<?=$name?>.zip" /> [ <a href="/upload/export/<?=$name?>.zip">скачать</a> ]<br />
                        Прайс (xls): <input size="70" type="text" value="/upload/export/<?=$name?>.xls" /> [ <a href="/upload/export/<?=$name?>.xls">скачать</a> ]<br />
                        <BR /><BR />
                    <?php } else{ ?>


                    <form method="post">
                        <table cellpadding="2" cellspacing="2">
                            <tr>
                                <th valign="top">
                                    Скидка/Наценка:
                                </th>
                                <td>
                                    <input type="number" max="100" min="-100" name="create_price_percents" size="2" style="width: 50px" value="<?=getOption('create_price_percents')?>" />%
                                </td>
                                <td valign="top"><span class="helper"><img title="Положительное, либо отрицательное число (Пример: -5)" alt="Подсказка" src="/img/question.png" width="16px" height="16px"></span></td>
                            </tr>
                            <tr>
                                <th valign="top">
                                    Не учитывать товары, где остаток меньше:
                                </th>
                                <td>
                                    <input type="number" max="100000" min="0" name="min_warehouse" size="2" style="width: 50px" value="<?=getOption('create_price_min_warehouse')?>" /> шт.
                                </td>
                                <td valign="top"><span class="helper"><img title="Положительное, либо отрицательное число (Пример: -5)" alt="Подсказка" src="/img/question.png" width="16px" height="16px"></span></td>
                            </tr>
                            <tr>
                                <th valign="top">
                                    Занижать остатки на:
                                </th>
                                <td>
                                    <input type="number" max="100" min="0" name="warehouse_zaniz" size="2" style="width: 50px" value="<?=getOption('warehouse_zaniz')?>" />%
                                </td>
                                <td valign="top"><span class="helper"><img title="Положительное, либо отрицательное число (Пример: -5)" alt="Подсказка" src="/img/question.png" width="16px" height="16px"></span></td>
                            </tr>
                            <tr style="border-bottom: dotted 1px #aaa">
                                <th valign="top">
                                    Поля:
                                </th>
                                <td>
                                    <div id="div_rows" style="display: inline-block; width: 200px; padding-right: 50px">
                                    <select id="sel_rows" name="rows[]"  multiple="" style="width: 200px; height: 300px">
                                        <?php
                                        $arr['no'] = '№';
                                        $arr['brand'] = 'Бренд';
                                        $arr['nomenklatura'] = 'Номенклатура поставщика';
                                        $arr['barcode'] = 'Штрихкод';
                                        $arr['articul'] = 'Артикул';
                                        $arr['color'] = 'Код цвета';
                                        $arr['razmer'] = 'Размер';
                                        $arr['razmer_ukraine'] = 'Размер - Украина';
                                        $arr['category'] = 'Раздел Сайта';
                                        $arr['sex'] = 'Пол';
                                        $arr['type'] = 'Тип';
                                        $arr['sostav'] = 'Состав';
                                        $arr['sezon'] = 'Сезон';
                                        $arr['country'] = 'Страна произв.';
                                        $arr['kolvo'] = 'Количество';
                                        $arr['price1'] = 'Цена покупки';
                                        //$arr['price2'] = 'Цена начальная';
                                        $arr['price_discount'] = 'Цена со скидкой';
                                        $arr['is_sale'] = 'Sale';
                                        $arr['qr'] = 'Штрихкод';
                                        $arr['short_content'] = 'Короткое описание';
                                        $arr['content'] = 'Полное описание';
                                        $arr['foto_names'] = 'Имена фото';
                                        $arr['foto_size'] = 'Размер на фото';

                                        ?>
                                        <option selected value="no">№</option>
                                        <option selected value="brand">Бренд</option>
                                        <option selected value="nomenklatura">Номенклатура поставщика</option>
                                        <option selected value="barcode">Штрихкод</option>
                                        <option selected value="articul">Артикул</option>
                                        <option selected value="color">Код цвета</option>
                                        <option selected value="razmer">Размер</option>
                                        <option selected value="razmer_ukraine">Размер - Украина</option>
                                        <option selected value="category">Раздел Сайта</option>
                                        <option selected value="sex">Пол</option>
                                        <option selected value="type">Тип</option>
                                        <option selected value="sostav">Состав</option>
                                        <option selected value="sezon">Сезон</option>
                                        <option selected value="country">Страна произв.</option>
                                        <option selected value="kolvo">Количество</option>
                                        <option selected value="price1">Цена покупки</option>
<!--                                        <option selected value="price2">Цена начальная</option>-->
                                        <option selected value="price_discount">Цена со скидкой</option>
                                        <option selected value="is_sale">Sale</option>
                                        <option selected value="qr">Штрихкод</option>
                                        <option selected value="short_content">Короткое описание</option>
                                        <option selected value="content">Полное описание</option>
                                        <option selected value="foto_names">Имена фото</option>
                                        <option selected value="foto_size">Размер на фото</option>
                                    </select>
                                    </div>
                                    <div id="templates" style="display: inline-block; width: 200px">
                                        <select id="template" name="template">
                                            <option value="">-- шаблоны --</option>
                                        </select><br />
                                        <button disabled id="load_template">Применить шаблон</button><br /><br />
                                        Сохранить шаблон спецификации:<br />
                                        <input type="text" id="template_name" name="template_name" placeholder="Название шаблона"><br/>
                                        <button disabled id="save_template">Сохранить</button>
                                    </div>
                                </td>

                            </tr>
                            <tr>
                                <th valign="top">
                                    Фото без лого:
                                </th>
                                <td>
                                    <input type="checkbox" name="image_no_logo" checked />
                                </td>
                            </tr>
                            <tr>
                                <th valign="top">
                                    Сохранить спецификацию как:
                                </th>
                                <td>
                                    <input type="checkbox" name="save" checked /><input type="text" name="saved_name"  value="<?=date("Y-m-d H:i")?>" />
                                </td>
                                <td valign="top"><span class="helper"><img title="Сохранить шаблон спецификации для повторных сборок" alt="Подсказка" src="/img/question.png" width="16px" height="16px"></span></td>
                            </tr>

                            <tr>
                                <th valign="top">
                                    Выбранные товары:
                                </th>
                                <td>
                                    <div style="width: 100%; height: 400px; overflow: auto">
                                        <table width="100%">
                                            <tr>
                                                <th>Название</th>
                                                <th>Цена</th>
                                                <th>Сезон</th>
                                                <th>На складе</th>
                                                <th>Скидка</th>
                                            </tr>
                                        <?php
                                        $articles = $this->mshop->getForMailer('checked');
                                        $usdToUah = getCurrencyValue('UAH');
                                        if($articles){
                                            foreach ($articles as $article){
                                                ?>
                                                <tr class="list" id="tr-mailer-<?=$article['id']?>">
                                                    <td>
                                                        <a target="_blank" href="/admin/shop/edit/<?=$article['id']?>/"><?=$article['name']?> (<?=$article['color']?>)</a>
                                                    </td>
                                                    <td><?=round($article['price']*$usdToUah, 2)?> грн</td>
                                                    <td><?=$article['season']?></td>
                                                    <td>
                                                        <?php
                                                        $warehouse = json_decode($article['warehouse'], true);

                                                        $sizes = explode('*',$article['razmer']);
                                                        //vd($sizes);
                                                        foreach ($sizes as $size){
                                                            if(isset($warehouse[$size]))
                                                                echo $size.': '.$warehouse[$size].'; ';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?=$article['discount']?>
                                                    </td>
                                                    <td>
                                                        <img class="mailer_action" action="checked" value="1" shop_id="<?=$article['id']?>" src="/img/admin/checked.gif" title="Убрать" id="mailer_checked_<?=$article['id']?>">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </table>
                                    </div>
                                </td>
                                <td valign="top"><span class="helper"><img title="Сохранить шаблон спецификации для повторных сборок" alt="Подсказка" src="/img/question.png" width="16px" height="16px"></span></td>
                            </tr>

                            <tr>
                                <td colspan="2"><input type="submit" name="create" value="Создать"></td>
                            </tr>

                        </table>
                    </form>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>
    <script>
        $(document).ready(function () {
            $(".mailer_action").click(function () {
                    var type = 'mailer';
                    var id = $(this).attr("shop_id");
                    var action = 'checked';
                    var value = $(this).attr("value");
                if(value == '1') {
                    if (confirm("Вы точно хотите убрать эту позицию из списка?")) {
                        value = 0;
                        $.ajax({
                            /* адрес файла-обработчика запроса */
                            url: '/admin/ajax/admin_action/',
                            /* метод отправки данных */
                            method: 'POST',
                            /* данные, которые мы передаем в файл-обработчик */
                            data: {
                                "action": action,
                                "type": type,
                                "obj": id,
                                "value": value
                            },

                        }).done(function (data) {
                            if (data != 'error') {
                                var thisClass = type + "_" + action + "_" + id;
                                $("#" + thisClass).attr("value", value);
                                if (value == 0) {
                                    $("#" + thisClass).addClass("img-not-active");
                                }
                                else {
                                    $("#" + thisClass).removeClass("img-not-active");
                                }
                            }

                            setAdminMessage(action);
                            $("#log").html(data);
                            $("#tr-mailer-" + id).hide();
                        });
                    }
                }
            });
        });
    </script>
<?php
include("application/views/admin/footer.php");
?>