<?php


namespace App\Http\Helper;

use nusoap_client;
use soapclient;
use SoapFault;


class SamanGateway
{
    /**
     * @var integer
     */
    private $MerchantCode = '';
    private $password = '';

    /**
     * MellatBank constructor.
     * @param string $MerchantCode
     * @param string $password
     */
    public function __construct($MerchantCode = '',$password = '')
    {
        if (!empty($MerchantCode)) {
            $this->MerchantCode = $MerchantCode;
        }
        if (!empty($password)) {
            $this->password = $password;
        }

    }

    /**
     * @param $ticket_price
     * @param $orderId
     * @return string
     * @throws SoapFault
     */
    public function startPayment($ticket_price, $orderId): string
    {
        $RedirectURL 	=  config('settings.saman.callback');
        $SoapClient = new SoapClient('https://sep.shaparak.ir/payments/initpayment.asmx?wsdl', ['encoding' => 'UTF-8']);
        $Token = $SoapClient->RequestToken($this->MerchantCode, $orderId, $ticket_price);
        if (!empty($Token) && strlen($Token) > 10) {
            $this->postToken($RedirectURL, $Token);
        }
        return $this->error($Token);
    }

    /**
     * @param $params
     * @return bool|string|null
     */
    public function verifyPayment($params)
    {
        $data = null;
        $ResCode = $params['StateCode']; // status code $StateCode
        $RefId = $params['RefNum']; // bank reference id equal to $RefNum
        $merchant = $this->MerchantCode;
        if (isset($RefId)) {
            $client = new nusoap_client('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL', 'wsdl');
            $nuSoapProxy = $client->getProxy();
            $amount = $nuSoapProxy->VerifyTransaction($RefId, $merchant);
            // Payment in the bank has been successful
            // مبلغ پرداختی با مبلغ ارسالی مقایسه میشود
            if(isset($_POST['State']) && $_POST['State'] === 'OK' && $ResCode === '0') {
                if ($amount > 0) {
                    // All payment steps completed correctly.
                    // show out put
                    $data = true;
                }
                else {
                    // وقتی که مقدار پرداختی با ارسالی برابر نباشد برگشت و خطا
                    $nuSoapProxy->ReverseTransaction($RefId, $merchant, $this->password, $amount);
                    $data = false;

                }
            }
            else {
                $data =$this->error($ResCode);
            }
        }
        else {
            $data = $this->error($ResCode);
        }
        return $data;

    }

    /**
     * @param $number
     * @return string
     */
    protected function error($number): string
    {
        return $this->response($number);
    }

    /**
     * @param $number
     * @return string
     */
    protected function response($number): string
    {
        switch ($number) {
            case '0'  :
                $prompt = ' ﺗﺮﺍﻛﻨﺶ ﺑﺎ ﻣﻮﻓﻘﻴﺖ ﺍﻧﺠﺎﻡ ﺷﺪ';
                break;
            case '-1':
                $prompt = 'خطا درپردازش اطلاعات ارسالی';
                break;
            case '-3':
                $prompt = 'ورودی حاوی کارکتر غیرمجاز';
                break;
            case '-4':
                $prompt = 'کلمه عبور یا کد فروشنده اشتباه است.';
                break;
            case '-6':
                $prompt = 'سند قبلا برگشت کامل یافته است.';
                break;
            case '-7':
                $prompt = 'رسید دیجیتال خالی است.';
                break;
            case '-8':
                $prompt = 'طول ورودی بیشتر از حد مجاز است';
                break;
            case '-9':
                $prompt = 'وجود کاراکترهای غیرمجاز در مبلغ بازگشتی';
                break;
            case '-10':
                $prompt = 'رسید دیجیتال بصورت Base64 نیست.';
                break;
            case '-11':
                $prompt = 'طول ورودی ها کمتر از حد مجاز است.';
                break;
            case '-12':
                $prompt = 'مبلغ برگشتی منفی است.';
                break;
            case '-13':
                $prompt = 'مبلغ برگشتی برای برگشت جزئی بیش از مبلغ برگشت خورده ی رسید دیجیتال است.';
                break;
            case '-14':
                $prompt = 'چنین تراکنشی تعریف نشده است.';
                break;
            case '-15':
                $prompt = 'مبلغ برگشتی بصورت اعشاری داده شده است.';
                break;
            case '-16':
                $prompt = 'خطای داخلی سیستم';
                break;
            case '-17':
                $prompt = 'برگشت زدن جزوی تراکنش مجاز نمیباشد.';
                break;
            case '-18':
                $prompt = 'ای پی سرور فروشنده نامعتبر است.';
                break;
            default:
                $prompt = 'خطاي نامشخص.';
        }
        return " ({$number}) : {$prompt}";
    }


    /**
     * @param string $RedirectURL
     * @param $Token
     */
    public function postToken(string $RedirectURL, $Token): void
    {
        $resForm = "<form name='Samanid' action='https://sep.shaparak.ir/Payment.aspx' method='POST'>";
        $resForm .= "<input type='hidden' id='RedirectURL' name='RedirectURL' value='{$RedirectURL}' />";
        $resForm .= "<input type='hidden' id='Token' name='Token' value='{$Token}' />";
        $resForm .= "</form>";
        $resForm = "<div style='display:none;visibility:hidden'>{$resForm}</div>";
        $resForm .= "<script language='javascript'>document.Samanid.submit();</script>";
        echo $resForm;
        die;
    }


}
