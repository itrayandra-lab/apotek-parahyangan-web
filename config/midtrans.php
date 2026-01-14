<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    */

    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),

    'api_url' => env('MIDTRANS_ENVIRONMENT', 'sandbox') === 'production'
        ? 'https://api.midtrans.com'
        : 'https://api.sandbox.midtrans.com',

    'snap_url' => env('MIDTRANS_ENVIRONMENT', 'sandbox') === 'production'
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    'snap' => [
        'enabled_payments' => [
            'credit_card',
            'bca_va',
            'bni_va',
            'bri_va',
            'permata_va',
            'other_va',
            'gopay',
            'shopeepay',
            'qris',
            'cimb_clicks',
            'bca_klikbca',
            'bca_klikpay',
            'bri_epay',
            'echannel',
            'mandiri_clickpay',
            'other_qris',
            'danamon_online',
            'akulaku',
            'alfamart',
            'indomaret',
        ],
        'credit_card' => [
            'secure' => true,
            'bank' => 'bca',
            'installment' => [
                'required' => false,
                'terms' => [
                    'bni' => [3, 6, 12],
                    'mandiri' => [3, 6, 12],
                    'cimb' => [3],
                    'bca' => [3, 6, 12],
                    'mega' => [3, 6, 12],
                ],
            ],
        ],
        'custom_expiry' => [
            'unit' => 'hour',
            'duration' => 24,
        ],
    ],

    'notification_url' => env('MIDTRANS_NOTIFICATION_URL'),
    'finish_url' => env('MIDTRANS_FINISH_URL'),
    'unfinish_url' => env('MIDTRANS_UNFINISH_URL'),
    'error_url' => env('MIDTRANS_ERROR_URL'),

    'sanitize' => true,
    'enable_3ds' => true,
];
