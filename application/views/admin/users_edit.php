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
                    <div class="top_menu_link"><a href="/admin/users/">Клиенты</a></div>
                    <div class="top_menu_link"><a href="/admin/users/export/">Экспорт</a></div>
                    <div class="top_menu_link"><a href="/admin/users/types/">Типы клиентов</a></div>
                    <!--div class="top_menu_link"><a href="/admin/users/add/">Добавить пользователя</a></div-->
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="/admin/users/edit/<?=$user['id']?>/" method="post">
                <? //var_dump($user);?>
                    <table>
                        <?php
                        if($user['photo'] != '')
                        {
                            ?>
                                <img style="float: right; padding-right: 50px" src="<?=$user['photo']?>">                          
                            <?php
                        }
                        ?>
                        <tr>
                            <td>Имя:</td>
                            <td><input type="text" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $user['name']?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Фамилия:</td>
                            <td><input type="text" name="lastname" value="<?php if(isset($_POST['lastname'])) echo $_POST['lastname']; else echo $user['lastname']?>" size="50" /></td>
                        </tr>
                        <tr style="display: none">
                            <td>Логин *:</td>
                            <td><input disabled="disabled" type="text" name="login" size="50" value="<?php if(isset($_POST['login'])) echo $_POST['login']; else echo $user['login'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Тип клиента:</td>
                            <td>
                                <SELECT name="user_type_id" required>
                                    <?php
                                    if($userTypes)
                                    {
                                        $ucount = count($userTypes);
                                        for($ui = 0; $ui < $ucount; $ui++)
                                        {
                                            $ut = $userTypes[$ui];
                                            ?>
                                            <option value="<?=$ut['id']?>"<?php if($user['user_type'] == $ut['name']) echo ' selected'; ?>><?=$ut['name']?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                    
                                    
                                </SELECT>
                            </td>
                        </tr>

                        
                        
                        
                        <tr>
                            <td>e-mail:</td>
                            <td><input type="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; else echo $user['email'];?>" size="50" /></td>
                            
                        </tr>

                        <tr>
                            <td>Дата последнего захода:</td>
                            <td><?=$user['last_login_date']?></td>

                        </tr>
                       
                        
                        <tr>
                            <td>Профили:</td>
                            <td>
                                <?php
                                if($user['profile'] != '')
                               {
                                $profiles = explode('|', $user['profile']);
                                if(is_array($profiles))
                                {
                                    foreach ($profiles as $key => $value) {
                                        echo '<a target="_blank" href="'.$value.'">'.$value.'</a><br>';
                                    }
                                }
                               }
                                ?>
                                
                            </td>
                        </tr>
                        <tr>
                            <td>День рождения:</td>
                            <td><input type="text" name="tel" value="<?php if(isset($_POST['bd_date'])) echo $_POST['bd_date']; else echo $user['bd_date'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Телефон:</td>
                            <td><input type="text" name="tel" value="<?php if(isset($_POST['tel'])) echo $_POST['tel']; else echo $user['tel'];?>" size="50" /></td>
                        </tr>
                        
                        <tr>
                            <td>Страна:</td>
                            <td><input type="text" name="country" value="<?php if(isset($_POST['country'])) echo $_POST['country']; else echo $user['country'];?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Город:</td>
                            <td><input type="text" name="city" value="<?php if(isset($_POST['city'])) echo $_POST['city']; else echo $user['city'];?>" size="50" /></td>
                        </tr>
                        
                        <tr>
                            <td>Адрес:</td>
                            <td><textarea name="adress"><?=$user['adress']?></textarea></td>
                        </tr>
                        
                        <tr>
                            <td>Тип аккаунта:</td>
                            <td>
                                <SELECT id="clientoradmin" name="type" required>
                                    <option value="client"<?php if($user['type'] == 'client') echo ' selected'; ?>>Пользователь</option>
                                    <option value="moder"<?php if($user['type'] == 'moder') echo ' selected'; ?>>Модератор</option>
                                    <option value="admin"<?php if($user['type'] == 'admin') echo ' selected'; ?>>Администратор</option>

                                    
                                </SELECT>
                                <span id="admin_info" style="font-size:12px; ">
                                    * Администратор будет иметь полный доступ к админке!!!
                                </span>

                            </td>
                            
                        </tr>

                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<?php if($user['active']==1) echo ' checked'; ?> /> Активный</td>                            
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="mailer"<?php if($user['mailer']==1) echo ' checked'; ?> /> Подписан на рассылку</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Изменить" /></td>
                        </tr>
                    </table>
                </form>
            </div>
            
            <div class="content">
                <h2>Заказы</h2>
                <?php
                //vd($orders);
                if( !isset($orders) || empty($orders) ){ ?>
                <center>Список пуст</center>
                <?php
                } else {?>
                <table class="cart_table">
                    <tr>
                        <th>Номер заказа</th>
                        <th width="15%">Сумма</th>
                        <th width="8%">Статус</th>
                    </tr>
                    <?php
                    for ($i = 0; $i < count($orders); $i++) {
                        $order = $orders[$i];
                        ?>
                        <tr>
                            <td>
                                <a href="/admin/orders/edit2/<?=$order['id']?>/"><?=$order['id']?></a>
                            </td>
                            <td>
                                <?php
                                $fullSumma = $order['full_summa'];
                                if(isset($order['currency']) && $order['currency'] == 'uah')
                                {
                                    $uah = getCurrencyValue('UAH');
                                    $fullSumma = $fullSumma * $uah;
                                }
                                elseif(isset($order['currency']) && $order['currency'] == 'rub')
                                {
                                    $rub = getCurrencyValue('RUB');
                                    $fullSumma = $fullSumma * $rub;
                                }
                                ?>
                                <?=$fullSumma?> <?=$order['currency']?>
                            </td>
                             <td>
                                <?=getStatus($order['status'])?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>o
                </table>
                <?php 
                }?>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>