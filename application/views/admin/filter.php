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
                <strong>Размер</strong>

                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>Название</th>                       
                        <th>Действия</th>  
                    </tr>
                    <?php
                    $count = count($razmer);
                    for($i = 0; $i < $count; $i++)
                    {
                        $page = $razmer[$i];
                        ?>
                        <tr class="list">
                            <td><?=$page['name']?></td>
                            
                            <td>                                
                                <a onclick="return confirm('Удалить?')" href="/admin/filter/del/<?=$page['id']?>/?table=razmer"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
                    <input required type="text" name="razmer" value="" /> <input type="submit" value="Добавить" />
                </form>
                
                
                <strong>Цвет</strong>

                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>Название</th>                       
                        <th>Действия</th>  
                    </tr>
                    <?php
                    $count = count($color);
                    for($i = 0; $i < $count; $i++)
                    {
                        $page = $color[$i];
                        ?>
                        <tr class="list">
                            <td><?=$page['name']?></td>
                            
                            <td>                                
                                <a onclick="return confirm('Удалить?')" href="/admin/filter/del/<?=$page['id']?>/?table=color"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
                    <input required type="text" name="color" value="" /> <input type="submit" value="Добавить" />
                </form>
                
                
                <strong>Состав</strong>

                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>Название</th>                       
                        <th>Действия</th>  
                    </tr>
                    <?php
                    $count = count($sostav);
                    for($i = 0; $i < $count; $i++)
                    {
                        $page = $sostav[$i];
                        ?>
                        <tr class="list">
                            <td><?=$page['name']?></td>
                            
                            <td>                                
                                <a onclick="return confirm('Удалить?')" href="/admin/filter/del/<?=$page['id']?>/?table=sostav"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
                    <input required type="text" name="sostav" value="" /> <input type="submit" value="Добавить" />
                </form>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>