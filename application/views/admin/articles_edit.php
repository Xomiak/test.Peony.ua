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
                    <div class="top_menu_link"><a href="/admin/articles/">Статьи</a></div>
                    <div class="top_menu_link"><a href="/admin/articles/add/">Добавить статью</a></div>
                </div>
                
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="/admin/articles/edit/<?=$article['id']?>/" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $article['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url']; else echo $article['url'];?>" /></td>
                        </tr>
			<tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" size="50" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $article['num'];?>" /></td>
                        </tr>
			
			<tr>
                            <td>Дата:</td>
                            <td><input class="date" type="text" name="date" size="50" value="<?php if(isset($_POST['date'])) echo $_POST['date']; else echo $article['date'];?>" /></td>
                        </tr>
			
                        <tr>
                            <td>Раздел *:</td>
                            <td>
                                <?php
                                    $cat_ids = explode('*',$article['category_id']);
                                    $ccount = count($cat_ids);
                                ?>
                                <SELECT required name="category_id[]"<?php if($article_in_many_categories != '0') echo ' multiple=""'; ?>>
                                    
                                <!--SELECT name="category_id"-->
                                    <option></option>
                                    <?php
                                    $count = count($categories);
                                    for($i = 0; $i < $count; $i++)
                                    {
                                        $cat = $categories[$i];
                                        echo '<option value="'.$cat['id'].'"';
                                        for($ci = 0; $ci < $ccount; $ci++)
                                        {
                                            if($cat_ids[$ci] == $cat['id']) echo ' selected';
                                        }
                                        //if(isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) echo ' selected';
                                        //else if($cat['id'] == $article['category_id']) echo ' selected';
                                        echo '>'.$cat['name'].'</option>';
                                        $subs = $this->mcats->getSubCategories($cat['id']);
                                        if($subs)
                                        {
                                            $subcount = count($subs);
                                            for($j = 0; $j < $subcount; $j++)
                                            {
                                                $sub = $subs[$j];
                                                echo '<option value="'.$sub['id'].'"';
                                                for($ci = 0; $ci < $ccount; $ci++)
                                                {
                                                    if($cat_ids[$ci] == $sub['id']) echo ' selected';
                                                }
                                                //if(isset($_POST['category_id']) && $_POST['category_id'] == $sub['id']) echo ' selected';
                                                //else if($sub['id'] == $article['category_id']) echo ' selected';
                                                
                                                echo '>&nbsp;└&nbsp;'.$sub['name'].'</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Заголовок H1:</td>
                            <td><input type="text" name="h1" size="50" value="<?php if(isset($_POST['h1'])) echo $_POST['h1']; else echo $article['h1'];?>" /></td>
                        </tr>
                        <!--tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $article['num']?>" size="3" /></td>
                        </tr>
                        <tr>
                            <td>Время:</td>
                            <td><input type="text" name="time" value="<?php if(isset($_POST['time'])) echo $_POST['time']; else echo $article['time']?>" size="10" /></td>
                        </tr>
                        <tr>
                            <td>Дата:</td>
                            <td><input type="text" name="date" value="<?php if(isset($_POST['date'])) echo $_POST['date']; else echo $article['date']?>" size="10" /></td>
                        </tr-->
                        <tr>
                            <td valign="top">Фото:</td>
                            <td>
                                <?php
                                if($article['image'] != '')
                                {
                                    echo '<img src="'.$article['image'].'" /><br /><input type="checkbox" name="image_del">Удалить<br />';
                                }
                                ?>
                                <input type="file" name="userfile" />
                                <input type="hidden" name="image" value="<?=$article['image']?>" />
                                <br /><a target="_blank" href="/admin/images/">Загрузить доп. фотографии</a>
                            </td>
                        </tr>
                        
                        
                       
                        
                        <tr>
                            <td>Youtube:</td>
                            <td>
                                <?php
                                $y = $article['youtube'];
                                if($article['youtube'] != '')
                                {
                                    $pos = strpos($y,'v=');
                                    if($pos)
                                    {
                                        $pos = $pos + 2;
                                        $end = strpos($y,'&',$pos);
                                        $y = substr($y,$pos,$end-$pos);
                                    }
                                    if($y != '')
                                        echo '<iframe width="560" height="315" src="http://www.youtube.com/embed/'.$y.'" frameborder="0" allowfullscreen></iframe><br />';
                                }
                                ?>
                                
                                <input type="text" name="youtube" size="89" value="<?php if(isset($_POST['youtube'])) echo $_POST['youtube']; else echo $article['youtube'];?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Краткое описание:</td>
                            <td><textarea name="short_content" class="ckeditor"><?php if(isset($_POST['short_content'])) echo $_POST['short_content']; else echo $article['short_content'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>Контент:</td>
                            <td><textarea name="content" class="ckeditor" rows="30"><?php if(isset($_POST['content'])) echo $_POST['content']; else echo $article['content'];?></textarea></td>
                        </tr>
                        
                        

                        <tr>
                            <td>Кнопки соц. сетей:</td>
                            <td><input type="checkbox" name="social_buttons" <?php if(isset($_POST['social_buttons']) && $_POST['social_buttons'] == true) echo ' checked'; else if($article['social_buttons'] == 1) echo ' checked'; ?> /></td>
                        </tr>
                        
                                               
                        <tr>
                            <td>title:</td>
                            <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title']; else echo $article['title'];?>" /></td>
                        </tr>
                        <tr>
                            <td>keywords:</td>
                            <td><textarea name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords']; else echo $article['keywords'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>description:</td>
                            <td><textarea name="description"><?php if(isset($_POST['description'])) echo $_POST['description']; else echo $article['description'];?></textarea></td>
                        </tr>
                        <!--tr>
                            <td>robots:</td>
                            <td><input type="text" name="robots" size="20" value="<?php if(isset($_POST['robots'])) echo $_POST['robots']; else echo 'index, follow';?>" /></td>
                        </tr-->
                        <tr>
                            <td>Счётчик:</td>
                            <td><input type="text" name="count" size="5" value="<?php if(isset($_POST['count'])) echo $_POST['count']; else echo $article['count']; ?>" /></td>
                        </tr>
                        <!--tr>
                            <td>SEO текст:</td>
                            <td><textarea name="seo" class="ckeditor"><?php if(isset($_POST['seo'])) echo $_POST['seo']; else echo $article['seo'];?></textarea></td>
                        </tr-->
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<? if($article['active']==1) echo ' checked'?> /> Активный</td>
                        </tr>
                        <!--tr>
                            <td colspan="2"><input type="checkbox" name="send_about_active"<?php if($article['active'] == 0) echo ' checked'; ?> /> Отправить автору письмо об одобрении статьи</td>
                        </tr-->
                        
                        <tr>
                            <td><input type="submit" name="save" value="Сохранить" /></td>
                            <td><input type="submit" name="save_and_stay" value="Сохранить и остаться" /></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="content">
                <h1>Дополнительные фото</h1>
                <a name="images"></a>
                <?php
                if($images)
                {
                    $count = count($images);
                    for($i = 0; $i < $count; $i++)
                    {
                        $image = $images[$i];
                        ?>
                        <form enctype="multipart/form-data" action="/admin/articles/edit_image/" method="post">
                        <table>
                            <tr>
                                <td>Фото:</td>
                                <td>
                                    <input type="text" value="<?=$image['image']?>" size="50" /><br />
                                    <a href="<?=$image['image']?>" target="_blank"><img src="<?=$image['image']?>" width="300px" border="0" /></a><br />
                                    <input type="file" name="userfile" /><br />
                                    <input type="checkbox" name="delete" /> Удалить<br />
                                    <input type="checkbox" name="show_in_bottom"<?php if($image['show_in_bottom'] == 1) echo ' checked'; ?> />Показать под статьёй<br />
                                    <input type="checkbox" name="active"<? if($image['active'] == 1) echo ' checked'; ?> /> Активный<br />
                                    <input type="hidden" name="article_id" value="<?=$article['id']?>" />
                                    <input type="hidden" name="image_id" value="<?=$image['id']?>" />
                                    <input type="submit" value="Применить" />
                                </td>
                            </tr>
                        </table>
                    </form>
                        <?php
                    }
                }
                ?>
                <hr />
                <form enctype="multipart/form-data" action="/admin/articles/add_image/" method="post">
                    <table>
                        <tr>
                            <td>Фото:</td>
                            <td>
                                <input type="file" name="userfile" /><br />
                                <input type="checkbox" name="show_in_bottom" checked />Показать под статьёй<br />
                                <input type="checkbox" name="active" checked /> Активный<br />                                
                                <input type="hidden" name="article_id" value="<?=$article['id']?>" />
                                <input type="submit" value="Загрузить" />
                            </td>
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