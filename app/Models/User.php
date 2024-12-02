<?php

namespace App\Models;

use App\Traits\Encryptable;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'public_key',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // 'username' => 'encrypted',
        'balance'   => 'encrypted' ,
        'public_key'=> 'encrypted' ,
    ];
    protected $with = [
        'rsaPublicKeys'
    ];

    // public function publicKey():Attribute{
    //     return Attribute::make(
    //         set: function($value){
    //             try {
    //                 // Decode and decompress
    //                 $originalString = decompressString($value);
    //                 return $originalString;
    //             } catch (Exception $e) {
    //                 throwError("Failed to decompress public_key");
    //             }
    //         }
    //     );
    // }

    public function rsaPublicKeys($auth_token=null){
        return $this->hasMany(RsaPublicKey::class)
        ->when($auth_token,fn($q)=>$q->where('auth_token',$auth_token));
    }

    public function transactions(){
        return $this->hasMany(Transaction::class)
;    }
}
