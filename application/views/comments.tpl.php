
<a name="comments"></a>
<h3 class="h3_comments">Комментарии</h3>
<?php
if(!$comments)
    echo '<p class="items_comments">Ваш комментарий может быть первым</p>';
else
{
    ?>
    
    <?php
    $count = count($comments);
    for($i = 0; $i < $count; $i++)
    {
        $com = $comments[$i];        
        $user = $this->users->getUserByLogin($com['name']);
        ?>
        <table width="98%">
            <tr>
                
                <td valign="top">
                    <div class="comments_name"><?=$com['name']?></div>
                    <div class="comments_date"><?=$com['date']?> <?=$com['time']?></div>
                    <?php
                    $this->load->helper("translit_helper");
                    $com['comment'] = BBCodesToHtml($com['comment']);
                    ?>
                    <p class="comments_comment"><?=$com['comment']?></p>
                                        
                    <?php
                    if (isClientAdmin())
                    {
                        echo '<p class="admin_comment_del"><a rel="nofollow" href="/admin/comments/del/'.$comments[$i]['id'].'/" onclick="return confirm(\'Удалить?\')">Удалить комментарий</a></p>';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <hr />
               
        <?php
    }
    ?>

    <?php
}
?>

<h3 class="h3_comments">Оставить комментарий</h3>

<?php
$show_add_form = true;
$comment_reg_only = $this->model_options->getOption('comment_reg_only');
if($comment_reg_only == 1)
{
    if($this->session->userdata('login') == false)
        $show_add_form = false;
}
if($show_add_form)
{
    $article_id = '';
    if($this->session->userdata('comment_article_id') != null)
    {
        $article_id = $this->session->userdata('comment_article_id');
        $this->session->unset_userdata('comment_article_id');
    }
    $image_id = '';
    if($this->session->userdata('comment_image_id') != null)
    {
        $article_id = $this->session->userdata('comment_image_id');
        $this->session->unset_userdata('comment_image_id');
    }
    $name = '';
    if($this->session->userdata('comment_name') != null && $article_id == $article['id'])
    {
        $name = $this->session->userdata('comment_name');
        $this->session->unset_userdata('comment_name');
    }
    $comment = '';
    if($this->session->userdata('comment_comment') != null && $article_id == $article['id'])
    {
        $comment = $this->session->userdata('comment_comment');
        $this->session->unset_userdata('comment_comment');
    }
    
    $commentAnswer = false;
    if($this->session->userdata('commentAnswer') !== false)
    {
        $commentAnswer = $this->session->userdata('commentAnswer');
        $this->session->unset_userdata('commentAnswer');
    }
    
    if($commentAnswer)
    {
        $comment = '[quote]';
        $comment .= '[b]'.$commentAnswer['name'].' ([i]'.$commentAnswer['date'].' '.$commentAnswer['time'].'[/i]):[/b][br]'."\r\n";
        $comment .= $commentAnswer['comment'].'[/quote]'."\r\n";
    }
    
    ?>
    <a name="add_comment_form"></a>
    <form action="/comments/add/" method="post">
        <?php
        if($commentAnswer)
        {
            ?>
            <input type="hidden" name="answer" value="<?=$commentAnswer['name']?>" />
            <?php
        }
        ?>
       
            <label class="label_comments">Ваше имя</label><br />    
            <input type="text" name="name" class="input_comments" value="<?=$name?>" /><br />
        
        <?php
        if($this->session->userdata('err_name') != null)
        {
            echo '<p class="comment_err">'.$this->session->userdata('err_name').'</p>';
            $this->session->unset_userdata('err_name');
        }
        ?>
        <label class="label_comments">Комментарий</label><br />
        <?php
        if($comment != '')
        {
            ?>
            <strong>Ответ на сообщение:</strong><br />
            <?=BBCodesToHtml($comment)?>
            <input type="hidden" name="answerComment" value="<?=$comment?>" />
            <?php
        }
        ?>
        <textarea name="comment" class="textarea_comments"></textarea><br />
        <?php
        if($this->session->userdata('err_comment') != null)
        {
            echo '<p class="comment_err">'.$this->session->userdata('err_comment').'</p>';
            $this->session->unset_userdata('err_comment');
        }
        ?>
        <label class="label_comments">Проверка на человечность</label><br />
        <?=$cap['image']?><br />
        <input type="text" name="captcha" value="" />
        <?php
        if(isset($err['captcha']) && $err['captcha'] != '')
        {
            ?>
            <div class="error"><?=$err['captcha']?></div>
            <?php
        }
        ?>
        <?php
        if($this->session->userdata('err_spam') != null)
        {
            echo '<p class="comment_err">'.$this->session->userdata('err_spam').'</p>';
            $this->session->unset_userdata('err_spam');
        }
        
        ?>
        <br />
        <input type="hidden" name="article_id" value="<?php if(isset($article['id'])) echo $article['id']; else echo '0'; ?>" />
        <input type="hidden" name="image_id" value="<?php if(isset($image['id'])) echo $image['id']; else echo '0'; ?>" />
        <input type="hidden" name="back" value="<?=$_SERVER['REQUEST_URI']?>" />
        <input type="submit" value="Прокомментировать" />
    </form>
<?php
}
else
{
    ?>
    <strong>Только зарегистрированные пользователи могут оставлять комментарии!</strong>
    <?php
}
?>