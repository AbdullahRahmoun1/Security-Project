<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'balance',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance'   => 'encrypted' ,
    ];
    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (is_null($model->balance)) {
            $model->balance = '0.0'; // Replace with your default value
        }
    });
}

    public function rsaPublicKeys($auth_token=null){
        return $this->hasMany(RsaPublicKey::class)
        ->when($auth_token,fn($q)=>$q->where('auth_token',$auth_token));
    }
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}
