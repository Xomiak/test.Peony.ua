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
                    <div class="top_menu_link"><a href="/admin/gallery/">Галерея</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/add/">Добавить фотку</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/zip_import/">Импорт zip архива</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/categories/">Разделы галереи</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/categories/add/">Добавить раздел галереи</a></div>                    
                    <div class="top_menu_link"><a href="/admin/options/set_module/gallery/">Настройки галереи</a></div>
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="/admin/gallery/categories/edit/<?=$category['id']?>/" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $category['name']; ?>" /></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url']; else echo $category['url']; ?>" /></td>
                            <td><span class="helper"><img src="/img/question.png" alt="Подсказка" title="Подсказка" width="16px" height="16px" /> Название категории в транслите. Используется в адресе</span></td>
                        </tr>
                        <tr>
                            <td>Имя папки:</td>
                            <td><input type="text" name="folder" size="50" value="<?php if(isset($_POST['folder'])) echo $_POST['folder']; else echo $category['folder'];?>" /></td>
                            <td><span class="helper"><img src="/img/question.png" alt="Подсказка" title="Подсказка" width="16px" height="16px" /> Имя папки на сервере, в котором будут храниться фотографии</span></td>
                        </tr>
                        <tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $category['num']?>" size="3" /></td>
                        </tr>
                        <tr>
                            <td>Родительский раздел:</td>
                            <td>
                                <SELECT name="parent_id">
                                    <option value="0">нет</option>
                                    <?php
                                    if($categories)
                                    {
                                        $count = count($categories);
                                        for($i = 0; $i < $count; $i++)
                                        {
                                            $c = $categories[$i];
                                            echo '<option value="'.$c['id'].'"';
                                            if($c['id'] == $category['parent_id']) echo ' selected';
                                            echo '>'.$c['name'].'</option>';
                                        }
                                    }
                                    ?>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Фото раздела:</td>
                            <td>
                                <?php
                                if($category['image'] != '')
                                {
                                    echo '<img src="'.$category['image'].'" /><br /><input type="checkbox" name="image_del">Удалить<br />';
                                }
                                ?>
                                <input type="file" name="userfile" />
                                <input type="hidden" name="image" value="<?=$category['image']?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>title:</td>
                            <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title']; else echo $category['title']; ?>" /></td>
                        </tr>
                        <tr>
                            <td>keywords:</td>
                            <td><textarea name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords']; else echo $category['keywords']; ?></textarea></td>
                        </tr>
                        <tr>
                            <td>description:</td>
                            <td><textarea name="description"><?php if(isset($_POST['description'])) echo $_POST['description']; else echo $category['description']; ?></textarea></td>
                        </tr>
                        <tr>
                            <td>SEO текст:</td>
                            <td><textarea name="seo" class="tinymce"><?php if(isset($_POST['seo'])) echo $_POST['seo']; else echo $category['seo']; ?></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<?php if($category['active'] == '1') echo ' checked'?> /> Активный</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Сохранить" /></td>
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