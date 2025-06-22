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
            self::$key = env('AES_KEY', 'e8c23aed79a443415e42f90cc6db4a0a');
            if (strlen(self::$key) !== 32) {
                throw new \RuntimeException('AES_KEY must be a 32-byte (256-bit) key');
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