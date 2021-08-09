<?php

return[
    'defaults' => [
      'guard' => env("AUTH_GUARD", "api"),
      'passwords' => 'personen',
    ],
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'personen',
        ],
    ],
    'providers' => [
      'personen' => [
          'driver' => 'eloquent',
          'model' => App\Models\User::class,
      ],
    ],
    'passwords' => [
      'personen' => [
          'provider' => 'personen',
          'table' => 'password_resets',
          'expire' => 60,
      ],
    ],
];
