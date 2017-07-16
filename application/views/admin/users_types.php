<?php
include("header.php");
$main_currency = getOption('main_currency');
?>
<script type="text/javascript">
var j = jQuery.noConflict();
j(document).ready(function() { 
      
   
</script>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" valign="top"><?php include("menu.php"); ?></td>
        <td width="20px"></td>
        <td valign="top">
            <div class="title_border">
                <div class="content_title"><h1><?=$title?></h1></div>
                <div class="back_and_exit">
                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
            
            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/users/">Клиенты</a></div>
                    <div class="top_menu_link"><a href="/admin/users/export/">Экспорт</a></div>
                    <div class="top_menu_link"><a href="/admin/users/types/">Типы клиентов</a></div>
                    <!--div class="top_menu_link"><a href="/admin/users/add/">Добавить пользователя</a></div-->
                </div>

                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th><a href="?sort=id">ID</a></th>   
                        <th><a href="?sort=user_type">Тип</a></th>
                        <th>Поздравление с ДР</th>
                        <th>Подарочная скидка</th>
                        <th>Текст поздравления</th>
                        <th>Надбавочная стоимость</th>
                        <th>Действия</th>
    
                    </tr>
                    <?php
                    $count = count($userTypes);
                    for($i = 0; $i < $count; $i++)
                    {
                        $ut = $userTypes[$i];
                        if(isset($_GET['edit']) && $_GET['edit'] == $ut['id'])
                        {
                            ?>
                            <form method="post" action="/admin/users/types/">                            
                            <tr class="list edit" colspan="4">
                                <td><?=$ut['id']?><input type="hidden" name="id" value="<?=$ut['id']?>" /></td>
                                
                                <td><input type="text" name="type" placeholder="Название нового типа" value="<?=$ut['name']?>" /></td>
                                <td>
                                    <input id="bd_mailing_edit" type="checkbox" name="bd_mailing" <?php if($ut['bd_mailing'] == 1) echo " checked"; ?>>                     
                                </td>

                                
                                <td><input size="2" type="text" name="discount" value="<?=$ut['discount']?>">%</td>
                                <td><textarea name="congratulation"><?=$ut['congratulation']?></textarea></td>
                                <td><input size="2" type="text" name="nadbavka" value="<?=$ut['nadbavka']?>"> <?=$main_currency?></td>
                                <td>
                                    <input type="submit" value="Сохранить" name="save_type">
                                    <input type="submit" value="Отмена" name="cancel">
                                </td>
                            </tr>
                            </form>
                            <?php
                        }
                        else
                        {
                        ?>
                            <tr class="list">
                                <td><?=$ut['id']?></td>
                                
                                <td><?=$ut['name']?></td>
                                <td>
                                    <?php
                                    if($ut['bd_mailing'] == 1) echo "да";
                                    else echo "нет";
                                    ?>
                                </td>
                                <td><?=$ut['discount']?>%</td>
                                <td><?=$ut['congratulation']?></td>
                                <td><?=$ut['nadbavka']?> <?=$main_currency?></td>
                                <td>
                                    <a href="/admin/users/type_active/<?=$ut['id']?>/"><?php
                                    if($ut['active'] == 1)
                                        echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                    else
                                        echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                    ?></a>
                                    <a href="<?=request_uri()?>?edit=<?=$ut['id']?>"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                    <a onclick="return confirm('Удалить?')" href="/admin/users/type_del/<?=$ut['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                </table>
            </div>

            <?php
            if(!isset($_GET['edit']))
            {
                ?>
                <div class="content">
                    <h3>Добавить тип</h3>
                    <br />
                    <form method="post">
                        <input type="text" name="new_type" placeholder="Название нового типа" />
                        <input id="bd_mailing" type="checkbox" name="bd_mailing" checked> Поздравлять с ДР
                        <span id="discount">Подарочная скидка: <input type="text" name="discount" value="0">%</span>
                        <span id="nadbavka">Надбавочная стоимость: <input type="text" name="nadbavka" value="0"> <?=$main_currency?></span>
                        <input type="submit" value="Добавить" />
                    </form>
                    <script type="text/javascript">
                    $(document).ready( function() {
                        $("#bd_mailing").click( function() { // при клике по главному чекбоксу
                            if($("#bd_mailing").attr('checked')){ // проверяем его значение
                                $('#discount').show();
                            } else {
                                $('#discount').hide();
                            }
                        });
                    });
                    </script>
                </div>
                <?php
            }
            ?>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>