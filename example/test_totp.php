<?php
// require(__DIR__.'/../classes/totp.class.php');
require(__DIR__.'/../classes/ah-totp.class.php');

/**
 * for qr code:   otpauth://totp/screenconnect?secret=PEHMPSDNLXIOG65U
 * for scan qr code in mobile use FreeOTP or Google Authenticator
 * vhdm.ir
 */
// $InitalizationKey = "PEHMPSDNLXIOG65U";					// Set the inital key
$InitalizationKey = AhTotp::generate_secret_key();

$TimeStamp	  = ahTotp::get_timestamp();
$secretkey 	  = ahTotp::base32_decode($InitalizationKey);	// Decode it into binary
$secrethex 	  = ahTotp::key_as_hex($InitalizationKey);	// Decode it into binary
$otp       	  = ahTotp::oath_hotp($secretkey, $TimeStamp);	// Get current token

echo PHP_EOL;
echo '++++++++++ TEST TOTP ++++++++++'. PHP_EOL;
echo PHP_EOL;
echo '----- init data:'. PHP_EOL;
echo "Init key: $InitalizationKey" . PHP_EOL;
// echo "Secretkey: $secretkey" . PHP_EOL; // this dumps binary ... what is a bit useless :-)
echo "hex     : $secrethex" . PHP_EOL; // ok, it was verified with https://cryptii.com/pipes/base32-to-hex
echo PHP_EOL;


echo '----- data for now (current timestamp):'. PHP_EOL;
// echo "Timestamp: $TimeStamp" . PHP_EOL;
echo "One time password that must match: $otp" . PHP_EOL;
echo PHP_EOL;

echo "----- SIMULATION: a user enters $otp as OTP..." . PHP_EOL;
$result = ahTotp::verify_key($InitalizationKey, $otp);
echo "" . ( $result ? 'OK: true' : 'ERROR: false' ). PHP_EOL;
echo PHP_EOL;

echo "----- SIMULATION: a user enters 123456 as OTP..." . PHP_EOL;
$result = ahTotp::verify_key($InitalizationKey, "123456");
echo "" . ( $result ? 'ERROR: true' : 'OK: false' ). PHP_EOL;
echo PHP_EOL;
