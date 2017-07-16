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
                    <div class="top_menu_link"><a href="/admin/blogs/">Блоги</a></div>
                    
                </div>

                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>Логин</th>
                        <th>Название</th>
                        <th></th>
                        <th>Рейтинг</th>
                        <th>Действия</th>
    
                    </tr>
                    <?php
                    $count = count($blogs);
                    for($i = 0; $i < $count; $i++)
                    {
                        $blog = $blogs[$i];
                        $user = $this->users->getUserByLogin($blog['login']);
                        ?>
                        <tr class="list">
                            <td><a href="/admin/users/edit/<?=$user['id']?>/" title="Перейти к редактированию пользователя"><?=$blog['login']?></a></td>
                            <td><a href="/admin/blogs/edit/<?=$blog['id']?>/" title="Перейти к редактированию"><?=$blog['name']?></a></td>
                            <td><strong><a href="/admin/blogs/blog/<?=$blog['id']?>/">Просмотр записей</a></strong></td>
                            <td><?=$blog['rating']?></td>
                            
                            <td>
                                <a href="/admin/blogs/active/<?=$blog['id']?>/"><?php
                                if($blog['active'] == 1)
                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                else
                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                ?></a>
                                <a href="/admin/blogs/edit/<?=$blog['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/blogs/del/<?=$blog['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
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