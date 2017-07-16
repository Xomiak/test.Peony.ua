<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function getMonthName($no)
{
    $month = '';
    if($no == '01') $month = 'января';
    elseif($no == '02') $month = 'февраля';
    elseif($no == '03') $month = 'марта';
    elseif($no == '04') $month = 'апреля';
    elseif($no == '05') $month = 'мая';
    elseif($no == '06') $month = 'июня';
    elseif($no == '07') $month = 'июля';
    elseif($no == '08') $month = 'августа';
    elseif($no == '09') $month = 'сентября';
    elseif($no == '10') $month = 'октября';
    elseif($no == '11') $month = 'ноября';
    elseif($no == '12') $month = 'декабря';
    return $month;
}

function getDayOfWeek($day, $month, $year)
{
    $day = jddayofweek ( cal_to_jd(CAL_GREGORIAN, $month, $day, $year) , 1 );
    
    $day = str_replace('Monday', 'Понедельник', $day);
    $day = str_replace('Tuesday', 'Вторник', $day);
    $day = str_replace('Wednesday', 'Среда', $day);
    $day = str_replace('Thursday', 'Четверг', $day);
    $day = str_replace('Friday', 'Пятница', $day);
    $day = str_replace('Saturday', 'Суббота', $day);
    $day = str_replace('Sunday', 'Воскресенье', $day);
    
    return $day;
}

function getMonthName2($no)
{
    $month = '';
    if($no == '01') $month = 'январь';
    elseif($no == '02') $month = 'февраль';
    elseif($no == '03') $month = 'март';
    elseif($no == '04') $month = 'апрель';
    elseif($no == '05') $month = 'май';
    elseif($no == '06') $month = 'июнь';
    elseif($no == '07') $month = 'июль';
    elseif($no == '08') $month = 'август';
    elseif($no == '09') $month = 'сентябрь';
    elseif($no == '10') $month = 'октябрь';
    elseif($no == '11') $month = 'ноябрь';
    elseif($no == '12') $month = 'декабрь';
    return $month;
}

function getWordDate($date, $withYear = true)
{
    if($date != '')
    {
        $arr = explode('-',$date);
        $ret = $arr[2].' '.getMonthName($arr[1]);
        if($withYear) $ret .= ' '.$arr[0];
        return $ret;
    }
    else return '';
}

?>