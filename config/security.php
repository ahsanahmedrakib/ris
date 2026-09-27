<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Strict Transport Security
    |--------------------------------------------------------------------------
    |
    | Only honoured over HTTPS, and only sent when the request arrives over
    | HTTPS. Leave disabled until every subdomain is served over TLS, since
    | includeSubDomains is easy to get wrong and hard to undo.
    |
    */

    'hsts' => (bool) env('SECURITY_HSTS', false),

    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy extras
    |--------------------------------------------------------------------------
    |
    | Appended to the matching base directive. Keep these as narrow as the
    | third party allows: a full origin is far weaker than a path or host
    | list. "development" is driven by APP_ENV and only relaxes the rules so
    | the Vite dev server can hot reload.
    |
    */

    'csp' => [
        'development' => env('APP_ENV') === 'local',

        'script_src' => (string) env('CSP_SCRIPT_SRC', ''),
        'style_src' => (string) env('CSP_STYLE_SRC', ''),
        'img_src' => (string) env('CSP_IMG_SRC', ''),
        'font_src' => (string) env('CSP_FONT_SRC', ''),
        'connect_src' => (string) env('CSP_CONNECT_SRC', ''),
        'frame_src' => (string) env('CSP_FRAME_SRC', ''),
    ],

];
