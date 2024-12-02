<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Encryptable;

    protected $fillable = [
        'username',
        'password',
        'balance',
        'public_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // 'username' => 'encrypted',
    ];
    protected $with = [
        'rsaPublicKeys'
    ];
    protected $encrypted = [
        'username',
        'balance',
        'public_key',
    ];

    public function rsaPublicKeys($auth_token=null){
        return $this->hasMany(RsaPublicKey::class)
        ->when($auth_token,fn($q)=>$q->where('auth_token',$auth_token));
    }
}
