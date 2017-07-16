<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<title>«PEONY» - украинский производитель платьев. Купить платья оптом, женская одежда, купить в магазине Пиони.</title>
<meta name="keywords" content="купить платья оптом, украинский производитель платьев, женская одежда купить, peony, Пиони, магазин одежды, оптовый магазин одежды, купить одежду,украинский производитель одежды,платья оптом." />
<meta name="description" content="«PEONY» - украинский производитель платьев. Купить платья оптом, женская одежда, купить в магазине Пиони." />
<meta name="robots" content="index, follow" />
</head>
<body>
<?php
//var_dump($_SERVER['REQUEST_URI']);die();
$url = "//peony.ua/all/?iframe";

//if(isset($_GET['api_url']) && $_GET['hash'] != '') $url .= 'api_url='.$_GET['api_url'].'&';
if(isset($_GET['api_id']) && $_GET['api_id'] != '') $url .= 'api_id='.$_GET['api_id'].'&';
if(isset($_GET['api_settings']) && $_GET['api_settings'] != '') $url .= 'api_settings='.$_GET['api_settings'].'&';
if(isset($_GET['viewer_id']) && $_GET['viewer_id'] != '') $url .= 'viewer_id='.$_GET['viewer_id'].'&';
//if(isset($_GET['viewer_type']) && $_GET['hash'] != '') $url .= 'viewer_type='.$_GET['viewer_type'].'&';
if(isset($_GET['sid']) && $_GET['sid'] != '') $url .= 'sid='.$_GET['sid'].'&';
if(isset($_GET['secret']) && $_GET['secret'] != '') $url .= 'secret='.$_GET['secret'].'&';
//
//if(isset($_GET['access_token']) && $_GET['access_token'] != '') $url .= 'access_token='.$_GET['access_token'].'&';
//if(isset($_GET['user_id']) && $_GET['user_id'] != '') $url .= 'user_id='.$_GET['user_id'].'&';
//if(isset($_GET['is_app_user']) && $_GET['is_app_user'] != '') $url .= 'is_app_user='.$_GET['is_app_user'].'&';
//if(isset($_GET['auth_key']) && $_GET['auth_key'] != '') $url .= 'auth_key='.$_GET['auth_key'].'&';
//if(isset($_GET['ad_info']) && $_GET['ad_info'] != '') $url .= 'ad_info='.$_GET['ad_info'].'&';
//if(isset($_GET['is_secure']) && $_GET['is_secure'] != '') $url .= 'is_secure='.$_GET['is_secure'].'&';
//if(isset($_GET['ads_app_id']) && $_GET['ads_app_id'] != '') $url .= 'ads_app_id='.$_GET['ads_app_id'].'&';
//
//if(isset($_GET['referrer']) && $_GET['referrer'] != '') $url .= 'referrer='.$_GET['referrer'].'&';
//if(isset($_GET['lc_name']) && $_GET['lc_name'] != '') $url .= 'lc_name='.$_GET['lc_name'].'&';
//if(isset($_GET['sign']) && $_GET['sign'] != '') $url .= 'sign='.$_GET['sign'].'&';
//if(isset($_GET['hash']) && $_GET['hash'] != '') $url .= 'hash='.$_GET['hash'].'&';

//var_dump($url);

?>
	<iframe src="<?=$url?>" width="979px" height="500px" frameborder="0"></iframe>
</body>
</html>