<?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" valign="top"><?php include("menu.php"); ?></td>
        <td width="20px" align="right" valign="top"><div class="border"> </div></td>
        <td valign="top">
            <div class="title_border">
                <div class="content_title"><h1><?=$title?></h1></div>
                <div class="back_and_exit">		    русский <a href="/en<?=$_SERVER['REQUEST_URI']?>">english</a>

                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>


            
            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/main/edit/">Редактировать</a></div>
                    <div class="top_menu_link"><a href="/admin/?clear_cache=true">Очистить кэш</a></div>
                    <div class="top_menu_link"><a href="/admin/tkdz/">URLs TKDZ</a></div>
                </div>
                
                    <table cellpadding="3" cellspacing="3">
                    <tr>
                        <th align="left">title:</th>
                        <td><?=$main['title']?></td>
                    </tr>
                    <tr>
                        <th align="left">keywords:</th>
                        <td><?=$main['keywords']?></td>
                    </tr>
                    <tr>
                        <th align="left">description:</th>
                        <td><?=$main['description']?></td>
                    </tr>
                    <tr>
                        <th align="left">h1:</th>
                        <td><?=$main['h1']?></td>
                    </tr>
					<tr>
                        <th align="left">1-я колонка:</th>
                        <td><?=$main['col1']?></td>
                    </tr>
					<tr>
                        <th align="left">2-я колонка:</th>
                        <td><?=$main['col2']?></td>
                    </tr>
                </table>
                
                
            </div>
            
            
        </td>
    </tr>
</table>

<?php
include("footer.php");
?>