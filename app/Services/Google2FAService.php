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
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            rawurlencode($issuer),
            rawurlencode($email),
            $secret,
            rawurlencode($issuer)
        );
    }

    /**
     * URL de imagen QR lista para usar (un solo nivel de codificación).
     */
    public static function getQRCodeImageUrl($name, $email, $secret, $issuer = 'Tactic Force', int $size = 200): string
    {
        $otpauth = self::getQRCodeUrl($name, $email, $secret, $issuer);

        return 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.rawurlencode($otpauth);
    }

    /**
     * Normaliza el código ingresado por el usuario (solo 6 dígitos).
     */
    public static function normalizeCode(?string $code): string
    {
        return substr(preg_replace('/\D/', '', (string) $code), 0, 6);
    }

    /**
     * Verifies a 6-digit TOTP code against a Base32 secret.
     */
    public static function verifyCode($secret, $code, $discrepancy = 2)
    {
        $code = self::normalizeCode($code);

        if (strlen($code) !== 6) {
            return false;
        }

        $secret = strtoupper(str_replace(' ', '', (string) $secret));

        // Get the 30-second time slice index
        $currentTimeSlice = (int) floor(time() / 30);

        // Allow clock skew (default ±60s)
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
        $timeBin = pack('N*', 0).pack('N*', (int) $timeSlice);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBin, $secretKey, true);

        // Dynamic Truncation
        $offset = ord($hash[19]) & 0xF;
        $truncatedHash = (
            (ord($hash[$offset]) & 0x7F) << 24 |
            (ord($hash[$offset + 1]) & 0xFF) << 16 |
            (ord($hash[$offset + 2]) & 0xFF) << 8 |
            (ord($hash[$offset + 3]) & 0xFF)
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

        if (! preg_match('/^[A-Z2-7]+$/', $base32)) {
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
