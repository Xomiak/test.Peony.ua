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
                    <div class="top_menu_link"><a href="/admin/shop/">Товары</a></div>
                    <div class="top_menu_link"><a href="/admin/shop/add/">Добавить товар</a></div>
		    <div class="top_menu_link"><a href="/admin/shop/import/">Импорт</a></div>
		    <div class="top_menu_link"><a href="/admin/shop/export/">Экспорт</a></div>
                </div>
                

                <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                    <table>
                        <tr>
                            <td>CSV файл:</td>
                            <td><input required type="file" name="userfile" /></td>
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