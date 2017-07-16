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
                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
            
            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/banners/">Баннеры</a></div>
                    <div class="top_menu_link"><a href="/admin/banners/add/">Добавить баннер</a></div>
                </div>
                <form action="/admin/banners/add/" method="post" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Загрузить баннер:</td>
                            <td><input type="file" name="userfile" /></td>
                        </tr>
                        <tr>
                            <td>Контент:</td>
                            <td><textarea name="content" class="ckeditor"><?php if(isset($_POST['content'])) echo $_POST['content'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" value="<?php if(isset($_POST['url'])) echo $_POST['url'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>NUM:</td>
                            <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Позиция:</td>
                            <td>
                                <SELECT name="position" required>
                                    <option></option>
                                    <option value="main1"<?php if(isset($_POST['position']) && $_POST['position'] == 'main1') echo ' selected'; ?>>Главная 1 (435px×146px)</option>
                                    <option value="main2"<?php if(isset($_POST['position']) && $_POST['position'] == 'main2') echo ' selected'; ?>>Главная 2 (435px×146px)</option>
                                    <option value="main3"<?php if(isset($_POST['position']) && $_POST['position'] == 'main3') echo ' selected'; ?>>Главная 3 (435px×146px)</option>
                                    <option value="main4"<?php if(isset($_POST['position']) && $_POST['position'] == 'main4') echo ' selected'; ?>>Главная 4 (435px×146px)</option>
                                    <!--option value="partners"<?php if(isset($_POST['position']) && $_POST['position'] == 'partners') echo ' selected'; ?>>Партнёры</option>
                                    <option value="generals"<?php if(isset($_POST['position']) && $_POST['position'] == 'generals') echo ' selected'; ?>>Генеральные спонсоры</option-->
                                    <option value="slider"<?php if(isset($_POST['position']) && $_POST['position'] == 'slider') echo ' selected'; ?>>Слайдер</option>
                                    <option value="left"<?php if(isset($_POST['position']) && $_POST['position'] == 'left') echo ' selected'; ?>>Левый баннер</option>
                                </SELECT>                                
                            </td>
                            <td>
                                <span class="helper">
                                    <img width="16px" height="16px" title="Подсказка" alt="Подсказка" src="/img/question.png">
                                    Если выбран слайдер, то размер: 960x390
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Счётчик:</td>
                            <td><input type="text" name="count" value="<?php if(isset($_POST['count'])) echo $_POST['count']; else echo '0'; ?>" size="7" /></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active" checked /> Активный</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Добавить" /></td>
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