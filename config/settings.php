<?php

return [

    // token for admin APIs
    'admin_jwt' =>[
        'key' => '$QYE&^ZtuDh8G6juBdipn6#8@Tx5UdEoZ!T3hkND2XYQ',
        'password' => 'iz7cV@gU@R7cTCFH$BcWTG',
    ],

    // dakkeh key
    'dakkeh_jwt' =>[
        'key' => 'ujbnjeccb#*apzbxkwj&t%786!&b3hw7q%',
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



];
