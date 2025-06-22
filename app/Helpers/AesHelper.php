<?php

namespace App\Helpers;

class AesHelper
{
    // 256-bit key (32 bytes) - Use a secure key from .env in production
    private static $key = "1234567890abcdef1234567890abcdef1234567890abcdef12";
    private static $ivLength = 16; // AES block size for CBC mode

    /**
     * Encrypt JSON data using AES-256-CBC
     */
    public static function encrypt($jsonData)
    {
        $plainText = json_encode($jsonData);
        if ($plainText === false) {
            throw new \InvalidArgumentException('Failed to encode JSON data');
        }

        $iv = openssl_random_pseudo_bytes(self::$ivLength);

        $encrypted = openssl_encrypt(
            $plainText,
            'aes-256-cbc',
            self::$key,
            0,
            $iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed');
        }

        // Combine IV and encrypted data, then encode
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data to JSON using AES-256-CBC
     */
    public static function decrypt($base64Data)
    {
        $data = base64_decode($base64Data, true);
        if ($data === false || strlen($data) <= self::$ivLength) {
            throw new \InvalidArgumentException('Invalid encrypted data');
        }

        $iv = substr($data, 0, self::$ivLength);
        $encrypted = substr($data, self::$ivLength);

        $decrypted = openssl_decrypt(
            $encrypted,
            'aes-256-cbc',
            self::$key,
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