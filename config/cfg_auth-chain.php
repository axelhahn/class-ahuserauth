<?php

define('USAGE_DISABLED', 0);
define('USAGE_OPTIONAL', 1);
define('USAGE_REQUIRED', 2);

/**
 * chaining of methods
 * 
 * endpoints:
 *  _success
 *  _abort
 * 
 * @var array
 */
return [
    'reader-shibboleth'=>[
        'status'=>USAGE_OPTIONAL,
        'ok'=>'_success',
        'error'=>'_abort',
        'na'=>'_abort',
    ],
    'reader-basicauth'=>[
        'status'=>USAGE_OPTIONAL,
        // 'ok'=>'totp',
        'ok'=>'_success',
        'error'=>'_abort',
        // 'na'=>'shibboleth',
        'na'=>'_abort',
    ],
    'totp'=>[
        'status'=>USAGE_REQUIRED,
        'ok'=>'_success',
        'error'=>'_abort',
        'na'=>'_abort',
    ],
];
