<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestUsage extends Model
{
    use HasFactory;

    protected $table = 'guest_usage';

    protected $fillable = [
        'ip_address',
        'topic',
        'model',
        'used_own_key',
        'status',
        'tokens_used',
    ];

    protected $casts = [
        'used_own_key' => 'boolean',
    ];
}
