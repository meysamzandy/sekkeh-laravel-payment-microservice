<?php

namespace Tests\Unit;

use App\Http\Helper\ValidatorHelper;
use App\Models\ForceGateway;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ValidatorHelperTest extends TestCase
{

    public function testDataTokenValidator()
    {
        // empty
        $data = [];
        $result = (new ValidatorHelper())->dataTokenValidator($data);
        self::assertFalse($result->passes());
        // not valid
        $data = [
            'factorId' => 'ssss',
            'finalPrice' => 'ssss',
            'src' => 'ssss',
        ];
        $result = (new ValidatorHelper())->dataTokenValidator($data);
        self::assertFalse($result->passes());
        //valid
        $data = [
            'factorId' => 111,
            'finalPrice' => 111,
            'src' => 'dakkeh',
        ];
        $result = (new ValidatorHelper())->dataTokenValidator($data);
        self::assertTrue($result->passes());

    }

    public function testDataValidator()
    {
        // empty
        $data = [];
        $result =(new ValidatorHelper())->dataValidator($data);
        self::assertFalse($result->passes());
        // not valid
        $data = [
            'gateway' => 'sss'
        ];
        $result =(new ValidatorHelper())->dataValidator($data);
        self::assertFalse($result->passes());
        //valid
        $data = [
            'gateway' => 'saman',
        ];
        $result = (new ValidatorHelper())->dataValidator($data);
        self::assertTrue($result->passes());
    }

    public function testForceGatewayValidator()
    {
        Artisan::call('migrate:fresh');
        // empty
        $data = [];
        $result =(new ValidatorHelper())->forceGatewayValidator($data);
        self::assertFalse($result->passes());
        // not valid
        $data = [
            'source' => 'sss',
            'gateway' => 'sss'
        ];
        $result =(new ValidatorHelper())->forceGatewayValidator($data);
        self::assertFalse($result->passes());


        //valid gateway
        $data = [
            'source' => 'dakkeh',
            'gateway' => 'saman',
        ];
        $result = (new ValidatorHelper())->forceGatewayValidator($data);
        self::assertTrue($result->passes());

        Artisan::call('migrate:refresh --seed --seeder=ForceGatewaySeeder');
        //not unique source
        $data = [
            'source' => 'dakkeh',
            'gateway' => 'saman',
        ];
        $result = (new ValidatorHelper())->forceGatewayValidator($data);
        self::assertFalse($result->passes());
    }
}
