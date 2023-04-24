<?php

require('inc_page.php');

$sSnippet1='$oAccess->logout();
header(\'Location: \'.basename(__FILE__));';

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
        <button class="pure-button pure" onclick="history.back(); return false;">Back</button>
        <button class="pure-button pure-button-primary">Logout</button>
    </form>

    <hr>
    <h2>Snippets</h2>
    <p>
        For logout there is the method logout(). This destroys the session.<br>
        Maybe you want to refresh the current page too - then send a location header.
    </p>
    <pre>'.htmlentities($sSnippet1).'</pre>

    '
    ;
}



showPage([
    'title'=>'Logout',
    'nav'=>' / <a href="profile.php">Profile</a> / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>$sContent,
]);
