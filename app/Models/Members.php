<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Members extends Authenticatable
{
    protected $connection = 'mysql2'; // pakai koneksi kedua
    protected $table = 'member';
    protected $primaryKey = 'member_id';
    public $timestamps = false; 
}
