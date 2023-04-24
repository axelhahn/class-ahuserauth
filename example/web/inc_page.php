<?php

// ----------------------------------------------------------------------
global $ACCESS_USER;
global $oAccess;

// ----------------------------------------------------------------------

/**
 * get currently logged in user
 * It sets the global var $ACCESS_USER here
 * @return boolean
 */
function getUser(){
    global $oAccess, $ACCESS_USER;
    if($oAccess->detectUser()) {
        // $ACCESS_USER=$oAccess->getUserid();
        // $ACCESS_USER=$oAccess->user;
        $aUser=$oAccess->auth->read();
        $ACCESS_USER=$aUser['userid'];
    }
    return true;    
}

/**
 * show a webpage and exit
 * @param  array  $aParts  array with these subkey for replacements in the template
 *                           - title
 *                           - nav
 *                           - userprofile
 *                           - content
 * @return undefined
 */
function showPage($aParts, $iCode=200){
    global $oAccess, $ACCESS_USER;
    $sPage='<!doctype html>
    <html>
        <head>
            <title>{{TITLE}}</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pure/3.0.0/pure-min.css" integrity="sha512-X2yGIVwg8zeG8N4xfsidr9MqIfIE8Yz1It+w2rhUJMqxUwvbVqC5OPcyRlPKYOw/bsdJut91//NO9rSbQZIPRQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <link rel="stylesheet" href="/main.css" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        </head>
        <body>
            <header>
                <span class="user">{{USERPROFILE}}</span>
                <h1>{{TITLE}}</h1>
                <nav><a href="/index.php" class=""><i class="fa-solid fa-house"></i> Home</a> {{NAV}}</nav>
            </header>

            <article class="sbox">
                {{CONTENT}}
            </article>

        </body>
    </html>
    ';


    if(!isset($aParts['userprofile'])){
        $aParts['userprofile']=$ACCESS_USER 
            ? '<a href="/profile.php"><i class="fa-solid fa-user"></i> '.$ACCESS_USER.'</a>' 
            : '<a href="/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>'
            ;
    }
    foreach([
        '{{CONTENT}}'=>'content',
        '{{NAV}}'=>'nav',
        '{{USERPROFILE}}'=>'userprofile',
        '{{TITLE}}'=>'title',
    ] as $sFrom=>$sPartKey){
        $sPage=isset($aParts[$sPartKey]) ? str_replace($sFrom, $aParts[$sPartKey], $sPage) : $sPage;
    }

    echo $sPage;
    exit(0);
}

// ----------------------------------------------------------------------
// INIT

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);



$ACCESS_APPDIR=__DIR__.'/../..';
require($ACCESS_APPDIR.'/classes/ah-access.class.php');

$oAccess=new axelhahn\ahAccesscontrol();

getUser();

// ----------------------------------------------------------------------
