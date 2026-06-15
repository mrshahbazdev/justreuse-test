<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */
    'default' => env('MAIL_DRIVER', config('services.mail.mail_driver', 'smtp')),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', config('services.mail.mail_host', 'smtp.zeptomail.com.au')),
            'port' => env('MAIL_PORT', config('services.mail.mail_port', 587)),
            'encryption' => env('MAIL_ENCRYPTION', config('services.mail.mail_encryption', 'TLS')),
            'username' => env('MAIL_USERNAME', config('services.mail.mail_username')),
            'password' => env('MAIL_PASSWORD', config('services.mail.mail_password')),
            'timeout' => null,
            'auth_mode' => null,
        ],

        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],

        'ses' => ['transport' => 'ses'],
        'mailgun' => ['transport' => 'mailgun'],
        'postmark' => ['transport' => 'postmark'],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -bs',
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL', config('services.mail.mail_log_channel')),
        ],

        'array' => ['transport' => 'array'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', config('services.mail.mail_from_address', 'noreply@justreused.com')),
        'name' => env('MAIL_FROM_NAME', config('services.mail.mail_from_name', 'Justreused!')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    */
    'markdown' => [
        'theme' => env('MAIL_MARKDOWN_THEME', config('services.mail.mail_markdown_theme', 'default')),
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];
