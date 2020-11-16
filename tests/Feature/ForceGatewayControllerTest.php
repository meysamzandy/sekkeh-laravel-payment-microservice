<?php

namespace Tests\Feature;

use App\Http\Controllers\ForceGatewayController;
use App\Http\Helper\JwtHelper;
use App\Models\ForceGateway;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ForceGatewayControllerTest extends TestCase
{
    public const LIST_FORCE_GATEWAYS= 'api/admin/sekkeh/forceGateways';
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function testStore()
    {
        // check if wrong url
        $url = 'self::LIST_FORCE_GATEWAYS';
        $response = $this->post($url);
        $response->assertStatus(404);

        // check if token doesn't exist
        $url = self::LIST_FORCE_GATEWAYS;
        $response = $this->post($url);
        $response->assertStatus(403);

        // check data validations
        $url = self::LIST_FORCE_GATEWAYS;
        $this->withoutMiddleware();
        $data = [

        ];
        $response = $this->post($url, $data);
        $response->assertStatus(400);

        // check  array
        $url = self::LIST_FORCE_GATEWAYS;
        $this->withoutMiddleware();
        $data = [
            //code group
            'source' => 'manualtest',
            'gateway' => '',

        ];
        $response = $this->post($url, $data);
        $response->assertStatus(400);

        $this->assertDatabaseCount('force_gateways', 0);

        // check  array
        $url = self::LIST_FORCE_GATEWAYS;
        $this->withoutMiddleware();
        $data = [
            //code group
            'source' => 'dakkeh',
            'gateway' => 'saman',

        ];
        $response = $this->post($url, $data);
        $response->assertStatus(201);

        $this->assertDatabaseCount('force_gateways', 1);

    }

    public function testList()
    {
        Artisan::call('migrate:refresh --seed --seeder=DatabaseSeeder');
// check if wrong url
        $url = 'self::LIST_FORCE_GATEWAYS';
        $response = $this->get($url);
        $response->assertStatus(404);

        // check if token doesn't exist
        $url = self::LIST_FORCE_GATEWAYS;
        $response = $this->get($url);
        $response->assertStatus(403);

        // check list without params
        $url = self::LIST_FORCE_GATEWAYS;
        $this->withoutMiddleware();
        $request = $this->get($url);
        $request->assertStatus(200);
        $request->assertExactJson(json_decode($request->getContent(), true));
        $data = json_decode($request->getContent(), true);
        self::assertEquals(1, $data['body']['current_page']);
        self::assertEquals(2, $data['body']['total']);

        // check list with page and limit params
        $url = self::LIST_FORCE_GATEWAYS . '?page=1&limit=1';
        $this->withoutMiddleware();
        $request = $this->get($url);
        $request->assertStatus(200);
        $request->assertExactJson(json_decode($request->getContent(), true));
        $data = json_decode($request->getContent(), true);
        self::assertEquals(1, $data['body']['current_page']);
        self::assertEquals(2, $data['body']['last_page']);
        self::assertEquals(2, $data['body']['total']);

        // check list with page and limit params
        $url = self::LIST_FORCE_GATEWAYS . '?page=1&limit=10&id=1&op_id==';
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

    public function testDestroy(): void
    {
        Artisan::call('migrate:refresh --seed --seeder=DatabaseSeeder');
        $group = ForceGateway::all();
        // check if wrong url
        $url = 'self::LIST_FORCE_GATEWAYS';
        $response = $this->delete($url);
        $response->assertStatus(404);

        // check if token doesn't exist
        $url = self::LIST_FORCE_GATEWAYS. '/' . $group[0]['id'];
        $response = $this->delete($url);
        $response->assertStatus(403);


        // delete group correctly
        $url = self::LIST_FORCE_GATEWAYS. '/' . $group[0]['id'];
        $data = [
            'password' => config('settings.admin_jwt.password')
        ];
        $jwt = JwtHelper::encodeJwt(config('settings.admin_jwt.key'), $data, 360000);
        $response = $this->delete($url, [], ['token' => $jwt]);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('force_gateways', [
            'id' =>  $group[0]['id']
        ]);
    }
}
