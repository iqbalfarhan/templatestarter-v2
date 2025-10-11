<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default role lists
    |--------------------------------------------------------------------------
    |
    | List of available roles in the application.
    | Format: Array of strings
    | Example: ['superadmin', 'admin', 'user']
    |
    */
    
    'default-roles' => [
        'superadmin',
        'admin',
        'user',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default role for new users
    |--------------------------------------------------------------------------
    |
    | The role that will be automatically assigned to newly registered users.
    | Value must exist in the default-roles array above.
    | Example: 'user'
    |
    */

    'default-role' => 'user',

    /*
    |--------------------------------------------------------------------------
    | Is Landing Page Enabled
    |--------------------------------------------------------------------------
    |
    | Controls the visibility of the landing page.
    | true  = Show landing page to visitors
    | false = Redirect directly to dashboard
    | Configure via WITH_LANDINGPAGE in .env
    |
    */

    'with-landingpage' => env('WITH_LANDINGPAGE', true),

    /*
    |--------------------------------------------------------------------------
    | Generated React Files Path
    |--------------------------------------------------------------------------
    |
    | Directory path where React component files will be generated.
    | Default: resources/js/pages
    | Configure via GENERATED_REACT_FILES_PATH in .env
    |
    */

    'generated-react-files-path' => env('GENERATED_REACT_FILES_PATH', resource_path('js/pages')),

    /*
    |--------------------------------------------------------------------------
    | Additional Permissions
    |--------------------------------------------------------------------------
    |
    | Define granular permissions for different sections of the application.
    | Format: [
    |    'section_name' => [
    |        'permission_name' => ['array_of_roles'],
    |    ]
    | ]
    | Use '*' to grant access to all roles
    |
    */

    'additional_permissions' => [
        "settings" => [
            "menu adminer" => ['superadmin'],
        ],
        "role_permission" => [
            "menu role" => ['superadmin'],
            "index role" => ['superadmin'],
            "show role" => ['superadmin'],
            "create role" => ['superadmin'],
            "update role" => ['superadmin'],
            "delete role" => ['superadmin'],
            "index permission" => ['superadmin'],
            "create permission" => ['superadmin'],
            "update permission" => ['superadmin'],
            "delete permission" => ['superadmin'],
            "resync permission" => ['superadmin'],
        ],
        "dashboard" => [
            "profile" => ["*"],
            "documentation" => ["*"]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable Adminer
    |--------------------------------------------------------------------------
    |
    | Controls the visibility of the Adminer database management tool.
    | true  = Show Adminer menu option
    | false = Hide Adminer menu option
    | Note: Only available to superadmin role
    |
    */

    'enable_adminer_menu' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable Laravel Socialite
    |--------------------------------------------------------------------------
    |
    | Controls social media authentication functionality via Laravel Socialite.
    | To use Google authentication, set these environment variables in .env:
    | 
    | GOOGLE_CLIENT_ID=your-client-id-here
    | GOOGLE_CLIENT_SECRET=your-client-secret-here
    | GOOGLE_REDIRECT_URI=http://your-domain/auth/google/callback
    |
    | Get your credentials from Google Cloud Console:
    | 1. Go to https://console.cloud.google.com
    | 2. Create a new project or select existing one
    | 3. Enable Google+ API
    | 4. Go to Credentials > Create Credentials > OAuth Client ID
    | 5. Set up OAuth consent screen if needed
    | 6. Choose Web Application type
    | 7. Add authorized redirect URIs
    |
    | @type boolean
    |
    */

    'enable_socialite' => false,
];