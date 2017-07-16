<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$CI = & get_instance();
$CI->load->library('Turbosms');

function sms_getCredits(){
    $params = sms_getParams();
    $sms = new Turbosms($params);
    return $sms->getCredits();
}

function sms_send($to, $message){
    $nCount = strlen($to);
    if($nCount == 10)
        $to = '+38'.$to;
    $params = sms_getParams();
    $sms = new Turbosms($params);
    $result = $sms->sendSms($to, $message);
    return $result;
}

function sms_getParams(){
    $sms_login = getOption('sms_login');
    $sms_password = getOption('sms_password');
    $sms_sender = getOption('sms_sender');
    $params = array(
        'login' => $sms_login,
        'password' => $sms_password,
        'debug' => false,
        'sender' => $sms_sender
    );
    return $params;
}