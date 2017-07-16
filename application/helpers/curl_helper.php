<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

//получение данных по указаному url
//используется для вызова методов API вконтакте
//при ошибке возвращает false
function get_curl($url='', $data = Array())
{
    //описание параметров
    //$url - урл, куда будет идти запрос
    //$data - список GET параметров в связке [имя]=>[значение], для передачи на указанный урл

    if(!empty($url) && !empty($data)):

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($curl);
        curl_close($curl);
        //ставим паузу от 0,2 до 0,3 секунды
        $rand_time_out = rand(250000, 320000);
        usleep($rand_time_out);

        return json_decode($out);
    else:
        return false;
    endif;
}

//отправка данных(изображения/файла) методом POST на указанный url
//при ошибке возвращает false
function set_post_curl($upload_url, $img_url)
{
    //описание параметров
    //$upload_url - урл, куда будет идти запрос
    //$img_url - путь к файлу (серверный)

    if(!empty($upload_url) && !empty($img_url)):
        $post_params = array(
            'file' => '@'.$img_url
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $upload_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
        $out = curl_exec($curl);
        curl_close($curl);

        return json_decode($out);
    else:
        return false;
    endif;
}