<?php

namespace Tests\Unit;


use Tests\TestCase;
use App\Http\Helper\JwtHelper;

class JwtHelperTest extends TestCase
{

    public function testDecodeJwt(): void
    {
        $data = [
            'fake' => ' fake',
        ];
        $token = JwtHelper::encodeJwt(config('settings.admin_jwt.key'), $data, 5);
        self::assertIsString($token);

    }

    public function testEncodeJwt(): void
    {
        $token = 'eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJib2R5Ijp7ImZha2UiOiIgZmFrZSJ9LCJleHAiOjE1OTU5MjI4Mjl9.5L2pVBqSWS-yEMs3TGDdDu0RW1rBcbVyPCSXb5t6Bh2WZkVJWuodX6v3MIQ07Tk2vTWqeicLXwIVgl4PjNcBMA';
        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), $token);
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);


        // Invalid header
        $token = 'eyJhbGciOiJIUzUxMiR5cCI6IkpXVCJ9.eyJib2R5Ijp7ImZha2UiOiIgZmFrZSJ9LCJleHAiOjE1OTU5MjI4Mjl9.5L2pVBqSWS-yEMs3TGDdDu0RW1rBcbVyPCSXb5t6Bh2WZkVJWuodX6v3MIQ07Tk2vTWqeicLXwIVgl4PjNcBMA';
        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), $token);
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);
        self::assertEquals('Invalid header', $data['result']);


        // Invalid payload
        $token = 'eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJib2R5Ijp7ImZha2UiOiIgZmFrZSJ9LCJleHAiOjE2MA4NDA3NTJ9.P6x2rONFlTOiPN9gfW-rfTq227sDfYNZG5YkMiRfRRVtbf_tmFGO5NhZ3XVorxPfVdYJOa9nqKe2S4_v84cchQ';
        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), $token);
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);
        self::assertEquals('Invalid payload', $data['result']);

        // Invalid signature
        $token = 'eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJib2R5Ijp7ImZha2UiOiIgZmFrZSJ9LCJleHAiOjE2MDA4NDA3NTJ9.P6x2rONFlTOiPN9gfW-rfTq227sDfYNZG5YkMiRfRRVtbf_tmFGO5NhZ3XorxPfVdYJOa9nqKe2S4_v84cchQ';
        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), $token);
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);
        self::assertEquals('Invalid signature', $data['result']);


        $data = ['fake' => ' fake',];
        $token = JwtHelper::encodeJwt(config('settings.admin_jwt.key'), $data, 5);
        $data = JwtHelper::decodeJwt('', $token);
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);
        self::assertEquals('Invalid signature', $data['result']);

        // Wrong number of segments

        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), 'sokssen');
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);
        self::assertEquals('Wrong number of segments', $data['result']);

        //Token not provided
        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), '');
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);
        self::assertEquals('Wrong number of segments', $data['result']);

        //Token expired
        $data = ['fake' => ' fake',];
        $token = JwtHelper::encodeJwt(config('settings.admin_jwt.key'), $data, 0);
        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), $token);
        self::assertIsArray($data);
        self::assertFalse($data['result_status']);


        $data = ['fake' => ' fake',];
        $token = JwtHelper::encodeJwt(config('settings.admin_jwt.key'), $data, 5);

        $data = JwtHelper::decodeJwt(config('settings.admin_jwt.key'), $token);
        self::assertIsArray($data);
        self::assertTrue($data['result_status']);
        self::assertArrayHasKey('fake', $data['result']['body']);



    }
}
