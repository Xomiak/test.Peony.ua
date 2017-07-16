<?php
include("header.php");
?>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("menu.php"); ?></td>
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
                        <div class="top_menu_link"><a href="/admin/shop/add/">Добавить товар</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/import/">Импорт</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/export/">Экспорт</a></div>
                        <div class="top_menu_link"><a href="/admin/shop/createCheckedPrice/">Создать прайс</a></div>
                        <div class="top_menu_link">
                            <form method="post" action="/admin/shop/set_category/">
                                <input type="hidden" name="back" value="<?= $_SERVER['REQUEST_URI'] ?>"/>
                                Выбор раздела:
                                <SELECT name="category_id" onchange="submit();">

                                    <option value="all">Все</option>
                                    <?php
                                    $count = count($categories);
                                    for ($i = 0; $i < $count; $i++) {
                                        $cat = $categories[$i];
                                        echo '<option value="' . $cat['id'] . '"';
                                        if ($this->session->userdata('category_id') == $cat['id']) echo ' selected';
                                        echo '>' . $cat['name'] . '</option>';
                                        $subcats = $this->mcats->getSubCategories($cat['id']);
                                        if ($subcats) {
                                            $subcount = count($subcats);
                                            for ($j = 0; $j < $subcount; $j++) {
                                                $sub = $subcats[$j];
                                                echo '<option value="' . $sub['id'] . '"';
                                                if ($this->session->userdata('category_id') == $sub['id']) echo ' selected';
                                                echo '>&nbsp;└&nbsp;' . $sub['name'] . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </SELECT>
                            </form>
                        </div>
                        <div class="top_menu_link">
                            <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                                Поиск:<input type="text" name="search"
                                             value="<?php if (isset($_POST['search'])) echo $_POST['search']; ?>"
                                             style="width:500px"/>
                                <input type="submit" value="Искать"/>
                            </form>
                        </div>
                    </div>
                    <?=$msg?>

                    <?php if(isset($name)) {?>

                    Архив с фото: <input size="70" type="text" value="/upload/export/<?=$name?>.zip" /> [ <a href="/upload/export/<?=$name?>.zip">скачать</a> ]<br />
                    Прайс (xls): <input size="70" type="text" value="/upload/export/<?=$name?>.xls" /> [ <a href="/upload/export/<?=$name?>.xls">скачать</a> ]<br />
                    <BR /><BR />
                    <?php } ?>
                    <a href="/admin/shop/createCheckedPrice/?clear_cache=true">Очистить временные файлы</a>
                </div>
            </td>
        </tr>
    </table>
<?php
include("footer.php");
?>