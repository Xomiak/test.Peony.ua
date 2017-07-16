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
                    <div class="top_menu_link"><a href="/admin/mailer/">Рассылка</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/?auto=true">Авторассылки</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/add/">Создание рассылки</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/?clear_mailing=true">Очистить список товаров</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/sms/">SMS рассылка</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/sms/add/">Создать SMS рассылку</a></div>
                </div>
                
                
             <h3>Товары в рассылке:</h3>
                <?php
                if($articles)
                {
                    echo '<ul>';
                    $count = count($articles);
                    for($i = 0; $i < $count; $i++)
                    {
                        $article = $articles[$i];
                        echo '<li><a target="_blank" href="/admin/shop/edit/'.$article['id'].'/">'.$article['name'].' ('.$article['color'].')</a></li>';
                    }
                    echo '</ul>';
                }
                else echo '<div>Нет прикреплённых товаров!</div><br /><br />';
                ?>
                
            <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                <table>
                    <tr>
                        <td valign="top">Шапка:</td>
                        <td>
                            <?php
                            if($header != '')
                            {
                                echo '<img style="max-width:1000px" src="'.$header.'" /><input type="hidden" name="header" value="'.$header.'"/>';
                            }
                            ?>
                            <div>Выбрать другую шапку:</div>
                            <input type="file" name="header" />
                        </td>
                    </tr>
                    <tr>
                        <td>Заголовок:</td>
                        <td>
                            <input type="text" name="name" size="50" value="<?=$name?>" />
                        </td>
                    </tr>
                   
                    
                    <tr>
                        <td>Основной текст:</td>
                        <td>
                            <textarea class="ckeditor" name="content" cols="50" rows="5"><?=$content?></textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Примечание:</td>
                        <td>
                            <textarea class="ckeditor" name="adding" cols="50" rows="5"><?=$adding?></textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Подвал:</td>
                        <td>
                            <textarea class="ckeditor" name="footer" cols="50" rows="5"><?=$footer?></textarea>
                        </td>
                    </tr>
					
					<tr>
							<td colspan="2"><input type="checkbox" name="no_price" /> Без цены</td>
					</tr> 
       
						<tr>
							<td></td>
							<td><input type="submit" value="Создать" /></td>
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