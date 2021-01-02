<?php
/**
 * Akeeba Restore
 * An AJAX-powered archive extraction library for JPA, JPS and ZIP archives
 *
 * @package   restore
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * AES implementation in PHP (c) Chris Veness 2005-2016.
 * Right to use and adapt is granted for under a simple creative commons attribution
 * licence. No warranty of any form is offered.
 *
 * Heavily modified for Akeeba Backup by Nicholas K. Dionysopoulos
 * Also added AES-128 CBC mode (with mcrypt and OpenSSL) on top of AES CTR
 * Removed CTR encrypt / decrypt (no longer used)
 */
class AKEncryptionAES
{
	// Sbox is pre-computed multiplicative inverse in GF(2^8) used in SubBytes and KeyExpansion [�5.1.1]
	protected static $Sbox =
		array(0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,
			0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,
			0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,
			0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,
			0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,
			0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,
			0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,
			0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,
			0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,
			0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,
			0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,
			0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,
			0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,
			0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,
			0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,
			0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16);

	// Rcon is Round Constant used for the Key Expansion [1st col is 2^(r-1) in GF(2^8)] [�5.2]
	protected static $Rcon = array(
		array(0x00, 0x00, 0x00, 0x00),
		array(0x01, 0x00, 0x00, 0x00),
		array(0x02, 0x00, 0x00, 0x00),
		array(0x04, 0x00, 0x00, 0x00),
		array(0x08, 0x00, 0x00, 0x00),
		array(0x10, 0x00, 0x00, 0x00),
		array(0x20, 0x00, 0x00, 0x00),
		array(0x40, 0x00, 0x00, 0x00),
		array(0x80, 0x00, 0x00, 0x00),
		array(0x1b, 0x00, 0x00, 0x00),
		array(0x36, 0x00, 0x00, 0x00));

	protected static $passwords = array();

	/**
	 * The algorithm to use for PBKDF2. Must be a supported hash_hmac algorithm. Default: sha1
	 *
	 * @var  string
	 */
	private static $pbkdf2Algorithm = 'sha1';

	/**
	 * Number of iterations to use for PBKDF2
	 *
	 * @var  int
	 */
	private static $pbkdf2Iterations = 1000;

	/**
	 * Should we use a static salt for PBKDF2?
	 *
	 * @var  int
	 */
	private static $pbkdf2UseStaticSalt = 0;

	/**
	 * The static salt to use for PBKDF2
	 *
	 * @var  string
	 */
	private static $pbkdf2StaticSalt = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

	/**
	 * AES Cipher function: encrypt 'input' with Rijndael algorithm
	 *
	 * @param   array $input    Message as byte-array (16 bytes)
	 * @param   array $w        key schedule as 2D byte-array (Nr+1 x Nb bytes) -
	 *                          generated from the cipher key by KeyExpansion()
	 *
	 * @return  string  Ciphertext as byte-array (16 bytes)
	 */
	protected static function Cipher($input, $w)
	{
		// main Cipher function [�5.1]
		$Nb = 4;                 // block size (in words): no of columns in state (fixed at 4 for AES)
		$Nr = count($w) / $Nb - 1; // no of rounds: 10/12/14 for 128/192/256-bit keys

		$state = array();  // initialise 4xNb byte-array 'state' with input [�3.4]

		for ($i = 0; $i < 4 * $Nb; $i++)
		{
			$state[$i % 4][floor($i / 4)] = $input[$i];
		}

		$state = self::AddRoundKey($state, $w, 0, $Nb);

		for ($round = 1; $round < $Nr; $round++)
		{  // apply Nr rounds
			$state = self::SubBytes($state, $Nb);
			$state = self::ShiftRows($state, $Nb);
			$state = self::MixColumns($state);
			$state = self::AddRoundKey($state, $w, $round, $Nb);
		}

		$state = self::SubBytes($state, $Nb);
		$state = self::ShiftRows($state, $Nb);
		$state = self::AddRoundKey($state, $w, $Nr, $Nb);

		$output = array(4 * $Nb);  // convert state to 1-d array before returning [�3.4]

		for ($i = 0; $i < 4 * $Nb; $i++)
		{
			$output[$i] = $state[$i % 4][floor($i / 4)];
		}

		return $output;
	}

