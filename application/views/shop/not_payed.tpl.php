<?php include("application/views/head_new.php"); ?><?php include("application/views/header_new.php"); ?>
    <div id="main_content">
        <div id="my_cart">
            <h1>Ошибка платежа</h1>
            <?php
            if(isset($result->err_code)) echo 'Код ошибки: '.$result->err_code;
            if(isset($result->err_code)) echo 'Описание ошибки: '.$result->err_description;
            ?>
        </div>
    </div>
    <div class="clr"></div>
<?php include("application/views/footer_new.php"); ?>