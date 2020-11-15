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
            // todo replace with new Producer
            $config->setMetadataBrokerList('192.168.97.1:9092,192.168.97.2:9092,192.168.97.3:9092');
            $config->setBrokerVersion('1.0.0');
            $config->setRequiredAck(1);
            $config->setIsAsyn(FALSE);
            $config->setProduceInterval(500);

            $producer = new Producer();
            $response = $producer->send([
                [
                    // todo replace with new topic
                    'topic' => 'cinemaa',
                    'value' => json_encode($data),
                ],
            ]);
        } catch (Exception $e) {
            $Url = null ;
            if ($transaction['source'] === 'dakkeh') {
                // todo replace with dakeh
                $Url = 'http://127.0.0.1/cinesma/store';
            }
            if ($transaction['source'] === 'gisheh') {
                // todo replace with dakeh
                $Url = 'http://127.0.0.1/cinessma/store';
            }
            try {
                $client = new Client();
                $request = $client->post($Url, ['json' => $data]);
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
