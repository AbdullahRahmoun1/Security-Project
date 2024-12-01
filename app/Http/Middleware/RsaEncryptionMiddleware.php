<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RsaEncryptionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure the request contains encrypted data
        if (!$request->has('encryptedData')) {
            return response()->json(['message' => 'Encrypted data is required'], 400);
        }

        // First Layer: Decrypt incoming encrypted data using server's private key
        $serverPrivateKey = Storage::get('private.pem');
        if (!$serverPrivateKey) {
            return response()->json(['message' => 'Server private key not found'], 500);
        }

        if ($request->has('encryptedData')) {
            $encryptedData = base64_decode($request->input('encryptedData'));
            $decryptedData = null;
            if (!openssl_private_decrypt($encryptedData, $decryptedData, $serverPrivateKey)) {
                $error = openssl_error_string(); // Capture error string from OpenSSL
                return response()->json(['message' => 'Decryption failed: ' . $error], 400);
            }
            $decodedData = json_decode($decryptedData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Invalid decrypted data format'], 400);
            }

            $request->merge(['data' => $decodedData]);
        }

        // Request processing
        $response = $next($request);

        // Second Layer: Encrypt response data using user's public key
        if (!$request->has('data.public_key')) {
            return response()->json(['message' => "Couldn't encrypt data. public_key required"], 400);
        }

        // $userPublicKey = $request->input('data.public_key');
        $userPublicKey = "-----BEGIN PUBLIC KEY-----\nMIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKCAYEA18fI+meyh8hL2uLOvjDskU/QBmSbkb5IA29njNKHCrtsokCyR5VNOf/lGry2PTHj8TCWR1HgkfX4vTUlTRa1GcBBk0J00kK4p0S3bkQZGNbRDgi52tp0wN7FVWGJlkuhdOnCBtJCt2gdyT831CtFo6FBs+XvA1Gd6xMZ+7TiG0MLTcIDJcOkDIWRrGx5Pq1/FKMfT+L05Kv5a0wevXa2ReB0OZJFcR1f8y2nLSH75onsG73s9loZ6RAUEdnYeuFMOtRpPS50mm9U6UjmoNF7fmt/+KW7NEv9yvTCZTuqRyqy07o+sE9H0IaHwv3QwtI4nWRLc89V8K6IFXj+4uIpID/RfQm6Z++7wUeXRJ9nk0ao62RFCTWxpGPsly+kHOTe1XeSwkkSEUO8hdlQuz/GanceRl4a+deDWNH1KkLo6LIWwXz1secZogdBTCsgQMR5b5krAnQ2LuPt5rPAYOVIvAocgpzqJ2qxM4jYg05N+HSkdeuLkEsDIASHyS4IjkhZAgMBAAE=\n-----END PUBLIC KEY-----";
        if ($userPublicKey) {
            $responseContent =$response->getContent();
            $encryptedData = null;
            // Verify the public key
            if (!$publicKeyResource = openssl_pkey_get_public($userPublicKey)) {
                return response()->json(['message' => 'Invalid public key format'], 400);
            }

            // Attempt to encrypt the response data
            if (!openssl_public_encrypt($responseContent, $encryptedData, $publicKeyResource)) {
                $error = openssl_error_string(); // Capture error string from OpenSSL
                return response()->json(['message' => 'Encryption failed: ' . $error], 400);
            }

            return response()->json(['encryptedData' => base64_encode($encryptedData)]);
        }

        return $response;
    }
}
