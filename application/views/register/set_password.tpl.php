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
                                  
                                    <p>Установите новый пароль</p>
                                    <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                                        <table border="0" cellpadding="1" cellspacing="1">                                            
                                            <tr>
                                                <td align="right" valign="top">
                                                    Пароль *:
                                                </td>
                                                <td>
                                                    <input required type="password" name="pass" size="50" />                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="top">
                                                    Ещё раз *:
                                                </td>
                                                <td>
                                                    <input required type="password" name="pass2" size="50" />                                                    
                                                </td>
                                            </tr>
                                                                                        
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Изменить!" />
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