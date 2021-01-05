<?php


namespace App\Http\Helper;


use App\Models\TransactionLog;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Kafka\Producer;
use Kafka\ProducerConfig;

class Kafka
{
    public static function SyncNewTransactionToKafka($transaction,$calBackData = null) {

        $data ['transaction'] = $transaction;
        try {
            $config = ProducerConfig::getInstance();
            $config->setMetadataRefreshIntervalMs(10000);
            $config->setMetadataBrokerList(config('settings.kafka_ip'));
            $config->setBrokerVersion('1.0.0');
            $config->setRequiredAck(1);
            $config->setIsAsyn(FALSE);
            $config->setProduceInterval(500);

            $producer = new Producer();
            $response = $producer->send([
                [
                    'topic' => 'sekkeh',
                    'value' => json_encode($data),
                ],
            ]);
        } catch (Exception $e) {
            $Url = null ;
            $option = null;
            if ($transaction['source'] === 'dakkeh') {
                $Url = config('settings.dakkeh_jwt.callback_url') .'/api/v1/factor/update/'.$transaction['sales_id'];
                $option = [
                    'json' => [
                        "sekkehId" => $transaction['payment_id'],
                        "transactionId" => $transaction['transaction_id'],
                        "gateway" => $transaction['final_gateway'],
                        "factorStatus" =>$transaction['status'],
                        "source" =>"api"
                    ],
                    'header' =>[
                        'Authorization' => JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'),['noting'],60)
                    ]
                ];
            }
            if ($transaction['source'] === 'gishe') {
                $Url = config('settings.gishe.callback_url') .'/gishe/api/v1/factor/'.$transaction['sales_id'].'/callback';
                $option = [
                    'json' => [
                        "sekkehId" => $transaction['payment_id'],
                        "transactionId" => $transaction['transaction_id'],
                        "gateway" => $transaction['final_gateway'],
                        "factorStatus" =>$transaction['status'],
                        "source" =>"api"
                    ],
                    'headers' =>[
                        'token' => JwtHelper::encodeJwt(config('settings.gishe.key'),['noting'],60)
                    ]
                ];
            }
            try {
                $client = new Client();
                $request = $client->put($Url, $option);
                $response = json_decode($request->getBody()->getContents());
            } catch (GuzzleException $e) {
                if ($calBackData) {
                    // set failed payment status
                    $transactionInfo = TransactionLog::query()->findOrFail($transaction['payment_id']);
                    $transactionInfo->update(['status'=>'failed', 'error_message'=> $e->getMessage()]);
                    // refund user payment amount
                    if ($transaction['final_gateway'] === 'mellat') {
                        $mellat = (New MellatGateway(config('settings.mellat.terminal'),config('settings.mellat.username'),config('settings.mellat.password')));
                        $mellat->refundAmount($calBackData);
                    }
                    if ($transaction['final_gateway'] === 'saman') {
                        $saman = (New SamanGateway(config('settings.saman.merchant'),config('settings.saman.password')));
                        $saman->refundAmount($calBackData);
                    }
                }
                return $e->getCode();
            }
        }

        return $response;
    }
}
