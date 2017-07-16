<?php
$full_price = 0;
$my_orders = array();
if (isset($sort) && !empty($sort) && $sort !== 'all') {
    if ($sort === 'incart') {
        for($i = 0; $i < count($my_cart_and_orders); $i++){
        	if(!isset($my_cart_and_orders[$i]['status'])) {
        	   $my_orders[] = $my_cart_and_orders[$i];
        	}
        }        
    } else {
        for($i = 0; $i < count($my_cart_and_orders); $i++){
        	if(isset($my_cart_and_orders[$i]['status']) && $my_cart_and_orders[$i]['status'] == $sort) {
        	   $my_orders[] = $my_cart_and_orders[$i];
        	}
        }
    }    
} else 
    $my_orders = $my_cart_and_orders;
    //vd($my_orders);
?>

<script type="text/javascript">
var j = jQuery.noConflict();
j(document).ready(function() {
    j('select[name=sort]').on('change', function() {
		alert("asd");
        j('#form_sort').submit();
    });
});
</script>

<div class="all-order">
	<h2>Заказы пользователя<span><?= $user['name'] ?></span></h2>
    <form id="form_sort" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
        <span>Сортировать</span>
        <select name="sort">
            <option value="all" <?=(isset($sort) && $sort == 'all')? 'selected': ''?> > Все</option>
            <option value="new" <?=(isset($sort) && $sort == 'new')? 'selected': ''?> >Новый</option>
            <option value="payed" <?=(isset($sort) && $sort == 'payed')? 'selected': ''?> >Оплачено</option>
            <option value="sended" <?=(isset($sort) && $sort == 'sended')? 'selected': ''?> >Отпроавлено</option>
            <option value="done" <?=(isset($sort) && $sort == 'done')? 'selected': ''?> >Готово</option>            
        </select>
		<input type="submit" value="Сортировать" />  
    </form>
</div>
<?php
if( !isset($my_orders) || empty($my_orders) ){ ?>
<center>Список пуст</center>
<?php
} else {?>
<table class="user-all-order">
    <tr>
		<th style="width: 60px"></th>
        <th style="width: 180px">Название</th>
        <th style="width: 70px">Размер</th>
        <th style="width: 110px">Номер заказа</th>
        <th style="width: 110px">Цена товара</th>
        <th style="width: 110px">Сумма</th>
        <th style="width: 130px">Статус</th>
    </tr>
    <?php
    for ($i = 0; $i < count($my_orders); $i++) {
        $mc = $my_orders[$i];
		//var_dump($mc);
        $shop = $this->model_shop->getArticleById($mc['shop_id']);
        $cat = $this->model_categories->getCategoryById($shop['category_id']);
        //$shop['name'] = unserialize($shop['name']);
        ?>
        <tr>
            <td>
                <img src="<?= CreateThumb2(120, 80, (isset($shop['image']) && !empty($shop['image']))? $shop['image'] : '/img/net_foto.png', 'cart'); ?>"/>
            </td>
            <td class="cart_name_row" >
                <a href="/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                   <?= $shop['name'] ?> x <?= $mc['kolvo'] ?> 
                </a>
            </td>
            <td>
                <?php if(isset($mc['razmer'])) echo $mc['razmer']; ?>
            </td>
        
            <td>
                <?= (isset($mc['order_id']))? $mc['order_id'] : 'none' ?>

            </td>
            <td class="cart_price_row">                                        
                <?= get_price($shop['price']) ?> <?= $currency ?>
            </td>
            <td class="cart_price_row">
                <span id="summ_<?= $i ?>">
                    <?php
                    $product_full_price = get_price($shop['price']) * $mc['kolvo'];
                    echo $product_full_price . '&nbsp;';
                    $full_price += $product_full_price;
                    ?>
                </span>
                <?= $currency ?>
            </td>
            <td>
                <span><?php
                if (isset($mc['status'])) {
                    switch ($mc['status']) {
                        case 'new': echo "Новый"; break;
                        case 'payed': echo "Оплачено"; break;
                        case 'sended': echo "Отправлен"; break;
                        case 'done': echo "Готово"; break;
                        default: echo 'err'; break;
                    }
                } 
                ?> </span> 
            </td>
        </tr>
        <?php
    }
    ?>

</table>
<?php 
}?>