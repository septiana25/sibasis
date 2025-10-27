<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViolationController extends Controller
{
    public function log(Request $request)
    {
        $member = Auth::guard('member')->user();
        $count = $request->count ?? 1;

        // Simpan log pelanggaran ke database
        ActivityLog::create([
            'member_id' => $member ? $member->member_id : null,
            'member_name' => $member ? $member->member_name : null,
            'action' => 'attempt_copy_or_inspect',
            'count' => $count
        ]);

        // Jika pelanggaran >= 3 kali, logout dan redirect
        if ($count >= 3 && $member) {
            Auth::guard('member')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['redirect' => route('login')]);
        }

        return response()->json(['ok' => true]);
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
