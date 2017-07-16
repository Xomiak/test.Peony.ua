<script>
    $(function () {
        function log(message) {
            $("<div>").text(message).prependTo("#log");
            $("#log").scrollTop(0);
        }


    });


</script>

<!--    ===== LOADER =====-->
<div class="loader" style="display: none">

    <div class="cssload-thecube">
        <h1>Жди</h1>
        <div class="cssload-cube cssload-c1"></div>
        <div class="cssload-cube cssload-c2"></div>
        <div class="cssload-cube cssload-c4"></div>
        <div class="cssload-cube cssload-c3"></div>
    </div>
</div>
<!--    ===== #/LOADER =====-->

<?php
if($order['domain'] == 'prom.ua' && $order['prom_id'] != 0){
    echo '<h3>Заказ на Проме: <a target="_blank" rel="nofollow" href="https://my.prom.ua/cabinet/order/edit/'.$order['prom_id'].'">перейти к заказу</a></h3>';
}
?>
<div style="" id="order_products">
    <?php
    $model = getModel('shop');
    $statuses = $model->getStatuses(1);
    //var_dump($statuses);
    ?>
    <h3>Статус заказа:</h3>
    <select name="status" id="order_status">
        <?php
        if ($statuses) {
            foreach ($statuses as $status) {
                echo '<option value="' . $status['status'] . '"';
                if ($status['status'] == $order['status']) echo ' selected';
                echo '>' . $status['name'] . '</option>';
            }
        }
        ?>
    </select>
    <?php
    //$my_cart = unserialize($page['products']);
    $data = $order['products'];
    $my_cart = $data;
    //var_dump($my_cart);
    $my_cart = unserialize($my_cart);
    //vd($my_cart);
    //var_dump($my_cart);
    $pcount = count($my_cart);
    loadHelper('admin');
    showOrderProducts($order['id'], true);

    //vd($my_cart);
    ?>
</div>

<div class="order-add-product" id="adding_product">
    <div style="position: absolute; right:4px;top:0;font-weight: bolder; cursor: pointer;"><span onclick="show_adding()" title="Показать" id="adding_product_show">+</span>
    </div>
    <div id="adding_form">
        <div style="position: absolute; right:15px;top:0;font-weight: bolder; cursor: pointer;"><span
                    onclick="hide_adding()" title="Спрятать"
                    id="adding_product_hide">&mdash;</span></div>
        <h3>Добавление товара в заказ</h3>


        <?php
        $inpClient = '';
        if (isset($user) && $user != false) {
            $inpClient = '[' . $user['id'] . '] ' . $user['lastname'] . ' ' . $user['name'];
        }
        ?>
        <img id="adding_product_image" src="/images/transparent_1x1.png"
             class="ui-state-default"
             alt=""><br/>
        <input id="inp_adding_product" placeholder="ID, либо название товара"
               type="text" name="adding_product" value="" size="50"><br/>


        <div id="selected-product">
            <select id="add_product_size" placeholder="Размер">
                <option value="0">- размер -</option>
                <?php
                $sizes = $this->shop->getAllSizes();
                foreach ($sizes as $size) {
                    echo '<option class="size_val" value="' . $size['name'] . '">' . $size['name'] . '</option>';
                }
                ?>
            </select>
            <img id="selected-product-image" style="max-height: 100px; float: left" src="">
            <input placeholder="Кол-во" type="number" value="1" id="add_product_count"/><br>
            <input type="hidden" id="add_product_id" value="">
            <input type="hidden" id="product_sizes" value="">
            <button id="add_product_to_order" onclick="return false">Добавить в заказ</button>
            <div style="clear: both"></div>
        </div>


    </div>
</div>


<div><b>Ссылка на оплату через LiqPay:</b> <a
            href="http://<?= $_SERVER['SERVER_NAME'] ?>/payment/liqpay/<?= $order['id'] ?>/"
            target="_blank">http://<?= $_SERVER['SERVER_NAME'] ?>
        /payment/liqpay/<?= $order['id'] ?>/</a><br/>
<b>Ссылка на оплату через Интеркассу:</b> <a
            href="http://<?= $_SERVER['SERVER_NAME'] ?>/payed/interkassa/<?= $order['id'] ?>/"
            target="_blank">http://<?= $_SERVER['SERVER_NAME'] ?>/payed/interkassa/<?= $order['id'] ?>/</a><br/>

</div>

<div style="float: right">
    <b>Результат аякс запроса:</b>
    <div id="result">Нет результатов</div>
</div>


<?php
//var_dump($order['currencies']);
$currencies = json_decode($order['currencies'], true);
//                        var_dump($currencies);
//                        if(!$currencies){
//                            echo "NO";
//                        }
?>
<div><b>Курс на момент оформления:</b><br>
    UAH: <?= $currencies['UAH'] ?><br/>
    RUB: <?= $currencies['RUB'] ?>
</div>


