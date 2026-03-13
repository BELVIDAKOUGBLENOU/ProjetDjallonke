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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'grpc' => [
        // gRPC endpoint via API Gateway (/iam-grpc route)
        'iam' => '127.0.0.1:9090',
    ],

    'iam' => [
        'credentials' => [
            'grant_type' => 'client_credentials',
            'client_id' => env('IAM_CLIENT_ID') ?? "019ce196-0844-71af-8649-d6d2c1b7147b",
            'client_secret' => env('IAM_CLIENT_SECRET') ?? "6nCH1sb0CTQd1SBHGRSK7fX9EJFDbdAz9rV9BtYp",
            'scope' => '',
        ],
    ],

];
