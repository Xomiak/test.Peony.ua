<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function getArticleImages($images)
{
    $html = "";
    if ($images) {
        $count = count($images);
        for ($i = 0; $i < $count; $i++) {
            $image = $images[$i];
            $html .= '
            <div class="article-image" style="float: left;width: 300px;margin-left: 10px">
                <div class="article-img" style="width: 300px;height:300px">
                    <img src="' . $image['image'] . '" style="max-width: 300px"/>
                </div>
                <input class="active-input" type="text" value="http://' . $_SERVER['SERVER_NAME'] . $image['image'] . '" style="width: 300px" /><br />
                <input class="image_show_in_buttom" type="checkbox" image_id="' . $image['id'] . '" name="show_in_bottom"';

            if ($image['show_in_bottom'] == 1) $html .= ' checked';
            $html .= '/>Показать под статьёй<br />
                <input class="image_active" type="checkbox" image_id="' . $image['id'] . '" name="active"';

            if ($image['active'] == 1) $html .= ' checked';
            $html .= '/> Активный<br />
            </div>';
        }
        return $html;
    }
}

function showArticleImages($article_id)
{
    $CI = &get_instance();
    $CI->load->model('Model_images', 'images');
    $images = $CI->images->getByArticleId($article_id);
    if ($images) {
        $count = count($images);
        for ($i = 0; $i < $count; $i++) {
            $image = $images[$i];
            ?>
            <div class="article-image" style="float: left;width: 300px;margin-left: 10px">
                <div class="article-img" style="width: 300px;height:300px">
                    <img src="<?= $image['image'] ?>" style="max-width: 300px"/>
                </div>
                <input class="active-input" type="text"
                       value="http://<?= $_SERVER['SERVER_NAME'] ?><?= $image['image'] ?>" style="width: 300px"/><br/>
                <input class="image_show_in_buttom" type="checkbox" image_id="<?= $image['id'] ?>"
                       name="show_in_bottom"<?php if ($image['show_in_bottom'] == 1) echo ' checked'; ?> />Показать под
                статьёй<br/>
                <input class="image_active" type="checkbox" image_id="<?= $image['id'] ?>"
                       name="active"<?php if ($image['active'] == 1) echo ' checked'; ?> /> Активный<br/>
            </div>
            <?php
        }
    }
}

