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
                    <div class="top_menu_link"><a href="/admin/menus/">Меню</a></div>
                    <div class="top_menu_link"><a href="/admin/menus/add/">Добавить пункт меню</a></div>
                    <div class="top_menu_link">
                        <form action="/admin/menus/del-all/" method="post">
                        Удалить: <SELECT name="type" required>
                        <option value=""></option>
                        <option value="left">left</option>
                        <option value="right">right</option>
                        <option value="top">top</option>
                        <option value="bottom">bottom</option>
                        </SELECT>
                        <input type="submit" value="Удалить" onclick="return confirm('Удалить?')" />
                    </form>
                    </div>
                </div>
                
                        
                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>Тип</th>
                        <th>Название</th>
                        <th>Родитель</th>
			<th>url</th>
                        <th>Позиция</th>
                        <th>Вверх/Вниз</th>
                        <th>Действия</th>
    
                    </tr>
                    <?php
                    $type = '';
                    $color = 1;
                    $count = count($menus);
                    for($i = 0; $i < $count; $i++)
                    {
                        $menu = $menus[$i];
                        ?>
                        <tr class="list<?php
                        if($menu['type'] !== $type)
                        {
                            
                            $type = $menu['type'];
                            if($color == 1) $color = 2;
                            else $color = 1;
                        }
                        echo $color;
                        ?>">
                            <td><?=$menu['type']?></td>
                            <td><a href="/admin/menus/edit/<?=$menu['id']?>/" title="Перейти к редактированию"><?=$menu['name']?></a></td>
                            <td>
                                <?php
                                if($menu['parent_id'] != 0)
                                {
                                    $parent = $this->mp->getMenuById($menu['parent_id']);
                                    echo '<i><a href="/admin/menus/edit/'.$parent['id'].'/" title="Перейти к редактированию">'.$parent['name'].'</a></i>';
                                }
                                else echo '<i>Нет</i>';
                                ?>
                            </td>
			    <td><a href="<?=$menu['url']?>" targhet="_blank"><?=$menu['url']?></a></td>
                            <td><?=$menu['num']?></td>
                            <td>
                                <a href="/admin/menus/up/<?=$menu['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                                <a href="/admin/menus/down/<?=$menu['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a>
                            </td>
                            
                            <td>
                                <a href="/admin/menus/active/<?=$menu['id']?>/"><?php
                                if($menu['active'] == 1)
                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                else
                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                ?></a>
                                <a href="/admin/menus/edit/<?=$menu['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/menus/del/<?=$menu['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                        $type = $menu['type'];
                        
                        $sub = $this->mp->getMenusWithParentId($menu['id']);
                        if($sub)
                        {
                            $count2 = count($sub);
                            for($j = 0; $j < $count2; $j++)
                            {
                                $menu = $sub[$j];
                                ?>
                                <tr class="list<?=$color?>">
                                    <td><?=$menu['type']?></td>
                                    <td><a href="/admin/menus/edit/<?=$menu['id']?>/" title="Перейти к редактированию">
                                        &nbsp;└&nbsp;<?=$menu['name']?></a></td>
                                    <td>
                                        <?php
                                        if($menu['parent_id'] != 0)
                                        {
                                            $parent = $this->mp->getMenuById($menu['parent_id']);
                                            echo '<i><a href="/admin/menus/edit/'.$parent['id'].'/" title="Перейти к редактированию">'.$parent['name'].'</a></i>';
                                        }
                                        else echo '<i>Нет</i>';
                                        ?></td>
				    <td><a href="<?=$menu['url']?>" targhet="_blank"><?=$menu['url']?></a></td>
                                    <td><?=$menu['num']?></td>
                                    <td>
                                        <a href="/admin/menus/up/<?=$menu['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                                        <a href="/admin/menus/down/<?=$menu['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a>
                                    </td>
                                    
                                    <td>
                                        <a href="/admin/menus/active/<?=$menu['id']?>/"><?php
                                        if($menu['active'] == 1)
                                            echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                        else
                                            echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                        ?></a>
                                        <a href="/admin/menus/edit/<?=$menu['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                        <a onclick="return confirm('Удалить?')" href="/admin/menus/del/<?=$menu['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                                    </td>
                                </tr>
                                <?php
                                $sub2 = $this->mp->getMenusWithParentId($menu['id']);
                                if($sub2)
                                {
                                    $count3 = count($sub2);
                                    for($j2 = 0; $j2 < $count3; $j2++)
                                    {
                                        $menu = $sub2[$j2];
                                        ?>
                                        <tr class="list<?=$color?>">
                                            <td><?=$menu['type']?></td>
                                            <td><?=$menu['subtype']?></td>
                                            <td><a href="/admin/menus/edit/<?=$menu['id']?>/" title="Перейти к редактированию">
                                                &nbsp;&nbsp;&nbsp;&nbsp;└&nbsp;<?=$menu['name']?></a></td>
                                            <td>
                                                <?php
                                                if($menu['parent_id'] != 0)
                                                {
                                                    $parent = $this->mp->getMenuById($menu['parent_id']);
                                                    echo '<i><a href="/admin/menus/edit/'.$parent['id'].'/" title="Перейти к редактированию">'.$parent['name'].'</a></i>';
                                                }
                                                else echo '<i>Нет</i>';
                                                ?></td>
                                            <td><?=$menu['num']?></td>
                                            <td>
                                                <a href="/admin/menus/up/<?=$menu['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                                                <a href="/admin/menus/down/<?=$menu['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a>
                                            </td>
                                            
                                            <td>
                                                <a href="/admin/menus/active/<?=$menu['id']?>/"><?php
                                                if($menu['active'] == 1)
                                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                                else
                                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                                ?></a>
                                                <a href="/admin/menus/edit/<?=$menu['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                                <a onclick="return confirm('Удалить?')" href="/admin/menus/del/<?=$menu['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
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