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
                    <div class="top_menu_link"><a href="/admin/options/">Опции</a></div>
                    <div class="top_menu_link"><a href="/admin/options/add/">Добавить опцию</a></div>
                </div>
                <form enctype="multipart/form-data" action="/admin/options/edit/<?=$option['id']?>/" method="post">
                    <table>
                        <tr>
                            <td>Описание *:</td>
                            <td>
                                <input required type="text" name="rus" size="50" value="<?php if(isset($_POST['rus'])) echo $_POST['rus']; else echo $option['rus'];?>" />
                                <?php
                                if(isset($err['rus']))
                                {
                                    ?>
                                    <div class="error"><?=$err['rus']?></div>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Название *:</td>
                            <td>
                                <input required<?php if($type != 'admin') echo ' disabled="disabled"'; elseif($option['required'] == 1) echo ' disabled="disabled"';  ?> type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $option['name'];?>" />
                                <?php
                                if(isset($err['name']))
                                {
                                    ?>
                                    <div class="error"><?=$err['name']?></div>
                                    <?php
                                }
                                ?>
                            </td>
                            <?php                        
                                if($option['required'] == 1)
                                {
                                    ?>
                                    <td>
                                        <span class="helper"><img src="/img/question.png" alt="Подсказка" title="Подсказка" width="16px" height="16px" /> Обязательный параметр</span>
                                    </td>
                                    <?php
                                }
                            ?>
                        </tr>
                        
                        <tr>
                            <td>Модуль:</td>
                            <td>
                                <SELECT name="module"<?php if($type != 'admin') echo ' disabled="disabled"';?>>
                                    <option value="main"<?php
                                    if(isset($_POST['module']) && ($_POST['module'] == 'main' || $_POST['module'] == '')) echo ' selected';
                                    else if($option['module'] == 'main' || $option['module'] == '') echo ' selected'; ?>>Основные</option>
                                    <?php
                                    if($modules)
                                    {
                                        $count = count($modules);
                                        for($i = 0; $i < $count; $i++)
                                        {
                                            $m = $modules[$i];
                                            echo '<option value="'.$m['name'].'"';
                                            if(isset($_POST['module']) && $_POST['module'] == $m['name'])  echo ' selected';
                                            else if($option['module'] == $m['name']) echo ' selected';
                                            echo '>'.$m['title'].'</option>';                                        
                                        }
                                    }
                                    ?>
                                </SELECT>
                                <?php
                                if(isset($err['module']))
                                {
                                    ?>
                                    <div class="error"><?=$err['module']?></div>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Значение *:</td>
                            <td>
                            <?php
                            if($option['type'] == 'bool')
                            {
                                ?>
                                <select name="value">
                                    <option value="1"<?php if($option['value'] == "1") echo ' selected' ?>>Да</option>
                                    <option value="0"<?php if($option['value'] == "0") echo ' selected' ?>>Нет</option>
                                </select>
                                <?php
                            }
                            elseif($option['type'] == 'input')
                            {
                                ?>
                                <input type="text" name="value" value="<?php if(isset($_POST['value'])) echo $_POST['value']; else echo $option['value'];?>" />
                                <?php
                            }
                            else if($option['type'] == 'tinymce')
                            {
                            ?>
                                <textarea name="value" class="ckeditor"><?php if(isset($_POST['value'])) echo $_POST['value']; else echo $option['value'];?></textarea>
                                <?php
                                if(isset($err['value']))
                                {
                                    ?>
                                    <div class="error"><?=$err['value']?></div>
                                    <?php
                                }
                                ?>
                            <?php
                            }
                            else
                            {
                            ?>
                                <textarea name="value" cols="50" rows="5"><?php if(isset($_POST['value'])) echo $_POST['value']; else echo $option['value'];?></textarea>
                                <?php
                                if(isset($err['value']))
                                {
                                    ?>
                                    <div class="error"><?=$err['value']?></div>
                                    <?php
                                }
                                ?>
                            <?php
                            }
                            ?>
                            </td>
                            <td valign="top">
                                <?php
                                if($option['adding'] != '')
                                {
                                ?>
                                <span class="helper">
                                    <img width="16px" height="16px" title="Подсказка" alt="Подсказка" src="/img/question.png">
                                    <?=$option['adding']?>
                                </span>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание значений:</td>
                            <td>
                                <textarea<?php if($type != 'admin') echo ' disabled="disabled"';?> name="adding"><?php if(isset($_POST['adding'])) echo $_POST['adding']; else echo $option['adding'];?></textarea>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Тип значения:</td>
                            <td>
                                <select<?php if($type != 'admin') echo ' disabled="disabled"';?> name="type">
                                    <option value="textarea"<?php if($option['type'] == 'textarea') echo ' selected' ?>>Текстовое поле</option>
                                    <option value="input"<?php if($option['type'] == 'input') echo ' selected' ?>>Строка</option>
                                    <option value="bool"<?php if($option['type'] == 'bool') echo ' selected' ?>>Да-Нет</option>
                                    <option value="tinymce"<?php if($option['type'] == 'tinymce') echo ' selected' ?>>Текстовый редактор</option>
                                </select>
                            </td>
                        </tr>
                        
                        <?php if($type == 'admin')
                        {
                        ?>
                        <tr>
                            <td>Привилегии:</td>
                            <td>
                                <input type="text" name="privilege" size="50" value="<?php if(isset($_POST['privilege'])) echo $_POST['privilege']; else echo $option['privilege'];?>" />
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
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