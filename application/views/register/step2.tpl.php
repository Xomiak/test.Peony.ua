<?php include("application/views/header.php") ?>

<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
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
                                            Регистрация
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="long">Регистрация</h1>
                                    
                                    <strong>Регистрация прошла успешно!</strong>
                                    <?php
                                    if($email_confirm == 1)
                                    {
                                    ?>
                                        <br />                                    
                                        На указанный Вами e-mail отправлено письмо с инструкциями по активации аккаунта.
                                    <?php
                                    }
                                    ?>
                                    <br /><br />
                                    <a href="/">Вернуться на главную</a>
                                </td>
                            </tr>
                        </table>                        
                    </td>
                </tr>
            </table>            
        </td>
    </tr>
</table>
<?php include("application/views/footer.php") ?>