<?php

namespace App\Http\Controllers;

use App\Http\Helper\SmallHelper;
use App\Http\Helper\ValidatorHelper;
use App\Models\ForceGateway;
use Exception;
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


    public function store(Request $request): JsonResponse
    {
        // validate code data
        $validate = (new ValidatorHelper())->forceGatewayValidator($request->post());

        if ($validate->fails()) {
            return response()->json([self::BODY => null, self::MESSAGE => $validate->errors()])->setStatusCode(400);
        }
        $data = $validate->validated();

        $result = (new ForceGateway)->create($data);

        return response()->json([self::BODY => $result[self::BODY], self::MESSAGE => $result[self::MESSAGE]])->setStatusCode(201);

    }

    /**
     * @param ForceGateway $id
     * @return JsonResponse|object
     */
    public function destroy(ForceGateway $id)
    {
        try {

            $id->delete();
            return response()->json([self::BODY => null, self::MESSAGE => __('messages.deletion_successful')])->setStatusCode(204);

        } catch (Exception $e) {

            return response()->json([self::BODY => null, self::MESSAGE => $e->getMessage()])->setStatusCode(417);

        }
    }
}
