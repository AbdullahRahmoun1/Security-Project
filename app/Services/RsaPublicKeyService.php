<?php

namespace App\Services;
use App\Models\FirebaseToken;
use App\Models\RsaPublicKey;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RsaPublicKeyService
{
    public function attachToOwner(Authenticatable $owner,$public_key,$authToken=null){
        if($authToken==null){
            $authToken=$owner->currentAccessToken();
        }
        RsaPublicKey::create([
            'user_id' => $owner->id,
            'auth_token' => $authToken->token,
            'key' => $public_key
        ]);
    }
}

