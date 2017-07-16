<?php
include("header.php");
?>
    <table width="100%" height="100%" cellpadding="0" cellspacing="0">
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
                        <div class="top_menu_link"<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>><a href="/admin/shop/add/">Добавить товар</a></div>
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/import/">Импорт</a></div>-->
                        <!--                        <div class="top_menu_link"><a href="/admin/shop/export/">Экспорт</a></div>-->
                        <div class="top_menu_link"<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>><a href="/admin/shop/currencies/">Валюты</a></div>
                        <div class="top_menu_link"<?php if(userdata('type') != 'admin') echo ' style="display:none;"';?>><a href="/admin/shop/createCheckedPrice/">Создать прайс</a></div>
                        <div class="top_menu_link"><a href="/import/yandex_market.xml">YML (XML)</a></div>
                    </div>

                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr bgcolor="#EEEEEE">

                            <th>Название</th>
                            <th>Код</th>
                            <th>Обозначение</th>
                            <th>Коэффициент</th>
                            <th>Основная</th>
                            <th>Автообновляемая</th>
                            <th>Действия</th>

                        </tr>
                        <?php
                        $count = count($currencies);
                        for($i = 0; $i < $count; $i++)
                        {
                            $cur = $currencies[$i];

                            if(isset($_GET['edit']) && $_GET['edit'] == $cur['id']){        // РЕДАКТИРОВАНИЕ
                                ?>
                                <tr class="list">
                                    <form method="post">
                                    <td><input type="text" name="name" value="<?=$cur['name']?>" /></td>
                                    <td><input type="text" name="code" value="<?=$cur['code']?>" /></td>
                                    <td><input type="text" name="symb" value="<?=$cur['symb']?>" /></td>
                                    <td><input type="text" name="value" value="<?=$cur['value']?>" /></td>
                                    <td><input type="checkbox" name="main" <?php if($cur['main'] == 1) echo ' checked'?> /></td>
                                    <td><input type="checkbox" name="auto_update" <?php if($cur['auto_update'] == 1) echo ' checked'?> /><input type="text" name="auto_update_plus" value="<?=$cur['auto_update_plus']?>"></td>

                                    <td>
                                        <input type="submit" name="save" value="Сохранить" />
                                    </td>
                                    </form>
                                </tr>
                                <?php
                            } else {            // ВЫВОД
                                ?>
                                <tr class="list">
                                    <td><a href="/admin/shop/currencies/?edit=<?= $cur['id'] ?>"
                                           title="Перейти к редактированию"><?= $cur['name'] ?></a></td>
                                    <td><?= $cur['code'] ?></td>
                                    <td><?= $cur['symb'] ?></td>
                                    <td><?= $cur['value'] ?></td>
                                    <td><?= $cur['main'] ?></td>
                                    <td>
                                        <?= $cur['auto_update'] ?>
                                        <?php
                                        if($cur['auto_update'] == 1) echo ' Надбавка: '.$cur['auto_update_plus'];
                                        ?>
                                    </td>

                                    <td>
<!--                                        <a href="/admin/shop/currencies/active/--><?//= $cur['id'] ?><!--/">--><?php
//
//                                            if ($cur['active'] == 1)
//                                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
//                                            else
//                                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
//                                            ?><!--</a>-->
                                        <a href="/admin/shop/currencies/?edit=<?= $cur['id'] ?>"><img
                                                src="/img/edit.png" width="16px" height="16px" border="0"
                                                title="Редактировать"/></a>
<!--                                        <a onclick="return confirm('Удалить?')"-->
<!--                                           href="/admin/shop/currencies/del/--><?//= $cur['id'] ?><!--/"><img src="/img/del.png"-->
<!--                                                                                                     border="0"-->
<!--                                                                                                     alt="Удалить"-->
<!--                                                                                                     title="Удалить"/></a>-->
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                </div>
            </td>
        </tr>
    </table>
<?php
include("footer.php");
?>