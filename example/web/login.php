<?php

require('inc_page.php');

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Login</h2>
    <p>
        You can simulate a login with the following methods:<br>
        <br>
        <a href="protected/shibboleth.php" class="pure-button">AAI</a>
        <a href="protected/basic-auth.php" class="pure-button">Basic authentication</a>
    </p>
    ';
} else {
    $sContent.='<h2>Login</h2>
    <p>
        You are logged in already.<br>
        Your user is <strong>'.$ACCESS_USER.'</strong>.<br>
        <br>
        <a href="/index.php" class="pure-button">Home</a>
        <a href="profile.php" class="pure-button">Profile</a><br>
    </p>
    '
    ;
}



showPage([
    'title'=>'Login',
    'nav'=>' / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>$sContent,
]);
