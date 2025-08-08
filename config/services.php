<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('WEBHOOK_SECRET')
    ],

    'privateMail' => [
        'host' => env('PRIVATE_MAIL_HOST'),
        'port' => env('PRIVATE_MAIL_PORT'),
        'username' => env('PRIVATE_MAIL_USERNAME'),
        'password' => env('PRIVATE_MAIL_PASSWORD'),
        'encryption' => env('PRIVATE_MAIL_ENCRYPTION'),
    ],

    'frontend' => [
        'url' => env('FRONTEND_URL'),
    ],

    'backend' => [
        'url' => env('APP_URL'),
    ],

    'pusher' => [
        'APP_ID' => env('PUSHER_APP_ID'),
        'APP_KEY' => env('PUSHER_APP_KEY'),
        'APP_SECRET' => env('PUSHER_APP_SECRET'),
        'APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
    ],
    'global_data' => [
        'course_id' => env('SHOWCASE_CID'),
    ],
    'etherscan' => [
        'API_KEY' => env('ETHERSCAN_API_KEY'),
        'URL' => env('ETHERSCAN_API_URL')
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
];
