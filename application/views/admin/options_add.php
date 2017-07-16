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
                
                <form enctype="multipart/form-data" action="/admin/options/add/" method="post">
                   <table>
                       <tr>
                           <td>Описание *:</td>
                           <td>
                               <input required type="text" name="rus" size="50" value="<?php if(isset($_POST['rus'])) echo $_POST['rus'];?>" />
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
                               <input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" />
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
                       
                       <tr>
                           <td>Модуль:</td>
                           <td>
                               <SELECT name="module">                                
                                   <?php
                                   if($modules)
                                   {
                                       $count = count($modules);
                                       for($i = 0; $i < $count; $i++)
                                       {
                                           $m = $modules[$i];
                                           echo '<option value="'.$m['name'].'"';
                                           if(isset($_POST['module']) && $_POST['module'] == $m['name'])  echo ' selected';
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
                           <td>Значение:</td>
                           <td>
                               <textarea name="value" cols="50" rows="5"><?php if(isset($_POST['value'])) echo $_POST['value'];?></textarea>
                               <?php
                               if(isset($err['value']))
                               {
                                   ?>
                                   <div class="error"><?=$err['value']?></div>
                                   <?php
                               }
                               ?>
                           </td>
                       </tr>
                       <tr>
                           <td>Описание значений:</td>
                           <td>
                               <textarea name="adding"><?php if(isset($_POST['adding'])) echo $_POST['adding'];?></textarea>
                           </td>
                       </tr>
                       
                       <tr>
                           <td>Тип значения:</td>
                           <td>
                               <select name="type">
                                   <option value="textarea"<?php if(isset($_POST['type']) && $_POST['type'] == 'textarea') echo ' selected' ?>>Текстовое поле</option>
                                   <option value="input"<?php if(isset($_POST['type']) && $_POST['type'] == 'input') echo ' selected' ?>>Строка</option>
                                   <option value="bool"<?php if(isset($_POST['type']) && $_POST['type'] == 'bool') echo ' selected' ?>>Да-Нет</option>
                                   <option value="tinymce"<?php if(isset($_POST['type']) && $_POST['type'] == 'tinymce') echo ' selected' ?>>Текстовый редактор</option>
                               </select>
                           </td>
                       </tr>
                       
                       <tr>
                           <td valign="top">Обязательный параметр:</td>
                           <td valign="top">
                               <input type="checkbox" name="required" />
                           </td>
                           <td><i>Если выбрать "Обязательный параметр", то его название нельзя будет поменять и сам параметр нельзя будет удалить!</i></td>
                       </tr>
                       
                       <tr>
                           <td>Привилегии:</td>
                           <td>
                               <input type="text" name="privilege" size="50" value="<?php if(isset($_POST['privilege'])) echo $_POST['privilege'];?>" />
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