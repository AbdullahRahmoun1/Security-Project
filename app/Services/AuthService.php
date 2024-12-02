<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class AuthService
{
    protected $rsaPublicKeyService;
    public function __construct(RsaPublicKeyService $rsaPublicKeyService)
    {
        $this->rsaPublicKeyService = $rsaPublicKeyService;
    }
    public function register($data): array
    {
        $user = User::create($data);
        $tokenData=$this->validateCountAndCreateToken($user);
        $this->rsaPublicKeyService->attachToOwner($user, $data['public_key'],$user->tokens->first());
        $user->refresh();
        return [
            'user' => $user,
        ]+$tokenData;
    }
    public function login($data)
    {
        $public_key = $data['public_key'];
        unset($data['public_key']);
        if (!auth()->attempt($data)) {
            throwError(
                "Wrong Credentials"
            );
        }
        $user = request()->user();
        $result = [
            'user' => $user,
        ];
        $result ['token'] = $this->validateCountAndCreateToken($user);
        $this->rsaPublicKeyService->attachToOwner($user, $public_key ,$user->tokens->last());
        return $result;
    }

    public function validateCountAndCreateToken(Authenticatable $authenticatable)
    {
        if ($authenticatable->tokens()->count() >= 6) {
            $token = $authenticatable->tokens()->orderBy('created_at')->first();
            if($authenticatable instanceof User){
                $authenticatable->rsaPublicKeys()->where('auth_token', $token->token)->delete();
            }
            $token->delete();
        }
        $type = 'user';
        $token = $authenticatable
        ->createToken(request()->ip(), [$type]);
        $plainToken = $token->plainTextToken;
        return [
            'token' => $plainToken,
            'type' => $type
        ];
    }
}
