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
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'google' => [
      	'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL').'/auth/google/callback',
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT'),
    ],
	'mail' => [
        'mail_driver' => env('MAIL_DRIVER', 'smtp'),
        'mail_host' => env('MAIL_HOST', 'smtp.zeptomail.com.au'),
        'mail_port' => env('MAIL_PORT', 587),
        'mail_encryption' => env('MAIL_ENCRYPTION', 'TLS'),
        'mail_username' => env('MAIL_USERNAME'),
        'mail_password' => env('MAIL_PASSWORD'),
        'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@justreused.com'),
        'mail_from_name' => env('MAIL_FROM_NAME', 'Justreused!'),
    ],
];
