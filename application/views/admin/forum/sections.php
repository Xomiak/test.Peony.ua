<?php
include("application/views/admin/header.php");
?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("application/views/admin/menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <table width="100%" cellpadding="0" cellspacing="0" height="20px" bgcolor="#DDDDDD">
                <tr>
                    <td width="11px" height="20px"><img src="/img/left-hmenu.jpg" /></td>
                    <td><a href="/admin/forum/sections/">Разделы</a></td>
                    <td><a href="/admin/forum/sections/add/">Добавить раздел</a></td>
                    <td><a href="/admin/forum/topics/">Темы</a></td>
                    <td><a href="/admin/forum/topics/add/">Добавить тему</a></td>
                    <td><a href="/admin/forum/messages/">Сообщения</a></td>
                    <td><a href="/admin/forum/options/">Настройки</a></td>
                    <td><a href="/admin/forum/options/add/">Добавить опцию</a></td>
                </tr>
            </table>
            <br />
            <?php
            if($hSections)
            {
                ?>
                <table width="100%">
                    <tr bgcolor="#EEEEEE">
                        <td><strong>Название</strong></td>
                        <td><strong>Позиция</strong></td>
                        <td><strong>Вверх/Вниз</strong></td>
                        <td><strong>Создание тем</strong></td>
                        <td><strong>Кол-во тем</strong></td>
                    </tr>
                <?php
                $count = count($hSections);
                for($i = 0; $i < $count; $i++)
                {
                    $sec = $hSections[$i];
                    ?>
                    <tr class="list">
                        <td>
                            <a href="/admin/forum/sections/edit/<?=$sec['id']?>/"><?=$sec['name']?></a>
                        </td>
                        <td>
                            <?=$sec['num']?>
                        </td>
                        <td>
                            <a href="/admin/forum/sections/up/<?=$sec['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                            <a href="/admin/forum/sections/down/<?=$sec['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a>
                        </td>
                        <td>
                            <?php
                            if($sec['create_topics'] == 1) echo 'да';
                            else echo 'нет';
                            ?>
                        </td>
                        <td>
                            <?php
                            if($sec['create_topics'] == 1)
                            {
                                $topics_count = count($this->forum->getTopicsBySectionId($sec['id']));
                                ?>
                                <a href="/admin/forum/topics/show_only_section_id/<?=$sec['id']?>/" title="Перейти к темам раздела <?=$sec['name']?>">
                                <?=$topics_count?>
                                </a>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <a href="/admin/forum/sections/active/<?=$sec['id']?>/"><?php
                            if($sec['active'] == 1)
                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                            else
                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                            ?></a>
                            <a href="/admin/forum/sections/edit/<?=$sec['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <a onclick="return confirm('Удалить?')" href="/admin/forum/sections/del/<?=$sec['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                        </td>
                    </tr>
                    <?php
                    
                    /////////////////////////
                    // ПОДРАЗДЕЛЫ ///////////
                    $sections = $this->forum->getSectionsByParentId($sec['id']);
                    if($sections)
                    {
                        $count2 = count($sections);
                        for($j = 0; $j < $count2; $j++)
                        {
                            $ssec = $sections[$j];
                            ?>
                            <tr class="list">
                                <td>
                                    &nbsp;└&nbsp;<a href="/admin/forum/sections/edit/<?=$ssec['id']?>/"><?=$ssec['name']?></a>
                                </td>
                                <td>
                                    &nbsp;└&nbsp;<?=$ssec['num']?>
                                </td>
                                <td>
                                    <a href="/admin/forum/sections/up/<?=$ssec['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a>
                                    <a href="/admin/forum/sections/down/<?=$ssec['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a>
                                </td>
                                <td>
                                    <?php
                                    if($ssec['create_topics'] == 1) echo 'да';
                                    else echo 'нет';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($ssec['create_topics'] == 1)
                                    {
                                        $topics_count = count($this->forum->getTopicsBySectionId($ssec['id']));
                                        ?>
                                        <a href="/admin/forum/topics/show_only_section_id/<?=$ssec['id']?>/" title="Перейти к темам раздела <?=$ssec['name']?>">
                                        <?=$topics_count?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="/admin/forum/sections/active/<?=$ssec['id']?>/"><?php
                                    if($ssec['active'] == 1)
                                        echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                    else
                                        echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                    ?></a>
                                    <a href="/admin/forum/sections/edit/<?=$ssec['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                    <a onclick="return confirm('Удалить?')" href="/admin/forum/sections/del/<?=$ssec['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                </table>
                <?php
            }
            ?>
        </td>
    </tr>
</table>
<?php
include("application/views/admin/footer.php");
?>