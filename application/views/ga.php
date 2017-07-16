<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-54499372-1', 'auto');
    ga('send', 'pageview');
    <?php if(userdata('login') !== false) { ?>
    ga('set', 'userId', '<?=userdata('login')?>'); // Задание идентификатора пользователя с помощью параметра user_id (текущий пользователь).
    <?php } ?>

    ga('require', 'ecommerce');
    <?php
        //set_userdata('complete_order', 844);
    if(userdata('complete_order') !== false){
        $order_id = userdata('complete_order');
        unset_userdata('complete_order');
        $order = getOrderById($order_id);
        if($order){
            ?>
            ga('ecommerce:addTransaction', {
                'id': '<?=$order['id']?>',
                'affiliation': 'Peony',
                'revenue': '<?=$order['full_summa']?>',
                'shipping': '<?=$order['delivery']?>',
                'tax': '0',
                'currency': 'USD'
            });
            <?php
            $cart = unserialize($order['products']);
            foreach ($cart as $item){
                $product = $this->model_shop->getArticleById($item['shop_id']);
                if($product) {
                $price = round($product['price'],2);
                if(isDiscount($product)) {
                    $product['price'] = getNewPrice($product['price'], $product['discount']);
                    $price = round($product['price'], 2);
                }

                    $cat = $this->model_categories->getCategoryById($product['category_id']);
            ?>
                    ga('ecommerce:addItem', {
                        'id': '<?=$product['id']?>',                     // Transaction ID. Required.
                        'name': '<?=$product['name']?> (<?=$product['color']?>)',    // Product name. Required.
                        'sku': '<?=$product['articul']?>',                 // SKU/code.
                        'category': '<?=$cat['name']?>',         // Category or variation.
                        'price': '<?=$product['price']?>',                 // Unit price.
                        'quantity': '<?=shop_sizes_count($item)?>'                   // Quantity.
                    });
                    <?php
                    }
            }
        }
         ?>
        ga('ecommerce:send');
    <?php
    }
    ?>

</script>


<!-- Google Code for &#1050;&#1086;&#1085;&#1074;&#1077;&#1088;&#1089;&#1080;&#1080; Conversion Page -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 943237681;
    var google_conversion_language = "ru";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "AGYFCOzTiGAQsdTiwQM";
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt=""
             src="//www.googleadservices.com/pagead/conversion/943237681/?label=AGYFCOzTiGAQsdTiwQM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>