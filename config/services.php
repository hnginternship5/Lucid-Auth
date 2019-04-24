<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id'     => '2207275122694029',
        'client_secret' => '3d51e272768d53f8f16d0bcb2e7bbd64',
        'redirect'      => 'https://auth.techteel.com/login/facebook/callback',
    ],

    'google' => [
        'client_id'     => '302599861511-epoto9bjfkd5cvkk0450d3r1qu7g0pgd.apps.googleusercontent.com',
        'client_secret' => 'fllMCVykG9bDIwRGnbL8SB3x',
        'redirect'      => 'https://auth.techteel.com/login/google/callback',
    ],

];
