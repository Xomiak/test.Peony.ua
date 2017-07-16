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
                    <div class="top_menu_link"><a href="/admin/pages/">Страницы</a></div>
                    <div class="top_menu_link"><a href="/admin/pages/add/">Добавить страницу</a></div>
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="/admin/pages/add/" method="post">
<input type="hidden" name="num" value="<?=$num?>" />
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></td>
                        </tr>
                        
                        <tr>
                            <td>Фото:</td>
                            <td><input type="file" name="userfile" /></td>
                        </tr>
                        <tr>
                            <td>Шаблон:</td>
                            <td>
                                <SELECT name="template">
                                <?php
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
                            <td>Контент:</td>
                            <td><textarea name="content" class="ckeditor" rows="30"><?php if(isset($_POST['content'])) echo $_POST['content'];?></textarea></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2"><input type="checkbox" name="social_buttons" checked /> Кнопки соц. сетей</td>
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