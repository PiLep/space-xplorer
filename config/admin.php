<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Email Whitelist
    |--------------------------------------------------------------------------
    |
    | List of email addresses authorized to access the admin panel.
    | Multiple emails should be separated by commas.
    | If empty, all super admin users can access the admin panel.
    |
    */

    'email_whitelist' => env('ADMIN_EMAIL_WHITELIST', ''),
];
