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
            
            <div class="show_only">
                <table style="margin-left: 10px;" width="100%">
                    <tr>
                        <td valign="top">
                            <a href="/admin/forum/messages/reserve_only/">Показать помеченные</a>
                        </td>
                        <td valign="bottom">
                            <form method="POST" action="/admin/forum/messages/search/">
                                <input required type="text" placeholder="Поиск по сообщениям..." name="search" size="30" />
                                <input type="submit" value="искать" />
                            </form>
                        </td>
                        <td valign="bottom">
                            <form method="POST" action="/admin/forum/messages/messages_search_user/">
                                <input required type="text" placeholder="Поиск по логину..." name="search" size="30" />
                                <input type="submit" value="искать" />
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
            
            <center><?=$pager?></center>
            
            <?php
            if($messages)
            {
                ?>
                <table width="100%">
                    <tr bgcolor="#EEEEEE">
                        <td><strong>Дата</strong></td>
                        <td><strong>Сообщение</strong></td>
                        <td><strong>Логин</strong></td>
                        <td><strong>Тема</strong></td>
                        <td><strong>Раздел</strong></td>                        
                        <td><strong>Главное в теме</strong></td>
                    </tr>
                <?php
                $count = count($messages);
                for($i = 0; $i < $count; $i++)
                {
                    $message    = $messages[$i];
                    $user       = $this->users->getUserByLogin($message['login']);
                    $topic      = $this->forum->getTopicById($message['topic_id']);
                    $subsection = $this->forum->getSectionById($topic['section_id']);
                    $section    = false;
                    if($subsection['parent_id'] == 0)
                    {
                        $section    = $subsection;
                        $subsection = false;
                    }
                    else
                        $section = $this->forum->getSectionById($subsection['parent_id']);
                    ?>
                    <tr class="list">
                        <td><?=$message['date']?> <?=$message['time']?></td>
                        <td>
                            <?php
                            $admin_message_strlen = $this->forum->getOption('admin_message_strlen');
                            if(!$admin_message_strlen) $admin_message_strlen = 500;
                            if(strlen($message['message']) > $admin_message_strlen)
                            {
                                echo substr($message['message'], 0, $admin_message_strlen).'...';
                            }
                            else echo $message['message'];
                            ?>
                        </td>
                        <td>
                            <a href="/admin/users/edit/<?=$user['id']?>/" title="Перейти к редактированию пользователя">
                                <?=$message['login']?>
                            </a>
                        </td>
                        <td>
                            <a href="/admin/forum/topics/edit/<?=$topic['id']?>/">
                                <?=$topic['name']?>
                            </a>
                        </td>
                        <td>
                            <a href="/admin/forum/sections/edit/<?=$section['id']?>/"><?=$section['name']?></a><?php
                            if($subsection)
                            {
                                ?>&nbsp;→&nbsp;<a href="/admin/forum/sections/edit/<?=$subsection['id']?>/"><?=$subsection['name']?></a>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($message['topic_message'] == 1) echo '<strong>да</strong>';
                            else echo 'нет';
                            ?>
                        </td>
                        
                        <td>
                            <a href="/admin/forum/messages/reserve/<?=$message['id']?>/"><?php
                            if($message['reserve'] == 1)
                                echo '<img src="/img/reserve.png" width="16px" height="16px" border="0" title="Убрать пометку" />';
                            else
                                echo '<img src="/img/not-reserve.png" width="16px" height="16px" border="0" title="Пометить" />';
                            ?></a>
                            <a href="/admin/forum/messages/active/<?=$message['id']?>/"><?php
                            if($message['active'] == 1)
                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                            else
                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                            ?></a>
                            <a href="/admin/forum/messages/edit/<?=$message['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <a onclick="return confirm('Удалить?')" href="/admin/forum/messages/del/<?=$message['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                        </td>
                        
                    </tr>
                    <?php
                }
                ?>
                </table>
                <?php
            }
            else
            {
                ?>
                <center><h1>Сообщений не найдено...</h1></center>
                <?php
            }
            ?>
            
            <center><?=$pager?></center>
            
            </td>
    </tr>
</table>
<?php
include("application/views/admin/footer.php");
?>