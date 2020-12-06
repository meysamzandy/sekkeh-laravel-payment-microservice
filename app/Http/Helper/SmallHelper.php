<?php


namespace App\Http\Helper;

use Exception;
use Illuminate\Http\Request;

class SmallHelper
{
    public const RESULT_STATUS = 'resultStats';
    public const STATUS_CODE = 'statusCode';
    public const BODY = 'body';
    public const MESSAGE = 'message';



    /**
     * @param bool $resultStatus
     * @param int $statusCode
     * @param null $body
     * @param null $message
     * @return array
     */
    public static function returnStatus(bool $resultStatus, int $statusCode, $body = null, $message = null): array
    {
        return [
            self::RESULT_STATUS => $resultStatus,
            self::STATUS_CODE => $statusCode,
            self::BODY => $body,
            self::MESSAGE => $message
        ];
    }




    /**
     * @param Request $request
     * @return array
     */
    public static function paginationParams(Request $request): array
    {
        $limit = 20;
        $page = (int)$request->input('page') ?: 1;

        if ($request->input('limit')) {
            $limit = $request->input('limit') <= 500 ? (int)$request->input('limit') : 500;
        }
        return array($page, $limit);
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function orderParams(Request $request): array
    {
        $orderColumn = $request->input('orderColumn') ?: 'created_at';
        $orderBy = $request->input('orderBy') ?: 'desc';
        return array($orderColumn, $orderBy);
    }

    /**
     * @param $requestParams
     * @param $query
     * @param Request $request
     * @param $page
     * @param $limit
     * @param $orderColumn
     * @param $orderBy
     * @return array
     */
    public static function fetchList($requestParams, $query, Request $request, $page, $limit, $orderColumn, $orderBy): ?array
    {
        $result = Filter::getData($requestParams, $request, $query, $page, $limit, $orderColumn, $orderBy);
        if (count($result) <= 0) {
            return [
                self::RESULT_STATUS => false,
                self::STATUS_CODE => 200,
                self::BODY => null,
                self::MESSAGE => null,
            ];
        }
        return [
            self::RESULT_STATUS => true,
            self::STATUS_CODE => 200,
            self::BODY => $result,
            self::MESSAGE => null,
        ];

    }
}