	protected static function AddRoundKey($state, $w, $rnd, $Nb)
	{
		// xor Round Key into state S [�5.1.4]
		for ($r = 0; $r < 4; $r++)
		{
			for ($c = 0; $c < $Nb; $c++)
			{
				$state[$r][$c] ^= $w[$rnd * 4 + $c][$r];
			}
		}

		return $state;
	}

	protected static function SubBytes($s, $Nb)
	{
		// apply SBox to state S [�5.1.1]
		for ($r = 0; $r < 4; $r++)
		{
			for ($c = 0; $c < $Nb; $c++)
			{
				$s[$r][$c] = self::$Sbox[$s[$r][$c]];
			}
		}

		return $s;
	}

	protected static function ShiftRows($s, $Nb)
	{
		// shift row r of state S left by r bytes [�5.1.2]
		$t = array(4);

		for ($r = 1; $r < 4; $r++)
		{
			for ($c = 0; $c < 4; $c++)
			{
				$t[$c] = $s[$r][($c + $r) % $Nb];
			}  // shift into temp copy

			for ($c = 0; $c < 4; $c++)
			{
				$s[$r][$c] = $t[$c];
			}         // and copy back
		}          // note that this will work for Nb=4,5,6, but not 7,8 (always 4 for AES):

		return $s;  // see fp.gladman.plus.com/cryptography_technology/rijndael/aes.spec.311.pdf
	}

