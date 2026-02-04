<?php

namespace App\Http\Controllers;

use App\Rules\ValidMember;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberAuthController extends Controller
{
    protected $memberService;
    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function showLoginForm()
    {

        // Cek apakah member sudah login menggunakan guard member
        if (Auth::guard('member')->check()) {
            return redirect()->route('skripsi.index');
        }

        return view('member.login');
    }

    public function login(Request $request)
    {
        $member = $this->memberService->getMemberAktif($request);

        // Login pakai guard member (satu query saja karena kita punya instance)
        Auth::guard('member')->login($member);

        // Regenerate session untuk mencegah session fixation
        $request->session()->regenerate();

        session(['member_id' => $member->member_id]);

        return redirect()->route('skripsi.index')
            ->with('success', 'Login anggota berhasil. Selamat datang, ' . $member->member_name);
    }

    public function dashboard()
    {
        $member = Auth::guard('member')->user();
        return redirect()->route('skripsi.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
