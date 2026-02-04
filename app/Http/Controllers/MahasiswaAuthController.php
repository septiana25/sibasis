<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MahasiswaAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('mahasiswa.login');
    }

    public function login(Request $request)
    {
        $request->validate(['nim' => 'required|numeric:strict']);

        $nim = $request->nim;

        $response = Http::get("https://api-univ.ac.id/mahasiswa/{$nim}");

        if ($response->successful() && $response->json('status') === 'aktif') {
            Session::put('nim', $nim);
            return redirect()->route('skripsi.index');
        }

        return back()->withErrors(['nim' => 'NIM tidak ditemukan atau tidak aktif']);
    }

    public function logout()
    {
        Session::forget('nim');
        return redirect()->route('mahasiswa.login');
    }
}
