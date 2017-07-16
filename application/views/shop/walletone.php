<?php include("application/views/head_new.php"); ?>

<?php include("application/views/header_new.php"); ?>
    <!--==============================content================================-->

    <section class="container news-list">
        <div class="breadcrumbs">
            <div xmlns:v="http://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>/
                Оплата заказа через WalletOne
            </div>
        </div>

        <h1>Оплата через WalletOne</h1>
        <?php
        //vd($user);
        ?>
        <article class="article-content">
            <?php
            //Секретный ключ інтернет-магазин
            $key = "5a716a5b62673639673034784d6863655c767454424b5577575956";

            $fields = array();

            // Добавление полей формы в ассоциативный массив
            $fields["WMI_MERCHANT_ID"]    = "122853487401";
            $fields["WMI_PAYMENT_AMOUNT"] = $order['full_summa'];
            $fields["WMI_CURRENCY_ID"]    = "840";
            $fields["WMI_PAYMENT_NO"]     = $order['id'];
            $fields["WMI_DESCRIPTION"]    = "BASE64:".base64_encode("Оплата заказа #".$order['id']." в магазине ".strtoupper($_SERVER['SERVER_NAME']));

            $fields["WMI_SUCCESS_URL"]    = "https://".$_SERVER['SERVER_NAME']."/payed/walletone/";
            $fields["WMI_FAIL_URL"]       = "https://".$_SERVER['SERVER_NAME']."/payed/walletone/?failed";
            $fields["WMI_AUTO_LOCATION"]       = 1; // Дополнительные параметры
            $fields["WMI_CULTURE_ID"]       = "ru-RU"; // интернет-магазина тоже участвуют
            if(isset($user) && $user != false){
                $fields['WMI_CUSTOMER_EMAIL'] = $user['email'];
                $fields['WMI_CUSTOMER_FIRSTNAME'] = $user['name'];
                $fields['WMI_CUSTOMER_LASTNAME'] = $user['lastname'];
            }
            //$fields["MyShopParam3"]       = "Value3"; // при формировании подписи!
            //Если требуется задать только определенные способы оплаты, раскоментируйте данную строку и перечислите требуемые способы оплаты.
            //$fields["WMI_PTENABLED"]      = array("UnistreamRUB", "SberbankRUB", "RussianPostRUB");

            //Сортировка значений внутри полей
            foreach($fields as $name => $val)
            {
                if (is_array($val))
                {
                    usort($val, "strcasecmp");
                    $fields[$name] = $val;
                }
            }

            // Формирование сообщения, путем объединения значений формы,
            // отсортированных по именам ключей в порядке возрастания.
            uksort($fields, "strcasecmp");
            $fieldValues = "";

            foreach($fields as $value)
            {
                if(is_array($value))
                    foreach($value as $v)
                    {
                        //Конвертация из текущей кодировки (UTF-8)
                        //необходима только если кодировка магазина отлична от Windows-1251
                        $v = iconv("utf-8", "windows-1251", $v);
                        $fieldValues .= $v;
                    }
                else
                {
                    //Конвертация из текущей кодировки (UTF-8)
                    //необходима только если кодировка магазина отлична от Windows-1251
                    $value = iconv("utf-8", "windows-1251", $value);
                    $fieldValues .= $value;
                }
            }

            // Формирование значения параметра WMI_SIGNATURE, путем
            // вычисления отпечатка, сформированного выше сообщения,
            // по алгоритму MD5 и представление его в Base64

            $signature = base64_encode(pack("H*", md5($fieldValues . $key)));

            //Добавление параметра WMI_SIGNATURE в словарь параметров формы

            $fields["WMI_SIGNATURE"] = $signature;

            // Формирование HTML-кода платежной формы

            print "<form action='https://wl.walletone.com/checkout/checkout/Index' method='POST'>";

            foreach($fields as $key => $val)
            {
                if(is_array($val))
                    foreach($val as $value)
                    {
                        print "$key: <input type='text' name='$key' value='$value'/><br />";
                    }
                else
                    print "$key: <input type='text' name='$key' value='$val'/><br />";
            }

            print "<input type='submit'/></form>";

            ?>

        </article>
    </section>
<?php include("application/views/footer_new.php"); ?>