<?php


namespace App\Http\Helper;


use Illuminate\Support\Facades\Validator;

class ValidatorHelper
{
    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function dataTokenValidator($data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data,
            [
                'factorId' => 'required|numeric',
                'finalPrice' => 'required|numeric',
                'src' => 'required|in:dakkeh,gishe',
            ]
            , [
                'required' => __('messages.required'),
                'numeric' => __('messages.numeric'),
                'in' => __('messages.in'),

            ]);
    }
    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function dataValidator($data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data,
            [
                'gateway' => 'required|in:saman,mellat',
            ]
            , [
                'required' => __('messages.required'),
                'in' => __('messages.in'),

            ]);
    }

    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function forceGatewayValidator($data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data,
            [
                'source' => 'required|in:dakkeh,gishe|unique:force_gateways',
                'gateway' => 'required|in:saman,mellat',
            ]
            , [
                'required' => __('messages.required'),
                'in' => __('messages.in'),
                'unique' => __('messages.unique'),

            ]);
    }
}
