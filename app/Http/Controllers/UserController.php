<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User\User;

class UserController extends Controller
{
    public function user(){
        $data = User::all(); // otomatis ambil dari mysql2
        return view('login.index', compact('data'));
    }
}
