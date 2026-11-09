<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    protected $fillable = [
        'body',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }
}