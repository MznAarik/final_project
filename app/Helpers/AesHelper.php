<?php

namespace App\Helpers;

class AesHelper
{
    // Fixed 128-bit key (16 bytes) for AES-128
    private static $key = "1234567890abcdef";

    private static $sBox = [
        0x63,
        0x7c,
        0x77,
        0x7b,
        0xf2,
        0x6b,
        0x6f,
        0xc5,
        0x30,
        0x01,
        0x67,
        0x2b,
        0xfe,
        0xd7,
        0xab,
        0x76,
    ];

    public static function encrypt($plainText)
    {
        $plainText = str_pad($plainText, 16, "\0");
        $key = self::$key;

        $state = array_values(unpack('C*', $plainText));
        $keyBytes = array_values(unpack('C*', $key));

        for ($i = 0; $i < 16; $i++) {
            $state[$i] ^= $keyBytes[$i];
        }

        for ($i = 0; $i < 16; $i++) {
            $state[$i] = self::$sBox[$state[$i] % count(self::$sBox)];
        }

        $cipherText = implode(array_map("chr", $state));
        return base64_encode($cipherText); // Encode for QR readability
    }

    public static function decrypt($cipherText)
    {
        $cipherText = base64_decode($cipherText);
        $key = self::$key;

        $state = array_values(unpack('C*', $cipherText));
        $keyBytes = array_values(unpack('C*', $key));

        // Reverse substitute bytes (inverse S-box needed, simplified here)
        for ($i = 0; $i < 16; $i++) {
            $state[$i] = array_search($state[$i], self::$sBox) ?: $state[$i]; // Rough inverse
        }

        // Reverse add round key
        for ($i = 0; $i < 16; $i++) {
            $state[$i] ^= $keyBytes[$i];
        }

        $plainText = implode(array_map("chr", $state));
        return rtrim($plainText, "\0"); // Remove padding
    }
}