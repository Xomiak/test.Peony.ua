<?php
include("header.php");
?>

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
                        <div class="top_menu_link"><a href="/admin/shop/">Товары</a></div>
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/add/">Добавить товар</a></div>
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/import/">Импорт</a></div>-->
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/export/">Экспорт</a></div>-->
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/currencies/">Валюты</a></div>
                        <div
                            class="top_menu_link"<?php if (userdata('type') != 'admin') echo ' style="display:none;"'; ?>>
                            <a href="/admin/shop/createCheckedPrice/">Создать прайс</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/create_extended_price/">Создать спецификацию</a></div>
                        <div class="top_menu_link"><a href="/import/yandex_market.xml">YML (XML)</a></div>
                    </div>

                    <strong><font color="Red"><?= $err ?></font></strong>
                    <form enctype="multipart/form-data" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <table border="0">
                            <tr>
                                <td>Название *:</td>
                                <td><input required type="text" name="name" size="50"
                                           value="<?php if (isset($_POST['name'])) echo $_POST['name']; else echo $article['name']; ?>"/>
                                </td>
                                <td rowspan="4" width="20%">
                                    <div style="padding: 15px; background-color: #bbb">
                                    <strong>Связка с ТоргСофтом</strong><br />

                                    <?php
                                    $stgi = $article['sizes_to_good_ids'];
                                    if($stgi != NULL && $stgi != ''){
                                        $arr = json_decode($stgi, true);
                                        $sizes = getOption('sizes');
                                        $sizes = explode('|', $sizes);
                                        foreach($sizes as $size){
                                            if(isset($arr[$size])) echo "Размер: ".$size." ID в ТС: ".$arr[$size].'<br />';
                                        }
                                    } else {
                                        echo "<div style='color: indianred; font-weight: bold'>Товар не связан ни с одной позицией ТоргСофта!</div>";
                                    }
                                    ?>
                                    </div>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Цвет:</td>
                                <td>
                                    <input type="text" name="color" value="<?= $article['color'] ?>"/>

                                </td>

                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Артикул:</td>
                                <td><input required type="text" name="articul" size="50"
                                           value="<?php if (isset($_POST['articul'])) echo $_POST['articul']; else echo $article['articul']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Цена:</td>
                                <td><input required type="text" name="price" size="50"
                                           value="<?php if (isset($_POST['price'])) echo $_POST['price']; else echo $article['price']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Скидка:</td>
                                <td>
                                    <select name="discount">
                                        <option value="0">Нет</option>
                                        <?php
                                        $discount = 5;
                                        while ($discount < 100) {
                                            echo '<option value="' . $discount . '"';
                                            if ($article['discount'] == $discount) echo ' selected';
                                            echo '>' . $discount . '%</option>';
                                            $discount += 5;
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td></td>
                                <td><input type="checkbox"
                                           name="sale"<? if ($article['sale'] == 1) echo ' checked' ?> /> SALE
                                    (безвременная скидка)
                                </td>
                            </tr>
                            <!--tr>
                            <td>Новая Цена:</td>
                            <td><input  type="text" name="old_price" size="50" value="<?php if (isset($_POST['old_price'])) echo $_POST['old_price']; else echo $article['old_price']; ?>" /></td>
                        </tr-->
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Старт акции:</td>
                                <td><input class="date" placeholder="В формате: 2015-09-28" type="text"
                                           name="akciya_start" size="50"
                                           value="<?php if (isset($_POST['akciya_start'])) echo $_POST['akciya_start']; else echo $article['akciya_start']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Конец акции:</td>
                                <td><input class="date" placeholder="В формате: 2015-09-28" type="text"
                                           name="akciya_end" size="50"
                                           value="<?php if (isset($_POST['akciya_end'])) echo $_POST['akciya_end']; else echo $article['akciya_end']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>url:</td>
                                <td><input type="text" name="url" size="50"
                                           value="<?php if (isset($_POST['url'])) echo $_POST['url']; else echo $article['url']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Позиция:</td>
                                <td><input required type="text" name="num" size="50"
                                           value="<?php if (isset($_POST['num'])) echo $_POST['num']; else echo $article['num']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Рейтинг (сумма):</td>
                                <td><input required type="text" name="rating" size="10"
                                           value="<?php if (isset($_POST['rating'])) echo $_POST['rating']; else echo $article['rating']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Кол-во проголосовавших:</td>
                                <td><input required type="text" name="voitings" size="10"
                                           value="<?php if (isset($_POST['voitings'])) echo $_POST['voitings']; else echo $article['voitings']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Раздел *:</td>
                                <td>
                                    <?php
                                    $cat_ids = explode('*', $article['category_id']);
                                    $ccount = count($cat_ids);
                                    ?>
                                    <SELECT style="height: 300px" required
                                            name="category_id[]"<?php if ($article_in_many_categories != '0') echo ' multiple=""'; ?>>

                                        <!--SELECT name="category_id"-->
                                        <option></option>
                                        <?php
                                        $count = count($categories);
                                        for ($i = 0; $i < $count; $i++) {
                                            $cat = $categories[$i];
                                            echo '<option value="' . $cat['id'] . '"';
                                            for ($ci = 0; $ci < $ccount; $ci++) {
                                                if ($cat_ids[$ci] == $cat['id']) echo ' selected';
                                            }
                                            //if(isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) echo ' selected';
                                            //else if($cat['id'] == $article['category_id']) echo ' selected';
                                            echo '>' . $cat['name'] . '</option>';
                                            $subs = $this->mcats->getSubCategories($cat['id']);
                                            if ($subs) {
                                                $subcount = count($subs);
                                                for ($j = 0; $j < $subcount; $j++) {
                                                    $sub = $subs[$j];
                                                    echo '<option value="' . $sub['id'] . '"';
                                                    for ($ci = 0; $ci < $ccount; $ci++) {
                                                        if ($cat_ids[$ci] == $sub['id']) echo ' selected';
                                                    }
                                                    //if(isset($_POST['category_id']) && $_POST['category_id'] == $sub['id']) echo ' selected';
                                                    //else if($sub['id'] == $article['category_id']) echo ' selected';

                                                    echo '>&nbsp;└&nbsp;' . $sub['name'] . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                            </tr>

                            <tr>
                                <td>Сезон:</td>
                                <td>
                                    <SELECT name="season">
                                        <option></option>
                                        <?php
                                        $seasons = getOption('seasons');
                                        if($seasons){
                                            $seasons = explode('|',$seasons);
                                            if(is_array($seasons)){
                                                foreach ($seasons as $season){
                                                    $season = trim($season);
                                                    if($season != '') {
                                                        echo '<option';
                                                        if(isset($_POST['season']) && $_POST['season'] == $season) echo ' selected';
                                                        elseif($article['season'] == $season)  echo ' selected';
                                                        echo ' value="' . $season . '">' . $season . '</option>';
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                            </tr>

                            <tr>
                                <td>Заголовок H1:</td>
                                <td><input type="text" name="h1" size="50"
                                           value="<?php if (isset($_POST['h1'])) echo $_POST['h1']; else echo $article['h1']; ?>"/>
                                </td>
                            </tr>
                            <!--tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" value="<?php if (isset($_POST['num'])) echo $_POST['num']; else echo $article['num'] ?>" size="3" /></td>
                        </tr-->
                            <tr>
                                <td valign="top">Фото:</td>
                                <td>
                                    <?php
                                    if ($article['image'] != '') {
                                        echo '<img style="max-height: 400px" src="' . $article['image'] . '" /><br /><input type="checkbox" name="image_del">Удалить<br />';
                                    }
                                    ?>
                                    <input type="file" name="userfile"/>
                                    <input type="hidden" name="image" value="<?= $article['image'] ?>"/>
                                    <br/><a target="_blank" href="/admin/images/">Загрузить доп. фотографии</a>
                                </td>
                            </tr>

                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td valign="top">Фото БЕЗ ЛОГО:</td>
                                <td>
                                    <?php
                                    if($article['image_no_logo'] != '')
                                    {
                                        echo '<img style="max-height: 400px" src="'.$article['image_no_logo'].'" /><br /><input type="checkbox" name="image_no_logo_del">Удалить<br />';
                                    }
                                    ?>
                                    <input type="file" name="image_no_logo" />
                                    <input type="hidden" name="image_no_logo" value="<?=$article['image_no_logo']?>" />
                                </td>
                            </tr>

                            <tr>
                                <td valign="top">Фото для ВК:</td>
                                <td>
                                    <?php
                                    if($article['image_vk'] != '')
                                    {
                                        echo '<img style="max-height: 400px" src="'.$article['image_vk'].'" /><br /><input type="checkbox" name="image_vk_del">Удалить<br />';
                                    }
                                    ?>
                                    <input type="file" name="image_vk" />
                                    <input type="hidden" name="image_vk" value="<?=$article['image_vk']?>" />
                                </td>
                            </tr>

                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Youtube:</td>
                                <td>
                                    <?php
                                    $y = $article['youtube'];
                                    if ($article['youtube'] != '') {
                                        $pos = strpos($y, 'v=');
                                        if ($pos) {
                                            $pos = $pos + 2;
                                            $end = strpos($y, '&', $pos);
                                            $y = substr($y, $pos, $end - $pos);
                                        }
                                        if ($y != '')
                                            echo '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . $y . '" frameborder="0" allowfullscreen></iframe><br />';
                                    }
                                    ?>

                                    <input type="text" name="youtube" size="89"
                                           value="<?php if (isset($_POST['youtube'])) echo $_POST['youtube']; else echo $article['youtube']; ?>"/>
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Краткое описание:</td>
                                <td><textarea name="short_content"
                                              class="ckeditor"><?php if (isset($_POST['short_content'])) echo $_POST['short_content']; else echo $article['short_content']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Контент:</td>
                                <td><textarea name="content" class="ckeditor"
                                              rows="30"><?php if (isset($_POST['content'])) echo $_POST['content']; else echo $article['content']; ?></textarea>
                                </td>
                            </tr>

                            <?php
                            $this->db->order_by('name', 'ASC');
                            $razmer = $this->db->get('razmer')->result_array();
                            $this->db->order_by('name', 'ASC');
                            $color = $this->db->get('color')->result_array();
                            $this->db->order_by('name', 'ASC');
                            $tkan = $this->db->get('tkan')->result_array();
                            ?>

                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Размеры:</td>
                                <td>
                                    <?php
                                    $sizes = getOption('sizes');
                                    $cat_ids = explode('|', $sizes);
                                    $count = count($cat_ids);
                                    $razmer = explode('*', $article['razmer']);
                                    ?>
                                    <SELECT name="razmer[]" multiple="" style="height: 150px">
                                        <?php
                                        for ($i = 0; $i < $count; $i++) {
                                            ?>
                                            <option
                                                value="<?= $cat_ids[$i] ?>" <?php if (in_array($cat_ids[$i], $razmer)) echo " selected"; ?>><?= $cat_ids[$i] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                                <td><?= $article['warehouse'] ?></td>
                            </tr>



                            <tr>
                                <td>Ткань:</td>
                                <td>
                                    <!--SELECT name="tkan">
                                        <?php
                                    /*
                                        $count = count($tkan);
                                        for ($i = 0; $i < $count; $i++) {
                                            ?>
                                            <option
                                                value="<?= $tkan[$i]['name'] ?>"<?php if ($article['tkan'] == $tkan[$i]['name']) echo ' selected'; ?>><?= $tkan[$i]['name'] ?></option>
                                            <?php
                                        }
                                    */
                                        ?>
                                    </SELECT-->
                                    <input name="tkan" value="<?php if (isset($_POST['tkan'])) echo $_POST['tkan']; else echo $article['tkan']; ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>Состав:</td>
                                <td>
                                    <textarea
                                        name="sostav"><?php if (isset($_POST['sostav'])) echo $_POST['sostav']; else echo $article['sostav']; ?></textarea>
                                </td>
                            </tr>

                            <tr>
                                <td>Длина:</td>
                                <td>
                                    <input name="height" value="<?php if (isset($_POST['height'])) echo $_POST['height']; else echo $article['height']; ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>Длина рукава:</td>
                                <td>
                                    <input name="hand_height" value="<?php if (isset($_POST['hand_height'])) echo $_POST['hand_height']; else echo $article['hand_height']; ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>Тэги:</td>
                                <td><textarea
                                        name="tags"><?php if (isset($_POST['tags'])) echo $_POST['tags']; else echo $article['tags']; ?></textarea><br />
                                    <?php
                                    $tags = '<b>' . str_replace(',','</b>,<b>',getTags($article)) . '</b>';

                                    ?>
                                    Автоматически сгенерированные тэги:
                                    <div class="tags" style="font-size: 12px">
                                        <?=$tags?>
                                    </div>
                                </td>
                            </tr>

                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Кнопки соц. сетей:</td>
                                <td><input type="checkbox"
                                           name="social_buttons" <?php if (isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) echo ' checked'; else if ($article['social_buttons'] == 1) echo ' checked'; ?> />
                                </td>
                            </tr>


                            <tr>
                                <td>title:</td>
                                <td><input type="text" name="title" size="50"
                                           value="<?php if (isset($_POST['title'])) echo $_POST['title']; else echo $article['title']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>keywords:</td>
                                <td><textarea
                                        name="keywords"><?php if (isset($_POST['keywords'])) echo $_POST['keywords']; else echo $article['keywords']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>description:</td>
                                <td><textarea
                                        name="description"><?php if (isset($_POST['description'])) echo $_POST['description']; else echo $article['description']; ?></textarea>
                                </td>
                            </tr>
                            <!--tr>
                            <td>robots:</td>
                            <td><input type="text" name="robots" size="20" value="<?php if (isset($_POST['robots'])) echo $_POST['robots']; else echo 'index, follow'; ?>" /></td>
                        </tr-->
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td>Счётчик:</td>
                                <td><input type="text" name="count" size="5"
                                           value="<?php if (isset($_POST['count'])) echo $_POST['count']; else echo $article['count']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>SEO текст:</td>
                                <td><textarea name="seo"
                                              class="ckeditor"><?php if (isset($_POST['seo'])) echo $_POST['seo']; else echo $article['seo']; ?></textarea>
                                </td>
                            </tr>

                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td colspan="2"><input type="checkbox"
                                                       name="glavnoe"<?php if ($article['glavnoe'] == 1) echo ' checked'; ?> />
                                    На главной
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td colspan="2"><input type="checkbox"
                                                       name="akciya"<? if ($article['akciya'] == 1) echo ' checked' ?> />
                                    Акция
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td colspan="2"><input type="checkbox"
                                                       name="mailer_new"<? if ($article['mailer_new'] == 1) echo ' checked' ?> />
                                    В рассылку новинок
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td colspan="2"><input type="checkbox"
                                                       name="mailer_sale"<? if ($article['mailer_sale'] == 1) echo ' checked' ?> />
                                    В рассылку sale
                                </td>
                            </tr>
                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td colspan="2"><input type="checkbox"
                                                       name="active"<? if ($article['active'] == 1) echo ' checked' ?> />
                                    Активный
                                </td>
                            </tr>

                            <tr<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                                <td colspan="2"><input type="checkbox"
                                                       name="ended"<? if ($article['ended'] == 1) echo ' checked' ?> />
                                    Товар закончился и больше его не будет
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"><input id="need_text" type="checkbox"
                                                       name="need_text"<? if ($article['need_text'] == 1) echo ' checked' ?> />
                                    Нужно написать описание
                                </td>
                            </tr>
                            <tr id="tr-copywriter" style="display: none">
                                <td colspan="2">
                                    <input id="mail_to_copywraiter" type="checkbox" name="mail_to_copywraiter"/>
                                    Уведомить копирайтера
                                </td>
                            </tr>

                            <tr>
                                <td><input type="submit" name="save" value="Сохранить"/></td>
                                <td><input type="submit" name="save_and_stay" value="Сохранить и остаться"/></td>
                            </tr>
                        </table>
                        <div class="flying-buttons">
                            <div class="title_border flying-buttons-paddings">
                                <input style="width: 100%" type="submit" name="save_and_stay"
                                       value="Сохранить и остаться"/><br/>
                                <input style="width: 100%" type="submit" name="save" value="Сохранить"/>
                            </div>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function () {
                            $("#need_text").change(function () {
                                if($(this).is(":checked")) {
                                    $("#tr-copywriter").show();
                                } else {
                                    $("#tr-copywriter").hide();
                                    $("#mail_to_copywraiter").attr("checked", false);
                                }
                            });
                        })
                    </script>
                </div>
                <div class="content"<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>>
                    <h1>Дополнительные фото</h1>
                    <a name="images"></a>
                    <?php
                    if ($images) {
                        $count = count($images);
                        for ($i = 0; $i < $count; $i++) {
                            $image = $images[$i];
                            ?>
                            <form enctype="multipart/form-data" action="/admin/shop/edit_image/" method="post">
                                <table>
                                    <tr>
                                        <td>Фото:</td>
                                        <td>
                                            <input type="text" value="<?= $image['image'] ?>" size="50"/><br/>
                                            <a href="<?= $image['image'] ?>" target="_blank"><img
                                                    src="<?= $image['image'] ?>" width="300px" border="0"/></a><br/>
                                            <input type="file" name="userfile"/><br/>
                                            <input type="checkbox" name="delete"/> Удалить<br/>
                                            <input type="checkbox"
                                                   name="show_in_bottom"<?php if ($image['show_in_bottom'] == 1) echo ' checked'; ?> />Фото
                                            с лого<br/>
                                            <input type="checkbox"
                                                   name="active"<? if ($image['active'] == 1) echo ' checked'; ?> />
                                            Активный<br/>
                                            <input type="text" name="num" value="<?= $image['num'] ?>" size="3"/><br/>
                                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>"/>
                                            <input type="hidden" name="image_id" value="<?= $image['id'] ?>"/>
                                            <input type="submit" value="Применить"/>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <?php
                        }
                    }
                    ?>
                    <hr/>
                    <form enctype="multipart/form-data" action="/admin/shop/add_image/" method="post">
                        <table>
                            <tr>
                                <td>Фото:</td>
                                <td>
                                    <input type="file" name="userfile"/><br/>
                                    <input type="checkbox" name="show_in_bottom" checked/>Фото с лого<br/>
                                    <input type="checkbox" name="active" checked/> Активный<br/>
                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>"/>
                                    <input type="submit" value="Загрузить"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </td>
        </tr>
    </table>
<?php
include("footer.php");
?>