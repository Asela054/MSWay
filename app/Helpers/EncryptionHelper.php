<?php

namespace App\Helpers;

class EncryptionHelper
{
    /**
     * Encrypt data using Laravel's encryptor
     */
    public static function encrypt($data)
    {
        return encrypt($data);
    }

    /**
     * Decrypt data using Laravel's decryptor
     */
    public static function decrypt($encryptedData)
    {
        return decrypt($encryptedData);
    }

    /**
     * Simple AES encryption (alternative method)
     */
    public static function encryptAES($data, $key = null)
    {
        if ($key === null) {
            $key = config('app.key');
        }

        $iv = random_bytes(16);
        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            hash('sha256', $key, true),
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $encrypted);
    }

    /**
     * AES decryption
     */
    public static function decryptAES($encryptedData, $key = null)
    {
        if ($key === null) {
            $key = config('app.key');
        }

        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        return openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            hash('sha256', $key, true),
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}