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
                    <div class="top_menu_link"><a href="/admin/">Главная</a></div>
                    <div class="top_menu_link"><a href="/admin/main/edit/">Редактировать</a></div>
                </div>
                
                <form action="/admin/main/edit/" method="post">
                    <table>                    
                        <tr>
                            <td>title:</td>
                            <td><input type="text" name="title" size="50" value="<?php if(isset($_POST['title'])) echo $_POST['title']; else echo $main['title'];?>" /></td>
                        </tr>
                        <tr>
                            <td>keywords:</td>
                            <td><textarea name="keywords"><?php if(isset($_POST['keywords'])) echo $_POST['keywords']; else echo $main['keywords'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>description:</td>
                            <td><textarea name="description"><?php if(isset($_POST['description'])) echo $_POST['description']; else echo $main['description'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>Заголовок H1:</td>
                            <td><input type="text" name="h1" size="50" value="<?php if(isset($_POST['h1'])) echo $_POST['h1']; else echo $main['h1'];?>" /></td>
                        </tr>
						<tr>
                            <td>1-я колонка:</td>
                            <td><textarea name="col1" class="ckeditor"><?php if(isset($_POST['col1'])) echo $_POST['col1']; else echo $main['col1'];?></textarea></td>
                        </tr>
                        <tr>
                            <td>2-я колонка:</td>
                            <td><textarea name="col2" class="ckeditor"><?php if(isset($_POST['col2'])) echo $_POST['col2']; else echo $main['col2'];?></textarea></td>
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