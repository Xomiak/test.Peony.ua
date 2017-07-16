<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
//$CI = & get_instance();

function findTask($where){
    $CI = & get_instance();
    if(isset($where['comment'])) unset($where['comment']);
    return $CI->db->get_where('tasks',$where,1,0)->result_array();
}

function addTask($dbins){
    if(!findTask($dbins)) {
        $CI = & get_instance();
        $CI->db->insert('tasks', $dbins);
        //echo 'Задача добавлена<br />';
    } //else echo 'Задача уже есть!<br />';
}