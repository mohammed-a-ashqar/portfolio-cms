<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    | Every locale the admin can write content in. Adding one here makes the
    | translatable form fields appear for it — no migration required, because
    | translations live in JSON columns.
    */
    'admin_prefix' => env('PORTFOLIO_ADMIN_PREFIX', 'admin'),

    'locales' => [
        'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr', 'flag' => '🇬🇧'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl', 'flag' => '🇸🇦'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'quotes_to' => env('PORTFOLIO_QUOTES_EMAIL'),
        'contact_to' => env('PORTFOLIO_CONTACT_EMAIL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public site caching
    |--------------------------------------------------------------------------
    | Seconds. Set to 0 while developing to see edits immediately.
    */
    'cache_ttl' => (int) env('PORTFOLIO_CACHE_TTL', 600),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'per_page' => [
        'projects' => 12,
        'reels' => 12,
        'admin' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reels
    |--------------------------------------------------------------------------
    | The Instagram token is entirely optional: without it reels still embed
    | through Instagram's public /embed/ route, they just keep the caption and
    | poster image you supply in the admin instead of the ones from Meta.
    */
    'reels' => [
        'instagram_token' => env('INSTAGRAM_OEMBED_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_size_kb' => (int) env('PORTFOLIO_MAX_UPLOAD_KB', 4096),
        'image_mimes' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
    ],
];
