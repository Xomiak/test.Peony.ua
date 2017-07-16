<?php
include("header.php");
?>
<table width="100%" height="100%">
    <tr>
        <td align="center">
            <?php
            if($this->session->userdata('login_err') !== false)
            {
                ?>
                <div class="login_err"><?=$this->session->userdata('login_err')?></div>
                <?php
                $this->session->unset_userdata('login_err');
            }
            ?>
            <h1 class="login_h1"><?=$this->config->item('cms_name')?> <span class="login_version"><?=$this->config->item('cms_version')?></span></h1>
            <h2 class="login_h2">Панель управления</h2>
            <form action="/admin/login/" method="post">
            <div style="background: url(/img/admin/login-bg.png) no-repeat; width: 533px; height: 332px;">
                    <div class="login_login">Ваш логин</div>
                    <div class="input_login"><input required type="text" name="login" /></div>
                    <div class="login_password">Пароль</div>
                    <div class="input_password"><input required type="password" name="pass" /></div>
                <br />
                    <div class="login_button"><input type="submit" style="background: url(/img/admin/admin_enter.png) no-repeat; width: 173px; height: 46px;" value="" /></div>                
            </div>
            <?=form_close()?>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>