<?php

require('inc_page.php');

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Login</h2>
    <p>
        You can simulate a login with different methods.<br>
        A successful login will be stored in a session variable.<br>
    </p>


    <h3>"Readonly" from $_SERVER</h3>
    <p>
        No implementation of user handling is required from the view of the application:<br>
        <br>
        <a href="protected/basic-auth.php" class="pure-button">Basic authentication</a>
        <a href="protected/shibboleth.php" class="pure-button">Shibboleth</a>
    </p>


    <h3>With login form</h3>
    <p>
        These login based methods authenticate a user with a local resource
        and you need a user admin in the backend.<br>
        For the user of your app there is just a for user + password
        without knowledge of the authentication method.<br>
        <br>
        <a href="protected/login-file.php" class="pure-button">File based user</a>
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
