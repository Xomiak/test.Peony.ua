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
                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
            
            <div class="content">
                <div class="top_menu">
                    
                </div>
                
                <div class="pagination"><?=$pager?></div>
                
                <?php
                if(isset($_GET['edit'])){
                    $c = $this->comments->getCommentById($_GET['edit']);
                    ?>
                    <div class="comment-edit">
                        <form method="post">
                            <input type="text" name="name" value="<?=$c['name']?>" size="50" /><br />
                            <textarea name="comment" style="width: 500px; height: 200px"><?=$c['comment']?></textarea><br />
                            <input type="submit" value="Сохранить" />
                        </form>
                    </div>
                    <?php
                }

                if($comments)
                {
                    $count = count($comments);
                    for($i = 0; $i < $count; $i++)
                    {
                        $c = $comments[$i];



                        echo '<table width="100%" '; 
                        if($c['active'] == 0) echo ' class="new-review" ';
                        echo' cellpadding="0" cellspacing="0">';
                        $art = $this->model_shop->getArticleById($c['shop_id']);
                        $carr = explode("*",$art['category_id']);
                        $cat_id = $carr[0];
                        $cat = $this->cat->getCategoryById($cat_id);
                        $parent = '';
                        if($cat['parent'] != 0)
                        {
                            $parent = $this->cat->getCategoryById($cat['parent']);
                        }
                        
                        echo '<tr><td><a href="/';
                        if($parent != '') echo $parent['url'];
                        else echo $cat['url'];
                        echo '/'.$art['url'].'/" target="_blank">'.$art['name'].'</a></td></tr>';                    
                        echo '<tr><td><strong>'.$c['name'].'</strong></td></tr>';
                        
                        $user = $this->users->getUserByLogin($c['login']);
                        if($user){
                            echo '<tr><td><strong><a href="/admin/users/edit/'.$user['id'].'/">'.$user['login'].'</a></strong></td></tr>';
                        }
                        echo '<tr><td>Оценка: </strong>'.$c['rate'].'</td></tr>';
                        echo '<tr><td><font size="1">'.$c['date'].' '.$c['time'].'</font></td></tr>';
                        echo '<tr><td><font size="2">'.$c['ip'].'</font></td></tr>';
                        echo '<tr><td>'.$c['comment'].'</td></tr>';
                        echo '<tr><td><font size="2">';

                        if($c['images'] != '' && $c['images'] != NULL){
                            echo '<tr><td>';
                            $images = json_decode($c['images']);
                            foreach ($images as $image){
                                echo '<a target="_blank" href="'.$image.'"><img src="'.$image.'" style="max-width: 200px; max-height: 200px" /></a>';
                            }
                            echo '</td></tr>';

                        }

                        if($c['active'] == 0)
                            echo '<a href="/admin/comments/active/'.$c['id'].'/">Активировать</a> | ';

                        echo '<a href="'.request_uri(false, true).'?edit='.$c['id'].'">Редактировать</a> | <a href="/admin/comments/del/'.$c['id'].'/" onclick="return confirm(\'Удалить?\')">Удавить</a></font></td></tr>';
                        echo '<tr><td><hr /></td></tr>';
                        echo "</table>";
                    }
                }
                ?>
                
                <div class="pagination"><?=$pager?></div>
            </div>
        </td>
    </tr>
</table>

<?php
include("footer.php");
?>