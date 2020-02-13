<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Knowledge Base Master Switch
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable Knowledge Base package.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Teamwork Desk Key
    |--------------------------------------------------------------------------
    |
    | The Teamwork Desk API Key can be generated at:
    | https://your-domain.teamwork.com/desk/#myprofile/apikeys
    |
    */
    'key' => env('TEAMWORK_DESK_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Teamwork Desk Domain Name
    |--------------------------------------------------------------------------
    |
    | The domain is the site address you have set on the Teamwork account.
    | To find the domain name just login to http://teamwork.com.
    | Then you will see the browser URL changing to:
    | https://your-domain.teamwork.com/launchpad/welcome
    |
    */
    'domain' => env('TEAMWORK_DESK_DOMAIN'),

    /*
     |--------------------------------------------------------------------------
     | Teamwork Site ID
     |--------------------------------------------------------------------------
     |
     | The site ID is the ID of the Help Docs project setup on Teamwork Desk.
     | You can find all your Help Docs projects here:
     | https://your-domain.teamwork.com/desk/help-docs/
     | Select the one that you need and copy the ID from the url.
     | E.g. URL: https://quantumnewswire.teamwork.com/desk/help-docs/xxx/articles
     | The "xxx" is the Site ID.
     |
     */
    'site_id' => env('TEAMWORK_SITE_ID'),

    /*
     |--------------------------------------------------------------------------
     | Route Group
     |--------------------------------------------------------------------------
     |
     | Route groups allow you to share route attributes, such as middleware
     | or namespaces, across a large number of routes without needing to define
     | those attributes on each individual route.
     | See: https://laravel.com/docs/6.x/routing#route-groups
     |
     */
    'route_group' => [
        'web' => [
            'domain'     => null,
            'as'         => null,
            'prefix'     => null,
            'middleware' => 'web'
        ],

        'api' => [
            'domain'     => null,
            'as'         => 'api.',
            'prefix'     => 'api',
            'middleware' => ['api', 'auth:api']
        ],
    ],
];
