<?php

require('inc_page.php');

showPage([
    'title'=>'Welcome',
    'nav'=>'',
    'content'=>'

        <h2>Hello</h2>
        <p>
            Here is a public, unprotected page.<br>
        </p>'
        . ($ACCESS_USER
            ? '<p>
                OK, you are logged in.<br>
                </p>            
                '
            : '<h2>Let\'s start</h2>    
                <p>
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
        '
        ,
]);
