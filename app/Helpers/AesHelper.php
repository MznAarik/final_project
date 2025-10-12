<?php

namespace App\Helpers;

class AesHelper
{
    private static $key = null;
    private static $ivLength = 16; // AES block size for CBC mode

    /**
     * Get the encryption key from .env, with a fallback for development
     */
    private static function getKey()
    {
        if (self::$key === null) {
            $keyHex = env('AES_KEY', 'ff627acaa4db8e8e114e3baf33fdc1ac8970056f229d4b463f5660db04982192');
            self::$key = hex2bin($keyHex);

            if (self::$key === false || mb_strlen(self::$key, '8bit') !== 32) {
                throw new \RuntimeException('AES_KEY must be a valid 64-character hex string representing a 32-byte (256-bit) key');
            }
        }
        return self::$key;
    }


    /**
     * Encrypt JSON data using AES-256-CBC
     */
    public static function encrypt($jsonData)
    {
        $plainText = json_encode($jsonData);
        if ($plainText === false) {
            throw new \InvalidArgumentException('Failed to encode JSON data');
        }

        $key = self::getKey();
        $iv = openssl_random_pseudo_bytes(self::$ivLength);

        $encrypted = openssl_encrypt(
            $plainText,
            'aes-256-cbc',
            $key,
            0,
            $iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed');
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data to JSON using AES-256-CBC
     */
    public static function decrypt($base64Data)
    {
        // $base64Data = str_replace(' ', '', $base64Data); // Remove space(s)
        $data = base64_decode($base64Data, true);
        if ($data === false || strlen($data) <= self::$ivLength) {
            throw new \InvalidArgumentException('Invalid encrypted data');
        }

        $key = self::getKey();
        $iv = substr($data, 0, self::$ivLength);
        $encrypted = substr($data, self::$ivLength);

        $decrypted = openssl_decrypt(
            $encrypted,
            'aes-256-cbc',
            $key,
            0,
            $iv
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Decryption failed');
        }

        $jsonData = json_decode($decrypted, true);
        if ($jsonData === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Failed to decode JSON data: ' . json_last_error_msg());
        }

        return $jsonData;
    }
}