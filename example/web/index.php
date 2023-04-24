<?php

require('inc_page.php');

$sSnippet1='$ACCESS_APPDIR=\'[app path]\';
require($ACCESS_APPDIR.\'/classes/ah-access.class.php\');
$oAccess=new axelhahn\ahAccesscontrol();';

$sSnippet2='if(!$oAccess->detectUser()) {
    echo "You are not logged in yet.<br>";
} else {
    echo "OK, you are logged in.<br>";
}';

showPage([
    'title'=>'Welcome',
    'nav'=>'',
    'content'=>'

        <h2>Hello</h2>
        <p>
            Here is a public, unprotected page.<br>
        </p>
        '
        . ($ACCESS_USER
            ? '<p>
                OK, you are logged in.<br>
                <br>
                <a href="profile.php" class="pure-button">Profile</a>
                </p>            
                '
            : '<p>
                    You are not logged in yet.<br>
                    <br>
                    <a href="login.php" class="pure-button">Login</a>
                </p>
            '
            )
        . '

        <h2>Check access and roles</h2>
        <p>
            <a href="/app-pages/index.php" class="pure-button">App</a> - unprotected area.<br>
        </p>

        <!--
        <p>
            <a href="/app-pages/role1.php" class="pure-button">App - role 1</a> - authenticated users only.<br>
        </p>
        -->

        <hr>
        <div class="snippets">
            <h2>Snippets</h2>
            <p>
                We need to initialize the class that handles access.<br>
            </p>
            <pre>'.htmlentities($sSnippet1).'</pre>
            <p>
                We can detect if a user is logged in:
            </p>
            <pre>'.htmlentities($sSnippet2).'</pre>
        </div>
        '
        ,
]);
