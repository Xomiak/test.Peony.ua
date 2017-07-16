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
                <form action="/admin/menus/add/" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input required type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url'];?>" /></td>
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
                                    echo '<OPTION value="'.$menus[$i]['id'].'">'.$menus[$i]['type'].'&nbsp;&nbsp;|&nbsp;&nbsp;'.$menus[$i]['name'].'</OPTION>';                                
                                    $subs = $this->mp->getMenusWithParentId($menus[$i]['id']);                                
                                    if($subs)
                                    {
                                        $count2 = count($subs);
                                        for($i2 = 0; $i2 < $count2; $i2++)
                                        {
                                            echo '<OPTION value="'.$subs[$i2]['id'].'">'.$menus[$i]['type'].'&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;└&nbsp;'.$subs[$i2]['name'].'</OPTION>';
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
                                <SELECT name="type" required>                                    
                                    <option value="top">Верхнее</option>
                                    <option value="top2">Верхнее 2</option>
				    <option value="left">Левое</option>
                                    <option value="right">Правое</option>
                                    <option value="bottom">Нижнее</option>
                                    <option value="galleryLeft">Галерея Левое</option>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Подтип:</td>
                            <td>
                                <SELECT name="subtype">
                                    <option value=""></option>
                                    <option value="first">Впереди</option>
                                    <option value="last">Вконце</option>                                
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Параметры:</td>
                            <td>
                                <input type="text" name="params" value="<?php if(isset($_POST['params'])) echo $_POST['params']; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active" checked /> Активный</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Добавить" /></td>
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