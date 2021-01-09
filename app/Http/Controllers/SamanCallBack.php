<?php


namespace App\Http\Controllers;


use App\Http\Helper\Kafka;
use App\Http\Helper\MellatGateway;
use App\Http\Helper\SamanGateway;
use App\Http\Helper\SmallHelper;
use App\Models\TransactionLog;
use Exception;
use Illuminate\Http\Request;

class SamanCallBack
{
    public const RESULT_STATUS = 'resultStats';
    public const BODY = 'body';
    public const MESSAGE = 'message';
    public const STATUS_CODE = 'statusCode';

    public function callBack(Request $request)
    {
        $saman = (New SamanGateway(config('settings.saman.merchant'),config('settings.saman.password')));
        $calBackData = $request->post();


        // check if has ResNum
        if (!$request->filled('ResNum')) {
            SmallHelper::redirectTransactionsResult(__('messages.notFoundResNum'),400);
        }

        $transactions = TransactionLog::query()->findOrFail($calBackData['ResNum']);
        /** @var TransactionLog $transactions */
        if (!$transactions) {
            SmallHelper::redirectTransactionsResult(__('messages.notFound'),404);
        }
        $results = $saman->verifyPayment($calBackData);

        if ($results !== true) {
            $transactions->update(['status'=>'failed']);
            $transactionsData = $this->preferTransactionData($transactions);
            Kafka::SyncNewTransactionToKafka($transactionsData);
            SmallHelper::redirectTransactionsResult($results,400);
        }

        try {
            $transactions->update(['status'=>'success', 'transaction_id'=> $calBackData['TRACENO']]);
            $transactionsData = $this->preferTransactionData($transactions);
            Kafka::SyncNewTransactionToKafka($transactionsData,$calBackData);
            SmallHelper::redirectTransactionsResult(__('messages.success'),200,$calBackData['TRACENO'],$transactions['alias']);
//            return response()->json([self::BODY => null, self::MESSAGE => __('messages.success')])->setStatusCode(200);

        } catch (Exception $e) {
            // set failed payment status
            $transactions->update(['status'=>'failed', 'error_message'=> $e->getMessage()]);
            // refund user payment amount
            $saman->refundAmount($calBackData);
            // send to kafka
            $transactionsData = $this->preferTransactionData($transactions);
            Kafka::SyncNewTransactionToKafka($transactionsData);
            SmallHelper::redirectTransactionsResult(__('messages.exceptionError'),417);
        }
    }

    /**
     * @param TransactionLog $transaction
     * @return array
     */
    protected function preferTransactionData(TransactionLog $transaction): array
    {
        return [
            "payment_id" => $transaction['id'],
            "sales_id" => $transaction['sales_id'],
            "price" => $transaction['price'],
            "source" => $transaction['source'],
            "selected_gateway" => $transaction['selected_gateway'],
            "final_gateway" => $transaction['final_gateway'],
            "status" => $transaction['status'], //  success or failed or init
            "transaction_id" => $transaction['transaction_id'],
        ];
    }

}
