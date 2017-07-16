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
                        <div class="top_menu_link"><a href="/admin/mailer/">Рассылка</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/?auto=true">Авторассылки</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/add/">Создание рассылки</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/?clear_mailing=true">Очистить список товаров</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/sms/">SMS рассылка</a></div>
                        <div class="top_menu_link"><a href="/admin/mailer/sms/add/">Создать SMS рассылку</a></div>
                    </div>




                    <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                        <table>
                            <tr>
                                <td>Название:</td>
                                <td>
                                    <input type="text" name="name" size="50" value="<?php if(isset($sms['name'])) echo $sms['name'];?>" />
                                </td>
                            </tr>
                            <tr>
                                <td valign="top">Шаблон:</td>
                                <td>
                                    <textarea style="width: 321px" id="contentbox" name="text"><?php if(isset($sms['text'])) echo $sms['text'];?></textarea><br />
                                    Символов: <span id="count"><?php if(isset($sms['text'])) echo mb_strlen($sms['text']); else echo '0';?></span>
                                </td>
                                <td>
                                    [name] - имя клиента
                                </td>
                            </tr>

                            <tr>
                                <td>Дата запуска:</td>
                                <td>
                                    <input type="text" name="start" size="20" value="<?php if(isset($sms['start'])) echo $sms['start']; else echo date("Y-m-d H")?>" />
                                </td>
                                <td>В формате: ГГГГ-ММ-ДД ЧЧ</td>
                            </tr>

                            <tr>
                                <td>Статус:</td>
                                <td>
                                    <input disabled type="text" name="status" size="50" value="<?php if(isset($sms['status'])) echo $sms['status']; else echo "new";?>" />
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td><input type="checkbox" name="start_now" /> Сразу запустить рассылку</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" value="<?php if($action == 'add') echo 'Создать'; elseif($action == 'edit') echo 'Сохранить';?>" /></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </td>
        </tr>
    </table>


<?php
include("footer.php");
?>
<script>
    $(document).ready(function()
    {
        $("#contentbox").keyup(function()
        {
            var box=$(this).val();
            $('#count').html(box.length);
        });
    });
</script>
