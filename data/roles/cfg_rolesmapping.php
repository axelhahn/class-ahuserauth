<?php
/**
 * ======================================================================
 * MAPPING of user and its groups to roles
 * ======================================================================
 * 
 * BEST PRACTICE: start with lowest permissions and go up into more 
 * detailed definitions
 * 
 * sections:
 * - start with "__anonymous__" access
 * - next is "__authenticated__" for any somehow authenticated user
 * - @GROUP - set permissions based on a group
 * - UUID - user id based special permissions
 * 
 * values:
 * - key {string} = name of a role you want to detect in your methods
 *                  the string "*" matches all roles and is admin mode 
 *                  with full access
 * - value {bool} = enable or disable a rule
 * 
 */
return [

    '__anonymous__'=>[
        'public'=>1,
    ],

    '__authenticated__'=>[
        'public'=>1,
        'profile'=>1,
        'do_posts'=>1,
        'do_comments'=>1,
    ],


    '@example-university'=>[
        'example-university-intranet'=>1,
    ],

    '@example-department'=>[
        'department-intranet'=>1,
    ],

    '@admin'=>[
        '*'=>1,
    ],

];