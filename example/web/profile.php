<?php

require('inc_page.php');

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>You are not logged in.</h2>
    <p>
        <a href="/login.php" class="pure-button">Login</a>
    </p>
    <ul>
        <li><a href="protected/shibboleth.php">AAI</a></li>
    </ul>
    ';
} else {
    $sContent.='<h2>Profile</h2>
    <p>
        Hello, <strong>'.$ACCESS_USER.'</strong>.<br>
        <br>
        <button class="pure-button pure" onclick="history.back(); return false;">Back</button>
        <a href="logout.php" class="pure-button">Logout</a>
    </p>
    <p>
        Here is no real profile page ... I just dump the known information :-)
    </p>
    <pre>'.print_r($oAccess->dump(), 1).'</pre>
    '
    ;
}



showPage([
    'title'=>'Login',
    'nav'=>' / <a href="profile.php">Profile</a>',
    'content'=>$sContent,
]);
