<?php
/*

    TEST create - read - update - delete of

*/
require(__DIR__.'/../classes/ah-auth-file.class.php');

$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$pwd = substr(str_shuffle($chars),0,16);
$pwd2 = substr(str_shuffle($chars),0,16);


$oUser=new axelhahn\ahAuthFile();

echo "--- userlist:".PHP_EOL;
print_r($oUser->list());


if($oUser->isReadonly){
    echo "--- read():".PHP_EOL;
    print_r($oUser->read());
    echo PHP_EOL;

    echo "SKIP write actions - ".__CLASS__ . ' is readonly.' . PHP_EOL;
} else {


    // echo "--- set 'admin':".PHP_EOL;
    // $oUser->set('admin');
    echo "--- create testuser with password $pwd:".PHP_EOL;
    echo $oUser->create('testuser', $pwd) ? "OK, created" : "Ooops - already exists";
    echo PHP_EOL;

    echo "--- set testuser:".PHP_EOL;
    $oUser->set('testuser');
    echo "--- read():".PHP_EOL;
    print_r($oUser->read());
    echo PHP_EOL;

    echo "--- userlist:".PHP_EOL;
    print_r($oUser->list());

    echo "--- authenticate('testuser', '".$pwd."'):".PHP_EOL;
    echo $oUser->authenticate('testuser', $pwd) ? "OK, authenticated" : "Ooops - authentication failed";
    echo PHP_EOL;

    echo "--- authenticate('testuser', '1234'):".PHP_EOL;
    echo $oUser->authenticate('testuser', '1234') ? "ERROR: authenticated with wrong password" : "OK: authentication failed when using wrong password";
    echo PHP_EOL;


    echo "--- authenticate('someuser', '".$pwd."'):".PHP_EOL;
    echo $oUser->authenticate('someuser', $pwd) ? "ERROR: authenticated with wrong user" : "OK: authentication failed when using wrong user";
    echo PHP_EOL;

    echo "--- authenticate('testuser', '".$pwd."'):".PHP_EOL;
    echo $oUser->authenticate('testuser', $pwd) ? "OK, authenticated" : "Ooops - authentication failed";
    echo PHP_EOL;


    echo "--- update(['password'=>$pwd2]):".PHP_EOL;
    echo $oUser->update(['password'=>$pwd2]) ? "OK, password was updated" : "Ooops - unable to update password";
    echo PHP_EOL;


    echo "--- authenticate('testuser', '".$pwd2."'):".PHP_EOL;
    echo $oUser->authenticate('testuser', $pwd2) ? "OK, authenticated with updated password" : "Ooops - authentication with new password failed";
    echo PHP_EOL;


    echo "--- delete():".PHP_EOL;
    echo $oUser->delete() ? "OK, deleted" : "Ooops - deletion failed";
    echo PHP_EOL;

    echo "--- userlist:".PHP_EOL;
    print_r($oUser->list());
}
