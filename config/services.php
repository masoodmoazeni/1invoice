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
        'public' => env('STRIPE_PUBLIC'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'success_url' => env('STRIPE_SUCCESS_URL'),
        'cancel_url' => env('STRIPE_CANCEL_URL'),
    ],
    'gohighlevel' => [
        'api_key' => env('GOHIGHLEVEL_API_KEY'),
        'base_url' => env('GOHIGHLEVEL_BASE_URL'),
    ],
    'sendgrid' => [
        'key' => env('SENDGRID_API_KEY', "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"),
        'from_email' => env('SENDGRID_FROM_EMAIL', "Dev@salonspaconnection.com"),
        'from_name' => env('SENDGRID_FROM_NAME', "SalonSpa Connection"),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL', 'http://136.243.255.112:81/login/google/callback'),
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'salonspa_connection' => [
        'n8n_new_listing_webhook' => env('N8N_SC_NEW_LISTING_WEBHOOK'),
        'n8n_buyer_inquiry_webhook' => env('N8N_SC_BUYER_INQUIRY_WEBHOOK'),
        'n8n_nda_signed_webhook' => env('N8N_SC_NDA_SIGNED_WEBHOOK'),
        'x_sc_secret' => env('N8N_SC_X_SC_SECRET'),
        'listing_path_prefix' => env('N8N_SC_LISTING_PATH_PREFIX', 'beauty-businesses-for-sale'),
    ],

];
