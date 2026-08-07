<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrosoftToken extends Model
{
    use HasFactory;

    protected $table = 'microsoft_tokens';

    protected $fillable = [
        'organization_id',
        'access_token',
        'refresh_token',
        'expires_in',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];
}
