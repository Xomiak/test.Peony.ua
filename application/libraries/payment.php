<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payment
{
    public function __construct()
    {
        global $config;
        $this->config = $config;
        $this->mysql_driver = strtolower($config['mysql_driver']);
        $this->ik = $config['pay_system']['interkassa'];
        $this->up = $config['pay_system']['unitpay'];
        //$this->rk = $config['pay_system']['robokassa']; // Ждём следующую версию
    }

    public function ik_sort($param)
    {
        $data['ik_co_id'] = $this->ik['shop_id'];
        foreach ($param as $key => $value) // убирает параметры без /ik_/
        {
            if (!preg_match('/ik_/', $key)) continue;
            $data[$key] = $value; // сохраняем параметры
        }
        return $data;
    }

    public function ik_sign($param) // интеркасса генератор контрольной цифровой подписи со стороны сервера
    {
        $data = $this->ik_sort($param);
        $ikSign = $data['ik_sign']; // сохраняем приходящую переменную
        unset($data['ik_sign']); // удаляем придодащую переменную, для генирации подписи
        $key = ($data['ik_pw_via'] == 'test_interkassa_test_xts') ? $this->ik['test_key'] : $this->ik['key'];
        if ($data['ik_pw_via'] == 'test_interkassa_test_xts' && !$this->ik['testing']) return false;
        ksort ($data, SORT_STRING); // сортируем массив
        array_push($data, $key); // внедряем переменуую $key в массив
        $signStr = implode(':', $data); // записываем массив в формат @string через :
        $sign = base64_encode(md5($signStr, true)); // хешируем подпись
        return ($sign == $ikSign) ? true : false;
    }


    public function up_json_reply($type = "error", $params) // системный ответ для сервера unitpay, json
    {
        if ($type == "check" || $type == "pay") $type = "success";
        $reply = array( // системный массив
            'error' => array(
                "jsonrpc" => "2.0",
                "error" => array("code" => -32000, "message" => $this->config['message']['fail']),
                'id' => $params['projectId']
            ),
            'success' => array(
                "jsonrpc" => "2.0",
                "result" => array("message" => $this->config['message']['success']),
                'id' => $params['projectId']
            )
        );
        return json_encode($reply[$type]); // возвращаем json
    }

    public function up_sign($reply) { // Проверка цифровой подписи unitpay
        ksort($reply); // сортируем массив
        $exp = explode("-", $this->up['project_id']);
        $Sign = $reply['sign']; // сохраняем подпись
        unset($reply['sign']); // удаляем подпись
        $reply['projectId'] = $exp[0]; // заменяем существующий ид проекта на свой, дабы убедиться, что запрос от нашего UP
        $return = (md5(join(null, $reply).$this->up['key']) != $Sign) ? "error" : "success"; // генирация и проверка подписи
        return $this->up_json_reply($return, $reply);
    }

    public function mysql_prepare($sql, $db, $binds)
    {
        foreach($binds as $key => $bind) {
            $a[] = $key;
            $b[] = "'{$bind}'";
        }
        $query = mysql_query(str_replace($a, $b, $sql), $db);
        return (!$query) ? false : true;
    }

    public function pay_systems($pay_system)
    {
        foreach($this->config['pay_system'] as $system => $options)
        {
            if ($pay_system == $system && $options['enable']) return true;
        }
        return false;
    }

    public function pay_form($amount, $user, $pay_system) // генерация GET запроса
    {
        $amount = (int) $amount;
        if ($amount > $this->config['max_pay'] || $amount <= 0 || !$this->pay_systems($pay_system)) return "/{$this->config['path']}/payment.php?reply=fail";
        $desc = "{$this->config['description']} {$user}";
        switch ($pay_system) {
            case "interkassa" :
                return "https://sci.interkassa.com/?ik_co_id={$this->ik['shop_id']}&ik_pm_no={$user}&ik_am={$amount}&ik_cur={$this->ik['cur']}&ik_desc={$desc}";
                break;
            case "unitpay" :
                return "https://unitpay.ru/pay/{$this->up['project_id']}?sum={$amount}&account={$user}&desc={$desc}";
                break;
            case "robokassa" :
                // Ждём следующую версию
                break;
            default:
                return "/{$this->config['path']}/payment.php?reply=fail";
                break;
        }
    }

    public function pay($amount, $user) // пополнение счета
    {
        $sql = "UPDATE `{$this->config['table']}` SET `{$this->config['column_money']}` = {$this->config['column_money']} + :amount WHERE `{$this->config['column_user']}` = :user";
        if(extension_loaded("MySQLi") && $this->mysql_driver == "auto" || $this->mysql_driver == "mysqli") {
            $conn = new mysqli($this->config['host'], $this->config['user'], $this->config['pass'], $this->config['bd_name']);
            $stmt = $conn->prepare(str_replace(array(":amount", ":user"), "?", $sql));
            $stmt->bind_param("is", $amo, $username);
            $username = $user; $amo = (int) $amount;
            return $stmt->execute();
        } else if(extension_loaded("PDO") && extension_loaded("PDO_MySQL") && $this->mysql_driver == "auto" || $this->mysql_driver == "pdo"){
            $conn = new PDO("mysql:host={$this->config['host']};dbname={$this->config['bd_name']}", $this->config['user'], $this->config['pass']);
            $query = $conn->prepare($sql);
            return $query->execute(array(":amount" => (int) $amount, ":user" => $user));
        } else {
            $conn = mysql_connect($this->config['host'], $this->config['user'], $this->config['pass']) or die('Ошибка подключения: '.mysql_error());
            $db = mysql_select_db($this->config['bd_name'], $conn);
            $user = mysql_real_escape_string(htmlspecialchars($user, ENT_QUOTES));
            return $this->mysql_prepare($sql, $conn, array(":amount"=>(int)$amount, ":user"=>$user)) or die(mysql_error());
        }
    }
}
?>