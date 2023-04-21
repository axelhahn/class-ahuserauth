<?php
require(__DIR__.'/../classes/ah-access.class.php');
require(__DIR__.'/inc_fakeenv_basicauth.php');

$oAccess=new axelhahn\ahAccesscontrol();

if(!$oAccess->detectUser()) {
    echo "NO USER user was detected. Now should follow a login form. ".PHP_EOL;
} else {
    echo "A user was detected:".PHP_EOL;
    // echo "    Type  : " . $oAccess->getUsertype().PHP_EOL;
    // echo "    userid: ".$oAccess->getUserid().PHP_EOL;
    echo PHP_EOL;
    // echo "oAccess->auth->read()".PHP_EOL;
    // print_r($oAccess->auth->read());
}
print_r($oAccess->dump());

// list all available roles
// echo "All available roles: "; print_r($oAccess->getDefinedRoles());