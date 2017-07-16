<script src="/libs/fancybox/jquery.fancybox.min.js"></script>
<script type="text/javascript">

    var message = '';
    var isNeedSaving = false;

    $("[data-fancybox]").fancybox({
        // Options will go here
        afterClose: function () {
            if(message != '') {
                showAdminMessage(message);
                message = '';
            }
        },

        beforeClose: function () {
            isUserDataChanged();
            if(isChanged){
                if(confirm("Вы хотите сохранить изменения?")){
                    if(userdataChanged){
                        // сохраняем изменения о клиенте
                        saveUserdataChanges();
                    }
                }
            }
        }


    });


    function saveUserdataChanges() {
        console.log('сохранение изменений о клиенте');
        $.ajax({
            url: '/admin/ajax/users/?edit=true',
            method: 'POST',
            data: {
                "action": 'edit',
                'user_id': $("#user_id").val(),
                'name': $("#user_name").val(),
                'lastname': $("#user_lastname").val(),
                'user_type_id': $("#user_type_id").val(),
                'email': $("#user_email").val(),
                'tel': $("#user_tel").val(),
                'country': $("#user_country").val(),
                'city': $("#user_city").val()
            },

        }).done(function (data) {
            if(message != '') message = message + '<br>';
            message = message + data;
            userdataChanged = false;
        });

        // !!!!!!!!!!!!!!!!!!!!

    }

    function saveDeliveryChanges() {
        console.log('сохранение изменений о доставке');
        var order_id = $("#edited_order_id").val();
        $.ajax({
            url: '/admin/ajax/edit_order/' + order_id + '/',
            method: 'POST',
            data: {
                "action": 'edit_addr',
                'addr_id': $("#order_addr_id").val(),
                'name': $("#addr_name").val(),
                'tel': $("#addr_tel").val(),
                'country': $("#addr_country").val(),
                'country_id': $("#addr_country_id").val(),
                'city': $("#addr_city").val(),
                'city_id': $("#addr_city_id").val(),
                'delivery': $("#sel_delivery").val(),
                'adress': $("#addr_adress").val(),
                'np': $("#addr_np").val(),
                'payment': $("#sel_payment").val(),
                'ttn': $("#addr_ttn").val()
            },

        }).done(function (data) {
            if(message != '') message = message + '<br>';
            message = message + data;
            userdataChanged = false;
        });

        // !!!!!!!!!!!!!!!!!!!!

    }




    // проверяем, вносились ли изменения в какую-то из форм
    function isUserDataChanged() {
        if(userdataChanged)
            isChanged = true;
        else if(deliveryChanged)
            isChanged = true;
    }
</script>