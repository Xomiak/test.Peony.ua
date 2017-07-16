<?php
$addrStr = '';
if ($addr['country'] != '')
    $addrStr .= $addr['country'] . ', ';
if ($addr['city'] != '')
    $addrStr .= $addr['city'] . ', ';
if ($addr['tel'] != '')
    $addrStr .= $addr['tel'];

?>
<script>
    var userdataChanged = false;
</script>
<h3>Адрес получателя:</h3>
<div id="delivery-div">
    <?php
    if ($order['addr_id'] != 0)
        $addr = getAddrById($order['addr_id']);
    else $addr = getDefaultAddrByUserId($order['user_id']);
    if (!$addr) {
        $addr = getAllAddrByUserId($order['user_id']);
        if (isset($addr[0])) $addr = $addr[0];
    }
    if (!$addr) {
        // Добавляем адрес доставки клиента
        $addr = addAddrFromUser($user);
    }
    //vd($addr);
    ?>

    <?php
    $countries = getCountries(1);
   // vd($countries);
    if ($order['delivery'] == 'Нова Пошта')
        $order['delivery'] = 'Новая Почта';

    $allOk = true;
    $err = array();
    if ($addr['name'] == '') {
        $allOk = false;
        $err['name'] = 'Не указано ФИО получателя';
    }
    if ($addr['tel'] == '') {
        $allOk = false;
        $err['tel'] = 'Не указан номер телефона получателя';
    }
    if ($addr['country'] == '') {
        $allOk = false;
        $err['country'] = 'Не указана страна';
    }
    if ($addr['city'] == '') {
        $allOk = false;
        $err = 'Не указан город';
    }

    /** Проверяем обязательные поля в зависимости от метода доставки */
    $delivery = false;
    $fields = array(
        'name',
        'tel',
        'country',
        'city'
    );
    if ($order['delivery'] != '') {
        $delivery = getDeliveryByName($order['delivery']);
        if ($delivery['required_fields']) {
            $requiredFields = explode('|', $delivery['required_fields']);
            if ($requiredFields) {
                foreach ($requiredFields as $field) {
                    if ($addr[$field] == '') {
                        $fields[] = $field;
                        $allOk = false;
                        if ($field == 'np') $err[$field] = 'Не указан номер отделения НП';
                        else
                            $err[$field] = 'Не заполнено поле ' . $field;
                    }
                }
            }
        }
    } else {
        $allOk = false;
        $err['delivery'] = 'Не указан способ доставки';
    }
    //vd($fields);
    if ($allOk) {
        ?>
        <strong><?= $addr['name'] ?></strong>,<br>
        <?= $addr['tel'] ?>,<br>
        <?= $addr['country'] ?>, <?= $addr['city'] ?><br>
        <?php
        if ($order['delivery'] == 'Новая Почта')
            echo 'Новая Почта №' . $addr['np'];

        echo '<div><a id="show_addr_edit_form" style="cursor:pointer; color: blue; font-size:12px;">Редактировать</a></div><br/><br/>';
    }
    ?>
    <input type="hidden" id="order_addr_id" value="<?= $addr['id'] ?>"/>
    <table id="addr_form">
        <tr>
            <td>ФИО получателя:</td>
            <td><input id="addr_name" class="order_delivery_inp" size="50" required type="text" name="deliveryName"
                       value="<?= $addr['name'] ?>"/></td>
            <?php if (isset($err['name'])) echo '<td class="error">' . $err['name'] . '</td>'; ?>
        </tr>
        <tr>
            <td>Телефон:</td>
            <td><input id="addr_tel" class="order_delivery_inp" size="50" required type="tel" name="tel"
                       value="<?= $addr['tel'] ?>"/></td>
            <?php if (isset($err['tel'])) echo '<td class="error">' . $err['tel'] . '</td>'; ?>
        </tr>
        <tr>
            <td>Страна:</td>
            <td>
                <select class="order_delivery_inp" style="width: 321px" name="country" id="addr_country">
                <?php

                if ($countries) {
                    foreach ($countries as $country) {
                        echo '<option value="' . $country['name'] . '"';
                        if ($order['country'] == $country['name'])
                            echo ' selected';
                        echo '>' . $country['name'] . '</option>';
                    }
                }
                ?>
                </select>
                <!--input id="addr_country" class="order_delivery_inp" size="50" required type="text" name="country"
                       value="<?= $addr['country'] ?>"/-->
                <input id="addr_country_id" type="hidden" name="country_id" value="<?= $addr['country_id'] ?>"/>
            </td>
            <?php if (isset($err['country'])) echo '<td class="error">' . $err['country'] . '</td>'; ?>
        </tr>
        <tr>
            <td>Город:</td>
            <td>
                <input id="addr_city" class="order_delivery_inp" size="50" required type="text" name="city"
                       value="<?= $addr['city'] ?>"/>
                <input id="addr_city_id" type="hidden" name="city_id" value="<?= $addr['city_id'] ?>"/>
            </td>
            <?php if (isset($err['city'])) echo '<td class="error">' . $err['city'] . '</td>'; ?>
        </tr>
        <tr>
            <td>Способ доставки:</td>
            <td>
                <select required class="order_delivery_inp" style="width: 321px" name="delivery" id="sel_delivery">
                    <option></option>
                    <?php
                    $deliveries = getDeliveries(1);
                    if ($deliveries) {
                        foreach ($deliveries as $delivery) {
                            echo '<option value="' . $delivery['name'] . '"';
                            if ($order['delivery'] == $delivery['name'])
                                echo ' selected';
                            if ($addr['country'] != '' && $addr['country_id'] != $delivery['country_id'])
                                echo ' style="display:none;"';
                            echo ' country="' . $delivery['country'] . '">' . $delivery['name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </td>
            <?php if (isset($err['delivery'])) echo '<td class="error">' . $err['delivery'] . '</td>'; ?>
        </tr>
        <tr<?php if (!in_array('adress', $fields)) echo ' style="display:none"' ?> class="beHidden">
            <td>Адрес:</td>
            <td><input id="addr_adress" class="order_delivery_inp" size="50" type="text" name="np"
                       value="<?= $addr['adress'] ?>"/></td>
            <?php if (isset($err['adress'])) echo '<td class="error">' . $err['adress'] . '</td>'; ?>
        </tr>
        <tr<?php if (!in_array('np', $fields)) echo ' style="display:none"' ?> class="beHidden">
            <td>Отделение НП:</td>
            <td><input id="addr_np" class="order_delivery_inp" size="50" type="text" name="np"
                       value="<?= $addr['np'] ?>"/></td>
            <?php if (isset($err['np'])) echo '<td class="error">' . $err['np'] . '</td>'; ?>
        </tr>

        <tr<?php if (!in_array('zip', $fields)) echo ' style="display:none"' ?> class="beHidden">
            <td>Индекс:</td>
            <td><input id="addr_zip" class="order_delivery_inp" size="50" type="text" name="zip"
                       value="<?= $addr['zip'] ?>"/></td>
            <?php if (isset($err['zip'])) echo '<td class="error">' . $err['zip'] . '</td>'; ?>
        </tr>

        <tr<?php if (!in_array('passport', $fields)) echo ' style="display:none"' ?> class="beHidden">
            <td>Паспорт:</td>
            <td><input id="addr_passport" class="order_delivery_inp" size="50" type="text" name="passport"
                       value="<?= $addr['passport'] ?>"/></td>
            <?php if (isset($err['passport'])) echo '<td class="error">' . $err['passport'] . '</td>'; ?>
        </tr>

        <tr>
            <td>Способ оплаты:</td>
            <td>
                <?php
                $paymentsArr = array(
                    'Перевод на карту Приват Банка',
                    'Банковской картой любого банка (LiqPay)',
                    'Интеркасса',
                    'Международный денежный перевод'
                )
                ?>
                <SELECT class="order_delivery_inp" style="width: 321px" name="payment" id="sel_payment">
                    <option>Не указан</option>
                    <?php
                    foreach ($paymentsArr as $value) {
                        echo '<option value="' . $value . '"';
                        if ($order['payment'] == $value) echo ' selected';
                        echo '>' . $value . '</option>';
                    }
                    ?>
                </SELECT>
            </td>
            <?php if (isset($err['payment'])) echo '<td class="error">' . $err['payment'] . '</td>'; ?>
        </tr>

        <tr>
            <td>ТТН:</td>
            <td><input id="addr_ttn" class="order_delivery_inp" type="text" size="50" name="ttn" value=""/>&nbsp;<span
                        id="sms_message"
                        style="color: red"></span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button class="save_order_button" id="delivery_button" disabled="disabled" onclick="return false">
                    Сохранить
                </button>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr/>
            </td>
        </tr>
    </table>

    <script>

        $(document).ready(function () {
            $("#delivery_button").hide();

//            Если все поля заполнены, то прячем таблицу
            <?php if($allOk) { ?>
            $("#addr_form").hide();

            <?php } ?>

            $("#show_addr_edit_form").click(function () {
                $("#addr_form").show();
                $('#li_delivery').animate({
                    height: $("#li_delivery").height() + 250
                });
            });
        });
        //    Отлавливаем, были ли изменения в форме
        $(".order_delivery_inp").keyup(function () {
            deliveryChanged = true;
            showSaveDeliveryButton();
        });
        $(".order_delivery_inp").change(function () {
            deliveryChanged = true;
            showSaveDeliveryButton();
        });

        function showSaveDeliveryButton() {
            if ($("#delivery_button").attr('disabled') == 'disabled') {
                $("#delivery_button").removeAttr('disabled');
                $("#delivery_button").fadeIn('slow');
                $('#li_delivery').animate({
                    height: $("#li_delivery").height() + 45
                });
            }
        }

        $(document).ready(function () {
            // SAVE BUTTON CLICK
            $("#delivery_button").click(function () {
                saveDeliveryChanges();
                deliveryChanged = false;
                $("#delivery_button").attr('disabled', 'disabled');
                $("#delivery_button").fadeOut('slow');
            });

            // DELIVERY CHANGED
            $("#sel_delivery").change(function () {
                var delivery = $(this).val();
                $(".beHidden").fadeIn('slow');
            });

            // COUNTRY CHANGE
            $("#addr_country").change(function () {
                $( "#sel_delivery :selected" ).remove();
                $( "#sel_delivery option" ).each(function( index ) {
                    var country = $("#addr_country").val();
                    if($(this).attr('country') == country)
                        $(this).show();
                    else
                        $(this).hide();
                });
            });
        });
    </script>
</div>