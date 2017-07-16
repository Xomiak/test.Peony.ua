<?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <table width="100%" cellpadding="0" cellspacing="0" height="20px" bgcolor="#DDDDDD">
                <tr>
                    <td width="11px" height="20px"><img src="/img/left-hmenu.jpg" /></td>
                    <td><a href="/admin/blogs/">Блоги</a></td>
                    <td><a href="/admin/blogs/invitation_codes/">Пригласительные</a></td>
                    <td><a href="/admin/blogs/invitation_code_add/">Создать приглашение</a></td>
                    <td><a href="/admin/blogs/options/">Опции</a></td>
                    <td><a href="/admin/blogs/options/add/">Добавить опцию</a></td>
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
                        <td><a href="/admin/blogs/options/edit/<?=$option['id']?>/" title="Перейти к редактированию"><?=$option['rus']?></a></td>
                        <td><?=$option['name']?></td>
                        <td><?=$option['value']?></td>
                        <td>                            
                            <a href="/admin/blogs/options/edit/<?=$option['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <?php
                            if($option['required'] != 1)
                            {
                                ?>
                                <a onclick="return confirm('Удалить?')" href="/admin/blogs/options/del/<?=$option['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
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
include("footer.php");
?>