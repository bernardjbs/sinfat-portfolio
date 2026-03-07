<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'type',
        'topic',
        'model',
        'tokens_used',
        'status',
    ];
}
