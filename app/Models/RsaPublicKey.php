<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsaPublicKey extends Model
{
    use HasFactory;
    protected $table = 'rsa_public_keys';
    protected $fillable = [
        'key',
        'user_id',
        'auth_token'
    ];
}
