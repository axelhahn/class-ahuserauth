<?php

require('../inc_page.php');

// (1) 
// fake a login by setting some vars into $_SERVER ...
require($ACCESS_APPDIR.'/example/inc_fakeenv_shibboleth.php');

// (2)
// ... and detect the user again
getUser();


$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Oops</h2>
    <p>
        Login failed. Even in this demo.
    </p>
    <pre>'.print_r($oAccess->dump(), 1).'</pre>
    ';
} else {
    $sContent.='<h2>Success</h2>
    <p>
        You are currently logged in as user <strong>'.$ACCESS_USER.'</strong>.
    </p>';
}


showPage([
    'title'=>'Login with Shibboleth',
    'nav'=>' / <a href="../login.php">Login</a>',
    'content'=>$sContent,
]);
