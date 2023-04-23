<?php

require('../inc_page.php');

$sContent='';
if(!$oAccess->detectUser()) {
    $sContent='';
}

showPage([
    'title'=>'My app',
    'nav'=>' / <a href="'.basename(__FILE__).'">{{TITLE}}</a>',
    'content'=>'

        <h2>Here is my fictive App</h2>
        <p>
            This is a public, unprotected page.<br>
            <br>
        </p>'
        ,
]);
