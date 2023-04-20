<?php

/**
 * 
 * SOURCE: https://gist.github.com/vhdm/f6e42479e1fb9f119d3d
 * from Phil
 *
 * - hungarian notation
 * - replaced array lot with a string
 * - replaced repeat of 32 bit string in generate_secret_key and base32_decode
 * - update phpdoc
 * 
 **/

class AhTotp
{

	const iExpireTime 	= 30;	// Interval between key regeneration
	const iOtpLength	= 6;	// Length of the Token generated

	/**
	 * Lookup needed for Base32 encoding; A -> 0 ... 7 -> 31
	 * @var string
	 */
	private static $sChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

	/**
	 * Generates a 16 digit secret key in base32 format
	 * 
	 * @param  integer  $iLength  custom length of the key; default: 16
	 * @return string
	 **/
	public static function generate_secret_key($iLength = 16)
	{
		$sReturn = "";

		for ($i = 0; $i < $iLength; $i++) {
			$sReturn .= self::$sChars[rand(0, 31)];
		}

		return $sReturn;
	}

	/**
	 * Returns the current Unix Timestamp devided by the iExpireTime
	 * period.
	 * 
	 * @return integer
	 **/
	public static function get_timestamp()
	{
		return floor(microtime(true) / self::iExpireTime);
	}

	/**
	 * Decodes a base32 string into a binary string.
	 * 
	 * @param  string  $b32  input value what to decode
	 * @return string
	 **/
	public static function base32_decode($b32)
	{
		$sReturn = "";

		$b32 	= strtoupper($b32);

		if (!preg_match('/^[' . self::$sChars . ']+$/', $b32, $match))
			throw new Exception('Invalid characters in the base32 string.');

		$l 	= strlen($b32);
		$n	= 0;
		$j	= 0;

		for ($i = 0; $i < $l; $i++) {

			$n = $n << 5; 				// Move buffer left by 5 to make room
			// $n = $n + self::$aLookupTable[$b32[$i]]; 	// Add value into buffer
			$n = $n + strpos(self::$sChars, $b32[$i]); 	// Add value into buffer

			$j = $j + 5;				// Keep track of number of bits in buffer

			if ($j >= 8) {
				$j = $j - 8;
				$sReturn .= chr(($n & (0xFF << $j)) >> $j);
			}
		}

		return $sReturn;
	}

	/**
	 * generate a hex code from init key
	 * 
	 * @param  string  $b32     input value what to decode
	 * @param  string  $sDelim  output delimiter between hex codes; default: ' ' (space)
	 * @return string
	 */
	public static function key_as_hex($b32, $sDelim = ' ')
	{
		$sReturn = '';
		$sBinary = self::base32_decode($b32);
		for ($i = 0; $i < strlen($sBinary); $i++) {
			$sReturn .= ($sReturn ? $sDelim : '') . bin2hex($sBinary[$i]);
		}
		return $sReturn;
	}

	/**
	 * Takes the secret key and the timestamp and returns the one time
	 * password.
	 *
	 * @param string   $key      Secret key in binary form.
	 * @param integer  $counter  Timestamp as returned by get_timestamp.
	 * @return string
	 **/
	public static function oath_hotp($key, $counter)
	{
		if (strlen($key) < 8) {
			throw new Exception('Secret key is too short. Must be at least 16 base 32 characters');
		}
		$bin_counter = pack('N*', 0) . pack('N*', $counter);		// Counter must be 64-bit int
		$hash 	 = hash_hmac('sha1', $bin_counter, $key, true);

		return str_pad(self::oath_truncate($hash), self::iOtpLength, '0', STR_PAD_LEFT);
	}

	/**
	 * Verifys a user inputted key against the current timestamp. Checks $window
	 * keys either side of the timestamp.
	 *
	 * @param string $b32seed
	 * @param string $key - User specified key
	 * @param integer $window
	 * @param boolean $useTimeStamp
	 * @return boolean
	 **/
	public static function verify_key($b32seed, $key, $window = 4, $useTimeStamp = true)
	{
		$timeStamp = self::get_timestamp();
		if ($useTimeStamp !== true) {
			$timeStamp = (int)$useTimeStamp;
		}

		$binarySeed = self::base32_decode($b32seed);

		for ($ts = $timeStamp - $window; $ts <= $timeStamp + $window; $ts++) {
			if (self::oath_hotp($binarySeed, $ts) == $key) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Extracts the OTP from the SHA1 hash.
	 * 
	 * @param string  $hash  binary string
	 * @return integer
	 **/
	public static function oath_truncate($hash)
	{
		$offset = ord($hash[19]) & 0xf;

		return (
			((ord($hash[$offset + 0]) & 0x7f) << 24) |
			((ord($hash[$offset + 1]) & 0xff) << 16) |
			((ord($hash[$offset + 2]) & 0xff) << 8) |
			(ord($hash[$offset + 3]) & 0xff)
		) % pow(10, self::iOtpLength);
	}
}
