<?php
include("header.php");
?>
<script type="text/javascript">
var j = jQuery.noConflict();
j(document).ready(function() { 
      
   
</script>
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
            <div class="pagination"><?=$pager?></div>
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/users/">Клиенты</a></div>
                    <div class="top_menu_link"><a href="/admin/users/export/">Экспорт</a></div>
                    <div class="top_menu_link"><a href="/admin/users/types/">Типы клиентов</a></div>
                    <!--div class="top_menu_link"><a href="/admin/users/add/">Добавить пользователя</a></div-->
                    <div class="users-filter">
                        <form method="post" style="float: left; margin-right: 10px;">
                            <select name="user_type" onchange="submit()">
                                <option value="">Все</option>
                                <?php
                                if($userTypes)
                                {
                                    $ucount = count($userTypes);
                                    for($ui = 0; $ui < $ucount; $ui++)
                                    {
                                        $ut = $userTypes[$ui];
                                        ?>
                                        <option value="<?=$ut['name']?>"<?php if($filter == $ut['name']) echo ' selected'; ?>><?=$ut['name']?></option>
                                        <?php
                                    }
                                }
                                ?>                                
                            </select>
                        </form>
                        <form method="post" style="float: left; margin-right: 10px;">
                            <input id="users_search" type="text" name="search" placeholder="Поиск клиента" value="<?=$this->input->post('search')?>" />
                            <input type="submit" value="Искать" />
                        </form>
                     </div>
                     
                </div>
                <!-- Количество записей: <?=$usersCount?> -->
                <table width="100%" cellpadding="1" cellspacing="1">
                    <tr bgcolor="#EEEEEE">
                        <th><a href="?sort=id">ID</a></th>
                        <th><a href="?sort=name">Имя, Фамилия</a></th>
                        <th><a href="?sort=user_type">Тип</a></th>
                        <th>Кол-во заказов</th>
                        <th><a href="?sort=country">Страна</a></th>
                        <th><a href="?sort=email">e-mail</a></th>
                        <th><a href="?sort=tel">Тел.</a></th>
                        <th><a href="?sort=domain">Сайт</a></th>
                        <th><a href="?sort=network">Соц.сеть</a></th>
                        <th><a href="?sort=from">Откуда</a></th>
                        <th>Действия</th>
    
                    </tr>
                    <?php
                    $count = count($users);
                    for($i = 0; $i < $count; $i++)
                    {
                        $user = $users[$i];
                        ?>
                        <tr class="list<?php
                        if($user['user_type'] == 'Постоянный') echo " user-type-1"; 
                        elseif($user['user_type'] == 'Оптовый') echo " user-type-2";
                        if($user['user_type'] == 'Покупатель') echo " user-type-3";  
                        elseif($user['user_type'] == 'Конкурент') echo " user-type-fake";
                        ?>">
                            <td><?=$user['id']?></td>
                            
                            <td><a class="tooltip" href="/admin/users/edit/<?=$user['id']?>/" title="Перейти к редактированию"><?=$user['name']?>, <?=$user['lastname']?><?php if($user['photo'] != '') echo '<span><img width="125px" src="'.$user['photo'].'"></span>'; ?></a></td>


                            <td><?=$user['user_type']?></td>
                            <td align="center">
                                <?
                                $ooo = $this->shop->getOrdersByUserId($user['id']);
                                if($ooo)
                                    $ccc = count($ooo);
                                else $ccc = 0;
                                echo $ccc;

                                if($ccc > 0 && $user['user_type_id'] == 1){
                                    $dbins = array(
                                        'user_type_id' => 2,
                                        'user_type'     => 'Покупатель'
                                    );
                                    $this->db->where('id', $user['id']);
                                    $this->db->limit(1);
                                    $this->db->update('users', $dbins);
                                }
                                ?>
                            </td>
                            <td><?=$user['country']?></td>
                            <td><?=$user['email']?></td>
                            <td><?=$user['tel']?></td>
                            <td><?=$user['domain']?></td>
                            <td>
                                <?php
                                if($user['profile'] != '') echo '<a target="_blank" href="'.$user['profile'].'">'.$user['network'].'</a>';
                                ?>
                            </td>
                            <td><?=$user['from']?></td>
                            
                            <td>
                                <a href="/admin/users/active/<?=$user['id']?>/"><?php
                                if($user['active'] == 1)
                                    echo '<img src="/img/visible.png" width="16px" height="16px" border="0" title="Деактивация" />';
                                else
                                    echo '<img src="/img/not-visible.png" width="16px" height="16px" border="0" title="Активация" />';
                                ?></a>
                                <a href="/admin/users/edit/<?=$user['id']?>/"><img src="/img/edit.png" width="16px" height="16px" border="0" title="Редактировать" /></a>
                                <a onclick="return confirm('Удалить?')" href="/admin/users/del/<?=$user['id']?>/"><img src="/img/del.png" border="0" alt="Удалить" title="Удалить" /></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <div class="pagination"><?=$pager?></div>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>