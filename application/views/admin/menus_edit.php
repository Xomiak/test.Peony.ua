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
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form action="/admin/menus/edit/<?=$menu['id']?>/" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $menu['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url']; else echo $menu['url'];?>" /></td>
                        </tr>
			
			<tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" size="50" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $menu['num'];?>" /></td>
                        </tr>
                        
                        <tr>
                            <td>Родитель:</td>
                            <td>
                                <SELECT name="parent_id">
                                    <option value="0">нет</option>
                                <?php
                                $count = count($menus);
                                for($i = 0; $i < $count; $i++)
                                {
                                    echo '<OPTION value="'.$menus[$i]['id'].'"';
                                    if($menu['parent_id'] == $menus[$i]['id']) echo ' selected';
                                    echo '>'.$menus[$i]['type'].'&nbsp;&nbsp;|&nbsp;&nbsp;'.$menus[$i]['name'].'</OPTION>';
                                    $subs = $this->mp->getMenusWithParentId($menus[$i]['id']);                                
                                    if($subs)
                                    {
                                        $count2 = count($subs);
                                        for($i2 = 0; $i2 < $count2; $i2++)
                                        {
                                            echo '<OPTION value="'.$subs[$i2]['id'].'"';
                                            if($menu['parent_id'] == $subs[$i2]['id']) echo ' selected';
                                            echo '>'.$menus[$i]['type'].'&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;└&nbsp;'.$subs[$i2]['name'].'</OPTION>';
                                        }
                                    }
                                }
                                ?>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Тип *:</td>
                            <td>
                                <SELECT required name="type">
                                    <option value="left"<?php if($menu['type'] == 'left') echo ' selected'; ?>>Левое</option>
                                    <option value="right"<?php if($menu['type'] == 'right') echo ' selected'; ?>>Правое</option>
                                    <option value="top"<?php if($menu['type'] == 'top') echo ' selected'; ?>>Верхнее</option>
                                    <option value="top2"<?php if($menu['type'] == 'top2') echo ' selected'; ?>>Верхнее 2</option>
                                    <option value="bottom"<?php if($menu['type'] == 'bottom') echo ' selected'; ?>>Нижнее</option>                                
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Подтип:</td>
                            <td>
                                <SELECT name="subtype">
                                    <option value=""<?php if($menu['subtype'] == '') echo ' selected'; ?>></option>
                                    <option value="first"<?php if($menu['subtype'] == 'first') echo ' selected'; ?>>Впереди</option>
                                    <option value="last"<?php if($menu['subtype'] == 'last') echo ' selected'; ?>>Вконце</option>                                
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Параметры:</td>
                            <td><input type="text" name="params" size="50" value="<?php if(isset($_POST['params'])) echo $_POST['params']; else echo $menu['params'];?>" /></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<? if($menu['active']==1) echo ' checked'?> /> Активный</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Изменить" /></td>
                        </tr>
                    </table>
                </form>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>