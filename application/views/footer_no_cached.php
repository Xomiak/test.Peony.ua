<?php
if(!check_smartphone()) {
    ?>
    <script position="left-top" type="text/javascript" src="https://vzakupke.com/widget/assets/js/initSiteWidget.js" charset="UTF-8"></script>

    <?php
}

if (userdata('msg') !== false && !strpos($_SERVER['REQUEST_URI'], 'export')) {
    getModalDialog('modal_login_ok', userdata('msg'));
    ?>
    <script type="text/javascript">
        j(document).ready(function () {
            jQuery('#modal_login_ok').modal('show');
        });
    </script>
    <?php
    unset_userdata('msg');

}
?>

<!--MODAL DIALOG-->
<div class="modal fade bs-example-modal-sm" id="login-logout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm registration-modal">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <h2>Авторизация</h2>
            <p>Выберите любую, удобную для Вас, соц. сеть, либо почтовую службу:</p>
            <script src="//ulogin.ru/js/ulogin.js"></script>
            <div id="uLoginde88b987"
                 data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=google,yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=http://<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-sm" id="download" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm registration-modal">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <h2>Авторизируйтесь</h2>
            <p style="font-size: 12px;">Для того что бы скачать каталог.</p>
            <div id="uLoginf418c86f"
                 data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=google,yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=http://<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>
        </div>
    </div>
</div>

<div class="modal fade modal-fast-order bs-example-modal-lg" id="fast-order" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <div id="fast-order-content">

            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-message-order bs-example-modal-message" id="modal-message" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-message">
        <div class="modal-content">
            <button class="close" type="button" data-dismiss="modal">&times;</button>
            <div id="modal-message-content">

            </div>
        </div>
    </div>
</div>