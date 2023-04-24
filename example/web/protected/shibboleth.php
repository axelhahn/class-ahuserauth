<?php

require('../inc_page.php');

$sEnvFile=$ACCESS_APPDIR.'/example/inc_fakeenv_shibboleth.php';
if (isset($_GET['login']) && $_GET['login']){
    // (1) 
    // fake a login by setting some vars into $_SERVER ...
    require($sEnvFile);

    // (2)
    // ... and detect the user again
    getUser();

}

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Shibboleth authentication</h2>
    <h3>In the real world...</h3>
    <p>
        In the real world you need to register a new AAI service provider and install ...
    </p>
    <ul>
        <li>Shibboleth service</li>
        <li>Apache httpd with mod_shibd</li>
        <li>
            configure your webserver to enable a Shibboleth protected page.
            <pre>
&lt;Location /protected/shibboleth.php>
  AuthType shibboleth
  ShibRequestSetting requireSession 1
  Require shib-session
&lt;/Location></pre>
        </li>
    </ul>
    <h3>Simulation</h3>
    <p>
        All these I don\'t have here.<br>
        For simulation of a valid Shibboleth session these data will be injected 
        into $_SERVER:
    </p>
    <pre>'.htmlentities(file_get_contents($sEnvFile)).'</pre>

    <p>
    <a href="?login=1" class="pure-button pure-button-primary">Login now</a>
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
    'title'=>'Login with Shibboleth',
    'nav'=>' / <a href="../login.php">Login</a> / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>$sContent,
]);
