<?php

namespace App\Helpers;

class TOTPHelper
{
    /**
     * Generates a 16-character Base32 secret key.
     *
     * @param int $length
     * @return string
     */
    public static function generateSecretKey($length = 16): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Decode a base32 string to binary.
     *
     * @param string $base32
     * @return string
     */
    public static function base32Decode(string $base32): string
    {
        $base32 = strtoupper($base32);
        if (!preg_match('/^[A-Z2-7]+$/', $base32)) {
            return '';
        }
        
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buf = 0;
        $len = 0;
        $decoded = '';
        
        for ($i = 0; $i < strlen($base32); $i++) {
            $c = $base32[$i];
            $val = strpos($alphabet, $c);
            $buf = ($buf << 5) | $val;
            $len += 5;
            if ($len >= 8) {
                $len -= 8;
                $decoded .= chr(($buf >> $len) & 0xFF);
            }
        }
        return $decoded;
    }

    /**
     * Calculate HOTP code for a specific time counter.
     *
     * @param string $secret
     * @param int|null $timeSlice
     * @return string|bool
     */
    public static function getCode(string $secret, ?int $timeSlice = null): string|bool
    {
        if ($timeSlice === null) {
            $timeSlice = (int) floor(time() / 30);
        }
        
        $secretKey = self::base32Decode($secret);
        if (!$secretKey) {
            return false;
        }
        
        // Pack time slice into binary 64-bit string
        $timeBin = pack('N*', 0) . pack('N*', $timeSlice);
        
        // Calculate HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBin, $secretKey, true);
        
        // Dynamic truncation
        $offset = ord($hash[19]) & 0xf;
        $otpBin = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );
        
        $otp = $otpBin % 1000000;
        return str_pad((string)$otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify TOTP code against a secret key within a discrepancy window.
     *
     * @param string $secret
     * @param string $code
     * @param int $discrepancy (timeslices of 30 seconds to allow for drift)
     * @return bool
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $currentTimeSlice = (int) floor(time() / 30);
        
        // Clean code input (remove spaces or dashes if any)
        $code = str_replace([' ', '-'], '', $code);
        if (strlen($code) !== 6 || !is_numeric($code)) {
            return false;
        }

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if ($calculatedCode !== false && hash_equals($calculatedCode, $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate dynamic QR code URL based on standard OTP provisioning URI.
     *
     * @param string $email
     * @param string $secret
     * @param string $issuer
     * @return string
     */
    public static function getQRCodeUrl(string $email, string $secret, string $issuer = 'Wires4USDT'): string
    {
        $data = 'otpauth://totp/' . rawurlencode($issuer) . ':' . rawurlencode($email) . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . rawurlencode($data);
    }
}
