<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function getUri()
{
    return $_SERVER['REQUEST_URI'];
}

function getDomain()
{
    return $_SERVER['SERVER_NAME'];
}

function post($name)
{
    if(isset($_POST[$name]))
        return $_POST[$name];
    else
        return false;
}

function postIf($name)
{
    if(isset($_POST[$name]))
        return $_POST[$name];
    else
        return '';
}

function getModel($model){
    $CI = & get_instance();

    $model = 'Model_'.$model;

    $CI->load->model($model);
    return $CI->$model;
}

// DATABASE //
function getItemById($id, $table){
    $model = getModel('sql');
    return $model->getById($id, $table);
}

function updateItem($id, $table, $dbins){
    $CI = & get_instance();
    return $CI->db->where('id', $id)->limit(1)->update($table, $dbins);
}

function getItemBy($by, $value, $table){
    $model = getModel('sql');
    return $model->getBy($by, $value, $table);
}

// * DATABASE * //

function loadLibrary($name){
    $CI = & get_instance();
    $CI->load->library($name);
}

function loadHelper($name){
    if(strpos($name,'_helper') === false)
        $name .= '_helper';

    $CI = & get_instance();
    $CI->load->helper($name);
}

function insertInDb($table, $dbins, $returnData = true){
    $CI = & get_instance();
    $CI->db->insert($table, $dbins);
    if($returnData){
        foreach ($dbins as $key => $value){
            $CI->db->where($key,$value);
        }
        $result = $CI->db->get($table)->result_array();
        if(isset($result[0])) return $result[0];
        else return false;
    }
}

function getFieldtypeByName($name){
    $CI = & get_instance();
    $CI->load->model('Model_fieldtypes', 'ft');
    return $CI->ft->getByName($name);
}

function get_no_get() {
    $back = $_SERVER['REQUEST_URI'];
    $strpos = strpos($back, '?');
    if ($strpos) {
        $back = substr($back, 0, $strpos);
    }
    return $back;
}

function request_uri($noPagination = false, $noGetParams = false, $addedGet = false)
{
    $CI = & get_instance();
    $uri = $CI->uri->uri_string();
    if($uri == "") $uri = "/";
    else $uri = "/".$uri."/";

    if($addedGet){
        $uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($uri,'?');
        if ($pos)
            $uri .= '&'.$addedGet;
        else $uri .= '?'.$addedGet;
        return $uri;
    }

    if(($noPagination) && strpos($uri,'/!'))
    {
        $pos = strpos($uri, '/!');
        $res = substr($uri,0,$pos);
        $pos = strpos($uri,'/',$pos+1);
        if($pos)
        {
            $res .= substr($uri,$pos);
        }
        $uri = $res;
    }

    if($noGetParams)
    {
        $pos = strpos($uri,'?');
        if ($pos) {
            $uri = substr($uri, 0, $pos);
        }
    }


    return $uri;
}

function getOption($name, $full = false, $notId = false)
{

    $CI = & get_instance();
    $ret = $CI->model_options->getOption($name, $full);
    //if($name == 'link_vk'){vdd($ret);}
    if($ret === false) return false;
    else return $ret;
}

function setOption($name, $value){
    $CI = & get_instance();
    $CI->db->where('name', $name)->limit(1)->update('options', array('value' => $value));
}

function getOptionByLang($name, $full = false, $notId = false)
{
    $ret = false;
    $current_lang = strtolower(getCurrentLanguage());
    $default_lang = strtolower(getDefaultLanguageCode());
    if($current_lang != $default_lang)
        $ret = getOption($name.'_'.$current_lang, $full, $notId);
    if(!$ret)
        $ret = getOption($name, $full, $notId);

    return $ret;
}

function getOptionsByModule($module){
    $CI = & get_instance();
    $options = $CI->model_options->getOptionsByModule($module);
    if($options){
        $ret = array();
        foreach ($options as $item){
            $ret[$item['name']] = $item['value'];
        }
    }
    return $ret;
}

function getOptionArray($name, $childArray = false)
{
    $optArr = array();
    $ret = getOption($name);
    if ($ret) {
        $ret = explode('|', $ret);
        if($ret){
            foreach ($ret as $item){
                $item = explode('=', $item);
                if(is_array($item) && count($item) > 1){
                    $optArr[] = array(
                        'name'  => $item[0],
                        'value' => $item[1]
                    );
                } else $optArr[] = $item[0];
            }
        }
    }
    return $optArr;
}

function userdata($name)
{
    $CI = & get_instance();

    return $CI->session->userdata($name);
}

function set_userdata($name, $value)
{
    $CI = & get_instance();
    return $CI->session->set_userdata($name, $value);
}

function unset_userdata($name)
{
    $CI = & get_instance();

    return $CI->session->unset_userdata($name);
}

function back_no_get()
{
    $back = $_SERVER['REQUEST_URI'];
    $strpos = strpos($back, '?');
    if ($strpos) {
        $back = substr($back, 0, $strpos);
    }
    if (!$back)
        $back = '/';
    redirect($back);
}

function vd($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

function vdd($value)
{
    vd($value);
    die();
}

function alert($value)
{
    ?>
    <script>
        alert('<?=$value?>');
    </script>
    <?php
}