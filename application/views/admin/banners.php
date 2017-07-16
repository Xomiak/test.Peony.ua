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

            <table width="100%" cellpadding="1" cellspacing="1">
                <tr bgcolor="#EEEEEE">
                    <th>Название</th>
                    <th>Баннер</th>
                    <th>Позиция</th>
                    <th>Счётчик</th>
                    <th>Действия</th>
                </tr>
                <?php
                $count = count($banners);
                for($i = 0; $i < $count; $i++)
                {
                    $banner = $banners[$i];
                    ?>
                    <tr class="list">
                        <td><a href="/admin/banners/edit/<?=$banner['id']?>/" title="Перейти к редактированию"><?=$banner['name']?></a></td>
                        <td><img src="<?=$banner['image']?>" style="max-width: 800px" /></td>
                        <td><?=$banner['position']?></td>
                        <td><?=$banner['count']?></td>
                        <td>
                            <a href="/admin/banners/active/<?=$banner['id']?>/"><?php
                            if($banner['active'] == 1)
                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" />';
                            else
                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" />';
                            ?></a>
                            <a href="/admin/banners/edit/<?=$banner['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <a onclick="return confirm('Удалить?')" href="/admin/banners/del/<?=$banner['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
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