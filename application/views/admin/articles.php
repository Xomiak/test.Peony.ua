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
                    <div class="top_menu_link"><a href="/admin/articles/">Статьи</a></div>
                    <div class="top_menu_link"><a href="/admin/articles/add/">Добавить статью</a></div>
                    <div class="top_menu_link">
                        <form method="post" action="/admin/articles/set_category/">
                            Выбор раздела:
                            <SELECT name="category_id" onchange="submit();">
                                <option value="all">Все</option>
                                <?php
                                $count = count($categories);
                                for($i = 0; $i < $count; $i++)
                                {
                                    $cat = $categories[$i];
                                    if($cat['type'] == 'articles') {
                                        echo '<option value="' . $cat['id'] . '"';
                                        if ($this->session->userdata('articles_category_id') == $cat['id']) echo ' selected';
                                        echo '>' . $cat['name'] . '</option>';
                                        $subcats = $this->mcats->getSubCategories($cat['id']);
                                        if ($subcats) {
                                            $subcount = count($subcats);
                                            for ($j = 0; $j < $subcount; $j++) {
                                                $sub = $subcats[$j];
                                                echo '<option value="' . $sub['id'] . '"';
                                                if ($this->session->userdata('articles_category_id') == $sub['id']) echo ' selected';
                                                echo '>&nbsp;└&nbsp;' . $sub['name'] . '</option>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </SELECT>
                        </form>
                    </div>
                    <div class="top_menu_link">
                        <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                            Поиск:<input type="text" name="search" value="<?php if(isset($_POST['search'])) echo $_POST['search']; ?>" style="width:500px" />
                            <input type="submit" value="Искать" />
                        </form>
                    </div>
                </div>
                
                <div class="pagination"><?=$pager?></div>
            
                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th>Название</th>
                        <th>Позиция</th>
                        <th>Раздел</th>
                        <th>Счётчик</th>
                        <th>Вверх/Вниз</th>
                        <th>Действия</th>
                    </tr>
                    <?php
                    if($articles)
                    {
                        $count = count($articles);
                        for($i = 0; $i < $count; $i++)
                        {
                            $article = $articles[$i];
                            $user = $this->users->getUserByLogin($article['login']);
                            ?>
                            <tr class="list">
                                <td><a title="Перейти к редактированию статьи" href="/admin/articles/edit/<?=$article['id']?>/"><?=$article['name']?></a></td>
                                <td><?=$article['num']?></td>
                                <td><?$cat = $this->mcats->getCategoryById($article['category_id']); echo '<a href="/admin/categories/edit/'.$cat['id'].'/" title="Перейти к редактированию раздела">'.$cat['name'].'</a>';?></td>
                                <td><?=$article['count']?></td>
                                <td><a href="/admin/articles/up/<?=$article['id']?>/"><img src="/img/uparrow.png" border="0" alt="Вверх" title="Вверх" /></a><a href="/admin/articles/down/<?=$article['id']?>/"><img src="/img/downarrow.png" border="0" alt="Вниз" title="Вниз" /></a></td>
                                
                                <td>
                                    <a href="/admin/articles/always_first/<?=$article['id']?>/"><?php
                                    if($article['always_first'] == 1)
                                        echo '<img src="/img/always_first.png" width="16px" height="16px" border="0" title="Открепить" />';
                                    else
                                        echo '<img src="/img/not-always_first.png" width="16px" height="16px" border="0" title="Прикрепить" />';
                                    ?></a>
                                    <a href="/admin/users/sendmail/<?=$user['id']?>/" title="Отправить письмо автору"><img src="/img/mail.png" border="0" /></a>
                                    <a href="/admin/articles/active/<?=$article['id']?>/"><?php
                                    if($article['active'] == 1)
                                        echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивировать" />';
                                    else
                                        echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активировать" />';
                                    ?></a>
                                    <a href="/admin/articles/edit/<?=$article['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                    <a  onclick="return confirm('Удалить?')" href="/admin/articles/del/<?=$article['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>                
                </table>
                <br />
                <div class="pagination"><?=$pager?></div>
            </div>
        </td>
    </tr>
</table>

<?php
include("footer.php");
?>