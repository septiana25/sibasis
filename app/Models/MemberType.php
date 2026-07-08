<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    protected $table = 'member_type';
    protected $fillable = [
        'member_type_id',
        'member_type_name'
    ];
}
