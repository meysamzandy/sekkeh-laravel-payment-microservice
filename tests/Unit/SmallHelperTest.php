<?php

namespace Tests\Unit;

use App\Http\Helper\SmallHelper;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;

class SmallHelperTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed --class="DatabaseSeeder"');

    }
    public function testReturnStatus(): void
    {
        $return = SmallHelper::returnStatus(true, 200, 'test', 'test message');
        self::assertIsArray($return);
        self::assertArrayHasKey('resultStats', $return);
        self::assertArrayHasKey('statusCode', $return);
        self::assertArrayHasKey('body', $return);
        self::assertArrayHasKey('message', $return);

    }

    public function testPaginationParams(): void
    {
        $request = new Request([], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');
        $pagination = SmallHelper::paginationParams($request);

        self::assertIsArray($pagination);
        // page should be  1
        self::assertEquals(1, $pagination[0]);

        // limit should be  20
        self::assertEquals(20, $pagination[1]);

        /**************/
        $request = new Request(['page' => 5 , 'limit' => 300], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');
        $pagination = SmallHelper::paginationParams($request);

        self::assertIsArray($pagination);
        // page should be Equals 5
        self::assertEquals(5, $pagination[0]);

        // limit should be Equals 300
        self::assertEquals(300, $pagination[1]);

        /**************/
        $request = new Request(['page' => 0 , 'limit' => 501], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');
        $pagination = SmallHelper::paginationParams($request);

        self::assertIsArray($pagination);
        // page should be grader than 0
        self::assertGreaterThanOrEqual(1, $pagination[0]);

        // limit should be less than 501
        self::assertLessThanOrEqual(500, $pagination[1]);

    }

    public function testOrderParams(): void
    {

        $request = new Request([], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');
        $pagination = SmallHelper::orderParams($request);

        self::assertIsArray($pagination);
        // page should be Equals created_at
        self::assertEquals('created_at', $pagination[0]);

        // limit should be Equals desc
        self::assertEquals('desc', $pagination[1]);

    }

    public function testFetchList(): void
    {

        //// has no data
        $request = new Request(['page' => 30 , 'limit' => 300], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');

        [$page, $limit] = SmallHelper::paginationParams($request);
        [$orderColumn, $orderBy] = SmallHelper::orderParams($request);

        $requestParams = (new TransactionLog())->getParams();
        $query = TransactionLog::query();
        $data = SmallHelper::fetchList($requestParams, $query, $request, $page, $limit, $orderColumn, $orderBy);

        self::assertFalse($data['resultStats']);
        self::assertEquals(200,$data['statusCode']);


        //// has data get all without filter
        $request = new Request(['page' => 0 , 'limit' => 300], $_GET, [], [], [], []);
        [$page, $limit] = SmallHelper::paginationParams($request);
        [$orderColumn, $orderBy] = SmallHelper::orderParams($request);

        $requestParams = (new TransactionLog())->getParams();
        self::assertIsArray( $requestParams);
        self::assertEquals(["id","sales_id","price","alias","factor_hash","source","selected_gateway","final_gateway","status","transaction_id","error_message","created_at","updated_at",], $requestParams);

        $query = TransactionLog::query();
        $data = SmallHelper::fetchList($requestParams, $query, $request, $page, $limit, $orderColumn, $orderBy);
        self::assertTrue($data['resultStats']);
        self::assertEquals(200,$data['statusCode']);



        //// has data and get all with filter that has no operation

        $request = new Request(['page' => 0 , 'limit' => 300,'id' => '1,6' ,'op_id' => 'between'], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');

        [$page, $limit] = SmallHelper::paginationParams($request);
        [$orderColumn, $orderBy] = SmallHelper::orderParams($request);

        $requestParams = (new TransactionLog())->getParams();
        $query = TransactionLog::query();
        $data = SmallHelper::fetchList($requestParams, $query, $request, $page, $limit, $orderColumn, $orderBy);

        self::assertTrue($data['resultStats']);
        self::assertEquals(200,$data['statusCode']);



        $request = new Request(['page' => 0 , 'limit' => 300,'final_gateway'=>'@saman@' ,'op_final_gateway'=> 'like'], $_GET, [], [], [], []);
        $request->headers->set('Content-Type', 'application/json');

        [$page, $limit] = SmallHelper::paginationParams($request);
        [$orderColumn, $orderBy] = SmallHelper::orderParams($request);

        $requestParams = (new TransactionLog())->getParams();
        $query = TransactionLog::query();
        $data = SmallHelper::fetchList($requestParams, $query, $request, $page, $limit, $orderColumn, $orderBy);
        self::assertTrue($data['resultStats']);
        self::assertEquals(200,$data['statusCode']);
    }
}
