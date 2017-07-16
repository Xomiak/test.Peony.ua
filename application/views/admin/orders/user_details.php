<div class="saved_message" id="userdata_saved" style="display: none">Сохранено!</div>
<div>
<form id="user_details_form" name="user_details">
    <input id="user_id" type="hidden" name="user_id" value="<?=$user['id']?>">
    <div style="float: right; padding-right: 50px" class="user_socials">

        <?php
        if($user['avatar'] != '')
            echo '<img src="'.$user['avatar'].'"><br/>';
        if($user['profile'] != '') {
            echo '<b>Соц.сеть:</b><br/>';
            $arr = explode('|', $user['profile']);
            if(!is_array($arr) && $arr != '')
                echo '<a target="_blank" rel="nofollow" href="' . $arr . '">Перейти к профилю</a>';
            else{
                foreach ($arr as $profile) {
                    if($profile != '')
                    echo '<a target="_blank" rel="nofollow" href="' . $profile . '">Перейти к профилю</a><br/>';
                }
            }
        }
        ?>

    </div>
    <table>

        <tr>
            <td>Имя:</td>
            <td><input id="user_name" class="userdata_value" type="text" name="name" before="<?= $user['name'] ?>" value="<?= $user['name'] ?>" size="50"/>
            </td>
        </tr>
        <tr>
            <td>Фамилия:</td>
            <td><input id="user_lastname" class="userdata_value" type="text" name="lastname" value="<?= $user['lastname'] ?>" before="<?= $user['lastname'] ?>"
                       size="50"/></td>
        </tr>
        <tr style="display: none">
            <td>Логин *:</td>
            <td><input id="user_login" class="userdata_value" disabled="disabled" type="text" name="login" size="50" value="<?= $user['login'] ?>"
                       before="<?= $user['login'] ?>"/></td>
        </tr>
        <tr>
            <td>Тип клиента:</td>
            <td>
                <SELECT id="user_type_id" class="userdata_value" before="<?= $user['user_type_id'] ?>" name="user_type_id" required>
                    <?php
                    $mUsers = getModel('users');
                    $userTypes = $mUsers->getUserTypes(1);
                    if ($userTypes) {
                        foreach ($userTypes as $type) {
                            echo '<option value="' . $type['id'] . '"';
                            if ($type['id'] == $user['user_type_id'])
                                echo ' selected';
                            echo '>' . $type['name'] . '</option>';
                        }
                    }
                    ?>
                </SELECT>
            </td>
        </tr>
        <tr>
            <td>e-mail:</td>
            <td><input id="user_email" class="userdata_value" type="email" name="email" value="<?= $user['email'] ?>" before="<?= $user['email'] ?>"
                       size="50"/></td>
        </tr>
        <tr>
            <td>Дата последнего захода:</td>
            <td>2017-06-26 09:56</td>
        </tr>

        <tr style="display: none">
            <td>День рождения:</td>
            <td><input class="userdata_value" type="text" name="tel" value="<?= $user['bd_date'] ?>" before="<?= $user['bd_date'] ?>"
                       size="50"/></td>
        </tr>
        <tr>
            <td>Телефон:</td>
            <td><input id="user_tel" class="userdata_value" type="text" name="tel" value="<?= $user['tel'] ?>" before="<?= $user['tel'] ?>" size="50"/></td>
        </tr>
        <tr>
            <td>Страна:</td>
            <td><input id="user_country" class="userdata_value" type="text" name="country" value="<?= $user['country'] ?>" before="<?= $user['country'] ?>"
                       size="50"/></td>
        </tr>
        <tr>
            <td>Город:</td>
            <td><input id="user_city" class="userdata_value" type="text" name="city" value="<?= $user['city'] ?>" before="<?= $user['city'] ?>" size="50"/>
            </td>
        </tr>


        <tr>
            <td colspan="2"><button class="save_order_button" id="userdata_button" disabled="disabled" onclick="return false">Сохранить</button></td>
        </tr>
    </table>

</form>
<script>

    $(document).ready(function () {
        $("#userdata_button").hide();
    });
//    Отлавливаем, были ли изменения в форме
    $( ".userdata_value" ).keyup(function() {
        userdataChanged = true;
        showSaveUserdataButton();
    });
$( ".userdata_value" ).change(function() {
    userdataChanged = true;
    showSaveUserdataButton();
});

function showSaveUserdataButton() {
    if($("#userdata_button").attr('disabled') == 'disabled'){
        $("#userdata_button").removeAttr('disabled');
        $("#userdata_button").fadeIn('slow');
        $('#li_order').animate({
            height : $("#li_order").height() + 45
        });
    }
}

$(document).ready(function () {
    $("#userdata_button").click(function () {
        saveUserdataChanges();
    });
});
</script>
</div>
<div style="clear: both"></div>