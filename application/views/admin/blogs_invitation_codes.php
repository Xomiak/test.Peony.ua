<?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <table width="100%" cellpadding="0" cellspacing="0" height="20px" bgcolor="#DDDDDD">
                <tr>
                    <td width="11px" height="20px"><img src="/img/left-hmenu.jpg" /></td>
                    <td><a href="/admin/blogs/">Блоги</a></td>
                    <td><a href="/admin/blogs/invitation_codes/">Пригласительные</a></td>
                    <td><a href="/admin/blogs/invitation_code_add/">Создать приглашение</a></td>
                    <td><a href="/admin/blogs/options/">Опции</a></td>
                    <td><a href="/admin/blogs/options/add/">Добавить опцию</a></td>
                </tr>
            </table>
            <table width="100%" cellpadding="1" cellspacing="1">
                <tr bgcolor="#EEEEEE">
                    <td><strong>Дата</strong></td>
                    <td><strong>Код</strong></td>
                    <td><strong>Выдан</strong></td>
                    <td><strong>Использован</strong></td>
                    <td><strong>Кем</strong></td>
                    <td><strong>Ссылка</strong></td>

                </tr>
                <?php
                $count = count($codes);
                for($i = 0; $i < $count; $i++)
                {
                    $code = $codes[$i];
                    ?>
                    <tr class="list">
                        <td><?=$code['date']?> <?=$code['time']?></a></td>
                        <td><?=$code['code']?></td>
                        
                        <td><?=$code['admin_login']?></td>
                        <td>
                            <?php
                            if($code['used'] == 1) echo 'Да';
                            else echo 'Нет';
                            ?>
                        </td>
                        <td><?=$code['used_login']?></td>
                        <td>
                            <?php
                            if($code['used'] != 1)
                            {
                                ?>
                                <a target="_blank" href="http://<?=$_SERVER['SERVER_NAME']?>/blog/create/<?=$code['code']?>/">
                                    http://<?=$_SERVER['SERVER_NAME']?>/blog/create/<?=$code['code']?>/
                                </a>
                                <?php
                            }
                            ?>
                        </td>
                        <td>                            
                            <a onclick="return confirm('Удалить?')" href="/admin/blogs/invitation_code_del/<?=$code['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>