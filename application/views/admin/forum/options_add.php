<?php
include("application/views/admin/header.php");
?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("application/views/admin/menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <table width="100%" cellpadding="0" cellspacing="0" height="20px" bgcolor="#DDDDDD">
                <tr>
                    <td width="11px" height="20px"><img src="/img/left-hmenu.jpg" /></td>
                    <td><a href="/admin/forum/sections/">Разделы</a></td>
                    <td><a href="/admin/forum/sections/add/">Добавить раздел</a></td>
                    <td><a href="/admin/forum/topics/">Темы</a></td>
                    <td><a href="/admin/forum/topics/add/">Добавить тему</a></td>
                    <td><a href="/admin/forum/messages/">Сообщения</a></td>
                    <td><a href="/admin/forum/options/">Настройки</a></td>
                    <td><a href="/admin/forum/options/add/">Добавить опцию</a></td>
                </tr>
            </table>
            <br />
             <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                <table>
                    <tr>
                        <td>Описание *:</td>
                        <td>
                            <input type="text" name="rus" size="50" value="<?php if(isset($_POST['rus'])) echo $_POST['rus'];?>" />
                            <?php
                            if(isset($err['rus']))
                            {
                                ?>
                                <div class="error"><?=$err['rus']?></div>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Название *:</td>
                        <td>
                            <input type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" />
                            <?php
                            if(isset($err['name']))
                            {
                                ?>
                                <div class="error"><?=$err['name']?></div>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Значение:</td>
                        <td>
                            <textarea name="value" cols="50" rows="5"><?php if(isset($_POST['value'])) echo $_POST['value'];?></textarea>
                            <?php
                            if(isset($err['value']))
                            {
                                ?>
                                <div class="error"><?=$err['value']?></div>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Описание значений:</td>
                        <td>
                            <textarea name="adding"><?php if(isset($_POST['adding'])) echo $_POST['adding'];?></textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td valign="top">Обязательный параметр:</td>
                        <td valign="top">
                            <input type="checkbox" name="required" />
                        </td>
                        <td><i>Если выбрать "Обязательный параметр", то его название нельзя будет поменять и сам параметр нельзя будет удалить!</i></td>
                    </tr>
                    
                    <tr>
                        <td colspan="2"><input type="submit" value="Добавить" /></td>
                    </tr>
                </table>
            </form>
            
        </td>
    </tr>
</table>
<?php
include("application/views/admin/footer.php");
?>