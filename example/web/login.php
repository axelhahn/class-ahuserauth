<?php

require('inc_page.php');

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Login</h2>
    <p>
        You can simulate a login with the following methods:
    </p>
    <ul>
        <li><a href="protected/shibboleth.php">AAI</a></li>
    </ul>
    ';
} else {
    $sContent.='<h2>Logout</h2>
    <p>
        You are logged in already.<br>
        Your user is <strong>'.$ACCESS_USER.'</strong>.<br>
        <br>
        <a href="logout.php" class="pure-button">Logout</a>
    </p>
    <h2>Profile</h2>
    <p>
        Here is no real profile ... I just dump the known information :-)
    </p>
    <pre>'.print_r($oAccess->dump(), 1).'</pre>
    '
    ;
}



showPage([
    'title'=>'Login',
    'nav'=>' / <a href="login.php">Login</a>',
    'content'=>$sContent,
]);
