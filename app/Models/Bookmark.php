<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
        'member_id',
        'nim',
        'member_name',
        'judul_ta',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
