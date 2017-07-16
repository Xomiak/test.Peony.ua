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
                        <td>В теме:</td>
                        <td><a href="/admin/forum/topics/edit/<?=$topic['id']?>/" title="Перейти к редактированию темы"><?=$topic['name']?></a></td>
                    </tr>
                    <tr>
                        <td>В разделе:</td>
                        <td>
                            <a href="/admin/forum/section/edit/<?=$section['id']?>/" title="Перейти к редактированию раздела"><?=$section['name']?></a><?php
                            if($subsection)
                            {
                                ?>&nbsp;→&nbsp;<a href="/admin/forum/section/edit/<?=$subsection['id']?>/" title="Перейти к редактированию раздела"><?=$subsection['name']?></a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Логин:</td>
                        <td><a href="/admin/users/edit/<?=$user['id']?>/" title="Перейти к редактированию пользователя"><?=$user['login']?></a></td>
                    </tr>
                    <tr>
                        <td>Дата:</td>
                        <td><?=$message['date']?> <?=$message['time']?></td>
                    </tr>
                    <tr>
                        <td>Главное в теме:</td>
                        <td>
                            <?php
                            if($message['topic_message'] == 1) echo 'да';
                            else echo 'нет';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Сообщение:</td>
                        <td>
                            <textarea name="message" class="tinymce"><?=$message['message']?></textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2"><input type="checkbox" name="active"<?php if($section['active'] == 1) echo ' checked' ?> /> Активный</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="save" value="Сохранить" />&nbsp;<input type="submit" name="save_and_edit" value="Сохранить и вернуться к редактированию" /></td>
                    </tr>
                </table>
            </form>
            
        </td>
    </tr>
</table>
<?php
include("application/views/admin/footer.php");
?>