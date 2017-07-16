<div class="all-order">
    <h2 class="user-h2">Мои клиенты</h2>
    <div class="cart-container">
        <div class="responsive-table">
            <table class="user-all-order">
                <tr>
                    <th>ID</th>
                    <th>Телефон</th>
                    <th>ФИО</th>
                    <th>Страна</th>
                    <th>Город</th>
                    <th>Адрес</th>
                    <th>Заказов</th>
                    <th>Действия</th>
                </tr>
                <?php
                $addressess = $this->users->getAddressesByUser(userdata('login'));
                //vd($addressess);
                if ($addressess) {

                    foreach ($addressess as $adr) {
                        echo '<tr>';
                        ?>
                        <td><?=$adr['id']?></td>
                        <td><?=$adr['tel']?></td>
                        <td><?=$adr['name']?></td>
                        <td><?=$adr['country']?></td>
                        <td><?=$adr['city']?></td>
                        <td>
                            <?php
                            if($adr['np'] != NULL && $adr['np'] != '')
                                echo 'Отделение Новой Почты №'.$adr['np'];
                            else echo $adr['adress'];
                            ?>
                        </td>
                        <td></td>
                        <td>
                            <a href="/user/dropship_client_adress/<?=$adr['id']?>/?action=edit">Редактировать</a><br />
                            <a onclick="return confirm('Вы уверены, что хотите удалить адрес?')" href="/user/dropship_client_adress/<?=$adr['id']?>/?action=delete">Удалить</a>
                        </td>
                        <?php
                        echo '</tr>';
                    }

                } else echo '<tr><td>Вы не добавили ни одного клиента...</td></tr>';
                ?>
            </table>
            <a href="/user/dropship_client_adress/?action=add">Добавить нового клиента</a>
        </div>
    </div>
</div>
