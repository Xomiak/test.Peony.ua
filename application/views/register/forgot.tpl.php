<?php include("application/views/header.php") ?>

<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="200px" align="left" valign="top">
                        <?php
                        include('application/views/left.tpl.php');
                        ?>
                    </td>
                    <td valign="top">
                        <table width="97%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr>
                                <td valign="top">
                                    <div class="kroshki">
                                        <div xmlns:v="http://rdf.data-vocabulary.org/#">
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            <?=$breadcrumbs?>
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="long"><?=$h1?></h1>
                                    
                                    <?php
                                    if(isset($err['err']) && $err['err'] != '')
                                    {
                                        ?>
                                        <div class="error"><?=$err['err']?></div>
                                        <?php
                                    }
                                    ?>
                                  
                                    <p>Для восстановления логина и/или пароля введите свой электронный адрес, на который был зарегистрирован Ваш аккаунт.</p>
                                    <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                                        <table border="0" cellpadding="1" cellspacing="1">                                            
                                            <tr>
                                                <td align="right" valign="top">
                                                    Введите Ваш e-mail *:
                                                </td>
                                                <td>
                                                    <input required type="email" name="email" size="50" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" />
                                                    <?php
                                                    if(isset($err['email']) && $err['email'] != '')
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['email']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td align="right" valign="top">
                                                    Введите цифры *:
                                                </td>
                                                <td>
                                                    <?=$cap['image']?><br />
                                                    <input required type="text" name="captcha" value="" />
                                                    <?php
                                                    if(isset($err['captcha']) && $err['captcha'] != '')
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['captcha']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Восстановить!" />
                                                </td>
                                            </tr>
                                        </table>                                        
                                    </form>
                                    <p class="warning">Поля, отмеченные <strong>*</strong>, обязательны для заполнения!</p>
                                </td>
                            </tr>
                        </table>                        
                    </td>
                </tr>
            </table>            
        </td>
        <td width="200px" valign="top" align="center">            
            <?php
            include('application/views/right.tpl.php');
            ?>
        </td>
    </tr>
</table>
<?php include("application/views/footer.php") ?>