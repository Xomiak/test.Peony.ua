 <?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <h2>"<?=$blog['name']?>"</h2>
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
                    <td><strong>Дата</strong></td>
                    <td><strong>Название</strong></td>
                    <td><strong>Краткое описание</strong></td>
                    <td><strong>Рейтинг</strong></td>

                </tr>
                <?php
                $count = count($blogs_content);
                for($i = 0; $i < $count; $i++)
                {
                    $bc = $blogs_content[$i];
                    
                    ?>
                    <tr class="list">
                        <td valign="top"><?=$bc['date']?> <?=$bc['time']?></a></td>
                        <td valign="top"><a href="/admin/blogs/blog_content_edit/<?=$bc['id']?>/" title="Перейти к редактированию"><?=$bc['name']?></a></td>
                        <td valign="top"><?=$bc['short_content']?></td>
                        <td valign="top"><?=$bc['rating']?></td>
                        
                        <td valign="top">
                            <a href="/admin/blogs/blog_content_active/<?=$bc['id']?>/">
                            <?php
                            if($bc['active'] == 1)
                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                            else
                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                            ?>
                            </a>
                            <a href="/admin/blogs/blog_content_edit/<?=$bc['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <a onclick="return confirm('Удалить?')" href="/admin/blogs/blog_content_del/<?=$bc['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
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