function showOrderProducts($order_id, $getScripts = true)
{
    $CI = &get_instance();
    $model = getModel('shop');
    $order = $model->getOrderById($order_id);
    //vd($order);
    //$data = $order['products'];
    if ($order['products_json'] != NULL)
        $my_cart = json_decode($order['products_json'], true);
    else $my_cart = unserialize($order['products']);

    $pcount = count($my_cart);
//    if($getScripts)
//        include("application/views/admin/orders/___order_js.php");
    ?>
    <!--<link rel="stylesheet" type="text/css" href="/css/style.min.css">-->
    <script>
        // РЕДАКТИРОВАНИЕ КОЛ-ВА РАЗМЕРА (ТОВАРА)
        $(".cart_numb").change(function () {
            setCount = true;
            showLoader();

            //alert('change!');
            var order_id = "<?=$order['id']?>";
            var shop_id = $(this).attr('shop_id');
            var size = $(this).attr('product_size');
            var count = $(this).val();

            orderProductCountSet(shop_id, size, count);
            hideLoader();
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
    </script>
    <div class="responsive-table">
        <h2>Товары:</h2>
        <table style="width: 100%">

            <tr>
                <th class="in_order_table" style="width: 100px"></th>
                <th style="">Наименование товара</th>
                <th style="width: 200px">Размер: Количество</th>


                <th style="">Цена</th>
                <th style="width: 240px">Сумма</th>
            </tr>

            <?php

            $full_price = 0;
            $full_price2 = 0;
            $full_price3 = 0;

            $notSalePrice = 0;

            $currency = $order['currency'];

            $currensy_grn = getCurrencyValue('UAH');
            $currensy_rub = getCurrencyValue('RUB');

            $countProducts = count($my_cart);
            //vd($my_cart);
            for ($i = 0; $i < $countProducts; $i++) {
                $mc = $my_cart[$i];
                $count = shop_count($my_cart);
                $shop = $model->getArticleById($mc['shop_id']);
                $cat = $CI->model_categories->getCategoryById($shop['category_id']);
                $razmer = explode('*', $shop['razmer']);
                $rcount = count($razmer);
                $kolvo = 0;
                for ($i2 = 0; $i2 < $rcount; $i2++) {
                    if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != 0) {
                        $kolvo += $mc['kolvo_' . $razmer[$i2]];
                    }
                }
                if ($kolvo != 0) {
                    ?>
                    <tr class="edit-order-tr" style="border-top: 4px double black; padding-bottom: 10px;">
                        <td valign="top" class="b-img">
                            <a href="/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                                <img height="75" src="//peony.ua<?= $shop['image'] ?>"/>
                            </a>
                        </td>
                        <td valign="top">
                            <a href="/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                                <?= $shop['name'] ?> (<?= $shop['color'] ?>)

                            </a>
                            <?php
                            if ($shop['discount'] > 0) echo '<span style="color:red">Sale!</span>';
                            ?>
                        </td>

                        <td valign="top" style="width: 225px">
                            <?php

                            $kolvo = 0;
                            $prsizes = 0;
                            for ($i2 = 0; $i2 < $rcount; $i2++) {
                                if (isset($mc['kolvo_' . $razmer[$i2]]) && $mc['kolvo_' . $razmer[$i2]] != 0) {
                                    $kolvo = $kolvo + $mc['kolvo_' . $razmer[$i2]];
                                    $prsizes++;
                                    ?>
                                    <p>
                                        <span>Размер <?= $razmer[$i2] ?>: <input style="width: 60px" class="cart_numb"
                                                                                 shop_id="<?= $shop['id'] ?>"
                                                                                 product_size="<?= $razmer[$i2] ?>"
                                                                                 id="kolvo_<?= $razmer[$i2] ?>_<?= $mc['shop_id'] ?>"
                                                                                 type="number"
                                                                                 name="kolvo_<?= $razmer[$i2] ?>_<?= $mc['shop_id'] ?>"
                                                                                 value="<?= $mc['kolvo_' . $razmer[$i2]] ?>"/></span>
                                        <?= '<a style="float:right" onclick = "if(confirm(\'Вы действительно хотите удалить?\')) delFromOrder(' . $order['id'] . ', ' . $mc['shop_id'] . ', ' . $razmer[$i2] . ');" class="_del_from_order" order_id="' . $order['id'] . '" shop_id="' . $mc['shop_id'] . '" size="' . $razmer[$i2] . '" title="Удалить из заказа"><IMG SRC="/img/del.png" /></a>'; ?>
                                    </p>

                                    <?php
                                } else {
                                    ?>
                                    <p style="display: none">
                                        <span>Размер <?= $razmer[$i2] ?>: <input style="width: 60px" class="cart_numb"
                                                                                 shop_id="<?= $shop['id'] ?>"
                                                                                 product_size="<?= $razmer[$i2] ?>"
                                                                                 id="kolvo_<?= $i2 ?>" type="num"
                                                                                 name="kolvo_<?= $razmer[$i2] ?>_<?= $mc['shop_id'] ?>"
                                                                                 value="0"/>&nbsp;шт.</span>
                                        <?= '<a style="float:right" onclick = "if(confirm(\'Вы действительно хотите удалить?\')) delFromOrder(' . $order['id'] . ', ' . $mc['shop_id'] . ', ' . $razmer[$i2] . ');" class="_del_from_order" order_id="' . $order['id'] . '" shop_id="' . $mc['shop_id'] . '" size="' . $razmer[$i2] . '" title="Удалить из заказа"><IMG SRC="/img/del.png" /></a>'; ?>
                                    </p>
                                    <?php
                                }
                            }
                            ?>

                        </td>


                        <td valign="top">
                            <?php $usdPrice = $res = getNewPrice($shop['price'], $shop['discount']);

                            if ($order['domain'] == 'prom.ua') {
                                if (!isset($mc['price'])) {
                                    $prom_price_adding = getOption('prom_price_adding');
                                    $mc['price'] = ($res + $prom_price_adding) * $currensy_grn;
                                }
                                $res = $mc['price'] / $currensy_grn;
                            }
                            $res = round($res, 2);
                            //$full_price = $full_price + $res;
                            echo '<i class="curval price-usd"' . ($currency != 'usd' ? ' style="display: none"' : '') . '>' . $res ?>
                            &nbsp;<?= ' $</i>' ?>
                            <?php

                            $price = $res * $currensy_grn;
                            echo '<i class="curval price-uah"' . ($currency != 'uah' ? ' style="display: none"' : '') . '>' . round($price, 2) . ' грн</i>';
                            $price = $res * $currensy_rub;
                            echo '<i class="curval price-rub"' . ($currency != 'rub' ? ' style="display: none"' : '') . '>' . round($price, 2) . ' р</i>';
                            ?>
                            <br>(<?=$usdPrice?>$)
                        </td>
                        <td valign="top">
                            <div id="summ_<?= $i ?>">
                                <?php $res = round($res, 2) * $kolvo;
                                $res = round($res, 2);
                                $full_price = $full_price + $res;
                                $full_price = round($full_price, 2);
                                if ($shop['discount'] == 0) {
                                    $notSalePrice = round($notSalePrice + $res, 2);
                                }

                                echo '<i class="curval price-usd"' . ($currency != 'usd' ? ' style="display: none"' : '') . '>' . $res . '$</i>' ?>
                                <?php
                                //$currensy_grn = $CI->model_options->getOption('usd_to_uah');
                                $price = $res * $currensy_grn;
                                echo '<i class="curval price-uah"' . ($currency != 'uah' ? ' style="display: none"' : '') . '>' . round($price, 2) . '&nbsp;грн</i>';
                                //$currensy_rub = $CI->model_options->getOption('usd_to_rur');
                                $price = $res * $currensy_rub;
                                echo '<i class="curval price-rub"' . ($currency != 'rub' ? ' style="display: none"' : '') . '>' . round($price, 2) . '&nbsp;р</i>';
                                ?>
                                <br>(<?=$usdPrice * $kolvo?>$)
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php
            $deliveryPrice = 0;
            if ($order['country_id'] == 0 && $order['country'] != '') {
                $country = getCountryByName($order['country']);
                if ($country) {
                    $dbins = array('country_id' => $country['id']);
                    if ($country['delivery_price'] > 0) {
                        if ($order['products_count'] >= $country['bigopt_from'] && $country['bigopt_delivery_price'] > 0)
                            $deliveryPrice = $order['products_count'] * $country['bigopt_delivery_price'];
                        else $deliveryPrice = $order['products_count'] * $country['delivery_price'];

                        if ($deliveryPrice > 0) $dbins['delivery_price'] = $order['delivery_price'] = $deliveryPrice;
                    }

                    updateItem($order['id'], 'orders', $dbins);
                }
            }

            if ($order['delivery_price'] > 0) {
                $deliveryPrice = $order['delivery_price'];
                $deliveryPriceFull = $deliveryPrice * $count;
                ?>

                <tr>
                    <td colspan="6" align="center"></td>
                    <td align="right" style="padding-right: 15px">Доставка в страну получателя:</td>
                    <td>
                        <?php
                        echo '<i class="curval price-usd"' . ($currency != 'usd' ? ' style="display: none"' : '') . '>' . $deliveryPriceFull . '$</i>';
                        echo '<i class="curval price-uah"' . ($currency != 'uah' ? ' style="display: none"' : '') . '>' . round($deliveryPriceFull * $currensy_grn, 2)  . '&nbsp;грн</i>';
                        echo '<i class="curval price-rub"' . ($currency != 'rub' ? ' style="display: none"' : '') . '>' . round($deliveryPriceFull * $currensy_rub, 2)  . '&nbsp;р</i>';
                        ?>
                        (<?=$deliveryPriceFull.'$'?>)
                    </td>
                </tr>
                <?php
            }
            if ($order['nadbavka'] > 0) {
                ?>
                <tr>
                    <td colspan="6" align="center"></td>
                    <td align="right" style="padding-right: 15px">Розничная надбавка:</td>
                    <td>
                        <?php
                        echo '<i class="curval price-usd"' . ($currency != 'usd' ? ' style="display: none"' : '') . '>' . $order['nadbavka'] . ' $</i>';
                        echo '<i class="curval price-uah"' . ($currency != 'uah' ? ' style="display: none"' : '') . '>' . round($order['nadbavka'] * $currensy_grn, 2) . ' грн</i>';
                        echo '<i class="curval price-rub"' . ($currency != 'rub' ? ' style="display: none"' : '') . '>' . round($order['nadbavka'] * $currensy_rub, 2) . ' р</i>';
                        ?>
                    </td>
                </tr>
                <?php
            }

            if ($currency != 'usd') { ?>
                <tr>
                    <td colspan="6" align="center"></td>
                    <td align="right" style="padding-right: 15px"><b>Всего:</b></td>
                    <td><b>
                        <span id="full_summa_in_currency">
                        <?php
                        if ($order['domain'] == 'prom.ua') {
                            $full_price = $order['full_summa'];
                        }
                        //vd($full_price);
                        if ($deliveryPrice > 0)
                            $full_price = $full_price + $deliveryPrice * $count;
                        //vd($full_price);
                        if ($currency == 'uah') echo (round($full_price * $currensy_grn, 2) + $deliveryPrice) . '&nbsp;грн';
                        elseif ($currency == 'rub') echo (round($full_price * $currensy_rub, 2) + $deliveryPrice) . '&nbsp;р';
                        ?>
                        </span>
                        </b>
                    </td>
                </tr>
                <?php
            }

            ?>
            <tr>
                <td colspan="6" align="center"></td>
                <td align="right" style="padding-right: 15px"><b>Всего в USD:</b></td>

                <td><b>
                        <?php
                        echo '<i id="full_price_usd" class="curval price-usd">' . $full_price . '$</i>';

                        ?>
                    </b>
                </td>
            </tr>
            <?php

            if ($count == 0) {
                ?>
                <tr>
                    <td colspan="5">Ваша корзина пуста...</td>
                </tr>
                <?php
            }
            ?>
        </table>

    </div>
    <?php
}

function setValueInOrder($id, $name, $value)
{
    $CI = &get_instance();
    $CI->db->where('id', $id)->limit(1)->update('orders', array($name => $value));
}

function setValueInUser($id, $name, $value)
{
    $CI = &get_instance();
    $CI->db->where('id', $id)->limit(1)->update('users', array($name => $value));
}

function getAdresses()
{
    $CI = &get_instance();
    return $CI->db->order_by('id', 'DESC')->get('addr')->result_array();
}