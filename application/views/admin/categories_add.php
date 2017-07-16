<?php
include("header.php");
?>
<table width="100%" cellpadding="0" cellspacing="0">
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


                            <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="/admin/categories/add/" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Название 1-й единицы:</td>
                            <td><input required type="text" name="name_one" size="50" value="<?php if(isset($_POST['name_one'])) echo $_POST['name_one'];?>" /></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $num?>" size="3" /></td>
                        </tr>
                        <tr>
                            <td>Родитель:</td>
                            <td>
                                <SELECT name="parent">
                                    <option value="0">нет</option>
                                <?php
                                $count = count($categories);
                                for($i = 0; $i < $count; $i++)
                                {
                                    echo '<OPTION value="'.$categories[$i]['id'].'">'.$categories[$i]['name'].'</OPTION>';
                                }
                                ?>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Шаблон:</td>
                            <td>
                                <SELECT name="template" required>
                                    <option></option>
                                <?php
                                $_POST['template'] = 'shop.tpl.php';
                                $this->load->helper('file');
                                $files = get_filenames('application/views/templates/');
                                $count = count($files);
                                for($i = 0; $i < $count; $i++)
                                {
                                    echo '<option value="'.$files[$i].'"';
                                    if(isset($_POST['template']) && $_POST['template'] == $files[$i]) echo ' selected';
                                    echo '>'.$files[$i].'</option>';
                                }
                                ?>
                                </SELECT>                            
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Шаблон:</td>
                            <td>
                                <SELECT name="content_template">
                                    <option></option>
                                <?php
                                $_POST['content_template'] = 'product_one.tpl.php';
                                $this->load->helper('file');
                                $files = get_filenames('application/views/templates/');
                                $count = count($files);
                                for($i = 0; $i < $count; $i++)
                                {
                                    echo '<option value="'.$files[$i].'"';
                                    if(isset($_POST['content_template']) && $_POST['content_template'] == $files[$i]) echo ' selected';                                    
                                    echo '>'.$files[$i].'</option>';
                                }
                                ?>
                                </SELECT>                            
                            </td>
                        </tr>
                        <tr>
                            <td>Тип раздела:</td>
                            <td>
                                <SELECT name="type">
				    <option value="shop">Товары</option>
                                    <option value="articles">Статьи</option>                                    
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Лого:</td>
                            <td><input type="file" name="userfile" /></td>
                        </tr>
                        <tr>
                            <td>h1:</td>
                            <td><input type="text" name="h1" size="50" value="<?php if(isset($_POST['h1'])) echo $_POST['h1'];?>" /></td>
                        </tr>
                        <tr>
                            <td>title:</td>
                            <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title'];?>" /></td>
                        </tr>
                        <tr>
                            <td>keywords:</td>
                            <td><textarea name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>description:</td>
                            <td><textarea name="description"><?php if(isset($_POST['description'])) echo $_POST['description'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>SEO текст:</td>
                            <td><textarea name="seo" class="ckeditor"><?php if(isset($_POST['seo'])) echo $_POST['seo'];?></textarea></td>
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