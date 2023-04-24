<?php

require('inc_page.php');

// do logout if a POST was sent
if(isset($_POST['action']) && $_POST['action']=='logout'){
    // echo 'DEBUG: <pre>$_POST = '.print_r($_POST, 1).'</pre>';
    $oAccess->logout();
    header('Location: '.basename(__FILE__));
}

getUser();

$sContent='';
if (!$ACCESS_USER){
    $sContent.='<h2>Logout successful</h2>
    <p>
        You were logged out.<br>
        <br>
        <a href="/index.php" class="pure-button">Home</a>
        <a href="/login.php" class="pure-button">Login</a>
    </p>
    ';
} else {
    $sContent.='<h2>Logout</h2>
    <form method="POST" action="?">
        <input type="hidden" name="action" value="logout" />
        Are you sure that you want to logout from user <strong>'.$ACCESS_USER.'</strong>?<br>
        <br>
        <button class="pure-button">Logout</button>
    </form>
    '
    ;
}



showPage([
    'title'=>'Logout',
    'nav'=>' / <a href="profile.php">Profile</a> / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>$sContent,
]);
