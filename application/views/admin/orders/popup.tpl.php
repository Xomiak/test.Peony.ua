<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Заказ</title>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/accordion.css" />

    <link href='http://fonts.googleapis.com/css?family=Josefin+Slab:400,700' rel='stylesheet' type='text/css' />


    <link rel="stylesheet" href="/js/easyautocomplete/easy-autocomplete.min.css">
    <link rel="stylesheet" href="/js/easyautocomplete/easy-autocomplete.themes.min.css">

    <noscript>
        <style>
            .st-accordion ul li{
                height:auto;
            }
            .st-accordion ul li > a span{
                visibility:hidden;
            }
        </style>
    </noscript>
</head>
<body>
<script>
    var userdataChanged = false;
    var deliveryChanged = false;
    var isChanged = false;
    var udSerialized = false;
</script>
<div class="container">

    <div class="wrapper">
        <div id="st-accordion" class="st-accordion">
            <h2>Заказ №<?=$order['id']?></h2>
            <input type="hidden" id="edited_order_id" value="<?=$order['id']?>" />
            <ul>
                <li id="li_order">
                    <a href="#">1. Клиент <?php if($user['user_type_id'] == 11) echo '<img width="45px" src="/img/admin/dropshipping.png" title="Клиент зарегистрирован, как дропшиппер" /> '; ?><strong><?=$user['name']?> <?=$user['lastname']?></strong> (<?=$user['login']?>)<span class="st-arrow">Открыть или закрыть</span></a>
                    <div class="st-content">
                        <?=$user_details?>
                    </div>
                </li>
                <li id="li_delivery">

                    <a href="#">2. Доставка и оплата<span class="st-arrow">Открыть или закрыть</span></a>
                    <div class="st-content" style="padding-top: -10px">
                        <?=$delivery_adress?>
                    </div>
                </li>
                <li id="li_products">
                    <a href="#">3. Заказ<?php if($order['prom_id'] > 0) echo ' <img class="order-prom-img" src="/img/admin/prom.png" alt="prom" title="Заказ с Прома"/> ';?><span class="st-arrow">Открыть или закрыть</span></a>
                    <div class="st-content">
                        <?=$orders_edit_block?>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.accordion.js"></script>
<script type="text/javascript" src="/js/jquery.easing.1.3.js"></script>

<script src="/js/easyautocomplete/jquery.easy-autocomplete.min.js"></script>
<script type="text/javascript">

    $(function() {

        $('#st-accordion').accordion({
            oneOpenedItem	: false
        });

    });


    function saveUserInfo() {
        showLoader();
        $.ajax({
            async: false,
            url: "/admin/ajax/users/?edit=true",
            data: {
                'user_id': 'add',
                'order_id': <?=$order['id']?>,
                'shop_id': shop_id,
                'count': count,
                'size': size
            },
            success: function (data) {
                $("#result").html(data);
                resetOrdersTable();
                hideLoader();
            }
        });
    }

    function setOrderStatus(order_id, status) {
        showLoader();
        $.ajax({
            async: false,
            url: "/admin/ajax/edit_order/"+order_id+"/",
            data: {
                'action': 'set_status',
                'order_id': <?=$order['id']?>,
                'value': status
            },
            success: function (data) {
                $("#result").html(data);
                hideLoader();
            }
        });
    }

