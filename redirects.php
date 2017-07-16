<?php

$url = $_SERVER['REQUEST_URI'];

if($url == '/novosti-industrii/pioni---ukrainskii-prolizvoditel-platev-u-nas-vi-mojete-kupit-platya-optom/')
{
    header("HTTP/1.1 301 Moved Permamently");
    header("Location: http://peony.ua/peony-ukrainskii-prolizvoditel-platev/");
    die();
}

if($url == '/index.php')
{
    header("HTTP/1.1 301 Moved Permamently");
    header("Location: http://peony.ua/");
    die();
}
if($url == '/0/')
{
    header("HTTP/1.1 301 Moved Permamently");
    header("Location: http://peony.ua/");
    die();
}
if($url == '/index.html')
{
    header("HTTP/1.1 301 Moved Permamently");
    header("Location: http://peony.ua/");
    die();
}

// if  ( $_SERVER['HTTPS'] )
// {
//         $host = $_SERVER['HTTP_HOST'];
//         $request_uri = $_SERVER['REQUEST_URI'];
//         $good_url = "http://" . $host . $request_uri;

//         header( "HTTP/1.1 301 Moved Permanently" );
//         header( "Location: $good_url" );
//         exit;
// } 

// if($_SERVER['SERVER_NAME'] != 'peony.ua')
// {
//     header("HTTP/1.1 301 Moved Permamently");
//     header("Location: http://peony.ua".$_SERVER['REQUEST_URI']);
//     die();
// }

if(strpos($url,'admin/') != true) {
    if (preg_match('/[A-Z]/', $url) == 1) {
        $url = strtolower($url);
        header("HTTP/1.1 301 Moved Permamently");
        header("Location: http://" . $_SERVER['SERVER_NAME'] . $url);
        die();
    }
}
?>