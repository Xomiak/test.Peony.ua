<?php
$user = false;
if($this->session->userdata('login') !== false)
{
    $user = $this->users->getUserByLogin($this->session->userdata('login'));
}
?>
<?php include("application/views/head.php"); ?>
<?php include("application/views/header.php"); ?>
<h1>Оформление заказа</h1>

    <table>
        <tr>
            <td>
                <?php
                if($user)
                {
                    ?>
                    Вы авторизированы, как <?=$user['name']?> (<?=$user['email']?>).
                    <?php
                }
                else
                {
                    ?>
<!--                    Я уже покупал у Вас.<br />-->
<!--                    <form method="post" action="/login/">-->
<!--                        <input type="hidden" name="action" value="login" />-->
<!--                        <input type="hidden" name="back" value="--><?//=$_SERVER['REQUEST_URI']?><!--" />-->
<!--                        e-mail:<br />-->
<!--                        <input type="text" name="login" required /><br />-->
<!--                        Пароль:<br />-->
<!--                        <input type="password" name="pass" required /><br />-->
<!--                        <input type="submit" value="Войти" />-->
<!--                    </form>-->
                    <?php
                }
                ?>
            </td>
            <td>
<!--                <form method="post" action="/order/"></form>-->
            </td>
        </tr>
    </table>

<?php include("application/views/footer.php"); ?>