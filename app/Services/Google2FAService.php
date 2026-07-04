<?php

namespace App\Services;

class Google2FAService
{
    private static $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generates a random Base32 secret key.
     */
    public static function generateSecretKey($length = 16)
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::$base32chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Generates the otpauth:// URI to scan with Authenticator apps.
     */
    public static function getQRCodeUrl($name, $email, $secret, $issuer = 'Tactic Force')
    {
        $label = rawurlencode($issuer . ':' . $email);
        $issuer = rawurlencode($issuer);
        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Verifies a 6-digit TOTP code against a Base32 secret.
     */
    public static function verifyCode($secret, $code, $discrepancy = 1)
    {
        // Clean any spaces
        $code = str_replace(' ', '', $code);

        if (strlen($code) !== 6 || !is_numeric($code)) {
            return false;
        }

        // Get the 30-second time slice index
        $currentTimeSlice = floor(time() / 30);

        // Allow a slight discrepancy of steps (default 1 = ±30s)
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculates the TOTP code for a specific time step.
     */
    private static function getCode($secret, $timeSlice)
    {
        $secretKey = self::base32Decode($secret);

        // Pack time slice into 8-byte big-endian binary string
        $timeBin = pack('N*', 0) . pack('N*', $timeSlice);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBin, $secretKey, true);

        // Dynamic Truncation
        $offset = ord($hash[19]) & 0xf;
        $truncatedHash = (
            (ord($hash[$offset]) & 0x7f) << 24 |
            (ord($hash[$offset + 1]) & 0xff) << 16 |
            (ord($hash[$offset + 2]) & 0xff) << 8 |
            (ord($hash[$offset + 3]) & 0xff)
        );

        $otp = $truncatedHash % 1000000;
        return str_pad($otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decodes a Base32 string to binary data.
     */
    private static function base32Decode($base32)
    {
        $base32 = strtoupper($base32);
        
        // Remove padding characters
        $base32 = str_replace('=', '', $base32);
        
        if (!preg_match('/^[A-Z2-7]+$/', $base32)) {
            throw new \Exception('Invalid base32 character.');
        }

        $binary = '';
        $lut = array_flip(str_split(self::$base32chars));

        $val = 0;
        $valLen = 0;
        
        for ($i = 0; $i < strlen($base32); $i++) {
            $val = ($val << 5) | $lut[$base32[$i]];
            $valLen += 5;
            if ($valLen >= 8) {
                $valLen -= 8;
                $binary .= chr(($val >> $valLen) & 0xFF);
            }
        }
        
        return $binary;
    }
}
