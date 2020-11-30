<?php


namespace App\Http\Helper;


use nusoap_client;

class MellatGateway
{
    /**
     * @var integer
     */
    private $terminal = '';

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';

    private $nameSpace = 'http://interfaces.core.sw.bps.com/';


    /**
     * MellatBank constructor.
     * @param string $terminal
     * @param string $username
     * @param string $password
     */
    public function __construct($terminal = '', $username = '', $password = '')
    {
        if (!empty($terminal)) {
            $this->terminal = $terminal;
        }

        if (!empty($username)) {
            $this->username = $username;
        }

        if (!empty($password)) {
            $this->password = $password;
        }
    }

    /**
     * @param $ticket_price
     * @param $orderId
     * @return mixed|string|null
     */
    public function startPayment($ticket_price, $orderId)
    {
        $outPut =null;
        $callBackUrl = config('settings.mellat.callback');
        $client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
        $terminalId = $this->terminal;
        $userName = $this->username;
        $userPassword = $this->password;
        $localDate = date('ymj');
        $localTime = date('His');
        $additionalData = '';
        $err = $client->getError();
        if ($err) {
            $outPut = $err;
        }
        $parameters = [
            'terminalId' => $terminalId,
            'userName' => $userName,
            'userPassword' => $userPassword,
            'orderId' => $orderId,
            'amount' => $ticket_price,
            'localDate' => $localDate,
            'localTime' => $localTime,
            'additionalData' => $additionalData,
            'callBackUrl' => $callBackUrl,
            'payerId' => 0
        ];
        $result = $client->call('bpPayRequest', $parameters, $this->nameSpace);
        if ($client->fault) {
            $outPut = $result;
        }

        $err = $client->getError();
        if ($err) {
            $outPut = $err;
        }
        $res = explode(',', $result);
        $ResCode = $res[0];
        if ($ResCode === '0') {
            $this->postRefId($res[1]);
        } else {
            $outPut = $this->error($ResCode);

        }
        return $outPut;
    }

    /**
     * @param $refIdValue
     */
    protected function postRefId($refIdValue): void
    {
        echo '<form name="mellat" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
				<input type="hidden" id="RefId" name="RefId" value="' . $refIdValue . '">
				</form>
				<script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>';
        exit;
    }


    /**
     * @param $params
     * @return array
     */
    public function prepareParameters($params)
    {
        $orderId = $params['SaleOrderId'];
        $verifySaleOrderId = $params['SaleOrderId'];
        $verifySaleReferenceId = $params['SaleReferenceId'];

        return [
            'terminalId'=> $this->terminal,
            'userName'=> $this->username,
            'userPassword'=> $this->password,
            'orderId' => $orderId,
            'saleOrderId' => $verifySaleOrderId,
            'saleReferenceId' => $verifySaleReferenceId
        ];
    }

    /**
     * @param $params
     * @return mixed|array|bool|string
     */
    public function verifyPayment($params)
    {
        $outPut = false;
        $client = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' ) ;
        $parameters = $this->prepareParameters($params);
        $err = $client->getError();
        if ($err) {
            $outPut = $err;
        }
        $result = $client->call('bpVerifyRequest', $parameters, $this->nameSpace);
        if ($client->fault) {
            $outPut = $result;
        } else {
            $resultStr = $result;
            $err = $client->getError();
            if ($err) {
                $outPut = $err;
            } else if( $resultStr === '0' ) {

                //  verification is done correctly, request a deposit
                // Call the SOAP method
                $result_client = $client->call('bpSettleRequest', $parameters, $this->nameSpace);

                if ($result_client == 0) {
                    // All payment steps completed correctly.
                    // show out put
                    $data = [
                        'message' => self::error($result),
                        'result' => $result,
                        'result_client' => $result_client,
                        'result_client_message' => self::error($result_client),
                        'verifySaleOrderId' => $parameters['SaleOrderId'],
                        'verifySaleReferenceId' => $parameters['SaleReferenceId'],
                        'RefId' => $params['ResCode'],
                    ];
                    $outPut = true;
                }else {
                    //There was a problem in asking for a deposit. Request a refund.
                    $client->call('bpReversalRequest', $parameters, $this->nameSpace);
                    $data = [
                        'message' => self::error($result),
                        'result' => $result,
                        'result_client' => $result_client,
                        'result_client_message' => self::error($result_client),
                        'verifySaleOrderId' => $parameters['SaleOrderId'],
                        'verifySaleReferenceId' => $parameters['SaleReferenceId'],
                        'RefId' => $params['RefId'],
                        'ResCode' => $params['ResCode'],
                    ];
                }


            }
        }
        return $outPut;
    }

    /**
     * @param $params
     */
    public function refundAmount($params)
    {
        $client = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' ) ;
        $parameters = $this->prepareParameters($params);
        $client->call('bpReversalRequest', $parameters, $this->nameSpace);
    }
    /**
     * @param $params
     * @return array|bool
     */
    public function checkPayment($params)
    {
        if(($params['ResCode'] === '0') && $this->verifyPayment($params) === true) {
            return [
                'status' => 'success',
                'trans' =>$params['SaleReferenceId']
            ];
        }
        return false;
    }

    /**
     * @param $number
     * @return string
     */
    public function error($number): string
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
            case 31 :
                $err = 'پاسخ نامعتبر است!';
                break;
            case 17 :
                $err = 'کاربر از انجام تراکنش منصرف شده است!';
                break;
            case 21 :
                $err = 'پذیرنده نامعتبر است!';
                break;
            case 25 :
                $err = 'مبلغ نامعتبر است!';
                break;
            case 34 :
                $err = 'خطای سیستمی!';
                break;
            case 41 :
                $err = 'شماره درخواست تکراری است!';
                break;
            case 421 :
                $err = 'ای پی نامعتبر است!';
                break;
            case 412 :
                $err = 'شناسه قبض نادرست است!';
                break;
            case 45 :
                $err = 'تراکنش از قبل ستل شده است';
                break;
            case 46 :
                $err = 'تراکنش ستل شده است';
                break;
            case 35 :
                $err = 'تاریخ نامعتبر است';
                break;
            case 32 :
                $err = 'فرمت اطلاعات وارد شده صحیح نمیباشد';
                break;
            case 43 :
                $err = 'درخواست verify قبلا صادر شده است';
                break;
            default  :
                $err = 'خطای نا مشخص';

        }
        return $err;
    }

}
