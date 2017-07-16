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
                </div>            <table width="100%" cellpadding="1" cellspacing="1">
                <tr bgcolor="#EEEEEE">
                    <th>Фото раздела</th>
                    <th>Название</th>
                    <th>Позиция</th>
                    <th>Вверх/Вниз</th>
                    <th>Действия</th>
                </tr>
                <?php
                $count = count($categories);
                for($i = 0; $i < $count; $i++)
                {
                    $cat = $categories[$i];
                    ?>
                    <tr class="list">
                        <td>
                            <?php
                            if($cat['image'] != '')
                            {                                
                            ?>
                            <a href="/admin/gallery/categories/edit/<?=$cat['id']?>/" title="Редактировать раздел">
                                <img src="<?=$cat['image']?>" border="0" />
                            </a>
                            <?php
                            }
                            ?>
                        </td>
                        <td><a class="name" href="/admin/gallery/categories/edit/<?=$cat['id']?>/" title="Редактировать раздел"><?=$cat['name']?></a></td>
                        <td><?=$cat['num']?></td>
                        <td><a href="/admin/gallery/categories/up/<?=$cat['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                        <a href="/admin/gallery/categories/down/<?=$cat['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a></td>
                        
                        <td>
                            <a href="/admin/gallery/categories/active/<?=$cat['id']?>/"><?php
                            if($cat['active'] == 1)
                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                            else
                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                            ?></a>
                            <a href="/admin/gallery/categories/edit/<?=$cat['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <a onclick="return confirm('Вы действительно хотите удалить данный раздел? Все фотографии данного раздела будут безвозвратно удалены!')" href="/admin/gallery/categories/del/<?=$cat['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a></td>
                    </tr>
                    <?php
                    $subs = $this->gallery->getSubCategories($cat['id']);
                    if($subs)
                    {
                        $scount = count($subs);
                        for($j = 0; $j < $scount; $j++)
                        {
                            $s = $subs[$j];
                            ?>
                            <tr class="list">
                                <td>
                                    <?php
                                    if($s['image'] != '')
                                    {                                
                                    ?>
                                    <a href="/admin/gallery/categories/edit/<?=$s['id']?>/" title="Редактировать раздел">
                                        <img src="<?=$s['image']?>" border="0" />
                                    </a>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>&nbsp;└&nbsp;<a href="/admin/gallery/categories/edit/<?=$s['id']?>/" title="Редактировать раздел"><?=$s['name']?></a></td>
                                <td><?=$s['num']?></td>
                                <td><a href="/admin/gallery/categories/up/<?=$s['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                                <a href="/admin/gallery/categories/down/<?=$s['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a></td>
                                
                                <td>
                                    <a href="/admin/gallery/categories/active/<?=$s['id']?>/"><?php
                                    if($s['active'] == 1)
                                        echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                    else
                                        echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                    ?></a>
                                    <a href="/admin/gallery/categories/edit/<?=$s['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                    <a onclick="return confirm('Удалить?')" href="/admin/gallery/categories/del/<?=$s['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
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