	protected static function MixColumns($s)
	{
		// combine bytes of each col of state S [�5.1.3]
		for ($c = 0; $c < 4; $c++)
		{
			$a = array(4);  // 'a' is a copy of the current column from 's'
			$b = array(4);  // 'b' is a�{02} in GF(2^8)

			for ($i = 0; $i < 4; $i++)
			{
				$a[$i] = $s[$i][$c];
				$b[$i] = $s[$i][$c] & 0x80 ? $s[$i][$c] << 1 ^ 0x011b : $s[$i][$c] << 1;
			}

			// a[n] ^ b[n] is a�{03} in GF(2^8)
			$s[0][$c] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3]; // 2*a0 + 3*a1 + a2 + a3
			$s[1][$c] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3]; // a0 * 2*a1 + 3*a2 + a3
			$s[2][$c] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3]; // a0 + a1 + 2*a2 + 3*a3
			$s[3][$c] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3]; // 3*a0 + a1 + a2 + 2*a3
		}

		return $s;
	}

	/**
	 * Key expansion for Rijndael Cipher(): performs key expansion on cipher key
	 * to generate a key schedule
	 *
	 * @param   array $key Cipher key byte-array (16 bytes)
	 *
	 * @return  array  Key schedule as 2D byte-array (Nr+1 x Nb bytes)
	 */
	protected static function KeyExpansion($key)
	{
		// generate Key Schedule from Cipher Key [�5.2]

		// block size (in words): no of columns in state (fixed at 4 for AES)
		$Nb = 4;
		// key length (in words): 4/6/8 for 128/192/256-bit keys
		$Nk = (int) (count($key) / 4);
		// no of rounds: 10/12/14 for 128/192/256-bit keys
		$Nr = $Nk + 6;

		$w    = array();
		$temp = array();

		for ($i = 0; $i < $Nk; $i++)
		{
			$r     = array($key[4 * $i], $key[4 * $i + 1], $key[4 * $i + 2], $key[4 * $i + 3]);
			$w[$i] = $r;
		}

		for ($i = $Nk; $i < ($Nb * ($Nr + 1)); $i++)
		{
			$w[$i] = array();
			for ($t = 0; $t < 4; $t++)
			{
				$temp[$t] = $w[$i - 1][$t];
			}
			if ($i % $Nk == 0)
			{
				$temp = self::SubWord(self::RotWord($temp));
				for ($t = 0; $t < 4; $t++)
				{
					$rConIndex = (int) ($i / $Nk);
					$temp[$t] ^= self::$Rcon[$rConIndex][$t];
				}
			}
			else if ($Nk > 6 && $i % $Nk == 4)
			{
				$temp = self::SubWord($temp);
			}
			for ($t = 0; $t < 4; $t++)
			{
				$w[$i][$t] = $w[$i - $Nk][$t] ^ $temp[$t];
			}
		}

		return $w;
	}

	protected static function SubWord($w)
	{
		// apply SBox to 4-byte word w
		for ($i = 0; $i < 4; $i++)
		{
			$w[$i] = self::$Sbox[$w[$i]];
		}

		return $w;
	}

	/*
	 * Unsigned right shift function, since PHP has neither >>> operator nor unsigned ints
	 *
	 * @param a  number to be shifted (32-bit integer)
	 * @param b  number of bits to shift a to the right (0..31)
	 * @return   a right-shifted and zero-filled by b bits
	 */

	protected static function RotWord($w)
	{
		// rotate 4-byte word w left by one byte
		$tmp = $w[0];
		for ($i = 0; $i < 3; $i++)
		{
			$w[$i] = $w[$i + 1];
		}
		$w[3] = $tmp;

		return $w;
	}

	protected static function urs($a, $b)
	{
		$a &= 0xffffffff;
		$b &= 0x1f;  // (bounds check)
		if ($a & 0x80000000 && $b > 0)
		{   // if left-most bit set
			$a = ($a >> 1) & 0x7fffffff;   //   right-shift one bit & clear left-most bit
			$a = $a >> ($b - 1);           //   remaining right-shifts
		}
		else
		{                       // otherwise
			$a = ($a >> $b);               //   use normal right-shift
		}

		return $a;
	}

	/**
	 * AES decryption in CBC mode. This is the standard mode (the CTR methods
	 * actually use Rijndael-128 in CTR mode, which - technically - isn't AES).
	 *
	 * It supports AES-128 only. It assumes that the last 4 bytes
	 * contain a little-endian unsigned long integer representing the unpadded
	 * data length.
	 *
	 * @since  3.0.1
	 * @author Nicholas K. Dionysopoulos
	 *
	 * @param   string $ciphertext The data to encrypt
	 * @param   string $password   Encryption password
	 *
	 * @return  string  The plaintext
	 */
	public static function AESDecryptCBC($ciphertext, $password)
	{
		$adapter = self::getAdapter();

		if (!$adapter->isSupported())
		{
			return false;
		}

		// Read the data size
		$data_size = unpack('V', substr($ciphertext, -4));

		// Do I have a PBKDF2 salt?
		$salt             = substr($ciphertext, -92, 68);
		$rightStringLimit = -4;

		$params        = self::getKeyDerivationParameters();
		$keySizeBytes  = $params['keySize'];
		$algorithm     = $params['algorithm'];
		$iterations    = $params['iterations'];
		$useStaticSalt = $params['useStaticSalt'];

		if (substr($salt, 0, 4) == 'JPST')
		{
			// We have a stored salt. Retrieve it and tell decrypt to process the string minus the last 44 bytes
			// (4 bytes for JPST, 16 bytes for the salt, 4 bytes for JPIV, 16 bytes for the IV, 4 bytes for the
			// uncompressed string length - note that using PBKDF2 means we're also using a randomized IV per the
			// format specification).
			$salt             = substr($salt, 4);
			$rightStringLimit -= 68;

			$key          = self::pbkdf2($password, $salt, $algorithm, $iterations, $keySizeBytes);
		}
		elseif ($useStaticSalt)
		{
			// We have a static salt. Use it for PBKDF2.
			$key = self::getStaticSaltExpandedKey($password);
		}
		else
		{
			// Get the expanded key from the password. THIS USES THE OLD, INSECURE METHOD.
			$key = self::expandKey($password);
		}

		// Try to get the IV from the data
		$iv               = substr($ciphertext, -24, 20);

		if (substr($iv, 0, 4) == 'JPIV')
		{
			// We have a stored IV. Retrieve it and tell mdecrypt to process the string minus the last 24 bytes
			// (4 bytes for JPIV, 16 bytes for the IV, 4 bytes for the uncompressed string length)
			$iv               = substr($iv, 4);
			$rightStringLimit -= 20;
		}
		else
		{
			// No stored IV. Do it the dumb way.
			$iv = self::createTheWrongIV($password);
		}

		// Decrypt
		$plaintext = $adapter->decrypt($iv . substr($ciphertext, 0, $rightStringLimit), $key);

		// Trim padding, if necessary
		if (strlen($plaintext) > $data_size)
		{
			$plaintext = substr($plaintext, 0, $data_size);
		}

		return $plaintext;
	}

	/**
	 * That's the old way of creating an IV that's definitely not cryptographically sound.
	 *
	 * DO NOT USE, EVER, UNLESS YOU WANT TO DECRYPT LEGACY DATA
	 *
	 * @param   string $password The raw password from which we create an IV in a super bozo way
	 *
	 * @return  string  A 16-byte IV string
	 */
	public static function createTheWrongIV($password)
	{
		static $ivs = array();

		$key = md5($password);

		if (!isset($ivs[$key]))
		{
			$nBytes  = 16;  // AES uses a 128 -bit (16 byte) block size, hence the IV size is always 16 bytes
			$pwBytes = array();
			for ($i = 0; $i < $nBytes; $i++)
			{
				$pwBytes[$i] = ord(substr($password, $i, 1)) & 0xff;
			}
			$iv    = self::Cipher($pwBytes, self::KeyExpansion($pwBytes));
			$newIV = '';
			foreach ($iv as $int)
			{
				$newIV .= chr($int);
			}

			$ivs[$key] = $newIV;
		}

		return $ivs[$key];
	}

	/**
	 * Expand the password to an appropriate 128-bit encryption key
	 *
	 * @param   string $password
	 *
	 * @return  string
	 *
	 * @since   5.2.0
	 * @author  Nicholas K. Dionysopoulos
	 */
	public static function expandKey($password)
	{
		// Try to fetch cached key or create it if it doesn't exist
		$nBits     = 128;
		$lookupKey = md5($password . '-' . $nBits);

		if (array_key_exists($lookupKey, self::$passwords))
		{
			$key = self::$passwords[$lookupKey];

			return $key;
		}

		// use AES itself to encrypt password to get cipher key (using plain password as source for
		// key expansion) - gives us well encrypted key.
		$nBytes  = $nBits / 8; // Number of bytes in key
		$pwBytes = array();

		for ($i = 0; $i < $nBytes; $i++)
		{
			$pwBytes[$i] = ord(substr($password, $i, 1)) & 0xff;
		}

		$key    = self::Cipher($pwBytes, self::KeyExpansion($pwBytes));
		$key    = array_merge($key, array_slice($key, 0, $nBytes - 16)); // expand key to 16/24/32 bytes long
		$newKey = '';

		foreach ($key as $int)
		{
			$newKey .= chr($int);
		}

		$key = $newKey;

		self::$passwords[$lookupKey] = $key;

		return $key;
	}

	/**
	 * Returns the correct AES-128 CBC encryption adapter
	 *
	 * @return  AKEncryptionAESAdapterInterface
	 *
	 * @since   5.2.0
	 * @author  Nicholas K. Dionysopoulos
	 */
	public static function getAdapter()
	{
		static $adapter = null;

		if (is_object($adapter) && ($adapter instanceof AKEncryptionAESAdapterInterface))
		{
			return $adapter;
		}

		$adapter = new OpenSSL();

		if (!$adapter->isSupported())
		{
			$adapter = new Mcrypt();
		}

		return $adapter;
	}

	/**
	 * @return string
	 */
	public static function getPbkdf2Algorithm()
	{
		return self::$pbkdf2Algorithm;
	}

	/**
	 * @param string $pbkdf2Algorithm
	 * @return void
	 */
	public static function setPbkdf2Algorithm($pbkdf2Algorithm)
	{
		self::$pbkdf2Algorithm = $pbkdf2Algorithm;
	}

	/**
	 * @return int
	 */
	public static function getPbkdf2Iterations()
	{
		return self::$pbkdf2Iterations;
	}

	/**
	 * @param int $pbkdf2Iterations
	 * @return void
	 */
	public static function setPbkdf2Iterations($pbkdf2Iterations)
	{
		self::$pbkdf2Iterations = $pbkdf2Iterations;
	}

	/**
	 * @return int
	 */
	public static function getPbkdf2UseStaticSalt()
	{
		return self::$pbkdf2UseStaticSalt;
	}

	/**
	 * @param int $pbkdf2UseStaticSalt
	 * @return void
	 */
	public static function setPbkdf2UseStaticSalt($pbkdf2UseStaticSalt)
	{
		self::$pbkdf2UseStaticSalt = $pbkdf2UseStaticSalt;
	}

	/**
	 * @return string
	 */
	public static function getPbkdf2StaticSalt()
	{
		return self::$pbkdf2StaticSalt;
	}

	/**
	 * @param string $pbkdf2StaticSalt
	 * @return void
	 */
	public static function setPbkdf2StaticSalt($pbkdf2StaticSalt)
	{
		self::$pbkdf2StaticSalt = $pbkdf2StaticSalt;
	}

	/**
	 * Get the parameters fed into PBKDF2 to expand the user password into an encryption key. These are the static
	 * parameters (key size, hashing algorithm and number of iterations). A new salt is used for each encryption block
	 * to minimize the risk of attacks against the password.
	 *
	 * @return  array
	 */
	public static function getKeyDerivationParameters()
	{
		return array(
			'keySize'       => 16,
			'algorithm'     => self::$pbkdf2Algorithm,
			'iterations'    => self::$pbkdf2Iterations,
			'useStaticSalt' => self::$pbkdf2UseStaticSalt,
			'staticSalt'    => self::$pbkdf2StaticSalt,
		);
	}

	/**
	 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 * Modified for Akeeba Engine by Akeeba Ltd (removed unnecessary checks to make it faster)
	 *
	 * @param   string  $password    The password.
	 * @param   string  $salt        A salt that is unique to the password.
	 * @param   string  $algorithm   The hash algorithm to use. Default is sha1.
	 * @param   int     $count       Iteration count. Higher is better, but slower. Default: 1000.
	 * @param   int     $key_length  The length of the derived key in bytes.
	 *
	 * @return  string  A string of $key_length bytes
	 */
	public static function pbkdf2($password, $salt, $algorithm = 'sha1', $count = 1000, $key_length = 16)
	{
		if (function_exists("hash_pbkdf2"))
		{
			return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, true);
		}

		$hash_length = akstringlen(hash($algorithm, "", true));
		$block_count = ceil($key_length / $hash_length);

		$output = "";

		for ($i = 1; $i <= $block_count; $i++)
		{
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack("N", $i);

			// First iteration
			$xorResult = hash_hmac($algorithm, $last, $password, true);
			$last      = $xorResult;

			// Perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j++)
			{
				$last = hash_hmac($algorithm, $last, $password, true);
				$xorResult ^= $last;
			}

			$output .= $xorResult;
		}

		return aksubstr($output, 0, $key_length);
	}

	/**
	 * Get the expanded key from the user supplied password using a static salt. The results are cached for performance
	 * reasons.
	 *
	 * @param   string  $password  The user-supplied password, UTF-8 encoded.
	 *
	 * @return  string  The expanded key
	 */
	private static function getStaticSaltExpandedKey($password)
	{
		$params        = self::getKeyDerivationParameters();
		$keySizeBytes  = $params['keySize'];
		$algorithm     = $params['algorithm'];
		$iterations    = $params['iterations'];
		$staticSalt    = $params['staticSalt'];

		$lookupKey = "PBKDF2-$algorithm-$iterations-" . md5($password . $staticSalt);

		if (!array_key_exists($lookupKey, self::$passwords))
		{
			self::$passwords[$lookupKey] = self::pbkdf2($password, $staticSalt, $algorithm, $iterations, $keySizeBytes);
		}

		return self::$passwords[$lookupKey];
	}

}
