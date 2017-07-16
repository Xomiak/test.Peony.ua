<?php include("application/views/head_new.php"); ?>
<?php include("application/views/header_new.php"); ?>

<section class="container user-date">
    <h1><?= $h1 ?></h1>
    <?php
    if(isset($msg))
        echo $msg;
    ?>
    <br /><br /><br /><br /><br /><br /><br /><br /><br />
</section>
<?php include("application/views/footer_new.php"); ?>
