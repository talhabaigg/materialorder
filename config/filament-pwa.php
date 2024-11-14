<?php

return [

    'manifest' => [
        'enabled' => true,
        'path' => 'manifest.json', // Ensure this path is correct
    ],

    /*
     * ---------------------------------------------------------------
     * Add Middleware To Roues
     * ---------------------------------------------------------------
     */
    "middlewares" => ['super_admin'],

    /*
     * ---------------------------------------------------------------
     * Allow Routes
     * ---------------------------------------------------------------
     */
    "allow_routes" => true
];
