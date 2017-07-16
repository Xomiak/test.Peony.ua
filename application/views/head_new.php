<?php
//ob_start("sanitize_output");
set_currency();
if ($robots == '')
    $robots = 'index, follow';
$main_currency = $this->model_options->getOption('main_currency');
$currency = $this->session->userdata('currency');

if (!$currency)
    $currency = $main_currency;


// TKDZ
$canonical = false;

if (isset($article) && isset($article['price'])) {
    $title = $category['name_buy'] . ' ' . $article['name'] . ' (' . $article['color'] . ') - ' . $category['name'] . ' от производителя PEONY';
    $description = $category['name_buy'] . ' ' . $article['name'] . ' (' . $article['color'] . ') ' . getPriceInCurrency($article['price'], $article['discount'], 'UAH') . ' грн. ' . $category['name'] . ' от производителя. «PEONY» - Оптовый интернет-магазин женской одежды. ☎ +38 (097) 259-29-26';

}

$tkdz = $this->model_tkdz->getByUrl("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
if ($tkdz) {
    //vd($tkdz);
    if (isset($tkdz['title']) && $tkdz['title']) $title = $tkdz['title'];
    if (isset($tkdz['keywords']) && $tkdz['keywords']) $keywords = $tkdz['keywords'];
    if (isset($tkdz['description']) && $tkdz['description']) $description = $tkdz['description'];
    if (isset($tkdz['h1']) && $tkdz['h1'] && isset($category['h1'])) $category['h1'] = $tkdz['h1'];
    if (isset($tkdz['seo']) && $tkdz['seo'] && isset($category['seo'])) $category['seo'] = $tkdz['seo'];
    if (isset($tkdz['canonical']) && $tkdz['canonical'] != NULL && $tkdz['canonical'] != '') $canonical = $tkdz['canonical'];
    if (isset($tkdz['robots']) && $tkdz['robots']) $robots = $tkdz['robots'];
} else {
    // Titles
    if (mb_strlen($title) > 70)
        $title = str_replace('женская одежда ', '', $title);
    if (mb_strlen($title) > 70)
        $title = str_replace('от производитея PEONY', 'от производитея', $title);
    if (mb_strlen($title) > 70)
        $title = str_replace('от производитея', 'PEONY', $title);
    if (mb_strlen($title) > 70)
        $title = str_replace('PEONY', '', $title);
    if (mb_strlen($title) > 70)
        $title = mb_substr($title, 0, 70);

    // Descriptions:
    if (mb_strlen($description) > 160)
        $description = str_replace('Оптовый интернет-', '', $description);
    if (mb_strlen($description) > 160)
        $description = str_replace('«PEONY» - магазин женской одежды', 'женская одежда Peony', $description);
    if (mb_strlen($description) > 160)
        $description = str_replace('женская одежда Peony', 'Peony', $description);
    if (mb_strlen($description) > 160)
        $description = str_replace('Peony', '', $description);
    if (mb_strlen($description) > 160)
        $description = mb_substr($description, 0, 160);
}
if (mb_strlen($description) < 50) $description .= ' «PEONY» - Оптовый интернет-магазин женской одежды. ☎ +38 (097) 259-29-26';

// /TKDZ
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <?php
    if (isset($article)) {
        $url = getFullUrl($article);
        if ($_SERVER['REQUEST_URI'] != $url) {
            echo '<link rel="canonical" href="https://' . $_SERVER['SERVER_NAME'] . $url . '" />';
            $robots = 'noindex, follow';
        }
    }

    if (isset($category) && isset($razmer) && $razmer !== false) {
        $url = '/' . $category['url'] . '/';
        echo '<link rel="canonical" href="https://' . $_SERVER['SERVER_NAME'] . $url . '" />

';
//		if($_SERVER['REQUEST_URI'] != $url)
//			$robots = 'noindex, follow';
    }
    ?>
    <meta name="w1-verification" content="122853487401"/>
    <meta name="interkassa-verification" content="098346e8a79b39de8ed984c1b467d6af"/>
    <meta name='yandex-verification' content='779e25f2e10b5688'/>

    <link rel=canonical href="https://<?= $_SERVER['SERVER_NAME'] ?><?= $_SERVER['REQUEST_URI'] ?>"/>

    <meta name="keywords" content="<?= $keywords ?>"/>
    <meta name="description" content="<?= $description ?>"/>
    <meta name="robots" content="<?= $robots ?>"/>

    <meta property="og:image"
          content="https://<?= $_SERVER['SERVER_NAME'] ?>/<?php if (isset($article['image']) && $article['image'] != '')
              echo $article['image']; elseif (isset($category['image']) && $category['image'] != '')
              echo $category['image'];
          elseif (isset($page['image']) && $page['image'] != '')
              echo $page['image'];
          else echo 'img/site-img/logo.png'; ?>"/>
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>"/>
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>"/>

    <?php
    echo '
<link rel="alternate" href="//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '" hreflang="ru-ua" />
';
    $url = '//peony-dress.ru' . $_SERVER['REQUEST_URI'];
    if (isset($category)) {
        $url = str_replace($category['url'], $category['original_url'], $url);
    }
    if (isset($article) && isset($article['original_url'])) {
        $url = str_replace($article['url'], $article['original_url'], $url);
    }
    echo '<link rel="alternate" href="' . $url . '" hreflang="ru-ru" />

';
    ?>

    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <!--	<link rel = "stylesheet" type = "text/css" href = "/js/layerslider.css">-->
    <!--link rel = "stylesheet" type = "text/css" href = "/css/zoom.css"-->
    <link rel="stylesheet" type="text/css" href="/css/my_cart.css">
    <!-- Include Cloud Zoom CSS -->
    <!--link href = "/js/cloudzoom.css" type = "text/css" rel = "stylesheet"/-->
    <!-- Include Thumbelina CSS -->
    <!--link href = "/js/thumbelina.css" type = "text/css" rel = "stylesheet"/>
    <link rel = "stylesheet" type = "text/css" href = "/css/jquery.bxslider.css"-->
    <link rel="stylesheet" type="text/css" href="/css/style.min.css">
    <link rel="stylesheet" type="text/css" href="/js/jquery.formstyler/jquery.formstyler.css">
    <link rel="stylesheet" type="text/css" href="/css/export-vk.css">
    <link rel="stylesheet" type="text/css" href="/css/responsive.min.css">
    <link rel="stylesheet" href="/css/autocomplete/jquery.ui.all.css">


    <meta name="google-site-verification" content="Qrvd7MEUv87yvczec3C9hrX4pcXZZWsgQ5BjOEQ4mnQ"/>

    <!-- VK TARGETING -->
    <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=Rut6RtrqkcouUllS637h*F6023bBUiSZ710FVK*HoO6PCESCnYOe8FSJIz8J1vQ5Bnb4OiOwcSFtYW0nMk7TKMSQIwnqMYTCel3*YLc76TrAL3FgrMAX*0dPjV/7tOtEU5CGBc6VGFmePS9eCgK3gU8NjbSL*TKQLhtohriChUY-';</script>

    <?php
    if ($_SERVER['REQUEST_URI'] == '/slider-test/') {
        ?>
        <link rel="stylesheet" type="text/css" media="all"
              href="//slider.peony.ua/revslider/public/assets/css/settings.css"/>
        <script type="text/javascript" src="//slider.peony.ua/assets/js/includes/jquery/jquery.js"></script>
        <script type="text/javascript"
                src="//slider.peony.ua/revslider/public/assets/js/jquery.themepunch.tools.min.js"></script>
        <script type="text/javascript"
                src="//slider.peony.ua/revslider/public/assets/js/jquery.themepunch.revolution.min.js"></script>
        <script type="text/javascript" id="revslider_script" src="//slider.peony.ua/assets/js/revslider.js"></script>
        <?php
    }
    ?>


    <script src="//js/jquery.formstyler/jquery.formstyler.min.js"></script>

    <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=L4G7UqZpakEeKv/UFJeHlltJ2XwMAktZH4bZTV8nQCxWb8NupYdD8bZCnYkAwGry5v2r6Cl5Ee0U9R*/aMH/1NNiqcV0Ww/Crjkvnihl89X/vkmx/PTJNCd4buCmApMfTsqoBCPzd6RBVFLzvM42iRA764fIsK9yBvjBYe4FphQ-&pixel_id=1000077515';</script>
    <!-- Facebook Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq)return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window,
            document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1547794215250532'); // Insert your pixel ID here.
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=1547794215250532&ev=PageView&noscript=1"
        /></noscript>
    <!-- DO NOT MODIFY -->
    <!-- End Facebook Pixel Code -->


</head>