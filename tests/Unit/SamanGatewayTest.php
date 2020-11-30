<?php

namespace Tests\Unit;

use App\Http\Helper\SamanGateway;
use Tests\TestCase;;

class SamanGatewayTest extends TestCase
{

    public function testError()
    {
        $class = new SamanGateway() ;

        $error = $class->error('0');
        self::assertEquals(" (0) :  ﺗﺮﺍﻛﻨﺶ ﺑﺎ ﻣﻮﻓﻘﻴﺖ ﺍﻧﺠﺎﻡ ﺷﺪ",$error);

        $error = $class->error('-1');
        self::assertEquals(' (-1) : خطا درپردازش اطلاعات ارسالی',$error);

        $error = $class->error('-3');
        self::assertEquals(' (-3) : ورودی حاوی کارکتر غیرمجاز',$error);

        $error = $class->error('-4');
        self::assertEquals(' (-4) : کلمه عبور یا کد فروشنده اشتباه است.',$error);

        $error = $class->error('-6');
        self::assertEquals(' (-6) : سند قبلا برگشت کامل یافته است.',$error);

        $error = $class->error('-7');
        self::assertEquals(' (-7) : رسید دیجیتال خالی است.',$error);

        $error = $class->error('-8');
        self::assertEquals(' (-8) : طول ورودی بیشتر از حد مجاز است',$error);

        $error = $class->error('-9');
        self::assertEquals(' (-9) : وجود کاراکترهای غیرمجاز در مبلغ بازگشتی',$error);

        $error = $class->error('-10');
        self::assertEquals(' (-10) : رسید دیجیتال بصورت Base64 نیست.',$error);

        $error = $class->error('-11');
        self::assertEquals(' (-11) : طول ورودی ها کمتر از حد مجاز است.',$error);

        $error = $class->error('-12');
        self::assertEquals(' (-12) : مبلغ برگشتی منفی است.',$error);

        $error = $class->error('-13');
        self::assertEquals(' (-13) : مبلغ برگشتی برای برگشت جزئی بیش از مبلغ برگشت خورده ی رسید دیجیتال است.',$error);

        $error = $class->error('-14');
        self::assertEquals(' (-14) : چنین تراکنشی تعریف نشده است.',$error);

        $error = $class->error('-15');
        self::assertEquals(' (-15) : مبلغ برگشتی بصورت اعشاری داده شده است.',$error);

        $error = $class->error('-16');
        self::assertEquals(' (-16) : خطای داخلی سیستم',$error);

        $error = $class->error('-17');
        self::assertEquals(' (-17) : برگشت زدن جزوی تراکنش مجاز نمیباشد.',$error);

        $error = $class->error('-18');
        self::assertEquals(' (-18) : ای پی سرور فروشنده نامعتبر است.',$error);

        $error = $class->error('-20');
        self::assertEquals(' (-20) : خطاي نامشخص.',$error);
    }
}
