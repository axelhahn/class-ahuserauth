<?php
// ----------------------------------------------------------------------
// 
// FAKE ENVIRONMENT for a basic authentication
// 
// ----------------------------------------------------------------------


// $_SERVER["AUTH_USER"]='John auth_user';         // IIS
$_SERVER["PHP_AUTH_USER"]='John php_auth_user';    // APACHE
// $_SERVER["REMOTE_USER"]='John remote_user';     // CGI standard
