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
        ?>
        <p>
            <span class="comments_name"><?=$comments[$i]['name']?></span>
            <span class="comments_date"><?=$comments[$i]['date']?> <?=$comments[$i]['time']?></span>
        </p>
        <p class="comments_comment"><?=$comments[$i]['comment']?></p>
        <?php
        if (isClientAdmin())
        {
            echo '<p class="admin_comment_del"><a rel="nofollow" href="/admin/comments/del/'.$comments[$i]['id'].'/" onclick="return confirm(\'Удалить?\')">Удалить комментарий</a></p>';
        }
        ?>
        <hr />
        <?php
    }
    ?>

    <?php
}
?>

<h3 class="h3_comments">Оставить комментарий</h3>

<?php

$name = '';
if($this->session->userdata('comment_name') != null)
{
    $name = $this->session->userdata('comment_name');
    $this->session->unset_userdata('comment_name');
}
$comment = '';
if($this->session->userdata('comment_comment') != null)
{
    $comment = $this->session->userdata('comment_comment');
    $this->session->unset_userdata('comment_comment');
}

?>
<form action="/comments/add/" method="post">
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
    <textarea name="comment" class="textarea_comments"><?=$comment?></textarea><br />
    <?php
    if($this->session->userdata('err_comment') != null)
    {
        echo '<p class="comment_err">'.$this->session->userdata('err_comment').'</p>';
        $this->session->unset_userdata('err_comment');
    }
    ?>
    <label class="label_comments">Проверка от спамеров</label><br />
    <strong><?=$spambot['question']?></strong><br />
    <input type="hidden" name="question" value="<?=$spambot['question']?>" />
    <input class="input_comments" style="margin-top:5px;" type="text" name="keystring"><br />
    <?php
    if($this->session->userdata('err_spam') != null)
    {
        echo '<p class="comment_err">'.$this->session->userdata('err_spam').'</p>';
        $this->session->unset_userdata('err_spam');
    }
    ?>
    
    <input type="hidden" name="article_id" value="" />
    <input type="hidden" name="image_id" value="<?=$image['id']?>" />
    <input type="hidden" name="page_id" value="" />
    <input type="hidden" name="back" value="<?=$_SERVER['REQUEST_URI']?>" />
    <input type="submit" value="Прокомментировать" />
</form>