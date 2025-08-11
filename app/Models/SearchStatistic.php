<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchStatistic extends Model
{
    protected $fillable = [
        'member_id',
        'member_name' ,
        'member_type_id',
        'nim',
        'nama',
        'judul_ta', 
        'program_studi', 
        'fakultas'
    ];
}
