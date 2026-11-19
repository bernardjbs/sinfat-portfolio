<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotebookSnapshot extends Model
{
    // The factory's snapshot name: the minute it was taken, YYYY-MM-DD-HHMM
    public const STAMP = '/^\d{4}-\d{2}-\d{2}-\d{4}$/';

    protected $fillable = [
        'stamp',
        'html',
    ];
}
