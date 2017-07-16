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
            if($topics)
            {
                ?>
                
                <?php
                if($sections)
                {
                    $count = count($sections);                    
                    ?>
                    <div class="show_only">
                        <table>
                            <tr>
                                <td>
                                    <form class="show_only_section_id_form" name="show_only_section_id_form" method="POST" action="/admin/forum/topics/show_only_section_id/">
                                        Выборка по разделам: <SELECT name="show_only_section_id" onChange="document.show_only_section_id_form.submit()">
                                            <option value="-1"<?php
                                            if($this->session->userdata('section_id') === false) echo ' selected';
                                            ?>>Все</option>
                                            <?php
                                            for($i = 0; $i < $count; $i++)
                                            {
                                                $sec = $sections[$i];
                                                echo '<option value="'.$sec['id'].'"';
                                                if($sec['create_topics'] != 1) echo ' disabled';
                                                if(($this->session->userdata('section_id') !== false) && $sec['id'] == $this->session->userdata('section_id'))
                                                    echo ' selected';
                                                echo '>'.$sec['name'].'</option>';
                                                
                                                $subsections = $this->forum->getSectionsByParentId($sec['id']);
                                                if($subsections)
                                                {
                                                    $subcount = count($subsections);
                                                    for($j = 0; $j < $subcount; $j++)
                                                    {
                                                        $sub = $subsections[$j];
                                                        echo '<option value="'.$sub['id'].'"';
                                                        if($sub['create_topics'] != 1) echo ' disabled';
                                                        if(($this->session->userdata('section_id') !== false) && $sub['id'] == $this->session->userdata('section_id'))
                                                            echo ' selected';
                                                        echo '>&nbsp;└&nbsp;'.$sub['name'].'</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </SELECT>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="/admin/forum/topics/search/">
                                        <input type="text" placeholder="Поиск..." name="search" size="30" />
                                        <input type="submit" value="искать" />
                                    </form>
                                </td>
                            </tr>
                        </table>

                    </div>
                    
                    
                    <?php
                }
                ?>

                <center><?=$pager?></center>
                <table width="100%">
                    <tr bgcolor="#EEEEEE">
                        <td><strong>Название</strong></td>
                        <td><strong>Раздел</strong></td>
                        <td><strong>Кол-во сообщений</strong></td>
                        <td><strong>Дата последнего</strong></td>
                    </tr>
                <?php
                $count = count($topics);
                for($i = 0; $i < $count; $i++)
                {
                    $topic = $topics[$i];
                    $section = $this->forum->getSectionById($topic['section_id']);
                    ?>
                    <tr class="list">
                        <td>
                            <a href="/admin/forum/topics/edit/<?=$topic['id']?>/"><?=$topic['name']?></a>
                        </td>
                        <td>
                            <a href="/admin/forum/sections/edit/<?=$section['id']?>/"><?=$section['name']?></a>
                        </td>
                        <td>
                            <?=count($this->forum->getMessagesByTopicId($topic['id']))?>
                        </td>
                        <td>
                            <?=$topic['lastmsgdatetime']?>
                        </td>
                        <td>
                            <a href="/admin/forum/topics/active/<?=$topic['id']?>/"><?php
                            if($topic['active'] == 1)
                                echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                            else
                                echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                            ?></a>
                            <a href="/admin/forum/topics/edit/<?=$topic['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                            <a onclick="return confirm('Удалить?')" href="/admin/forum/topics/del/<?=$topic['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </table>
                <center><?=$pager?></center>
                <?php
            }
            ?>
            
            </td>
    </tr>
</table>
<?php
include("application/views/admin/footer.php");
?>