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
            $request->replace($decodedData);
        }

        // Request processing
        $response = $next($request);

        // Second Layer: Encrypt response data using user's public key
        $user = request()->user();
        if(!$user && !request('public_key')){
            return response()->json(['message' => 'Encryption failed: cant find a public_key to encrypt the data with it'], 400);
        }
        if(request('public_key')){
            $userPublicKey = request('public_key');
        }else{
            $userPublicKey = $user?->rsaPublicKeys($user->currentAccessToken()->token)->first();
        }
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
