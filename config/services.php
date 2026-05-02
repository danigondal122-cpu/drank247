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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'map_key'       => env('GOOGLE_MAP_KEY'),
        'map_api_url'   => env('GOOGLE_MAP_API_URL'),
        'client_id'     => env('GOOGLE_CLIENT_ID'),
		'client_secret' => env('GOOGLE_CLIENT_SECRET'),
		'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT'),
    ],

    'postcode' => [
        'api_token' => env('POST_CODE_API_TOKEN'),
        'enpoint'   => env('POST_CODE_API_URL'),
    ],

    'paynl' => [
        'service_id'        => env('PAYNL_SERVICE_ID'),
		'test_mode'         => env('PAYNL_TEST_MODE'),
        'auth' => [
            'transaction'   => [
                'username'  => env('PAYNL_TRANSACTION_AUTH_USERNAME'),
                'password'  => env('PAYNL_TRANSACTION_AUTH_PASSWORD'),
            ],
            'idin' => [
                'username'  => env('PAYNL_IDIN_AUTH_USERNAME'),
                'password'  => env('PAYNL_IDIN_AUTH_PASSWORD'),
            ]
        ],
    ],

    'deliverect' => [
        'mode'          => env('DELIVERECT_MODE', 'Staging'),
        'client_id'     => env('DELIVERECT_CLIENT_ID'),
        'client_secret' => env('DELIVERECT_CLIENT_SECRET'),
        'account_id'    => env('DELIVERECT_ACCOUNT_ID'),
        'location_id'   => env('DELIVERECT_LOCATION_ID'),
    ],

    'uber' => [
        'client_id'     => env('UBER_EATS_CLIENT_ID'),
        'client_secret' => env('UBER_EATS_CLIENT_SECRET'),
    ],

    'takeaway' => [
        'mode'              => env('TAKEAWAY_MODE', 'Staging'),
        'staging_store_id'  => env('TAKEAWAY_STAGING_STORE_ID'),
        'live_store_id'     => env('TAKEAWAY_LIVE_STORE_ID'),
        'username'          => env('TAKEAWAY_USERNAME'),
        'password'          => env('TAKEAWAY_PASSWORD'),
        'api_key'           => env('TAKEAWAY_API_KEY'),
    ],
];
