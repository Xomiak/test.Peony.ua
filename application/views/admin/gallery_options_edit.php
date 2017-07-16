<?php
include("header.php");
?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <br />
            <form action="/admin/gallery/options/edit/" method="post">
                <table>                    
                    <tr>
                        <td>title:</td>
                        <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title']; else echo $main['title'];?>" /></td>
                    </tr>
                    <tr>
                        <td>keywords:</td>
                        <td><textarea class="mceNoEditor" name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords']; else echo $main['keywords'];?></textarea></td>
                    </tr>
                    <tr>
                        <td>description:</td>
                        <td><textarea class="mceNoEditor" name="description"><?php if(isset($_POST['description'])) echo $_POST['description']; else echo $main['description'];?></textarea></td>
                    </tr>
                    <tr>
                        <td>Заголовок H1:</td>
                        <td><input type="text" name="h1" size="50" value="<?php if(isset($_POST['h1'])) echo $_POST['h1']; else echo $main['h1'];?>" /></td>
                    </tr>
                    <tr>
                        <td>robots:</td>
                        <td><input type="text" name="robots" size="50" value="<?php if(isset($_POST['robots'])) echo $_POST['robots']; else echo $main['robots'];?>" /></td>
                    </tr>
                    <tr>
                        <td>Кол-во ячеек:</td>
                        <td><input type="text" name="cols" size="5" value="<?php if(isset($_POST['cols'])) echo $_POST['cols']; else echo $main['cols'];?>" /></td>
                    </tr>
                    <tr>
                        <td>Кол-во фото на странице:</td>
                        <td><input type="text" name="pagination" size="5" value="<?php if(isset($_POST['pagination'])) echo $_POST['pagination']; else echo $main['pagination'];?>" /></td>
                    </tr>
                    <tr>
                        <td>SEO текст:</td>
                        <td><textarea name="seo" class="tinymce"><?php if(isset($_POST['seo'])) echo $_POST['seo']; else echo $main['seo'];?></textarea></td>
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