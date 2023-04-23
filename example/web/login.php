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
        <li><a href="protected/basic-auth.php">Basic authentication</a></li>
    </ul>
    ';
} else {
    $sContent.='<h2>Login</h2>
    <p>
        You are logged in already.<br>
        Your user is <strong>'.$ACCESS_USER.'</strong>.<br>
        <br>
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
