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
                        <form method="post" action="/admin/options/set_module/">
                            Выбор раздела:
                            <SELECT name="module" onchange="submit();">
                                <option value="all">Все</option>                    
                                <?php
                                if($modules)
                                {
                                    $count = count($modules);
                                    for($i = 0; $i < $count; $i++)
                                    {
                                        $m = $modules[$i];
                                        echo '<option value="'.$m['name'].'"';
                                        if($this->session->userdata('options_module_name') == $m['name']) echo ' selected';
                                        echo '>'.$m['title'].'</option>';                           
                                    }
                                }
                                ?>
                            </SELECT>
                        </form>
                    </div>
                    <div class="top_menu_link"><a href="/admin/options/">Опции</a></div>
                    <div class="top_menu_link"><a href="/admin/options/add/">Добавить опцию</a></div>
                </div>
                
            
                <table width="100%" cellpadding="1" cellspacing="1" class="admin_table">
                    <tr bgcolor="#EEEEEE">
                        <th><a href="<?=$_SERVER['REQUEST_URI']?>?sort_by=id"<?php if($sort_by == 'id') echo ' class="sort_by"'; ?>>ID</a></th>
                        <th><a href="<?=$_SERVER['REQUEST_URI']?>?sort_by=module"<?php if($sort_by == 'module') echo ' class="sort_by"'; ?>>Модуль</a></th>
                        <th><a href="<?=$_SERVER['REQUEST_URI']?>?sort_by=adding"<?php if($sort_by == 'adding') echo ' class="sort_by"'; ?>>Описание</a></th>
                        <th><a href="<?=$_SERVER['REQUEST_URI']?>?sort_by=name"<?php if($sort_by == 'name') echo ' class="sort_by"'; ?>>Название</a></th>
                        <th>Значение</th>
                        <th>Действия</th>
                    </tr>
                    <?php
                    $count = count($options);
                    for($i = 0; $i < $count; $i++)
                    {
                        $option = $options[$i];
                        ?>
                        <tr class="list">
                            <td><?=$option['id']?></td>
                            <td><?=$this->model_options->getModuleTitle($option['module'])?></td>
                            <td><a href="/admin/options/edit/<?=$option['id']?>/" title="Перейти к редактированию"><?=$option['rus']?></a></td>
                            <td><?=$option['name']?></td>
                            <td>
                                <?php
                                if($option['type'] == 'bool')
                                {
                                    if($option['value'] == '1') echo "Да";
                                    else echo "Нет";
                                }
                                else echo $option['value'];
                                ?>
                            </td>
                            <td>                            
                                <a href="/admin/options/edit/<?=$option['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <?php
                                if($option['required'] != 1)
                                {
                                    ?>
                                    <a onclick="return confirm('Удалить?')" href="/admin/options/del/<?=$option['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                                    <?php
                                }
                                ?>                            
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