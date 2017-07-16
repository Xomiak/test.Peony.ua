<?php
if(!isset($user)){
   // echo 'Не найден user...<br/>';
    if(!isset($modelUsers))
        $modelUsers = getModel('users');
    $user = $modelUsers->getUserById($order['id']);
}
if(!isset($user)){
    echo '<h1>КАПЕЦ! НЕТ ЮЗЕРА!!!</h1>';
}
?>



<script type="text/javascript">


    $(document).ready(function () {
        // УДАЛЕНИЕ РАЗМЕРА (ТОВАРА) ИЗ ЗАКАЗА
        $(".del_from_order").click(function () {
            showLoader();
            var order_id = $(this).attr('order_id');
            var shop_id = $(this).attr('shop_id');
            var size = $(this).attr('size');
            //alert('Delete: '+shop_id+' size: '+size);

            delFromOrder(order_id, shop_id, size);
            hideLoader();
        });

        // РЕДАКТИРОВАНИЕ КОЛ-ВА РАЗМЕРА (ТОВАРА)
        $(".cart_numb").change(function () {
            setCount = true;
            showLoader();

            //alert('change!');
            var order_id = "<?=$order['id']?>";
            var shop_id = $(this).attr('shop_id');
            var size = $(this).attr('product_size');
            var count = $(this).val();

            orderProductCountSet(shop_id,size,count);
            hideLoader();
        });

        $("#inp_adding_product").change(function () {


        });

    });


function setSizesList() {
    $('#add_product_size option:selected').each(function(){
        this.selected=false;
    });
}

</script>

<script type="text/javascript">
    var activeSizes = '';
    $(function () {
        function log2(message) {
            $("<div>").text(message).prependTo("#log2");
            $("#log").scrollTop(0);
        }

        $("#inp_adding_product").easyAutocomplete({
            source: products,
            minLength: 2,
            select: function (event, ui) {
                $("#add_product_id").val(ui.item.id);
                $("#inp_adding_product").val(ui.item.value);
                $("#adding_product_image").attr("src", ui.item.icon);
                $("#adding_product_image").attr("width", "100px");
                activeSizes = ui.item.desc;
                //alert(activeSizes);
                setActiveSizes(activeSizes);

                return false;
            }
        });
    });



    $(document).ready(function () {
        hideLoader();
        $("#add_product_to_order").click(function () {
            showLoader();
            // проверяем, всё ли заполнено для добавления товара в заказ
            var size = $("#add_product_size").val();
            var shop_id = $("#add_product_id").val();
            var count =  $("#add_product_count").val();
            if(shop_id != '' && size != '' && count > 0){
                addingProductToOrder(shop_id,size,count);

                return false;
            } else alert('Вы что-то забыли выбрать!');

            hideLoader();
            return false;
        });

        $(".cart_numb").change(function () {
            showLoader();
            //alert('change');
            var shop_id = $(this).attr('shop_id');
            var size = $(this).attr('size');
            var count = $(this).val();
            orderProductCountSet(shop_id, size, count);
            //$('#preloader').hide();
        });
    });


</script>


<!--ОППЕРАЦИИ С SMS СЕРВИСОМ TURBOSMS-->
<script type="text/javascript">
    $(document).ready(function () {
        $("#status").change(function () {
            var status = $(this).val();
            var ttn = $("#inp_ttn").val();
            var tel = '<?=$user['tel']?>';
            if (status == 'sended') {
                $("#sendSMS").html('<input type="checkbox" id="chk_sendSMS" name="send_sms" checked /> Отправить ТТН клиенту по SMS на номер: <input id="inp_tel" type="text" size="50" name="tel" value="<?=$user['tel']?>" />&nbsp;<span id="span_tel" style="color: red"></span>');

                if (ttn == '')
                    $("#sms_message").html("Укажите номер ТТН!");
                else $("#sms_message").html("");
                if (tel == '')
                    $("#span_tel").html("Клиент не указал свой тел!");
                else $("#span_tel").html("");

            } else if (status == 'processing') {
                <?php
                $smsMessage = getOption('sms_sumTemplate');
                $orderPrice = $order['full_summa'] * getCurrencyValue($order['currency']);
                $orderPrice .= ' ' . $order['currency'];
                $smsMessage = str_replace('[order_id]', $order['id'], $smsMessage);
                $smsMessage = str_replace('[order_sum]', $orderPrice, $smsMessage);
                ?>
                var sms_message = '<?=$smsMessage?>';
                $("#sendSMS").html('<input type="checkbox" id="chk_sendSMS" name="send_sum_sms" checked /> Отправить клиенту сумму оплаты по SMS на номер: <input id="inp_tel" type="text" size="50" name="tel" value="<?=$user['tel']?>" />&nbsp;<span id="span_tel" style="color: red"></span><br /><input size="50" placeholder="Текст сообщения" type="text" name="sms_mesage" value="' + sms_message + '" />');

                if (tel == '')
                    $("#span_tel").html("Клиент не указал свой тел!");
                else $("#span_tel").html("");

            } else $("#sendSMS").html('');
        });

        $("#inp_ttn").keyup(function () {
            var ttn = $(this).val();
            if (ttn != '')
                $("#sms_message").html('');
            else if ($("#chk_sendSMS").attr("checked") == 'checked')
                $("#sms_message").html("Укажите номер ТТН!");
        });

        $("#inp_tel").keyup(function () {
            var tel = $(this).val();
            if (tel != '')
                $("#span_tel").html('');
            else if ($("#chk_sendSMS").attr("checked") == 'checked')
                $("#span_tel").html("Клиент не указал свой тел!");
        });

    });
</script>