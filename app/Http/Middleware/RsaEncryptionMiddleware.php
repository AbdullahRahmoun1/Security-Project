<?php
namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RsaEncryptionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Paths to the server's RSA keys
        $serverPrivateKey = Storage::get('private.pem');

        // Get user public key (Assuming you're using the first user for demonstration)
        $userPublicKey = null;
        if (true) { // Replace with proper condition
            $id = User::first()->id; // Get the user ID
            $userPublicKey = Storage::get("keys/{$id}_public.pem");
        }

        // Decrypt incoming request (User → Server)
        if ($request->has('encryptedData')) {
            $encryptedData = base64_decode($request->input('encryptedData'));
            // Decrypt using server's private key
            $decryptedData = null;
            if (openssl_private_decrypt($encryptedData, $decryptedData, $serverPrivateKey)) {
                // Decode decrypted data (assuming it's base64-encoded JSON)
                $decodedData = json_decode(base64_decode($decryptedData), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Successfully decoded the data, merge it into the request
                    $request->merge(['data' => $decodedData]);
                } else {
                    // Handle the case where JSON is invalid
                    return response()->json(['error' => 'Invalid decrypted data format'], 400);
                }
            } else {
                // Handle decryption failure
                return response()->json(['error' => 'Decryption failed'], 400);
            }
        }

        // Proceed with the request and intercept the response
        $response = $next($request);

        // Encrypt outgoing response (Server → User)
        if ($response->getContent() && $userPublicKey) {
            $data = $response->getContent();

            // Encrypt using user's public key
            $encryptedData = null;
            if (openssl_public_encrypt($data, $encryptedData, $userPublicKey)) {
                // Return encrypted data in the response
                return response()->json(['encryptedData' => base64_encode($encryptedData)]);
            } else {
                // Handle encryption failure
                return response()->json(['error' => 'Encryption failed'], 500);
            }
        }

        return $response;
    }
}
