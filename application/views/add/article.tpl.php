<?php include("application/views/header.php") ?>

<?php
$type = $this->session->userdata('type');
?>

<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="200px" align="left" valign="top">
                        <?php
                        include('application/views/left.tpl.php');
                        ?>
                    </td>
                    <td valign="top">
                        <table width="97%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr>
                                <td valign="top">
                                    <div class="kroshki">
                                        <div xmlns:v="http://rdf.data-vocabulary.org/#">
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            Добавление статьи
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="long"><?=$h1?></h1>
                                    <p class="warning">
                                        Внимание! Все добавленные в статью ссылки индексироваться не будут, т.к. к ним автоматически подставится параметр rel="nofollow".<br />
                                        Если Вы публикуете статью ради размещения внешней ссылки, то не тратьте ни своё, ни наше время!
                                    </p>
                                    <form enctype="multipart/form-data" action="/add/article/" method="post">
                                        <table>
                                            <tr>
                                                <td>Название *:</td>
                                                <td>
                                                    <input type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" />
                                                    <?php
                                                    if(isset($err['name']))
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['name']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                                <tr>
                                                    <td>url:</td>
                                                    <td><input type="text" name="url" size="50" value="<?php if(isset($_POST['url'])) echo $_POST['url'];?>" /></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td valign="top">Раздел *:</td>
                                                <td>
                                                    <SELECT name="category_id[]" multiple="" style="height: 150px;">
                                                    <!--SELECT name="category_id"-->
                                                        <option></option>
                                                        <?php
                                                        $count = count($categories);
                                                        for($i = 0; $i < $count; $i++)
                                                        {
                                                            $cat = $categories[$i];
                                                            echo '<option value="'.$cat['id'].'"';
                                                            if(isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) echo ' selected';
                                                            else if($this->session->userdata('category_id') == $cat['id']) echo ' selected';
                                                            
                                                            echo '>'.$cat['name'].'</option>';
                                                            $subs = $this->categories->getSubCategories($cat['id'],1);
                                                            if($subs)
                                                            {
                                                                $subcount = count($subs);
                                                                for($j = 0; $j < $subcount; $j++)
                                                                {
                                                                    $sub = $subs[$j];
                                                                    echo '<option value="'.$sub['id'].'"';
                                                                    if(isset($_POST['category_id']) && $_POST['category_id'] == $sub['id']) echo ' selected';
                                                                    else if($this->session->userdata('category_id') == $sub['id']) echo ' selected';
                                                                    
                                                                    echo '>&nbsp;└&nbsp;'.$sub['name'].'</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </SELECT>
                                                    <?php
                                                    if(isset($err['category_id']))
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['category_id']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                            <tr>
                                                <td>Заголовок H1:</td>
                                                <td><input type="text" name="h1" size="50" value="<?php if(isset($_POST['h1'])) echo $_POST['h1'];?>" /></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                            <tr>
                                                <td>Позиция:</td>
                                                <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $num?>" size="3" /></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                            <tr>
                                                <td>Время:</td>
                                                <td><input type="text" name="time" value="<?php if(isset($_POST['time'])) echo $_POST['time']; else echo date("H:i");?>" size="3" /></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                            <tr>
                                                <td>Дата:</td>
                                                <td>
                                                    <input id="datepicker" type="text" name="date" value="<?php if(isset($_POST['date'])) echo $_POST['date']; else echo date("Y-m-d");?>" />
                                                    <script>new DatePicker("#datepicker",{autoOpen:true,sideButton:false,hiddenButton:false,format:"Y-m-d"});</script>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td>Фото:</td>
                                                <td>
                                                    <input type="file" name="userfile" />
                                                    <?php
                                                    if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                                    {
                                                    ?>
                                                    <br /><a target="_blank" href="/admin/images/">Загрузить доп. фотографии</a>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Youtube:</td>
                                                <td><input type="text" name="youtube" size="50" value="<?php if(isset($_POST['youtube'])) echo $_POST['youtube'];?>" /></td>
                                            </tr>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                            <tr>
                                                <td>Краткое описание:</td>
                                                <td><textarea name="short_content" class="tinymce"><?php if(isset($_POST['short_content'])) echo $_POST['short_content'];?></textarea></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td>Контент *:</td>
                                                <td>
                                                    <textarea name="content" class="tinymce"><?php if(isset($_POST['content'])) echo $_POST['content'];?></textarea>
                                                    <?php
                                                    if(isset($err['content']))
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['content']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>
                                            <tr>
                                                <td>Источник/Автор:</td>
                                                <td><input type="text" name="source" size="50" value="<?php if(isset($_POST['source'])) echo $_POST['source'];?>" /></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            
                                            <?php
                                            if($type == 'moder' || $type == 'admin' || $type == 'superadmin')
                                            {
                                            ?>                                            
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
                                                <td>robots:</td>
                                                <td><input type="text" name="robots" size="20" value="<?php if(isset($_POST['robots'])) echo $_POST['robots']; else echo 'index, follow';?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Счётчик:</td>
                                                <td><input type="text" name="count" size="5" value="<?php if(isset($_POST['count'])) echo $_POST['count']; else echo '0';?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>SEO текст:</td>
                                                <td><textarea name="seo" class="tinymce"><?php if(isset($_POST['seo'])) echo $_POST['seo'];?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><input type="checkbox" name="glavnoe" checked /> На главной</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><input type="checkbox" name="active" checked /> Активный</td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td align="right" valign="top">
                                                    Введите цифры *:
                                                </td>
                                                <td>
                                                    <?=$cap['image']?><br />
                                                    <input type="text" name="captcha" value="" />
                                                    <?php
                                                    if(isset($err['captcha']) && $err['captcha'] != '')
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['captcha']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><input type="submit" value="Добавить" /></td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>                        
                    </td>
                </tr>
            </table>            
        </td>
        <td width="200px" valign="top" align="center">            
            <?php
            include('application/views/right.tpl.php');
            ?>
        </td>
    </tr>
</table>
<?php include("application/views/footer.php") ?>