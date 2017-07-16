<div class="form-info adresses">
    <div class="form-addr-block">
        <h2>Адрес доставки</h2>
        <input type="hidden" id="checked_addr_id" name="addr_id" value="0">
        <ul class="form-adress">
            <?php
            $sModel = getModel('shop');
            $addrArr = $sModel->getAddrByLogin(userdata('login'));
            if((is_array($addrArr)) && count($addrArr) > 0){
                foreach ($addrArr as $addr) {
                    ?>
                    <label class="sel_addr" addr_id="<?=$addr['id']?>" for="addr-<?=$addr['id']?>" country_id="<?=$addr['country_id']?>">
                        <li>
                            <div class="form-addr-radio">
                                <input id="addr-<?=$addr['id']?>" name="addrId" value="<?=$addr['id']?>" type="radio" <?php if($addr['default'] == 1 || count($addrArr) == 1) echo ' checked';?> />
                            </div>
                            <div class="form-addr-text">
                                <?php
                                echo $addr['name'].'<br />'.$addr['country'].', '.$addr['city'].'<br />';
                                if($addr['country_id'] == 1)
                                    echo 'Новая Почта №'.$addr['np'];
                                ?>
                            </div>
                            <div class="form-addr-edit">
                                <a class="adress_popup" data-target=".add_adress" data-toggle="modal" addr_id="<?=$addr['id']?>" action="edit"><img src="/img/addr-edit.png" alt="Редактировать адрес" title="Редактировать адрес" /></a>
                            </div>
                        </li>
                    </label>
                    <?php
                }
            }
            $addr['id'] = 0;
            ?>
            <div class="clr"></div>




        </ul>
        <a class="adress_popup sel_addr add_new_adress_button" addr_id="0" data-toggle="modal" data-target=".add_adress">
            Добавить адрес
        </a>
    </div>
    <script>
        var checkedAddr = 0;
        $(document).ready(function () {
            $(".sel_addr").click(function () {
                checkedAddr = $(this).attr('addr_id');
            });

            $(".adress_popup").click(function () {
                addAddrForm($(this).attr('addr_id'));
            });

            function addAddrForm(addr_id = 0){
                $.ajax({
                    url: '/ajax/show_block/form_adress/',
                    method: 'post',
                    async: false,
                    data: {
                        "addr_id": addr_id
                    },

                }).done(function (data) {
                    $("#popup_adress_block").html(data);
                });
            }
        });
    </script>

    <!--script>
        var checkedAddr = 0;
        $(document).ready(function () {
            $(".sel_addr").click(function () {
                checkedAddr = $(this).attr('addr_id');
                $("#checked_addr_id").val(checkedAddr);
                if(checkedAddr == 0){
                    $("#finish").attr('disabled','disabled');
                    showFormNewAddr();
                } else{
                    $("#finish").removeAttr('disabled');
                    $("#new_addr_div").html('');
                }
            })
        })

        function showFormNewAddr() {
            $.ajax({
                url: '/my_cart/?block_only=form_new_addr',
                method: 'post',
                async: false,
                data: {
                    "action": "show_block",
                    "block_only": "form_new_addr"
                },

            }).done(function (data) {
                $("#new_addr_div").html(data);
            });
        }
    </script-->

</div>

<div class="modal fade add_adress bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" id="add_edit_address">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <div id="popup_adress_block">
                <h2>Добавление нового адреса</h2>
            </div>
        </div>
    </div>
</div>

<div id="countries" class="form-info">
    <script src="/js/jquery.min.js"></script>
      <h2 class="pay-var">Способ оплаты</h2>
    <ul class="pay-var-list">

        <li>
            <input required="" class="payment_method" checked="" id="bank_transfer" name="payment" value="Перевод на карту Приват Банка" placeholder="" type="radio">
            <label for="bank_transfer">Перевод на карту Приват Банка</label>

        </li>

        <li>
            <input class="payment_method" required="" id="pay_by_liqpay" name="payment" value="liqpay" placeholder="" type="radio">
            <label for="pay_by_liqpay">Банковской картой любого банка (LiqPay)</label>

        </li>
        <li>
            <input required="" class="payment_method" id="interkassa" name="payment" value="Интеркасса" placeholder="" type="radio">
            <label for="interkassa">Интеркасса</label><span data-toggle="modal" data-target=".interkassa"><img style="cursor: pointer; padding-left: 5px;" src="/img/info-green.png" alt="info" title="Информация об Интеркассе"></span>
        </li>
        <li>
            <input required="" class="payment_method" id="international" name="payment" value="Международный денежный перевод" placeholder="" type="radio">
            <label for="international">Международный денежный перевод</label><span data-toggle="modal" data-target=".international"><img style="cursor: pointer; padding-left: 5px;" src="/img/info-green.png" alt="info" title="Информация о международных платежах"></span>
        </li>
        <li>
            <div class="oferta-div">
                <input id="oferta" name="oferta" type="checkbox"> <label for="oferta">С <a style="color: black; text-decoration: underline; font-size: 18px" href="/oferta/" target="_blank">публичной офертой</a>
                    ознакомлен</label>
            </div>
            <div class="form-error" id="form_oferta_err" style="display: none">Вы должны
                подтвердить
                ознакомление!
            </div>
        </li>
    </ul>
</div>
<input id="finish" type="submit" value="Отправить заказ"
       onclick="yaCounter26267973.reachGoal('zakaz'); ga('send', 'event', 'zakaz', 'click');">