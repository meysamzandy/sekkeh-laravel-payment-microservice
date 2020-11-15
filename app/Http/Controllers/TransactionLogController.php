<?php

namespace App\Http\Controllers;

use App\Http\Helper\JwtHelper;
use App\Http\Helper\MellatGateway;
use App\Http\Helper\QuickHelper;
use App\Http\Helper\SamanGateway;
use App\Http\Helper\SimpleHelper;
use App\Http\Helper\SmallHelper;
use App\Http\Helper\ValidatorHelper;
use App\Models\ForceGateway;
use App\Models\TransactionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionLogController extends Controller
{
    public const RESULT_STATUS = 'resultStats';
    public const BODY = 'body';
    public const MESSAGE = 'message';
    public const STATUS_CODE = 'statusCode';

    public function paymentRequest(Request $request)
    {
//        $data = [
//            'factorId' => 193,
//            'finalPrice' => 10000,
//            'src' => 'dakkeh',
//        ];
//        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $data, 360000) ;
//        dd($jwt);

        if (!$request->filled('gateway')) {
            return response()->json([self::BODY => null, self::MESSAGE => __('messages.gatewayValueNotExist')])->setStatusCode(400);
        }
        // if gateway is not valid
        $dataValidator = (new ValidatorHelper)->dataValidator($request->post());
        if ($dataValidator->fails()) {
            return response()->json([self::BODY => null, self::MESSAGE => $dataValidator->errors()])->setStatusCode(400);
        }
        if (!$request->filled('token')) {
            return response()->json([self::BODY => null, self::MESSAGE => __('messages.tokenValueNotExist')])->setStatusCode(400);
        }
        // decode token in data
        $tokenData = JwtHelper::decodeJwt(config('settings.dakkeh_jwt.key'), $request->input('token'));

        // check if token is not valid
        if (!$tokenData['result_status']) {
            return response()->json([self::BODY => null, self::MESSAGE => $tokenData['result']])->setStatusCode(403);
        }
        // if data in token is not valid
        $tokenValidator = (new ValidatorHelper)->dataTokenValidator($tokenData['result']['body']);
        if ($tokenValidator->fails()) {
            return response()->json([self::BODY => null, self::MESSAGE => $tokenValidator->errors()])->setStatusCode(400);
        }
        $final_gateway = ForceGateway::query()->where('source',$tokenValidator->validated()['src'])->first('gateway');


        $data = [
                'sales_id' => $tokenValidator->validated()['factorId'],
                'price' => $tokenValidator->validated()['finalPrice'],
                'source' => $tokenValidator->validated()['src'],
                'selected_gateway' => $dataValidator->validated()['gateway'],
                'final_gateway' => $final_gateway ? $final_gateway['gateway']: $dataValidator->validated()['gateway'],
        ];
        try {
            $insertResult = TransactionLog::query()->create($data);
            if ($data['final_gateway'] === 'mellat') {
                return (New MellatGateway(config('settings.mellat.terminal'),config('settings.mellat.username'),config('settings.mellat.password')))
                    ->startPayment($insertResult['price'],$insertResult['id']);
            }
            if ($data['final_gateway'] === 'saman') {
                return (New SamanGateway(config('settings.saman.merchant'),config('settings.saman.password')))
                    ->startPayment($insertResult['price'],$insertResult['id']);
            }
        } catch (\Exception $e) {
            return response()->json([self::BODY => null, self::MESSAGE => __('messages.public_error') ])->setStatusCode(400);
        }

    }


    public function list(Request $request): JsonResponse
    {
        [$page, $limit] = SmallHelper::paginationParams($request);
        // get query params
        [$orderColumn, $orderBy] = SmallHelper::orderParams($request);
        $requestParams = (new TransactionLog())->getParams();
        $query = TransactionLog::query();
        $data = SmallHelper::fetchList($requestParams, $query, $request, $page, $limit, $orderColumn, $orderBy);

        return response()->json([self::BODY => $data[self::BODY], self::MESSAGE => $data[self::MESSAGE]])->setStatusCode($data[self::STATUS_CODE]);
    }
}
