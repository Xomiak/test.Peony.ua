<?php
include("header.php");
?>
    <table width="100%" height="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("menu.php"); ?></td>
            <td width="20px" align="right" valign="top"><div class="border"> </div></td>
            <td valign="top">
                <div class="title_border">
                    <div class="content_title"><h1><?=$title?></h1></div>
                    <div class="back_and_exit">

                        <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                        <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                    </div>
                </div>

                <div class="content">
                    <iframe class="box" width="100%" height="200px" src="http://slider.peony.ua/index.php?c=account&m=login&username=admin&password=123qweasdzxc"></iframe>
                </div>


            </td>
        </tr>
    </table>
    <script>
        $( document ).ready(function() {
            var $box = $('.box'); // кэшируем результат вызова функции
            $box.height($(window).height());
        });

        $(function(){
            $(window).resize(function() {
                var $box = $('.box'); // кэшируем результат вызова функции
                $box.height($(window).height());
            })
        })
    </script>
<?php
include("footer.php");
?>