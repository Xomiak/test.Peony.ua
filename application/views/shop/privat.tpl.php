<?php
//if($currency == 'РУБ') $currency = 'rur';
$currency = 'USD';
$privat24_merchant = $this->model_options->getOption('privat24_merchant');
?>
<?php include("application/views/head.php"); ?>

    <h1>Оплата через Приват24!</h1>
    <form action="https://api.privatbank.ua/p24api/ishop" method="POST">
        <input type="hidden" name="amt" value="<?=get_price($order['summa'])?>"/>
        <input type="hidden" name="ccy" value="<?=$currency?>" />
        <input type="hidden" name="merchant" value="<?=$privat24_merchant?>" />
        <input type="hidden" name="order" value="<?=$order['id']?>" />
        <input type="hidden" name="details" value="Оплата товаров" />
        <input type="hidden" name="ext_details" value="" />
        <input type="hidden" name="pay_way" value="privat24" />
        <input type="hidden" name="return_url" value="http://<?=$_SERVER['SERVER_NAME']?>/payed/privat24/<?=$order['id']?>/" />
        <input type="hidden" name="server_url" value="http://<?=$_SERVER['SERVER_NAME']?>/payed/privat24/<?=$order['id']?>/" />
        <input type="submit" value="Перейти к оплате" />
    </form>

<?php include("application/views/footer.php"); ?>