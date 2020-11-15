<?php

namespace App\Http\Controllers;

use App\Http\Helper\SmallHelper;
use App\Models\ForceGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForceGatewayController extends Controller
{
        public const RESULT_STATUS = 'resultStats';
        public const BODY = 'body';
        public const MESSAGE = 'message';
        public const STATUS_CODE = 'statusCode';
    public function list(Request $request): JsonResponse
    {
        [$page, $limit] = SmallHelper::paginationParams($request);
        // get query params
        [$orderColumn, $orderBy] = SmallHelper::orderParams($request);
        $requestParams = (new ForceGateway())->getParams();
        $query = ForceGateway::query();
        $data = SmallHelper::fetchList($requestParams, $query, $request, $page, $limit, $orderColumn, $orderBy);

        return response()->json([self::BODY => $data[self::BODY], self::MESSAGE => $data[self::MESSAGE]])->setStatusCode($data[self::STATUS_CODE]);
    }
}
