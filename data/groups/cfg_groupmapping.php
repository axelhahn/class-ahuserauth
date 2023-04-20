<?php
/*

    Mappings:
    FROM a found group of a autth
    TO   group name for my app
*/

return [

    '*'                      => [],

    'type=reader-basicauth'  => [
        'PHP_AUTH_USER' => 'my-company',
    ],
    'type=file'              => [],
    'type=reader-shibboleth' => [
        'university/example.com'                         => 'example-university',
        'university/example.com/com:example:department'  => 'example-department',
    ],
];
