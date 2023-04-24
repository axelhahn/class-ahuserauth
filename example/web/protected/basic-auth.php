<?php

require('../inc_page.php');

$sEnvFile=$ACCESS_APPDIR.'/example/inc_fakeenv_basicauth.php';
if (isset($_GET['login']) && $_GET['login']){
    // (1) 
    // fake a login by setting some vars into $_SERVER ...
    require($sEnvFile);

    // (2)
    // ... and detect the user again
    getUser();

}

$sSnippet1='<Location /protected/basic-auth.php>

  AuthType Basic
  AuthName "My app - protected area"
  
  // verify against ...
  AuthBasicProvider ldap
  AuthLDAPURL "ldaps://ldap.example.com:636/..." SSL
  AuthLDAPBindDN "cn=lookup,dc=example.com"
  AuthLDAPBindPassword "password-of-lookup-user"

  Require valid-user

</Location>';


$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Basic authentication</h2>
    <h3>In the real world...</h3>
    <p>
        In the real world the browser shows a dialog to enter user and password.<br>
        The mostly known authentication is with a local htpasswd file.<br>
        But you can use basic auth with sql databases or ldap too.<br>
        <br>
        After a successful login the username can be found in AUTH_USER, PHP_AUTH_USER or REMOTE_USER.<br>
        <br>
        Snippet for Apache httpd to protect a file:
    </p>
    <pre>'.htmlentities($sSnippet1).'</pre>
    <h3>Simulation</h3>
    <p>
        All these I don\'t have here.<br>
        For simulation of a basic authentication these data will be injected 
        into $_SERVER:
    </p>
    <pre>'.htmlentities(file_get_contents($sEnvFile)).'</pre>

    <p>
        <a href="?login=1" class="pure-button">Login now</a>
        <a href="/login.php" class="pure-button">Pickup another method</a><br>
    </p>
    ';
} else {
    $sContent.='<h2>Success</h2>
    <p>
        You are logged in as user <strong>'.$ACCESS_USER.'</strong> now.
    </p>
    <pre>$oAccess->auth->read() = '.print_r($oAccess->auth->read(), 1).'</pre>'
    ;
}


showPage([
    'title'=>'Login with Basic Auth',
    'nav'=>' / <a href="../login.php">Login</a> / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>$sContent,
]);
