<html>
    <head>
        <title><?=$title?> - <?=$this->config->item('cms_name')?> <?=$this->config->item('cms_version')?> Админ</title>
        <link rel="stylesheet" href="/css/admin.css" type="text/css" />
        <link rel="stylesheet" href="/css/admin_themes.css" type="text/css" />


  <link rel="stylesheet" href="/css/jquery.ui.all.css">
<!--        <script src="/js/autocomplete/jquery-1.7.1.js"></script>-->
<!--        <script src="/js/autocomplete/jquery.ui.core.js"></script>-->
<!--        <script src="/js/autocomplete/jquery.ui.accordion.js"></script>-->
<!--        <script src="/js/autocomplete/jquery.ui.button.js"></script>-->
<!--        <script src="/js/autocomplete/jquery.ui.widget.js"></script>-->
<!--        <script src="/js/autocomplete/jquery.ui.position.js"></script>-->
<!--        <script src="/js/autocomplete/jquery.ui.autocomplete.js"></script>-->



	<link rel="stylesheet" href="/js/ckedit/sample.css">

        <link rel="stylesheet" type="text/css" href="/libs/fancybox/jquery.fancybox.min.css">

    </head>
    <body>
    <?php
        if(userdata('type') == 'admin')
        {
        $newOrders = getNewOrdersCount();
        $newComments = getNewCommentsCount();
        if($newOrders > 0)
            $newOrders = '<a href="/admin/orders/"><span style="color:red">'.$newOrders.'</span></a>';
        if($newComments > 0)
            $newComments = '<a href="/admin/comments/"><span style="color:red">'.$newComments.'</span></a>';

            $this->load->helper('sms_helper');
            $smsCount = sms_getCredits();

            $tkdzst = $this->model_main->getMain();
        ?>
        <div class="message">
            Новых заказов: <?=$newOrders?>
            Новых отзывов: <?=$newComments?>
<!--            SMS Кредитов: --><?//=$smsCount?><!-- <span  style="font-size: 11px">(<a href="https://turbosms.ua/auth.html" target="_blank">пополнить</a>)</span>-->
            Скачиваний каталога: <span title="Всего"><?=$tkdzst['price_downloads_count']?></span>/<span title="Вчера"><?=$tkdzst['price_downloads_count_yesterday']?></span>/<span title="Сегодня"><?=$tkdzst['price_downloads_count_today']?></span>
        </div>
    <?php
    }
    ?>
    