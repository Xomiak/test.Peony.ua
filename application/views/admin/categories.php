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
                    <div class="top_menu_link"><a href="/admin/categories/">Разделы</a></div>
                    <div class="top_menu_link"><a href="/admin/categories/add/">Добавить раздел</a></div>
                </div>

            
                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>ID</th>
                        <th>Название</th>
                        <th>Фото</th>
			             <th>Путь</th>
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
                            <td><?=$cat['id']?></td>
                            <td>
                                <a title="Перейти к редактированию статьи"
                                   href="/admin/categories/edit/<?=$cat['id']?>/"
                                   class="tooltip"><?= $cat['name'] ?><span><img
                                            width="125px" src="<?= $cat['image'] ?>"></span></a>

                            </td>
                            <td><img src="<?=$cat['image']?>" style="max-width:200px"/></td>
			    <td><a target="_blank" href="/<?=$cat['url']?>/">/<?=$cat['url']?>/</a></td>
                            <td><?=$cat['num']?></td>
                            <td>
                                <a href="/admin/categories/up/<?=$cat['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                                <a href="/admin/categories/down/<?=$cat['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a>
                            </td>
                            
                            <td>
                                <a href="/admin/categories/active/<?=$cat['id']?>/"><?php
                                if($cat['active'] == 1)
                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                else
                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                ?></a>
                                <a href="/admin/categories/edit/<?=$cat['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/categories/del/<?=$cat['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                        $sub = $this->mcats->getSubCategories($cat['id']);
                        if($sub)
                        {
                            $subcount = count($sub);
                            for($j = 0; $j < $subcount; $j++)
                            {
				$par = $cat;
                                $cat = $sub[$j];
                                ?>
                                <tr class="list">
                                    <td>&nbsp;└&nbsp;<a href="/admin/categories/edit/<?=$cat['id']?>/" title="Редактировать раздел"><?=$cat['name']?></a></td>
				    <td><a target="_blank" href="/<?=$par['url']?>/<?=$cat['url']?>/">/<?=$par['url']?>/<?=$cat['url']?>/</a></td>
                                    <td><?=$cat['num']?></td>
                                    <td><a href="/admin/categories/up/<?=$cat['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a><a href="/admin/categories/down/<?=$cat['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a></td>
                                    
                                    <td>
                                        <a href="/admin/categories/active/<?=$cat['id']?>/"><?php
                                        if($cat['active'] == 1)
                                            echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                        else
                                            echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                        ?></a>
                                        <a href="/admin/categories/edit/<?=$cat['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                        <a onclick="return confirm('Удалить?')" href="/admin/categories/del/<?=$cat['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a></td>
                                </tr>
                                <?php
                            }
                        }
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