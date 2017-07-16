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
        <label class="sel_addr" addr_id="<?=$addr['id']?>" for="addr-<?=$addr['id']?>">
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
                        <a href="#"><img src="/img/addr-edit.png" alt="Редактировать адрес" title="Редактировать адрес" /></a>
                    </div>
                </li>
        </label>
        <?php
    }
}
$addr['id'] = 0;
?>
        <div class="clr"></div>
        

        <label for="addr-new" class="sel_addr" addr_id="0">
            <li>
                <div class="form-addr-radio">
                    <input id="addr-new" name="addrId" value="new" type="radio" <?php if(count($addrArr) == 0) echo ' checked';?> />
                </div>
                <div class="form-addr-text">
                    <br/>
                    Добавить новый адрес
                </div>
                <div class="form-addr-edit">
                    <a href="#"><img src="/img/addr-edit.png" alt="Редактировать адрес" title="Редактировать адрес" /></a>
                </div>
            </li>
        </label>
    </ul>
</div>

<script>
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
</script>
