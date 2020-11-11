<?php


namespace App\Http\Helper;


use Exception;
use Nowakowskir\JWT\Exceptions\EmptyTokenException;
use Nowakowskir\JWT\Exceptions\IntegrityViolationException;
use Nowakowskir\JWT\Exceptions\TokenExpiredException;
use Nowakowskir\JWT\Exceptions\TokenInactiveException;
use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenDecoded;
use Nowakowskir\JWT\TokenEncoded;

class JwtHelper
{
    public const RESULT_STATUS = 'result_status';
    public const RESULT = 'result';
    public const BODY = 'body';

    /**
     * @param $key
     * @param $data
     * @param int $expireTime
     *
     * @return string
     */
    public static function encodeJwt($key,$data, int $expireTime): string {
        $header = ['alg' => JWT::ALGORITHM_HS512];
        $payload = [
            self::BODY => $data,
            'exp' => time() + (1 * 1 * $expireTime * 60),
        ];
        $tokenDecoded = new TokenDecoded($payload, $header);
        $tokenEncoded = $tokenDecoded->encode($key,JWT::ALGORITHM_HS512);
        return $tokenEncoded->toString();
    }


    /**
     * @param $key
     * @param null $tokenString
     *
     * @return array|string
     */
    public static function decodeJwt($key ,$tokenString = NULL) {
        try {
            $tokenEncoded = new TokenEncoded($tokenString);
            try {
                $tokenEncoded->validate($key,JWT::ALGORITHM_HS512);
                $outPut = [
                    self::RESULT_STATUS => true,
                    self::RESULT => $tokenEncoded->decode()->getPayload(),
                ];
            } catch (IntegrityViolationException $e) {
                // Token is not trusted
                $outPut = [
                    self::RESULT_STATUS => false,
                    self::RESULT => $e->getMessage(),
                ];
            } catch (TokenExpiredException $e) {
                // Token expired (exp date reached)
                $outPut = [
                    self::RESULT_STATUS => false,
                    self::RESULT => $e->getMessage(),
                ];
            } catch (TokenInactiveException $e) {
                // Token is not yet active (nbf date not reached)
                $outPut = [
                    self::RESULT_STATUS => false,
                    self::RESULT => $e->getMessage(),
                ];
            } catch (Exception $e) {
                // Something else gone wrong
                $outPut = [
                    self::RESULT_STATUS => false,
                    self::RESULT => $e->getMessage(),
                ];
            }

        } catch (EmptyTokenException $e) {
            $outPut = [
                self::RESULT_STATUS => false,
                self::RESULT => $e->getMessage(),
            ];
        } catch (Exception $e) {
            $outPut = [
                self::RESULT_STATUS => false,
                self::RESULT => $e->getMessage(),
            ];
        }

        return $outPut;
    }
}
