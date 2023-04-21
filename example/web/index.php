<?php

require('inc_page.php');

showPage([
    'title'=>'Welcome',
    'nav'=>'',
    'content'=>'

        <h2>Hello</h2>
        <p>
            Here is a public, unprotected page.<br>
            But I want to have protected content. Therefor
            we offer different login types.
        </p>

        <h2>Let\'s start</h2>
        <p>
            <a href="login.php" class="pure-button">Login</a>
        </p>
        ',
]);
