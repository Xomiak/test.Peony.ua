<?php
include("application/views/admin/header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
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
            <table width="100%" cellpadding="1" cellspacing="1">
                <tr bgcolor="#EEEEEE">
                    <td><strong>Описание</strong></td>
                    <td><strong>Название</strong></td>
                    <td><strong>Значение</strong></td>
                </tr>
                <?php
                $count = count($options);
                for($i = 0; $i < $count; $i++)
                {
                    $option = $options[$i];
                    ?>
                    <tr class="list">
                        <td><a href="/admin/forum/options/edit/<?=$option['id']?>/" title="Перейти к редактированию"><?=$option['rus']?></a></td>
                        <td><?=$option['name']?></td>
                        <td><?=$option['value']?></td>
                        <td>                            
                            <a href="/admin/forum/options/edit/<?=$option['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <?php
                            if($option['required'] != 1)
                            {
                                ?>
                                <a onclick="return confirm('Удалить?')" href="/admin/forum/options/del/<?=$option['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                                <?php
                            }
                            ?>                            
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </td>
    </tr>
</table>
<?php
include("application/views/admin/footer.php");
?>