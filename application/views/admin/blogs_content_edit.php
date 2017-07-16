<?php
include("header.php");
?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <table width="100%" cellpadding="0" cellspacing="0" height="20px" bgcolor="#DDDDDD">
                <tr>
                    <td width="11px" height="20px"><img src="/img/left-hmenu.jpg" /></td>
                    <td><a href="/admin/blogs/">Блоги</a></td>
                    <td><a href="/admin/blogs/blog/<?=$user_blog['id']?>/">Блог пользователя <?=$user_blog['login']?></a></td>

                    <td><a href="/admin/blogs/invitation_codes/">Пригласительные</a></td>
                    <td><a href="/admin/blogs/invitation_code_add/">Создать приглашение</a></td>
                    
                    <td><a href="/admin/blogs/options/">Опции</a></td>
                    <td><a href="/admin/blogs/options/add/">Добавить опцию</a></td>
                </tr>
            </table>
            <br />
            <strong><font color="Red"><?=$err?></font></strong>
            <form enctype="multipart/form-data" action="/admin/blogs/blog_content_edit/<?=$blog['id']?>/" method="post">
                <table>
                    <tr>
                        <td>Название *:</td>
                        <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $blog['name'];?>" /></td>
                    </tr>                    
                    <tr>
                        <td>Логин:</td>
                        <td><input disabled="disabled" type="text" name="login" size="50" value="<?=$blog['login']?>" /></td>
                    </tr>                    
                    <tr>
                        <td valign="top">Фото:</td>
                        <td>
                            <?php
                            if($blog['image'] != '')
                            {
                                echo '<img src="/upload/blogs/normal/'.$blog['image'].'" /><br /><input type="checkbox" name="image_del">Удалить<br />';
                            }
                            ?>
                            <input type="file" name="userfile" />
                            <input type="hidden" name="image" value="<?=$blog['image']?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>Краткое описание:</td>
                        <td><textarea name="short_content" class="tinymce"><?php if(isset($_POST['short_content'])) echo $_POST['short_content']; else echo $blog['short_content'];?></textarea></td>
                    </tr>
                    <tr>
                        <td>Контент:</td>
                        <td><textarea name="content" class="tinymce"><?php if(isset($_POST['content'])) echo $_POST['content']; else echo $blog['content'];?></textarea></td>
                    </tr>
                    <tr>
                        <td>Рейтинг:</td>
                        <td><input type="text" name="rating" size="5" value="<?php if(isset($_POST['rating'])) echo $_POST['rating']; else echo $blog['rating'];?>" /></td>
                    </tr>
                    <tr>
                        <td>title:</td>
                        <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title']; else echo $blog['title'];?>" /></td>
                    </tr>
                    <tr>
                        <td>keywords:</td>
                        <td><textarea name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords']; else echo $blog['keywords'];?></textarea></td>
                    </tr>
                    <tr>
                        <td>description:</td>
                        <td><textarea name="description"><?php if(isset($_POST['description'])) echo $_POST['description']; else echo $blog['description'];?></textarea></td>
                    </tr>
                    <tr>
                        <td>robots:</td>
                        <td><input type="text" name="robots" size="50" value="<?php if(isset($_POST['robots'])) echo $_POST['robots']; else echo $blog['robots'];?>" /></td>
                    </tr>
                    <tr>
                        <td>SEO текст:</td>
                        <td><textarea name="seo" class="tinymce"><?php if(isset($_POST['seo'])) echo $_POST['seo']; else echo $blog['seo'];?></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="checkbox" name="active"<? if($blog['active']==1) echo ' checked'?> /> Активный</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Изменить" /></td>
                    </tr>
                </table>
            </form>
            
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>