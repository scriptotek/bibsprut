<?php

return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => 'bibsprut',

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id'       => env('GOOGLE_CLIENT_ID'),
    'client_secret'   => env('GOOGLE_CLIENT_SECRET'),
    'redirect_uri'    => env('GOOGLE_REDIRECT_URI'),
    'scopes'          => [
                            'https://www.googleapis.com/auth/youtube.readonly',
                            'https://www.googleapis.com/auth/youtube',
                            'https://www.googleapis.com/auth/youtube.upload',
                            'https://www.googleapis.com/auth/youtubepartner',
                            'https://www.googleapis.com/auth/yt-analytics.readonly',
                         ],
    'access_type'     => 'online',
    'approval_prompt' => 'auto',

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => '',  // env('GOOGLE_API_KEY'),

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Enable and set the information below to use assert credentials
    | Enable and leave blank to use app engine or compute engine.
    |
    */
    'service' => [
        /*
        | Enable service account auth or not.
        */
        'enable' => false,

        /*
        | Example xxx@developer.gserviceaccount.com
        */
        'account' => '',

        /*
        | Example ['https://www.googleapis.com/auth/cloud-platform']
        */
        'scopes' => [],

        /*
        | Path to key file
        | Example storage_path().'/key/google.p12'
        */
        'key' => '',
    ],
];
