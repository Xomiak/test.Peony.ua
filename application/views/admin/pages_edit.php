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
                <form enctype="multipart/form-data" action="/admin/pages/edit/<?=$page['id']?>/" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $page['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url']; else echo $page['url'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $page['num']?>" size="3" /></td>
                        </tr>
                        
                        <tr>
                            <td valign="top">Фото:</td>
                            <td>
                                <?php
                                if($page['image'] != '')
                                {
                                    echo '<img src="'.$page['image'].'" /><br /><input type="checkbox" name="image_del">Удалить<br />';
                                }
                                ?>
                                <input type="file" name="userfile" />
                                <input type="hidden" name="image" value="<?=$page['image']?>" />
                            </td>
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
                                    elseif($page['template'] == $files[$i]) echo ' selected';
                                    echo '>'.$files[$i].'</option>';
                                }
                                ?>
                                </SELECT>                             
                            </td>
                        </tr>
                        <tr>
                            <td>Контент:</td>
                            <td><textarea name="content" class="ckeditor" rows="30"><?php if(isset($_POST['content'])) echo $_POST['content']; else echo $page['content'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>title:</td>
                            <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title']; else echo $page['title'];?>" /></td>
                        </tr>
                        <tr>
                            <td>keywords:</td>
                            <td><textarea name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords']; else echo $page['keywords'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>description:</td>
                            <td><textarea name="description"><?php if(isset($_POST['description'])) echo $_POST['description']; else echo $page['description'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>SEO текст:</td>
                            <td><textarea name="seo" class="ckeditor"><?php if(isset($_POST['seo'])) echo $_POST['seo']; else echo $page['seo'];?></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="social_buttons"<? if($page['social_buttons']==1) echo ' checked'?> /> Кнопки соц. сетей</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<? if($page['active']==1) echo ' checked'?> /> Активный</td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="save" value="Сохранить" /></td>
                            <td><input type="submit" name="save_and_stay" value="Сохранить и остаться" /></td>
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