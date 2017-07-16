<?php
include("header.php");
?>
    <table width="100%" cellpadding="0" cellspacing="0">
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
                        <div class="top_menu_link"><a href="/admin/categories/">Разделы</a></div>
                        <div class="top_menu_link"><a href="/admin/categories/add/">Добавить раздел</a></div>
                    </div>

                    <strong><font color="Red"><?= $err ?></font></strong>
                    <form enctype="multipart/form-data" action="/admin/categories/edit/<?= $cat['id'] ?>/"
                          method="post">
                        <table>
                            <tr>
                                <td>Название *:</td>
                                <td><input required type="text" name="name" size="50"
                                           value="<?php if (isset($_POST['name'])) echo $_POST['name']; else echo $cat['name']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Название 1-й единицы:</td>
                                <td><input type="text" name="name_one" size="50"
                                           value="<?php if (isset($_POST['name_one'])) echo $_POST['name_one']; else echo $cat['name_one']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>url:</td>
                                <td><input type="text" name="url" size="50"
                                           value="<?php if (isset($_POST['url'])) echo $_POST['url']; else echo $cat['url']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Тип раздела:</td>
                                <td>
                                    <SELECT name="type">
                                        <option value="articles"<? if ($cat['type'] == 'articles') echo ' selected'; ?>>
                                            Статьи
                                        </option>
                                        <option value="shop"<? if ($cat['type'] == 'shop') echo ' selected'; ?>>Товары
                                        </option>
                                    </SELECT>
                                </td>
                            </tr>
                            <tr>
                                <td>Скидка:</td>
                                <td>
                                    <select name="discount">
                                        <option value="0">Нет</option>
                                        <?php
                                        $discount = 5;
                                        while ($discount < 100) {
                                            echo '<option value="' . $discount . '"';
                                            if ($cat['discount'] == $discount) echo ' selected';
                                            echo '>' . $discount . '%</option>';
                                            $discount += 5;
                                        }
                                        ?>
                                    </select>
                                    <input type="checkbox" name="clear_discount"/> Очистить акцию у товаров этого
                                    раздела
                                </td>
                            </tr>
                            <tr>
                                <td>Старт акции:</td>
                                <td><input class="date" placeholder="В формате: 2015-09-28" type="text"
                                           name="akciya_start" size="50"
                                           value="<?php if (isset($_POST['akciya_start'])) echo $_POST['akciya_start']; else echo $cat['akciya_start']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Конец акции:</td>
                                <td><input class="date" placeholder="В формате: 2015-09-28" type="text"
                                           name="akciya_end" size="50"
                                           value="<?php if (isset($_POST['akciya_end'])) echo $_POST['akciya_end']; else echo $cat['akciya_end']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Позиция:</td>
                                <td><input type="text" name="num"
                                           value="<?php if (isset($_POST['num'])) echo $_POST['num']; else echo $cat['num'] ?>"
                                           size="3"/></td>
                            </tr>
                            <tr>
                                <td>Родитель:</td>
                                <td>
                                    <SELECT name="parent">
                                        <option value="0"<? if ($cat['parent'] == 0) echo ' selected'; ?>>нет</option>
                                        <?php
                                        $count = count($categories);
                                        for ($i = 0; $i < $count; $i++) {
                                            echo '<OPTION value="' . $categories[$i]['id'] . '"';
                                            if ($cat['parent'] == $categories[$i]['id']) echo ' selected';
                                            echo '>' . $categories[$i]['name'] . '</OPTION>';
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                            </tr>
                            <tr>
                                <td>Шаблон:</td>
                                <td>
                                    <SELECT name="template" required>
                                        <option></option>
                                        <?php
                                        $this->load->helper('file');
                                        $files = get_filenames('application/views/templates/');
                                        $count = count($files);
                                        for ($i = 0; $i < $count; $i++) {
                                            echo '<option value="' . $files[$i] . '"';
                                            if (isset($_POST['template']) && $_POST['template'] == $files[$i]) echo ' selected';
                                            elseif ($cat['template'] == $files[$i]) echo ' selected';
                                            echo '>' . $files[$i] . '</option>';
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                            </tr>

                            <tr>
                                <td>Шаблон контента:</td>
                                <td>
                                    <SELECT name="content_template">
                                        <option></option>
                                        <?php
                                        $this->load->helper('file');
                                        $files = get_filenames('application/views/templates/');
                                        $count = count($files);
                                        for ($i = 0; $i < $count; $i++) {
                                            echo '<option value="' . $files[$i] . '"';
                                            if (isset($_POST['content_template']) && $_POST['content_template'] == $files[$i]) echo ' selected';
                                            elseif ($cat['content_template'] == $files[$i]) echo ' selected';
                                            echo '>' . $files[$i] . '</option>';
                                        }
                                        ?>
                                    </SELECT>
                                </td>
                            </tr>

                            <tr>
                                <td valign="top">Лого:</td>
                                <td>
                                    <?php
                                    if ($cat['image'] != '') {
                                        echo '<img src="' . $cat['image'] . '" /><br /><input type="checkbox" name="image_del">Удалить<br />';
                                    }
                                    ?>
                                    <input type="file" name="userfile"/>
                                    <input type="hidden" name="image" value="<?= $cat['image'] ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <td>h1:</td>
                                <td><input type="text" name="h1" size="50"
                                           value="<?php if (isset($_POST['h1'])) echo $_POST['h1']; else echo $cat['h1']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>title:</td>
                                <td><input type="text" name="title" size="50"
                                           value="<?php if (isset($_POST['title'])) echo $_POST['title']; else echo $cat['title']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>keywords:</td>
                                <td><textarea
                                        name="keywords"><?php if (isset($_POST['keywords'])) echo $_POST['keywords']; else echo $cat['keywords']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>description:</td>
                                <td><textarea
                                        name="description"><?php if (isset($_POST['description'])) echo $_POST['description']; else echo $cat['description']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>SEO текст:</td>
                                <td><textarea name="seo"
                                              class="ckeditor"><?php if (isset($_POST['seo'])) echo $_POST['seo']; else echo $cat['seo']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Текст для товаров на Проме:</td>
                                <td><textarea name="prom_content"
                                              class="ckeditor"><?php if (isset($_POST['prom_content'])) echo $_POST['prom_content']; else echo $cat['prom_content']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="checkbox"
                                                       name="active"<? if ($cat['active'] == 1) echo ' checked' ?> />
                                    Активный
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="submit" value="Изменить"/></td>
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