<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()  {
        $cards = [
            [
                'title' => 'Total Skripsi',
                'icons' => [
                    'icon' => 'journals',
                    'bg' => 'success-subtle',
                    'color' => 'success'
                ],
                'total' => 200
            ],
            [
                'title' => 'Total Membaca 2025',
                'icons' => [
                    'icon' => 'book-half',
                    'bg' => 'success-subtle',
                    'color' => 'success'
                ],
                'total' => 200
            ],
            [
                'title' => 'Total Member Aktif 2025',
                'icons' => [
                    'icon' => 'person-circle',
                    'bg' => 'success-subtle',
                    'color' => 'success'
                ],
                'total' => 200
            ],
        ];
        return view('admin.index', compact('cards'));
    }
}
