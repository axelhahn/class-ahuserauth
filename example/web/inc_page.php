<?php

$AUTH_APPDIR=__DIR__.'/../../';
require($AUTH_APPDIR.'/classes/ah-access.class.php');

function showPage($aParts, $iCode=200){
    $sPage='<!doctype html>
    <html>
        <head>
            <title>{{TITLE}}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css" integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">
            <link rel="stylesheet" href="/main.css" />
        </head>
        <body>
            <header>
                <h1>{{TITLE}}</h1>
                <nav><a href="index.php" class="pure-button">Home</a> {{NAV}}</nav>
            </header>

            <article class="sbox">
                {{CONTENT}}
            </article>

        </body>
    </html>
    ';

    foreach([
        '{{TITLE}}'=>'title',
        '{{CONTENT}}'=>'content',
        '{{NAV}}'=>'nav',
    ] as $sFrom=>$sPartKey){
        $sPage=isset($aParts[$sPartKey]) ? str_replace($sFrom, $aParts[$sPartKey], $sPage) : $sPage;
    }

    echo $sPage;
    exit(0);
}