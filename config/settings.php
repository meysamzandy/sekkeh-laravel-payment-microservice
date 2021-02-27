<?php

return [

    // token for admin APIs
    'admin_jwt' =>[
        'key' => '$QYE&^ZtuDh8G6juBdipn6#8@Tx5UdEoZ!T3hkND2XYQ',
        'password' => 'iz7cV@gU@R7cTCFH$BcWTG',
    ],

    // dakkeh key
    'dakkeh_jwt' =>[
        'key' => 'Pg)mg/E>H`e49bC)5#f$>tbgZTv.29eDp}.dJPH%',
        'callback_url' => 'http://192.168.81.160'
    ],
    //sekkeh
    'gishe' => [
        'key' => 't3GdT78H8L8*V%4J%5B!cSS797u',
        'callback_url' => 'http://192.168.81.70'
    ],

    //event1400
    'event1400' => [
        'key' => 'df4%6dT78HdL8*V%4J%5B!sckj79dg',
        'callback_url' => 'http://192.168.90.10',
        'redirect_url' => 'http://play.zing.school/payment/result'
    ],

    // saman gateway
    'saman' =>[
        'merchant' => '11812985',
        'password' => '3979324',
        'callback' => env('APP_URL', 'https://sekkeh.filmgardi.com') . '/api/payment/saman/callback'
    ],

    // mellat gateway
    'mellat' =>[
        'terminal' => '5370638',
        'username' => 'filmgardi12',
        'password' => '53711180',
        'callback' => env('APP_URL', 'https://sekkeh.filmgardi.com') . '/api/payment/mellat/callback'
    ],

    'redirect_url' => 'https://new.filmgardi.com/payment/result',

    'kafka_ip' => '192.168.99.11:9092,192.168.99.12:9092,192.168.99.13:9092'



];
