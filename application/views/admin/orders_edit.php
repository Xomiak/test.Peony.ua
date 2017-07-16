<?php
include("header.php");
?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" valign="top"><?php include("menu.php"); ?></td>
        <td width="20px"></td>
        <td valign="top">
            <div class="title_border">
                <div class="content_title"><h1><?=$title?></h1></div>
                <div class="back_and_exit">
                    		    русский <a href="/en<?=$_SERVER['REQUEST_URI']?>">english</a>

                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
            
            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/orders/">Заказы</a></div>
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <?php
                if($order['order_sms_sended'] == 1) {
                    echo 'SMS уведомление о заказе было отправлено: ' . json_decode($order['order_sms_result']);
                }
                 if($order['ttn_sms_sended'] == 1){
                    echo 'SMS уведомление с ТТН было отправлено: '.json_decode($order['ttn_sms_result']);
                }
                ?>
                <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                    <table>
                        <tr>
                            <td>ID:</td>
                            <td><input disabled type="text" name="id" size="50" value="<?=$order['id']?>" /></td>
                        </tr>
                        <tr>
                            <td>Дата:</td>
                            <td><input disabled type="text" size="50" value="<?=$order['date']?> <?=$order['time']?>" /></td>
                        </tr>
                        <tr>
                            <td>Клиент:</td>
                            <td><a target="_blank" href="/admin/users/edit/<?=$order['user_id']?>/"><?=$user['name']?></a></td>
                        </tr>
                        <tr>
                            <td>Оплата:</td>
                            <td>
                                <SELECT name="payment" id="payment">
                                    <option>Не указан</option>
                                    <option value="Перевод на карту Приват Банка"<?php if($order['payment'] == 'Перевод на карту Приват Банка') echo ' selected'; ?>>Перевод на карту Приват Банка</option>
                                    <option value="liqpay"<?php if($order['payment'] == 'liqpay') echo ' selected'; ?>>liqpay</option>
                                    <option value="interkassa"<?php if($order['payment'] == 'interkassa') echo ' selected'; ?>>interkassa</option>
                                    <option value="Международный денежный перевод"<?php if($order['payment'] == 'Международный денежный перевод') echo ' selected'; ?>>Международный денежный перевод</option>

                                </SELECT>
                            </td>

                        </tr>
                        <tr>
                            <td>Доставка:</td>
                            <td><input type="text" size="50" value="<?=$order['delivery']?>" /></td>
                        </tr>
                        <tr>
                            <td>Статус:</td>
                            <td>
                                <SELECT name="status" id="status">
                                    <option value="new"<?php if($order['status'] == 'new') echo ' selected'; ?>><?=getStatus('new')?></option>
                                    <option value="processing"<?php if($order['status'] == 'processing') echo ' selected'; ?>><?=getStatus('processing')?></option>
                                    <option value="one_click"<?php if($order['status'] == 'one_click') echo ' selected'; ?>><?=getStatus('one_click')?></option>
                                    <option value="npnp_payed"<?php if($order['status'] == 'npnp_payed') echo ' selected'; ?>><?=getStatus('npnp_payed')?></option>
                                    <option value="payed"<?php if($order['status'] == 'payed') echo ' selected'; ?>><?=getStatus('payed')?></option>
                                    <option value="error"<?php if($order['status'] == 'error') echo ' selected'; ?>><?=getStatus('error')?></option>
                                    <option value="wait_accept"<?php if($order['status'] == 'wait_accept') echo ' selected'; ?>><?=getStatus('wait_accept')?></option>
                                    <option value="process"<?php if($order['status'] == 'process') echo ' selected'; ?>><?=getStatus('process')?></option>
                                    <option value="not_payed"<?php if($order['status'] == 'not_payed') echo ' selected'; ?>><?=getStatus('not_payed')?></option>
                                    <option value="sended"<?php if($order['status'] == 'sended') echo ' selected'; ?>><?=getStatus('sended')?></option>
                                    <option value="done"<?php if($order['status'] == 'done') echo ' selected'; ?>><?=getStatus('done')?></option>
                                    <option value="canceled"<?php if($order['status'] == 'canceled') echo ' selected'; ?>><?=getStatus('canceled')?></option>
                                    <option value=" fail"<?php if($order['status'] == ' fail') echo ' selected'; ?>><?=getStatus(' fail')?></option>
                                </SELECT><div id="sendSMS"></div>
                                <?php
                                $smsMessage = getOption('sms_sumTemplate');
                                $orderPrice = $order['full_summa'] * getCurrencyValue($order['currency']);
                                $orderPrice .= ' '.$order['currency'];
                                $smsMessage = str_replace('[order_id]',$order['id'],$smsMessage);
                                $smsMessage = str_replace('[order_sum]',$orderPrice,$smsMessage);

                                ?>
                                <script>
                                    $(document).ready(function () {
                                        $("#status").change(function () {
                                            var status = $(this).val();
                                            var ttn = $("#inp_ttn").val();
                                            var tel = '<?=$user['tel']?>';
                                            if(status == 'sended'){
                                                $("#sendSMS").html('<input type="checkbox" id="chk_sendSMS" name="send_sms" checked /> Отправить ТТН клиенту по SMS на номер: <input id="inp_tel" type="text" size="50" name="tel" value="<?=$user['tel']?>" />&nbsp;<span id="span_tel" style="color: red"></span>');

                                                if(ttn == '')
                                                    $("#sms_message").html("Укажите номер ТТН!");
                                                else $("#sms_message").html("");
                                                if(tel == '')
                                                    $("#span_tel").html("Клиент не указал свой тел!");
                                                else $("#span_tel").html("");

                                            } else if(status == 'processing'){
                                                var sms_message = '<?=$smsMessage?>';
                                                $("#sendSMS").html('<input type="checkbox" id="chk_sendSMS" name="send_sum_sms" checked /> Отправить клиенту сумму оплаты по SMS на номер: <input id="inp_tel" type="text" size="50" name="tel" value="<?=$user['tel']?>" />&nbsp;<span id="span_tel" style="color: red"></span><br /><input size="50" placeholder="Текст сообщения" type="text" name="sms_mesage" value="' + sms_message + '" />');

                                                if(tel == '')
                                                    $("#span_tel").html("Клиент не указал свой тел!");
                                                else $("#span_tel").html("");

                                            } else $("#sendSMS").html('');
                                        });

                                        $("#inp_ttn").keyup(function () {
                                            var ttn = $(this).val();
                                            if(ttn != '')
                                                $("#sms_message").html('');
                                            else if($("#chk_sendSMS").attr("checked") == 'checked')
                                                $("#sms_message").html("Укажите номер ТТН!");
                                        });

                                        $("#inp_tel").keyup(function () {
                                            var tel = $(this).val();
                                            if(tel != '')
                                                $("#span_tel").html('');
                                            else if($("#chk_sendSMS").attr("checked") == 'checked')
                                                $("#span_tel").html("Клиент не указал свой тел!");
                                        });

                                    });
                                </script>

                            </td>
                        </tr>
                        <tr>
                            <td>ТТН:</td>
                            <td><input id="inp_ttn" type="text" size="50" name="ttn" value="<?=$order['ttn']?>" />&nbsp;<span id="sms_message" style="color: red"></span></td>
                        </tr>
                        <tr>
                            <td>Заказ:</td>
                            <td>
                                <table class="products" border="1">
                                    <th>Товар</th>
									<th>Цвет</th>
                                    <th>Кол-во</th>
									<th>Цена за 1 шт.</th>
                                    <?php
                                    //$my_cart = unserialize($page['products']);
                                    $data = $order['products'];
                                    $my_cart = $data;
                                    //var_dump($my_cart);
                                    $my_cart = unserialize($my_cart);
                                    //var_dump($my_cart);
                                    $pcount = count($my_cart);
                                    for($j = 0;$j < $pcount; $j++)
                                    {
                                        $mc = $my_cart[$j];
                                        $product = $this->shop->getProductById($mc['shop_id']);
                                        $cat = $this->categories->getCategoryById($product['category_id']);
                                        $parent = false;
					$razmer = explode('*',$product['razmer']);
                                        if($product['parent_category_id'] != 0) $parent = $this->categories->getCategoryById($product['parent_category_id']);
                                        
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="/<?php if($parent) echo $parent['url'].'/'; ?><?=$cat['url']?>/<?=$product['url']?>/" target="_blank"><?=$product['name']?></a>
                                            </td>
											<td>
												<?=$product['color']?>
											</td>
                                            <td align="center">
                                                <?php
												$rcount = count($razmer);
												$mc['kolvo'] = 0;
												for($i2 = 0; $i2 < $rcount; $i2++)
												{
													if(isset($mc['kolvo_'.$razmer[$i2]]) && $mc['kolvo_'.$razmer[$i2]] > 0)
													{
													echo $razmer[$i2].': '.$mc['kolvo_'.$razmer[$i2]].'<br />';
													$mc['kolvo'] += $mc['kolvo_'.$razmer[$i2]];
													}
												}
												?>
												<strong>Всего: <?=$mc['kolvo']?></strong>
                                            </td>
											<td>
												<?php
												$price = getAkciyaPrice($product, $order['date']);
												echo $price.' USD';												
												if(isAkciya($product, $order['date'])) echo ' (Акция)';
												?>
											</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </td>
                        </tr>
                        <?php
                        //var_dump($order['currencies']);
                        $currencies = json_decode($order['currencies'], true);
//                        var_dump($currencies);
//                        if(!$currencies){
//                            echo "NO";
//                        }
                        ?>
                        <tr>
                            <td>Курс на момент оформления:</td>
                            <td>
                                UAH: <?=$currencies['UAH']?><br/>
                                RUB: <?=$currencies['RUB']?>
                            </td>
                        </tr>
                        <tr>
                            <td>Сумма:</td>
                            <td>
                                <?=$order['summa']?>$
                            </td>
                        </tr>
                        <tr>
                            <td>Сумма в выбранной валюте:</td>
                            <td>
                                <?php
                                if(isset($order['currency']) && $order['currency'] == 'uah')
                                    $order['summa'] = $order['summa'] * $currencies['UAH'];
                                elseif(isset($order['currency']) && $order['currency'] == 'rub')
                                    $order['summa'] = $order['summa'] * $currencies['RUB'];
                                ?>
                                <?=$order['summa']?> <?=$order['currency']?>
                            </td>
                        </tr>

                        <tr>
                            <td>Адрес:</td>
                            <td><textarea class="ckeditor" name="adress"><?=$order['adress']?></textarea></td>
                        </tr>
			
			<?php
			if($order['pay_answer'] != '')
			{
			    ?>
			    <tr>
				<td>Ответ платёжной системы:</td>
				<td>
				    <?php
				    var_dump(unserialize($order['pay_answer']));
				    ?>
				</td>
			    </tr>
			    <?php
			}
			?>
                        
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<? if($order['active']==1) echo ' checked'?> /> Активный</td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="save" value="Сохранить" /></td>
                            <td><input type="submit" name="save_and_stay" value="Сохранить и остаться" /></td>
                        </tr>
                    </table>
                </form>
            </div>
	    
	    
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>