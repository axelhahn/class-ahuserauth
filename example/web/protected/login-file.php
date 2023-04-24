<?php

require('../inc_page.php');
require('inc_login.php');

if(count($_POST)){
    echo '<pre>'.print_r($_POST, 1).'</pre>';
    echo "TODO: process these data<br><br>";

    require($ACCESS_APPDIR.'/classes/ah-auth-file.class.php');
    $oUser=new axelhahn\ahAuthFile();
    echo "--- userlist: <pre>".print_r($oUser->list(), 1).'</pre>';
    echo '<br><br>';
    if($oUser->authenticate($_POST['username'], $_POST['password'])){
        echo 'OK: authentication was successful<br><br>';
        getUser();
    } else {
        echo 'ERROR: authentication failed :-/<br><br>';
    }   
}

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Login</h2>
    <p>
        Here is a login form to let enter user and password.<br>
        <br>
        For demo authentication use<br>
        user1<br>
        password1<br>
    </p>'

    .showLoginForm('file')
    ;
} else {
    $sContent.='<h2>Success</h2>
    <p>
        You are logged in as user <strong>'.$ACCESS_USER.'</strong> now.
    </p>
    <pre>$oAccess->auth->read() = '.print_r($oAccess->auth->read(), 1).'</pre>'
    ;
}


showPage([
    'title'=>'Login (file based)',
    'nav'=>' / <a href="../login.php">Login</a> / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>$sContent,
]);