</script>
<script type="text/javascript">
    var loading = false;
    var setCount = false;
    $.ajaxSetup({
        async: false,
        type: 'POST'
    });

    function showLoader(delayTime = 500) {
            if(loading == false) {
                loading = true;
                $(".loader").fadeIn(200).delay(delayTime);
            }
    }

    function hideLoader(delayTime = 500) {
            if(loading == true) {
                $(".loader").fadeOut(200).delay(delayTime);

                loading = false;
            }
    }

    function resetOrdersTable() {
        if (loading == false)
            showLoader();
        //alert('Обновляем таблицу...');

        $.ajax({
            async: false,
            url: "/admin/ajax/get_order_products/<?=$order['id']?>/",
            data: {
                'action': 'resetOrderTable'
            },
            success: function (data) {
                console.log("Обновили!");
                $("#order_products").html(data);
                hideLoader();
            }
        });
        if (loading == true) {
            console.log("loading = true");
            hideLoader();
            loading = false;
        }
    }

    function setActiveSizes(sizes) {
        showLoader();
        setSizesList();
        var result = sizes.split('*');
        $(".size_val").each(function (index) {
            var optText = $(this).val();
            if (optText != '') {
                if ($.inArray(optText, result) == -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
                console.log(index + ": " + $(this).text());
            }
        });
        hideLoader();
        return false;
    }

    function orderProductCountSet(shop_id, size, count) {
        if (setCount == true) {
            $.ajax({
                url: "/admin/ajax/edit_order/<?=$order['id']?>/",
                async: false,
                data: {
                    'action': 'set_count',
                    'order_id': <?=$order['id']?>,
                    'shop_id': shop_id,
                    'count': count,
                    'size': size
                },
                success: function (data) {
                    setCount = false;
                    $("#result").html(data);
                    resetOrdersTable();
                }
            });
            if (loading == true)
                hideLoader();
        }
    }

    function addingProductToOrder(shop_id, size, count) {
        showLoader();
        $.ajax({
            async: false,
            url: "/admin/ajax/edit_order/<?=$order['id']?>/",
            data: {
                'action': 'add',
                'order_id': <?=$order['id']?>,
                'shop_id': shop_id,
                'count': count,
                'size': size
            },
            success: function (data) {
                $("#result").html(data);
                resetOrdersTable();
                hideLoader();
            }
        });
    }

    function delFromOrder(order_id, shop_id, size) {
        showLoader();

        $.ajax({
            async: false,
            url: "/admin/ajax/edit_order/<?=$order['id']?>/",
            data: {
                'action': 'delete',
                'order_id': order_id,
                'shop_id': shop_id,
                'size': size
            },
            success: function (data) {
                $("#result").html(data);
                resetOrdersTable();
                hideLoader();
            }
        });
    }

    var products = [
        <?php
        $modelShop = getModel('shop');
        $all = $modelShop->getArticles(-1, -1);
        if($all){
        foreach ($all as $item){
            $sizesArr = $item['razmer'];
            $name = '[' . $item['id'] . '] ' . $item['name'] . ' (' . $item['color'] . ')';
            $label = $name .= ' Размеры: ' . str_replace('*', ', ', $item['razmer']);
            ?>
            {
                id: "<?=$item['id']?>",
                value: '<?=$name?>',
                label: "<?=$label?>",
                desc: "<?=$sizesArr?>",
                icon: "<?=$item['image']?>"
            },
            <?php
            }
        }
        ?>
    ];





    $(function () {
        function log2(message) {
            $("<div>").text(message).prependTo("#log2");
            $("#log").scrollTop(0);
        }


        $("#inp_adding_product").easyAutocomplete({
            data: products,
            getValue: "value",
            list: {
                match: {
                    enabled: true
                }
            },
            onSelectItemEvent: function() {
                var selectedItemValue = $("#inp_adding_product").getSelectedItemData().value;
                var selectedItemImage = $("#inp_adding_product").getSelectedItemData().image;

                //$("#selected-product-image").src(selectedItemImage);
                //$("#inputTwo").val(selectedItemValue).trigger("change");
            },
            minLength: 2,
            template: {
                type: "custom",
                method: function(value, item) {
                    return "<img style='float: left; max-height: 100px' src='//peony.ua" + item.icon + "' /><b>" + value + "</b><br/>" + item.desc + "<div style='clear:both'></div>";
                }
            }
        });

    });


    <?php
    if ($order['dropship_id'] != 0) echo 'var isDropship = true;';
    else echo 'var isDropship = false;';
    ?>
    $(document).ready(function () {
        $('#is_dropship').change(function () {
            if (isDropship == true) {
                $("#dropship_adress").hide();
                isDropship = false;
            } else {
                $("#dropship_adress").show();
                isDropship = true;
            }
        });
    });
</script>
<script>
    function hide_adding() {
        $("#adding_form").hide();
        $("#adding_product_show").show();
    }
    function show_adding() {
        $("#adding_form").show();
        $("#adding_product_show").hide();
    }
    $(document).ready(function () {
        show_adding();
    });
</script>






<!--FROM order_js.php-->
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

        $("#add_product_id").change(function () {
            alert($("#add_product_id").val());
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


    });


    $(document).ready(function () {

        $("#order_status").change(function () {
            setOrderStatus(<?=$order['id']?>, $("#order_status").val());
        });

        hideLoader();
        $("#add_product_to_order").click(function () {
            showLoader();
            // проверяем, всё ли заполнено для добавления товара в заказ
            var size = $("#add_product_size").val();
            var shop_id = $("#add_product_id").val();
            var count =  $("#add_product_count").val();
            if(shop_id == '' || shop_id == 0){
                alert('Вы не выбрали товар!');
                return false;
            }
            if(size <= 0){
                alert('Вы не выбрали размер!');
                return false;
            }
            if(count <= 0){
                alert('Вы не указали количество!');
                return false;
            }
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
</body>
</html>