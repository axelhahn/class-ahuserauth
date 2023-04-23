<?php

global $ACCESS_USER; $ACCESS_USER='';
global $oAccess;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);



$ACCESS_APPDIR=__DIR__.'/../../';
require($ACCESS_APPDIR.'/classes/ah-access.class.php');

$oAccess=new axelhahn\ahAccesscontrol();

function getUser(){
    global $oAccess, $ACCESS_USER;
    if($oAccess->detectUser()) {
        // $ACCESS_USER=$oAccess->getUserid();
        $ACCESS_USER=$oAccess->user;
    }
    return true;    
}

function showPage($aParts, $iCode=200){
    global $oAccess, $ACCESS_USER;
    $sPage='<!doctype html>
    <html>
        <head>
            <title>{{TITLE}}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css" integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">
            <link rel="stylesheet" href="/main.css" />
        </head>
        <body>
            <header>
                <span class="user">{{USERPROFILE}}</span>
                <h1>{{TITLE}}</h1>
                <nav><a href="/index.php" class="">Home</a> {{NAV}}</nav>
            </header>

            <article class="sbox">
                {{CONTENT}}
            </article>

        </body>
    </html>
    ';


    if(!isset($aParts['userprofile'])){
        $aParts['userprofile']=$ACCESS_USER ? $ACCESS_USER : '(Not logged in)';
    }
    foreach([
        '{{TITLE}}'=>'title',
        '{{CONTENT}}'=>'content',
        '{{NAV}}'=>'nav',
        '{{USERPROFILE}}'=>'userprofile',
    ] as $sFrom=>$sPartKey){
        $sPage=isset($aParts[$sPartKey]) ? str_replace($sFrom, $aParts[$sPartKey], $sPage) : $sPage;
    }

    echo $sPage;
    exit(0);
}

// INIT
getUser();
