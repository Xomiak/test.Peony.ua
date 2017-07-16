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
<script>
    $(function() {
        function log( message ) {
            $( "<div/>" ).text( message ).prependTo( "#log" );
            $( "#log" ).scrollTop( 0 );
        }
        $( "#birds" ).autocomplete({

            source: "/admin/ajax/users/?search",
            minLength: 1,
            select: function( event, ui ) {
                // window.location = "http://<?=$_SERVER['SERVER_NAME']?>"+ui.item.url;
                log( ui.item ?
                    "Выбран клиент: " + ui.item.label + " ID: " + ui.item.id:
                    "Нет ничего подходящего " + this.value );
            }
        });
    });
</script>
            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/coupons/">Купоны</a></div>
                    <div class="top_menu_link"><a href="/admin/coupons/add/">Создать купон</a></div>
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                    <table>
                        <tr>
                            <td>Код *:</td>
                            <td><input required type="text" name="code" size="50" value="<?php if(isset($_POST['code'])) echo $_POST['code']; else if(isset($coupon['code'])) echo $coupon['code']; else echo $new_code;?>" /></td>
                        </tr>
                        <tr>
                            <td>Скидка:</td>
                            <td><input style="text-align: center" type="text" name="discount" size="2" value="<?php if(isset($_POST['discount'])) echo $_POST['discount']; else if(isset($coupon['discount'])) echo $coupon['discount']; else echo userdata('coupon_discount')?>" /></td>
                        </tr>
                        <tr>
                            <td>Тип:</td>
                            <td>
                                <input<?php if(isset($coupon) && $coupon['type'] == 0) echo ' checked'; else echo ' checked';?> type="radio" name="type" value="0" />%<br />
                                <input<?php if(isset($coupon) && $coupon['type'] == 1) echo ' checked'; ?> type="radio" name="type" value="1" />USD
                            </td>
                        </tr>
                        <?php
                        $date = date("Y-m-d");
                        $d = new DateTime($date);
                        $d->modify("+7 day");
                        ?>
                        <tr>
                            <td>Начало действия:</td>
                            <td><input placeholder="ГГГГ-ММ-ДД" style="text-align: center" type="text" name="start_date" size="10" value="<?php if(isset($_POST['start_date'])) echo $_POST['start_date']; else if(isset($coupon['start_date'])) echo $coupon['start_date']; else echo $date;?>" /></td>
                            <td>В формате: ГГГГ-ММ-ДД. Если пусто, то безвременный</td>
                        </tr>
                        <tr>

                            <td>Конец действия:</td>
                            <td><input placeholder="ГГГГ-ММ-ДД" style="text-align: center" type="text" name="end_date" size="10" value="<?php if(isset($_POST['end_date'])) echo $_POST['end_date']; else if(isset($coupon['end_date'])) echo $coupon['end_date']; else echo $d->format("Y-m-d");?>" /></td>
                            <td>В формате: ГГГГ-ММ-ДД. Если пусто, то безвременный</td>
                        </tr>
                        <tr>
                            <td>Индивидуально для:</td>
                            <td><input id="birds" class="ui-autocomplete-input" role="textbox" aria-autocomplete="list" aria-haspopup="true" placeholder="e-mail клиента" type="text" name="user_login" size="50" value="<?php if(isset($_POST['user_login'])) echo $_POST['user_login']; else if(isset($coupon['user_login'])) echo $coupon['user_login'];?>" />
                            <br /><div id="log"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Инфо:</td>
                            <td><textarea style="width: 325px; height: 200px" placeholder="Дополнительная информация о купоне" name="info"><?php if(isset($_POST['info'])) echo $_POST['info']; else if(isset($coupon['info'])) echo $coupon['info']; else echo userdata('coupon_info');?></textarea></td>
                        </tr>

                        <tr>
                            <td>Скидка действует только на следующие товары:</td>
                            <?php
                            $arr = false;
                            if(isset($coupon))
                                $arr = json_decode($coupon['products_only']);
                            $po = '';
                            $model = getModel('shop');
                            if($arr){
                                $first = true;
                                foreach ($arr as $item){
                                    $item = $model->getArticleById($item);
                                    if($item) {
                                        if(!$first)
                                            $po .= ',';
                                        $po .= $item['id'];
                                        $first = false;
                                    }
                                }
                            }
                            ?>
                            <td><textarea name="products_only" style="width: 325px; height: 100px"><?=$po?></textarea><br />
                                <?php
                                if(isset($coupon))
                                $arr = json_decode($coupon['products_only']);
                                //vd($arr);
                                if($arr){
                                    echo '<ul class="coils"><ol>';

                                    foreach ($arr as $item){
                                        $item = $model->getArticleById($item);
                                        if($item) {
                                            echo '<li><a href="' . getFullUrl($item) . '">' . $item['name'] . ' (' . $item['color'] . ')</a></li>';
                                        }
                                    }
                                    echo '</ol></ul>';
                                }
                                ?>
                                <br /><div id="log"></div>
                            </td>
                        </tr>

                        <tr>
                            <td><input<?php if(isset($coupon['multi']) && $coupon['multi'] == 1) echo " checked"; ?> type="checkbox" name="multi" /> Мультискидка (Многоразовая)</td>
                        </tr>
                        <tr>
                            <td><input<?php if(isset($coupon['not_sale']) && $coupon['not_sale'] == 1) echo " checked"; ?> type="checkbox" name="not_sale" /> не действует на товары из Sale</td>
                        </tr>
                        <?php

                        ?>
                        <tr>
                            <td><input<?php if(isset($coupon['used_date']) && $coupon['used_date'] != false) echo " disabled"; ?> type="submit" name="save" value="Сохранить" /></td>
                            <td><input<?php if(isset($coupon['used_date']) && $coupon['used_date'] != false) echo " disabled"; ?> type="submit" name="save_and_stay" value="Сохранить и остаться" /></td>
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