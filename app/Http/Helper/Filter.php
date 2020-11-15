<?php

namespace App\Http\Helper;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class Filter
{

    /**
     * @param array $inputParams
     * @param Request $request
     * @param Builder $query
     * @param $page
     * @param $limit
     * @param string $orderColumn
     * @param string $orderBy
     * @return array|LengthAwarePaginator
     */
    public static function getData(array $inputParams, Request $request, Builder $query, $page, $limit, string $orderColumn, string $orderBy): LengthAwarePaginator
    {
        
        $filterParams = [];
        $filterBetweenParams = [];
        foreach ($inputParams as $params) {
            if (!$request->has($params)) {
                continue;
            }
            if (!$request->has('op_'.$params)) {
                continue;
            }
            $operator = $request->input('op_'.$params);
            if (!$operator) {
                continue;
            }
            $value = $request->input($params);
            if (!$value && $value !== "0") {
                continue;
            }
            if ($operator === 'between') {
                $filterBetweenParams[] = [$params, $value];
                continue;
            }
            $newValue = str_replace("@", '%', $value);
            $filterParams[] = [$params, $operator, $newValue];

        }
        $query->where($filterParams);
        if (count($filterBetweenParams) > 0) {
            foreach ($filterBetweenParams as $filterBetweenParam) {
                $query->whereBetween($filterBetweenParam[0], explode(",", $filterBetweenParam[1]));
            }
        }
        $query->orderBy($orderColumn, $orderBy);
        return $query->orderBy($orderColumn, $orderBy)->paginate($limit,['*'],'page',$page);
    }

}
