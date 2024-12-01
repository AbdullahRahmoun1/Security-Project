<?php

namespace App\Classes;

use Exception;
use Illuminate\Support\Facades\Storage;

class RsaKeyGenerator
{
    public static function generate($userId)
    {
        try{
            $config = [
                "private_key_bits" => 2048,  // Key size
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ];
            // Generate a new private key
            $keyResource = openssl_pkey_new($config);
            // Extract private key
            openssl_pkey_export($keyResource, $privateKey);
            // Extract public key
            $keyDetails = openssl_pkey_get_details($keyResource);
            $publicKey = $keyDetails['key'];

            // Save the keys (e.g., in files or the database)
            Storage::put("keys/{$userId}_private.pem", $privateKey);
            Storage::put("keys/{$userId}_public.pem", $publicKey);

        }catch(Exception $e){
            throw new Exception("Failed to create RSA keys for user with id $userId");
        }
        return [
            'private_key' => $privateKey,
            'public_key' => $publicKey,
        ];
    }
}
