<?php

namespace App\Models\user;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mysql2'; // koneksi ke database kedua
    protected $table = 'user';
}
