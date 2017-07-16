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
                    <div class="top_menu_link"><a href="/admin/coupons/">Купоны</a></div>
                    <div class="top_menu_link"><a href="/admin/coupons/add/">Создать купон</a></div>
                </div>

                <div class="pagination"><?=$pager?></div>

                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
    
                        <th>ID</th>
                        <th>Код</th>
                        <th>Скидка</th>
                        <th>Период действия</th>
                        <th>Индивидуальный</th>
                        <th>Инфо</th>
                        <th>Создан</th>
                        <th>Использован</th>
                        <th>Кем</th>
                        <th>Мульти<th>
                        <th>Действия</th>
    
                    </tr>
                    <?php
                    $count = count($coupons);
                    for($i = 0; $i < $count; $i++)
                    {
                        $coupon = $coupons[$i];
                        ?>
                        <tr class="list<?php if($coupon['used_date'] != false && $coupon['multi'] == 0) echo " coupon-used"; ?>">
                            <td><?=$coupon['id']?></td>
                            <td><a href="/admin/coupons/edit/<?=$coupon['id']?>/" title="Перейти к редактированию"><?=$coupon['code']?></a></td>
                            <td>
                                <?php
                                echo $coupon['discount'];
                                if($coupon['type'] == 1) echo ' USD';
                                elseif($coupon['type'] == 0) echo '%';
                                ?>
                            </td>
                            <td>
                                <?php
                                if($coupon['start_date'] != false && $coupon['end_date'] != false)
                                {
                                    echo $coupon['start_date']." - ".$coupon['end_date'];
                                }
                                else
                                    echo "нет";
                                ?>
                            </td>
                            <td>
                                <?php
                                if($coupon['user_login'] != false)
                                {
                                    $user = $this->users->getUserByLogin($coupon['user_login']);
                                    if($user)
                                    {
                                        echo '<a href="/admin/users/edit/'.$user['id'].'/" target="_blank">'.$user['name'].'</a>';
                                    }
                                    else
                                        echo "Клиента не существует!";
                                }
                                else
                                    echo "нет";
                                ?>
                            </td>
                            <td>
                                <?php
                                if($coupon['not_sale'] == 1) echo '<b>Не действует на товары из Sale!</b><br />'
                                ?>
                                <?=$coupon['info']?>
                            </td>
                            <td><?=$coupon['created_date']?></td>
                            <td>
                                <?php
                                if($coupon['used_date'] != false) echo $coupon['used_date'];
                                else echo "нет";
                                ?>
                            </td>
                            <td>
                                <?php
                                if($coupon['used_by'] != NULL) {
                                    $user = $this->users->getUserByLogin($coupon['used_by']);
                                    echo '<a href="/admin/users/edit/' . $user['id'] . '/">'.$user['name'].'</a>';
                                    }
                                ?>
                            </td>
                            <td align="center">
                                <input disabled type="checkbox" <?php if($coupon['multi'] == 1) echo " checked"; ?> />
                            </td>
                            <td></td>
                            <td>
                                <a href="/admin/coupons/active/<?=$coupon['id']?>/"><?php
                                if($coupon['active'] == 1)
                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                else
                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                ?></a>
                                <a href="/admin/coupons/edit/<?=$coupon['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/coupons/del/<?=$coupon['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <br />
                <div class="pagination"><?=$pager?></div>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>