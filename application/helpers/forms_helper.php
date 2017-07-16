<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function isFormsHere()
{
    if($_SERVER['REQUEST_URI'] == '/otzivi/')
        return formOtzivi();
    else return false;
}

function formOtzivi()
{
    $ci = & get_instance();
    $ci->load->helper('shortnames_helper');
    
    if(isset($_POST['form_otzivi']))
    {
        $err = false;
        if(isset($_POST['captcha']))
        {
            // КАПЧА
            $ci->load->helper('captcha');
            $expiration = time()-7200; // Two hour limit
            $ci->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
            
            // Then see if a captcha exists:
            $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
            $binds = array($_POST['captcha'], $ci->input->ip_address(), $expiration);
            $query = $ci->db->query($sql, $binds);
            $cap = $query->row();
            if($cap->count == 0) $err['captcha'] = 'Это поле заполнено не верно!';
            ///////////
        }
        if((!post('name')) || post('name') == '')       $err['name'] = 'Это поле не может быть пустым!';
        if((!post('comment')) || post('comment') == '')    $err['comment'] = 'Это поле не может быть пустым!';
        
        if($err)
        {
            $ret['err'] = $err;
            return $ret;
        }
        else
        {
            $dbins = array(
                'name'      => post('name'),
                'comment'   => post('comment'),
                'ip'        => GetRealIp(),
                'date'      => date("Y-m-d"),
                'time'      => date("H:i"),
                'page_id'   => post('page_id')
            );
            $ci->db->insert('comments', $dbins);
            unset($_POST);
            return true;
        }
    }
}