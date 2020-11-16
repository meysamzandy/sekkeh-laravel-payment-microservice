<?php

namespace Tests\Feature;

use App\Http\Controllers\TransactionLogController;
use App\Http\Helper\JwtHelper;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TransactionLogControllerTest extends TestCase
{
    public const REQUEST_URL = 'api/payment/request';
    public const LIST_TRANSACTIONS = 'api/admin/sekkeh/transactions';

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:refresh --seed --seeder=DatabaseSeeder');
    }

    public function testList()
    {
        // check if wrong url
        $url = 'self::LIST_TRANSACTIONS';
        $response = $this->get($url);
        $response->assertStatus(404);

        // check if token doesn't exist
        $url = self::LIST_TRANSACTIONS;
        $response = $this->get($url);
        $response->assertStatus(403);

        // check list without params
        $url = self::LIST_TRANSACTIONS;
        $this->withoutMiddleware();
        $request = $this->get($url);
        $request->assertStatus(200);
        $request->assertExactJson(json_decode($request->getContent(), true));
        $data = json_decode($request->getContent(), true);
        self::assertEquals(1, $data['body']['current_page']);
        self::assertEquals(20, $data['body']['total']);

        // check list with page and limit params
        $url = self::LIST_TRANSACTIONS . '?page=1&limit=1';
        $this->withoutMiddleware();
        $request = $this->get($url);
        $request->assertStatus(200);
        $request->assertExactJson(json_decode($request->getContent(), true));
        $data = json_decode($request->getContent(), true);
        self::assertEquals(1, $data['body']['current_page']);
        self::assertEquals(20, $data['body']['last_page']);
        self::assertEquals(20, $data['body']['total']);

        // check list with page and limit params
        $url = self::LIST_TRANSACTIONS . '?page=1&limit=10&id=1&op_id==';
        $this->withoutMiddleware();
        $request = $this->get($url);
        $request->assertStatus(200);
        $request->assertExactJson(json_decode($request->getContent(), true));
        $data = json_decode($request->getContent(), true);
        self::assertEquals(1, $data['body']['data'][0]['id']);
        self::assertEquals(1, $data['body']['current_page']);
        self::assertEquals(1, $data['body']['last_page']);
        self::assertEquals(1, $data['body']['total']);

    }


    public function testPaymentRequest()
    {
        // check if wrong url
        $url = 'self::REQUEST_URL';
        $response = $this->post($url);
        $response->assertStatus(404);

        // check if data doesn't exist
        $url = self::REQUEST_URL;
        $response = $this->post($url);
        $response->assertStatus(400);

        // check if gateway doesn't exist
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 193,
            'finalPrice' => 10000,
            'src' => 'dakkeh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(400);

        // check if token doesn't exist
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $data = [
            'gateway' => 'saman',
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(400);


        // check if data in token us not valid
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 'ddddd',
            'finalPrice' => 10000,
            'src' => 'dakkeh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'gateway' => 'saman',
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(400);

        // check if  token us not valid
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();$data = [
            'gateway' => 'saman',
            'token' => 'ddddd',
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(403);

        // check if data in token us not valid
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 'ddddd',
            'finalPrice' => 10000,
            'src' => 'dakkeh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'gateway' => 'saman',
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(400);

        // check if gateway us not valid
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 192,
            'finalPrice' => 10000,
            'src' => 'dakkseh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'gateway' => 'saman',
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(400);

        // check if gateway us not valid
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 192,
            'finalPrice' => 10000,
            'src' => 'dakkeh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'gateway' => 'samsan',
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertStatus(400);

        // every thing is ok
        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 193,
            'finalPrice' => 10000,
            'src' => 'dakkeh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'gateway' => 'saman',
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertSessionHasNoErrors();

        $url = self::REQUEST_URL;
        $this->withoutMiddleware();
        $tokenData = [
            'factorId' => 193,
            'finalPrice' => 10000,
            'src' => 'dakkeh',
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.dakkeh_jwt.key'), $tokenData, 360000) ;
        $data = [
            'gateway' => 'mellat',
            'token' => $jwt,
        ];
        $request = $this->post($url,$data);
        $request->assertSessionHasNoErrors();
    }




}
