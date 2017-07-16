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
                        <div class="top_menu_link"><a href="/admin/mailer/add/">Создание рассылки</a></div>
                    </div>


                    <button id="start">Запустить рассылку</button>
                    <button id="stop" style="display: none">Остановить рассылку</button><br/>
                    <textarea id="result" style="width: 600px;height:600px"></textarea>

                </div>
            </td>
        </tr>
    </table>

    <script src="/js/jquery-1.7.1.min.js"></script>
<script>
    var line = 1;
    var emailsCount = 0;
    $(document).ready(function () {
        alert("asd");
        $("#start").click(function () {
            addLine("Приступаем к рассылке...");
            $("#start").hide();
            $("#stop").show();
            //getCount();
        });
    });

    // отправляем следующее письмо
    function send() {
        console.log("Приступаем к отправке");
        $.ajax({
            url: '/admin/ajax/mail_send/<?=$mailer['id']?>/',
            method: 'post',
            data: {
                "id": <?=$mailer['id']?>
            },

        }).done(function (data) {
            if(data == 'end') addLine('Рассылка завершена!');
            else{
                addLine(line+": "+data);
                line++;
                send();
            }
//            var obj = jQuery.parseJSON(data);
//            if(obj.value == 'ended') {
//                console.log(obj.msg);
//                addLine(obj.msg);
//            }
//            else {
//                console.log(obj.msg);
//                addLine(obj.msg);
//                send();
//            }
        });
    }
</script>

<?php
include("footer.php");
?>