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
                    
                </div>
                <form enctype="multipart/form-data" action="/admin/images/" method="post">
                <table>
                    <tr>
                        <td>Максимальная ширина</td>
                        <td><input required type="number" min="1" name="width" value="<?=$upload_image_max_width?>" /></td>
                    </tr>
                    <tr>
                        <td>Максимальная высота</td>
                        <td><input required type="number" min="1" name="height" value="<?=$upload_image_max_height?>" /></td>
                    </tr>
                    <tr>
                        <td>Фото:</td>
                        <td><input type="file" name="userfile" /></td>
                    </tr>
                </table>
                
                <br />
                <input type="submit" value="Загрузить!" />
                
                </form>
                
                <?php
                if($image != '')
                {
                    echo '<p>Ваша фотография успешно загружена!</p>';
                    echo '<p><strong>Адрес: </strong><input type="text" value="http://'.$_SERVER['SERVER_NAME'].$image.'" onfocus="this.select()" size="110" /></p>';
                    echo '<p><img src="'.$image.'" /></p>';
                    
                }
                ?>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>