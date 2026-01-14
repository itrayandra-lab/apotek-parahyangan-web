<?php

return [
    'api_key' => env('RAJAONGKIR_API_KEY', ''),
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
    'timeout' => env('RAJAONGKIR_TIMEOUT', 30),

    'origin' => [
        'city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID'),
        'city_name' => env('RAJAONGKIR_ORIGIN_CITY_NAME', 'BANDUNG'),
    ],

    'couriers' => [
        'jne' => 'JNE',
        'sicepat' => 'SiCepat',
        'jnt' => 'J&T Express',
        'ninja' => 'Ninja Xpress',
        'anteraja' => 'AnterAja',
        'pos' => 'POS Indonesia',
    ],

    'default_weight' => 200,
];
