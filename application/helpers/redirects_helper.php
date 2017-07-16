<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$ci = & get_instance();
$ci->load->helper('shortnames_helper');

$www_on         = $ci->config->item('www_on');
$index_php_on   = $ci->config->item('index_php_on');

$domain = getDomain();
$uri    = getUri();

if($www_on === false)
{
    if(strpos($domain,'www.') !== true)
    {
        $domain = str_replace('www.','',$domain);
        redirect('http://'.$domain.getUri());
    }
}
elseif($www_on !== false)
{
    if(strpos($domain,'www') !== true)
    {
        $domain = 'www.'.$domain;
        redirect('http://'.$domain);
    }
}

if(!$index_php_on)
{
    if(strpos(getUri(),'/index.php') !== false)
    {
        redirect('http://'.$domain);
    }
}
else
{
    if(getUri() == '/')
    {
        redirect('http://'.$domain.'/index.php');
    }
}