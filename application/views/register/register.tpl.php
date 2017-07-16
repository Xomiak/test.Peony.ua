<?php include("application/views/header.php") ?>
<!--
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
                    -->
                        <table class="register-field" width="97%" cellpadding="0" cellspacing="0" border="0" align="center">
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
                                    
                                    <?php
                                    /*
                                    if(isset($err['err']) && $err['err'] != '')
                                    {
                                        ?>
                                        <div class="error"><?=$err['err']?></div>
                                        <?php
                                    }
                                    */
                                    
                                    //var_dump($err);
                                    ?>
                                  
                                    <form enctype="multipart/form-data" action="/register/" method="post">
                                        <table border="0" cellpadding="1" cellspacing="1">
                                            <tr>                                                
                                                <td>
                                                    <input required type="text" name="name" placeholder="Имя и фамилия" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>" />
                                                </td>
                                            </tr>
                                                <td>
                                                        <input required type="email" name="email" size="50" placeholder="E-mail" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" />
                                                </td>
                                            <tr>
                                            	<td>
                                                    <?php
                                                    if(isset($err['email']) && $err['email'] != '')
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['email']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                            	</td>
                                            <tr>                                                
                                                <td>
                                                    <input id="phone" required type="text" name="tel" size="50" placeholder="Номер телефона" value="" />
                                                    <script type="text/javascript">
														jQuery(function($){
														   $("#phone").mask("+7 (999) 999-9999");
														});
													</script>
                                                </td>
                                            </tr>
                                            <tr>                                                
                                                <td>
                                                    <input required type="password" name="pass" size="50" placeholder="Пароль"/>
                                                </td>
                                            </tr>
                                            <tr>                                              
                                                <td>
													<?php
                                                    if(isset($err['pass']) && $err['pass'] != '')
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['pass']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>                                              
                                                <td>
                                                    <input required type="password" name="pass2" size="50" placeholder="Повторите пароль"/>
                                                </td>
                                            </tr>
                                            <tr>                                                
                                                <td>
                                                    <input required type="text" name="city" size="50" placeholder="Город" value="" />
                                                </td>
                                            </tr>
                                            <tr>
                                                    <td>
                                                        <input required type="text" name="captcha" size="50" placeholder="Введите символы с картинки" value="" />
                                                    </td>
                                                </tr>
                                             <tr>                                                
                                                <td>
                                                    <?=$cap['image']?>                                                                                                 
                                                </td>
                                              </tr>
                                              <tr>                                                
                                                <td>                                                    
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
                                                    <input type="submit" value="зарегистрироваться" />
                                                    
                                                </td>
                                            </tr>
                                        </table>                                        
                                    </form>
                                </td>
                            </tr>
                        </table>
                        
                        <!--                        
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
</table>-->
<?php include("application/views/footer.php") ?>