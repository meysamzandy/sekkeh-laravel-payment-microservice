<?php

namespace Tests\Unit;

use App\Http\Helper\MellatGateway;
use Mockery;
use Tests\TestCase;;

class MellatGatewayTest extends TestCase
{

    public function testPrepareParameters()
    {
        $class = new MellatGateway();
        $params = [
            "RefId" => "D9EEDE4B22DFC0B5",
            "ResCode" => "0",
            "SaleOrderId" => "8",
            "SaleReferenceId" => "168418081238",
            "CardHolderInfo" => "846F213A7FF5A47A488E8521836F53B6AACF3DC6370ADA47A326F1C68134AD46",
            "CardHolderPan" => "621986******3922",
            "FinalAmount" => "10000"
        ];
        $prepareParameters = $class->prepareParameters($params);
        self::assertEquals('8',$prepareParameters['orderId']);
        self::assertEquals('8',$prepareParameters['saleOrderId']);
        self::assertEquals('168418081238',$prepareParameters['saleReferenceId']);
    }

    public function testError()
    {
        $class = new MellatGateway();
        $error = $class->error(31);
        self::assertEquals('پاسخ نامعتبر است!',$error);

        $error = $class->error(17);
        self::assertEquals('کاربر از انجام تراکنش منصرف شده است!',$error);

        $error = $class->error(21);
        self::assertEquals('پذیرنده نامعتبر است!',$error);

        $error = $class->error(25);
        self::assertEquals('مبلغ نامعتبر است!',$error);

        $error = $class->error(421);
        self::assertEquals('ای پی نامعتبر است!',$error);

        $error = $class->error(412);
        self::assertEquals('شناسه قبض نادرست است!',$error);

        $error = $class->error(45);
        self::assertEquals('تراکنش از قبل ستل شده است',$error);

        $error = $class->error(46);
        self::assertEquals('تراکنش ستل شده است',$error);

        $error = $class->error(35);
        self::assertEquals('تاریخ نامعتبر است',$error);


        $error = $class->error(32);
        self::assertEquals('فرمت اطلاعات وارد شده صحیح نمیباشد',$error);


        $error = $class->error(34);
        self::assertEquals('خطای سیستمی!',$error);

        $error = $class->error(41);
        self::assertEquals('شماره درخواست تکراری است!',$error);


        $error = $class->error(43);
        self::assertEquals('درخواست verify قبلا صادر شده است',$error);


        $error = $class->error(1);
        self::assertEquals("خطای نا مشخص",$error);

    }
}
