<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Official Corporate Identity
    |--------------------------------------------------------------------------
    |
    | Official corporate profile for PT Rapid Infrastruktur Indonesia (RAPID).
    | Used across corporate documents, emails, and Digital Business Cards.
    |
    */

    'name' => env('COMPANY_NAME', 'PT Rapid Infrastruktur Indonesia'),

    'brand' => env('COMPANY_BRAND', 'RAPID'),

    'office_address' => env('COMPANY_OFFICE_ADDRESS', 'Talavera Office Park Lt. 12, Jl. TB Simatupang Kav. 22–26, Cilandak Barat, Cilandak, Jakarta Selatan, DKI Jakarta'),

    'website' => env('COMPANY_WEBSITE', 'https://www.rapidinfrastruktur.com'),

    'email' => env('COMPANY_EMAIL', 'info@rapidinfrastruktur.com'),

    'phone' => env('COMPANY_PHONE', '+62 21 0000 0000'),

    /*
    |--------------------------------------------------------------------------
    | Corporate Timezone
    |--------------------------------------------------------------------------
    |
    | Official corporate operational timezone for PT Rapid Infrastruktur Indonesia.
    | Defaults to Asia/Jakarta (WIB) for local contract & business validity rules.
    |
    */

    'timezone' => env('COMPANY_TIMEZONE', 'Asia/Jakarta'),

    /*
    |--------------------------------------------------------------------------
    | Corporate Branding & Visuals
    |--------------------------------------------------------------------------
    |
    | Official navy, red, and white corporate color direction.
    |
    */

    'colors' => [
        'primary' => '#000000',
        'secondary' => '#1e293b',
        'accent' => '#dc2626',
        'background' => '#ffffff',
    ],

    /*
    |--------------------------------------------------------------------------
    | Official Company Logo Paths
    |--------------------------------------------------------------------------
    |
    | Relative to public path. Black logo for white background, white logo for black background.
    |
    */

    'logo_path' => env('COMPANY_LOGO_PATH', 'images/company-logo.png'),

    'white_logo_path' => env('COMPANY_WHITE_LOGO_PATH', 'images/company-logo-white.png'),

];
