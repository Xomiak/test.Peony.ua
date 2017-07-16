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
                    <div class="top_menu_link"><a href="/admin/users/">Пользователи</a></div>
                    <div class="top_menu_link"><a href="/admin/users/add/">Добавить пользователя</a></div>
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="/admin/users/add/" method="post">
                    <table>
                        <tr>
                            <td>Логин *:</td>
                            <td><input required type="text" name="login" size="50" value="<?php if(isset($_POST['login'])) echo $_POST['login'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Тип *:</td>
                            <td>
                                <SELECT required name="type">
                                    <option value="user">Пользователь</option>
                                    <option value="author">Автор</option>
                                    <option value="moder">Модератор</option>
                                    <option value="admin">Админ</option>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Пароль *:</td>
                            <td><input required type="password" name="pass" size="50" value="<?php if(isset($_POST['pass'])) echo $_POST['pass'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Пароль ещё раз *:</td>
                            <td><input required type="password" name="pass2" size="50" value="<?php if(isset($_POST['pass2'])) echo $_POST['pass2'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Имя:</td>
                            <td><input type="text" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>e-mail *:</td>
                            <td><input required type="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Пол:</td>
                            <td>
                                <SELECT name="sex">
                                    <option value="w">женский</option>
                                    <option value="m">мужской</option>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>Город:</td>
                            <td><input type="text" name="city" value="<?php if(isset($_POST['city'])) echo $_POST['city'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Возраст:</td>
                            <td><input type="text" name="age" value="<?php if(isset($_POST['age'])) echo $_POST['age'];?>" size="5" /></td>
                        </tr>
                        <tr>
                            <td>Сайт:</td>
                            <td><input type="text" name="site" value="<?php if(isset($_POST['site'])) echo $_POST['site']; else echo 'http://';?>" size="50" /></td>
                        </tr>
                        
                        <tr>
                            <td>Аватар:</td>
                            <td><input type="file" name="userfile" /></td>
                        </tr>                    
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active" checked /> Активный</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Добавить" /></td>
                        </tr>
                        <tr>
                            <td align="right">
                            </td>
                            <td>
                                <input type="checkbox" name="mailer" checked="checked" /> Получать новости
                            </td>
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