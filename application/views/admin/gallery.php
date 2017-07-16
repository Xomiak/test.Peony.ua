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
                    <div class="top_menu_link">                                                
                        <form method="post" action="/admin/gallery/set_category/" style="float: left;">
                            Выбор раздела:
                            <SELECT name="category_id" onchange="submit();">
                                <option value="all">Все</option>
                                <?php
                                $count = count($categories);
                                for($i = 0; $i < $count; $i++)
                                {
                                    $cat = $categories[$i];
                                    echo '<option value="'.$cat['id'].'"';
                                    if($this->session->userdata('gallery_category_id') == $cat['id']) echo ' selected';
                                    echo '>'.$cat['name'].'</option>';
                                    $subcats = $this->gallery->getSubCategories($cat['id']);
                                    if($subcats)
                                    {
                                        $subcount = count($subcats);
                                        for($j = 0; $j < $subcount; $j++)
                                        {
                                            $sub = $subcats[$j];
                                            echo '<option value="'.$sub['id'].'"';
                                            if($this->session->userdata('gallery_category_id') == $sub['id']) echo ' selected';
                                            echo '>&nbsp;└&nbsp;'.$sub['name'].'</option>';
                                        }
                                    }
                                }
                                ?>
                            </SELECT>
                        </form>
                    </div>
                    <div class="top_menu_link"><a href="/admin/gallery/">Галерея</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/add/">Добавить фотку</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/zip_import/">Импорт zip архива</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/categories/">Разделы галереи</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/categories/add/">Добавить раздел галереи</a></div>                    
                    <div class="top_menu_link"><a href="/admin/options/set_module/gallery/">Настройки галереи</a></div>
                </div>

                <div class="pagination"><?=$pager?></div>
                
                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th width="200px">Фото</th>
                        <th>Название</th>
                        <th>Позиция</th>
                        <th>Вверх/Вниз</th>
                        <th>Действия</th>
                    </tr>
                    <?php
                    $count = count($images);                
                    for($i = 0; $i < $count; $i++)
                    {
                        $img = $images[$i];
                        $category = $this->gallery->getCategoryById($img['category_id']);
                        $folder = '';
                        if($category['folder'] != '') $folder = 'categories/'.$category['folder'].'/';
                        $thumb = '';
                        if($thumb == '')
                                        $thumb = '/upload/gallery/'.$folder.'mini/'.$img['image'];
                        ?>
                        <tr class="list">
                            <td>
                                <a href="/admin/gallery/edit/<?=$img['id']?>/" title="Редактировать фото">
                                    <img src="<?=$thumb?>" />
                                </a>
                            </td>
                            <td><a href="/admin/gallery/edit/<?=$img['id']?>/" title="Редактировать фото"><?=$img['name']?></a></td>
                            <td><?=$img['num']?></td>
                            <td><a href="/admin/gallery/up/<?=$img['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                            <a href="/admin/gallery/down/<?=$img['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a></td>
                            
                            <td>
                                <a href="/admin/gallery/active/<?=$img['id']?>/"><?php
                                if($img['active'] == 1)
                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                else
                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                ?></a>
                                <a href="/admin/gallery/edit/<?=$img['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/gallery/del/<?=$img['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a></td>
                        </tr>
                        <?php
                        }
                    ?>
                </table>
                <div class="pagination"><?=$pager?></div>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>