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
                    <div class="top_menu_link"><a href="/admin/subscription/">Рассылка</a></div>
                </div>
                Подписчиков: <?=$subscriptions?>
                
                <?php
                if($this->session->userdata('subscription_sended') !== false)
                {
                    ?>
                    <h1 style="color:red">Рассылка отправлена!</h1>
                    <?php
                    $this->session->unset_userdata('subscription_sended');
                }
                ?>
                
                <h2>Новая рассылка</h2>
                <form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
                    <textarea name="subscription"  class="ckeditor"></textarea>
                    <input type="submit" value="Отправить" />
                </form>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>