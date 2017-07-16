<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Данный пример предоставляет возможность отправлять СМС сообщения
 * с подменой номера, просматривать остаток кредитов пользователя,
 * просматривать статус отправленных сообщений.
 * -----------------------------------------------------------------
 * Для работы данного примера необходимо подключить SOAP-расширение.
 */
class Turbosms
{
    // Все данные возвращаются в кодировке UTF-8

    private $from;
    private $client;
    private $login;
    private $password;
    private $sender;
    private $debug = false;

    public function __construct($params = array())
    {
        if (!isset($params['soapClient'])) $params['soapClient'] = 'http://turbosms.in.ua/api/wsdl.html';
        $this->client = $params['soapClient'];

        if (!isset($params['login'])) $params['login'] = '';
        $this->login = $params['login'];

        if (!isset($params['password'])) $params['password'] = '';
        $this->password = $params['password'];

        if (!isset($params['from'])) $params['from'] = '';
        $this->from = $params['from'];

        if (!isset($params['sender'])) $params['sender'] = $_SERVER['SERVER_NAME'];
        $this->sender = $params['sender'];

        if(isset($params['debug']) && $params['debug'] == true)
            $this->debug = true;

        $this->connect();

    }

    public function getCredits()
    {
        // Получаем количество доступных кредитов
        $result = $this->client->GetCreditBalance();
        if($this->debug)
            echo $result->GetCreditBalanceResult . PHP_EOL;
        return $result->GetCreditBalanceResult . PHP_EOL;
    }

    public function connect()
    {
        // Подключаемся к серверу
        $this->client = new SoapClient($this->client);
        // Данные авторизации
        $auth = ['login' => $this->login,
            'password' => $this->password];

        // Авторизируемся на сервере
        $result = $this->client->Auth($auth);

        // Результат авторизации
        if($this->debug)
            echo $result->AuthResult . PHP_EOL;
        return $result->AuthResult . PHP_EOL;
    }

    public function getFunctions()
    {
        return $this->client->__getFunctions();
    }

    public function sendSms($to, $message, $encoding = 'UTF-8')
    {
        $result = false;
        header('Content-type: text/html; charset=utf-8');

        echo '<pre>';
        try {

            if ($encoding != 'UTF-8')
                $message = iconv($encoding, 'utf-8', $message);                       // Текст сообщения ОБЯЗАТЕЛЬНО отправлять в кодировке UTF-8

            // Отправляем сообщение с WAPPush ссылкой
            // Ссылка должна включать http://
            $sms = ['sender' => $this->sender,
                'destination' => $to,
                'text' => $message,
                'wappush' => 'http://' . $_SERVER['SERVER_NAME']];
            $result = $this->client->SendSMS($sms);

        } catch
        (Exception $e) {
            $result['error'] =  'Ошибка: ' . $e->getMessage() . PHP_EOL;
        }
        echo '</pre>';

        if($this->debug)
            echo 'Результаты отправки: '.$result->SendSMSResult->ResultArray[0] . PHP_EOL;

        return $result->SendSMSResult;

//
//
//        // Отправляем сообщение на один номер.
//        // Подпись отправителя может содержать английские буквы и цифры. Максимальная длина - 11 символов.
//        // Номер указывается в полном формате, включая плюс и код страны
//        $sms = ['sender' => 'Rassilka',
//            'destination' => '+380XXXXXXXXX',
//            'text' => $text];
//        $result = $client->SendSMS($sms);
//
//        // Отправляем сообщение на несколько номеров.
//        // Номера разделены запятыми без пробелов.
//        $sms = ['sender' => 'Rassilka',
//            'destination' => '+380XXXXXXXX1,+380XXXXXXXX2,+380XXXXXXXX3',
//            'text' => $text];
//        $result = $client->SendSMS($sms);
//
//        // Выводим результат отправки.
//        echo $result->SendSMSResult->ResultArray[0] . PHP_EOL;
//
//        // ID первого сообщения
//        echo $result->SendSMSResult->ResultArray[1] . PHP_EOL;
//
//        // ID второго сообщения
//        echo $result->SendSMSResult->ResultArray[2] . PHP_EOL;

    }

    public function getStatus($sms_id)
    {
        // Запрашиваем статус конкретного сообщения по ID
        $sms = ['MessageId' => $sms_id];
        $status = $this->client->GetMessageStatus($sms);
        if($this->debug)
            echo $status->GetMessageStatusResult . PHP_EOL;
        return $status->GetMessageStatusResult . PHP_EOL;
    }


}