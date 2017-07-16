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
                    <?php
                    $stgi = false;
                    $stgi_json = false;
                    if(isset($_GET['stgi'])){
                        $stgi_json = urldecode($_GET['stgi']);
                        $stgi = json_decode($stgi_json, true);
                    }
                    $base_ids = false;
                    $base_ids_json = false;
                    if(isset($_GET['base_ids'])){
                        $base_ids_json = urldecode($_GET['base_ids']);
                        $base_ids = json_decode($base_ids_json);
                    }
                    $warehouse = false;
                    $warehouse_json = false;
                    if(isset($_GET['warehouse'])){
                        $warehouse_json = urldecode($_GET['warehouse']);
                        $warehouse = json_decode($warehouse_json, true);
                    }
                    ?>
                    <form enctype="multipart/form-data" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        
                        <input type="hidden" name="num" value="<?= $num ?>"/>
                        <table>
                            <tr>
                                <td>Название *:</td>
                                <td><input required type="text" name="name" size="50"
                                           value="<?php if (isset($_GET['name'])) echo $_GET['name']; ?>"/></td>
                                <td>«»</td>
                            </tr>
                            <tr>
                                <td>Цвет:</td>
                                <td>
                                    <input type="text" name="color"
                                           value="<?php if (isset($_GET['color'])) echo urldecode($_GET['color']); ?>"/>
                                    <!-- <SELECT name="color">
				    <?php
                                    $count = count($color);
                                    for ($i = 0; $i < $count; $i++) {
                                        ?>
					<option value="<?= $color[$i]['name'] ?>"><?= $color[$i]['name'] ?></option>
					<?php
                                    }
                                    ?>
				</SELECT> -->
                                </td>

                            </tr>

                            <tr>
                                <td>Артикул:</td>
                                <td><input required type="text" name="articul" size="50"
                                           value="<?php if (isset($_GET['articul'])) echo $_GET['articul']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Цена:</td>
                                <td><input required type="text" name="price" size="10"
                                           value="<?php if (isset($_GET['price'])) echo $_GET['price']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Скидка:</td>
                                <td>
                                    <select name="discount">
                                        <option value="0">Нет</option>
                                        <?php
                                        $discount = 5;
                                        while ($discount < 100) {
                                            echo '<option value="' . $discount . '">' . $discount . '%</option>';
                                            $discount += 5;
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="checkbox" name="sale"/> SALE</td>
                            </tr>
                            <tr>
                                <td>Раздел *:</td>
                                <td>
                                    <SELECT style="width: 200px; height: 300px;" required
                                            name="category_id[]"<?php if ($article_in_many_categories != '0') echo ' multiple=""'; ?>>
                                        <!--SELECT name="category_id"-->
                                        <option></option>
                                        <?php
                                        $count = count($categories);
                                        for ($i = 0; $i < $count; $i++) {
                                            $cat = $categories[$i];
                                            echo '<option value="' . $cat['id'] . '"';
                                            if (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) echo ' selected';
                                            else if ($this->session->userdata('category_id') == $cat['id']) echo ' selected';

                                            echo '>' . $cat['name'] . '</option>';
                                            $subs = $this->mcats->getSubCategories($cat['id']);
                                            if ($subs) {
                                                $subcount = count($subs);
                                                for ($j = 0; $j < $subcount; $j++) {
                                                    $sub = $subs[$j];
                                                    echo '<option value="' . $sub['id'] . '"';
                                                    if (isset($_GET['category_id']) && $_GET['category_id'] == $sub['id']) echo ' selected';
                                                    else if ($this->session->userdata('category_id') == $sub['id']) echo ' selected';

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
                                                    if($season != '')
                                                        echo '<option value="'.$season.'">'.$season.'</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Фото:</td>
                                <td><input type="file" name="userfile"/><br/><a target="_blank" href="/admin/images/">Загрузить
                                        доп. фотографии</a></td>
                            </tr>

                            <tr>
                                <td>Youtube:</td>
                                <td><input type="text" name="youtube" size="50"
                                           value="<?php if (isset($_GET['youtube'])) echo $_GET['youtube']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Краткое описание:</td>
                                <td><textarea name="short_content"
                                              class="ckeditor"><?php if (isset($_GET['short_content'])) echo $_GET['short_content']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Контент:</td>
                                <td><textarea name="content" class="ckeditor"
                                              rows="30"><?php if (isset($_GET['content'])) echo $_GET['content']; ?></textarea>
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

                            <tr>
                                <td>Размеры:</td>
                                <td>
                                    <?php
                                    $sizes = getOption('sizes');
                                    $sizes = explode('|', $sizes);
                                    ?>
                                    <SELECT name="razmer[]" multiple="" style="height: 150px">
                                        <?php
                                        foreach ($sizes as $size){
                                            ?>
                                            <option value="<?= $size ?>"
                                                <?php
                                                if(isset($stgi[$size])) echo " selected";
                                                ?>
                                            ><?= $size ?></option>
                                            <?php
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                                <?php if($warehouse_json) echo '<td>'.$warehouse_json.'</td>'; ?>
                            </tr>



                            <tr>
                                <td>Ткань:</td>
                                <td>
                                    <!--SELECT name="tkan">
				    <?php
                                    /*
                                        $count = count($tkan);
                                        for($i = 0; $i < $count; $i++)
                                        {
                                        ?>
                                        <option value="<?=$tkan[$i]['name']?>"><?=$tkan[$i]['name']?></option>
                                        <?php
                                        }
                                    */
                                    ?>
				</SELECT-->
                                    <input name="tkan"
                                           value="<?php if (isset($_GET['tkan'])) echo urldecode($_GET['tkan']); ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>Состав:</td>
                                <td>
                                    <textarea
                                        name="sostav"><?php if (isset($_POST['sostav'])) echo $_POST['sostav'];  ?></textarea>
                                </td>
                            </tr>

                            <tr>
                                <td>Длина:</td>
                                <td>
                                    <input name="height"
                                           value="<?php if (isset($_GET['height'])) echo urldecode($_GET['height']); ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>Длина рукава:</td>
                                <td>
                                    <input name="hand_height"
                                           value="<?php if (isset($_GET['hand_height'])) echo urldecode($_GET['hand_height']); ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>Тэги:</td>
                                <td><textarea
                                        name="tags"><?php if (isset($_POST['tags'])) echo $_POST['tags']; ?></textarea>
                                </td>
                            </tr>


                            <tr>
                                <td>На главной:</td>
                                <td><input type="checkbox" name="glavnoe" checked="checked"/></td>
                            </tr>

                            <tr>
                                <td>Нужно написать описание:</td>
                                <td>
                                    <input id="need_text" type="checkbox" name="need_text"/>
                                </td>
                            </tr>
                            <tr id="tr-copywriter" style="display: none">
                                <td>Уведомить копирайтера:</td>
                                <td>
                                    <input id="mail_to_copywraiter" type="checkbox" name="mail_to_copywraiter"/>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"><input type="checkbox" name="mailer_new"/> В рассылку новинок</td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="checkbox" name="active" checked/> Активный</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" name="social_buttons" value="true"/>
                                    <input type="submit" value="Добавить"/>
                                </td>
                            </tr>
                        </table>
                        <script>
                            $(document).ready(function () {
                                $("#need_text").change(function () {
                                    if($(this).is(":checked")) {
                                        $("#tr-copywriter").show();
                                        $("#mail_to_copywraiter").attr("checked", true);
                                    } else {
                                        $("#tr-copywriter").hide();
                                        $("#mail_to_copywraiter").attr("checked", false);
                                    }
                                });
                            })
                        </script>
                    </form>
                </div>
            </td>
        </tr>
    </table>
<?php
include("footer.php");
?>